<?php

namespace App\Models;

use App\Models\Engagement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Roles extends Model
{
    use HasFactory;
    protected $table = "t_roles";
    protected $fillable = ['id_entrep',	'name'];
    public function engagements(){
        return $this->hasMany(Engagement::class, 'id_role', 'id');
    }
}
