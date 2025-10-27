<?php
class NotificationManager
{
    public static function addSuccess($message)
    {
        $_SESSION['notification'] = json_encode([
            'message' => $message,
            'type' => 'success'
        ]);
    }

    public static function addError($message)
    {
        $_SESSION['notification'] = json_encode([
            'message' => $message,
            'type' => 'error'
        ]);
    }

    public static function clear()
    {
        unset($_SESSION['notification']);
    }

    public static function get()
    {
        if (isset($_SESSION['notification'])) {
            $notification = json_decode($_SESSION['notification'], true);
            self::clear();
            return $notification;
        }
        return null;
    }
}
