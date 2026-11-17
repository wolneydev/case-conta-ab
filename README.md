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

