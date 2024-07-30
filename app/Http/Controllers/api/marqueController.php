<?php

namespace App\Http\Controllers\api;

use App\Models\Marque;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\api\UtilController;

class marqueController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            "name" => "required|unique:t_marque,name",
            "image" => "required|image"
        ], ['name.unique' => "Cette marque existe déjà"] );
        if (Auth::user()->Is(['Admin', 'Super admin'])) {
            $image = UtilController::uploadImage($request->image, '/uploads/marques/');
            $marque = new Marque();
            $marque->name = $request->name;
            $marque->image = $image;
            $marque->save();
            return response()->json([
                "message" => "marque créée"
            ], 200);
        } else {
            return response()->json([
                "message" => "Vous ne pouvez pas éffectuer cette action"
            ], 402);
        }
    }
    public function update(Request $request, $id)
    {
        $marque = Marque::find($id);
        if($marque){
            $request->validate([
                "name" => "required|unique:t_marque,name,".$marque->id,
            ], ['name.unique' => "Cette marque existe déjà"] );
            if (Auth::user()->Is(['Admin', 'Super admin'])) {
                if($request->image){
                    UtilController::removeImage($marque->image, '/uploads/marques/');
                    $image = UtilController::uploadImage($request->image, '/uploads/marques/');
                    $marque->image = $image;
                }
                $marque->name = $request->name;
                $marque->save();
                return response()->json([
                    "message" => "Marque modifiée"
                ], 200);
            } else {
                return response()->json([
                    "message" => "Vous ne pouvez pas éffectuer cette action"
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "Id not found"
            ], 404);
        }
   
    }
    public function delete($id)
    {
        if (Auth::user()->Is(['Admin', 'Super admin'])) {
            $marque = Marque::where('deleted', 0)->find($id);
            if($marque){
                UtilController::removeImage($marque->image, "/uploads/marques/");
                $marque->deleted = 1;
                $marque->save();
                return response()->json([
                    "message" => 'Marque supprimée'
                ], 200);
            } else {
                return response()->json([
                    "message" => 'id not found'
                ], 422);
            }
        } else {
            return response()->json([
                "message" => "Vous ne pouvez pas éffectuer cette action"
            ], 402);
        }
    }


    public function allMarque(){
        return response()->json([
            "data" => Marque::where('deleted', 0)->get()
        ], 200);
    }
}
