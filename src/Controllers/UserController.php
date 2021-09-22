<?php
namespace RatchetChat\Controllers;
use RatchetChat\Repositories\UserRepository;

class UserController{
    private $repo;

    public function __construct($context)
    {
        $this->repo = new UserRepository($context);
    }
}