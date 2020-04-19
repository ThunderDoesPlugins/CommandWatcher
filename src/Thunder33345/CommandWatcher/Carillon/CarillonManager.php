<?php
declare(strict_types=1);
/** Created By Thunder33345 **/

namespace Thunder33345\CommandWatcher\Carillon;

use Exception;
use Thunder33345\CommandWatcher\Carillon\Bells\AbstractBell;
use Thunder33345\CommandWatcher\Carillon\Bells\BroadcastBell;
use Thunder33345\CommandWatcher\Carillon\Bells\DiscordBell;
use Thunder33345\CommandWatcher\Carillon\Bells\FileBell;
use Thunder33345\CommandWatcher\Carillon\Bells\LoggerBell;
use Thunder33345\CommandWatcher\CommandWatcher;

class CarillonManager
{
	private $commandWatcher;
	/** @var Carillon[] */
	private $carillons = [];

	public function __construct(CommandWatcher $commandWatcher)
	{
		$this->commandWatcher = $commandWatcher;
	}

	public function addCarillon(Carillon $carillon)
	{
		$this->carillons[$carillon->getName()] = $carillon;
	}

	public function getCarillon(string $name):?Carillon
	{
		return $this->carillons[$name] ?? null;
	}

	/**
	 * @return Carillon[]
	 */
	public function getAllCarillon():array{ return $this->carillons; }

	public function loadFrom(array $config)
	{
		foreach($config as $name => $data){
			$carillon = new Carillon($this->commandWatcher, $name);
			foreach($data as $key => $bellData){
				$typeName = $bellData['type'];
				$type = $this->getBellType($typeName);
				$bell = null;
				if(is_string($type))
					try{
						$bell = new $type($carillon, $bellData);
					} catch(Exception$exception){
					}
				if(!$bell instanceof AbstractBell){
					$this->commandWatcher->getLogger()->notice("Error Loading Channel $name, subchannel(#$key,$typeName): invalid type ($typeName)");
					continue;
				}
				if(!$bell->onTest()){
					$this->commandWatcher->getLogger()->notice("Error Loading Channel $name, subchannel(#$key,$typeName): subchannel rejected");
					continue;
				}
				$carillon->addBell($bell);
			}
			$this->carillons[$carillon->getName()] = $carillon;
		}
	}

	public function getBellType(string $type):?string
	{
		switch($type){
			case "logger":
				return LoggerBell::class;
			case "broadcast":
				return BroadcastBell::class;
			case "file":
				return FileBell::class;
			case "discord":
				return DiscordBell::class;
			default:
				return null;
		}
	}
}