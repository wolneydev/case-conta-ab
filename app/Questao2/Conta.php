<?php

namespace App\Questao2;

class Conta
{
    public function __construct(
        public string $id,
        public int $saldo,
        public bool $bloqueada = false,
    ) {}
}
