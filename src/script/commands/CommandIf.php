<?php
namespace app\script\commands;
use app\script\ConditionCalculator;
use app\script\ArgumentParser;

class CommandIf extends BaseCommand {
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
     * @var array
     */
    private $branches = array();
    
    /**
     * @var array
     */
    private $blockChain = [];
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::pushCommand()
     */
    public function pushCommand(ICommand $command) {
        if ( $command === $this ) { 
            # 初始化
            $this->blockChain[] = $command;
            $this->branches[] = ['condition' => $command, 'commands' => []];
        } else if ( $command->isBlockStart() ) {
            # 出现子块
            $this->blockChain[] = $command;
            $this->branches[count($this->branches)-1]['commands'][] = $command;
        } else if ( 1 < count($this->blockChain)
        && $command->isBlockEnd($this->blockChain[count($this->blockChain)-1]) ) {
            # 出现块结束符
            array_pop($this->blockChain);
            $this->branches[count($this->branches)-1]['commands'][] = $command;
        } else if ( 1==count($this->branches) 
        && $command instanceof CommandElseif || $command instanceof CommandElse ) {
            # 出现分支
            $this->branches[] = ['condition' => $command, 'commands' => []];
        } else if ( 1==count($this->blockChain) 
        && $command instanceof CommandEndif ) {
            # 当前块结束
            $this->getRuntime()->blockFinished();
            $this->exec();
        } else {
            # 普通命令
            $this->branches[count($this->branches)-1]['commands'][] = $command;
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::run()
     */
    protected function run() {
        do {
            $branch = array_shift($this->branches);
            if ( null === $branch ) {
                break;
            }
            if ( true !== $branch['condition']->calculateCondition() ) {
                continue;
            }
            
            $file = $this->getDefination('file');
            $this->getTestCase()->executeCommands($branch['commands'], $file);
            break;
        } while ( true );
    }

    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::isBlockStart()
     */
    public function isBlockStart() {
        return true;
    }
    
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