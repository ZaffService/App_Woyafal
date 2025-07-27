<?php
namespace App\Core\Abstract;

use App\Core\Database;
use PDO;

abstract class AbstractRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }
}
