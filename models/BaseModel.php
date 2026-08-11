<?php

class BaseModel
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = require __DIR__ . '/../config/banco.php';
    }
}