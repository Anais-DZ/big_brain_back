<?php

namespace abstracts;

use PDO;
use function utils\connect;

abstract class Model
{

    protected ?PDO $bdd;


    public function __construct()
    {
        $this->bdd = connect();
    }

    public function getBdd(): PDO
    {
        return $this->bdd;
    }
}