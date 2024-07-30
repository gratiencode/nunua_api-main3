<?php

namespace App\Http\Controllers\api;

use App\Models\Produits;
use App\Models\Commandes;
use App\Mail\InvoiceEmail;
use App\Models\Engagement;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CommandeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            "payment_method" => "required",
            'customer_name' => 'required',
            'customer_address' => "required",
            'country' => 'required',
            "city" => "required",
            "zipCode" => "required",
            "phone" => "required",
            "email" => "required",
            "currency" => "required",
            "total" => "required",
            "shipping_cost" => "required",
            "cart" => "required|array",

        ]);
        $user = Auth::user();
        $order = $user->commandes()->create([
            'order_number' => uniqid('#'),
            'total' => $request->total,
            'payment_method' => $request->payment_method,
            'shipping_adresse' => $request->customer_address,
            'shipping_city' => $request->city,
            'shipping_code' => $request->zipCode,
            'billing_fullname' => $request->customer_name,
            'billing_phone' => $request->phone,
            'billing_email' => $request->email,
            'billing_currency' => $request->currency,
            'shipping_country' => $request->country,
            'free_shipping' => $request->shipping_cost,
            'grand_total' => $request->shipping_cost + $request->total,
        ]);
        foreach ($request->cart as $item) {

            $order->details()->attach($item['id'], ['price' => $item['price'], 'quantity' => $item['quantity']]);
            $stock = Produits::find($item['id']);
            $stock->qte = $stock->qte - $item['quantity'];
            $stock->save();
        }
        $order->creationSousCommande();
        $com = Commandes::with('details.mesure')->where('id', $order->id)->first();
        Mail::to($user->email)->send(new InvoiceEmail($user, $com));

        return response()->json([
            "message" => 'Commande envoyée avec succè',
            "data" => $com

        ], 200);
    }

    public function oneInvoice($id)
    {
        $order = Commandes::with('details.mesure')->find($id);
        if ($order) {
            return response()->json([
                "data" => $order
            ], 200);
        } else {
            return response()->json([
                "message" => "Id not found"
            ], 404);
        }
    }
    public function CustomerOrders()
    {
        $user = Auth::user();
        return response()->json([
            "data" => $user->commandes()->with('details.mesure')->get()
        ], 200);
    }
    public function allOrders()
    {
        return response()->json([
            "data" => Commandes::with('details.mesure')->get()
        ], 200);
    }
    public function changeStatus(Request $request)
    {
        if (Auth::user()->Is(['Admin', 'Super admin'])) {
            $request->validate([
                "status" => "required",
                "id_commande" => "required"
            ]);
            $commande = Commandes::find($request->id_commande);
            if ($commande) {
                $commande->status = $request->status;
                $commande->save();
                $commande->SousCommandes()->update(['status' => $request->status]);
                return response()->json([
                    "message" => "Status de la commande changé avec succès"
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

    public function entrepriseOrders($entreprise){
        $entreprises = Entreprise::find($entreprise);
        if ($entreprises) {
            if (Auth::user()->Is(['Vendor'])) {
                $engagement = Engagement::where('id_user', Auth::user()->id)->where('id_entrep', $entreprises->id)->where('status', 1)->first();
                if ($engagement) {
                    if ($engagement->can('view_order')) {
                        return response()->json([
                            "data" => $entreprises->commandes()->with('commande.user','details.mesure', 'details.marque')->get()
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
}
