<?php
namespace app\script\commands;
/**
 * @author sige
 */
class CommandAction implements ICommand {
    /**
     * name of operators
     * @var string
     */
    private $name = null;
    
    /**
     * params to operators
     * @var array
     */
    private $params = null;
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::setCmdArgs()
     */
    public function setCmdArgs($args) {
        $this->name = array_shift($args);
        $this->params = $args;
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::exec()
     */
    public function exec(\app\script\Runtime $runtime) {
        /** @var IOperator $curOperator */
        $curOperator = $runtime->getData('ActiveOperator');
        if ( null === $curOperator ) {
            throw new \Exception('no active operator');
        }
        
        $action = explode('-', $this->name);
        $action = array_map('ucfirst', $action);
        $action = 'cmd'.implode('', $action);
        if ( !is_callable([$curOperator, $action]) ) {
            throw new \Exception("invalid operator action : '{$this->name}'");
        }
        
        call_user_func_array([$curOperator, $action], $this->params);
    }
}