<?php
namespace RatchetChat\Repositories;

use RatchetChat\Transversal\Util;

class ChannelRepository
{
    private $dbContext;

    public function __construct($context)
    {
        $this->db = $context;
    }

    public function Find($channel_id)
    {
        try 
        {            
            return $this->db->Select("SELECT * FROM CHANNELS WHERE CHANNEL_ID = :CHANNEL_ID", [ 
                "CHANNEL_ID" => $channel_id 
            ]);
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }

    public function All()
    {
        try 
        {            
            $result = $this->db->Select("SELECT * FROM CHANNELS");
            return $result;
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }

    public function Create($channel_name)
    {
        try 
        {            
            $res = $this->db->Insert("INSERT INTO CHANNELS(CHANNEL_NAME) VALUES(:CHANNEL_NAME)", [ 
                "CHANNEL_NAME" => $channel_name
            ]);
            return $res;
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }
}