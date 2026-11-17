<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transferencias', function (Blueprint $table) {
            $table->id();
            $table->string('origem');
            $table->string('destino');
            $table->bigInteger('valor');

            $table->foreign('origem')->references('id')->on('contas');
            $table->foreign('destino')->references('id')->on('contas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transferencias');
    }
};
