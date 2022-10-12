<?php

/** 
 * A singleton to return the same database connection all the time
 */

namespace App\CoreComponents;

namespace App\CoreComponents;

use App\CoreComponents\DatabaseInterface;
use App\CoreComponents\DatabaseFactory;

class DatabaseConnection {
    static DatabaseInterface $database;
    static function getDatabase():DatabaseInterface{
        if (!isset(self::$database)||!is_object(self::$database)) {
            self::$database= DatabaseFactory::new();
        }
        
        return self::$database;
    }
}
