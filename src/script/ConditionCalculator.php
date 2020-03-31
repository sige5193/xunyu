<?php
namespace app\script;
class ConditionCalculator {
    /**
     * @var unknown
     */
    public $leftValue = null;
    
    /**
     * @var unknown
     */
    public $rightValue = null;
    
    /**
     * @var unknown
     */
    public $operator = null;
    
    /**
     * @throws \Exception
     * @return boolean
     */
    public function calculate() {
        switch ( $this->operator ) {
        case '='  : return $this->leftValue == $this->rightValue;
        case '>=' : return $this->leftValue >= $this->rightValue;
        case '>'  : return $this->leftValue >  $this->rightValue;
        case '<=' : return $this->leftValue <= $this->rightValue;
        case '<'  : return $this->leftValue <  $this->rightValue;
        case '!=' : return $this->leftValue != $this->rightValue;
        default   : throw new \Exception("operator `{$this->operator}` has not been supported");
        }
    }
}