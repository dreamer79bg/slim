<?php

declare(strict_types=1);

namespace App\Application\DataServices;

use App\CoreComponents\DatabaseConnection;
use App\CoreComponents\DatabaseInterface;
use Exception;
use App\Application\CRUD\UserObject;

class UserDataService {
    protected DatabaseInterface $_database;
    protected UserObject $userObject;
    
    public function __construct() {
        $this->_database= DatabaseConnection::getDatabase();
        $this->userObject= new UserObject();
    }
    
    public function getLoginId($userName, $password) {
        $id=0;
        
        $passSql= UserObject::encryptPassword($password);
        $sql= sprintf('select %7$s as id from %1$s where %2$s=%3$s and %4$s=%5$s %6$s limit 1'
                , $this->userObject->getTableName()
                , $this->userObject->getFieldByAttribute('userName')
                , $this->_database->fullEscape($userName)
                , $this->userObject->getFieldByAttribute('password')
                , $this->_database->fullEscape($passSql)
                , $this->userObject->getDeletedWhere()
                , $this->_database->escapeFieldName($this->userObject->getTableKeyField())
                );
        $res= $this->_database->query($sql);
        
        foreach ($res as $row) {
            $id=$row['id'];
        }
        
        return $id;
    }
    
    public function getById($id): UserObject {
        return new UserObject($id);
    }
}