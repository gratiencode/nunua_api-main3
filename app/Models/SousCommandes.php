<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SousCommandes extends Model
{
    use HasFactory, HasUuids;
    protected $table = 't_sous_commande';
    protected $fillable = [
        "commande_id",
        "id_entrep",
        "grand_total",
        "currency",
        "status",
    ];


    public function details()
    {
        return $this->belongsToMany(Produits::class, 't_sous_commande_detail', 'commande_id', 'produit_id')->withPivot(['price', 'quantity']);
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entrep', 'id');
    }

    public function commande()
    {
        return $this->belongsTo(Commandes::class, 'commande_id', 'id');
    }
}
