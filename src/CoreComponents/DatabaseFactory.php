<?php

namespace App\CoreComponents;

use App\CoreComponents\DatabaseInterface;
use App\CoreComponents\MySQLDatabase;

class DatabaseFactory {

    static function new(): DatabaseInterface
    {
        return new MySQLDatabase();
    }
    
}