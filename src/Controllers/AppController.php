<?php
namespace RatchetChat\Controllers;

use RatchetChat\Repositories\AppRepository;

class AppController
{
    private $repo;

    public function __construct($context)
    {
        $this->repo = new AppRepository($context);
    }

    public function Create($input)
    {
        $entity = $this->repo->Create($input);
    }
}