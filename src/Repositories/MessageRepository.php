<?php
namespace RatchetChat\Repositories;
use RatchetChat\Transversal\Util;

class MessageRepository
{
    private $dbContext;

    public function __construct($context)
    {
        $this->db = $context;
    }

    public function Create($channel_id, $usuario_id, $text)
    {
        try 
        {            
            $res = $this->db->Insert("INSERT INTO MESSAGES(CHANNEL_ID, USUARIO_ID, TEXT) VALUES(:CHANNEL_ID, :USUARIO_ID, :TEXT)", [ 
                "CHANNEL_ID" => $channel_id,
                "USUARIO_ID" => $usuario_id,
                "TEXT" => $text
            ]);
            return $res;
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }

    public function GetByChannelId($channel_id)
    {
        try 
        {            
            $res = $this->db->Select("SELECT * FROM MESSAGES WHERE CHANNEL_ID = :CHANNEL_ID", [ 
                "CHANNEL_ID" => $channel_id
            ]);
            return $res;
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }
    
}