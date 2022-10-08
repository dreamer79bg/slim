<?php

declare(strict_types=1);

namespace Tests\Application\CoreComponents;

use Tests\TestCase;
use App\CoreComponents\MySQLDatabase;
use Exception;

class MySQLDatabaseTest extends TestCase
{
    public function testAction()
    {
        $ok= true;
        
        try {
            $database= new MySQLDatabase();
            $sql= 'create table if not exists testtable (id int, textinfo varchar(50), PRIMARY KEY (`id`))';
            $database->execute($sql);
            
            $sql= 'truncate table testtable';
            $database->execute($sql);
            
            $sql= 'insert ignore into testtable (id,textinfo) values (1,\'1\'),(2,\'2\')';
            $database->execute($sql);
            $sql= 'drop table testtable';
            $database->execute($sql);
        } catch (Exception $e) {
            $ok= false;
        }
        
        $this->assertEquals(true, $ok);
    }
}
