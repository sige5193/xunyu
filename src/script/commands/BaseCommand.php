<?php
namespace app\script\commands;
use app\script\TestCase;
/**
 * 
 */
abstract class BaseCommand implements ICommand {
    /**
     * @var string
     */
    private $rawCommand = null;
    
    /**
     * @var array
     */
    private $args = [];
    
    /**
     * @var array
     */
    private $defination = array(
        'file' => null,
        'line' => null,
    );
    
    /**
     * @var TestCase
     */
    private $testcase = null;
    
    /**
     * @return \app\script\TestCase
     */
    public function getTestCase() {
        return $this->testcase;
    }
    
    /**
     * @param unknown $testcase
     */
    public function setTestCase( TestCase $testcase ) {
        $this->testcase = $testcase;
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::setDefination()
     */
    public function setDefination($name, $value) {
        $this->defination[$name] = $value;
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::getDefination()
     */
    public function getDefination($name) {
        return $this->defination[$name];
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::setRawCommand()
     */
    public function setRawCommand( $rawCommand ) {
        $this->rawCommand = $rawCommand;
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::getRawCommand()
     */
    public function getRawCommand() {
        return $this->rawCommand;
    }
    
    /**
     * {@inheritDoc}
     * @see \app\operators\IOperator::setCmdArgs()
     */
    public function setCmdArgs($args) {
        $argParser = $this->getArgsParser();
        if ( null === $argParser ) {
            return null;
        }
        
        $argParser->parse($args);
        if ( $argParser->hasError() ) {
            throw new \Exception("Operator param error : {$argParser->getErrorSummary()}");
        }
        
        $argNames = $argParser->getArgNames();
        foreach ( $argNames as $argName ) {
            if ( property_exists($this, $argName) ) {
                $this->$argName = $argParser->get($argName);
            }
        }
    }
    
    /**
     * @return NULL|ArgumentParser
     */
    protected function getArgsParser() {
        return null;
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::isBlockStart()
     */
    public function isBlockStart() {
        return false;
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::isBlockEnd()
     */
    public function isBlockEnd(\app\script\commands\ICommand $command) {
        return false;
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::pushCommand()
     */
    public function pushCommand(\app\script\commands\ICommand $command) {
        return false;
    }
    
    /**
     * get current runtime
     * @return \app\script\Runtime
     */
    protected function getRuntime() {
        return \Application::app()->getTaseCase()->getRuntime();
    }
    
    /**
     *
     */
    public function exec() {
        try {
            $this->run();
            $this->testcase->tick($this);
            \Application::app()->log("> {$this->getRawCommand()}");
        } catch ( \Exception $e ) {
            $this->testcase->failed($this, $e);
        }
    }
    
    /**
     * 
     */
    protected function run() {
        
    }
}