<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cupons;
use Illuminate\Support\Carbon;

class CuponsSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        for ($i = 1; $i <= 10; $i++) {
            $data_inicio = $now->copy()->addDays($i - 1);
            $data_fim = $data_inicio->copy()->addDays(5);

            Cupons::create([
                'nome'        => 'CUPOM' . $i,
                'desconto'    => rand(5, 20), // desconto entre 5% e 20%
                'data_inicio' => $data_inicio,
                'data_fim'    => $data_fim,
                'ativo'       => true,
                'user_id'     => rand(1, 5) // Associando a um usuário aleatório entre 1 e 5
            ]);
        }
    }
}