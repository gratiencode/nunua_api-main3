<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = 't_country';
    protected $fillable = ['name', 'code', 'status', 'deleted'];

    public function ville(){
        return $this->hasMany(City::class, 'country_id', 'id')->where('deleted', 0);
    }
}
