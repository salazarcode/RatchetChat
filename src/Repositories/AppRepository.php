<?php
namespace RatchetChat\Repositories;

class AppRepository
{
    private $dbContext;

    public function __construct($context)
    {
        $this->dbContext = $context;
    }

    public function Create($input)
    {
    }
}