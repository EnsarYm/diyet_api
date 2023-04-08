<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helpers;

class UserController extends Controller
{
    public function createUser(UserRequest $request)
    {
        $newUser = $request->validated();
        $user = Auth::user();

        if ($user->role != "manager") {
            return response()->json(['error' => 'unauthorized'], 401);
        }
        $newUser['password'] = Hash::make($newUser['password']);
        $newUser['role'] = 'user';
        $newUser['manager_id'] = $user->id;
        $createdUser = User::create($newUser);

        $success["data"] = $newUser;
        $success["user"] = $createdUser;
        $success["success"] = true;
        return response()->json($success, 200);
    }

    public function getUsers()
    {

        $user = Auth::user();

        $myUsers = DB::table('users')->where('manager_id', $user->id, 'id')->orderBy('first_name')->get();
        $i = 0;

        foreach ($myUsers as $user) {
            $success["users"][$i] = array(
                "id" => Helpers::myCrypt($user->id),
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "gender" => $user->gender,
                "email" => $user->email,
                "phone" => $user->phone,
                "image" => $user->image == null ? "avatar.jpg" : $user->image,
                "birth_date" => $user->birth_date,
                "is_active" => $user->is_active,
                "weight" => $user->weight,
                "height" => $user->height,
                "job" => $user->job
            );
            $i++;
        }

        $success["success"] = true;
        return response()->json($success, 200);
    }

    public function getUser(Request $request)
    {

        $user = Auth::user();


        $userData = DB::select('SELECT * FROM users WHERE id = ? AND manager_id = ?', [$request->user_id, $user->id]);
        // $i = 0;

        // foreach ($myUsers as $user) {
        //     $success["users"][$i] = array(
        //         "id" => $user->id,
        //         "first_name" => $user->first_name,
        //         "last_name" => $user->last_name,
        //         "gender" => $user->gender,
        //         "email" => $user->email,
        //         "phone" => $user->phone,
        //         "image" => $user->image == null ? "avatar.jpg" : $user->image,
        //         "birth_date" => $user->birth_date,
        //         "is_active" => $user->is_active,
        //         "weight" => $user->weight,
        //         "height" => $user->height,
        //         "job" => $user->job
        //     );
        //     $i++;
        // }

        $success["success"] = true;
        $success["user"] = $userData;
        return response()->json($success, 200);
    }
}
