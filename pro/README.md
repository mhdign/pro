# 🚀 Pro - سیستم مدیریت مالی

یک سیستم مدیریت مالی مدرن با PHP، MySQL و رابط کاربری React که بر روی XAMPP اجرا می‌شود.

## ✨ ویژگی‌ها

- ✅ احراز هویت کامل با JWT
- ✅ مدیریت کاربران با نقش‌های مختلف
- ✅ پشتیبانی از فارسی (UTF8MB4)
- ✅ طراحی زیبا و مدرن
- ✅ رابط کاربری واکنش‌گرا
- ✅ امنیت بالا
- ✅ مدیریت تراکنش‌های مالی
- ✅ سیستم پیام‌رسانی
- ✅ اطلاع‌رسانی‌ها
- ✅ مدیریت وظایف

## 📋 پیش‌نیازها

- XAMPP (شامل Apache و MySQL)
- PHP 7.4 یا بالاتر
- MySQL 5.7 یا بالاتر
- مرورگر وب مدرن

## 🚀 راه‌اندازی سریع

### 1. فایل‌های پروژه را در مسیر XAMPP قرار دهید

```
C:\xampp\htdocs\pro\
```

### 2. اجرای پروژه

1. XAMPP را اجرا کنید
2. Apache و MySQL را Start کنید
3. به آدرس زیر بروید:
   ```
   http://localhost/pro/
   ```

### 3. راه‌اندازی پایگاه داده

برای ایجاد خودکار دیتابیس و جدول‌ها، به آدرس زیر بروید:
```
http://localhost/pro/setup_database.php
```

یا از طریق کامند لاین:
   ```bash
cd C:\xampp\htdocs\pro
php setup_database.php
```

## 📁 ساختار پروژه

```
pro/
├── index.php                  # صفحه اصلی
├── login.html                  # صفحه ورود
├── signup.html                 # صفحه ثبت‌نام
├── setup_database.php          # راه‌اندازی پایگاه داده
├── test_api.php                # تست API
├── api/
│   └── auth.php               # API احراز هویت
├── includes/
│   └── db.php                 # اتصال دیتابیس
├── config/
│   ├── app.php                # تنظیمات برنامه
│   └── database.php           # تنظیمات دیتابیس
├── css/
│   └── globals.css            # استایل‌های جهانی
├── database/
│   └── migrations/
│       └── 001_create_tables.sql
└── pages/
    ├── login.jsx               # صفحه ورود React
    └── signup.jsx              # صفحه ثبت‌نام React
```

## 🎯 صفحات اصلی

- **صفحه اصلی**: `http://localhost/pro/`
- **ورود**: `http://localhost/pro/login.html`
- **ثبت‌نام**: `http://localhost/pro/signup.html`

## 🔌 API Endpoints

### ورود (Login)
```
POST /pro/api/auth.php/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

### ثبت‌نام (Signup)
```
POST /pro/api/auth.php/signup
Content-Type: application/json

{
  "name": "نام کاربر",
  "email": "user@example.com",
  "password": "password",
  "birthdate": "1990-01-01"
}
```

## 💾 پایگاه داده

### اطلاعات اتصال
- **Host**: localhost
- **Username**: root
- **Password**: (خالی)
- **Database**: pro_db
- **Charset**: utf8mb4
- **Collation**: utf8mb4_unicode_ci

### جدول‌ها
- `users` - کاربران
- `refresh_tokens` - توکن‌های تازه‌سازی
- `activities` - فعالیت‌های کاربران
- `transactions` - تراکنش‌های مالی
- `messages` - پیام‌ها
- `notifications` - اطلاع‌رسانی‌ها
- `tasks` - وظایف
- `migrations` - تاریخچه مایگریشن‌ها

## 🛠️ توسعه

### ساختار احراز هویت

پروژه از JWT برای احراز هویت استفاده می‌کند:
- **Access Token**: زمان انقضا: 24 ساعت
- **Refresh Token**: زمان انقضا: 7 روز

### افزودن جدول جدید

1. فایل SQL را در `database/migrations/` ایجاد کنید
2. در `setup_database.php` import کنید
3. یا مستقیماً در phpMyAdmin اجرا کنید

### استایل‌دهی

فایل‌های CSS در `css/globals.css` قرار دارند.

## 🧪 تست

برای تست سیستم، به آدرس زیر بروید:
```
http://localhost/pro/test_api.php
```

این صفحه موارد زیر را بررسی می‌کند:
- ✓ اتصال دیتابیس
- ✓ وجود جدول‌ها
- ✓ وجود API
- ✓ پاسخ‌دهی سرور HTTP

## 🐛 رفع مشکلات

### خطای "Not Found"
- مطمئن شوید Apache در XAMPP اجرا است
- مسیر فایل‌ها را بررسی کنید

### خطای اتصال دیتابیس
```
http://localhost/pro/setup_database.php
```

### خطای MySQL
- از phpMyAdmin وارد شوید: `http://localhost/phpmyadmin`
- بررسی کنید دیتابیس `pro_db` وجود دارد

### فارسی نمایش داده نمی‌شود
- کدگذاری دیتابیس را بررسی کنید (باید utf8mb4 باشد)
- Headers را بررسی کنید (`Content-Type: text/html; charset=utf-8`)

## 📝 لاگ

### لاگ‌های PHP
```
C:\xampp\php\logs\
```

### لاگ‌های Apache
```
C:\xampp\apache\logs\
```

### لاگ‌های MySQL
```
C:\xampp\mysql\data\
```

## 🔒 امنیت

### تنظیمات امنیتی
- ✓ رمزگذاری پسورد با `password_hash()`
- ✓ Prepared Statements برای جلوگیری از SQL Injection
- ✓ CORS تنظیم شده
- ✓ Token-based Authentication

### پیشنهادات برای تولید
- تغییر `JWT_SECRET` در config
- غیرفعال کردن `display_errors` در production
- استفاده از HTTPS
- تنظیم rate limiting

## 📚 مستندات بیشتر

- `SETUP.md` - راهنمای نصب تفصیلی
- `DEVELOPMENT.md` - راهنمای توسعه

## 👤 نقش‌ها

- **owner** - مالک
- **tenant** - مستأجر
- **manager** - مدیر
- **admin** - مدیر کل

## 📞 پشتیبانی

برای مشکلات فنی، لاگ‌ها را بررسی کنید یا مسائل را در GitHub مطرح کنید.

## 📄 لایسنس

MIT License

## ✨ نسخه

**نسخه فعلی**: 2.0.0

---

**توسعه یافته با ❤️ برای جامعه برنامه‌نویسان فارسی‌زبان**