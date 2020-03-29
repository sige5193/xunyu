<?php
namespace app\operators;
use app\script\ArgumentParser;
abstract class BaseOperator implements IOperator {
    /**
     * {@inheritDoc}
     * @see \app\operators\IOperator::setCmdArgs()
     */
    public function setCmdArgs($args) {
        $argParser = $this->getArgsParser();
        if ( null === $argParser ) {
            return null;
        }
        
        $argParser->parse($args);
        if ( $argParser->hasError() ) {
            throw new \Exception("Operator param error : {$argParser->getErrorSummary()}");
        }
        
        $argNames = $argParser->getArgNames();
        foreach ( $argNames as $argName ) {
            if ( property_exists($this, $argName) ) {
                $this->$argName = $argParser->get($argName);
            }
        }
    }
    
    /**
     * @return NULL|ArgumentParser
     */
    protected function getArgsParser() {
        return null;
    }
}