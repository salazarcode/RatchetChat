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
    //     "channel_id" => 18,
    //     "usuario_name" => "Abimael",
    //     "usuario_id" => "9",
    // ];

    // $input = [
    //     "procedure" => "NEW_CHANNEL",
    //     "channel_name" => "los power rangers",
    //     "usuarios" => [
    //         [
    //             "user_id" => 25,
    //             "user_name" => "Abimael"
    //         ],
    //         [
    //             "user_id" => 2,
    //             "user_name" => "Adrián"
    //         ],
    //     ]
    // ];

    $input = [
        "procedure" => "ADD_MESSAGE",
        "channel_id" => 18,
        "user_id" => 9,
        "text" => "Una segunda vez pasa el milagro"
    ];

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