<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDietController extends Controller
{
    public function getMyDiets(Request $request)
    {
        $user = Auth::user();
        $manager_id = $request->manager_id;


        $dietData = DB::select('SELECT * FROM diets WHERE user_id = ? AND manager_id = ?', [$user->id, $manager_id]);

        if (count($dietData) == 0) {
            $success["success"] = true;
            $success["msg"] = "no diet not found";
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
        $success["dietData"] = $manager_id;
        return response()->json($success, 200);
        unset($manager_id);
    }

    public function getDietDetail(Request $request)
    {
        $user = Auth::user();
        $dietID = $request->dietID;


        // $dietData = DB::select('SELECT * FROM diets WHERE user_id = ? AND manager_id = ?', [$user->id, $manager_id]);
        $dietData = DB::table('diets')->where('user_id', $user->id)->where('id', $dietID)->first();
        if (!$dietData) {
            $success["success"] = true;
            $success["msg"] = "diet not found";
            return response()->json($success, 404);
        }
        return response()->json($dietData, 200);
    }
}
