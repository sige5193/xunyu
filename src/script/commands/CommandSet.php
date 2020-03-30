<?php
namespace app\script\commands;
use app\script\Runtime;
use app\script\ArgumentParser;

/**
 *
 */
class CommandSet extends BaseCommand {
    /**
     * @var unknown
     */
    protected $name = null;
    
    /**
     * @var unknown
     */
    protected $value = null;
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::getArgsParser()
     */
    protected function getArgsParser() {
        return ArgumentParser::setup()
        ->addArgument('name')->setIsRequired('name', true)
        ->addArgument('value')->setIsRequired('value', true);
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::exec()
     */
    public function exec(Runtime $runtime) {
        $runtime->variableSet($this->name, $this->value);
    }
}