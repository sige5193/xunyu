<?php
namespace app\script\buildin;
use app\script\Runtime;

class BuildinString {
    /**
     * @param unknown $varName
     * @param unknown $content
     */
    public static function handleAppend( $varName, $content ) {
        $runtime = \Application::app()->getTaseCase()->getRuntime();
        $string = $runtime->variableGet($varName);
        $string = $string.$content;
        $runtime->variableSet($varName, $string);
    }
    
    /**
     * @param unknown $varName
     * @param unknown $path
     */
    public static function handleReadFile( $varName, $path ) {
        $path = \Application::app()->getDocPath($path);
        $content = file_get_contents($path);
        
        $runtime = \Application::app()->getTaseCase()->getRuntime();
        $runtime->variableSet($varName, $content);
    }
    
    /**
     * @param unknown $varName
     * @param unknown $content
     */
    public static function handleMd5( $varName ) {
        $runtime = \Application::app()->getTaseCase()->getRuntime();
        
        $value = $runtime->variableGet($varName);
        $value = md5($value);
        
        $runtime->variableSet($varName, $value);
    }
    
    /**
     * @param unknown $varName
     * @param unknown $content
     */
    public static function handleUppercase( $varName ) {
        $runtime = \Application::app()->getTaseCase()->getRuntime();
        
        $value = $runtime->variableGet($varName);
        $value = strtoupper($value);
        
        $runtime->variableSet($varName, $value);
    }
    
    /**
     * @return string
     */
    public static function getUuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}