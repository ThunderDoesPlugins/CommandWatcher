<?php
declare(strict_types=1);
/** Created By Thunder33345 **/

namespace Thunder33345\CommandWatcher\Carillon\Bells;

use LogLevel;
use pocketmine\command\Command;
use pocketmine\event\server\CommandEvent;
use ReflectionClass;

class LoggerBell extends AbstractBell
{
	private $logLevel;

	public function onTest():bool
	{
		$config = $this->getConfig();
		$level = strtolower($config['level']) ?? null;

		$reflector = new ReflectionClass(LogLevel::class);
		$validLevels = $reflector->getConstants();
		$valid = false;
		foreach($validLevels as $validLevel){
			if($validLevel === $level) $valid = true;
		}
		if(!$valid) return false;
		$this->logLevel = $level;

		$msg = $config['message'] ?? null;
		if(!is_string($msg)) return false;
		$this->setFormatMessage($msg);
		return true;
	}

	public function onRing(Command $command, CommandEvent $commandEvent):void
	{
		$message = $this->parseFormatMessage($command, $commandEvent);
		if($message===null) return;
		$this->getServer()->getLogger()->log($this->logLevel, $message);
	}
}