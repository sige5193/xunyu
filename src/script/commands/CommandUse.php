<?php
namespace app\script\commands;
use app\operators\IOperator;
/**
 * @author sige
 */
class CommandUse extends BaseCommand {
    /**
     * name of operator
     * @var string
     */
    private $operatorName = null;
    
    /**
     * params to operator
     * @var string[]
     */
    private $params = [];
    
    /**
     * name of operator
     * @var string
     */
    private $alias = null;
    
    /**
     * @var array
     */
    private static $operatorCounter = array();
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::setCmdArgs()
     */
    public function setCmdArgs($args) {
        $this->operatorName = array_shift($args);
        $this->params = $args;
        
        # setup alias for operator
        if ( 2 <= count($args) && 'as' === strtolower($args[count($args)-2]) ) {
            $this->alias = trim($args[count($args)-1]);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::run()
     */
    protected function run() {
        $operatorName = ucfirst($this->operatorName);
        $operatorClass = "\\app\\operators\\Operator{$operatorName}";
        if ( !class_exists($operatorClass) ) {
            throw new \Exception("unknown operator : {$this->operatorName}");
        }
        
        /** @var IOperator $operator */
        $operator = new $operatorClass();
        $operator->setCmdArgs($this->params);
        $operator->init();
        $operator->start();
        
        if ( !isset(self::$operatorCounter[$this->operatorName]) ) {
            self::$operatorCounter[$this->operatorName] = 0;
        }
        self::$operatorCounter[$this->operatorName] ++;
        
        if ( null === $this->alias ) {
            $counter = self::$operatorCounter[$this->operatorName];
            $this->alias = "{$this->operatorName}:{$counter}";
        }
        
        $this->getRuntime()->loadOperator($this->alias, $operator);
    }
}