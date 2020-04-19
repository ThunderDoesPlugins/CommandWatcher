<?php
declare(strict_types=1);

namespace Thunder33345\CommandWatcher;

use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Thunder33345\CommandWatcher\Carillon\Carillon;
use Thunder33345\CommandWatcher\Carillon\CarillonManager;

/** Created By Thunder33345 **/
class CommandWatcher extends PluginBase
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
			foreach($data['commands'] as $command){
				$cmd = $this->getServer()->getCommandMap()->getCommand($command);
				if($cmd instanceof Command){
					$trueCommands[] = $cmd->getName();
				} else {
					$this->getLogger()->notice("Failed to find command name $command, assuming it to be real command name");
					$trueCommands[] = $trueCommands;
				}
			}
			$this->watchedConfig->setNested("watched-commands.$name.commands", $trueCommands);

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
		if((bool)$this->watchedConfig->get('debug', false))//hidden debug key
			$this->getServer()->getCommandMap()->register($this->getName(), new CommandWatcherDebug('cwd', $this));
	}

	public function getChannelsConfig():Config{ return $this->channelsConfig; }

	public function getWatchedConfig():Config{ return $this->watchedConfig; }

	public function getCarillonManager():CarillonManager{ return $this->carillonManager; }

	public function getWatcherManager():WatcherManager{ return $this->watcherManager; }
}