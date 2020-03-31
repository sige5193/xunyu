<?php
namespace app\script\commands;
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
     * @see \app\script\commands\BaseCommand::run()
     */
    protected function run() {
        $this->getRuntime()->variableSet($this->name, $this->value);
    }
}