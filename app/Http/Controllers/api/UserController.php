<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\TypesUser;
use Illuminate\Http\Request;
use App\Mail\Verificationmail;
use App\Models\CodeValidation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required",
            "pswd" => "required"
        ]);
        if (User::where('email', $request->email)->exists()) {
            $user = User::where('email', $request->email)->first();
            if ($user->status == 1) {
                if (Hash::check($request->pswd, $user->pswd)) {
                    $token = $user->createToken("accessToken")->plainTextToken;
                    return response()->json([
                        "message" => 'success',
                        "data" => User::with(['userType', 'ville', 'pays'])->find($user->id),
                        "status" => 1,
                        "token" => $token
                    ], 200);
                } else {
                    return response()->json([
                        "message" => 'Le mot de passe est incorrect'
                    ], 422);
                }
            } else {
                return response()->json([
                    "message" => 'Votre compte n\'est pas activé'
                ], 422);
            }
        } else {
            return response()->json([
                "message" => "Cette adresse email n'existe pas"
            ], 404);
        }
    }
    public function ask(Request $request)
    {
        $request->validate([
            "email" => "required"
        ]);
        if (User::where('email', $request->email)->exists()) {
            $code = mt_rand(1, 999999);
            $val = CodeValidation::where('email', $request->email)->first();
            if ($val) {
                $val->code = $code;
                $val->save();
            } else {
                CodeValidation::create(['email' => $request->email, 'code' => $code]);
            }
            Mail::to($request->email)->send(new Verificationmail($request->email, $code));
            return response()->json([
                "message" => "Un code de validation vous a été envoyé à l'adresse " . $request->email,
                "code_validation" => $code
            ], 200);
        } else {
            return response()->json([
                "message" => "Cette adresse email n'existe pas"
            ], 404);
        }
    }

    public function resendEmail(Request $request)
    {
        $request->validate([
            "email" => "required"
        ]);
        $code = mt_rand(1, 999999);
        $val = CodeValidation::where('email', $request->email)->first();
        if ($val) {
            $val->code = $code;
            $val->save();
        } else {
            CodeValidation::create(['email' => $request->email, 'code' => $code]);
        }
        Mail::to($request->email)->send(new Verificationmail($request->email, $code));
        return response()->json([
            "message" => "Un code de validation vous a été envoyé à l'adresse " . $request->email,
            "code_validation" => $code
        ], 200);
    }

    public function register(Request $request)
    {

        $request->validate([
            "pays" => "required",
            "email" => "required|email",
            "pswd" => "required",
            "full_name" => "required",

        ]);
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                "message" => "Cette adresse email est déjà utilisée"
            ], 422);
        } else {
            if ($request->otp) {
                if (CodeValidation::where('email', $request->email)->where('code', $request->otp)->exists()) {
                    $user = User::create([
                        "pays" => $request->pays,
                        "full_name" => $request->full_name,
                        "pswd" => Hash::make($request->pswd),
                        "email" => $request->email
                    ]);
                    CodeValidation::where('email', $request->email)->delete();
                    $token = $user->createToken("accessToken")->plainTextToken;
                    return response()->json([
                        "message" => 'Votre compte a été crée avec succès',
                        "data" => User::with(['userType', 'ville', 'pays'])->find($user->id),
                        "status" => 1,
                        "token" => $token
                    ], 200);
                } else {
                    return response()->json([
                        "message" => "Le code de validation est incorrect",
                    ], 422);
                }
            } else {
                $code = mt_rand(1, 999999);
                $val = CodeValidation::where('email', $request->email)->first();
                if ($val) {
                    $val->code = $code;
                    $val->save();
                } else {
                    CodeValidation::create(['email' => $request->email, 'code' => $code]);
                }
                Mail::to($request->email)->send(new Verificationmail($request->email, $code));
                return response()->json([
                    "message" => "Un code de validation vous a été envoyé à l'adresse " . $request->email,
                    "code_validation" => $code
                ], 200);
            }
        }
    }

    public function seachUser($query)
    {
        return response()->json([
            "data" => User::select('*')
                ->where('full_name', 'LIKE', "%" . $query . "%")
                ->orWhere('email', 'LIKE', "%" . $query . "%")
                ->get()
        ], 200);
    }
    public function AuthProvider(Request $request)
    {
        $request->validate([
            "email" => "required",
            "fullname" => "required",
            "image" => "required",
        ]);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update([
                "full_name" => $request->fullname,
                "email" => $request->email,
                "provider" => 1,
                "profil" => $request->image
            ]);
            $token = $user->createToken("accessToken")->plainTextToken;
            return response()->json([
                "message" => 'success',
                "data" => User::with('userType')->find($user->id),
                "status" => 1,
                "token" => $token
            ], 200);
        } else {
            $user = User::create([
                "full_name" => $request->fullname,
                "email" => $request->email,
                "provider" => 1,
                "profil" => $request->image
            ]);
            $token = $user->createToken("accessToken")->plainTextToken;
            return response()->json([
                "message" => 'success',
                "data" => User::with('userType')->find($user->id),
                "status" => 1,
                "token" => $token
            ], 200);
        }
    }
    public function changePswd(Request $request)
    {
        $request->validate([
            "pswd" => "required",
            "email" => "required",
            "otp" => "required"
        ]);
        if (CodeValidation::where('email', $request->email)->where('code', $request->otp)->exists()) {
            $user = User::where('email', $request->email)->update([
                "pswd" => Hash::make($request->pswd)
            ]);
            CodeValidation::where('email', $request->email)->delete();
            return response()->json([
                "message" => 'Votre mot de passe a été réinitialisé avec succès'
            ], 200);
        } else {
            return response()->json([
                "message" => "Le code de validation est incorrect",
            ], 422);
        }
    }

    public function editProfile(Request $request)
    {
        $request->validate([
            "email" => "required",
            "full_name" => "required",
            "phone" => "required",
            "pays" => "required",
            "ville" => "required"
        ]);
    }
}
