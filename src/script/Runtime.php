<?php
namespace app\script;
use app\script\commands\ICommand;
class Runtime {
    /**
     * place where command to store runtime data
     * @var array
     */
    private $data = array();
    
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
}