<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageProduits extends Model
{
    use HasFactory;
    protected $table = "t_images_produits";
    protected $fillable = [
        'id_produit',    'images',    'status'
    ];
}
