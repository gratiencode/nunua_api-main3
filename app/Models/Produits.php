<?php

namespace App\Models;

use App\Models\Entreprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produits extends Model
{
    use HasFactory, HasUuids;
    protected $table = "t_produits";
    protected $fillable = [
        'name_produit', 'description', 'price', 'image',    'id_mesure',    'id_marque',    'id_category',    'id_entrep', 'etat_top',    'deleted',    'price_red',    'qte'
    ];

    public function images()
    {
        return $this->hasMany(ImageProduits::class, 'id_produit', 'id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id');
    }
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'id_entrep', 'id');
    }
    public function mesure()
    {
        return $this->belongsTo(Mesure::class, 'id_mesure', 'id');
    }
    public function marque()
    {
        return $this->belongsTo(Marque::class, 'id_marque', 'id');
    }
}
