<?php
declare(strict_types=1);

namespace Thunder33345\CommandWatcher;

use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Thunder33345\CommandWatcher\Carillon\Carillon;
use Thunder33345\CommandWatcher\Carillon\CarillonManager;

/** Created By Thunder33345 **/
class CommandWatcher extends PluginBase implements Listener
{
	/** @var CarillonManager */
	private $carillonManager;
	/** @var WatcherManager */
	private $watcherManager;
	/** @var Config */
	private $channelsConfig;
	/** @var Config */
	private $watchedConfig;

	public function onLoad()
	{
		$this->saveResource('channels.yml');
		$this->saveResource('watched.yml');
		$this->channelsConfig = new Config($this->getDataFolder() . 'channels.yml');
		$this->watchedConfig = new Config($this->getDataFolder() . 'watched.yml');
	}

	public function onEnable()
	{
		$this->carillonManager = $carillonManager = new CarillonManager($this);
		$this->watcherManager = $watcherManager = new WatcherManager($this);

		$carillonManager->loadFrom($this->channelsConfig->get('channels', []));

		$watchedGroups = $this->watchedConfig->get('watched-commands', []);
		foreach($watchedGroups as $name => $data){
			$trueCommands = [];
			$commandsFixed = [];
			foreach($data['commands'] as $command){
				$cmd = $this->getServer()->getCommandMap()->getCommand($command);
				if($cmd instanceof Command){
					$trueCommands[] = $cmd->getName();
					$commandsFixed[] = $cmd->getName();
				} else {
					$commandsFixed[] = $command;
				}
			}
			$this->watchedConfig->setNested("watched-commands.$name.commands", $commandsFixed);

			$selected_ = $data['channel'] ?? '<%nothing%>';
			$selected = $carillonManager->getCarillon($selected_);
			if(!$selected instanceof Carillon){
				$this->getLogger()->notice("Error Loading group $name: failed to get channel " . $selected_ . '.');
				continue;
			}
			$watcher = new Watcher($name, $selected, ...$trueCommands);
			$watcherManager->addWatchers($watcher);
		}
		$this->watchedConfig->save();
	}
}