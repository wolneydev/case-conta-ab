<?php

namespace App\Questao2;

class ServicoTransferenciaComFalha
{
    private array $movimentacoes = [];

    public function transferir(Conta $origem, Conta $destino, int $valor): Movimentacao
    {
        $this->rejeitarSeSaldoInsuficiente($origem, $valor);
        $this->debitar($origem, $valor);
        $this->creditar($destino, $valor);

        return $this->registrar($origem->id, $destino->id, $valor);
    }

    public function listarMovimentacoes(): array
    {
        return $this->movimentacoes;
    }

    private function rejeitarSeSaldoInsuficiente(Conta $origem, int $valor): void
    {
        if ($origem->saldo < $valor) {
            throw new \RuntimeException('Saldo insuficiente.');
        }
    }

    private function debitar(Conta $origem, int $valor): void
    {
        $origem->saldo -= $valor;
    }

    private function creditar(Conta $destino, int $valor): void
    {
        if ($destino->bloqueada) {
            throw new \RuntimeException('Conta destino bloqueada.');
        }

        $destino->saldo += $valor;
    }

    private function registrar(string $origem, string $destino, int $valor): Movimentacao
    {
        $movimentacao = new Movimentacao(
            id: count($this->movimentacoes) + 1,
            origem: $origem,
            destino: $destino,
            valor: $valor,
        );

        $this->movimentacoes[] = $movimentacao;

        return $movimentacao;
    }
}
