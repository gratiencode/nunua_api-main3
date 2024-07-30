<?php

namespace App\Models;

use App\Models\Produits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommandeDetails extends Model
{
    use HasFactory;
    protected $table = "t_commande_details";
    public function products(){
        return $this->belongsTo(Produits::class, 'produit_id', 'id');
    }
}
