<?php
namespace app\script\commands;
use app\script\Variable;

class CommandReturn extends BaseCommand {
    /**
     * @var unknown
     */
    private $returnVal = null;
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::setCmdArgs()
     */
    public function setCmdArgs($args) {
        $this->returnVal = empty($args) ? null : $args[0];
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::run()
     */
    protected function run() {
        $runtime = $this->getRuntime();
        
        $returnVal = new Variable($this->returnVal);
        $returnVal->setType(Variable::TYPE_FUNC_RETURN_VAR);
        $runtime->variableSet('return-val', $returnVal);
    }
}