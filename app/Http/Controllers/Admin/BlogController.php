<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogRequest;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Library\Services\BunnyCDNStorage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    public function createBlog(Request $request, BunnyCDNStorage $bunnyCDNStorage)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,svg,gif|max:2048',
            'category_id' => 'required|integer|exists:blog_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        // Create the new blog with validated data
        $newBlog = $validator->validated();
        $newBlog['manager_id'] = $user->id;
        $newBlog['category_id'] = $request->category_id;
        $blog = Blog::create($newBlog);

        // Upload blog files and add their URLs to the blog
        $fileArray = [];
        foreach ($request->file('files', []) as $file) {
            $uuid = Str::uuid()->toString();
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = $file->getClientOriginalName();
            $filePath = $file->getRealPath();

            $api_key = config('services.bunny_cdn.api_key');
            $storage_zone_path = '/diyet/Blogs/' . $user->id;
            $fileNameUnique = pathinfo($fileName, PATHINFO_FILENAME) . $uuid . '.' . $fileExtension;

            $result = $bunnyCDNStorage->Storage($api_key)->PutFile($filePath, $storage_zone_path, $fileNameUnique);
            if ($result['status'] !== 'success') {
                $success['success'] = false;
                $success['result'] = $result;
                $success['filePath'] = $filePath;
                return response()->json($success, 401);
            }

            $url = 'https://cdn.diyetapi.com/Blogs/' . $user->id . '/' . $result['file_name'];
            $fileArray[] = [
                'url' => $url,
                'blog_id' => $blog->id,
            ];
        }
        DB::table('blog_files')->insert($fileArray);

        // Return the success response
        $success = [
            'blog' => $blog,
            'files' => $fileArray,
            'success' => true,
        ];
        return response()->json($success, 200);
    }

    public function getMyBlogs(Request $request)
    {
        $user = Auth::user();

        $myBlogs = DB::table('blogs')
            ->leftJoin('blog_files', 'blog_files.blog_id', '=', 'blogs.id')
            ->leftJoin('blog_categories', 'blog_categories.id', '=', 'blogs.category_id')
            ->selectRaw('
                blogs.id,
                blogs.title,
                blogs.content,
                blogs.is_active,
                GROUP_CONCAT(blog_files.url) AS urls,
                blog_categories.name AS categoryName,
                blog_categories.description AS categoryDescription
            ')
            ->where([
                ['blogs.manager_id', '=', $user->id],
                ['blogs.is_active', '=', true],
                ['blogs.is_deleted', '=', false],
            ])
            ->groupBy('blogs.id')
            ->orderBy('blogs.title')
            ->get();

        // Check if the user has any blogs
        if ($myBlogs->isEmpty()) {
            return response()->json(['error' => 'You have no blogs'], 404);
        }

        // Split the URLs string into an array
        foreach ($myBlogs as $blog) {
            if ($blog->urls) {
                $blog->urls = explode(',', $blog->urls);
            } else {
                $blog->urls = [];
            }
        }

        $success = [
            'blogs' => $myBlogs,
            'success' => true,
        ];
        return response()->json($success, 200);
    }


    public function deleteBlog(Request $request, BunnyCDNStorage $bunnyCDNStorage)
    {
        $user = Auth::user();

        $blog = Blog::find($request->id);

        if (!$blog) {
            return response()->json(['error' => 'Blog not found'], 404);
        }

        if ($blog->manager_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $blogFiles = $blog->blogFiles()->get();
        $api_key = config('services.bunny_cdn.api_key');

        foreach ($blogFiles as $blogFile) {
            $fileName = pathinfo($blogFile->url, PATHINFO_BASENAME);
            $result = $bunnyCDNStorage->Storage($api_key)->DeleteFile('/diyet/Blogs/' . $user->id . '/' . $fileName);

            if ($result['status'] !== 'success') {
                return response()->json(['error' => 'Failed to delete file'], 400);
            }

            // Delete the file record from the database
            $blogFile->delete();
        }

        $blog->delete();


        return response()->json(['success' => true], 200);
    }
}
