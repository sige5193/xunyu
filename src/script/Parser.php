<?php
namespace app\script;
use app\script\commands\CommandAction;
use app\script\commands\ICommand;
class Parser {
    /**
     * @param string $commandText
     * @return ICommand
     */
    public function parse( $commandText ) {
        $commandParts = $this->split($commandText);
        
        $charCommandMap = ['#'=>'Comment', '%'=>'Document'];
        $firstArg = $commandName = array_shift($commandParts);
        if ( array_key_exists($commandName, $charCommandMap) ) {
            $commandName = $charCommandMap[$commandName];
        }
        $commandName = ucfirst($commandName);
        
        $commandNamespace = '\\app\\script\\commands';
        $commandClass = "{$commandNamespace}\\Command{$commandName}";
        if ( !class_exists($commandClass) ) {
            array_unshift($commandParts, $firstArg);
            $commandClass = CommandAction::class;
        }
        
        /** @var $command ICommand */
        $command = new $commandClass();
        $command->setCmdArgs($commandParts);
        return $command;
    }
    
    /**
     * split command to parts
     * @param unknown $commandText
     * @return string[]
     */
    private function split( $commandText ) {
        if ( empty($commandText) ) {
            throw new \Exception("unable to split command text : {$commandText}");
        }
        
        $parts = [];
        
        $part = [];
        $commandChars = str_split($commandText);
        for ($i=0; $i<count($commandChars); $i++) {
            $char = $commandChars[$i];
            if ( ' ' === $char && empty($part) ) {
                # ignore the spaces before the part starts
                continue;
            } else if ( ' ' === $char && !empty($part) ) {
                $part = implode('', $part);
                $parts[] = $part;
                $part = [];
            } else {
                $part[] = $char;
            }
        }
        if ( !empty($part) ) {
            $parts[] = implode('', $part);
        }
        
        if ( empty($parts) ) {
            throw new \Exception("unable to split command text to parts : {$commandText}");
        }
        return $parts;
    }
}