<?php

namespace RatchetChat;

use RatchetChat\Controllers\AppController;
use RatchetChat\Controllers\ChannelController;
use RatchetChat\Controllers\MessageController;
use RatchetChat\Controllers\UserController;
use Ratchet\ConnectionInterface;

class App
{
    protected $appController;
    protected $channelController;
    protected $messageController;
    protected $userController;
    protected $controllerMappings;
    protected $eventTypes;

    public function __construct()
    {
        $this->appController = new AppController();
        $this->channelController = new ChannelController();
        $this->messageController = new MessageController();
        $this->userController = new UserController();

        $this->eventTypes = [
            "NEW_APP",
            "NEW_CHANNEL",
            "MESSAGE"
        ];
    }

    public function HandleMessage(ConnectionInterface $from, $objMsg)
    {

    }
}