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
     * @var array
     */
    private $variableStack = [];
    
    /**
     * @var ICommand|null
     */
    private $block = null;
    
    /**
     * @var array
     */
    private $funcs = array();
    
    /**
     * @param UserFunction $func
     */
    public function funcRegister( UserFunction $func ) {
        $this->funcs[$func->name] = $func;
    }
    
    /**
     * @param unknown $name
     * @return \app\script\UserFunction 
     */
    public function funcGet( $name ) {
        return $this->funcs[$name];
    }
    
    /**
     * 
     */
    public function __construct() {
        $this->variableScopeEnterNew();
    }
    
    /**
     * @return void
     */
    public function blockFinished() {
        $this->block = null;
    }
    
    /**
     * @param ICommand $command
     * @return void
     */
    public function execCommand( ICommand $command ) {
        if ( null !== $this->block ) {
            $this->block->pushCommand($command);
        } else if ( $command->isBlockStart() ) {
            $this->block = $command;
            $this->block->pushCommand($command);
        } else {
            $command->exec($this);
        }
    }
    
    /**
     * @param unknown $name
     * @param unknown $value
     */
    public function variableSet( $name, $value ) {
        $curScope = &$this->variableStack[count($this->variableStack)-1];
        $globalScope = &$this->variableStack[0];
        
        if ( !($value instanceof Variable ) ) {
            $value = new Variable($value);
        }
        
        if ( array_key_exists($name, $curScope) ) {
            $curScope[$name] = $value;
        } else if ( array_key_exists($name, $globalScope) ) {
            $globalScope[$name] = $value;
        } else {
            $curScope[$name] = $value;
        }
    }
    
    /**
     * @param unknown $name
     */
    public function variableGet( $name ) {
        $curScope = &$this->variableStack[count($this->variableStack)-1];
        $globalScope = &$this->variableStack[0];
        
        $keys = explode('.', $name);
        
        $key = array_shift($keys);
        if ( array_key_exists($key, $curScope) ) {
            $value = $curScope[$key];
        } else if ( array_key_exists($key, $globalScope) ) {
            $value = $globalScope[$key];
        } else {
            return '';
        }
        
        $value = $value->getValue();
        while ( !empty($keys) ) {
            $key = array_shift($keys);
            if ( array_key_exists($key, $value) ) {
                $value = $value[$key];
            } else {
                return '';
            }
        }
        
        return $value;
    }
    
    /**
     * 
     */
    public function variableScopeEnterNew() {
        $this->variableStack[] = array();
    }
    
    /**
     * 
     */
    public function variableScopeLeave() {
        array_pop($this->variableStack);
    }
    
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
     * 
     */
    public function shutdown() {
        foreach ( $this->operators as $operator ) {
            $operator->stop();
            $operator->destory();
        }
    }
}