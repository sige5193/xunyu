<?php
namespace app\script\commands;
class CommandInclude extends BaseCommand {
    /**
     * @var unknown
     */
    protected $file = null;
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::setCmdArgs()
     */
    public function setCmdArgs($args) {
        $this->file = $args[0];
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::run()
     */
    protected function run() {
        $runtime = \Application::app()->getRuntime();
        $parser = \Application::app()->getParser();
        
        $file = \Application::app()->getDocPath($this->file);
        $commands = file($file);
        
        foreach ( $commands as $commandTextRaw ) {
            $commandText = trim($commandTextRaw);
            if ( empty($commandText) ) {
                continue;
            }
            $command = $parser->parse($commandText);
            $runtime->execCommand($command);
        }
    }
}