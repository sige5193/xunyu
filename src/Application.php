<?php 
use app\script\Parser;
use app\script\Runtime;

class Application {
    /**
     * @var self
     */
    private static $app = null;
    
    /**
     * @var Runtime
     */
    private $runtime = null;
    
    /**
     * @return self
     */
    public static function app() {
        if ( null === self::$app ) {
            self::$app = new self();
        }
        return self::$app;
    }
    
    /**
     * init
     * @return void
     */
    private function __construct() {
        spl_autoload_register([$this, '_autoloader']);
        register_shutdown_function([$this, '_shutdown']);
        pcntl_signal();
    }
    
    /**
     * @param unknown $path
     */
    public function getPath( $path ) {
        return __DIR__.'/'.$path;
    }
    
    /**
     * @return string
     */
    public function getPlatformName() {
        return 'windows';
    }
    
    /**
     * @return void
     */
    public function _autoloader( $class ) {
        if ( 'app\\' !== substr($class, 0, 4) ) {
            return;
        }
        
        $classpath = __DIR__.'/'.substr(str_replace('\\', '/', $class), 4).'.php';
        if ( file_exists($classpath) ) {
            require $classpath;
        }
    }
    
    /**
     * 
     */
    public function _shutdown() {
        $this->runtime->shutdown();
    }
    
    /**
     * @return void
     */
    public function start() {
        global $argv;
        array_shift($argv);
        if ( empty($argv) ) {
            return;
        }
        
        $parser = new Parser();
        $this->runtime = $runtime = new Runtime();
        
        $file = $argv[0];
        $commands = file($file);
        
        foreach ( $commands as $commandText ) {
            $commandText = trim($commandText);
            if ( empty($commandText) ) {
                continue;
            }
            echo "> {$commandText}\n";
            try {
                $command = $parser->parse($commandText);
                $runtime->execCommand($command);
            } catch ( Exception $e ) {
                echo "\n\nERROR : {$e->getMessage()}\n";
                exit();
            }
        }
    }
}