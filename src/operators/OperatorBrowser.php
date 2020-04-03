<?php
namespace app\operators;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Remote\RemoteWebElement;
use app\script\ArgumentParser;
use Facebook\WebDriver\Remote\LocalFileDetector;
use app\script\Assertion;
/**
 *
 */
class OperatorBrowser extends BaseOperator {
    /**
     * name of browser
     * @var string
     */
    protected $browserName = 'chrome';
    
    /**
     * version of browser
     * @var string
     */
    protected $version = 'default';
    
    /**
     * @var \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    private $driver = null;
    
    /**
     * @var array
     */
    private $pipes = [];
    
    /**
     * @var string
     */
    private $cmdPattern = null;
    
    /**
     * {@inheritDoc}
     * @see \app\operators\BaseOperator::getArgsParser()
     */
    protected function getArgsParser() {
        return ArgumentParser::setup()
        ->addArgument('browserName')->setDefaultValue('browserName', 'chrome')
        ->addArgument('version')->setDefaultValue('version', 'default')
        ->addArgument('as')->setIsKeyword('as',true)
        ->addArgument('operatorName');
    }

    /**
     * {@inheritDoc}
     * @see \app\operators\IOperator::init()
     */
    public function init() {
        $this->pipes['input']['path'] = tempnam(sys_get_temp_dir(), 'XY');
        file_put_contents($this->pipes['input']['path'], '');
        $this->pipes['input']['filehandler'] = fopen($this->pipes['input']['path'], 'r');
        
        $this->pipes['output']['path'] = tempnam(sys_get_temp_dir(), 'XY');
        $this->pipes['output']['filehandler'] = fopen($this->pipes['output']['path'], 'w');
        
        $this->pipes['ioe'] = [
            $this->pipes['input']['filehandler'],
            $this->pipes['output']['filehandler'],
            $this->pipes['output']['filehandler']
        ];
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
        $this->$handler();
        $this->driver->manage()->window()->maximize();
    }
    
    /**
     * @throws \Exception
     */
    private function startBrowserIe() {
        $plarform = \Application::app()->getPlatformName();
        $driverExt = '';
        switch ( $plarform ) {
        case 'windows' :
            $driverExt = '.exe';
            break;
        default:
            throw new \Exception("platform `{$plarform}` has not been supported for browser.");
        }
        
        $driverPath = \Application::app()->getPath("webdriver/iedriver{$driverExt}");
        if ( !file_exists($driverPath) ) {
            throw new \Exception("not supported browser type `{$this->browserName}-v{$this->version}`");
        }
        
        $port = $this->findAnAvailablePort();
        switch ( $plarform ) {
        case 'windows' :
            $command = "\"\"{$driverPath}\" \"/port={$port}\"\"";
            $this->cmdPattern = "#iedriver.*?port={$port}#";
            break;
        default:
            $command = "{$driverPath} --port={$port}";
            break;
        }
        
        $cmd = proc_open($command, $this->pipes['ioe'], $pipes);
        if ( false === $cmd ) {
            throw new \Exception("failed to start webdriver service");
        }
        
        $this->waitForWebdriverServer($port);
        
        # close cmd but not the webdriver service
        proc_terminate($cmd);
        proc_close($cmd);
        
        $host = "http://127.0.0.1:{$port}";
        $dc = DesiredCapabilities::internetExplorer();
        $dc->setCapability('ignoreProtectedModeSettings', true);
        $dc->setCapability('ignoreZoomSetting', true);
        $dc->setCapability('initialBrowserUrl', 'https://www.bing.com');
        $this->driver = RemoteWebDriver::create($host, $dc);
    }
    
