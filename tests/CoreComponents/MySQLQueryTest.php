<?php

declare(strict_types=1);

namespace Tests\Application\CoreComponents;

use Tests\TestCase;
use App\CoreComponents\MySQLDatabase;

class MySQLQueryTest extends TestCase
{
    public function testAction()
    {
        $database= new MySQLDatabase();
        $sql= 'create table if not exists testtable (id int, textinfo varchar(50), PRIMARY KEY (`id`))';
        $database->execute($sql);

        $sql= 'truncate table testtable';
        $database->execute($sql);
        
        $sql= 'insert ignore into testtable (id,textinfo) values (1,\'1\'),(2,\'2\')';
        $database->execute($sql);
        
        $sql= 'select * from testtable';
        $queryres= $database->query($sql);
        
        $result= array();
        foreach ($queryres as $row) {
            $result[]= $row;
        }

        $sql= 'drop table testtable';
        $database->execute($sql);
        
        
        $this->assertEquals(array(array('id'=>1,'textinfo'=>'1'),array('id'=>2,'textinfo'=>'2')), $result);
    }
}
