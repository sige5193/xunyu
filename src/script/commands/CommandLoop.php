<?php
namespace app\script\commands;
use app\script\ArgumentParser;

class CommandLoop extends BaseCommand {
    /**
     * @var unknown
     */
    protected $count = null;
    
    /**
     * @var unknown
     */
    protected $index = null;
    
    /**
     * @var array
     */
    private $commands = array();
    
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
            $this->commands = [];
        } else if ( $command->isBlockStart() ) {
            # 出现子块
            $this->blockChain[] = $command;
            $this->commands[] = $command;
        } else if ( 1 < count($this->blockChain)
        && $command->isBlockEnd($this->blockChain[count($this->blockChain)-1]) ) {
            # 出现块结束符
            array_pop($this->blockChain);
            $this->commands[] = $command;
        } else if ( 1==count($this->blockChain)
        && $command instanceof CommandEndloop ) {
            # 当前块结束
            $this->getRuntime()->blockFinished();
            $this->exec();
        } else {
            # 普通命令
            $this->commands[] = $command;
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::getArgsParser()
     */
    protected function getArgsParser() {
        return ArgumentParser::setup()
        ->addArgument('count')->setIsRequired('count', true)
        ->addArgument('=>')->setIsRequired('=>', false)
        ->addArgument('index')->setIsRequired('index', true);
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::isBlockStart()
     */
    public function isBlockStart() {
        return true;
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::run()
     */
    protected function run() {
        $runtime = $this->getRuntime();
        for ( $i=0; $i<$this->count; $i++ ) {
            if ( null !== $this->index ) {
                $runtime->variableSet($this->index, $i);
            }
            
            $file = $this->getDefination('file');
            $this->getTestCase()->executeCommands($this->commands, $file);
        }
    }
}