<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\TypesUser;
use App\Models\Engagement;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;
    protected $table="t_users";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'pswd',
        'phone',
        'pays',
        'ville',
        'gender',
        'status',
        'profil',
        'provider'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function userType()
    {
        return $this->belongsToMany(TypesUser::class, 't_affect_type_user', 'user_id', 'types_id');
    }
    public function engagement(){
        return $this->hasMany(Engagement::class, 'id_user', 'id');
    }
    public function Is($name)
    {
        return $this->userType()->whereIn('name', $name)->exists();
    }
    public function commandes()
    {
        return $this->hasMany(Commandes::class, 'user_id', 'id');
    }
    public function ville()
    {
        return $this->belongsTo(City::class, 'ville', 'id');
    }
    public function pays()
    {
        return $this->belongsTo(Country::class, 'pays', 'id');
    }
    // public function codeValidation(){
    //     return $this->hasOne(CodeValidation::class, 'user_id', 'id');
    // }
}
