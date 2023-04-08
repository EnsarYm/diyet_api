<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DietRequest;
use Illuminate\Http\Request;
use App\Library\Services\BunnyCDNStorage;
use App\Models\Diet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class DietController extends Controller
{
    public function addDiet(DietRequest $request, BunnyCDNStorage $bunnyCDNStorage)
    {
        $newDiet = $request->validated();
        $user = Auth::user();

        $file = $request->file('doc');
        $start_date = Carbon::createFromFormat('Y-m-d', $request->start_date)->format('Y-m-d');
        $end_date = Carbon::createFromFormat('Y-m-d', $request->end_date)->format('Y-m-d');

        $newDiet["manager_id"] = $user->id;
        $newDiet["user_id"] = $request->user_id;
        $newDiet["description"] = $request->description;
        $newDiet["manager_note"] = $request->manager_note;
        $newDiet["start_date"] = $start_date;
        $newDiet["end_date"] = $end_date;

        // Doküman yükleme işlemi
        if ($file) {
            $uuid = Str::uuid()->toString();
            //File Name
            $fileName = $file->getClientOriginalName();
            //Display File Extension
            $fileExtension = $file->getClientOriginalExtension();
            //Display File Real Path
            $filePath = $file->getRealPath();
            //Display File Size
            $fileSize = $file->getSize();
            $api_key = "393e4b68-4478-499b-93baefc9397c-e461-451e";
            $storage_zone_path =  '/diyet/Diets/' . $user->id;
            $fileNameUnique = pathinfo($fileName, PATHINFO_FILENAME) . $uuid . '.' . $fileExtension;
            $result = $bunnyCDNStorage->Storage($api_key)->PutFile($filePath, $storage_zone_path, $fileNameUnique);

            if ($result['status'] == "success") {
                $newDiet["file"] = "https://cdn.diyetapi.com/Diets/" . $user->id . '/' . $result['file_name'];
            } else {
                $success['success'] = false;
                return response()->json($success, 401);
            }
        }

        $diet = Diet::create($newDiet);
        $success['diet'] = $diet;
        $success['success'] = true;
        return response()->json($success, 200);
    }



    public function getUserDiets(Request $request)
    {
        $user = Auth::user();
        $user_id = $request->user_id;


        $dietData = DB::select('SELECT * FROM diets WHERE user_id = ? AND manager_id = ?', [$user_id, $user->id]);

        if (count($dietData) == 0) {
            $success["success"] = false;
            return response()->json($success, 404);
        }

        $i = 0;

        foreach ($dietData as $diet) {
            $success["diets"][$i] = array(
                "id" => $diet->id,
                "name" => $diet->name,
                "description" => $diet->description,
                "file" => $diet->file,
                "start_date" => $diet->start_date,
                "end_date" => $diet->end_date,
                "is_active" => $diet->is_active,
                "is_deleted" => $diet->is_deleted,
                "is_readed" => $diet->is_readed,
                "is_success" => $diet->is_success,
                "manager_id" => $diet->manager_id,
                "user_id" => $diet->user_id
            );
            $i++;
        }

        $success["success"] = true;
        $success["dietData"] = $user_id;
        return response()->json($success, 200);
        unset($user_id);
    }

    public function deleteDiet(Request $request)
    {
        $affected = DB::table('diets')
            ->where('id', $request->id)
            ->update(['is_deleted' => true]);

        if ($affected) {
            $success["success"] = true;
            return response()->json($success, 200);
        } else {
            $success["success"] = false;
            return response()->json($success, 400);
        }
    }
}
