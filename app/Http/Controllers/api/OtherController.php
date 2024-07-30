<?php

namespace App\Http\Controllers\api;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OtherController extends Controller
{
    public function getCountry(){
        return response()->json([
            "code" => 200,
            "data" => Country::with(["ville"])->where('status', 1)->where('deleted', 0)->get()
        ], 200);
    }
    public function getCity(Request $request){
        $request->validate(["id_country" => "required"]);
        $coutry = Country::where('deleted', 0)->find($request->id_country);
        if($coutry){
            return response()->json([
                "data" => $coutry->ville()->where('deleted', 0)->get()
            ], 200);
        } else {
            return response()->json([
                "message"=>"Id not found"
            ], 404);
        }
     
    }
}
