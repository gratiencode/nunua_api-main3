<?php

namespace App\Models;

use App\Models\Roles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Engagement extends Model
{
    use HasFactory;
    protected $table = "t_engagement";
    protected $fillable = ['id_user', 'id_entrep', 'id_role'];

    public function user(){
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function entreprise(){
        return $this->belongsTo(Entreprise::class, 'id_entrep', 'id');
    }
    public function permissions()
    {
        return $this->belongsToMany(PermissionsModels::class, 't_affectation', 'id_engagement', 'id_permission');
    }
    public function role(){
        return $this->belongsTo(Roles::class, 'id_role', 'id');
    }

    public function can($name)
    {
        return $this->permissions()->where('pseudo', $name)->where('status', 1)->where('deleted', 0)->exists();
    }

   
}
