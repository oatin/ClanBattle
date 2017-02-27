<?php

namespace BEcraft;

use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use pocketmine\permission\Permission;
use pocketmine\event\entity\EntityDamageEvent as D;
use pocketmine\event\entity\EntityDamageByEntityEvent as DB;
use pocketmine\event\player\PlayerJoinEvent as J;
use pocketmine\event\player\PlayerRespawnEvent as R;
use pocketmine\event\player\PlayerDeathEvent as DE;
use pocketmine\command\Command;
use pocketmine\math\Vector3;
use pocketmine\command\CommandSender;

class CBTask extends PluginTask{
	
	public function __construct(Yir $main){
		parent::__construct($main);
		$this->main = $main;
		}

public function onRun($tick){
	foreach($this->main->getServer()->getOnlinePlayers() as $players){
		$count = count($players);
		if($players->hasPermission("local.permiso")){
			$team = "§aLocal";
			}else{
				$team = "§cVisitante";
				}
		$players->sendPopup("§7-=]§eClan§6Battle§7[=-\n§7Players: §a".$count." §7Team: ".$team);
				if($this->main->cb == 1){
					if($players->isSurvival() and count($players->hasPermission("local.permiso") <= 0)){
						$this->main->getServer()->broadcastMessage("§bEl equipo §cVisitante §bgano el juego!");
						}else if($players->isSurvival() and count($players->hasPermission("visitante.permiso") <= 0)){
							$this->main->getServer()->broadcastMessage("§bEl equipo §aLocal §bgano el juego!");
							}
							$players->setGamemode(0);
							$this->main->getServer()->getScheduler()->cancelTask($this->getTaskId());
							$this->main->cb = 0;
							$this->main->scrim = 0;
							$this->main->running =0;
							$this->main->getServer()->broadcastMessage("§7El juego termino");
					}
}
	}
	}
	
class Yir extends PluginBase implements Listener{
	
