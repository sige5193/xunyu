<?php
namespace app\script;
use app\script\commands\ICommand;
use app\operators\IOperator;
class Runtime {
    /**
     * @var IOperator[]
     */
    private $operators = array();
    
    /**
     * @var string
     */
    private $activatedOperatorName = null;
    
    /**
     * place where command to store runtime data
     * @var array
     */
    private $data = array();
    
    /**
     * @param string $name
     * @param IOperator $operator
     */
    public function loadOperator( $name, IOperator $operator ) {
        if ( array_key_exists($name, $this->operators) ) {
            throw new \Exception("Operator `{$name}` already exists");
        }
        $this->operators[$name] = $operator;
        $this->activatedOperatorName = $name;
    }
    
    /**
     * @param string $name
     */
    public function unloadOperator( $name ) {
        if ( !array_key_exists($name, $this->operators) ) {
            throw new \Exception("Operator `{$name}` does not exists");
        }
        unset($this->operators[$name]);
        if ( $this->activatedOperatorName == $name ) {
            $this->activatedOperatorName = null;
        }
    }
    
    /**
     * @return string
     */
    public function getActiveOperatorName( ) {
        return $this->activatedOperatorName;
    }
    
    /**
     * @return \app\operators\IOperator
     */
    public function getActiveOperator() {
        if ( null === $this->activatedOperatorName ) {
            throw new \Exception('no active operator');
        }
        return $this->operators[$this->activatedOperatorName];
    }
    
    /**
     * @param unknown $name
     */
    public function activeOperator( $name ) {
        if ( !array_key_exists($name, $this->operators) ) {
            throw new \Exception("Operator `{$name}` does not exists");
        }
        $this->activatedOperatorName = $name;
    }
    
    /**
     * @param unknown $name
     * @return IOperator
     */
    public function getOperator( $name ) {
        if ( !array_key_exists($name, $this->operators) ) {
            throw new \Exception("Operator `{$name}` does not exists");
        }
        return $this->operators[$name];
    }
    
    /***
     * set runtime data
     * @param string $name
     * @param mixed $value
     */
    public function setData ( $name, $value ) {
        $this->data[$name] = $value;
    }
    
    /**
     * get data from runtime data
     * @param unknown $name
     * @param unknown $default
     * @return mixed
     */
    public function getData ( $name, $default=null ) {
        return array_key_exists($name, $this->data) ? $this->data[$name] : $default;
    }
    
    /**
     * @param ICommand $command
     * @return void
     */
    public function execCommand( ICommand $command ) {
        $command->exec($this);
    }
    
    /**
     * 
     */
    public function shutdown() {
        foreach ( $this->operators as $operator ) {
            $operator->stop();
            $operator->destory();
        }
    }
}