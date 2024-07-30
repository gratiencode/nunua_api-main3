<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Collections extends Model
{
    use HasFactory, HasUuids;
    protected $table = 't_collections';
    protected $fillable = ['name', 'description', 'image'];

    public function categories(){
        return $this->belongsToMany(Category::class, 't_collection_has_category', 'id_collection', 'id_categorie');
    }
}
