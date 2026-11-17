<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $table = 'contas';

    protected $fillable = [
        'id',
        'saldo',
    ];

    protected function casts(): array
    {
        return [
            'saldo' => 'integer',
        ];
    }
}
