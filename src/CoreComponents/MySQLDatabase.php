<?php

namespace App\CoreComponents;

use App\CoreComponents\MySQLQuery as Query;
use Exception;
use mysqli;

class MySQLDatabase implements DatabaseInterface 
{

    protected $dbconfig;
    protected $dbinstance;
    protected $mysqlversion;
    protected $dbname;
    protected $inTransaction = false;

    public function __construct() {
        include __DIR__.'/../../app/db.php';
        
        $this->dbconfig = $dbconfig;
        $this->dbinstance = null;

        $this->inTransaction = false;
    }

    protected function checkDBConnection() {
        if (empty($this->dbinstance)) {

            $olderep = error_reporting();

            error_reporting(0);

            if (!empty($this->dbconfig['dbport'])) {
                $this->dbinstance = new mysqli($this->dbconfig['dbhost'], $this->dbconfig['dbuser'], $this->dbconfig['dbpass'], $this->dbconfig['dbname'], $this->dbconfig['dbport']);
            } else {
                $this->dbinstance = new mysqli($this->dbconfig['dbhost'], $this->dbconfig['dbuser'], $this->dbconfig['dbpass'], $this->dbconfig['dbname']);
            }

            error_reporting($olderep);

            $this->dbname = $this->dbconfig['dbname'];

            $this->dbinstance->query("CREATE DATABASE IF NOT EXISTS '". $this->escape($this->dbname)."'");
            
            $this->dbinstance->select_db($this->dbconfig['dbname']);

            if ($this->dbinstance->errno)
                throw new Exception("MySQL error: (" . $this->dbinstance->errno . ") " . $this->dbinstance->error);

            if ($this->dbinstance->connect_errno)
                throw new Exception("Failed to connect to MySQL: (" . $this->dbinstance->connect_errno . ") " . $this->dbinstance->connect_error);

            $this->execute("set names 'utf8'");

            $this->mysqlversion = $this->dbinstance->server_version;
        }
    }

    public function execute($sql) {
        $this->checkDBConnection();
        $this->dbinstance->query($sql);

        if ($this->dbinstance->errno)
            throw new Exception("MySQL error: (" . $this->dbinstance->errno . ") " . $this->dbinstance->error);
    }

    public function query($sql) {
        $this->checkDBConnection();

        $tmp = $this->dbinstance->query($sql);

        if ($this->dbinstance->errno)
            throw new Exception("MySQL error: (" . $this->dbinstance->errno . ") " . $this->dbinstance->error);

        return new Query($tmp);
    }

    public function escape($str) {
        $this->checkDBConnection();

        return $this->dbinstance->real_escape_string($str);
    }

    public function fullEscape($str) {
        $this->checkDBConnection();

        return '\'' . $this->dbinstance->real_escape_string($str) . '\'';
    }

    public function safeForLike($str) {
        $this->checkDBConnection();

        return str_replace('%', '', $this->escape($str)); //make it not to include wildcards inside string
    }

    public function getFullLike($str) {
        $this->checkDBConnection();

        return '\'%' . $this->safeForLike($str) . '%\'';
    }

    public function fullEscapeLike($str) {
        $this->checkDBConnection();

        return '\'' . $this->safeForLike($str) . '\'';
    }

    public function __destruct() {
        if (!empty($this->dbinstance)) {
            $this->dbinstance->close();
        }
    }

    public function insertId() {
        $this->checkDBConnection();

        return $this->dbinstance->insert_id;
    }

    //transaction support- as for MySQL 5.6 and later mysqli has different API, we execute two different scenarios depending on MySQL server version
    public function beginTransaction() {
        $this->checkDBConnection();

        if (!$this->inTransaction) {
            //check mysql version- prior to 5.6 we must use different scenario
            if ($this->mysqlversion >= 50600) {
                //5.6 and up
                $args = func_get_args();
                call_user_func_array(array($this->dbinstance, 'begin_transaction'), $args);
            } else {
                $this->dbinstance->autocommit(false);
            }
        }

        $this->inTransaction = true;
    }

    public function commit() {
        $this->checkDBConnection();

        if ($this->inTransaction) {

            $args = func_get_args();
            call_user_func_array(array($this->dbinstance, 'commit'), $args);

            if ($this->mysqlversion < 50600) {
                $this->dbinstance->autocommit(true);
            }
        }

        $this->inTransaction = false;
    }

    public function rollback() {
        $this->checkDBConnection();

        if ($this->inTransaction) {

            $args = func_get_args();
            call_user_func_array(array($this->dbinstance, 'rollback'), $args);

            if ($this->mysqlversion < 50600) {
                $this->dbinstance->autocommit(true);
            }
        }

        $this->inTransaction = false;
    }

    public function getDBName() {
        return $this->dbname;
    }

    public function escapeTableName($tableName) {
        $this->checkDBConnection();

        $arr = explode('.', $tableName);

        $resultArr = array();

        foreach ($arr as $value) {
            $resultArr[] = '`' . strtr(stripcslashes($value), array('\'' => '', '`' => '', '"' => '')) . '`';
        }

        return join('.', $resultArr);
    }

    public function escapeFieldName($fieldName) {
        $this->checkDBConnection();

        return $this->escapeTableName($fieldName); //they are actually the same ;)
    }

    public function escapeIntValNull($value) {
        $this->checkDBConnection();

        return empty($value) ? 'null' : sprintf('%d', $value);
    }

}
