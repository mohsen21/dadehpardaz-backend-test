# Test project

سیستم مدیریت درخواست هزینه برای سازمان

## نیازمندی‌ها

- PHP >= 8.2
- Composer
- PostgreSQL
- MinIO (برای ذخیره‌سازی فایل‌ها)

## نصب

```bash
composer install
cp .env.example .env
php artisan key:generate
```

تنظیم دیتابیس PostgreSQL در `.env`:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=expense_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

تنظیم MinIO در `.env`:

```
MINIO_ENDPOINT=http://localhost:9000
MINIO_KEY=your_key
MINIO_SECRET=your_secret
MINIO_BUCKET=expense-attachments
MINIO_REGION=us-east-1
```

ساخت دیتابیس و اجرای migrations:

```bash
php artisan migrate
php artisan db:seed
```

## اجرا

```bash
php artisan serve
```

## API Endpoints

- `POST /api/expense-requests` - ثبت درخواست
- `GET /api/expense-requests` - لیست درخواست‌ها
- `GET /api/expense-requests/{id}` - جزئیات درخواست
- `GET /api/approvals` - کارتابل تایید
- `POST /api/approvals/action` - تایید/رد درخواست
- `GET /api/approvals/{id}/download` - دانلود فایل
- `GET /api/approvals/approved` - درخواست‌های تایید شده
- `POST /api/approvals/process-payment` - پرداخت دستی

## پرداخت خودکار

پرداخت خودکار هر روز در ساعت مشخص (قابل تنظیم در `config/payment.php`) اجرا می‌شود.

## نکات

- این پروژه برای تست فنی است و از Docker استفاده نشده است
- PostgreSQL به دلیل MVCC و عدم نیاز به lock انتخاب شده است
- MinIO برای ذخیره‌سازی فایل‌ها استفاده می‌شود
- می‌شد از Swagger برای مستندسازی API استفاده کرد اما به خاطر سادگی پروژه، مستندات در همین README قرار داده شده است
