<?php

declare(strict_types=1);

namespace App\Application\DataServices;

use App\CoreComponents\DatabaseConnection;
use App\CoreComponents\DatabaseInterface;
use Exception;
use App\Application\CRUD\PostObject;

class PostDataService {
    protected DatabaseInterface $_database;
    protected PostObject $dataObject;
    
    public function __construct() {
        $this->_database= DatabaseConnection::getDatabase();
        $this->dataObject= new PostObject();
    }
    
    
    /**
     * return an PostObject by id
     * @param type $id
     * @return PostObject
     */
    public function getById($id): PostObject {
        return new PostObject($id);
    }
    
    /**
     * returns an array of records(array each)
     * @return array
     */
    public function getAll(): array {
        $data= array();
        $sql= sprintf('select %3$s as id, %4$s as title, %5$s as shortDesc, %6$s as createdAt, %7$s as userId
                       from %1$s 
                       where 
                       true %2$s
                       order by createdat desc'
                , $this->dataObject->getTableName()
                , $this->dataObject->getDeletedWhere()
                , $this->_database->escapeFieldName($this->dataObject->getTableKeyField())
                , $this->dataObject->getFieldByAttribute('title')
                , $this->dataObject->getFieldByAttribute('shortDesc')
                //6
                , $this->dataObject->getFieldByAttribute('createdAt')
                , $this->dataObject->getFieldByAttribute('userId')
                );
        $res= $this->_database->query($sql);
        
        foreach ($res as $row) {
            $data[]= $row;
        }
        
        return $data;
    }
    
    /**
     * Creates a new record from array 
     * @param array $data
     * @throws Exception
     */
    public function createPost(array $data) {
        if (!isset($data['id'])) {
            $this->dataObject->clearData();
            if (isset($data['title'])) {
                $this->dataObject->title= $data['title'];
            }
            if (isset($data['shortDesc'])) {
                $this->dataObject->shortDesc= $data['shortDesc'];
            }
            if (isset($data['content'])) {
                $this->dataObject->content= $data['content'];
            }
            if (isset($data['imageFile'])) {
                $this->dataObject->imageFile= $data['imageFile'];
            }
            if (isset($data['userId'])) {
                $this->dataObject->userId= $data['userId'];
            }
            if (isset($data['featuredPos'])) {
                $this->dataObject->featuredPos= $data['featuredPos'];
            }
            $this->dataObject->save(); //if there is a problem with data exception will be thrown
            
            return $this->dataObject->getId();
        } else {
            throw new Exception('Can not create a post with a given id.');
        }
    }
    
    /**
     * Deletes a user. Throws an exception if id not found/already deleted
     * @param type $id
     */
    public function deletePost($id) {
        $this->dataObject->read($id);
        $this->dataObject->delete();
    }
    
     /**
     * updates a user from array 
     * @param array $data
     * @throws Exception
     */
    public function updatePost(array $data) {
        if (isset($data['id'])) {
            
            $this->dataObject->read($data['id']);
            if (isset($data['title'])) {
                $this->dataObject->title= $data['title'];
            }
            if (isset($data['shortDesc'])) {
                $this->dataObject->shortDesc= $data['shortDesc'];
            }
            if (isset($data['content'])) {
                $this->dataObject->content= $data['content'];
            }
            if (isset($data['imageFile'])) {
                $this->dataObject->imageFile= $data['imageFile'];
            }
            if (isset($data['featuredPos'])) {
                $this->dataObject->featuredPos= $data['featuredPos'];
            }
            $this->dataObject->save(); //if there is a problem with data exception will be thrown
            
            return $this->dataObject->getId();
        } else {
            throw new Exception('Can not edit a post without id.');
        }
    }
    
}