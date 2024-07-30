<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeValidation extends Model
{
    use HasFactory;
    protected $table = "t_code_validation";
    protected $fillable = ['code', 'status', 'email'];
}
