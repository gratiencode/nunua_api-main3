<?php

namespace App\Models;

use App\Models\User;
use App\Models\Produits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commandes extends Model
{
    use HasFactory, HasUuids;
    protected $table = "t_commandes";
    protected $fillable = [
        'grand_total',
        'items_count',
        'user_id',
        'status',
        'payment_method',
        'shipping_adresse',
        'shipping_city',
        'shipping_country',
        'shipping_code',
        'billing_fullname',
        'billing_phone',
        'billing_email',
        'billing_currency',
        'free_shipping',
        'order_number',
        'total'
    ];
    public function details()
    {
        return $this->belongsToMany(Produits::class, 't_commande_details', 'commande_id', 'produit_id')->withPivot(['price', 'quantity']);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function getNameAttribute()
    {
        return $this->getRelationValue('details')->pivot;
    }
    public function SousCommandes()
    {
        return $this->hasMany(SousCommandes::class, 'commande_id', 'id');
    }

    public function creationSousCommande()
    {
        foreach ($this->details->groupBy('id_entrep') as $id_entre => $produit) {
            $entreprise = Entreprise::find($id_entre);
            $everyPriceProductArray = array();
            foreach ($produit as $key => $item) {
                $price = $item->pivot->price * $item->pivot->quantity;
                array_push($everyPriceProductArray, $price);
            }
            $everyPriceProduct = array_sum($everyPriceProductArray);
            $sousCom = $this->SousCommandes()->create([
                'id_entrep' => $entreprise->id,
                'grand_total' => $everyPriceProduct,
                "currency" => $this->billing_currency
            ]);
            foreach ($produit as $prod) {
                $sousCom->details()->attach($prod->id, ['price' => $prod->pivot->price, 'quantity' => $prod->pivot->quantity]);
            }
        }
    }
}
