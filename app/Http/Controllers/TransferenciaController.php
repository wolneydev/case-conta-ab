<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferirRequest;
use App\Services\TransferenciaService;
use Illuminate\Http\JsonResponse;

class TransferenciaController extends Controller
{
    public function store(TransferirRequest $request, TransferenciaService $service): JsonResponse
    {
        $transferencia = $service->transferir(
            $request->string('origem')->toString(),
            $request->string('destino')->toString(),
            $request->integer('valor'),
        );

        return response()->json([
            'id' => $transferencia->id,
        ], 201);
    }
}
