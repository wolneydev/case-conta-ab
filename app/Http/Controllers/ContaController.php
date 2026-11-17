<?php

namespace App\Http\Controllers;

use App\Services\TransferenciaService;
use Illuminate\Http\JsonResponse;

class ContaController extends Controller
{
    public function show(string $id, TransferenciaService $service): JsonResponse
    {
        $conta = $service->buscarConta($id);

        return response()->json([
            'id' => $conta->id,
            'saldo' => $conta->saldo,
        ]);
    }

    public function movimentacoes(string $id, TransferenciaService $service): JsonResponse
    {
        return response()->json($service->listarMovimentacoes($id));
    }
}
