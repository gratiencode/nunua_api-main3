<?php

namespace App\Http\Controllers\api;

use App\Models\AppSettings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AppSettingsController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            "email" => "required",
            "phone" => "required",
            "adresse" => "required",
            "app_name" => "required",
            "id" => "nullable",
            "lat" => "nullable",
            "long" => "nullable",
        ]);
        if (Auth::user()->Is(['Admin', "Super admin"])) {
            $settings = AppSettings::createOrUpdate([
                "email" => $request->email,
                "phone" => $request->phone,
                "adresse" => $request->adresse,
                "app_name" => $request->app_name,
                "lat" => $request->lat ? $request->lat : NULL,
                "long" => $request->long ? $request->long : NULL,
            ], $request->id);

            return response()->json([
                "message" => "Réglages enregistés avec succès",
                "data" => AppSettings::orderBy('created_at', 'desc')->first()
            ], 200);
        } else {
            return response()->json([
                "message" => "Vous n'avez pas cette autorisation"
            ], 402);
        }
    }
    public function createAbout(Request $request)
    {
        $request->validate([
            "about_us" => "required",
            "mission" => "nullable",
            "id" => "nullable"
        ]);
        if (Auth::user()->Is(['Admin', "Super admin"])) {
            $settings = AppSettings::createOrUpdate([
                "about_us" => $request->about_us,
                "mission" => $request->mission ? $request->mission : NULL,
            ], $request->id);

            return response()->json([
                "message" => "Réglages enregistés avec succès",
                "data" => AppSettings::orderBy('created_at', 'desc')->first()
            ], 200);
        } else {
            return response()->json([
                "message" => "Vous n'avez pas cette autorisation"
            ], 402);
        }
    }
    public function createSocial(Request $request)
    {
        $request->validate([
            "facebook" => "required",
            "linkedin" => "required",
            "youtube" => "required",
            "instagram" => "required",
            "id" => "required"
        ]);
        if (Auth::user()->Is(['Admin', "Super admin"])) {
            AppSettings::createOrUpdate([
                "facebook" => $request->facebook,
                "linkedin" => $request->linkedin,
                "youtube" => $request->youtube,
                "instagram" => $request->instagram
            ], $request->id);

            return response()->json([
                "message" => "Réglages enregistés avec succès",
                "data" => AppSettings::orderBy('created_at', 'desc')->first()
            ], 200);
        } else {
            return response()->json([
                "message" => "Vous n'avez pas cette autorisation"
            ], 402);
        }
    }
    public function getSettings()
    {
        return response()->json([
            "data" => AppSettings::orderBy('created_at', 'desc')->first()
        ], 200);
    }
}
