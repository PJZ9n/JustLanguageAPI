<?php
    declare(strict_types=1);

    namespace PJZ9n\JustLanguageAPI\Event;

    use PJZ9n\JustLanguageAPI\JustLanguageAPI;
    use pocketmine\event\Listener;

    class eventListener implements Listener
    {
        private $plugin;

        public function __construct(JustLanguageAPI $plugin)
        {
            $this->plugin = $plugin;
        }

    }