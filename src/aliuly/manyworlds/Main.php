<?php
/**
 **
 **/

namespace aliuly\manyworlds;

use aliuly\manyworlds\common\BasicHelp;
use aliuly\manyworlds\common\BasicPlugin;
use aliuly\manyworlds\common\mc;
use aliuly\manyworlds\common\MPMU;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

class Main extends BasicPlugin implements CommandExecutor{

	public function onEnable(){
		// We don't really need this...
		//if (!is_dir($this->getDataFolder())) mkdir($this->getDataFolder());
		mc::plugin_init($this, $this->getFile());

		$this->modules = [];
		foreach([
			"MwTp",
			"MwLs",
			"MwCreate",
			"MwGenLst",
			"MwLoader",
			"MwDefault",
		] as $mod){
			$mod = __NAMESPACE__ . "\\" . $mod;
			$this->modules[] = new $mod($this);
		}
		$this->modules[] = new BasicHelp($this);
	}

	public function autoLoad(CommandSender $c, $world){
		if($this->getServer()->isLevelLoaded($world)){
			return true;
		}
		if($c !== null && !MPMU::access($c, "mw.cmd.world.load")){
			return false;
		}
		if(!$this->getServer()->isLevelGenerated($world)){
			if($c !== null){
				$c->sendMessage(mc::_("[MW] No world with the name %1% exists!", $world));
			}

			return false;
		}
		$this->getServer()->loadLevel($world);

		return $this->getServer()->isLevelLoaded($world);
	}

	//////////////////////////////////////////////////////////////////////
	//
	// Command dispatcher
	//
	//////////////////////////////////////////////////////////////////////
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool{
		if($cmd->getName() !== "manyworlds"){
			return false;
		}

		return $this->dispatchSCmd($sender, $cmd, $args);
	}
}
