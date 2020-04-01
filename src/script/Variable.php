<?php
namespace app\script;
class Variable {
    /**
     * @var integer
     */
    const TYPE_NORMAL = 1;
    
    /**
     * @var integer
     */
    const TYPE_FUNC_RETURN_VAR = 2;
    
    /**
     * @var unknown
     */
    private $value = null;
    
    /**
     * @var unknown
     */
    private $type = null;
    
    /**
     * @param unknown $value
     */
    public function __construct( $value ) {
        $this->value = $value;
        $this->type = self::TYPE_NORMAL;
    }
    
    /**
     * @param unknown $type
     */
    public function setType( $type ) {
        $this->type = $type;
    }
    
    /**
     * @return \app\script\unknown
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * @return \app\script\unknown
     */
    public function getValue() {
        return $this->value;
    }
}