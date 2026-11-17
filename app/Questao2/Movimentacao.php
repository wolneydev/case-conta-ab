<?php

namespace App\Questao2;

class Movimentacao
{
    public function __construct(
        public int $id,
        public string $origem,
        public string $destino,
        public int $valor,
    ) {}
}
