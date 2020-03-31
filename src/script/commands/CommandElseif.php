<?php
namespace app\script\commands;
use app\script\ConditionCalculator;
use app\script\ArgumentParser;

class CommandElseif extends BaseCommand {
    /**
     * @var unknown
     */
    protected $leftValue = null;
    
    /**
     * @var unknown
     */
    protected $rightValue = null;
    
    /**
     * @var unknown
     */
    protected $operator = null;
    
    /**
     * @return boolean
     */
    public function calculateCondition() {
        $cal = new ConditionCalculator();
        $cal->leftValue = $this->leftValue;
        $cal->rightValue = $this->rightValue;
        $cal->operator = $this->operator;
        return $cal->calculate();
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::getArgsParser()
     */
    protected function getArgsParser() {
        return ArgumentParser::setup()
        ->addArgument('leftValue')->setIsRequired('leftValue', true)
        ->addArgument('operator')->setIsRequired('leftValue', true)
        ->addArgument('rightValue')->setIsRequired('rightValue', true);
    }
}