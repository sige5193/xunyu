<?php 
use app\script\Parser;
use app\script\Runtime;
use Commando\Command;
use app\script\RuntimeErrorException;
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
     * @var Parser
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
        
        $this->docroot = $params['doc-root'];
        
        # set up env vars
        $envpath = $this->getDocPath($params['env']);
        if ( file_exists($envpath) ) {
            $env = parse_ini_file($envpath, true);
            $runtime->variableSet('env', $env);
        }
        
        $this->runTests($params['path']);
    }
    
    /**
     * 
     */
    private function runTests( $path ) {
        $path = rtrim($path, '/\\');
        if ( is_file($path) ) {
            echo "[==> {$path}]\n";
            return $this->runCommandsByFile($path);
        } else if ( is_dir($path) ) {
            $files = scandir($path);
            foreach ( $files as $file ) {
                $newPath = $path.DIRECTORY_SEPARATOR.$file;
                if ( '.' === $file[0] 
                || (is_file($newPath) && '.xy' !== pathinfo($file, PATHINFO_EXTENSION) ) 
                ) {
                    continue;
                }
                $this->runTests($newPath);
            }
        } else {
            throw new Exception("test path is not available : {$path}");
        }
    }
    
    /**
     * @return \Commando\Command
     */
    private function cliParseParams() {
        $cmd = new Command();
        $cmd->option()->describedAs('path to test case(s)');
        $cmd->option('e')->aka('env')->default('env.ini')->describedAs('path or name of env file, default to env.ini');
        $cmd->option('d')->aka('doc-root')->describedAs('path of document root');
        
        $params = array();
        $conffile = getcwd().'/xunyu.json';
        if ( file_exists($conffile) ) {
            $params = json_decode(file_get_contents($conffile), true);
        }
        
        $params['path'] = $cmd[0];
        $params['env'] = $cmd['env'];
        $params['doc-root'] = $cmd['doc-root'];
        if ( empty($params['doc-root']) ) {
            $params['doc-root'] = is_dir($params['path']) 
            ? $params['path'] 
            : dirname($params['path']);
        }
        return $params;
    }
    
    /**
     * @param string $file
     */
    public function runCommandsByFile( $file ) {
        $file = is_file($file) ? $file : $this->getDocPath($file);
        if ( !file_exists($file) ) {
            throw new RuntimeErrorException("unable to load script file : {$file}");
        }
        
        $commands = file($file);
        foreach ( $commands as $index => $commandText ) {
            $commandText = trim($commandText);
            if ( empty($commandText) ) {
                continue;
            }
            try {
                $command = $this->parser->parse($commandText);
                $command->setDefination('file', $file);
                $command->setDefination('line', $index+1);
                $this->runtime->execCommand($command);
            } catch ( Exception $e ) {
                echo "\n\nERROR : {$e->getMessage()}\n";
                exit();
            }
        }
    }
}