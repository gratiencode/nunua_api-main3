<?php

namespace App\Models;

use App\Models\Roles;
use App\Models\Produits;
use App\Models\Engagement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entreprise extends Model
{
    use HasFactory, HasUuids;
    protected $table = "t_entreprise";
    protected $fillable = [
        'name',    'description', 'email', 'phone',  'ville', 'pays', 'num_impot', 'rccm_document', 'import_document', 'idnat',    'rccm',    'status'
    ];
    public function produits()
    {
        return $this->hasMany(Produits::class, 'id_entrep', 'id');
    }
    public function engagement()
    {
        return $this->hasMany(Engagement::class, 'id_entrep', 'id');
    }
    public function role()
    {
        return $this->hasMany(Roles::class, 'id_entrep', 'id');
    }
    public function mesures()
    {
        return $this->hasMany(Mesure::class, 'id_entrep', 'id');
    }

    public function commandes(){
        return $this->hasMany(SousCommandes::class, 'id_entrep', 'id');
    }
}
