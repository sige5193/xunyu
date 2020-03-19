<?php 
use app\script\Parser;
use app\script\Runtime;

class Application {
    /**
     * @var self
     */
    private static $app = null;
    
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
     * @return void
     */
    public function start() {
        global $argv;
        array_shift($argv);
        if ( empty($argv) ) {
            return;
        }
        
        $parser = new Parser();
        $runtime = new Runtime();
        
        $file = $argv[0];
        $commands = file($file);
        
        foreach ( $commands as $commandText ) {
            $commandText = trim($commandText);
            echo "> {$commandText}\n";
            $command = $parser->parse($commandText);
            $runtime->execCommand($command);
        }
    }
}