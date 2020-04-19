<?php
declare(strict_types=1);
/** Created By Thunder33345 **/

namespace Thunder33345\CommandWatcher;

use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;

class WatcherManager implements Listener
{
	/** @var CommandWatcher */
	private $commandWatcher;
	/** @var Watcher[] */
	private $watchers = [];

	public function __construct(CommandWatcher $commandWatcher)
	{
		$this->commandWatcher = $commandWatcher;
		$commandWatcher->getServer()->getPluginManager()->registerEvents($this, $commandWatcher);
	}

	public function addWatchers(Watcher...$watchers)
	{
		foreach($watchers as $watcher)
			$this->watchers[] = $watcher;
	}

	public function OnCommandEvent(CommandEvent $commandEvent)
	{
		$server = $this->commandWatcher->getServer();

		$commandLine = $commandEvent->getCommand();
		$commandName = explode(' ', $commandLine, 2)[0];
		$selectedCommand = $server->getCommandMap()->getCommand($commandName);
		if(!$selectedCommand instanceof Command) return;

		foreach($this->watchers as $watcher){
			$watcher->testCommand($selectedCommand, $commandEvent);
		}
	}
}