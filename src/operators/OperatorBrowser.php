<?php
namespace app\operators;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Remote\RemoteWebElement;
/**
 * @todo 进程意外退出时，浏览器服务没有关闭
 */
class OperatorBrowser extends BaseOperator {
    /**
     * name of browser
     * @var string
     */
    private $browserName = 'chrome';
    
    /**
     * version of browser
     * @var string
     */
    private $version = 'default';
    
    /**
     * @var \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    private $driver = null;
    
    /**
     * @var resource
     */
    private $input = null;
    
    /**
     * @var resource
     */
    private $output = null;
    
    /**
     * @example use browser
     * @example use browser chrome
     * @example use browser chrome 80
     */
    public function setCmdArgs($args) {
        if ( !isset($args[0]) ) {
            throw new \Exception("unable");
        }
        $this->browserName = isset($args[0]) ? $args[0] : $this->browserName;
        $this->version = isset($args[1]) ? $args[1] : $this->version;
    }

    /**
     * {@inheritDoc}
     * @see \app\operators\IOperator::init()
     */
    public function init() {
        $inputFile = \Application::app()->getPath('tmp/operator-browser-input');
        file_put_contents($inputFile, '');
        $this->input = fopen($inputFile, 'r');
        
        $outputFile = \Application::app()->getPath('tmp/operator-browser-output');
        $this->output = fopen($outputFile, 'w');
    }

    /**
     * {@inheritDoc}
     * @see \app\operators\IOperator::start()
     */
    public function start() {
        $handler = 'startBrowser'.ucfirst($this->browserName);
        if ( !method_exists($this, $handler) ) {
            throw new \Exception("browser `{$this->browserName}` has not been supported");
        }
        return $this->$handler();
    }
    
    /**
     * @throws \Exception
     */
    private function startBrowserIe() {
        $plarform = \Application::app()->getPlatformName();
        $extName = ('windows' === $plarform) ? '.exe' : '';
        $driverPath = \Application::app()->getPath("platforms/{$plarform}/iedriver-{$this->version}{$extName}");
        if ( !file_exists($driverPath) ) {
            throw new \Exception("not supported browser type `{$this->browserName}-v{$this->version}`");
        }
        
        $cmd = proc_open($driverPath, [$this->input, $this->output, $this->output], $pipes);
        if ( false === $cmd ) {
            throw new \Exception("failed to start webdriver service");
        }
        
        $isServiceAvailable = false;
        for ( $i=0; $i<5; $i++ ) {
            sleep(1);
            $fp = @fsockopen('127.0.0.1', 5555, $errno, $errstr, 0.1);
            if (false !== $fp) {
                fclose($fp);
                $isServiceAvailable = true;
                break;
            }
        }
        if ( !$isServiceAvailable ) {
            throw new \Exception("unable to connect to webdriver service");
        }
        
        # close cmd but not the webdriver service
        proc_terminate($cmd);
        proc_close($cmd);
        
        $host = 'http://127.0.0.1:5555';
        $dc = DesiredCapabilities::internetExplorer();
        $dc->setCapability('ignoreProtectedModeSettings', true);
        $dc->setCapability('ignoreZoomSetting', true);
        $dc->setCapability('initialBrowserUrl', 'https://www.bing.com');
        $this->driver = RemoteWebDriver::create($host, $dc);
    }
    
