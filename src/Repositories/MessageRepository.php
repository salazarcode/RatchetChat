<?php
namespace RatchetChat\Repositories;
use RatchetChat\Domain\Message;
use RatchetChat\Domain\Channel;

class MessageRepository
{
    private $dbContext;

    public function __construct($context)
    {
        $this->db = $context;
    }

    public function Create(Message $message)
    {
        try 
        {            
            echo "Se esta creando teoricamente con: ({$message->channel_id}, {$message->usuario_id}, {$message->text})\n\n";
            $res = $this->db->Insert("INSERT INTO MESSAGES(CHANNEL_ID, USUARIO_ID, TEXT) VALUES(:CHANNEL_ID, :USUARIO_ID, :TEXT)", [ 
                "CHANNEL_ID" => $message->channel_id,
                "USUARIO_ID" => $message->usuario_id,
                "TEXT" => $message->text
            ]);
            return $res;
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }

    public function GetByChannel(Channel $channel)
    {
        try 
        {            
            $res = $this->db->Select("SELECT * FROM MESSAGES WHERE CHANNEL_ID = :CHANNEL_ID", [ 
                "CHANNEL_ID" => $channel->channel_id
            ]);

            $arr = [];
            foreach ($res as $elem) 
            {
                $obj = new Message();
                $obj->message_id = $elem["message_id"];
                $obj->text = $elem["text"];
                $obj->channel_id = $elem["channel_id"];
                $obj->usuario_id = $elem["usuario_id"];

                $arr[] = $obj;
            }
            
            return $arr;
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }
    
}