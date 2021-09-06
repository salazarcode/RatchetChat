<?php
namespace RatchetChat\Presentation;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__) . '/../vendor/autoload.php';

$eventHub = new EventHub();
$wsServer = new WsServer($eventHub);
$httpServer = new HttpServer($wsServer);
$server = IoServer::factory($httpServer, 4000);
$server->run();