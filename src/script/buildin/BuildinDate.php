<?php
namespace app\script\buildin;
class BuildinDate {
    /**
     * @return string
     */
    public static function getYyyymmdd() {
        return date('Ymd');
    }
    
    /**
     * @return number
     */
    public static function getTimestamp() {
        return time();
    }
}