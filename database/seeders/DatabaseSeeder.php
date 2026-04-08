<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProvinsiSeeder::class,
            JenisBabakSeeder::class,
            LombaBabakReguSeeder::class,
        ]);
    }
}
