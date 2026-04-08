<?php

namespace Database\Seeders;

use App\Models\JenisBabak;
use App\Models\LombaBabakRegu;
use Illuminate\Database\Seeder;

class LombaBabakReguSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get jenis babak IDs by kode
        $p1 = JenisBabak::where('kode', 'P1')->first();
        $p2 = JenisBabak::where('kode', 'P2')->first();
        $p3 = JenisBabak::where('kode', 'P3')->first();

        if (!$p1 || !$p2 || !$p3) {
            $this->command->error('Jenis babak P1, P2, or P3 not found. Please run JenisBabakSeeder first.');
            return;
        }

        $data = [
            ['nomor' => 1, 'jenis_babak_id' => $p1->id, 'kode' => 'P1A', 'uraian' => 'Penyisihan 1, Regu A'],
            ['nomor' => 2, 'jenis_babak_id' => $p1->id, 'kode' => 'P1B', 'uraian' => 'Penyisihan 1, Regu B'],
            ['nomor' => 3, 'jenis_babak_id' => $p1->id, 'kode' => 'P1C', 'uraian' => 'Penyisihan 1, Regu C'],
            ['nomor' => 4, 'jenis_babak_id' => $p2->id, 'kode' => 'P2A', 'uraian' => 'Penyisihan 2, Regu A'],
            ['nomor' => 5, 'jenis_babak_id' => $p2->id, 'kode' => 'P2B', 'uraian' => 'Penyisihan 2, Regu B'],
            ['nomor' => 6, 'jenis_babak_id' => $p2->id, 'kode' => 'P2C', 'uraian' => 'Penyisihan 2, Regu C'],
            ['nomor' => 7, 'jenis_babak_id' => $p3->id, 'kode' => 'P3A', 'uraian' => 'Penyisihan 3, Regu A'],
            ['nomor' => 8, 'jenis_babak_id' => $p3->id, 'kode' => 'P3B', 'uraian' => 'Penyisihan 3, Regu B'],
            ['nomor' => 9, 'jenis_babak_id' => $p3->id, 'kode' => 'P3C', 'uraian' => 'Penyisihan 3, Regu C'],
        ];

        foreach ($data as $item) {
            LombaBabakRegu::firstOrCreate(
                ['kode' => $item['kode']],
                [
                    'nomor' => $item['nomor'],
                    'jenis_babak_id' => $item['jenis_babak_id'],
                    'uraian' => $item['uraian'],
                ]
            );
        }
    }
}
