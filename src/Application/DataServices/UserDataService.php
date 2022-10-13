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
    
    /**
     * Get the id of the user by user name and password
     * Used for logins
     * @param type $userName
     * @param type $password
     * @return type
     */
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
    
    /**
     * return an UserObject by id
     * @param type $id
     * @return UserObject
     */
    public function getById($id): UserObject {
        return new UserObject($id);
    }
    
    /**
     * returns an array of user records(array each)
     * @return array
     */
    public function getAll(): array {
        $users= array();
        $sql= sprintf('select %5$s as id, %2$s as userName, %3$s as fullName
                       from %1$s 
                       where 
                       true %4$s
                       order by userName'
                , $this->userObject->getTableName()
                , $this->userObject->getFieldByAttribute('userName')
                , $this->userObject->getFieldByAttribute('fullName')
                , $this->userObject->getDeletedWhere()
                , $this->_database->escapeFieldName($this->userObject->getTableKeyField())
                );
        $res= $this->_database->query($sql);
        
        foreach ($res as $row) {
            $users[]= $row;
        }
        
        return $users;
    }
    
    /**
     * Creates a new user from array 
     * @param array $data
     * @throws Exception
     */
    public function createUser(array $data) {
        if (!isset($data['id'])) {
            $this->userObject->clearData();
            if (isset($data['userName'])) {
                $this->userObject->userName= $data['userName'];
            }
            if (isset($data['fullName'])) {
                $this->userObject->fullName= $data['fullName'];
            }
            if (isset($data['password'])) {
                $this->userObject->password= $data['password'];
            }
            $this->userObject->save(); //if there is a problem with data exception will be thrown
            
            return $this->userObject->getId();
        } else {
            throw new Exception('Can not create a user with a given id.');
        }
    }
    
    /**
     * Deletes a user. Throws an exception if id not found/already deleted
     * @param type $id
     */
    public function deleteUser($id) {
        $this->userObject->read($id);
        $this->userObject->delete();
    }
    
     /**
     * updates a user from array 
     * @param array $data
     * @throws Exception
     */
    public function updateUser(array $data) {
        if (isset($data['id'])) {
            
            $this->userObject->read($data['id']);
            /*if (isset($data['userName'])) {
                $this->userObject->userName= $data['userName'];
            }*/ //userName is readonly
            if (isset($data['fullName'])) {
                $this->userObject->fullName= $data['fullName'];
            }
            if (isset($data['password']) && !empty($data['password']) &&!empty(trim($data['password']))) {
                $this->userObject->password= $data['password'];
            }
            $this->userObject->save(); //if there is a problem with data exception will be thrown
            
            return $this->userObject->getId();
        } else {
            throw new Exception('Can not edit a user without id.');
        }
    }
    
}