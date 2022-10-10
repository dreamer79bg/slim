<?php

namespace App\Setup;

use App\CoreComponents\MySQLDatabase;

class Setup {
    /*
     * execute setup database migrations
     */
    static function doSetup() {
        $setupPath= realpath(__DIR__);
        $migrationsPath= $setupPath.'/migrations';
        
        $database= new MySQLDatabase();
        
        if (!file_exists($migrationsPath)) {
            mkdir($migrationsPath);
        }
        
        /*
         * Create migrations log if not present
         */
        $sql= 'create table if not exists migrationslog (id int AUTO_INCREMENT,name varchar(200),dateexec DATETIME, PRIMARY KEY (`id`))';
        $database->execute($sql);
        
        /*
         * Get migrations already executed
         */
        $executedMigrations= array();
        $sql= 'select * from migrationslog';
        $res= $database->query($sql);
        
        foreach ($res as $val) {
            $executedMigrations[$val['name']]= 1;
        }
        
        $migrations= array();
        $dir= opendir($migrationsPath);
        while($fileName= readdir($dir)) {
            if ($fileName!='.'&&$fileName!='..'&& preg_match('~\.sql$~', $fileName)) {
                //add only if not executed
                if (!isset($executedMigrations[$fileName])) {
                    $migrations[]=$fileName;
                }
            }
        }
        closedir($dir);
        
        //sort so that execution is in proper order
        asort($migrations,SORT_STRING);
        
        /*
         * Execute migrations
         */
        reset($migrations);
        foreach ($migrations as $migFile) {
            $migText= file_get_contents($migrationsPath.'/'.$migFile)."\n\n";
            $arrMig= explode("\n--!ENDQUERY--",$migText);
            reset($arrMig);
            foreach ($arrMig as $sql) {
                $sql= trim($sql);
                if (!empty($sql)) {
                    $database->execute($sql);
                }
            }
    
            //store in log
            $sql= sprintf('insert into migrationslog (name,dateexec) values (%1$s,now())',
                    $database->fullEscape($migFile)
                    );
            $database->execute($sql);
            
            print "\nExecuted migration $migFile";
        }
        
        print "\n";
    }
}

