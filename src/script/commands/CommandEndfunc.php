<?php
namespace app\script\commands;
class CommandEndfunc extends BaseCommand {
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::isBlockEnd()
     */
    public function isBlockEnd(ICommand $command) {
        return $command instanceof CommandFunc;
    }
}