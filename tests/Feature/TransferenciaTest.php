<?php

use Database\Seeders\ContaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(ContaSeeder::class);
});

it('transfere centavos e registra a movimentacao nas duas contas', function () {
    $response = $this->postJson('/transferencias', [
        'origem' => 'A',
        'destino' => 'B',
        'valor' => 3000,
    ]);

    $response->assertCreated()->assertJsonStructure(['id']);

    $id = $response->json('id');
    $movimento = [
        'id' => $id,
        'origem' => 'A',
        'destino' => 'B',
        'valor' => 3000,
    ];

    $this->getJson('/contas/A')
        ->assertOk()
        ->assertJson(['id' => 'A', 'saldo' => 97000]);

    $this->getJson('/contas/B')
        ->assertOk()
        ->assertJson(['id' => 'B', 'saldo' => 53000]);

    $this->getJson('/contas/A/movimentacoes')
        ->assertOk()
        ->assertExactJson([$movimento]);

    $this->getJson('/contas/B/movimentacoes')
        ->assertOk()
        ->assertExactJson([$movimento]);
});

it('rejeita saldo insuficiente sem alterar saldos nem criar movimentacao', function () {
    $this->postJson('/transferencias', [
        'origem' => 'A',
        'destino' => 'B',
        'valor' => 100001,
    ])->assertStatus(409);

    $this->getJson('/contas/A')
        ->assertOk()
        ->assertJson(['id' => 'A', 'saldo' => 100000]);

    $this->getJson('/contas/B')
        ->assertOk()
        ->assertJson(['id' => 'B', 'saldo' => 50000]);

    $this->getJson('/contas/A/movimentacoes')
        ->assertOk()
        ->assertExactJson([]);

    $this->getJson('/contas/B/movimentacoes')
        ->assertOk()
        ->assertExactJson([]);
});

it('rejeita entrada invalida sem alterar saldos nem criar movimentacao', function (array $payload) {
    $this->postJson('/transferencias', $payload)->assertStatus(400);

    $this->getJson('/contas/A')
        ->assertOk()
        ->assertJson(['id' => 'A', 'saldo' => 100000]);

    $this->getJson('/contas/B')
        ->assertOk()
        ->assertJson(['id' => 'B', 'saldo' => 50000]);

    $this->getJson('/contas/A/movimentacoes')
        ->assertOk()
        ->assertExactJson([]);

    $this->getJson('/contas/B/movimentacoes')
        ->assertOk()
        ->assertExactJson([]);
})->with([
    'valor zero' => [['origem' => 'A', 'destino' => 'B', 'valor' => 0]],
    'valor negativo' => [['origem' => 'A', 'destino' => 'B', 'valor' => -1]],
    'valor com fracao' => [['origem' => 'A', 'destino' => 'B', 'valor' => 3000.5]],
    'campo ausente' => [['origem' => 'A', 'destino' => 'B']],
    'origem igual ao destino' => [['origem' => 'A', 'destino' => 'A', 'valor' => 3000]],
]);

it('devolve 404 quando a conta nao existe e nao cria movimentacao', function () {
    $this->getJson('/contas/Z')->assertNotFound();

    $this->postJson('/transferencias', [
        'origem' => 'Z',
        'destino' => 'B',
        'valor' => 3000,
    ])->assertNotFound();

    $this->getJson('/contas/A')
        ->assertOk()
        ->assertJson(['id' => 'A', 'saldo' => 100000]);

    $this->getJson('/contas/B')
        ->assertOk()
        ->assertJson(['id' => 'B', 'saldo' => 50000]);

    $this->getJson('/contas/A/movimentacoes')
        ->assertOk()
        ->assertExactJson([]);
});
