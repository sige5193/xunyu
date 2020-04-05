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
        $this->getTestCase()->executeFile($this->file);
    }
    
    /**
     * 
     */
    private function exportParams( ) {
        $runtime = $this->getRuntime();
        
        $params = explode(';', $this->params);
        $params = array_filter($params);
        
        $argv = $runtime->variableGet('argv');
        if ( empty($argv) ) {
            $argv = [];
        }
        
        foreach ( $params as $item ) {
            list($name, $value) = explode('=', $item);
            $argv[$name] = $value;
        }
        $runtime->variableSet('argv', $argv);
    }
}