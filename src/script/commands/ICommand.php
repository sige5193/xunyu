<?php
namespace app\script\commands;
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
    function exec( );
    
    /**
     * @param string $rawCommand
     */
    function setRawCommand( $rawCommand );
    
    /**
     * @return string
     */
    function getRawCommand();
    
    /**
     * 
     */
    function isBlockStart();
    
    /**
     * 
     */
    function isBlockEnd(ICommand $command);
    
    /**
     * @param ICommand $command
     */
    function pushCommand(ICommand $command);
}

