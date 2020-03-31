<?php
namespace app\script\buildin;
use app\script\Runtime;

class BuildinString {
    /**
     * @param unknown $varName
     * @param unknown $content
     */
    public static function handleAppend( Runtime $runtime, $varName, $content ) {
        $string = $runtime->variableGet($varName);
        $string = $string.$content;
        $runtime->variableSet($varName, $string);
    }
}