	public $cb = 0;
	public $scrim = 0;
	public $running = 0;
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->notice("§b-=]§eClan§6Battle §eby §b@becraft_mcpe");
		}
		
		public function CBTask(){
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new CBTask($this), 15)->getTaskId();
		}
			
		public function onJoin(J $e){
			$player = $e->getPlayer();
			if($player->hasPermission("local.permiso")){
				$player->setNameTag("§7[§aLOCAL§7] §e".$player->getName());
				$player->sendMessage("§7te has unido al equipo §alocal");
				}else{
					$player->setNameTag("§7[§cVISITANTE§7] §e".$player->getName());
					$player->sendMessage("§7te has unido al equipo §cvisitantes");
					}
				$e->setJoinMessage("");
				$permiso = "visitante.permiso";
				$this->getServer()->broadcastMessage("§e".$player->getName()." §7se ha unido al juego");
				$player->setGamemode(0);
				$player->getInventory()->clearAll();
				if($player->hasPermission("local.permiso")){
					}else{
						$player->addAttachment($this, $permiso, true);
						$player->sendMessage("§7Tienes el permiso: §a".$permiso."§7 si cambias de permiso en juego por favor has relog");
						}
					$inv = $player->getInventory();
						$inv->setHelmet(Item::get(Item::DIAMOND_HELMET, 0, 1));
						$inv->setChestplate(Item::get(Item::DIAMOND_CHESTPLATE, 0, 1));
						$inv->setLeggings(Item::get(Item::DIAMOND_LEGGINGS, 0, 1));
						$inv->setBoots(Item::get(Item::DIAMOND_BOOTS, 0, 1));
						$comida = Item::get(320, 0, 34);
						$espada = Item::get(Item::DIAMOND_SWORD, 0, 1);
						$inv->setItem(5, $comida);
						$inv->setItem(6, $espada);
			}
		
		public function onDeath(DE $e){
			$player = $e->getPlayer();
			if($this->cb == 1){
				$player->setGamemode(3);
				}else{
					$player->setGamemode(0);
					}
			if($this->scrim == 1){
				$pos = new Vector3($player->x, $player->y, $player->z);
				$player->setSpawn($pos);
				}else{
					}
				if($e->getEntity() instanceof Player){
					$e->setDrops([]);
					}
			}
		
		public function onRepawn(R $e){
			$player = $e->getPlayer();
			if($this->scrim == 1){
				$inv = $player->getInventory();
						$inv->setHelmet(Item::get(Item::DIAMOND_HELMET, 0, 1));
						$inv->setChestplate(Item::get(Item::DIAMOND_CHESTPLATE, 0, 1));
						$inv->setLeggings(Item::get(Item::DIAMOND_LEGGINGS, 0, 1));
						$inv->setBoots(Item::get(Item::DIAMOND_BOOTS, 0, 1));
						$comida = Item::get(320, 0, 34);
						$espada = Item::get(Item::DIAMOND_SWORD, 0, 1);
						$inv->setItem(5, $comida);
						$inv->setItem(6, $espada);
				}else{
					}
			}
		
		public function onDamage(D $e){
			if($e instanceof DB){
				$killer = $e->getDamager()->getNameTag();
				$player = $e->getEntity()->getNameTag();
				if((strpos($player, "§7[§aLOCAL§7] §e") !== false) && (strpos($killer, "§7[§aLOCAL§7] §e") !== false) && $this->scrim == 0){
					$e->setCancelled();
					}
				if((strpos($player, "§7[§cVISITANTE§7] §e") !== false) && (strpos($killer, "§7[§cVISITANTE§7] §e") !== false) && $this->scrim == 0){
					$e->setCancelled();
					}
				}
			}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
	switch($command->getName()){
		case "cb":
		if($sender->isOp()){
			if(isset($args[0])){
				if($args[0] == "start"){
					if($this->cb == 0){
						$this->getServer()->broadcastMessage("§7El juego comenzara en §a5 §7segundos...");
						Sleep(5);
						$this->CBTask();
						foreach($this->getServer()->getOnlinePlayers() as $players){
						$inv = $players->getInventory();
						$inv->setHelmet(Item::get(Item::DIAMOND_HELMET, 0, 1));
						$inv->setChestplate(Item::get(Item::DIAMOND_CHESTPLATE, 0, 1));
						$inv->setLeggings(Item::get(Item::DIAMOND_LEGGINGS, 0, 1));
						$inv->setBoots(Item::get(Item::DIAMOND_BOOTS, 0, 1));
						$comida = Item::get(320, 0, 34);
						$espada = Item::get(Item::DIAMOND_SWORD, 0, 1);
						$inv->setItem(5, $comida);
						$inv->setItem(6, $espada);
						$this->getServer()->broadcastMessage("§7[§eClan§6Battle§7] ha comenzado...");
						$this->cb = 1;
						$this->running = 1;
						return true;
						}
						}else{$sender->sendMessage("§cEl juego ya empezó...");}
					}
				}else{$sender->sendMessage("§7use §e/cb start §7para comenzar la batalla");}
			}else{$sender->sendMessage("§cno tienes permiso para este comando...");}
			return true;
			break;
			
	case "stopbattle":
	if($sender->isOp()){
		foreach($this->getServer()->getOnlinePlayers() as $pl){
		$this->cb = 0;
		$this->scrim = 0;
		$pl->sendMessage("§7El juego ha sido parado...");
		$pl->getInventory()->clearAll();
		$pl->setFood(20);
		$pl->setHealth(20);
		$pl->setGamemode(0);
		$pl->teleport(new Vector3($sender->x, $sender->y, $sender->z));
		if($this->running == 1){
		$this->getServer()->getScheduler()->cancelTask($this->getTaskId());
		}
		return true;
		}
		}else{$sender->sendMessage("§cno tienes permiso para este comando");}
		return true;
		break;
		
		
	case "scrim":
	if($sender->isOp()){
		if(isset($args[0])){
			if($args[0] == "start"){
				if($this->scrim == 0){
		$this->cb = 0;
		$this->getServer()->broadcastMessage("§7[§aScrim§7] comenzara en §a5 §7segundos...");
		Sleep(5);
		$this->getServer()->broadcastMessage("§7Juego de entrenamiento ha comenzado");
		foreach($this->getServer()->getOnlinePlayers() as $players){
			$inv = $players->getInventory();
						$inv->setHelmet(Item::get(Item::DIAMOND_HELMET, 0, 1));
						$inv->setChestplate(Item::get(Item::DIAMOND_CHESTPLATE, 0, 1));
						$inv->setLeggings(Item::get(Item::DIAMOND_LEGGINGS, 0, 1));
						$inv->setBoots(Item::get(Item::DIAMOND_BOOTS, 0, 1));
						$comida = Item::get(320, 0, 34);
						$espada = Item::get(Item::DIAMOND_SWORD, 0, 1);
						$inv->setItem(5, $comida);
						$inv->setItem(6, $espada);
						return true;
			}
			}else{$sender->sendMessage("§cEl juego ya empezó...");}
			}
			}else{$sender->sendMessage("§7use §e/scrim start §7para comenzar el entrenamiento");}
			}else{$sender->sendMessage("§cno tienes permiso para este comando...");}
	return true;
	break;
	
		}
}
		
	}//final