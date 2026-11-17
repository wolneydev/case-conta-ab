<?php

namespace App\Services;

use App\Models\Conta;
use App\Models\Transferencia;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransferenciaService
{
    public function transferir(string $origemId, string $destinoId, int $valor): Transferencia
    {
        return DB::transaction(function () use ($origemId, $destinoId, $valor) {
            $origem = $this->buscarConta($origemId);
            $destino = $this->buscarConta($destinoId);

            if ($origem->saldo < $valor) {
                abort(409, 'Saldo insuficiente.');
            }

            $origem->saldo -= $valor;
            $origem->save();

            $destino->saldo += $valor;
            $destino->save();

            return Transferencia::query()->create([
                'origem' => $origem->id,
                'destino' => $destino->id,
                'valor' => $valor,
            ]);
        });
    }

    public function buscarConta(string $id): Conta
    {
        return Conta::query()->findOrFail($id);
    }

    public function listarMovimentacoes(string $contaId): Collection
    {
        $this->buscarConta($contaId);

        return Transferencia::query()
            ->where(function ($query) use ($contaId) {
                $query->where('origem', $contaId)
                    ->orWhere('destino', $contaId);
            })
            ->orderBy('id')
            ->get(['id', 'origem', 'destino', 'valor'])
            ->map(fn (Transferencia $transferencia) => [
                'id' => $transferencia->id,
                'origem' => $transferencia->origem,
                'destino' => $transferencia->destino,
                'valor' => $transferencia->valor,
            ])
            ->values();
    }
}
