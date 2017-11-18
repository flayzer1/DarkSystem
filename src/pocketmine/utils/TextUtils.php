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

class TextUtils{

    public static function center($input){
        $clear = TextFormat::clean($input);
        $lines = explode("\n", $clear);
        $max = max(array_map("strlen", $lines));
        $lines = explode("\n", $input);
        foreach($lines as $key => $line){
            $lines[$key] = str_pad($line, $max + TextUtils::colorCount($line), " ", STR_PAD_LEFT);
        }
        
        return implode("\n", $lines);
    }

    public static function colorCount($input){
        $colors = "abcdef0123456789klmnor";
        $count = 0;
        for($i = 0; $i < strlen($colors); $i++){
            $count += substr_count($input, "§". $colors{$i});
        }
        
        return $count;
    }
    
}
