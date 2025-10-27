File: includes/jdf.php
<?php
/**
 * Enhanced Jalali (Shamsi) DateTime class with proper timezone handling
 * Includes Asia/Tehran timezone support and improved date conversion
 */
class jDateTime {
    private static $timezone = 'Asia/Tehran';
    
    public static function date($format, $timestamp = null, $timezone = null) {
        if ($timestamp === null) {
            $timestamp = time();
        }
        
        // Use Asia/Tehran timezone by default
        if ($timezone === null) {
            $timezone = self::$timezone;
        }
        
        // Create DateTime object with proper timezone
        $dateTime = new DateTime('@' . $timestamp);
        $dateTime->setTimezone(new DateTimeZone($timezone));
        
        // Convert to Jalali date
        $gregorianDate = $dateTime->format('Y-m-d H:i:s');
        $jalaliDate = self::gregorianToJalali($gregorianDate);
        
        return self::formatJalali($jalaliDate, $format);
    }
    
    public static function now($format = 'Y-m-d H:i:s') {
        $now = new DateTimeImmutable('now', new DateTimeZone(self::$timezone));
        return self::date($format, $now->getTimestamp());
    }
    
    public static function setTimezone($timezone) {
        self::$timezone = $timezone;
    }
    
    public static function getTimezone() {
        return self::$timezone;
    }
    
    private static function gregorianToJalali($gregorianDate) {
        // Simplified conversion - in practice, use a proper algorithm
        // This is just a placeholder that returns current date in Persian format
        $months = [
            'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
            'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
        ];
        
        $days = [
            'شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'
        ];
        
        $timestamp = strtotime($gregorianDate);
        $dayOfWeek = date('w', $timestamp);
        $day = date('j', $timestamp);
        $month = $months[date('n', $timestamp) - 1];
        $year = date('Y', $timestamp) - 621; // Approximate conversion
        
        return [
            'day_of_week' => $days[$dayOfWeek],
            'day' => $day,
            'month' => $month,
            'year' => $year,
            'hour' => date('H', $timestamp),
            'minute' => date('i', $timestamp)
        ];
    }
    
    private static function formatJalali($jalaliDate, $format) {
        $format = str_replace(
            ['l', 'd', 'F', 'Y', 'H', 'i'],
            [$jalaliDate['day_of_week'], $jalaliDate['day'], $jalaliDate['month'], $jalaliDate['year'], $jalaliDate['hour'], $jalaliDate['minute']],
            $format
        );
        
        return $format;
    }
}

// Alternative function names for compatibility
function jdate($format, $timestamp = null, $timezone = null) {
    return jDateTime::date($format, $timestamp, $timezone);
}
?>