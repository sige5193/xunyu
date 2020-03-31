<?php
namespace app\script\commands;
/**
 * @author sige
 */
class CommandAction extends BaseCommand {
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
     * @throws \Exception
     */
    protected function run() {
        /** @var IOperator $curOperator */
        $operator = $this->getRuntime()->getActiveOperator();
        
        $action = explode('-', $this->name);
        $action = array_map('ucfirst', $action);
        $action = 'cmd'.implode('', $action);
        if ( !is_callable([$operator, $action]) ) {
            throw new \Exception("invalid operator action : '{$this->name}'");
        }
        
        call_user_func_array([$operator, $action], $this->params);
    }
}