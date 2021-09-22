<?php
namespace RatchetChat\Controllers;
use RatchetChat\Repositories\ChannelRepository;
use RatchetChat\Transversal\Util;

class ChannelController{
    private $repo;

    public function __construct($context)
    {
        $this->repo = new ChannelRepository($context);
    }

    public function Find($channel_id)
    {
        try 
        {            
            return $this->repo->Find($channel_id);
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
            return $this->repo->All();
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
            return $this->repo->Create($channel_name);
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }
    }
}