<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\TypesUser;
use App\Models\Engagement;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class EngagementController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            "id_user" => "required",
            "id_entrep" => "required",
            "id_role" => "required",
            "permissions" => "required|array|min:2"
        ]);
        if (Auth::user()->Is(['Vendor'])) {
            $engagement = Engagement::where('id_user', Auth::user()->id)->where('id_entrep', $request->id_entrep)->where('status', 1)->first();
            if ($engagement) {
                if ($engagement->can('create_user')) {
                    $userExist = Engagement::where('id_entrep', $request->id_entrep)->where('id_user', $request->id_user)->exists();
                    if ($userExist) {
                        return response()->json([
                            "message" => "Cet utilisateur existe déjà dans cette entreprise"
                        ], 422);
                    } else {
                        $en = Engagement::create([
                            "id_user"=> $request->id_user,
                            "id_entrep" => $request->id_entrep,
                            "id_role" => $request->id_role
                        ]);
                        $type = TypesUser::where('name', "Vendor")->first();
                        $user = User::find($request->id_user);
                        $user->userType()->attach($type->id);
                        $en->permissions()->sync($request->permissions);
                        return response()->json([
                            "message" => "Engagement reussi"
                        ], 200);
                    }
                } else {
                    return response()->json([
                        "message" => "Vous ne pouvez pas éffectuer cette action"
                    ], 402);
                }
            } else {
                return response()->json([
                    "message" => "Cet utilisateur existe déjà dans cette entreprise"
                ], 422);
            }
        } else {
            return response()->json([
                "message" => "Vous ne pouvez pas éffectuer cette action"
            ], 402);
        }
    }

    public function liste($entreprise){
        $entreprises = Entreprise::find($entreprise);
        if($entreprises){
            return response()->json([
                "data" => $entreprises->engagement()->with('role', 'permissions', 'user')->get()
            ], 200);
        } else {
            return response()->json([
                "message" => "id not found"
            ], 404);
        }
    }
}
