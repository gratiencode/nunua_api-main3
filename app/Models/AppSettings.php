<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSettings extends Model
{
    use HasFactory, HasUuids;
    protected $table = "app_settings";
    protected $fillable = [
        "app_name", "email", "phone","lat","long","adresse","facebook","youtube"	,"linkedin"	,"instagram","about_us","mission"
    ];

    public static function createOrUpdate($data, $keys) {
        $record = self::where("id", $keys)->first();
        if (is_null($record)) {
            return self::create($data);
        } else {
            return $record->update($data);
        }
    }
}
