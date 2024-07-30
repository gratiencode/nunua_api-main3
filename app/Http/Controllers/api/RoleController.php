<?php

namespace App\Http\Controllers\api;

use App\Models\Engagement;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            "name" => "required",
            "id_entrep" => "required"
        ]);
        $entreprises = Entreprise::find($request->id_entrep);
        if ($entreprises) {
            foreach ($entreprises->role as $item) {
                if ($item->name == $request->name) {
                    return response()->json([
                        "message" => "Ce role existe déjà"
                    ], 422);
                }
            }
            if (Auth::user()->Is(['Vendor'])) {
                $engagement = Engagement::where('id_user', Auth::user()->id)->where('id_entrep', $entreprises->id)->where('status', 1)->first();
                if ($engagement) {
                    if ($engagement->can('create_role')) {
                        $entreprises->role()->create([
                            "name" => $request->name
                        ]);
                        return response()->json([
                            "message" => "Role crée"
                        ], 200);
                    } else {
                        return response()->json([
                            "message" => "Vous ne pouvez pas éffectuer cette action"
                        ], 402);
                    }
                } else {
                    return response()->json([
                        "message" => "Vous ne pouvez pas éffectuer cette action"
                    ], 402);
                }
            } else {
                return response()->json([
                    "message" => "Vous ne pouvez pas éffectuer cette action"
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "Entreprise not found"
            ], 404);
        }
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            "name" => "required",
            "id_entrep" => "required"
        ]);
        $entreprises = Entreprise::find($request->id_entrep);
        if ($entreprises) {
            $role = $entreprises->role()->where('status', 1)->find($id);
            if ($role) {
                foreach ($entreprises->role as $item) {
                    if ($item->name == $request->name && $item->id !== $role->id) {
                        return response()->json([
                            "message" => "Ce role existe déjà"
                        ], 422);
                    }
                }
                if (Auth::user()->Is(['Vendor'])) {
                    $engagement = Engagement::where('id_user', Auth::user()->id)->where('id_entrep', $entreprises->id)->where('status', 1)->first();
                    if ($engagement) {
                        if ($engagement->can('update_role')) {
                            $role->name = $request->name;
                            $role->save();
                            return response()->json([
                                "message" => "Role modifié"
                            ], 200);
                        } else {
                            return response()->json([
                                "message" => "Vous ne pouvez pas éffectuer cette action"
                            ], 402);
                        }
                    } else {
                        return response()->json([
                            "message" => "Vous ne pouvez pas éffectuer cette action"
                        ], 402);
                    }
                } else {
                    return response()->json([
                        "message" => "Vous ne pouvez pas éffectuer cette action"
                    ], 402);
                }
            } else {
                return response()->json([
                    "message" => "id not found"
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "Entreprise not found"
            ], 404);
        }
    }
    public function delete($entreprise, $id)
    {
        $entreprises = Entreprise::find($entreprise);
        if ($entreprises) {
            if (Auth::user()->Is(['Vendor'])) {
                $engagement = Engagement::where('id_user', Auth::user()->id)->where('id_entrep', $entreprises->id)->where('status', 1)->first();
                if ($engagement) {
                    if ($engagement->can('delete_role')) {
                        $role = $entreprises->role()->where('status', 1)->find($id);
                        if ($role) {
                            $role->status = 0;
                            $role->save();
                            return response()->json([
                                "message" => "Role supprimé"
                            ], 200);
                        } else {
                            return response()->json([
                                "message" => "id role not found"
                            ], 404);
                        }
                    } else {
                        return response()->json([
                            "message" => "Vous ne pouvez pas éffectuer cette action"
                        ], 402);
                    }
                } else {
                    return response()->json([
                        "message" => "Vous ne pouvez pas éffectuer cette action"
                    ], 402);
                }
            } else {
                return response()->json([
                    "message" => "Vous ne pouvez pas éffectuer cette action"
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "id not found"
            ], 404);
        }
    }

    public function liste($entreprise)
    {
        $entre = Entreprise::find($entreprise);
        if ($entre) {
            return response()->json([
                "data" =>  $entre->role()->where('status', 1)->get()
            ], 200);
        } else {
            return response()->json([
                "message" => "id not found"
            ], 404);
        }
    }
}
