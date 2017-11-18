<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace darksystem;

use pocketmine\Server;

class ThemeManager{
	
	public $availableThemes = [
		"classic",
		"dark",
		"light",
		"metal",
		"energy",
		"uranium"
	];
	
	const DEFAULT_THEME = "classic";
	
	public function __construct(Server $server){
		$this->server = $server;
	}
	
	public function getTheme(){
		return "uranium"; //Force theme
		$configTheme = $this->server->getConfigString("theme", ThemeManager::DEFAULT_THEME);
		/*if($this->server->getConfigInt("random-theme", "false")){
		    return $this->availableThemes[array_rand($this->availableThemes)];
		}*/
		
		if($this->server->getConfigInt("colorful-theme", "false")){
		    return $this->availableThemes[array_rand($this->availableThemes)];
		}
		
		if($configTheme === null){
			return false;
		}
		
		return $configTheme;
    }
    
    public function setTheme($value){
    	if(!in_array($value, $this->availableThemes)){
    	    return false;
    	}
    
    	if($value == $this->getTheme()){
    	    return false;
    	}
    
		$this->server->setConfigString("theme", $value);
    }
    
    public function getDefaultTheme(){
    	return ThemeManager::DEFAULT_THEME;
    }
    
    public function getLogoTheme($dbotcheck, $dbotver, $version, $mcpe, $protocol, $build, $tag, $splash){
    	switch($this->getTheme()){
    	    case "darkness":
    	    $this->setTheme(ThemeManager::DEFAULT_THEME);
			return "
			
	§dYOU §aFOUND §eAN §cEASTER §6EGG §3LOL :D
	
			";
			break;
			case "classic":
			return "
			
    §d______           _    _____           _                  
    §5|  _  \         | |  /  ___|         | |                  
    §6| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
    §5| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
    §d| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
    §6|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
                                 §5__/  |      
                                 §d|___/            §eMCPE: $mcpe §a($protocol)
                                                      §eDARKBOT: $dbotcheck (v$dbotver)
      $splash
                                      
      §bDarkSystem $version ($build)  *$tag*
      
			";
			break;
			case "dark":
			return "
			
    §7______           _    _____           _                  
    §8|  _  \         | |  /  ___|         | |                  
    §6| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
    §7| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
    §8| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
    §3|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
                                 §7__/  |      
                                 §8|___/            §6MCPE: $mcpe §2($protocol)
                                                      §6DARKBOT: $dbotcheck (v$dbotver)
      $splash
                                      
      §9DarkSystem $version ($build)  *$tag*
      
			";
			break;
			case "light":
			return "
			
    §f______           _    _____           _                  
    §f|  _  \         | |  /  ___|         | |                  
    §f| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
    §f| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
    §f| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
    §f|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
                                 §f__/  |      
                                 §f|___/            §bMCPE: $mcpe §e($protocol)
                                                      §bDARKBOT: $dbotcheck (v$dbotver)
      $splash
                                      
      §fDarkSystem $version ($build)  *$tag*
      
			";
			break;
			case "metal":
			return "
			
    §f______           _    _____           _                  
    §7|  _  \         | |  /  ___|         | |                  
    §f| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
    §f| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
    §7| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
    §f|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
                                 §f__/  |      
                                 §f|___/            §bMCPE: $mcpe §e($protocol)
                                                      §bDARKBOT: $dbotcheck (v$dbotver)
      $splash
                                      
      §dDarkSystem $version ($build)  *$tag*
      
			";
			break;
			case "energy":
			return "
			
    §f______           _    _____           _                  
    §e|  _  \         | |  /  ___|         | |                  
    §f| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
    §e| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
    §f| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
    §e|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
                                 §f__/  |      
                                 §e|___/            §aMCPE: $mcpe §b($protocol)
                                                      §aDARKBOT: $dbotcheck (v$dbotver)
      $splash
                                      
      §eDarkSystem $version ($build)  *$tag*
      
			";
			break;
			case "uranium":
			return "
			
    §f______           _    _____           _                  
    §7|  _  \         | |  /  ___|         | |                  
    §a| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
    §f| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
    §7| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
    §a|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
                                 §f__/  |      
                                 §7|___/            §eMCPE: $mcpe §b($protocol)
                                                      §eDARKBOT: $dbotcheck (v$dbotver)
      $splash
                                      
      §aDarkSystem $version ($build)  *$tag*
      
			";
			break;
			default;
			return "
			
    §d______           _    _____           _                  
    §5|  _  \         | |  /  ___|         | |                  
    §6| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
    §5| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
    §d| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
    §6|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
                                 §5__/  |      
                                 §d|___/            §eMCPE: $mcpe §a($protocol)
                                                      §eDARKBOT: $dbotcheck (v$dbotver)
      $splash
                                      
      §bDarkSystem $version ($build)  *$tag*
      
			";
			break;
		}
    }   
}
