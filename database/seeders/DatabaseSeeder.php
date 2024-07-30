<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Country;
use App\Models\PermissionsModels;
use App\Models\User;
use App\Models\TypesUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        $country = Country::create(['name' => "Congo, the Democratic Republic of the (Kinshasa)", "code" => "CD"]);
        $country->ville()->create(['name' => "Bukavu"]);
        $country->ville()->create(['name' => "Kinshasa"]);
        $country->ville()->create(['name' => "Goma"]);
        $country->ville()->create(['name' => "Lubumbashi"]);

        TypesUser::create(['name' => "Super admin"]);
        TypesUser::create(['name' => "Admin"]);
        TypesUser::create(['name' => "Vendor"]);
        TypesUser::create(['name' => "Livreur"]);
        $json = File::get("database/permissions.json");
        $permissions = json_decode($json);
        foreach ($permissions as $item) {
            PermissionsModels::create(["name" => $item->name, "pseudo" => $item->guard_name]);
        }

        $user = User::create([
            'full_name' => 'Samuel Baeni',
            'email' => 'baenisam@gmail.com',
            "pswd" => Hash::make(123456),
            "pays" => 1,
            "ville" => 3
        ]);
        $type = TypesUser::where('name', 'Super admin')->first();
        $user->userType()->attach($type->id);
    }
}
