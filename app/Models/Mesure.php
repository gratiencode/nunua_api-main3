<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesure extends Model
{
    use HasFactory, HasUuids;
    protected $table = 't_mesure';
    protected $fillable = ['name', 'status', 'deleted'];
}
