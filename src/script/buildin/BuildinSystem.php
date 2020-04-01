<?php
namespace app\script\buildin;
class BuildinSystem {
    /**
     * @param unknown $varName
     * @param unknown $content
     */
    public static function handleSleep( $seconds ) {
        sleep($seconds);
    }
}