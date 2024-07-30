<?php

namespace App\Http\Controllers\api;

use App\Models\Collections;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\api\UtilController;

class CollectionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "image" => "required",
            "category" => "required|array"
        ]);
        $image = UtilController::uploadImage($request->image, '/uploads/collections/');
        $collection = new Collections();
        $collection->name = $request->name;
        $collection->image = $image;
        $collection->description = $request->description ? $request->description : NULL;
        $collection->Save();
        $collection->categories()->sync($request->category);
        return response()->json([
            'message' => "Collection créée"
        ], 200);
    }
}
