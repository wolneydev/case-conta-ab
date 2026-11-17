<?php

namespace Database\Seeders;

use App\Models\Conta;
use Illuminate\Database\Seeder;

class ContaSeeder extends Seeder
{
    public function run(): void
    {
        Conta::upsert(
            [
                ['id' => 'A', 'saldo' => 100000],
                ['id' => 'B', 'saldo' => 50000],
            ],
            ['id'],
            ['saldo'],
        );
    }
}
