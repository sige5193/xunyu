<?php 
use app\script\Parser;
use app\script\Runtime;
use Commando\Command;
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
     * @var unknown
     */
    private $parser = null;
    
    /**
     * @var unknown
     */
    private $docroot = null;
    
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
    }
    
    /**
     * @param unknown $path
     */
    public function getPath( $path ) {
        return __DIR__.'/'.$path;
    }
    
    /**
     * @param unknown $path
     * @return string
     */
    public function getDocPath( $path ) {
        return $this->docroot.DIRECTORY_SEPARATOR.$path;
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
        if ( null !== $this->runtime ) {
            $this->runtime->shutdown();
        }
    }
    
    /**
     * @return void
     */
    public function start() {
        $this->runtime = $runtime = new Runtime();
        $this->parser = $parser = new Parser($runtime);
        
        $params = $this->cliParseParams();
        
        # set up test case
        $file = $params['path'];
        $this->docroot = dirname(realpath($file));
        
        # set up env vars
        $envpath = $this->getDocPath($params['env']);
        if ( file_exists($envpath) ) {
            $env = parse_ini_file($envpath, true);
            $runtime->variableSet('env', $env);
        }
        
        
        $commands = file($file);
        foreach ( $commands as $commandText ) {
            $commandText = trim($commandText);
            if ( empty($commandText) ) {
                continue;
            }
            try {
                $command = $parser->parse($commandText);
                $runtime->execCommand($command);
            } catch ( Exception $e ) {
                echo "\n\nERROR : {$e->getMessage()}\n";
                exit();
            }
        }
    }
    
    /**
     * @return \Commando\Command
     */
    private function cliParseParams() {
        $cmd = new Command();
        $cmd->option()->require()->describedAs('path to test case(s)');
        $cmd->option('e')->aka('env')->default('env.ini')->describedAs('path or name of env file, default to env.ini');
        
        $params = array();
        $params['path'] = $cmd[0];
        $params['env'] = $cmd['env'];
        return $params;
    }
}