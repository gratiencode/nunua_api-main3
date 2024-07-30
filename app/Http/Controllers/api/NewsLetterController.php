<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\NewsLetter;
use Illuminate\Http\Request;

class NewsLetterController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            "email" => "required|email"
        ]);
        NewsLetter::updateOrCreate([
            "email" => $request->email
        ]);
        return response()->json([
            "message" => "Enregistrement reussi"
        ], 200);
    }
}
