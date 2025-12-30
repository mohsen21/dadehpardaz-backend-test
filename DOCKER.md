# Docker

## راه‌اندازی سریع

```bash
./docker-setup.sh
```

یا دستی:

```bash
cp .env.docker.example .env
docker-compose up -d --build
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan storage:link
docker-compose exec app bash docker-init-minio.sh
```

## دسترسی

- اپلیکیشن: http://localhost
- MinIO: http://localhost:9001 (minioadmin/minioadmin)

## دستورات مفید

```bash
# لاگ‌ها
docker-compose logs -f

# اجرای artisan
docker-compose exec app php artisan [command]

# shell
docker-compose exec app bash

# توقف
docker-compose stop

# حذف
docker-compose down
```

## سرویس‌ها

- app: PHP-FPM
- nginx: وب سرور
- postgres: دیتابیس
- redis: کش
- minio: ذخیره فایل
- queue: پردازش صف
