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
     * @var unknown
     */
    public $file = null;
    
    /**
     * @var unknown
     */
    public $line = null;
    
    /**
     * @param unknown $params
     */
    public function exec( $params ) {
        $testcase = \Application::app()->getTaseCase();
        $runtime = $testcase->getRuntime();
        $parser = $testcase->getParser();
        
        $runtime->variableScopeEnterNew();
        foreach ( $this->paramNames as $paramName ) {
            $runtime->variableSet($paramName, array_shift($params));
        }
        
        $testcase->executeCommands($this->commands, $this->file, $this->line);
        
        $returnVal = $runtime->variableGet('return-val');
        $runtime->variableScopeLeave();
        $runtime->variableSet('return-val', $returnVal);
    }
}