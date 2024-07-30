<?php

namespace App\Http\Controllers\api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            "name" => "required",
            "image" => "required|image",
            "icon" => "required|image",
        ], ['name.unique' => "Cette catégorie existe déjà"] );
        if($request->parent){
            $category = Category::where('deleted', 0)->find($request->parent);
            if($category){
                if (Auth::user()->Is(['Admin', 'Super admin'])) {
                    foreach($category->children()->where('deleted', 0)->get() as $row){
                        if($row->name == $request->name){
                            return response()->json([
                                "message" => "Cette sous-catégorie existe déjà dans cette catégorie"
                            ], 422);
                        }
                    }
                    $image = UtilController::uploadImage($request->image, '/uploads/category/');
                    $icon = UtilController::uploadImage($request->icon, '/uploads/category/');
                    $category->children()->create([
                        'name' => $request->name,
                        "image" => $image,
                        "icon" => $icon,
                    ]);
                    return response()->json([
                        "message" => "Catégorie créée"
                    ], 200);
                } else {
                    return response()->json([
                        "message" => "Vous ne pouvez pas éffectuer cette action"
                    ], 402);
                }
            } else {
                return response()->json([
                    "date" => "id not found"
                ]);
            }
        } else {
            if (Auth::user()->Is(['Admin', 'Super admin'])) {
                foreach(Category::where('deleted', 0)->whereNull('parent_id')->get() as $row){
                    if($row->name == $request->name){
                        return response()->json([
                            "message" => "Cette catégorie existe déjà"
                        ], 422);
                    }
                }
                $image = UtilController::uploadImage($request->image, '/uploads/category/');
                $icon = UtilController::uploadImage($request->icon, '/uploads/category/');
                $category = new Category();
                $category->name = $request->name;
                $category->image = $image;
                $category->icon = $icon;
                $category->parent_id = $request->parent ? $request->parent : NULL;
                $category->save();
                return response()->json([
                    "message" => "Catégorie créée"
                ], 200);
            } else {
                return response()->json([
                    "message" => "Vous ne pouvez pas éffectuer cette action"
                ], 402);
            }
        }
      
    }
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if($category){
            $request->validate([
                "name" => "required|unique:t_categorie,name,".$category->id,
            ], ['name.unique' => "Cette catégorie existe déjà"] );
            if (Auth::user()->Is(['Admin', 'Super admin'])) {
                if($request->image){
                    UtilController::removeImage($category->image, '/uploads/category/');
                    $image = UtilController::uploadImage($request->image, '/uploads/category/');
                    $category->image = $image;
                }
                if($request->icon){
                    UtilController::removeImage($category->image, '/uploads/category/');
                    $icon = UtilController::uploadImage($request->icon, '/uploads/category/');
                    $category->icon = $icon;
                }
                $category->name = $request->name;
                $category->parent_id = $request->parent ? $request->parent : NULL;
                $category->save();
                return response()->json([
                    "message" => "Catégorie modifiée"
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
            $category = Category::where('deleted', 0)->find($id);
            if($category){
                UtilController::removeImage($category->image, "/uploads/category/");
                $category->deleted = 1;
                $category->save();
                return response()->json([
                    "message" => 'Catégorie supprimée'
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

    public function liste(){
        return response()->json([
            "data" => Category::tree()
        ],200);
    }

    public function allCategory(){
        return response()->json([
            "data" => Category::where('deleted', 0)->get()
        ], 200);
    }
    public function featured()
    {
        $categories = Category::with('children')->has('produits')->where('deleted', 0)->get();
        return response()->json([
            "data" => $categories
        ], 200);
    }
}
