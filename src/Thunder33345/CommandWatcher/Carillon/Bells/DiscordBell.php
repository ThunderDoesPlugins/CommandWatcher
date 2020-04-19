<?php
declare(strict_types=1);
/** Created By Thunder33345 **/

namespace Thunder33345\CommandWatcher\Carillon\Bells;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\command\Command;
use pocketmine\event\server\CommandEvent;

class DiscordBell extends AbstractBell
{
	/** @var Webhook */
	private $webhook;
	private $title, $description;
	private $name, $avatar;

	public function onTest():bool
	{
		if(!class_exists(Webhook::class)) return false;
		$config = $this->getConfig();
		$link = $config['webhook'] ?? null;
		if(!is_string($link)) return false;
		$this->webhook = new Webhook($link);

		if(!$this->webhook->isValid()) return false;

		$this->title = $config['title'] ?? '';
		$this->description = $config['description'] ?? '';

		$this->name = $config['name'];
		$this->avatar = $config['avatar'];
		return true;
	}

	public function onRing(Command $command, CommandEvent $commandEvent):void
	{
		$webhook = $this->webhook;

		$title = $this->rawParseFormatMessage($this->title, $command, $commandEvent);
		$description = $this->rawParseFormatMessage($this->description, $command, $commandEvent);


		$embed = new Embed();
		$embed->setTitle($title);
		$embed->setDescription($description);
		$msg = new Message();
		$msg->addEmbed($embed);
		if(is_string($this->name))
			$msg->setUsername($this->name);
		if(is_string($this->avatar))
			$msg->setAvatarURL($this->avatar);
		$webhook->send($msg);
	}
}