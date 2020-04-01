<?php
namespace app\script\commands;
class CommandCall extends BaseCommand {
    /**
     * @var unknown
     */
    private $name = null;
    
    /**
     * @var unknown
     */
    private $params = null;
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::setCmdArgs()
     */
    public function setCmdArgs($args) {
        $this->name = array_shift($args);
        $this->params = $args;
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::run()
     */
    protected function run() {
        $func = $this->getRuntime()->funcGet($this->name);
        $func->exec($this->params);
    }
}