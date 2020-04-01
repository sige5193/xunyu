<?php
namespace app\script;
class UserFunction {
    /**
     * @var unknown
     */
    public $name = null;
    
    /**
     * @var array
     */
    public $paramNames = [];
    
    /**
     * @var array
     */
    public $commands = [];
    
    /**
     * @param unknown $params
     */
    public function exec( $params ) {
        $runtime = \Application::app()->getRuntime();
        $parser = \Application::app()->getParser();
        
        $runtime->variableScopeEnterNew();
        foreach ( $this->paramNames as $paramName ) {
            $runtime->variableSet($paramName, array_shift($params));
        }
        
        foreach ( $this->commands as $command ) {
            $rawCommand = $command->getRawCommand();
            $command = $parser->parse($rawCommand);
            $command->setRawCommand($rawCommand);
            $runtime->execCommand($command);
        }
        
        $returnVal = $runtime->variableGet('return-val');
        $runtime->variableScopeLeave();
        $runtime->variableSet('return-val', $returnVal);
    }
}