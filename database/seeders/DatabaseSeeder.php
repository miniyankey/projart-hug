<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => 'Admin',
            'surname' => 'CTS',
            'email' => 'admin@hug.ch',
            'password' => 'password',
        ]);

        $this->call([
            CompanySeeder::class,
            PlaceSeeder::class,
        ]);
    }
}
