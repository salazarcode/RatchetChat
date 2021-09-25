<?php

require 'vendor/autoload.php';

\Ratchet\Client\connect('ws://localhost:5001')->then(function($conn) 
{

    $conn->on('message', function($msg) use ($conn) {
        header('Content-Type: application/json; charset=utf-8');
        echo $msg;
        $conn->close();
    });

    // $input = [
    //     "procedure" => "GET_CHANNEL",
    //     "channel_id" => 12,
    //     "usuario_name" => "Andrés",
    //     "usuario_id" => "2",
    // ];

    $input = [
        "procedure" => "NEW_CHANNEL",
        "channel_name" => "evangelion 2",
        "usuarios" => [
            [
                "user_id" => 1,
                "user_name" => "Andrés"
            ],
            [
                "user_id" => 2,
                "user_name" => "Adrián"
            ],
        ]
    ];

    // $input = [
    //     "procedure" => "ADD_MESSAGE",
    //     "channel_id" => 12,
    //     "usuario_id" => 1,
    //     "text" => "Esta es una prueba de andres"
    // ];

    // $input = [
    //     "procedure" => "ADD_MESSAGE",
    //     "channel_id" => 12,
    //     "usuario_id" => 1,
    //     "text" => "Esta es una prueba de andres"
    // ];

    $conn->send(json_encode($input));
}, 
function ($e) 
{
    echo "Could not connect: {$e->getMessage()}\n";
});