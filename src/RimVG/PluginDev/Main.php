<?php

namespace RimVG\PluginDev;

use pocketmine\Server;
use RimVG\wreg\WregTool;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use pocketmine\world\Position;
use pocketmine\command\{
  Command,
  CommandSender
};

use pocketmine\utils\{
	Config,
	TextFormat as TF
};

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

use ReflectionClass;
class Main extends PluginBase implements Listener {
  #// You should not delete and move or replace this top because it will error and crash! I expected that!!!   //#
  private ?string $info = null;
  private Config $config;
  private string $notPlayer = "No One Here, Do It On The Server!";
  
  
  public function onEnable() : void {
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->saveResource("config.yml");
    $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
    $this->getLogger()->notice(TF::AQUA . TF::BOLD . "RSocialUI" . TF::RESET . TF::GREEN . "RSocialUI runEnable!");
  }
  
  public static function soundPacket(Player $player, string $sound, int|float $volume = 1, int|float $pitch = 1) {
    	$sounds = new PlaySoundPacket();
		$sounds->x = $player->getPosition()->getX();
		$sounds->y = $player->getPosition()->getY();
		$sounds->z = $player->getPosition()->getZ();
		$sounds->soundName = $sound;
		$sounds->volume = 1;
		$sounds->pitch = 1;
		$player->getNetworkSession()->sendDataPacket($sounds);
  }
  
  public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
    if ($cmd->getName() === "rcui"){
      if ($sender instanceof Player){
        $this->openForm($sender);
        self::soundPacket($sender, "note.harp", 1, 1);
        return true;
      } else {
        $sender->sendMessage($this->notPlayer);
        return true;
      }
    }
    return false;
  }
  
  public function openForm(Player $player){
    $form = new SimpleForm(
      function($player, int $data = null){
        if ($data == null) return true;
        
        switch($data) {
        	case 0:
                 $player->sendMessage($this->config->get("message-close"));
                 self::soundPacket($player, "random.pop", 1, 1);
                 return true;
            break;
        }
      });
      
      $form->setTitle($this->config->get("title"));
      $form->setContent(str_replace("{player}", $player->getName(), $this->config->get("content")));
      $form->addButton($this->config->get("button-close"), 0, 'textures/ui/realms_red_x');
      $form->sendToPlayer($player);
      return $form;
  }
}
