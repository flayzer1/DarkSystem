<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\utils;

abstract class Terminal{
	
	public static $FORMAT_BOLD = "";
	public static $FORMAT_OBFUSCATED = "";
	public static $FORMAT_ITALIC = "";
	public static $FORMAT_UNDERLINE = "";
	public static $FORMAT_STRIKETHROUGH = "";

	public static $FORMAT_RESET = "";

	public static $COLOR_BLACK = "";
	public static $COLOR_DARK_BLUE = "";
	public static $COLOR_DARK_GREEN = "";
	public static $COLOR_DARK_AQUA = "";
	public static $COLOR_DARK_RED = "";
	public static $COLOR_PURPLE = "";
	public static $COLOR_GOLD = "";
	public static $COLOR_GRAY = "";
	public static $COLOR_DARK_GRAY = "";
	public static $COLOR_BLUE = "";
	public static $COLOR_GREEN = "";
	public static $COLOR_AQUA = "";
	public static $COLOR_RED = "";
	public static $COLOR_LIGHT_PURPLE = "";
	public static $COLOR_YELLOW = "";
	public static $COLOR_WHITE = "";

	private static $formattingCodes = null;

	public static function hasFormattingCodes(){
		if(Terminal::$formattingCodes === null){
			$opts = getopt("", ["enable-ansi", "disable-ansi"]);
			if(isset($opts["disable-ansi"])){
				Terminal::$formattingCodes = false;
			}else{
				Terminal::$formattingCodes = ((Utils::getOS() !== "win" and getenv("TERM") != "" and (!function_exists("posix_ttyname") or !defined("STDOUT") or posix_ttyname(STDOUT) !== false)) or isset($opts["enable-ansi"]));
			}
		}

		return Terminal::$formattingCodes;
	}

	protected static function getFallbackEscapeCodes(){
		Terminal::$FORMAT_BOLD = "\x1b[1m";
		Terminal::$FORMAT_OBFUSCATED = "";
		Terminal::$FORMAT_ITALIC = "\x1b[3m";
		Terminal::$FORMAT_UNDERLINE = "\x1b[4m";
		Terminal::$FORMAT_STRIKETHROUGH = "\x1b[9m";

		Terminal::$FORMAT_RESET = "\x1b[m";

		Terminal::$COLOR_BLACK = "\x1b[38;5;16m";
		Terminal::$COLOR_DARK_BLUE = "\x1b[38;5;19m";
		Terminal::$COLOR_DARK_GREEN = "\x1b[38;5;34m";
		Terminal::$COLOR_DARK_AQUA = "\x1b[38;5;37m";
		Terminal::$COLOR_DARK_RED = "\x1b[38;5;124m";
		Terminal::$COLOR_PURPLE = "\x1b[38;5;127m";
		Terminal::$COLOR_GOLD = "\x1b[38;5;214m";
		Terminal::$COLOR_GRAY = "\x1b[38;5;145m";
		Terminal::$COLOR_DARK_GRAY = "\x1b[38;5;59m";
		Terminal::$COLOR_BLUE = "\x1b[38;5;63m";
		Terminal::$COLOR_GREEN = "\x1b[38;5;83m";
		Terminal::$COLOR_AQUA = "\x1b[38;5;87m";
		Terminal::$COLOR_RED = "\x1b[38;5;203m";
		Terminal::$COLOR_LIGHT_PURPLE = "\x1b[38;5;207m";
		Terminal::$COLOR_YELLOW = "\x1b[38;5;227m";
		Terminal::$COLOR_WHITE = "\x1b[38;5;231m";
	}

	protected static function getEscapeCodes(){
		Terminal::$FORMAT_BOLD = `tput bold`;
		Terminal::$FORMAT_OBFUSCATED = `tput smacs`;
		Terminal::$FORMAT_ITALIC = `tput sitm`;
		Terminal::$FORMAT_UNDERLINE = `tput smul`;
		Terminal::$FORMAT_STRIKETHROUGH = "\x1b[9m"; //`tput `;

		Terminal::$FORMAT_RESET = `tput sgr0`;

		$colors = (int) `tput colors`;
		if($colors > 8){
			Terminal::$COLOR_BLACK = $colors >= 256 ? `tput setaf 16` : `tput setaf 0`;
			Terminal::$COLOR_DARK_BLUE = $colors >= 256 ? `tput setaf 19` : `tput setaf 4`;
			Terminal::$COLOR_DARK_GREEN = $colors >= 256 ? `tput setaf 34` : `tput setaf 2`;
			Terminal::$COLOR_DARK_AQUA = $colors >= 256 ? `tput setaf 37` : `tput setaf 6`;
			Terminal::$COLOR_DARK_RED = $colors >= 256 ? `tput setaf 124` : `tput setaf 1`;
			Terminal::$COLOR_PURPLE = $colors >= 256 ? `tput setaf 127` : `tput setaf 5`;
			Terminal::$COLOR_GOLD = $colors >= 256 ? `tput setaf 214` : `tput setaf 3`;
			Terminal::$COLOR_GRAY = $colors >= 256 ? `tput setaf 145` : `tput setaf 7`;
			Terminal::$COLOR_DARK_GRAY = $colors >= 256 ? `tput setaf 59` : `tput setaf 8`;
			Terminal::$COLOR_BLUE = $colors >= 256 ? `tput setaf 63` : `tput setaf 12`;
			Terminal::$COLOR_GREEN = $colors >= 256 ? `tput setaf 83` : `tput setaf 10`;
			Terminal::$COLOR_AQUA = $colors >= 256 ? `tput setaf 87` : `tput setaf 14`;
			Terminal::$COLOR_RED = $colors >= 256 ? `tput setaf 203` : `tput setaf 9`;
			Terminal::$COLOR_LIGHT_PURPLE = $colors >= 256 ? `tput setaf 207` : `tput setaf 13`;
			Terminal::$COLOR_YELLOW = $colors >= 256 ? `tput setaf 227` : `tput setaf 11`;
			Terminal::$COLOR_WHITE = $colors >= 256 ? `tput setaf 231` : `tput setaf 15`;
		}else{
			Terminal::$COLOR_BLACK = Terminal::$COLOR_DARK_GRAY = `tput setaf 0`;
			Terminal::$COLOR_RED = Terminal::$COLOR_DARK_RED = `tput setaf 1`;
			Terminal::$COLOR_GREEN = Terminal::$COLOR_DARK_GREEN = `tput setaf 2`;
			Terminal::$COLOR_YELLOW = Terminal::$COLOR_GOLD = `tput setaf 3`;
			Terminal::$COLOR_BLUE = Terminal::$COLOR_DARK_BLUE = `tput setaf 4`;
			Terminal::$COLOR_LIGHT_PURPLE = Terminal::$COLOR_PURPLE = `tput setaf 5`;
			Terminal::$COLOR_AQUA = Terminal::$COLOR_DARK_AQUA = `tput setaf 6`;
			Terminal::$COLOR_GRAY = Terminal::$COLOR_WHITE = `tput setaf 7`;
		}
	}

	public static function init(){
		if(!Terminal::hasFormattingCodes()){
			return;
		}

		switch(Utils::getOS()){
			case "linux":
			case "mac":
			case "bsd":
				Terminal::getEscapeCodes();
				return;

			case "win":
			case "android":
				Terminal::getFallbackEscapeCodes();
				return;
		}
	}
}