    /**
     * @throws \Exception
     * @link https://stackoverflow.com/questions/41133391/which-chromedriver-version-is-compatible-with-which-chrome-browser-version
     */
    private function startBrowserChrome() {
        $driverVersion = null;
        $driverExt = '';
        
        $plarform = \Application::app()->getPlatformName();
        switch ( $plarform ) {
        case 'windows' :
            $chromePath = trim(shell_exec("where chrome.exe"));
            if ( empty($chromePath) ) {
                throw new \Exception("unable to find chrome.exe, please make sure you have chrome.exe in your PATH.");
            }
            
            $chromePath = str_replace('\\', '\\\\', $chromePath);
            $chromeVersion = trim(shell_exec("wmic datafile where name=\"{$chromePath}\" get Version /value"));
            $chromeVersion = explode('.', str_replace('Version=', '', $chromeVersion));
            $chromeVersion = intval($chromeVersion[0]);
            if ( 'default' !== $this->version && $this->version != $chromeVersion ) {
                throw new \Exception("test case requires chrome {$this->versio} but your chrome is version {$chromeVersion}");
            }
            $driverExt = '.exe';
            break;
        default:
            throw new \Exception("platform `{$plarform}` has not been supported for browser.");
        }
        
        $browserDriverVersionmap = [
            '72'=>'2.46','71'=>'2.46','70'=>'2.45','69'=>'2.44','68'=>'2.43',
            '67'=>'2.41','66'=>'2.40','65'=>'2.38','64'=>'2.37','63'=>'2.36',
            '62'=>'2.35','61'=>'2.34','60'=>'2.33'
        ];
        
        if ( 73 <= $chromeVersion ) {
            $driverVersion = $chromeVersion;
        } else if ( isset($browserDriverVersionmap[$chromeVersion]) ) {
            $driverVersion = $browserDriverVersionmap[$chromeVersion];
        } else if ( 57 <= $chromeVersion ) {
            $driverVersion = '2.28';
        } else if ( 54 <= $chromeVersion ) {
            $driverVersion = '2.25';
        } else if ( 53 <= $chromeVersion ) {
            $driverVersion = '2.24';
        } else if ( 51 <= $chromeVersion ) {
            $driverVersion = '2.22';
        } else if ( 44 <= $chromeVersion ) {
            $driverVersion = '2.19';
        } else if ( 42 <= $chromeVersion ) {
            $driverVersion = '2.15';
        } else {
            throw new \Exception("unable to match webdriver version for chrome v{$chromeVersion}");
        }
        
        $driverPath = \Application::app()->getPath("webdriver/chromedriver-{$driverVersion}{$driverExt}");
        if ( !file_exists($driverPath) ) {
            throw new \Exception("not supported browser type `{$this->browserName}-v{$this->version}`");
        }
        
        $port = $this->findAnAvailablePort();
        
        switch ( $plarform ) {
        case 'windows' :
            $command = "\"\"{$driverPath}\" \"--port={$port}\"\"";
            $this->cmdPattern = "#chromedriver.*?--port={$port}#";
            break;
        default:
            $command = "{$driverPath} --port={$port}";
            break;
        }
        
        $cmd = proc_open($command, $this->pipes['ioe'], $pipes);
        if ( false === $cmd ) {
            throw new \Exception("failed to start webdriver service");
        }
        
        $this->waitForWebdriverServer($port);
        proc_terminate($cmd);
        proc_close($cmd);
        
        $host = "http://127.0.0.1:{$port}";
        $this->driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());
    }
    
    /**
     * @return void
     */
    private function startBrowserFirefox() {
        $plarform = \Application::app()->getPlatformName();
        $driverExt = '';
        
        switch ( $plarform ) {
        case 'windows' :
            $firefoxPath = trim(shell_exec("where firefox.exe"));
            if ( empty($firefoxPath) ) {
                throw new \Exception("unable to find firefox.exe, please make sure you have firefox.exe in your PATH.");
            }
            $driverExt = '.exe';
            
            break;
        default:
            throw new \Exception("platform `{$plarform}` has not been supported for browser.");
        }
        
        $driverPath = \Application::app()->getPath("webdriver/firefoxdriver{$driverExt}");
        if ( !file_exists($driverPath) ) {
            throw new \Exception("unable to find firefox webdriver");
        }
        
        $port = $this->findAnAvailablePort();
        switch ( $plarform ) {
        case 'windows' :
            $command = "\"\"{$driverPath}\" \"--port\" \"{$port}\" \"-vv\"\"";
            $this->cmdPattern = "#firefoxdriver.*?-port.*?{$port}#";
            break;
        default:
            $command = "{$driverPath} -p {$port}";
            break;
        }
        $cmd = proc_open($command, $this->pipes['ioe'], $pipes);
        if ( false === $cmd ) {
            throw new \Exception("failed to start webdriver service");
        }
        
        $this->waitForWebdriverServer($port);
        
        # close cmd but not the webdriver service
        proc_terminate($cmd);
        proc_close($cmd);
        
        $host = "http://127.0.0.1:{$port}";
        $this->driver = RemoteWebDriver::create($host, DesiredCapabilities::firefox());
    }
    
    /**
     * find a available port to use
     * @return integer
     */
    private function findAnAvailablePort() {
        $port = 65534;
        while ( 0 < $port ) {
            $fp = @fsockopen('127.0.0.1', $port, $errno, $errstr, 5);
            if (false === $fp) {
                return $port;
            }
            fclose($fp);
            $port --;
        }
        throw new \Exception('unable to find an available port for webdriver server');
    }
    
    /**
     * @param integer $port
     */
    private function waitForWebdriverServer( $port ) {
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
    }
    
    /**
     * {@inheritDoc}
     * @see \app\operators\IOperator::stop()
     */
    public function stop() {
        $this->driver->quit();
        
        $plarform = \Application::app()->getPlatformName();
        switch ( $plarform ) {
        case 'windows' :
            exec('WMIC PROCESS get Processid,Commandline', $output);
            foreach ( $output as $outputLine ) {
                if ( '' === trim($outputLine) || !preg_match($this->cmdPattern, $outputLine) ) {
                    continue;
                }
                preg_match('#\\d+$#', $outputLine, $matchedPid);
                exec("taskkill /F /PID {$matchedPid[0]}");
                break;
            }
            break;
        default:
            throw new \Exception("platform `{$plarform}` has not been supported for browser.");
            break;
        }
    }

    /**
     * {@inheritDoc}
     * @see \app\operators\IOperator::destory()
     */
    public function destory() {
        fclose($this->pipes['input']['filehandler']);
        fclose($this->pipes['output']['filehandler']);
        unlink($this->pipes['input']['path']);
        unlink($this->pipes['output']['path']);
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
     * trigger double click event on an element
     * @param string $selector
     */
    public function cmdDblclick( $selector ) {
        $action = new WebDriverActions($this->driver);
        $action->doubleClick($this->driver->findElement($this->parseSelector($selector)))->perform();
    }
    
    /**
     * @param unknown $selector
     * @param unknown $content
     */
    public function cmdInput( $selector, $content ) {
        $this->driver->findElement($this->parseSelector($selector))->sendKeys($content);
    }
    
    /**
     * @param unknown $selector
     * @param unknown $path
     */
    public function cmdUpload($selector, $path) {
        $path = \Application::app()->getDocPath($path);
        $elem = $this->driver->findElement($this->parseSelector($selector));
        if ( 'ie' === $this->browserName ) {
            $path = str_replace('/', '\\', $path);
            $elem->sendKeys($path);
        } else {
            $elem->setFileDetector(new LocalFileDetector())->sendKeys($path);
        }
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
     * 
     */
    public function cmdAlertAccept() {
        $this->driver->switchTo()->alert()->accept();
    }
    
    /**
     * 
     */
    public function cmdAlertDismiss() {
        $this->driver->switchTo()->alert()->dismiss();
    }
    
    /**
     * @param unknown $value
     */
    public function cmdAlertInput($value) {
        $this->driver->switchTo()->alert()->sendKeys($value);
    }
    
    /**
     * @param unknown $path
     */
    public function cmdTakeScreenshot($path) {
        $path = \Application::app()->getDocPath($path);
        $this->driver->takeScreenshot($path);
    }
    
    /**
     * @param unknown $selector
     */
    public function cmdSwitchToFrame($selector) {
        $frame = $this->driver->findElement($this->parseSelector($selector));
        $this->driver->switchTo()->frame($frame);
    }
    
    /**
     * 
     */
    public function cmdSwitchToParent() {
        $this->driver->switchTo()->parent();
    }
    
    /**
     * 
     */
    public function cmdCloseCurrentTab() {
        $this->driver->close();
        $handlers = $this->driver->getWindowHandles();
        $this->driver->switchTo()->window($handlers[0]);
    }
    
    /**
     * @param unknown $selector
     */
    public function cmdBlur( $selector ) {
        $this->driver->findElement($this->parseSelector($selector))->sendKeys("\t");
    }
    
    /**
     * @param unknown $title
     */
    public function cmdWaitTitle( $title, $timeout=null ) {
        $wait = new WebDriverWait($this->driver, $timeout);
        if ( '/' === $title[0] ) {
            $wait->until(WebDriverExpectedCondition::titleMatches($title));
        } else {
            $wait->until(WebDriverExpectedCondition::titleIs($title));
        }
    }
    
    /**
     * @param unknown $url
     * @param unknown $timeout
     */
    public function cmdWaitUrl( $url, $timeout=null ) {
        $wait = new WebDriverWait($this->driver, $timeout);
        if ( '/' === $url[0] ) {
            $wait->until(WebDriverExpectedCondition::urlMatches($url));
        } else {
            $wait->until(WebDriverExpectedCondition::urlIs($url));
        }
    }
    
    /**
     * @param unknown $selector
     * @param unknown $timeout
     */
    public function cmdWaitElemExists( $selector, $timeout=null ) {
        $by = $this->parseSelector($selector);
        $wait = new WebDriverWait($this->driver, $timeout);
        $wait->until(WebDriverExpectedCondition::presenceOfElementLocated($by));
    }
    
    /**
     * @param unknown $selector
     * @param unknown $timeout
     */
    public function cmdWaitElemNotExists( $selector, $timeout=null ) {
        $by = $this->parseSelector($selector);
        $wait = new WebDriverWait($this->driver, $timeout);
        $wait->until(WebDriverExpectedCondition::not(WebDriverExpectedCondition::presenceOfElementLocated($by)));
    }
    
    /**
     * @param unknown $selector
     * @param unknown $timeout
     */
    public function cmdWaitElemVisiable( $selector, $timeout=null ) {
        $by = $this->driver->findElement($this->parseSelector($selector));
        $wait = new WebDriverWait($this->driver, $timeout);
        $wait->until(WebDriverExpectedCondition::visibilityOf($by));
    }
    
    /**
     * @param unknown $selector
     * @param unknown $timeout
     */
    public function cmdWaitElemInvisible( $selector, $timeout=null ) {
        $by = $this->parseSelector($selector);
        $wait = new WebDriverWait($this->driver, $timeout);
        $wait->until(WebDriverExpectedCondition::invisibilityOfElementLocated($by));
    }
    
    /**
     * @param unknown $selector
     * @param unknown $text
     * @param unknown $timeout
     */
    public function cmdWaitElemText( $selector, $text, $timeout=null ) {
        $by = $this->parseSelector($selector);
        $wait = new WebDriverWait($this->driver, $timeout);
        if ( '/' === $text[0] ) {
            $wait->until(WebDriverExpectedCondition::elementTextMatches($by, $text));
        } else {
            $wait->until(WebDriverExpectedCondition::elementTextIs($by, $text));
        }
    }
    
    /**
     * 
     */
    public function cmdWaitAlertPresent( $timeout=null ) {
        $wait = new WebDriverWait($this->driver, $timeout);
        $wait->until(WebDriverExpectedCondition::alertIsPresent());
    }
    
    /**
     * @param unknown $title
     */
    public function cmdAssertTitle( $title, $message=null ) {
        $assertion = new Assertion();
        $assertion->message = $message;
        $assertion->expect = $title;
        $assertion->actual = $this->driver->getTitle();
        
        if ( '/' === $title[0] ) {
            $assertion->name = 'browser title matches';
            $assertion->assertMatch();
        } else {
            $assertion->name = 'browser title equals';
            $assertion->assertEqual();
        }
    }
    
    /**
     * @param unknown $url
     * @param unknown $timeout
     */
    public function cmdAssertUrl( $url, $message=null ) {
        $assertion = new Assertion();
        $assertion->message = $message;
        $assertion->expect = $url;
        $assertion->actual = $this->driver->getCurrentURL();
        
        if ( '/' === $url[0] ) {
            $assertion->name = 'browser url matches';
            $assertion->assertMatch();
        } else {
            $assertion->name = 'browser url equals';
            $assertion->assertEqual();
        }
    }
    
    /**
     * @param unknown $selector
     * @param unknown $timeout
     */
    public function cmdAssertElemExists( $selector, $message=null ) {
        $assertion = new Assertion();
        $assertion->message = $message;
        $assertion->expect = true;
        $assertion->actual = !empty($this->driver->findElements($this->parseSelector($selector)));
        $assertion->name = 'browser document element exists';
        $assertion->assertEqual();
    }
    
    /**
     * @param unknown $selector
     * @param unknown $timeout
     */
    public function cmdAssertElemVisiable( $selector, $message=null ) {
        $assertion = new Assertion();
        $assertion->message = $message;
        $assertion->expect = true;
        $assertion->actual = $this->driver->findElement($this->parseSelector($selector))->isDisplayed();
        $assertion->name = 'browser document element visiable';
        $assertion->assertEqual();
    }
    
    /**
     * @param unknown $selector
     * @param unknown $timeout
     */
    public function cmdAssertElemInvisible( $selector, $message=null ) {
        $assertion = new Assertion();
        $assertion->message = $message;
        $assertion->expect = false;
        $assertion->actual = !$this->driver->findElement($this->parseSelector($selector))->isDisplayed();
        $assertion->name = 'browser document element invisiable';
        $assertion->assertEqual();
    }
    
    /**
     * @param unknown $selector
     * @param unknown $text
     * @param unknown $timeout
     */
    public function cmdAssertElemText( $selector, $text, $message=null ) {
        $assertion = new Assertion();
        $assertion->message = $message;
        $assertion->expect = $text;
        $assertion->actual = $this->driver->findElement($this->parseSelector($selector))->getText();
        
        if ( '/' === $text[0] ) {
            $assertion->name = 'browser document element text matches';
            $assertion->assertMatch();
        } else {
            $assertion->name = 'browser document element text equels';
            $assertion->assertEqual();
        }
    }
    
    /**
     *
     */
    public function cmdAssertAlertPresent( $message=null ) {
        $assertion = new Assertion();
        $assertion->message = $message;
        $assertion->expect = true;
        $assertion->actual = null !== WebDriverExpectedCondition::alertIsPresent();
        $assertion->name = 'browser document element invisiable';
        $assertion->assertEqual();
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