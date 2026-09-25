#!/bin/sh
set -eu
cd "$(dirname "$0")/.."
export LOCAL_UID=${LOCAL_UID:-$(id -u)}
export LOCAL_GID=${LOCAL_GID:-$(id -g)}
if [ ! -f .env ]; then cp .env.example .env; fi
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
docker compose build app
docker compose run --rm app composer install --no-interaction
if ! grep -Eq '^APP_KEY=.+$' .env; then
    docker compose run --rm app php artisan key:generate --no-interaction
fi
if [ ! -f database/database.sqlite ]; then touch database/database.sqlite; fi
docker compose run --rm app php artisan migrate --force
docker compose run --rm node npm ci
docker compose run --rm node npm run build
docker compose up -d --wait app
printf '%s\n' 'Relay is ready. Open the APP_URL configured in .env.'
printf '%s\n' 'Optional local demo accounts: docker compose run --rm app php artisan db:seed --class=DemoSeeder'
