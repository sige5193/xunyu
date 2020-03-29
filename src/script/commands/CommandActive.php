<?php
namespace app\script\commands;
use app\script\Runtime;

class CommandActive extends BaseCommand {
    /**
     * @var string
     */
    private $operatorName = null;
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::setCmdArgs()
     */
    public function setCmdArgs($args) {
        if ( !isset($args[0]) ) {
            throw new \Exception('active command requires an operator name');
        }
        $this->operatorName = $args[0];
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::exec()
     */
    public function exec(Runtime $runtime) {
        $runtime->activeOperator($this->operatorName);
    }
}