<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Models\PermissionsModels;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    public function getPermissions(){
        return response()->json([
            "data" => PermissionsModels::where('deleted', 0)->get()
        ], 200);
    }
}
