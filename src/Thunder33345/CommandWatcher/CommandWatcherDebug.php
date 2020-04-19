<?php
declare(strict_types=1);
/** Created By Thunder33345 **/

namespace Thunder33345\CommandWatcher;

use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginCommand;

class CommandWatcherDebug extends PluginCommand
{
	private $commandWatcher;

	public function __construct(string $name, CommandWatcher $owner)
	{
		$this->commandWatcher = $owner;
		$this->setPermission('');
		parent::__construct($name, $owner);
	}


	public function testPermissionSilent(CommandSender $target):bool
	{
		return ($target instanceof ConsoleCommandSender);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		$commandWatcher = $this->commandWatcher;
		$logger = $this->commandWatcher->getLogger();
		$carillons = $commandWatcher->getCarillonManager()->getAllCarillon();
		foreach($carillons as $carillon){
			$bells = $carillon->getAllBells();
			$logger->info('listing carillon: ' . $carillon->getName() . '(' . count($bells) . ')');
			foreach($bells as $bell){
				$logger->info($carillon->getName() . " registered bell" . get_class($bell));
			}
		}
		$watcherManager = $commandWatcher->getWatcherManager();
		foreach($watcherManager->getWatchers() as $watcher){
			$logger->info("Watcher: " . $watcher->getName() . " using " . $watcher->getCarillon()->getName());
			$logger->info("Watching " . count($watcher->getCommands()) . ": " . implode(',', $watcher->getCommands()));
		}

		$logger->info('Channels config');
		$logger->info(json_encode($commandWatcher->getChannelsConfig()->getAll(), JSON_PRETTY_PRINT));
		$logger->info('Watched config');
		$logger->info(json_encode($commandWatcher->getWatchedConfig()->getAll(), JSON_PRETTY_PRINT));
	}
}