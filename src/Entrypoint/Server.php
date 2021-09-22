<?php
namespace RatchetChat\Entrypoint;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__) . '/../vendor/autoload.php';

$puerto = 5001;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new EventHub()
        )
    ), 
    $puerto
)->run();
