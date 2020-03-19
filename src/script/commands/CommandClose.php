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
        $this->operatorName = $args[0];
    }

    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::exec()
     */
    public function exec(\app\script\Runtime $runtime) {
        /** @var IOperator $curOperator */
        $curOperator = $runtime->getData('ActiveOperator');
        if ( null === $curOperator ) {
            throw new \Exception("no actived opertor");
        }
        
        $curOperator->stop();
        $curOperator->destory();
        $runtime->setData('ActiveOperator', null);
    }
}