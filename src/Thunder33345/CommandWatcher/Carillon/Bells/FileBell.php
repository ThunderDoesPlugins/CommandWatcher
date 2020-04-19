<?php
declare(strict_types=1);
/** Created By Thunder33345 **/

namespace Thunder33345\CommandWatcher\Carillon\Bells;

use pocketmine\command\Command;
use pocketmine\event\server\CommandEvent;

class FileBell extends AbstractBell
{
	private $path, $filename, $prepend;

	public function onTest():bool
	{
		$config = $this->getConfig();
		$path = $config['path'] ?? null;
		if(!is_string($path)) return false;;
		if(substr($path, 0, 1) === '$')
			$path = $this->getServer()->getDataPath() . substr($path, 1);

		@mkdir($path, 0777, true);
		$dirPath = realpath($path);
		if($dirPath === false){
			return false;
		}
		$this->path = $dirPath . DIRECTORY_SEPARATOR;

		$filename = $config['filename'] ?? null;
		if(!is_string($filename)) return false;
		$this->filename = $filename;

		if(!touch($path . $filename)) return false;

		$this->prepend = (bool)($config['prepend'] ?? true);

		$msg = $config['message'] ?? null;
		if(!is_string($msg)) return false;
		$this->setFormatMessage($msg);
		return true;
	}

	public function onRing(Command $command, CommandEvent $commandEvent):void
	{
		$message = $this->parseFormatMessage($command, $commandEvent);
		if($message === null) return;
		$this->writeTo($message);
	}

	public function writeTo(string $message)
	{
		if($this->prepend){
			$file = file_get_contents($this->path . $this->filename);
			file_put_contents($this->path . $this->filename, $message . PHP_EOL . $file);
		} else
			file_put_contents(PHP_EOL . $this->path . $this->filename, $message, FILE_APPEND);
	}
}