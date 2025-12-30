#!/bin/bash
set -e

echo "راه‌اندازی Docker..."

if [ ! -f .env ]; then
    cp .env.docker.example .env
fi

docker-compose up -d --build
sleep 5

docker-compose exec -T app composer install --ignore-platform-reqs || docker-compose exec -T app composer update --no-interaction --ignore-platform-reqs
docker-compose exec -T app php artisan key:generate --force || true
docker-compose exec -T app php artisan migrate --force

read -p "Seed دیتابیس؟ (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    docker-compose exec -T app php artisan db:seed --force
fi

docker-compose exec -T app php artisan storage:link || true
docker-compose exec -T app bash docker-init-minio.sh || echo "MinIO setup skipped"

echo ""
echo "✅ آماده است!"
echo "   اپلیکیشن: http://localhost"
echo "   MinIO: http://localhost:9001 (minioadmin/minioadmin)"

