<?php
declare(strict_types=1);
/** Created By Thunder33345 **/

namespace Thunder33345\CommandWatcher\Carillon;

use pocketmine\command\Command;
use pocketmine\event\server\CommandEvent;
use Thunder33345\CommandWatcher\Carillon\Bells\AbstractBell;
use Thunder33345\CommandWatcher\CommandWatcher;

class Carillon
{
	private $commandWatcher;
	private $name;
	/** @var AbstractBell[] $bells */
	private $bells = [];

	public function __construct(CommandWatcher $commandWatcher, string $name)
	{
		$this->commandWatcher = $commandWatcher;
		$this->name = $name;
	}

	public function addBell(AbstractBell $bell){ $this->bells[] = $bell; }

	public function removeBell(int $index){ unset($this->bells[$index]); }

	public function getAllBells(){ return $this->bells; }

	public function play(Command $command, CommandEvent $commandEvent):void
	{
		foreach($this->bells as $bell){
			$bell->onRing($command, $commandEvent);
		}
	}

	public function getName():string{ return $this->name; }

	public function getCommandWatcher():CommandWatcher{ return $this->commandWatcher; }
}