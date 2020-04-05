<?php 
use Commando\Command;
use app\script\TestCase;
use app\script\Logger;
class Application {
    /**
     * @var self
     */
    private static $app = null;
    
    /**
     * @var string
     */
    private $docroot = null;
    
    /**
     * @var TestCase
     */
    private $testcase = null;
    
    /**
     * @var Logger
     */
    private $logger = null;
    
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
        $this->logger = new Logger();
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
    public function getDocPath( $path=null ) {
        return (null === $path) 
        ? $this->docroot 
        : $this->docroot.DIRECTORY_SEPARATOR.$path;
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
        if ( null !== $this->getTaseCase() ) {
            $this->getTaseCase()->getRuntime()->shutdown();
        }
    }
    
    /**
     * log runtime message
     * @param string $message
     */
    public function log( $message ) {
        $this->logger->log($message);
    }
    
    /**
     * @return \app\script\TestCase
     */
    public function getTaseCase() {
        return $this->testcase;
    }
    
    /**
     * @return void
     */
    public function start() {
        $params = $this->cliParseParams();
        $this->docroot = $params['doc-root'];
        $this->runTests($params['path'], $params);
    }
    
    /**
     * 
     */
    private function runTests( $path, $params ) {
        $path = rtrim($path, '/\\');
        if ( is_file($path) ) {
            $this->testcase = new TestCase($path, $params);
            $this->testcase->execute();
        } else if ( is_dir($path) ) {
            $files = scandir($path);
            foreach ( $files as $file ) {
                $newPath = $path.DIRECTORY_SEPARATOR.$file;
                if ( '.' === $file[0] 
                || (is_file($newPath) && 'xy' !== pathinfo($file, PATHINFO_EXTENSION) ) 
                ) {
                    continue;
                }
                $this->runTests($newPath, $params);
            }
        } else {
            throw new Exception("test path is not available : {$path}");
        }
    }
    
    /**
     * parse command options
     * @return array
     */
    private function cliParseParams() {
        $default = [];
        $conffile = getcwd().'/xunyu.json';
        if ( file_exists($conffile) ) {
            $default = json_decode(file_get_contents($conffile), true);
        }
        
        $cmd = new Command();
        $cmd->option()->describedAs('path to test case(s)');
        $cmd->option('e')->aka('env')->describedAs('path or name of env file, default to env.ini');
        $cmd->option('d')->aka('doc-root')->describedAs('path of document root');
        
        $params = array();
        $params['path'] = (null===$cmd[0]) ? getcwd() : $cmd[0];
        $params['env'] = $cmd['env'];
        $params['doc-root'] = $cmd['doc-root'];
        if ( empty($params['doc-root']) ) {
            $params['doc-root'] = is_dir($params['path']) 
            ? $params['path'] 
            : dirname($params['path']);
        }
        $params = array_filter($params);
        $params = array_merge($default, $params);
        return $params;
    }
}