    /**
     * @throws \Exception
     */
    private function startBrowserChrome() {
        $plarform = \Application::app()->getPlatformName();
        $extName = ('windows' === $plarform) ? '.exe' : '';
        $driverPath = \Application::app()->getPath("platforms/{$plarform}/chromedriver-{$this->version}{$extName}");
        if ( !file_exists($driverPath) ) {
            throw new \Exception("not supported browser type `{$this->browserName}-v{$this->version}`");
        }
        
        $cmd = proc_open($driverPath, [$this->input, $this->output, $this->output], $pipes);
        if ( false === $cmd ) {
            throw new \Exception("failed to start webdriver service");
        }
        
        $isServiceAvailable = false;
        for ( $i=0; $i<5; $i++ ) {
            sleep(1);
            $fp = @fsockopen('127.0.0.1', 9515, $errno, $errstr, 0.1);
            if (false !== $fp) {
                fclose($fp);
                $isServiceAvailable = true;
                break;
            }
        }
        if ( !$isServiceAvailable ) {
            throw new \Exception("unable to connect to webdriver service");
        }
        
        # close cmd but not the webdriver service
        proc_terminate($cmd);
        proc_close($cmd);
        
        $host = 'http://localhost:9515';
        $this->driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());
    }
    
    /**
     * @return void
     */
    private function startBrowserFirefox() {
        $plarform = \Application::app()->getPlatformName();
        $extName = ('windows' === $plarform) ? '.exe' : '';
        $driverPath = \Application::app()->getPath("platforms/{$plarform}/firefoxdriver-{$this->version}{$extName}");
        if ( !file_exists($driverPath) ) {
            throw new \Exception("not supported browser type `{$this->browserName}-v{$this->version}`");
        }
        
        $port = 4444;
        $startCmd = "\"\"{$driverPath}\" \"-p\" \"{$port}\" \"-vv\"\"";
        $cmd = proc_open($startCmd, [$this->input, $this->output, $this->output], $pipes);
        if ( false === $cmd ) {
            throw new \Exception("failed to start webdriver service");
        }
        
        $isServiceAvailable = false;
        for ( $i=0; $i<5; $i++ ) {
            sleep(1);
            $fp = @fsockopen('127.0.0.1', $port, $errno, $errstr, 0.1);
            if (false !== $fp) {
                fclose($fp);
                $isServiceAvailable = true;
                break;
            }
        }
        if ( !$isServiceAvailable ) {
            throw new \Exception("unable to connect to webdriver service");
        }
        
        # close cmd but not the webdriver service
        proc_terminate($cmd);
        proc_close($cmd);
        
        $host = "http://127.0.0.1:{$port}";
        $this->driver = RemoteWebDriver::create($host, DesiredCapabilities::firefox());
    }

    /**
     * {@inheritDoc}
     * @see \app\operators\IOperator::stop()
     */
    public function stop() {
        $this->driver->close();
        
        # end the webdriver service
        $terminateOutput = null;
        $terminateReturnVar = null;
        switch ( $this->browserName ) {
        case 'ie' : exec("taskkill /F /im iedriver-default.exe", $terminateOutput, $terminateReturnVar); break;
        case 'chrome' : exec("taskkill /F /im chromedriver-80.exe", $terminateOutput, $terminateReturnVar); break;
        case 'firefox' : exec("taskkill /F /im firefoxdriver-default.exe", $terminateOutput, $terminateReturnVar); break;
        default : throw new \Exception('not handled'); break;
        }
    }

    /**
     * {@inheritDoc}
     * @see \app\operators\IOperator::destory()
     */
    public function destory() {
        fclose($this->input);
        fclose($this->output);
        unlink(\Application::app()->getPath('tmp/operator-browser-input'));
        unlink(\Application::app()->getPath('tmp/operator-browser-output'));
    }
    
    /**
     * @param unknown $url
     */
    public function cmdOpen( $url ) {
        $this->driver->get($url);
    }
    
    /**
     * click a  elem
     */
    public function cmdClick( $selector ) {
        $this->driver->findElement($this->parseSelector($selector))->click();
    }
    
    /**
     * @param unknown $selector
     * @param unknown $content
     */
    public function cmdInput( $selector, $content ) {
        $this->driver->findElement($this->parseSelector($selector))->sendKeys($content);
    }
    
    /**
     * @param unknown $args
     */
    public function cmdHover( $selector ) {
        $action = new WebDriverActions($this->driver);
        $action->moveToElement($this->driver->findElement($this->parseSelector($selector)))->perform();
    }
    
    /**
     * @param unknown $selector
     * @param unknown $timeout
     */
    public function cmdWaitElemPresent ( $selector, $timeout=null ) {
        $wait = new WebDriverWait($this->driver, $timeout);
        $wait->until(WebDriverExpectedCondition::visibilityOf($this->driver->findElement($this->parseSelector($selector))));
    }
    
    /**
     * @param unknown $url
     * @example wait-url http://www.google.com
     */
    public function cmdWaitUrl( $url, $timeout=null ) {
        $wait = new WebDriverWait($this->driver, $timeout);
        $wait->until(WebDriverExpectedCondition::urlIs($url));
    }
    
    /**
     * @param unknown $elem
     * @param unknown $text
     */
    public function cmdWaitElemText( $selector, $text, $timeout=null ) {
        $wait = new WebDriverWait($this->driver, $timeout);
        $wait->until(WebDriverExpectedCondition::elementTextIs($this->parseSelector($selector), $text));
    }
    
    /**
     * @param unknown $tab
     */
    public function cmdCloseTab( $tab ) {
        $tabs = $this->driver->getWindowHandles();
        $this->driver->switchTo()->window($tabs[$tab-1]);
        $this->driver->close();
        
        $tabs = $this->driver->getWindowHandles();
        if ( !empty($tabs) ) {
            $this->driver->switchTo()->window($tabs[0]);
        }
    }
    
    /**
     * @param unknown $url
     */
    public function cmdCloseTabByUrl( $url ) {
        $tabs = $this->driver->getWindowHandles();
        foreach ( $tabs as $tab ) {
            $this->driver->switchTo()->window($tab);
            if ( $url === $this->driver->getCurrentURL() ) {
                $this->driver->close();
            }
        }
        
        $tabs = $this->driver->getWindowHandles();
        if ( !empty($tabs) ) {
            $this->driver->switchTo()->window($tabs[0]);
        }
    }
    
    /**
     * @param unknown $selector
     * @param unknown $text
     */
    public function cmdSelect( $selector, $text ) {
        $select = $this->driver->findElement($this->parseSelector($selector));
        $options = $select->findElements(WebDriverBy::tagName('option'));
        foreach ( $options as $option ) {
            /** @var RemoteWebElement $option */
            if ( trim($text) === trim($option->getText()) ) {
                $option->click();
            }
        }
    }
    
    /**
     * @param string $selector
     * @example #id
     * @example .class
     * @example /xpath
     * @example >登录
     * @example tag
     */
    private function parseSelector( $selector ) {
        $startChar = $selector[0];
        
        switch ( $startChar ) {
        case '#' : return WebDriverBy::id(substr($selector, 1)); 
        case '.' : return WebDriverBy::className(substr($selector, 1));
        case '/' : return WebDriverBy::xpath($selector);
        case '>' : return WebDriverBy::linkText(substr($selector, 1));
        default  : return WebDriverBy::tagName($selector);
        }
    }
}