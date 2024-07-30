<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionsModels extends Model
{
    use HasFactory, HasUuids;
    protected $table="t_permissions";
    protected $fillable=['name', 'status','pseudo'];
}
