<?php
namespace RatchetChat\Controllers;
use RatchetChat\Repositories\MessageRepository;

use RatchetChat\Transversal\Util;

class MessageController{
    private $repo;

    public function __construct($context)
    {
        $this->repo = new MessageRepository($context);
    }

    public function Create($channel_id, $usuario_id, $text)
    {
        try 
        {                        
            return $this->repo->Create($channel_id, $usuario_id, $text);
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
            return $this->repo->GetByChannelId($channel_id);
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }
}