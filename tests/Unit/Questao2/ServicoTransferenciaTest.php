<?php

use App\Questao2\Conta;
use App\Questao2\ServicoTransferencia;
use App\Questao2\ServicoTransferenciaComFalha;
use RuntimeException;

it('mostra a falha original quando o destino esta bloqueado', function () {
    $origem = new Conta(id: 'A', saldo: 10000, bloqueada: false);
    $destino = new Conta(id: 'B', saldo: 5000, bloqueada: true);
    $servico = new ServicoTransferenciaComFalha;

    expect(fn () => $servico->transferir($origem, $destino, 3000))
        ->toThrow(RuntimeException::class, 'Conta destino bloqueada.');

    expect($origem->saldo)->toBe(7000);
    expect($destino->saldo)->toBe(5000);
    expect($servico->listarMovimentacoes())->toHaveCount(0);
});

it('mostra a correcao quando o destino esta bloqueado', function () {
    $origem = new Conta(id: 'A', saldo: 10000, bloqueada: false);
    $destino = new Conta(id: 'B', saldo: 5000, bloqueada: true);
    $servico = new ServicoTransferencia;

    expect(fn () => $servico->transferir($origem, $destino, 3000))
        ->toThrow(RuntimeException::class, 'Conta destino bloqueada.');

    expect($origem->saldo)->toBe(10000);
    expect($destino->saldo)->toBe(5000);
    expect($servico->listarMovimentacoes())->toHaveCount(0);
});

it('transfere quando a conta B esta desbloqueada', function () {
    $origem = new Conta(id: 'A', saldo: 10000, bloqueada: false);
    $destino = new Conta(id: 'B', saldo: 5000, bloqueada: false);
    $servico = new ServicoTransferencia;

    $movimentacao = $servico->transferir($origem, $destino, 3000);

    expect($origem->saldo)->toBe(7000);
    expect($destino->saldo)->toBe(8000);
    expect($servico->listarMovimentacoes())->toHaveCount(1);
    expect($movimentacao->origem)->toBe('A');
    expect($movimentacao->destino)->toBe('B');
    expect($movimentacao->valor)->toBe(3000);
});

it('rejeita saldo insuficiente sem alterar saldos nem criar movimentacao', function () {
    $origem = new Conta(id: 'A', saldo: 10000, bloqueada: false);
    $destino = new Conta(id: 'B', saldo: 5000, bloqueada: false);
    $servico = new ServicoTransferencia;

    expect(fn () => $servico->transferir($origem, $destino, 10001))
        ->toThrow(RuntimeException::class, 'Saldo insuficiente.');

    expect($origem->saldo)->toBe(10000);
    expect($destino->saldo)->toBe(5000);
    expect($servico->listarMovimentacoes())->toHaveCount(0);
});
