<?php

declare(strict_types=1);

namespace ShauryaGupta06\Lifesteal;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

    public function onEnable(): void{
        $this->getLogger()->info("LifeSteal enabled!");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
    }

    public function onDisable(): void{
        $this->getLogger()->info("LifeSteal disabled!");
    }

    /**
     * @priority MONITOR
     */
    public function onPlayerDeath(PlayerDeathEvent $event): void{
        $player = $event->getPlayer();
        $maxHealth = $player->getMaxHealth();
        
        // Fetch max heart limit from config
        $maxHeartLimit = (int) $this->getConfig()->get("max_heart_limit", 20);

        // If the player's max health is greater than the max heart limit, set it to the limit
        if($maxHealth > $maxHeartLimit) {
            $player->setMaxHealth($maxHeartLimit);
        }
        
        $player->setMaxHealth($maxHealth - 2);

        $lastDamageCause = $player->getLastDamageCause();
        if($lastDamageCause instanceof EntityDamageByEntityEvent){
            $entity = $lastDamageCause->getEntity();
            if($entity instanceof Player){
                $entity->setMaxHealth($entity->getMaxHealth() + 2);
            }
        }
        if($player->getMaxHealth() === 0){
            if($player->kick('You lost all your healths')){
                $player->getServer()->getNameBans()->addBan($player->getName(), 'Lost all healths');
            }
        }
    }
}
