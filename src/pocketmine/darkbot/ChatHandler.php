<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\darkbot;

use pocketmine\Server;
use pocketmine\Player;

class ChatHandler{
	
	public function __construct(Server $server){
		$server = $server;
	}
	
	public function check($player, $message){
		$server = Server::getInstance();
		$dbotprefix = $server->getDarkBotPrefix();
		$msg = "§aSizin İsteğiniz Benim İçin Bir Emirdir!";
		$operators = ["DarkYusuf13", "calci123", "BluishCoot11", "Kayra_OyunYT"]; //You can add operators of your server here
		if($server->getName() == "DarkSystem"){
			switch($message){
				/* English */
				case "darkbot hi":
				case "#hi":
				$result = $dbotprefix . "Hi!";
				break;
				case "darkbot how are you":
				case "#how are you":
				$result = $dbotprefix . "I am fine, thanks!";
				break;
				case "darkbot what are you doing":
				case "#what are you doing":
				$result = $dbotprefix . "Nothing.";
				break;
				/* Korean */
				case "안녕":
				case "darkbot 안녕":
				$result = $dbotprefix . "안녕";
				break;
				case "darkbot 잘 지냈어요":
				case "darkbot 너는 어떻게 지내니":
				case "darkbot 안녕하십니까":
				case "darkbot 스폰으로 이동 하였습니다":
				$result = $dbotprefix . "난 괜찮아";
				break;
				/* Turkish */
				case "dbot gm1":
				case "gm1 dbot":
				case "darkbot gm1":
				case "gm1 darkbot":
				case "darkbot gm1 ver":
				case "darkbot bana gm1 ver":
				if(/*$server->getDarkBot()->check() = "§aAktif" && */$this->isOp() || in_array($this->getName(), $operators)){
					$this->setGamemode(1);
					$result = $dbotprefix . $msg;
				}
				//return true;
				break;
				case "dbot gm0":
				case "gm0 dbot":
				case "darkbot gm0":
				case "gm0 darkbot":
				case "darkbot gm0 ver":
				case "darkbot bana gm0 ver":
				if(/*$server->getDarkBot()->check() = "§aAktif" && */$this->isOp() || in_array($this->getName(), $operators)){
					$this->setGamemode(0);
					$result = $dbotprefix . $msg;
				}
				//return true;
				break;
				case "dbot spawn":
				case "spawn dbot":
				case "darkbot spawn":
				case "spawn darkbot":
				case "darkbot spawna ışınla":
				case "darkbot beni spawna ışınla":
				$this->teleport($server->getDefaultLevel()->getSafeSpawn());
				$result = $dbotprefix . $msg;
				//return true;
				break;
				case "dbot kill":
				case "kill dbot":
				case "darkbot kill":
				case "kill darkbot":
				case "darkbot kill çek":
				case "darkbot bana kill çek":
				case "darkbot beni öldür":
				if(/*$server->getDarkBot()->check() = "§aAktif" && */$this->isOp() || in_array($this->getName(), $operators)){
					$this->kill();
					$result = $dbotprefix . $msg;
				}
				//return true;
				break;
				case "dbot sabah yap":
				case "darkbot sabah yap":
				case "sabah yap dbot":
				case "sabah yap darkbot":
				if(/*$server->getDarkBot()->check() = "§aAktif" && */$this->isOp() || in_array($this->getName(), $operators)){
					$this->level->setTime(3000);
					$result = $dbotprefix . $msg;
				}
				break;
				case "dbot akşam yap":
				case "darkbot akşam yap":
				case "akşam yap dbot":
				case "akşam yap darkbot":
				if(/*$server->getDarkBot()->check() = "§aAktif" && */$this->isOp() || in_array($this->getName(), $operators)){
					$this->level->setTime(14000);
					$result = $dbotprefix . $msg;
				}
				break;
				case "dbot ddos at":
				case "ddos at dbot":
				case "darkbot ddos":
				case "ddos darkbot":
				case "darkbot ddos at":
				case "darkbot dos at":
				if(/*$server->getDarkBot()->check() = "§aAktif" && */$this->isOp() || in_array($this->getName(), $operators)){
					/*BUGGY*/
					$result = $dbotprefix . " ";
					$result = $dbotprefix . "§aSunucu DDoS İçin Hazırlanıyor...";
					$result = $dbotprefix . " ";
					$result = $dbotprefix . "§aBağlantı Hızlandırılıyor...";
					$result = $dbotprefix . " ";
					$result = $dbotprefix . "§aIP Tespit Ediliyor...";
					$result = $dbotprefix . " ";
					$result = $dbotprefix . " ";
					$result = $dbotprefix . "§aHedef IP Belirlendi.";
					$result = $dbotprefix . "§6DDoSing.";
					$result = $dbotprefix . "§6DDoSing..";
					$result = $dbotprefix . "§6DDoSing...";
					$result = $dbotprefix . "§6DDoSing.";
					$result = $dbotprefix . "§6DDoSing..";
					$result = $dbotprefix . "§aDDoS Başarılı!";
				}
				//return true;
				break;
				//$efendim = 0;
				case "dbot":
				case "Dbot":
				case "darkbot":
				case "Darkbot":
				case "dark bot":
				case "Dark bot":
				case "#dbot":
				case "#Dbot":
				case "#darkbot":
				case "#Darkbot":
				case "#dark bot":
				case "#Dark bot":
				$result = $dbotprefix . "§aEfendim?";
				/*++$efendim;
				if($efendim > 1){
					$result = $dbotprefix . "§aLan ne Vaaarrr?!?";
				}*/
				break;
				case "#sana demedim":
				case "#sana demedim dbot":
				case "#sana demedim darkbot":
				$result = $dbotprefix . "§aHa, Tamam.";
				break;
				case "dbot naber":
				case "darkbot naber":
				case "dbot nasılsın":
				case "darkbot nasılsın":
				case "dbot nasilsin":
				case "darkbot nasilsin":
				case "#naber":
				case "#darkbot naber":
				$result = $dbotprefix . "§aİyiyim, Peki ya Sen?";
				break;
				case "dbot sen delisin":
				case "darkbot sen delisin":
				case "#sen delisin":
				case "#darkbot sen delisin":
				$result = $dbotprefix . "§aDeli sensin!";
				break;
				case "dbot iyimisin":
				case "dbot iyi misin":
				case "darkbot iyimisin":
				case "darkbot iyi misin":
				case "#iyimisin":
				case "#iyi misin":
				case "#darkbot iyimisin":
				case "#darkbot iyi misin":
				$result = $dbotprefix . "§aEvet, Peki Ya sen?";
				break;
				case "bende iyiyim dbot":
				case "bende iyiyim darkbot":
				case "#bende":
				case "#bnde":
				case "#bende iyiyim":
				case "#bende iyiyim dbot":
				case "#bende iyiyim darkbot":
				$result = $dbotprefix . "§aBuna Sevindim.";
				break;
				case "alım varmı":
				case "alım var mı":
				case "alim varmi":
				case "alim var mi":
				case "alım varmi":
				case "alım var mi":
				case "alım varmı":
				case "alım var mı":
				case "yetki ver":
				case "yetki verirmisin":
				case "yetki verir misin":
				case "#alım varmı":
				case "#alım var mı":
				case "#alim varmi":
				case "#alim var mi":
				case "#alım varmi":
				case "#alım var mi":
				case "#alım varmı":
				case "#alım var mı":
				case "#yetki ver":
				case "#yetki verirmisin":
				case "#yetki verir misin":
				$result = $dbotprefix . "§aHayır.";
				break;
				case "darkbot canım sıkıldı":
				case "canım sıkıldı darkbot":
				case "#canım sıkıldı":
				case "#darkbot canım sıkıldı":
				case "#canım sıkıldı darkbot":
				$result = $dbotprefix . "§aBenim de. Hadi Sohbet Edelim!";
				break;
				case "#ok":
				case "#OK":
				case "#tmm":
				case "#tamam":
				$result = $dbotprefix . "§aTamam.";
				break;
				case "#easter":
				$result = $dbotprefix . "§aNe diyon olm";
				break;
				case "darkbot günaydın":
				case "günaydın darkbot":
				case "#günaydın":
				case "#darkbot günaydın":
				case "#günaydın darkbot":
				$result = $dbotprefix . "§aSanada.";
				break;
				case "darkbot by":
				case "darkbot bb":
				case "#by":
				case "#bb":
				$result = $dbotprefix . "§aBay, Görüşürüz!";
				break;
				case "darkbot iyi akşamlar":
				case "iyi akşamlar darkbot":
				case "#iyi akşamlar":
				case "#darkbot iyi akşamlar":
				case "#iyi akşamlar darkbot":
				$result = $dbotprefix . "§aSanada İyi Akşamlar!";
				break;
				case "darkbot senin sürümün kaç":
				case "darkbot senin sürümün kaç?":
				case "senin sürümün kaç darkbot":
				case "senin sürümün kaç darkbot?":
				case "#senin sürümün kaç":
				case "#senin sürümün kaç?":
				$result = $dbotprefix . TF::GREEN . $server->getDarkBotVersion();
				break;
				case "darkbot seni kim yaptı":
				case "darkbot seni kim yaptı?":
				case "seni kim yaptı darkbot":
				case "seni kim yaptı darkbot?":
				case "#seni kim yaptı":
				case "#seni kim yaptı?":
				$result = $dbotprefix . "§9DarkYusuf13!";
				break;
				case "darkbot sen kimsin":
				case "darkbot sen nesin":
				case "#sen kimsin":
				case "#sen nesin":
				$result = $dbotprefix . "§aBen Yapay Bir Zekaya Sahip, DarkSystem'i Ve Onun Sunucularını Korumak İçin Kodlanmış Sanal Bir Robotum.";
				break;
				case "darkbot napıyon":
				case "darkbot napıyosun":
				case "darkbot napıyorsun":
				case "#darkbot napıyon":
				case "#darkbot napıyosun":
				case "#darkbot napıyorsun":
				$result = $dbotprefix . "§aHiiç, Ne Olsun.";
				break;
				case "darkbot yardım et":
				case "darkbot yardımet":
				case "darkbot yardim et":
				case "darkbot yardimet":
				case "#darkbot yardım et":
				case "#darkbot yardımet":
				case "#darkbot yardim et":
				case "#darkbot yardimet":
				$result = $dbotprefix . "§aNoldu?!";
				break;
				case "#elinin körü":
				case "#elinin körü :D":
				$result = $dbotprefix . "§a.d";
				break;
				case "darkbot gul":
				case "darkbot gül":
				case "#darkbot gul":
				case "#darkbot gül":
				$result = $dbotprefix . "§aMuhaaaahaaaa";
				break;
				//$sent = false;
				case "sa":
				case "s.a":
				case "s.a.":
				case "s,a":
				case "s,a,":
				case "sea":
				case "#sa":
				case "#s.a":
				case "#s.a.":
				case "#s,a":
				case "#s,a,":
				case "#sea":
				/*$sent = true;
				if($sent = true){
					$result = $dbotprefix . "§aAleyküm Selam";
				}*/
				if($player->getName() == "BluishCoot11"){ //For something
					break;
				}
				//$result = $dbotprefix . "§aAleyküm Selam";
				switch(mt_rand(1, 3)){
					case 1:
					$result = $dbotprefix . "§aAleyküm Selam";
					break;
					case 2:
					$result = $dbotprefix . "§aAleyküm Selam, Hoş Geldin";
					break;
					case 3:
					$result = $dbotprefix . "§aas";
					break;
					default;
					break;
				}
				break;
				case "darkbot espri":
				case "darkbot espiri":
				case "darkbot espri yap":
				case "darkbot espiri yap":
				case "darkbot bir espri yap":
				case "darkbot bir espiri yap":
				case "#espri":
				case "#espiri":
				case "#espri yap":
				case "#espiri yap":
				case "#bir espri yap":
				case "#bir espiri yap":
				case "#darkbot espri":
				case "#darkbot espiri":
				case "#darkbot espri yap":
				case "#darkbot espiri yap":
				case "#darkbot bir espri yap":
				case "#darkbot bir espiri yap":
				switch(mt_rand(1, 11)){
					case 1:
					$result = $dbotprefix . "§aTamam Madem Çok İstedin: Adamın Biri Varmış İkinci Dönem Düzeltmiş :D :D Hadi Gülün";
					break;
					case 2:
					$result = $dbotprefix . "§aSize Deniz Anası Taklidi Yapayım mı? Deniiiiz Gel Kızım Geeel.. :D sjsjjs (Robot Olmama Rağmen Gülüyorum Sizde Gülün :D)";
					break;
					case 3:
					$result = $dbotprefix . "§aHmm, Aklıma Gelmedi Sonra Tekrar Sor.";
					break;
					case 4:
					$result = $dbotprefix . "§aTemel Ayakkabıcıya Gitmiş Ayakkabıcı Sıkıyosa Alma Demiş Temel de Korkmuş Almış :D :D";
					break;
					case 5:
					$result = $dbotprefix . "§aKral Tahta Çıkınca Ne Yapmış? Tahtayı Yerine Çakmış! :D :D";
					break;
					case 6:
					$result = $dbotprefix . "§aTamam Yapıyorum, Kaç Derece Olsun? :D :D";
					break;
					case 7:
					$result = $dbotprefix . "§aSaat Niçin Tehlikelidir? Çünkü Akrebi Var!! :D :D sjjsjshajaj";
					break;
					case 8:
					$result = $dbotprefix . "§aAdamın Biri Gülmüş, Bahçeye Dikmişler :D";
					break;
					case 9:
					$result = $dbotprefix . "§aYılın Haberi: Mezardan Ceset Çıktı!!! Şok Şok Şok! İlginç";
					break;
					case 10:
					$result = $dbotprefix . "§aAklıma Gelmedi :/";
					break;
					case 11:
					/*BUGGY*/
					$result = $dbotprefix . "§aTemel ile Dursun Anaokulunda Çıkan Yangındaki Çocukları Kurtarıyormuş. Dursun Bir Çocuğu Atmış, Temel de Tutmuş, Başka Bir Tane Atmış Yine Tutmuş.";
					$result = $dbotprefix . "§aBu Sefer Zenci Bir Tane Atmış Temel Tutmamış. Sebebini Sorunca: Yanmışları Atma Zaman Kaybediyoruz Demiş Muahaaa";
					break;
					default;
					$result = "";
					break;
					return $result;
				}
				default;
				$result = "";
				break;
				return $result;
			}
		}
	}
}
