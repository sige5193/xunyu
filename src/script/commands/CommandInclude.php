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
        $this->exportParams();
        \Application::app()->runCommandsByFile($this->file);
    }
    
    /**
     * 
     */
    private function exportParams( ) {
        $runtime = \Application::app()->getRuntime();
        
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