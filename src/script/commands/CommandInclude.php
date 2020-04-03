<?php
namespace app\script\commands;
use app\script\Runtime;

class CommandInclude extends BaseCommand {
    /**
     * @var unknown
     */
    protected $file = null;
    
    /**
     * @var unknown
     */
    protected $params = null;
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::setCmdArgs()
     */
    public function setCmdArgs($args) {
        $this->file = $args[0];
        $this->params = (isset($args[1])) ? $args[1] : null;
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::run()
     */
    protected function run() {
        $runtime = \Application::app()->getRuntime();
        $parser = \Application::app()->getParser();
        
        $this->exportParams($runtime);
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
    
    /**
     * @param Runtime $runtime
     */
    private function exportParams( Runtime $runtime ) {
        $params = explode(';', $this->params);
        $params = array_filter($params);
        
        $argv = [];
        foreach ( $params as $item ) {
            list($name, $value) = explode('=', $item);
            $argv[$name] = $value;
        }
        $runtime->variableSet('argv', $argv);
    }
}