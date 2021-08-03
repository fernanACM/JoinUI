<?php

namespace ACM\JoinACM;

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\sound\GhastShootSound;

use pocketmine\item\Item;

use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

use ACM\JoinACM\FormAPI\Form;
use ACM\JoinACM\FormAPI\SimpleForm;

use pocketmine\event\Listener;

use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        $this->getConfig = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $name = $player->getName();
        $event->setJoinMessage("");
        $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
        $player->getLevel()->addSound(new GhastShootSound($player));
        $this->openMyForm($player);
        $this->getServer()->broadcastMessage("§8[§r§a+§8]§r §a$name");
    }
    
    public function openMyForm($sender){
         $form = new SimpleForm(function (Player $sender, $data){
            $result = $data;
            if($result === null){
                return true;
            }
            switch($result){
                case 0:
                
                	$sender->addTitle($this->getConfig()->get("Join-Title"), $this->getConfig()->get("Join-SubTitle"));
					$this->getServer()->dispatchCommand($sender, "sa 26");
                
                break;
            }
        });
        $form->setTitle($this->getConfig()->get("JoinUI-Title"));
        $form->setContent($this->getConfig()->get("JoinUI-Content"));
        $form->addButton($this->getConfig()->get("JoinUI-Button"), $this->getConfig()->get("Img-Type"), $this->getConfig()->get("Img-Url"));
        $form->sendToPlayer($sender);
        return $form;
    }
    
    public function onQuit(PlayerQuitEvent  $event){
		$player = $event->getPlayer();
	    $name = $player->getName();
	    $event->setQuitMessage("");
	    $this->getServer()->broadcastMessage("§8[§r§c-§8]§r §c$name");
	}
}