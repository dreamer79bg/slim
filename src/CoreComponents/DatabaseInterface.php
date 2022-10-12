<?php

namespace App\CoreComponents;

interface DatabaseInterface {
    
    public function execute($sql);
    public function query($sql);
    public function escape($str);
    public function fullEscape($str);

    public function safeForLike($str);

    public function getFullLike($str);
    public function fullEscapeLike($str);
    
    public function insertId();

    //transaction support- as for MySQL 5.6 and later mysqli has different API, we execute two different scenarios depending on MySQL server version
    public function beginTransaction();
    
    public function commit();

    public function rollback();

    public function escapeTableName($tableName);

    public function escapeFieldName($fieldName);

    public function escapeIntValNull($value);

}
