<?php
namespace RatchetChat\Repositories;

use RatchetChat\Domain\User;
use RatchetChat\Domain\Channel;

class UserRepository{
    private $db;

    public function __construct($context)
    {
        $this->db = $context;
    }

    
    public function Create(User $user)
    {
        try 
        {            
            $user->user_id = $this->db->Insert("INSERT INTO USERS(USER_EXTERNAL_ID, USER_NAME) VALUES(:USER_EXTERNAL_ID, :USER_NAME);", [ 
                "USER_EXTERNAL_ID" => $user->user_external_id,
                "USER_NAME" => $user->user_name
            ]);

            return $user;
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }

    public function Find($user_id)
    {
        try 
        {            
            $res = $this->db->Select("SELECT * FROM USERS WHERE USER_EXTERNAL_ID = :USER_EXTERNAL_ID ;", [ 
                "USER_EXTERNAL_ID" => $user_id,
            ]);

            if(count($res))
            {              
                $user = new User();  
                $user->user_id = $res[0]["user_id"];
                $user->user_name = $res[0]["user_name"];
                $user->user_external_id = $res[0]["user_external_id"];

                return $user;
            }

            return null;
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
            $users = $this->db->Select("SELECT U.* FROM USERS U INNER JOIN CHANNEL_USER CU ON CU.USER_ID = U.USER_ID WHERE CU.CHANNEL_ID = :CHANNEL_ID", [ 
                "CHANNEL_ID" => $channel->channel_id
            ]);

            if(count($users) != 0)
            {
                $arr = [];
                foreach ($users as $row) 
                {
                    $user = new User();
                    $user->user_id = $row["user_id"];
                    $user->user_name = $row["user_name"];
                    $user->user_external_id = $row["user_external_id"];

                    $arr[] = $user;
                }

                return $arr;
            }

            return null;
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }
}