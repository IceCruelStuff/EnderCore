<?php

namespace Core;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TE;

class Core extends PluginBase implements Listener {

	public $interactDelay = [];
	
	public function onEnable(){
		$this->getLogger()->info("§6===========================");
        $this->getLogger()->info("§aEnderCore por IvanCraft623");
        $this->getLogger()->info("§aLobbyCore para tu Server");
        $this->getLogger()->info("§aNo quites creditos :)");
        $this->getLogger()->info("§6===========================");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	
	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$event->setJoinMessage("");
		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		$player->setHealth(20);
		$player->setFood(20);
		$player->setGamemode(0);
		$player->setScale(1.0);
		$player->removeAllEffects(true);
		$player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
		$player->addTitle(TE::BOLD.TE::GREEN."Bienvenido a ". $this->getConfig()->get("Server"));
		$player->getInventory()->setItem(3, Item::get(288, 0, 1)->setCustomName(TE::AQUA."Fly"));
		$player->getInventory()->setItem(4, Item::get(467, 0, 1)->setCustomName(TE::GREEN."Juegos"));
		$player->getInventory()->setItem(5, Item::get(377, 0, 1)->setCustomName(TE::AQUA."Cosméticos"));
		$player->getInventory()->setItem(6, Item::get(340, 0, 1)->setCustomName(TE::GREEN."Información"));
		$player->getInventory()->setItem(2, Item::get(345, 0, 1)->setCustomName(TE::GREEN.$this->getConfig()->get("Brujula")));
		$this->getServer()->broadcastMessage($this->getConfig()->get("Prefix").TE::GOLD.$name.TE::GRAY." Se conecto al servidor");
	}
		
	public function onQuit(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$event->setQuitMessage("");
		$this->getServer()->broadcastMessage($this->getConfig()->get("Prefix").TE::GOLD.$name.TE::GRAY." Se desconecto del servidor");
	}
		
	public function onRespawn(PlayerRespawnEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		$player->getInventory()->setItem(3, Item::get(288, 0, 1)->setCustomName(TE::AQUA."Fly"));
	    $player->getInventory()->setItem(4, Item::get(467, 0, 1)->setCustomName(TE::GREEN."Juegos"));
	    $player->getInventory()->setItem(5, Item::get(377, 0, 1)->setCustomName(TE::AQUA."Cosméticos"));
	    $player->getInventory()->setItem(6, Item::get(340, 0, 1)->setCustomName(TE::GREEN."Información"));
		$player->getInventory()->setItem(2, Item::get(345, 0, 1)->setCustomName(TE::GREEN.$this->getConfig()->get("Brujula")));
	}
		
