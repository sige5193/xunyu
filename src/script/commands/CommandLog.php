<?php
namespace app\script\commands;
class CommandLog extends BaseCommand {
    /**
     * @var string
     */
    private $params = [];
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::setCmdArgs()
     */
    public function setCmdArgs($args) {
        $this->params = $args;
    }

    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::run()
     */
    protected function run() {
        echo implode(' ', $this->params), "\n";
    }
}