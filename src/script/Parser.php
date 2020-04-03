<?php
namespace app\script;
use app\script\commands\CommandAction;
use app\script\commands\ICommand;
use app\script\commands\CommandBuildinHandler;
class Parser {
    /**
     * @var Runtime
     */
    private $runtime = null;
    
    /**
     * @param Runtime $runtime
     */
    public function __construct( Runtime $runtime ) {
        $this->runtime = $runtime;
    }
    
    /**
     * @param string $commandText
     * @return ICommand
     */
    public function parse( $commandText ) {
        $commandParts = $this->split($commandText);
        
        $command = $this->tryGetBuildinCommand($commandParts);
        $command = (null===$command) ? $this->tryGetBuildinHandlerCommand($commandParts) : $command;
        $command = (null===$command) ? $this->tryGetOperatorCommand($commandParts) : $command;
        $command->setRawCommand($commandText);
        return $command;
    }
    
    /**
     * @param string[] $args
     * @return string[]
     */
    private function parseArgsToVariable( $args ) {
        $list = array();
        foreach ( $args as $arg ) {
            if ( '$' === $arg[0] ) {
                $arg = $this->getVariableValue(substr($arg, 1));
            } else {
                $arg = $this->replaceVaribleInsideTheString($arg);
            }
            $list[] = $arg;
        }
        return $list;
    }
    
    /**
     * @link https://github.com/sige-chen/xunyu/issues/1
     * @param string $arg
     * @return string
     */
    private function replaceVaribleInsideTheString( $arg ) {
        preg_match_all('#(?P<placeholder>\{\$(?P<name>[a-zA-Z0-9\-\.]+)\})#is', $arg, $matches);
        foreach ( $matches['name'] as $index => $varname ) {
            $value = $this->getVariableValue($varname);
            $arg = str_replace($matches['placeholder'][$index], $value, $arg);
        }
        return $arg;
    }
    
    /**
     * 获取变量值
     * @param unknown $name
     */
    private function getVariableValue( $name ) {
        $variableParts = explode('.', $name);
        if ( 2 !== count($variableParts) ) {
            return $this->runtime->variableGet($name);
        }
        
        $handler = [
            '\\app\\script\\buildin\\Buildin'.ucfirst($variableParts[0]),
            'get'.ucfirst($variableParts[1])
        ];
        if ( !is_callable($handler) ) {
            return $this->runtime->variableGet($name);
        }
        
        return call_user_func_array($handler, [$this->runtime]);
    }
    
    /**
     * split command to parts
     * @param unknown $commandText
     * @return string[]
     */
    private function split( $commandText ) {
        if ( empty($commandText) ) {
            throw new \Exception("unable to split command text : {$commandText}");
        }
        
        $parts = [];
        
        $part = [];
        $commandChars = str_split($commandText);
        for ($i=0; $i<count($commandChars); $i++) {
            $char = $commandChars[$i];
            if ( ' ' === $char && empty($part) ) {
                # ignore the spaces before the part starts
                continue;
            } else if ( ' ' === $char && !empty($part) ) {
                $part = implode('', $part);
                $parts[] = $part;
                $part = [];
            } else {
                $part[] = $char;
            }
        }
        if ( !empty($part) ) {
            $parts[] = implode('', $part);
        }
        
        if ( empty($parts) ) {
            throw new \Exception("unable to split command text to parts : {$commandText}");
        }
        return $parts;
    }
    
    /**
     * 解析为内部命令
     * @param array $commandParts
     * @return null|ICommand
     */
    private function tryGetBuildinCommand( $commandParts ) {
        $charCommandMap = ['#'=>'Comment', '%'=>'Document'];
        $firstArg = $commandName = array_shift($commandParts);
        if ( array_key_exists($commandName, $charCommandMap) ) {
            $commandName = $charCommandMap[$commandName];
        }
        $commandName = ucfirst($commandName);
        
        $commandNamespace = '\\app\\script\\commands';
        $commandClass = "{$commandNamespace}\\Command{$commandName}";
        if ( !class_exists($commandClass) ) {
            return null;
        }
        
        $args = $this->parseArgsToVariable($commandParts);
        
        /** @var $command ICommand */
        $command = new $commandClass();
        $command->setCmdArgs($args);
        return $command;
    }
    
    /**
     * 解析为内置函数命令
     * @param array $commandParts
     * @return null|ICommand
     */
    private function tryGetBuildinHandlerCommand( $commandParts ) {
        $cmd = $commandParts[0];
        if ( false === strpos($cmd, '.') ) {
            return null;
        }
        
        $cmdParts = explode('.', $cmd);
        if ( 2 != count($cmdParts) ) {
            return null;
        }
        
        $handleClass = '\\app\\script\\buildin\\Buildin'.ucfirst($cmdParts[0]);
        if ( !class_exists($handleClass) ) {
            return null;
        }
        
        $handlerAction = implode('', array_map('ucfirst', explode('-', $cmdParts[1])));
        $handlerAction = 'handle'.$handlerAction;
        if ( !is_callable([$handleClass, $handlerAction]) ) {
            return null;
        }
        
        $command = new CommandBuildinHandler();
        $command->setCmdArgs($this->parseArgsToVariable($commandParts));
        return $command;
    }
    
    /**
     * 解析为内部命令
     * @param array $commandParts
     * @return null|ICommand
     */
    private function tryGetOperatorCommand( $commandParts ) {
        /** @var $command ICommand */
        $command = new CommandAction();
        $command->setCmdArgs($this->parseArgsToVariable($commandParts));
        return $command;
    }
}