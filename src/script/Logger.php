<?php
namespace app\script;
class Logger {
    /**
     * append message to logger
     * @param string $message
     * @return void
     */
    public function log( $message ) {
        $filePath = \Application::app()->getDocPath('run.log');
        
        $message = sprintf("%s\n", $message);
        file_put_contents($filePath, $message, FILE_APPEND);
    }
}