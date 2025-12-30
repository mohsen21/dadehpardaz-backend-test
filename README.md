# Test project

سیستم مدیریت درخواست هزینه برای سازمان

## نیازمندی‌ها

-   PHP >= 8.2
-   Composer
-   PostgreSQL
-   MinIO (برای ذخیره‌سازی فایل‌ها)

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

-   `POST /api/expense-requests` - ثبت درخواست
-   `GET /api/expense-requests` - لیست درخواست‌ها
-   `GET /api/expense-requests/{id}` - جزئیات درخواست
-   `GET /api/approvals` - کارتابل تایید
-   `POST /api/approvals/action` - تایید/رد درخواست
-   `GET /api/approvals/{id}/download` - دانلود فایل
-   `GET /api/approvals/approved` - درخواست‌های تایید شده
-   `POST /api/approvals/process-payment` - پرداخت دستی


## سوالاتی که برام در پروژه وجود داشت
### 1. در اجرای پروژه
توی یک بخش از داکیومنت گفته شده بود که نیازی به اجرای پروژه نیست و جای دیگه نوشته شده بود که برای اینکه پروژه به خطا نخوره کد مربوط به ارسال ایمیل بوده


### 2. جدول کاربر
متاسفانه متوجه اینکه شماره شبا توی جدول کاربر باشه یا برای هر درخواست جدا باشه نشدم 


### 3. امکان ساده تر کردن پروژه
پروژه رو میشد ساده تر پیش برد ولی خواستم یه سری موارد رو داخلش به کار ببرم مثل repository و minio و...

## پرداخت خودکار

پرداخت خودکار هر روز در ساعت مشخص (قابل تنظیم در `config/payment.php`) اجرا می‌شود.

**نکته مهم:** شماره شبا (`sheba_number`) در جدول `expense_requests` ذخیره می‌شود، نه در جدول `users`. این به این دلیل است که هر کاربر ممکن است برای درخواست‌های مختلف از شماره شباهای متفاوتی استفاده کند.

## سوالات متداول (FAQ)

### 1. درباره محل ذخیره‌سازی شماره شبا

شماره شبا (`sheba_number`) در جدول `expense_requests` ذخیره می‌شود، نه در جدول `users`.

**دلایل این طراحی:**

-   هر کاربر ممکن است برای درخواست‌های مختلف از شماره شباهای متفاوتی استفاده کند
-   امکان تغییر شماره شبا برای هر درخواست وجود دارد
-   اطلاعات کاربر (در جدول `users`) از اطلاعات درخواست (در جدول `expense_requests`) جدا نگه داشته شده است

**مثال:** کاربری ممکن است برای یک درخواست از شماره شبا حساب شخصی و برای درخواست دیگر از شماره شبا حساب شرکت استفاده کند.

## نکات

-   این پروژه برای تست فنی است و از Docker استفاده نشده است
-   PostgreSQL به دلیل MVCC و عدم نیاز به lock انتخاب شده است
-   MinIO برای ذخیره‌سازی فایل‌ها استفاده می‌شود
-   می‌شد از Swagger برای مستندسازی API استفاده کرد اما به خاطر سادگی پروژه، مستندات در همین README قرار داده شده است
