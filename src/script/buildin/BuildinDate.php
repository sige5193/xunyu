<?php
namespace app\script\buildin;
use app\script\Runtime;
class BuildinDate {
    /**
     * @param Runtime $runtime
     * @return string
     */
    public static function getYyyymmdd( ) {
        return date('Ymd');
    }
}