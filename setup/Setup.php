<?php

namespace App\Setup;

use App\CoreComponents\MySQLDatabase;
use Composer\InstalledVersions;

class Setup {

    static $copyPackages = array(
        'tinymce/tinymce' => 'tinymce'
    );
    static $publicDir = 'public';
    static $basePath;
    static $publicDirPath;

    /**
     * Copies packages to the public directory so we can keep the code repository clean and distribute updates properly
     * To be used only on packages such as TinyMCE.
     */
    static function copyPublicPackages() {
        print "\nCopying public packages...\n";
        
        if (stripos(PHP_OS,'linux')!==false) {
            $pathSep= '/';
        } else {
            $pathSep= '\\';
        }
        
        reset(self::$copyPackages);
        foreach (self::$copyPackages as $packageName => $packageDir) {
            $path = InstalledVersions::getInstallPath($packageName);

            $srcPath = realpath($path);
            $toPath = realpath(self::$publicDirPath) . $pathSep . $packageDir;

            print "Copying package $packageName contents from $srcPath to $toPath...\n";
            
            if (stripos(PHP_OS,'linux')!==false) {
                if (file_exists($toPath)) {
                    exec('rm -rf "' . realpath($toPath).'"');
                }
                mkdir($toPath);
                chmod($toPath, 0764); //RWX RW R
                exec('cp -R "' . $srcPath . '/*" "' . realpath($toPath) . '"');
            } else {
                if (file_exists($toPath)) {
                    exec('rd /s /q "' . realpath($toPath).'"');
                }

                mkdir($toPath);
                exec('xcopy /s /q "' . $srcPath . '\*" "' . realpath($toPath) . '"');
            }
        }
    }

    /**
     * Reads .sql files from migrations directory and executes them in alphabetical order if not marked as already executed in 
     * the migrationslog table.
     * @throws \Exception
     */
    static function executeMigrations() {
         print "\nExecuting SQL migrations...\n";
        
        $setupPath = realpath(__DIR__);

        $migrationsPath = $setupPath . '/migrations';

        $database = new MySQLDatabase();

        if (!file_exists($migrationsPath)) {
            mkdir($migrationsPath);
        }

        /*
         * Create migrations log if not present
         */
        $sql = 'create table if not exists migrationslog (id int AUTO_INCREMENT,name varchar(200),dateexec DATETIME, PRIMARY KEY (`id`))';
        $database->execute($sql);

        /*
         * Get migrations already executed
         */
        $executedMigrations = array();
        $sql = 'select * from migrationslog';
        $res = $database->query($sql);

        foreach ($res as $val) {
            $executedMigrations[$val['name']] = 1;
        }

        $migrations = array();
        $dir = opendir($migrationsPath);
        while ($fileName = readdir($dir)) {
            if ($fileName != '.' && $fileName != '..' && preg_match('~\.sql$~', $fileName)) {
                //add only if not executed
                if (!isset($executedMigrations[$fileName])) {
                    $migrations[] = $fileName;
                }
            }
        }
        closedir($dir);

        //sort so that execution is in proper order
        asort($migrations, SORT_STRING);

        /*
         * Execute migrations
         */
        reset($migrations);
        foreach ($migrations as $migFile) {
            $migText = file_get_contents($migrationsPath . '/' . $migFile) . "\n\n";
            $arrMig = explode("\n--!ENDQUERY--", $migText);
            reset($arrMig);
            foreach ($arrMig as $sql) {
                $sql = trim($sql);
                if (!empty($sql)) {
                    try {
                        $database->execute($sql);
                    } catch (\Exception $e) {
                        print "\n\n" . $sql . "\n";
                        throw $e;
                    }
                }
            }

            //store in log
            $sql = sprintf('insert into migrationslog (name,dateexec) values (%1$s,now())',
                    $database->fullEscape($migFile)
            );
            $database->execute($sql);

            print "\nExecuted migration $migFile";
        }

    }
    
    /*
     * execute setup database migrations
     */

    static function doSetup() {
        self::$basePath = realpath(__DIR__ . '/..');
        if (self::$publicDir[0] != '/') {
            $path = self::$basePath . '/' . self::$publicDir;
        } else {
            $path = self::$publicDir;
        }
        self::$publicDirPath = realpath($path);
        
        self::copyPublicPackages();

        self::executeMigrations();
        
        print "\n";
    }

}
