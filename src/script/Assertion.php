<?php
namespace app\script;
class Assertion {
    /**
     * @var string
     */
    public $name = null;
    
    /**
     * @var string
     */
    public $expect = null;
    
    /**
     * @var string
     */
    public $actual = null;
    
    /**
     * @var string
     */
    public $message = null;
    
    /**
     * @throws \Exception
     */
    public function assertMatch() {
        if ( preg_match($this->expect, $this->actual) ) {
            return ;
        }
        $this->assertFailed();
    }
    
    /**
     * 
     */
    public function assertEqual() {
        if ( $this->expect == $this->actual ) {
            return ;
        }
        $this->assertFailed();
    }
    
    /**
     * 
     */
    public function assertFailed() {
        $assertion = [];
        $assertion[] = "assertion failed : {$this->name}";
        $assertion[] = "expect : {$this->expect}";
        $assertion[] = "actual : {$this->actual}";
        if ( null !== $this->message && "" !== trim($this->message) ) {
            $assertion[] = "message : {$this->message}";
        }
        $assertion = implode("\n", $assertion);
        throw new \Exception($assertion);
    }
}