<?php
namespace app\script\commands;
class CommandBuildinHandler extends BaseCommand {
    /**
     * @var unknown
     */
    private $handler = null;
    
    /**
     * @var unknown
     */
    private $params = null;
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::setCmdArgs()
     */
    public function setCmdArgs($args) {
        $this->handler = array_shift($args);
        $this->params = $args;
    }
    
    /**
     * {@inheritDoc}
     * @see \app\script\commands\BaseCommand::run()
     */
    protected function run() {
        $handler = explode('.', $this->handler);
        $handler[0] = "\\app\\script\\buildin\\Buildin".ucfirst($handler[0]);
        $handler[1] = "handle".ucfirst($handler[1]);
        
        $params = $this->params;
        array_unshift($params, $this->getRuntime());
        call_user_func_array($handler, $params);
    }
}