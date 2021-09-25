<?php
namespace RatchetChat\Controllers;
use RatchetChat\Repositories\UserRepository;
use RatchetChat\Domain\User;
use RatchetChat\Domain\Channel;

class UserController{
    private $repo;

    public function __construct($context)
    {
        $this->repo = new UserRepository($context);
    }

    public function Find($user_id)
    {
        try 
        {            
            return $this->repo->Find($user_id);
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }

    public function Create(User $user)
    {
        try 
        {            
            return $this->repo->Create($user);
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
            return $this->repo->GetByChannel($channel);
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }
    
}