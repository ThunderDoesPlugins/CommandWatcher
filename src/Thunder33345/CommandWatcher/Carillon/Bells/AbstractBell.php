<?php
declare(strict_types=1);
/** Created By Thunder33345 **/

namespace Thunder33345\CommandWatcher\Carillon\Bells;

use pocketmine\command\Command;
use pocketmine\event\server\CommandEvent;
use pocketmine\Server;
use Thunder33345\CommandWatcher\Carillon\Carillon;
use Thunder33345\CommandWatcher\CommandWatcher;

abstract class AbstractBell
{
	private const PREFIX_START = '{%';
	private const PREFIX_END = '}';
	//private const REGEX = self::PREFIX_START . '(.+?)' . self::PREFIX_END;
	private $carillon;
	private $config = [];
	private $message;
	private const DEFAULT_DATE = 'd/m/Y\TH:i:s(O)';

	public function __construct(Carillon $carillon, array $config)
	{
		$this->carillon = $carillon;
		$this->config = $config;
	}


	abstract public function onTest():bool;//for checking configs etc, false = disabling

	abstract public function onRing(Command $command, CommandEvent $commandEvent):void;

	protected function getConfig():array{ return $this->config; }

	public function getCarillon():Carillon{ return $this->carillon; }

	protected function getCommandWatcher():CommandWatcher{ return $this->carillon->getCommandWatcher(); }

	protected function getServer():Server{ return $this->carillon->getCommandWatcher()->getServer(); }

	protected function setFormatMessage(string $message):void{ $this->message = $message; }

	protected function getFormatMessage():?string{ return $this->message; }

	protected function parseFormatMessage(Command $command, CommandEvent $commandEvent):?string
	{
		$message = $this->getFormatMessage();
		if($message === null) return null;

		return $this->rawParseFormatMessage($this->message, $command, $commandEvent);
	}

	protected function rawParseFormatMessage(string $message, Command $command, CommandEvent $commandEvent):?string
	{
		$commandName = $command->getName();
		$commandFull = $commandEvent->getCommand();
		$senderName = $commandEvent->getSender()->getName();
		$groupName = $this->carillon->getName();

		$map = [
			'command.name' => $commandName,
			'command.full' => $commandFull,
			'sender.name' => $senderName,
			'group' => $groupName,
			'date' => date(($this->config['date'] ?? self::DEFAULT_DATE)),
		];
		$key = [];
		$value = [];
		foreach($map as $key_ => $value_){
			$key[] = self::PREFIX_START . $key_ . self::PREFIX_END;
			$value[] = $value_;
		}
		$formatted = str_replace($key, $value, $message);

		if(!is_string($formatted)) return null;

		return $formatted;
	}
}