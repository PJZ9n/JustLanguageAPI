<?php
    declare(strict_types=1);

    namespace PJZ9n\JustLanguageAPI\Command;

    use PJZ9n\JustLanguageAPI\JustLanguageAPI;
    use pocketmine\command\Command;
    use pocketmine\command\CommandSender;

    class langCommand extends Command
    {
        private $plugin;

        public function __construct(JustLanguageAPI $plugin)
        {
            //TODO: description translate
            parent::__construct("lang", "desc", "/lang");
            $this->setPermission("justlanguageapi.command.lang");
            $this->plugin = $plugin;
        }

        public function execute(CommandSender $sender, string $commandLabel, array $args): bool
        {
            return true;
        }

    }