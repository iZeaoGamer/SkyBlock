<?php
/**
 *  _____    ____    ____   __  __  __  ______
 * |  __ \  / __ \  / __ \ |  \/  |/_ ||____  |
 * | |__) || |  | || |  | || \  / | | |    / /
 * |  _  / | |  | || |  | || |\/| | | |   / /
 * | | \ \ | |__| || |__| || |  | | | |  / /
 * |_|  \_\ \____/  \____/ |_|  |_| |_| /_/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 */

namespace SkyBlock;

use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\command\IsleCommandMap;
use SkyBlock\generator\IsleGeneratorManager;
use SkyBlock\isle\IsleManager;
use SkyBlock\provider\json\JSONProvider;
use SkyBlock\provider\Provider;
use SkyBlock\session\SessionManager;

class SkyBlock extends PluginBase {

    /** @var SkyBlock */
    private static $object = null;

    /** @var SkyBlockSettings */
    private $settings;
    
    /** @var Provider */
    private $provider;
    
    /** @var SessionManager */
    private $sessionManager;
    
    /** @var IsleManager */
    private $isleManager;
    
    /** @var IsleCommandMap */
    private $commandMap;
    
    /** @var IsleGeneratorManager */
    private $generatorManager;
    
    /** @var SkyBlockListener */
    private $eventListener;
    
    public function onLoad(): void {
        self::$object = $this;
        if(!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }
        $this->saveResource("messages.json");
        $this->saveResource("settings.json");
    }

    public function onEnable(): void {
        $this->settings = new SkyBlockSettings($this);
        $this->provider = new JSONProvider($this);
        $this->sessionManager = new SessionManager($this);
        $this->isleManager = new IsleManager($this);
        $this->generatorManager = new IsleGeneratorManager($this);
        $this->commandMap = new IsleCommandMap($this);
        $this->eventListener = new SkyBlockListener($this);
        $this->getLogger()->info("SkyBlock was enabled");
    }

    public function onDisable(): void {
        $this->getLogger()->info("SkyBlock was disabled");
    }

    /**
     * @return SkyBlock
     */
    public static function getInstance(): SkyBlock {
        return self::$object;
    }
    
    /**
     * @return SkyBlockSettings
     */
    public function getSettings(): SkyBlockSettings {
        return $this->settings;
    }
    
    /**
     * @return Provider
     */
    public function getProvider(): Provider {
        return $this->provider;
    }
    
    /**
     * @return SessionManager
     */
    public function getSessionManager(): SessionManager {
        return $this->sessionManager;
    }
    
    /**
     * @return IsleManager
     */
    public function getIsleManager(): IsleManager {
        return $this->isleManager;
    }

    /**
     * @return IsleGeneratorManager
     */
    public function getGeneratorManager(): IsleGeneratorManager {
        return $this->generatorManager;
    }
    
    /**
     * @param int $seconds
     * @return string
     */
    public static function printSeconds(int $seconds): string {
        $m = floor($seconds / 60);
        $s = floor($seconds % 60);
        return (($m < 10 ? "0" : "") . $m . ":" . ($s < 10 ? "0" : "") . (string) $s);
    }
    
    /**
     * @param Position $position
     * @return string
     */
    public static function writePosition(Position $position): string {
        return "{$position->getLevel()->getName()},{$position->getX()},{$position->getY()},{$position->getZ()}";
    }
    
    /**
     * @param string $position
     * @return null|Position
     */
    public static function parsePosition(string $position): ?Position {
        $array = explode(",", $position);
        if(isset($array[3])) {
            $level = Server::getInstance()->getLevelByName($array[0]);
            if($level != null) {
                return new Position((float) $array[1],(float) $array[2],(float) $array[3], $level);
            }
        }
        return null;
    }
    
    /**
     * Parse an Item
     *
     * @param string $item
     * @return null|Item
     */
    public static function parseItem(string $item): ?Item {
        $parts = explode(",", $item);
        foreach($parts as $key => $value) {
            $parts[$key] = (int) $value;
        }
        if(isset($parts[0])) {
            return Item::get($parts[0], $parts[1] ?? 0, $parts[2] ?? 1);
        }
        return null;
    }
    
    /**
     * @param array $items
     * @return array
     */
    public static function parseItems(array $items): array {
        $result = [];
        foreach($items as $item) {
            $item = self::parseItem($item);
            if($item != null) {
                $result[] = $item;
            }
        }
        return $result;
    }
    
    /**
     * @param string $message
     * @return string
     */
    public static function translateColors(string $message): string {
        $message = str_replace("§", TextFormat::BLACK, $message);
        $message = str_replace("§", TextFormat::DARK_BLUE, $message);
        $message = str_replace("§", TextFormat::DARK_GREEN, $message);
        $message = str_replace("§", TextFormat::DARK_AQUA, $message);
        $message = str_replace("§", TextFormat::DARK_RED, $message);
        $message = str_replace("§", TextFormat::DARK_PURPLE, $message);
        $message = str_replace("§", TextFormat::GOLD, $message);
        $message = str_replace("§", TextFormat::GRAY, $message);
        $message = str_replace("§", TextFormat::DARK_GRAY, $message);
        $message = str_replace("§", TextFormat::BLUE, $message);
        $message = str_replace("§", TextFormat::GREEN, $message);
        $message = str_replace("§", TextFormat::AQUA, $message);
        $message = str_replace("§", TextFormat::RED, $message);
        $message = str_replace("§", TextFormat::LIGHT_PURPLE, $message);
        $message = str_replace("§", TextFormat::YELLOW, $message);
        $message = str_replace("§", TextFormat::WHITE, $message);
        $message = str_replace("§", TextFormat::OBFUSCATED, $message);
        $message = str_replace("§", TextFormat::BOLD, $message);
        $message = str_replace("§", TextFormat::STRIKETHROUGH, $message);
        $message = str_replace("§", TextFormat::UNDERLINE, $message);
        $message = str_replace("§", TextFormat::ITALIC, $message);
        $message = str_replace("§", TextFormat::RESET, $message);
        return $message;
    }
    
    /**
     * @return string
     */
    public static function generateUniqueId(): string {
        return "is-" . microtime();
    }
    
}
