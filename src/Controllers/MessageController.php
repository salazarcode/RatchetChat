<?php
namespace RatchetChat\Controllers;
use RatchetChat\Repositories\MessageRepository;

use RatchetChat\Transversal\Util;
use RatchetChat\Domain\Channel;
use RatchetChat\Domain\Message;

class MessageController{
    private $repo;

    public function __construct($context)
    {
        $this->repo = new MessageRepository($context);
    }

    public function Create(Message $message)
    {
        try 
        {                        
            return $this->repo->Create($message);
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