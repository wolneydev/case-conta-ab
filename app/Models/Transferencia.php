<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    public $timestamps = false;

    protected $table = 'transferencias';

    protected $fillable = [
        'origem',
        'destino',
        'valor',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'valor' => 'integer',
        ];
    }
}
