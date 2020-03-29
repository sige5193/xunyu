<?php
namespace app\script;
class ArgumentParser {
    /**
     * @var array
     */
    private $args = array();
    
    /**
     * @var array
     */
    private $errors = array();
    
    /**
     * @return self
     */
    public static function setup() {
        return new self();
    }
    
    /**
     * @param string $name
     * @return self
     */
    public function addArgument($name) {
        $this->args[$name] = array(
            'IsRequired'=>false, 
            'DefaultValue'=>null,
            'IsKeywrod'=>false,
            'Value' => null,
            'Errors'=> array(),
        );
        return $this;
    }
    
    /**
     * @param string $name
     * @param boolean $isRequired
     * @return self
     */
    public function setIsRequired($name, $isRequired) {
        $this->args[$name]['IsRequired'] = $isRequired;
        return $this;
    }
    
    /**
     * @param string $name
     * @param string $defaultValue
     * @return self
     */
    public function setDefaultValue($name, $defaultValue) {
        $this->args[$name]['DefaultValue'] = $defaultValue;
        $this->args[$name]['Value'] = $defaultValue;
        return $this;
    }
    
    /**
     * @param string $name
     * @param string $isKeyWord
     * @return self
     */
    public function setIsKeyword($name, $isKeyWord) {
        $this->args[$name]['IsKeywrod'] = $isKeyWord;
        return $this;
    }
    
    /**
     * @param unknown $args
     * @return self
     */
    public function parse( $args ) {
        $keywords = array();
        foreach ( $this->args as $key => $def ) {
            if ( true === $def['IsKeywrod'] ) {
                $keywords[] = strtolower($key);
            }
        }
        
        foreach ( $this->args as $key => $def ) {
            if ( empty($args) ) {
                break;
            }
            $param = $args[0];
            
            if ( true === $def['IsKeywrod'] 
            && in_array(strtolower($param), $keywords) 
            && strtolower($key) === strtolower($param) 
            ) {
                # 当前在匹配关键词 并且 参数为关键词
                $this->args[$key]['Value'] = true;
                array_shift($args);
                continue;
            }
            
            if ( false === $def['IsKeywrod'] 
            && in_array(strtolower($param), $keywords) 
            ) {
                # 当前正在匹配普通参数， 结果参数是关键词， 则该参数处理为默认值
                if ( true === $def['IsRequired'] ) {
                    $this->args[$key]['Errors'][] = "{$key} is required";
                    continue;
                } else {
                    $this->args[$key]['Value'] = $this->args[$key]['DefaultValue'];
                    array_shift($args);
                    continue;
                }
            }
            
            if ( false === $def['IsKeywrod']
            && !in_array(strtolower($param), $keywords) ) {
                # 当前在匹配普通参数， 并且参数为普通参数， 这种是最理想的， 直接赋值。
                $this->args[$key]['Value'] = $param;
                array_shift($args);
                continue;
            }
        }
        
        return $this;
    }
    
    /**
     * @return boolean
     */
    public function hasError() {
        foreach ( $this->args as $key => $arg ) {
            if ( !empty($arg['Errors']) ) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @return array
     */
    public function getErrors() {
        $errors = array();
        foreach ( $this->args as $key => $arg ) {
            if ( !empty($arg['Errors']) ) {
                $errors[$key] = $arg['Errors']; 
            }
        }
        return $errors;
    }
    
    /**
     * @return string
     */
    public function getErrorSummary() {
        $errors = $this->getErrors();
        foreach ( $errors as $key => &$errorItem ) {
            $errorItem = "{$key} : ".implode(',', $errorItem);
        }
        return implode(';', $errors);
    }
    
    /**
     * @param unknown $name
     */
    public function get( $name ) {
        return $this->args[$name]['Value'];
    }
    
    /**
     * @return array
     */
    public function getArgNames() {
        return array_keys($this->args);
    }
}