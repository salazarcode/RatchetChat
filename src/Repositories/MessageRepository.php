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
            echo "Se esta creando teoricamente con: ({$message->channel_id}, {$message->user_id}, {$message->text})\n\n";
            $message->message_id = $this->db->Insert("INSERT INTO MESSAGES(CHANNEL_ID, USER_ID, TEXT, DATETIME) VALUES(:CHANNEL_ID, :USER_ID, :TEXT, :DATETIME)", [ 
                "CHANNEL_ID" => $message->channel_id,
                "USER_ID" => $message->user_id,
                "TEXT" => $message->text,
                "DATETIME" => $message->datetime
            ]);

            return $message;
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
                $obj->user_id = $elem["user_id"];

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