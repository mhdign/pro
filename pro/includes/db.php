<?php
// تنظیمات دیتابیس
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pro');

class Database
{
    private $conn;
    public $error = '';

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        try {
            // ایجاد اتصال
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            // بررسی خطای اتصال
            if ($this->conn->connect_error) {
                $this->error = $this->getFriendlyErrorMessage($this->conn->connect_errno);
                error_log("Database Connection Error: " . $this->conn->connect_error);
                return false;
            }

            // تنظیم کدگذاری
            if (!$this->conn->set_charset("utf8mb4")) {
                $this->error = "خطا در تنظیم کدگذاری کاراکترها";
                error_log("Charset Error: " . $this->conn->error);
                return false;
            }

            return true;
        } catch (Exception $e) {
            $this->error = "خطا در اتصال به پایگاه داده";
            error_log("Database Exception: " . $e->getMessage());
            return false;
        }
    }

    private function getFriendlyErrorMessage($error_code)
    {
        $messages = [
            1044 => "دسترسی به پایگاه داده رد شد. لطفاً با مدیر سیستم تماس بگیرید.",
            1045 => "نام کاربری یا رمز عبور پایگاه داده نادرست است.",
            1049 => "پایگاه داده انتخاب شده وجود ندارد.",
            2002 => "سرور پایگاه داده در دسترس نیست. لطفاً بعداً تلاش کنید.",
            2006 => "اتصال به سرور پایگاه داده قطع شد.",
            2019 => "خطا در تنظیم کدگذاری کاراکترها",
            0 => "خطای ناشناخته در اتصال به پایگاه داده رخ داده است."
        ];

        return $messages[$error_code] ?? $messages[0];
    }

    public function getConnection()
    {
        // اگر اتصال از دست رفته، مجدداً وصل شو
        if (!$this->conn || !$this->conn->ping()) {
            $this->connect();
        }
        return $this->conn;
    }

    public function prepare($sql)
    {
        $conn = $this->getConnection();
        if (!$conn) {
            return false;
        }

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $this->error = "خطا در آماده‌سازی کوئری: " . $conn->error;
            error_log("Prepare Error: " . $conn->error);
            return false;
        }

        return $stmt;
    }

    public function query($sql)
    {
        $conn = $this->getConnection();
        if (!$conn) {
            return false;
        }

        $result = $conn->query($sql);
        if (!$result) {
            $this->error = "خطا در اجرای کوئری: " . $conn->error;
            error_log("Query Error: " . $conn->error);
            return false;
        }

        return $result;
    }

    public function getError()
    {
        return $this->error;
    }

    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// ایجاد اتصال جهانی
try {
    $database = new Database();
    $conn = $database->getConnection();

    // بررسی موفقیت‌آمیز بودن اتصال
    if (!$conn || $database->error) {
        error_log("Failed to establish database connection: " . $database->error);
        // در اینجا اتصال cut نمی‌شود تا برنامه بتواند ادامه دهد
    }
} catch (Exception $e) {
    error_log("Database initialization failed: " . $e->getMessage());
    // ادامه بدون قطع کردن برنامه
}


// Silent mode - don't output connection status
// Connection status is now checked in index.php
