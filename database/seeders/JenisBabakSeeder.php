<?php

namespace Database\Seeders;

use App\Models\JenisBabak;
use Illuminate\Database\Seeder;

class JenisBabakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kode' => 'P1', 'nama' => 'Penyisihan 1'],
            ['kode' => 'P2', 'nama' => 'Penyisihan 2'],
            ['kode' => 'P3', 'nama' => 'Penyisihan 3'],
            ['kode' => 'FN', 'nama' => 'Final'],
        ];

        foreach ($data as $item) {
            JenisBabak::firstOrCreate(
                ['kode' => $item['kode']],
                ['nama' => $item['nama']]
            );
        }
    }
}
