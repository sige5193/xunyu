<?php
namespace app\operators;
class OperatorPoster extends BaseOperator {
    /**
     * @var string
     */
    private $method = null;
    
    /**
     * @var string
     */
    private $address = null;
    
    /**
     * @var array
     */
    private $params = null;
    
    /**
     * @var string|array
     */
    private $body = [];
    
    /**
     * @var string
     */
    private $responseBody = null;
    
    /**
     * @var string
     */
    private $preRequestHandlerName = null;
    
    /**
     * @param unknown $method
     */
    public function cmdMethod( $method ) {
        $this->method = $method;
    }
    
    /**
     * @param unknown $address
     */
    public function cmdAddress ( $address ) {
        $this->address = $address;
    }
    
    /**
     * @param unknown $key
     * @param unknown $value
     */
    public function cmdBodyAdd( $key, $value ) {
        $this->body[$key] = $value;
    }
    
    /**
     * @param unknown $key
     * @param unknown $varName
     */
    public function cmdBodyGet( $key, $varName ) {
        if ( !array_key_exists($key, $this->body) ) {
            throw new \Exception("poster body does not containe the key `{$key}`");
        }
        
        $value = $this->body[$key];
        \Application::app()->getRuntime()->variableSet($varName, $value);
    }
    
    /**
     * 
     */
    public function cmdPreRequestHandler( $funcName ) {
        $this->preRequestHandlerName = $funcName;
    }
    
    /**
     * 
     */
    public function cmdSend() {
        $this->callPreRequestHandler();
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $url = $this->address;
        if ( !empty($this->params) ) {
            $params = http_build_query($this->params);
            $url .= (false===strpos($url, '?')) ? '?' : '&';
            $url .= $params;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        
        switch ( $this->method ){
        case 'POST' :
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
            break;
        default:
            throw new \Exception("method `{$this->method}` has not been supported");
        }
        
        $this->responseBody = curl_exec($ch);
        if ( CURLE_OK !== curl_errno($ch) ) {
            throw new \Exception('request error : '.curl_error($ch));
        }
        
        curl_close($ch);
    }
    
    /**
     * 
     */
    private function callPreRequestHandler( ) {
        if ( null === $this->preRequestHandlerName ) {
            return ;
        }
        
        $runtime = \Application::app()->getRuntime();
        $func = $runtime->funcGet($this->preRequestHandlerName);
        $func->exec([]);
    }
    
    /**
     * @param string $varName
     */
    public function cmdResponseReadAsJson( $varName ) {
        $runtime = \Application::app()->getRuntime();
        $runtime->variableSet($varName, json_decode($this->responseBody, true));
    }
}