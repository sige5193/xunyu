<?php
namespace app\script\commands;
use app\script\Runtime;
use app\operators\IOperator;

/**
 * @example use browser chrome
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
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::setCmdArgs()
     */
    public function setCmdArgs($args) {
        $this->operatorName = array_shift($args);
        $this->params = $args;
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::exec()
     */
    public function exec(Runtime $runtime) {
        $operatorName = ucfirst($this->operatorName);
        $operatorClass = "\\app\\operators\\Operator{$operatorName}";
        if ( !class_exists($operatorClass) ) {
            throw new \Exception("unknown operator : {$this->operatorName}");
        }
        
        /** @var IOperator $curOperator */
        $curOperator = $runtime->getData('ActiveOperator');
        if ( null !== $curOperator ) {
            $curOperator->stop();
            $curOperator->destory();
        }
        
        /** @var IOperator $operator */
        $operator = new $operatorClass();
        $operator->setCmdArgs($this->params);
        $operator->init();
        $operator->start();
        
        $runtime->setData('ActiveOperator', $operator);
    }
}