	public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$name = $player->getName();
		if ($event->getItem()->getId() == 288 and $event->getItem()->getCustomName() == TE::AQUA."Fly"){
			if ($player->hasPermission("fly.vuelo")){
				if ($player->getAllowFlight()){
					$player->sendMessage($this->getConfig()->get("Prefix").TE::RED."Vuelo Desactivado");
					$player->setFlying(false);
					$player->setAllowFlight(false);
				} else {
					$player->sendMessage($this->getConfig()->get("Prefix").TE::GREEN."Vuelo Activado");
					$player->setAllowFlight(true);
				}
			} else {
				$player->sendMessage($this->getConfig()->get("Prefix").TE::RED." No tienes permiso para volar");
			}
		}
		if ($event->getItem()->getId() == 467 and $event->getItem()->getCustomName() == TE::GREEN."Juegos"){
			if (!isset($this->interactDelay[$player->getName()])) {
				$this->interactDelay[$player->getName()] = time() + 1;
				$this->OpenJuegosUI($player);
			} else {
				if(time() >= $this->interactDelay[$player->getName()]){
					unset($this->interactDelay[$player->getName()]);
				}
			}
		}
		if ($event->getItem()->getId() == 345 and $event->getItem()->getCustomName() == TE::GREEN.$this->getConfig()->get("Brujula")){
            if (!isset($this->interactDelay[$player->getName()])) {
                $this->interactDelay[$player->getName()] = time() + 1;
                $commandbrujula = $this->getConfig()->get("Brujulla_Comando");
                $this->getServer()->dispatchCommand($player, $commandbrujula);
            } else {
                if(time() >= $this->interactDelay[$player->getName()]){
                    unset($this->interactDelay[$player->getName()]);
                }
            }
		}
		if ($event->getItem()->getId() == 377 and $event->getItem()->getCustomName() == TE::AQUA."Cosméticos"){
			if (!isset($this->interactDelay[$player->getName()])) {
				$this->interactDelay[$player->getName()] = time() + 1;
				$this->OpenCosmeticosUI($player);
			} else {
				if(time() >= $this->interactDelay[$player->getName()]){
					unset($this->interactDelay[$player->getName()]);
				}
			}
		}
		if ($event->getItem()->getId() == 340 and $event->getItem()->getCustomName() == TE::GREEN."Información"){
			if (!isset($this->interactDelay[$player->getName()])) {
				$this->interactDelay[$player->getName()] = time() + 1;
				$this->InfoUI($player);
			} else {
				if(time() >= $this->interactDelay[$player->getName()]){
					unset($this->interactDelay[$player->getName()]);
				}
			}
		}
	}

	public function OpenJuegosUI($sender){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, int $data = null) {
            $result = $data;
            if($result === null){
                return true;
            }             
            switch ($result) {
            	case 0:
                    $Juego1comando = $this->getConfig()->get("Juego1_comando");
            		$this->getServer()->dispatchCommand($sender, $Juego1comando);
            		break;
            	case 1:
            		$Juego2comando = $this->getConfig()->get("Juego2_comando");
                    $this->getServer()->dispatchCommand($sender, $Juego2comando);
            		break;
            	case 2:
            		$Juego3comando = $this->getConfig()->get("Juego3_comando");
                    $this->getServer()->dispatchCommand($sender, $Juego3comando);
            		break;
            	case 3:
            		$Juego4comando = $this->getConfig()->get("Juego4_comando");
                    $this->getServer()->dispatchCommand($sender, $Juego4comando);
            		break;
            	case 4:
            		$Juego5comando = $this->getConfig()->get("Juego5_comando");
                    $this->getServer()->dispatchCommand($sender, $Juego5comando);
            		break;
            	case 5:
            		$Juego6comando = $this->getConfig()->get("Juego6_comando");
                    $this->getServer()->dispatchCommand($sender, $Juego6comando);
            		break;
            }
        });
        $form->setTitle("§b§lJuegos");
        $form->addButton($this->getConfig()->get("Juego1").$this->getConfig()->get("Juego1_desc"));
        $form->addButton($this->getConfig()->get("Juego2").$this->getConfig()->get("Juego2_desc"));
        $form->addButton($this->getConfig()->get("Juego3").$this->getConfig()->get("Juego3_desc"));
        $form->addButton($this->getConfig()->get("Juego4").$this->getConfig()->get("Juego4_desc"));
        $form->addButton($this->getConfig()->get("Juego5").$this->getConfig()->get("Juego5_desc"));
        $form->addButton($this->getConfig()->get("Juego6").$this->getConfig()->get("Juego6_desc"));
        $form->sendToPlayer($sender);
    }
    public function OpenCosmeticosUI($sender){
      	$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, int $data = null) {
            $result = $data;
            if($result === null){
                return true;
            }             
            switch ($result) {
            	case 0:
            		$Cosmetico1comando = $this->getConfig()->get("Cosmetico1_comando");
                    $this->getServer()->dispatchCommand($sender, $Cosmetico1comando);
            		break;
            	case 1:
            		$Cosmetico2comando = $this->getConfig()->get("Cosmetico2_comando");
                    $this->getServer()->dispatchCommand($sender, $Cosmetico2comando);
            		break;
            	case 2:
            		$Cosmetico3comando = $this->getConfig()->get("Cosmetico3_comando");
                    $this->getServer()->dispatchCommand($sender, $Cosmetico3comando);
            		break;
            	case 3:
            		$this->SizeUI($sender);
            		break;
            	case 4:
            		$this->TiempoUI($sender);
            		break;
            }
        });
        $form->setTitle("§6§lCosméticos");
        $form->setContent("§fSelecciona un Cosmético");
        $form->addButton($this->getConfig()->get("Cosmetico1").$this->getConfig()->get("Cosmetico1_desc"));
        $form->addButton($this->getConfig()->get("Cosmetico2").$this->getConfig()->get("Cosmetico2_desc"));
        $form->addButton($this->getConfig()->get("Cosmetico3").$this->getConfig()->get("Cosmetico3_desc"));
        $form->addButton("§6§lSize\n§r§7Modifica tu tamaño");
        $form->addButton("§e§lHora\n§r§7Establece la hora del hub");
        $form->sendToPlayer($sender);
    }

    public function TiempoUI($sender){
      	$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, int $data = null) {
            $result = $data;
            if($result === null){
                return true;
            }             
            switch ($result) {
            	case 0:
            		if(!$sender->hasPermission("hora.poner")) {
            			$sender->sendMessage($this->getConfig()->get("Prefix").$this->getConfig()->get("No_Perm_Hora"));
            			return false;
            		}
            		$sender->getLevel()->setTime(0);
            		$sender->sendMessage($this->getConfig()->get("Prefix").TE::GREEN . "Has hecho el Lobby de:" . TE::YELLOW . " Dia");
            		break;
            	case 1:
            		if(!$sender->hasPermission("hora.poner")) {
            			$sender->sendMessage($this->getConfig()->get("Prefix").$this->getConfig()->get("No_Perm_Hora"));
            			return false;
            		}
            		$sender->getLevel()->setTime(14000);
            		$sender->sendMessage($this->getConfig()->get("Prefix").TE::GREEN . "Has hecho el Lobby de:" . TE::DARK_PURPLE . " Noche");
            		break;
            	case 2:
            		if(!$sender->hasPermission("hora.poner")) {
            			$sender->sendMessage($this->getConfig()->get("Prefix").$this->getConfig()->get("No_Perm_Hora"));
            			return false;
            		}
            		$sender->getLevel()->setTime(12200);
            		$sender->sendMessage($this->getConfig()->get("Prefix").TE::GREEN . "Has hecho el Lobby de:" . TE::GOLD . " Atardecer");
            		break;
            	case 3:
            		if(!$sender->hasPermission("hora.poner")) {
            			$sender->sendMessage($this->getConfig()->get("Prefix").$this->getConfig()->get("No_Perm_Hora"));
            			return false;
            		}
            		$sender->getLevel()->setTime(5500);
            		$sender->sendMessage($this->getConfig()->get("Prefix").TE::GREEN . "Has hecho el Lobby de:" . TE::AQUA . " Medio Día");
            		break;
            	case 4:
            		$this->OpenCosmeticosUI($sender);
            		break;
            }
        });
        $form->setTitle("§e§lHora");
        $form->setContent("§fSelecciona una hora");
        $form->addButton("§e§lDía\n§r§7Establece de Día");
        $form->addButton("§5§lNoche\n§r§7Establece de Noche");
        $form->addButton("§6§lAtardecer\n§r§7Establece un atardecer");
        $form->addButton("§b§lMedio Día\n§r§7Establece Medio día");
        $form->addButton("§c§lVolver");
        $form->sendToPlayer($sender);
    }

    public function SizeUI($sender){
      	$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, int $data = null) {
            $result = $data;
            if($result === null){
                return true;
            }             
            switch ($result) {
            	case 0:
            		if(!$sender->hasPermission("size.tamaño")) {
            			$sender->sendMessage($this->getConfig()->get("Prefix").$this->getConfig()->get("No_Perm_Size"));
            			return false;
            		}
            		$sender->setScale(0.7);
            		$sender->sendMessage($this->getConfig()->get("Prefix").TE::AQUA."Tu tamaño ahora es:".TE::BLUE." Pequeño");
            		break;
            	case 1:
            		if(!$sender->hasPermission("size.tamaño")) {
            			$sender->sendMessage($this->getConfig()->get("Prefix").$this->getConfig()->get("No_Perm_Size"));
            			return false;
            		}
            		$sender->setScale(1.0);
            		$sender->sendMessage($this->getConfig()->get("Prefix").TE::AQUA."Tu tamaño ahora es:".TE::BLUE." Normal");
            		break;
            	case 2:
            		if(!$sender->hasPermission("size.tamaño")) {
            			$sender->sendMessage($this->getConfig()->get("Prefix").$this->getConfig()->get("No_Perm_Size"));
            			return false;
            		}
            		$sender->setScale(1.5);
            		$sender->sendMessage($this->getConfig()->get("Prefix").TE::AQUA."Tu tamaño ahora es:".TE::BLUE." Grande");
            		break;
            	case 3:
            		$this->OpenCosmeticosUI($sender);
            		break;
            }
        });
        $form->setTitle("§6§lSize");
        $form->setContent("§fModifica tu tamaño");
        $form->addButton("§9§lPequeño\n§r§7Serás Pequeño");
        $form->addButton("§9§lNormal\n§r§7Serás Normal");
        $form->addButton("§9§lGrande\n§r§7Serás Grande");
        $form->addButton("§c§lVolver");
        $form->sendToPlayer($sender);
    }

    public function InfoUI($sender){
      	$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, int $data = null) {
        	$name = $sender->getName();
        	$rank = $this->getServer()->getPluginManager()->getPlugin("PurePerms")->getUserDataMgr($sender)->getGroup($sender);
            $result = $data;
            if($result === null){
                return true;
            }             
            switch ($result) {
            	case 0:
            		$this->ServerInfoUI($sender);
            		break;
            	case 1:
            		$sender->sendMessage(TE::GRAY."✖✖✖".TE::GREEN." PERFIL ".TE::GRAY."✖✖✖");
            		$sender->sendMessage(TE::RESET."\n");
            		$sender->sendMessage(TE::GREEN."Gamertag: ".TE::GRAY.$name);
            		$sender->sendMessage(TE::GREEN."Rango: ".TE::GRAY.$rank);
            		$sender->sendMessage(TE::GREEN."Ping: ".TE::GRAY.$sender->getPing());
            		$sender->sendMessage(TE::RESET."\n");
            		$sender->sendMessage(TE::GRAY."✖✖✖".TE::GREEN." PERFIL ".TE::GRAY."✖✖✖");
            		break;
            	case 2:
            		$this->RangosInfoUI($sender);
            		break;
            	case 3:
            		$this->ReglasInfoUI($sender);
            		break;
            	case 4:
            		$sender->sendMessage(TE::GRAY."✖✖✖".TE::GREEN." Redes Sociales ".TE::GRAY."✖✖✖");
            		$sender->sendMessage(TE::RESET."\n");
            		$sender->sendMessage($this->getConfig()->get("RedSocial1"));
            		$sender->sendMessage($this->getConfig()->get("RedSocial2"));
            		$sender->sendMessage(TE::RESET."\n");
            		$sender->sendMessage(TE::GRAY."✖✖✖".TE::GREEN." Redes Sociales ".TE::GRAY."✖✖✖");
            		break;
            }
        });
        $form->setTitle("§2§lInformación");
        $form->setContent("§fInformación del server y tu perfil");
        $form->addButton("§6§lServidor\n§r§7Toca para ver");
        $form->addButton("§2§lPerfil\n§r§7Toca para ver");
        $form->addButton("§5§lRangos\n§r§7Toca para ver");
        $form->addButton("§c§lReglas\n§r§7Toca para ver");
        $form->addButton("§9§lRedes Sociales\n§r§7Toca para ver");
        $form->sendToPlayer($sender);
    }

    public function ServerInfoUI($sender){
      	$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, int $data = null) {
            $result = $data;
            if($result === null){
                return true;
            }             
            switch ($result) {
            	case 0:
            		$this->InfoUI($sender);
            		break;
            }
        });
        $form->setTitle("§2§lServer Información");
        $form->setContent($this->getConfig()->get("ServerInfo"));
        $form->addButton("§c§lVolver");
        $form->sendToPlayer($sender);
    }

    public function RangosInfoUI($sender){
      	$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, int $data = null) {
            $result = $data;
            if($result === null){
                return true;
            }             
            switch ($result) {
            	case 0:
            		$this->YouTuberUI($sender);
            		break;
            	case 1:
            		$this->CompraRanksUI($sender);
            		break;
            	case 2:
            		$sender->sendMessage($this->getConfig()->get("Prefix").$this->getConfig()->get("StaffInfo"));
            		break;
            	case 3:
            		$this->InfoUI($sender);
            		break;
            }
        });
        $form->setTitle("§5§lRangos Información");
        $form->setContent("§fSelecciona una categoría");
        $form->addButton("§f§lYou§4Tube");
        $form->addButton("§6§lRangos de Compra");
        $form->addButton("§9§lStaff");
        $form->addButton("§c§lVolver");
        $form->sendToPlayer($sender);
    }

    public function YouTuberUI($sender){
      	$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, int $data = null) {
            $result = $data;
            if($result === null){
                return true;
            }             
            switch ($result) {
            	case 0:
            		$this->RangosInfoUI($sender);
            		break;
            }
        });
        $form->setTitle("§5§lRangos §f§lYou§4Tube");
        $form->setContent($this->getConfig()->get("YT_Info"));
        $form->addButton("§c§lVolver");
        $form->sendToPlayer($sender);
    }

    public function CompraRanksUI($sender){
      	$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, int $data = null) {
            $result = $data;
            if($result === null){
                return true;
            }             
            switch ($result) {
            	case 0:
            		$this->RangosInfoUI($sender);
            		break;
            }
        });
        $form->setTitle("§6§lRangos de Compra");
        $form->setContent($this->getConfig()->get("Buy_Ranks_Info"));
        $form->sendToPlayer($sender);
    }

    public function ReglasInfoUI($sender){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $sender, int $data = null) {
            $result = $data;
            if($result === null){
                return true;
            }             
            switch ($result) {
              case 0:
                $this->InfoUI($sender);
                break;
            }
        });
        $form->setTitle("§c§lReglas");
        $form->setContent($this->getConfig()->get("Reglas_Info"));
        $form->addButton("§c§lVolver");
        $form->sendToPlayer($sender);
    }
}
