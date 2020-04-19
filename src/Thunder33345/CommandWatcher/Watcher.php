<?php
declare(strict_types=1);

namespace Thunder33345\CommandWatcher;

use pocketmine\command\Command;
use pocketmine\event\server\CommandEvent;
use Thunder33345\CommandWatcher\Carillon\Carillon;

/** Created By Thunder33345 **/
class Watcher
{
	private $name;
	private $carillon;
	private $commands = [];

	public function __construct(string $name, Carillon $carillon, string ...$commands)
	{
		$this->name = $name;
		$this->carillon = $carillon;
		foreach($commands as $command){
			$this->commands[] = strtolower($command);
		}
	}

	/*
	 * API for command event handling
	 */
	public function testCommand(Command $command, CommandEvent $commandEvent):void
	{
		if(!$this->isWatched($command->getName())) return;
		$test = $command->testPermissionSilent($commandEvent->getSender());
		if($test)
			$this->fireAlert($command, $commandEvent);
	}

	public function fireAlert(Command $command, CommandEvent $commandEvent):void
	{
		$this->carillon->play($command, $commandEvent);
	}

	public function isWatched(string $input):bool
	{
		foreach($this->commands as $command){
			if($command === strtolower($input)) return true;
		}
		return false;
	}
}