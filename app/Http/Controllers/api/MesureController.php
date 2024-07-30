<?php

namespace App\Http\Controllers\api;

use App\Models\Entreprise;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Engagement;
use App\Models\Mesure;
use Illuminate\Support\Facades\Auth;

class MesureController extends Controller
{
    public function create(Request $request, $entreprise)
    {
        $entreprises = Entreprise::find($entreprise);
        if ($entreprises) {
            $request->validate([
                "name" => "required",
            ]);
            foreach ($entreprises->mesures as $item) {
                if ($item->name == $request->name) {
                    return response()->json([
                        "message" => "Cette mésure existe déjà"
                    ], 422);
                }
            }
            if (Auth::user()->Is(['Vendor'])) {
                $engagement = Engagement::where('id_user', Auth::user()->id)->where('id_entrep', $entreprises->id)->where('status', 1)->first();
                if ($engagement) {
                    if ($engagement->can('create_mesure')) {
                        $entreprises->mesures()->create([
                            "name" => $request->name
                        ]);
                        return response()->json([
                            "message" => "Mésure créée"
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
    }
    public function update(Request $request, $entreprise, $id)
    {
        $entreprises = Entreprise::find($entreprise);
        if ($entreprises) {
            if (Auth::user()->Is(['Vendor'])) {
                $engagement = Engagement::where('id_user', Auth::user()->id)->where('id_entrep', $entreprises->id)->where('status', 1)->first();
                if ($engagement) {
                    if ($engagement->can('update_mesure')) {
                        $request->validate([
                            "name" => "required",
                        ]);
                        $mesure = $entreprises->mesures()->where('deleted', 0)->find($id);
                        if ($mesure) {
                            foreach ($entreprises->mesures as $item) {
                                if ($item->name == $request->name && $item->id !== $mesure->id) {
                                    return response()->json([
                                        "message" => "Cette mésure existe déjà"
                                    ], 422);
                                }
                            }

                            $mesure->name = $request->name;
                            $mesure->save();
                            return response()->json([
                                "message" => "Mésure modifiée"
                            ], 200);
                        } else {
                            return response()->json([
                                "message" => "id mesure not found"
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
    public function delete(Request $request, $entreprise, $id)
    {
        $entreprises = Entreprise::find($entreprise);
        if ($entreprises) {
            if (Auth::user()->Is(['Vendor'])) {
                $engagement = Engagement::where('id_user', Auth::user()->id)->where('id_entrep', $entreprises->id)->where('status', 1)->first();
                if ($engagement) {
                    if ($engagement->can('delete_mesure')) {
                        $mesure = $entreprises->mesures()->where('deleted', 0)->find($id);
                        if ($mesure) {
                            $mesure->deleted =1;
                            $mesure->save();
                            return response()->json([
                                "message" => "Mésure supprimée"
                            ], 200);
                        } else {
                            return response()->json([
                                "message" => "id mesure not found"
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

    public function liste($entreprise){
        $entre = Entreprise::find($entreprise);
        if($entre){
            return response()->json([
                "data" =>  $entre->mesures()->where('deleted', 0)->get()
            ], 200);
        } else {
            return response()->json([
                "message" => "id not found"
            ], 404);
        }
   
    }
}
