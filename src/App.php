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
use RatchetChat\Domain\Channel;
use RatchetChat\Domain\User;
use RatchetChat\Domain\Message;
use Illuminate\Support\Collection;

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
    public $channels = [];

    public function __construct()
    {
        $this->connections = [];
        $this->channels = collect([]);
        $this->usuariosConectados = [];

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
            
            $this->connections[spl_object_hash( $newConn )] = [ 
                'connection' => $newConn, 
                'user_id' => 0,
                'user_name' => "",
                'channel_id' => 0 
            ];

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
            print_r($msgArr);
    
            switch ($msgArr->procedure) 
            {    
                case 'GET_CHANNEL':
                    if(!property_exists($msgArr, "user_id") 
                    && !property_exists($msgArr, "user_name")
                    && !property_exists($msgArr, "channel_id") )
                    {
                        $from->send(Util::Warning("Parámetros incompletos"));
                    }
                    
                    $connection = &$this->connections[spl_object_hash($from)];
                    $connection["channel_id"] = $msgArr->channel_id;
                    $connection["user_id"] = $msgArr->user_id;
                    $connection["user_name"] = $msgArr->user_name;

                    //Se ubica toda la información del canal especificado
                    //Para pasarla al PHP y que el mismo la dibuje
                    $channel = $this->channelController->Find($msgArr->channel_id);
                    $channel->messages = $this->messageController->GetByChannel($channel);
                    $channel->users = $this->userController->GetByChannel($channel);

                    //Agregamos el channel al Collection donde se 
                    //mantiene el control en caliente de los mismos
                    $this->channels->push($channel);

                    $connection["connection"]->send(Util::Ok([
                        "channel"=>$channel
                    ]));
                    break;
    
                case 'ADD_MESSAGE':
                    if(!property_exists($msgArr, "user_id") 
                    && !property_exists($msgArr, "text") 
                    && !property_exists($msgArr, "channel_id") )
                    {
                        $from->send(Util::Warning("Parámetros incompletos"));
                    }

                    //Se crea el mensaje sin ID y con 
                    //todos los campos pasados como input
                    $newMessage = new Message();
                    $newMessage->text = $msgArr->text;
                    $newMessage->datetime = date("Y-m-d H:i:s");
                    $newMessage->user_id = $msgArr->user_id;
                    $newMessage->channel_id = $msgArr->channel_id;

                    //Creamos el mensaje y obtenemos una instancia con el nuevo ID
                    $mensajeAgregado = $this->messageController->Create($newMessage);
                    
                    //Obtenemos los usuarios del channel
                    $theChannel = new Channel();
                    $theChannel->channel_id = $msgArr->channel_id;

                    $resUsersByChannel = $this->userController->GetByChannel($theChannel);
                    
                    $destinatarios = [];

                    foreach ($resUsersByChannel as $user) 
                    {
                        $destinatario = array_filter($this->connections, function($conn) use($user, $theChannel)
                        {
                            return $user->user_id == $conn["user_id"] && $conn["channel_id"] == $theChannel->channel_id;
                        });
                        if(count($destinatario) != 0)
                            $destinatarios[] = reset($destinatario);
                    }
                    
                    foreach ($destinatarios as $to) 
                    {
                        $connection = $to["connection"];
                        $connection->send(Util::Ok([
                            "new_message" => $mensajeAgregado
                        ]));
                    }
                    
                    break;

                case 'NEW_CHANNEL':     
                    if( !property_exists($msgArr, "channel_name") 
                    && !property_exists($msgArr, "usuarios") )
                    {
                        $from->send(Util::Warning("Parámetros incompletos"));
                    }               

                    //Creo un objeto de channel 
                    //con los parámetros pasados por el usuario
                    $channel = $this->channelController->Create($msgArr->channel_name);
                    
                    //Por cada usuario pasado por el usuario, como participante del canal en cuestión, 
                    //Lo busco a ver si no está creado. En caso contrario lo creo.
                    //Finalmente agrego los usuarios agregados al campo usuarios 
                    //del channel que se está creando
                    foreach ($msgArr->usuarios as $user) 
                    {
                        $theUser = $this->userController->Find($user->user_id);

                        if($theUser == null)
                        {
                            $newUser = new User();
                            $newUser->user_external_id = $user->user_id;
                            $newUser->user_name = $user->user_name;
                            $theUser = $this->userController->Create($newUser);
                        }

                        $channel->users[] = $theUser;
                    }                   

                    foreach ($channel->users as $user) 
                    {
                        $this->channelController->AddUserToChannel($user, $channel->channel_id);
                    }

                    //Agrego el channel recién creado a la variable HOT de channels
                    $this->channels->push($channel);
                      
                    $from->send(Util::Ok($channel));
                    break;
    
                case 'ALL_CHANNELS':
                    $channels = $this->channelController->All(); 
                    $from->send(Util::Ok($channels));
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
            unset($this->connections[spl_object_hash($conn)]);

            echo "Connection {$conn->resourceId} has disconnected*************************************************\n\n";
        } 
        catch (\Exception $ex) 
        {
            return Util::Error($ex);
        }
    }
}