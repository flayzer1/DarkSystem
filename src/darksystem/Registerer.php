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
use pocketmine\darkbot\entity\NPC;
use pocketmine\entity\{Entity, Arrow, BlazeFireball, BlueWitherSkull, Boat, Camera, Car, Item as DroppedItem, Egg, EnderCrystal, EnderPearl, FallingSand, FishingHook, FloatingText, GhastFireball, LeashKnot, Lightning, Minecart, MinecartChest, MinecartHopper, MinecartTNT, Painting, PrimedTNT, ShulkerBullet, Snowball, ThrownExpBottle, ThrownPotion, XPOrb, Herobrine, Human, Dragon, ElderGuardian, EnderDragon, Endermite, EvocationFangs, Giant, Guardian, Illusioner, LavaSlime, Llama, PolarBear, Shulker, Slime, SkeletonHorse, Squid, Vindicator, Witch, Wither, WitherSkeleton, ZombieHorse};
use pocketmine\entity\animal\walking\{Chicken, Cow, Donkey, Horse, Mooshroom, Mule, Ocelot, Pig, Rabbit, Sheep, Villager};
use pocketmine\entity\animal\flying\{Bat, Parrot};
use pocketmine\entity\monster\flying\{Blaze, Ghast, Vex};
use pocketmine\entity\monster\jumping\{MagmaCube/*, Slime*/};
use pocketmine\entity\monster\walking\{CaveSpider, Creeper, Enderman, Husk, IronGolem, PigZombie, Silverfish, Skeleton, SnowGolem, Spider, Stray, Wolf, Zombie, ZombieVillager};
use pocketmine\entity\projectile\FireBall;
use pocketmine\inventory\customInventory\CustomChest;
use pocketmine\tile\{Tile, ArmorStand, Banner, Beacon, Bed, BrewingStand, Cauldron, Chest, CommandBlock, Dispenser, DLDetector, Dropper, EnchantTable, EnderChest, FlowerPot, Furnace, Hopper, ItemFrame, Jukebox, MobSpawner, Sign, Skull};

class Registerer{
	
	public static function registerAll(){
		Entity::registerEntity(Arrow::class);
		Entity::registerEntity(BlazeFireball::class);
		Entity::registerEntity(Camera::class);
		Entity::registerEntity(Car::class);
		Entity::registerEntity(DroppedItem::class);
		Entity::registerEntity(Egg::class);
		Entity::registerEntity(EnderCrystal::class);
		Entity::registerEntity(EnderPearl::class);
		Entity::registerEntity(FallingSand::class);
		Entity::registerEntity(FireBall::class);
		Entity::registerEntity(FishingHook::class);
		Entity::registerEntity(FloatingText::class);
		Entity::registerEntity(GhastFireball::class);
		Entity::registerEntity(LeashKnot::class);
		Entity::registerEntity(Lightning::class);
		Entity::registerEntity(Minecart::class);
		Entity::registerEntity(MinecartChest::class);
		Entity::registerEntity(MinecartHopper::class);
		Entity::registerEntity(MinecartTNT::class);
		Entity::registerEntity(Painting::class);
		Entity::registerEntity(PrimedTNT::class);
		Entity::registerEntity(Snowball::class);
		Entity::registerEntity(ThrownExpBottle::class);
		Entity::registerEntity(ThrownPotion::class);
		Entity::registerEntity(XPOrb::class);
		Entity::registerEntity(Human::class, true);
		Entity::registerEntity(Bat::class);
		Entity::registerEntity(Blaze::class);
		Entity::registerEntity(BlueWitherSkull::class);
		Entity::registerEntity(Boat::class);
		Entity::registerEntity(CaveSpider::class);
		Entity::registerEntity(Chicken::class);
		Entity::registerEntity(Cow::class);
		Entity::registerEntity(Creeper::class);
		Entity::registerEntity(Dragon::class);
		Entity::registerEntity(Donkey::class);
		Entity::registerEntity(ElderGuardian::class);
		Entity::registerEntity(EnderDragon::class);
		Entity::registerEntity(Enderman::class);
		Entity::registerEntity(Endermite::class);
		Entity::registerEntity(EvocationFangs::class);
		Entity::registerEntity(Giant::class);
		Entity::registerEntity(Ghast::class);
		Entity::registerEntity(Guardian::class);
		//Entity::registerEntity(Herobrine::class);
		//Entity::registerEntity(Horse::class);
		Entity::registerEntity(Husk::class);
		Entity::registerEntity(Illusioner::class);
		Entity::registerEntity(IronGolem::class);
		Entity::registerEntity(LavaSlime::class);
		Entity::registerEntity(Llama::class);
		Entity::registerEntity(MagmaCube::class);
		Entity::registerEntity(Mooshroom::class);
		Entity::registerEntity(Mule::class);
		Entity::registerEntity(NPC::class);
		Entity::registerEntity(Ocelot::class);
		Entity::registerEntity(Parrot::class);
		Entity::registerEntity(Pig::class);
		Entity::registerEntity(PigZombie::class);
		Entity::registerEntity(PolarBear::class);
		Entity::registerEntity(Rabbit::class);
		Entity::registerEntity(Sheep::class);
		Entity::registerEntity(Shulker::class);
		Entity::registerEntity(ShulkerBullet::class);
		Entity::registerEntity(Silverfish::class);
		Entity::registerEntity(Slime::class);
		Entity::registerEntity(Skeleton::class);
		Entity::registerEntity(SkeletonHorse::class);
		Entity::registerEntity(SnowGolem::class);
		Entity::registerEntity(Spider::class);
		Entity::registerEntity(Stray::class);
		Entity::registerEntity(Squid::class);
		Entity::registerEntity(Vex::class);
		Entity::registerEntity(Villager::class);
		Entity::registerEntity(Vindicator::class);
		Entity::registerEntity(Witch::class);
		Entity::registerEntity(Wither::class);
		Entity::registerEntity(WitherSkeleton::class);
		Entity::registerEntity(Wolf::class);
		Entity::registerEntity(Zombie::class);
		Entity::registerEntity(ZombieHorse::class);
		Entity::registerEntity(ZombieVillager::class);
		
		Tile::registerTile(ArmorStand::class);
		Tile::registerTile(Banner::class);
		Tile::registerTile(Beacon::class);
		Tile::registerTile(Bed::class);
		Tile::registerTile(BrewingStand::class);
		Tile::registerTile(Cauldron::class);
		Tile::registerTile(Chest::class);
		Tile::registerTile(CommandBlock::class);
		Tile::registerTile(Dispenser::class);
		Tile::registerTile(DLDetector::class);
		Tile::registerTile(Dropper::class);
		Tile::registerTile(EnchantTable::class);
		Tile::registerTile(EnderChest::class);
		Tile::registerTile(FlowerPot::class);
		Tile::registerTile(Furnace::class);
		Tile::registerTile(Hopper::class);
		Tile::registerTile(ItemFrame::class);
		Tile::registerTile(Jukebox::class);
		Tile::registerTile(MobSpawner::class);
		Tile::registerTile(Sign::class);
		Tile::registerTile(Skull::class);
		
		Tile::registerTile(CustomChest::class);
	}
	
}
