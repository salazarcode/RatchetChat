<?php

namespace RatchetChat;

use RatchetChat\Controllers\AppController;
use RatchetChat\Controllers\ChannelController;
use RatchetChat\Controllers\MessageController;
use RatchetChat\Controllers\UserController;
use RatchetChat\Logs\Mylogger;
use Ratchet\ConnectionInterface;
use RatchetChat\Repositories\DatabaseContext;
use RatchetChat\Transversal\Util;

use Dotenv\Dotenv;

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
    public $dbContext;
    public $container;
    public $connections;

    //Domain
    private $channels = [];

    public function __construct(\SplObjectStorage $connections)
    {
        $this->connections = $connections;

        $dotenv = Dotenv::createImmutable(__DIR__ . "/../");
        $dotenv->load();
        $host = $_ENV["HOST"];
        $user = $_ENV["USER"];
        $pass = $_ENV["PASS"];       
        $dbname = $_ENV["DBNAME"];           
        $this->dbContext = new DatabaseContext($host, $dbname, $user, $pass);

        $this->appController = new AppController($this->dbContext);
        $this->channelController = new ChannelController($this->dbContext);
        $this->messageController = new MessageController($this->dbContext);
        $this->userController = new UserController($this->dbContext);   

        $this->logger =  MyLogger::Get("main_channel");   
    }

    public function HandleOpen(ConnectionInterface $newConn)
    {
        try 
        {
            //$this->authenticator->Authenticate($newConn);
            $this->logger->info("Se agregó un nuevo cliente con id de resource: $newConn->resourceId");
            $this->connections->attach($newConn);
            echo "New connection! ({$newConn->resourceId})\n\n";
        } 
        catch (\Exception $ex) 
        {
            return Util::Error($ex);
        }
    }

    public function HandleMessage(ConnectionInterface $from, $objMsg)
    {
        try 
        {
            $this->logger->debug("Se inicia un HandleMessage");
            $this->logger->debug($objMsg);

            $msgArr = json_decode($objMsg);
    
            switch ($msgArr->procedure) 
            {
                case 'NEW_CHANNEL':                    
                    $channelName = $msgArr->channel_name;
                    $res = $this->channelController->Create($channelName);
                    $this->logger->debug("Se creó el channel sin problemas");        
                    $from->send(Util::Ok($res));
                    break;
    
                case 'ALL_CHANNELS':
                    $channels = $this->channelController->All(); 
                    $from->send(Util::Ok($channels));
                    break;
    
                case 'GET_CHANNEL':
                    $channel = $this->channelController->Find($msgArr->channel_id);
                    $messages = $this->messageController->GetByChannelId($msgArr->channel_id);
                    $from->send(Util::Ok([
                        "channel"=>$channel, 
                        "messages"=>$messages
                    ]));
                    break;
    
                case 'ADD_MESSAGE':
                    $res = $this->messageController->Create($msgArr->channel_id, $msgArr->usuario_id, $msgArr->text); 
                    //Luego de creado el mensaje, tengo que mandar el mismo a los involucrados en la conversación.
                    //Para esto tengo que saber cuales usuarios están involucrados en la misma
                    //Para esto, cuando un usuario se conecta, debe haberse identificado y su id guardado para futuros mensajes

                    $from->send(Util::Ok($res));
                    break;
            }
        } 
        catch (\Exception $ex) 
        {
            return Util::Error($ex);
        }

    }

    public function HandleClose(ConnectionInterface $conn)
    {
        try 
        {
            $this->connections->detach($conn);

            echo "Connection {$conn->resourceId} has disconnected*************************************************\n\n";
        } 
        catch (\Exception $ex) 
        {
            return Util::Error($ex);
        }
    }
}