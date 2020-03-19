<?php
namespace app\script\commands;
use app\script\Runtime;
interface ICommand {
    /**
     * set command args to command
     * @param array $args
     * @return void
     */
    function setCmdArgs( $args );
    
    /**
     * execute command with runtime
     * @return void
     */
    function exec( Runtime $runtime );
}

