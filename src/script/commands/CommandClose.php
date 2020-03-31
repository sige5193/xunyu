<?php
namespace app\script\commands;
class CommandClose extends BaseCommand {
    /**
     * @var string
     */
    private $operatorName = null;
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::setCmdArgs()
     */
    public function setCmdArgs($args) {
        $this->operatorName = isset($args[0]) ? $args[0] : null;
    }

    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::run()
     */
    protected function run() {
        $runtime = $this->getRuntime();
        if ( null === $this->operatorName ) {
            $this->operatorName = $runtime->getActiveOperatorName();
        }
        $operator = $runtime->getOperator($this->operatorName);
        
        $operator->stop();
        $operator->destory();
        $runtime->unloadOperator($this->operatorName);
    }
}