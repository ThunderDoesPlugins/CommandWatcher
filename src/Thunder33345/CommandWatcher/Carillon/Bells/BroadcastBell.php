<?php
declare(strict_types=1);
/** Created By Thunder33345 **/

namespace Thunder33345\CommandWatcher\Carillon\Bells;

use pocketmine\command\Command;
use pocketmine\event\server\CommandEvent;

class BroadcastBell extends AbstractBell
{
	private $permission;

	public function onTest():bool
	{
		$config = $this->getConfig();
		$permission = $config['permission'] ?? null;
		if(!is_string($permission)) return false;
		$this->permission = $permission;

		$msg = $config['message'] ?? null;
		if(!is_string($msg)) return false;
		$this->setFormatMessage($msg);
		return true;
	}

	public function onRing(Command $command, CommandEvent $commandEvent):void
	{
		$message = $this->parseFormatMessage($command, $commandEvent);
		if($message===null) return;
		foreach($this->getServer()->getOnlinePlayers() as $player){
			if($player->hasPermission($this->permission)) $player->sendMessage($message);
		}
	}
}