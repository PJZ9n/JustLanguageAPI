<?php
    declare(strict_types=1);

    namespace PJZ9n\JustLanguageAPI;

    use PJZ9n\JustLanguageAPI\Command\langCommand;
    use PJZ9n\JustLanguageAPI\Event\eventListener;
    use pocketmine\network\mcpe\protocol\AddEntityPacket;
    use pocketmine\network\mcpe\protocol\LoginPacket;
    use pocketmine\Player;
    use pocketmine\plugin\PluginBase;
    use pocketmine\utils\Config;

    class JustLanguageAPI extends PluginBase
    {
        private static $instance = null;

        public $player_lang;

        public $language_list;
        public $language_data;

        public function onEnable(): void
        {
            $a = new AddEntityPacket();
            $a->
            $this->register();
            $this->initLanguage();
            $this->player_lang = new Config($this->getDataFolder() . "player_lang.json", Config::JSON);
            $this->getLogger()->info("{$this->getDescription()->getName()}-{$this->getDescription()->getVersion()} の読み込みが完了しました");
        }

        public function onDisable(): void
        {
            $this->getLogger()->info("{$this->getDescription()->getName()}-{$this->getDescription()->getVersion()} が終了が完了しました");
        }

        public function onLoad(): void
        {
            self::$instance = $this;
        }

        private function register(): void
        {
            $this->getServer()->getPluginManager()->registerEvents(new eventListener($this), $this);
            $this->getServer()->getCommandMap()->register("lang", new langCommand($this));
        }

        private function initLanguage(): void
        {
            if (!file_exists($this->getDataFolder())) {
                mkdir($this->getDataFolder(), 0777);
            }
            if (!file_exists($this->getDataFolder() . "system.json")) {
                file_put_contents($this->getDataFolder() . "system.json", json_encode(array(
                    "language" => "system",
                    "commands" => array(
                        "lang" => array(
                            "usage" => "/lang",
                            "description" => "言語を設定します",
                        ),
                    ),
                    "texts" => array(
                        "system.info.enable" => "{%1} {%2} を読み込みました",
                        "system.info.disable" => "{%1} {%2} を終了しました",
                        "justlanguageapi.info.language.load" => "{%1} の言語 {%2} を読み込みました",
                        "justlanguageapi.warning.language.invalid" => "{%1} の記述が間違っているため読み込めません",
                        "justlanguageapi.info.language.complete" => "言語を {%1} 個読み込みました",
                        "justlanguageapi.error.language.notfound" => "言語がひとつも存在しないためサーバーを終了します",
                    ),
                )));
            }
            $this->language_data["system"] = json_decode(file_get_contents($this->getDataFolder() . "system.json"), true);
            foreach (glob($this->getDataFolder() . "*.json") as $language_file) {
                $lang = json_decode(file_get_contents($this->getDataFolder() . $language_file), true);
                if (isset($lang["language"])) {
                    $this->language_list[] = $lang["language"];
                    $this->language_data[$lang["language"]] = $lang;
                    $this->getLogger()->info(str_replace(array("{%1}", "{%2}"), array($language_file, $lang["language"]), $this->getSystemTranslate("justlanguageapi.info.language.load")));
                } else {
                    $this->getLogger()->warning(str_replace("{%1}", $language_file, $this->getSystemTranslate("justlanguageapi.warning.language.invalid")));
                }
            }
            if ($this->language_list === null || $this->language_list !== null && count($this->language_list) <= 0) {
                //TODO: error translate
                $this->getLogger()->error("not found");
            } else {
                //TODO: info translate
                $this->getLogger()->info("complete");
            }
        }

        /**
         * @return JustLanguageAPI
         */
        public function getInstance(): JustLanguageAPI
        {
            return self::$instance;
        }

        /**
         * @param Player $player
         * @return null|string
         */
        public function getLanguage(Player $player): ?string
        {
            $name = $player->getName();
            if (!$this->player_lang->exists($name)) {
                return null;
            } else {
                return $this->player_lang->get($name);
            }
        }

        /**
         * @param Player $player
         * @param String $language
         * @return bool
         */
        public function setLanguage(Player $player, String $language): bool
        {
            $name = $player->getName();
            if (!in_array($this->getLanguageList(), array($language), true)) {
                return false;
            } else {
                $this->player_lang[$name] = $language;
                return true;
            }
        }

        /**
         * @return array
         */
        public function getLanguageList(): array
        {
            return $this->language_list;
        }

        /**
         * @param string $text
         * @return string
         */
        public function getSystemTranslate(string $text): string
        {
            if (!isset($this->language_data["system"][$text])) {
                return $text;
            } else {
                return $this->language_data["system"][$text];
            }
        }

        /**
         * @param Player $player
         * @param string $text
         * @return string
         */
        public function getTranslate(Player $player, string $text): string
        {
            if (!isset($this->language_data[$this->getLanguage($player)][$text])) {
                return $text;
            } else {
                return $this->language_data[$this->getLanguage($player)][$text];
            }
        }
    }