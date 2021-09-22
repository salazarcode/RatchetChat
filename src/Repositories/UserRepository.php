<?php
namespace RatchetChat\Repositories;

class UserRepository{
    private $dbContext;

    public function __construct($context)
    {
        $this->dbContext = $context;
    }
}