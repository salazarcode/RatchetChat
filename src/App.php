<?php

namespace RatchetChat;

use RatchetChat\Controllers\AppController;
use RatchetChat\Controllers\ChannelController;
use RatchetChat\Controllers\MessageController;
use RatchetChat\Controllers\UserController;
use Ratchet\ConnectionInterface;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class App
{
    protected $appController;
    protected $channelController;
    protected $messageController;
    protected $userController;
    protected $controllerMappings;
    public $eventTypes;
    protected $logFilePath;
    public $logger;

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

        //loading the env file on $_ENV
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../");
        $dotenv->load();
        $this->logFilePath =__DIR__ . "/Logs/";

        // create a log channel
        $this->logger = new Logger('main_logger');
        $this->logger->pushHandler(new StreamHandler($this->logFilePath . "log_" . date("Ymd") . ".log", Logger::DEBUG));
    }

    public function HandleMessage(ConnectionInterface $from, $objMsg)
    {
        $this->logger->debug("Llamada al procedimiento " . __FUNCTION__);

        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, json_encode($objMsg), $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send(json_encode($objMsg));
            }
        }

    }
}