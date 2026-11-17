# API de transferências

## Comandos

```bash
cp .env.example .env
docker compose up --build -d
```

O entrypoint gera a `APP_KEY` se ela estiver vazia, roda `migrate --seed` e sobe a API em `http://localhost:8000`.

```bash
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan test
docker compose down
```

O seed usa `upsert` nas contas A e B. Rodar de novo não duplica linhas. O `saldo` volta para `100000` e `50000`.

## Escolhas técnicas

- Laravel 13 e PHP 8.5.12 no Dockerfile (php:8.5.12-cli).
- PostgreSQL 17 para persistir contas e transferencias. Centavos entram como bigint.
- Docker Compose sobe a API e o banco. Não há frontend, autenticação nem deploy.
## Exemplos de requisições

Saldo inicial:

bash
curl -s http://localhost:8000/contas/A
curl -s http://localhost:8000/contas/B


Transferência válida (A → B, 3000 centavos). Depois disso A fica 97000 e B fica 53000:

bash
curl -s -X POST http://localhost:8000/transferencias \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"origem":"A","destino":"B","valor":3000}'


Histórico das duas contas:

bash
curl -s http://localhost:8000/contas/A/movimentacoes
curl -s http://localhost:8000/contas/B/movimentacoes


Entrada inválida (400):

bash
curl -s -o /dev/null -w "%{http_code}\n" -X POST http://localhost:8000/transferencias \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"origem":"A","destino":"B","valor":0}'


Conta inexistente (404):

bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8000/contas/Z


Saldo insuficiente (409):

bash
curl -s -o /dev/null -w "%{http_code}\n" -X POST http://localhost:8000/transferencias \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"origem":"A","destino":"B","valor":100001}'



## Melhorias
Idempotência (Chaves de Idempotência)

O problema: Quedas de rede em clientes geram retries automáticos. Se a API não for idempotente, o mesmo pagamento/transferência é executado múltiplas vezes.

A solução: Atribuir um UUID ou idempotency key único por transação. Repetições da mesma requisição retornam o resultado cacheado do processamento original sem reexecutar a mutação de estado.

Concorrência e Race Conditions

O problema: Duas threads ou requisições paralelas leem o saldo ao mesmo tempo, validam que há fundos e debitam, levando a conta a um saldo negativo.

A solução: Uso de mecanismos de travamento para o acesso concorrente aos recursos da conta.

Transações Atômicas vs. Ambientes em Memória

O problema: Testes unitários que rodam inteiramente em memória mascaram falhas de infraestrutura. Se o processo morrer no meio de um fluxo de débito e crédito, o sistema fica em estado inconsistente.

Solução: Garantir transações nativas no banco de dados relacional, assegurando a Atomicidade ou todas as operações da transação ocorrem com sucesso ou ocorre um rollback para desfazer a operação.

Tratamento de Erros:
O problema: Tentar mascarar exceções cria um falso senso de segurança enquanto corrompe dados da aplicação.

Solução: Permitir que erro suba para manter a transparência operacional, combinando gerenciamento adequado de estado para que falhas acionem rollbacks.