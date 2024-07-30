<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\TypesUser;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use App\Models\PermissionsModels;
use App\Http\Controllers\Controller;
use App\Mail\EntrepriseConfirmation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\api\UtilController;

class EntrepriseController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            "name" => "required",
            "logo" => "nullable|image",
            "idnat" => "nullable",
            "rccm" => "required",
            "num_impot" => "required",
            "impot_doc" => "required|file|max:1024",
            "rccm_doc" => "required|file|max:1024",
            "phone" => "required",
            "email" => "nullable|email",
            "pays" => "required",
            "ville" => "required"
        ], ["impot_doc.max" => "Le document du numéro d'impôt ne doit pas dépasser 1mb", "rccm_doc.max" => "Le document du RCCM ne doit pas dépasser 1mb",]);
        $user = Auth::user();
        $engagements = $user->engagement()->with('role', 'entreprise')->get();
        foreach ($engagements as $engagement) {
            if ($engagement->entreprise->name == $request->name && $engagement->role->name == 'Propriétaire') {
                return response()->json([
                    "message" => "Vous avez une autre entreprise avec le même nom"
                ], 422);
            }
        }
        $rccm_doc = UtilController::uploadFile($request->file('rccm_doc'), "/uploads/documents/", "rccm_" . $request->name);
        $impot_doc = UtilController::uploadFile($request->file('impot_doc'), "/uploads/documents/", "impot_" . $request->name);
        $entreprise = new Entreprise();
        if ($request->logo) {
            $logo = UtilController::uploadImage($request->logo, "/uploads/entreprises/");
            $entreprise->logo = $logo;
        }
        $entreprise->name = $request->name;
        $entreprise->idnat = $request->idnat ? $request->idnat : NULL;
        $entreprise->rccm = $request->rccm;
        $entreprise->ville = $request->ville;
        $entreprise->pays = $request->pays;
        $entreprise->email = $request->email ? $request->email : NULL;
        $entreprise->phone = $request->phone;
        $entreprise->rccm_document = $rccm_doc;
        $entreprise->import_document = $impot_doc;
        $entreprise->description = $request->description ? $request->description : "";
        $entreprise->save();
        $role = $entreprise->role()->create([
            "name" => "Propriétaire"
        ]);
        $engagement = $user->engagement()->create([
            "id_entrep" => $entreprise->id,
            "id_role" => $role->id
        ]);
        $permissions = PermissionsModels::where('status', 1)->where('deleted', 0)->get();
        foreach ($permissions as $permission) {
            $engagement->permissions()->attach($permission->id);
        }
        $types = TypesUser::where('name', 'Vendor')->first();
        if (!$user->userType()->where('types_id', $types->id)->exists()) {
            $user->userType()->attach([$types->id]);
        }


        Mail::to($user->email)->send(new EntrepriseConfirmation($user, $request));
        return response()->json([
            "message" => "Entreprise créée",
            "data" => $rccm_doc
        ], 200);
    }
    public function account(Request $request)
    {
        $request->validate([
            "full_name" => "required",
            "email" => "required|email",
            "password" => "required",
            "country" => "required",
            "name" => "required",
            "logo" => "required|image",
            "idnat" => "required",
            "rccm" => "required",
        ]);
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                "message" => "Cette adresse email est déjà utilisée"
            ], 422);
        }
        $user = new User();
        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->pswd = Hash::make($request->password);
        $user->save();
        $logo = UtilController::uploadImage($request->logo, "/uploads/entreprises/");
        $entreprise = new Entreprise();
        $entreprise->logo = $logo;
        $entreprise->name = $request->name;
        $entreprise->idnat = $request->idnat;
        $entreprise->rccm = $request->rccm;
        $entreprise->description = $request->description ? $request->description : "";
        $entreprise->save();
        $role = $entreprise->role()->create([
            "name" => "Propriétaire"
        ]);
        $engagement = $user->engagement()->create([
            "id_entrep" => $entreprise->id,
            "id_role" => $role->id
        ]);
        $permissions = PermissionsModels::where('status', 1)->where('deleted', 0)->get();
        foreach ($permissions as $permission) {
            $engagement->permissions()->attach($permission->id);
        }
        $types = TypesUser::where('name', 'Vendor')->first();
        if (!$user->userType()->where('types_id', $types->id)->exists()) {
            $user->userType()->attach([$types->id]);
        }

        return response()->json([
            "message" => "Entreprise créée"
        ], 200);
    }

    public function UserEntreprises()
    {
        return response()->json([
            "data" => Auth::user()->engagement()->with('role', 'entreprise', 'permissions')->get()
        ], 200);
    }

    public function changeStatus(Request $request)
    {
        if (Auth::user()->Is(['Admin', 'Super admin'])) {
            $request->validate([
                "status" => "required",
                "id_entreprise" => "required"
            ]);
            $entreprise = Entreprise::find($request->id_entreprise);
            if ($entreprise) {
                $entreprise->status = $request->status;
                $entreprise->save();
                return response()->json([
                    "message" => "Status de l'entreprise changé avec succès"
                ], 200);
            } else {
                return response()->json([
                    "message" => "id not found"
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "Vous n'avez pas cette autorisation"
            ], 402);
        }
    }

    public function allEntreprises()
    {
        return response()->json([
            "status" => 200,
            "data" => Entreprise::get()
        ], 200);
    }
    public function OneEntreprises($id)
    {
        $entreprise = Entreprise::find($id);
        if ($entreprise) {
            return response()->json([
                "status" => 200,
                "data" => $entreprise
            ], 200);
        } else {
            return response()->json([
                "status" => 200,
                "message" => "id not found"
            ], 404);
        }
    }
}
