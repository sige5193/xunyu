<?php
namespace app\script\commands;
class CommandEndif extends BaseCommand {
    /**
     * {@inheritDoc}
     * @see \app\script\commands\ICommand::isBlockEnd()
     */
    public function isBlockEnd(ICommand $command) {
        return $command instanceof CommandIf;
    }
}