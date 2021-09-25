<?php
namespace RatchetChat\Repositories;

use RatchetChat\Transversal\Util;
use RatchetChat\Domain\Channel;
use RatchetChat\Domain\User;

class ChannelRepository
{
    private $db;

    public function __construct($context)
    {
        $this->db = $context;
    }

    public function Find($channel_id)
    {
        try 
        {            
            $res = $this->db->Select("SELECT * FROM CHANNELS WHERE CHANNEL_ID = :CHANNEL_ID", [ 
                "CHANNEL_ID" => $channel_id 
            ]);

            if(count($res) != 0)
            {
                $channel = new Channel();
                $channel->channel_id = $res[0]["channel_id"];
                $channel->channel_name = $res[0]["channel_name"];
                return $channel;                
            }

            return null;
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

            if(count($result) != 0)
            {
                $arr = [];
                foreach ($result as $elem) 
                {
                    $obj = new Channel();
                    $obj->channel_id = $elem["channel_id"];
                    $obj->channel_name = $elem["channel_name"];

                    $arr[] = $obj;
                }
            }

            return $result;
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }

    public function AddUserToChannel(User $user, $channel_id)
    {
        try 
        {            
            $this->db->Insert("INSERT INTO CHANNEL_USER(USER_ID, CHANNEL_ID) VALUES(:USER_ID, :CHANNEL_ID)", [ 
                "USER_ID" => $user->user_id,
                "CHANNEL_ID" => $channel_id
            ], false);
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
            $res = $this->db->Insert("INSERT INTO CHANNELS(CHANNEL_NAME) VALUES(:CHANNEL_NAME);", [ 
                "CHANNEL_NAME" => $channel_name
            ]);

            $channel = new Channel();
            $channel->channel_id = $res;
            $channel->channel_name = $channel_name;

            return $channel;
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }
}