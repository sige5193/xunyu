<?php
namespace app\script;
use app\script\commands\ICommand;
use Commando\Command;

class TestCase {
    /**
     * @var string
     */
    private $path = null;

    /**
     * @var unknown
     */
    private $runtime = null;
    
    /**
     * @var unknown
     */
    private $parser = null;
    
    /**
     * @param string $path
     */
    public function __construct( $path, $options=array() ) {
        $this->path = $path;
        $this->runtime = new Runtime();
        $this->parser = new Parser($this->runtime);
        
        # set up env vars
        $envpath = \Application::app()->getDocPath($options['env']);
        if ( file_exists($envpath) ) {
            $env = parse_ini_file($envpath, true);
            $this->runtime->variableSet('env', $env);
        }
    }
    
    /**
     * @return void
     */
    public function tick( ICommand $command ) {
        echo "  > {$command->getRawCommand()}\n";
    }
    
    /**
     * @param \Exception $e
     */
    public function failed( ICommand $command, \Exception $e ) {
        echo "[ failed ]                                       \n";
        echo "Command Error : \n";
        echo "{$e->getMessage()} \n";
        echo "File : {$command->getDefination('file')}\n";
        echo "Line : #{$command->getDefination('line')}\n";
        echo "\n";
        exit();
    }
    
    /**
     * 
     */
    public function quit() {
        echo "<<<< test case exited.";
        exit();
    }
    
    /**
     * 
     */
    public function success() {
    }
    
    /**
     * @return void
     */
    public function execute() {
        echo "{$this->getTitle()}\n";
        $this->executeFile($this->path);
        $this->success();
    }
    
    /**
     * @param array $commands
     */
    public function executeCommands( array $commands, $file, $line=0 ) {
        foreach ( $commands as $index => $command ) {
            try {
                if ( !($command instanceof ICommand) ) {
                    $command = trim($command);
                    if ( empty($command) ) {
                        continue;
                    }
                    $command = $this->parser->parse($command);
                    $command->setDefination('file', $file);
                    $command->setDefination('line', $line+$index+1);
                }
                $command->setTestCase($this);
                $this->runtime->execCommand($command);
            } catch ( \Exception $e ) {
                $this->failed($command, $e);
            }
        }
    }
    
    /**
     * @param unknown $path
     */
    public function executeFile( $path ) {
        $file = is_file($path) ? $path : \Application::app()->getDocPath($path);
        if ( !file_exists($file) ) {
            throw new RuntimeErrorException("unable to load script file : {$file}");
        }
        
        $commands = file($file);
        $this->executeCommands($commands, $file);
    }
    
    /**
     * 
     */
    private function getTitle() {
        $name = $this->path;
        
        $docpathLen = strlen(\Application::app()->getDocPath()) + 1;
        if ( strlen($name) > $docpathLen ) {
            $name = substr($name, $docpathLen);
        }
        return $name;
    }
    
    /**
     * @return \app\script\Runtime
     */
    public function getRuntime() {
        return $this->runtime;
    }
    
    /**
     * @return \app\script\Parser
     */
    public function getParser() {
        return $this->parser;
    }
}