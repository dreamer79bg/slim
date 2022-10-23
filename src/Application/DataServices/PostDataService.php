<?php

declare(strict_types=1);

namespace App\Application\DataServices;

use App\CoreComponents\DatabaseConnection;
use App\CoreComponents\DatabaseInterface;
use Exception;
use App\Application\CRUD\PostObject;
use App\Application\CRUD\UserObject;
use App\Application\Services\SecurityService;

class PostDataService {

    protected DatabaseInterface $_database;
    protected PostObject $dataObject;

    public function __construct() {
        $this->_database = DatabaseConnection::getDatabase();
        $this->dataObject = new PostObject();
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
    public function getAll($limit = 0, $start = 0): array {
        $users = new UserObject();
        $data = array();
        $sql = sprintf('select %1$s.%3$s as id, %1$s.%4$s as title, %1$s.%5$s as shortDesc, %1$s.%6$s as createdAt, %1$s.%7$s as userId, %1$s.%8$s as content,
                    %1$s.%9$s as imageFile,%1$s.%12$s as featuredPos, %10$s.fullname as userFullName
                       from %1$s  left join %10$s on %1$s.%7$s=%10$s.%11$s
                       where 
                       true %2$s
                       order by createdAt desc
                       %13$s'
                , $this->dataObject->getTableName()
                , $this->dataObject->getDeletedWhere($this->dataObject->getTableName())
                , $this->_database->escapeFieldName($this->dataObject->getTableKeyField())
                , $this->dataObject->getFieldByAttribute('title')
                , $this->dataObject->getFieldByAttribute('shortDesc')
                //6
                , $this->dataObject->getFieldByAttribute('createdAt')
                , $this->dataObject->getFieldByAttribute('userId')
                , $this->dataObject->getFieldByAttribute('content')
                , $this->dataObject->getFieldByAttribute('imageFile')
                , $users->getTableName()
                //11
                , $this->_database->escapeFieldName($users->getTableKeyField())
                , $this->dataObject->getFieldByAttribute('featuredPos')
                , $limit > 0 ? sprintf('limit %1$s%2$d', $start > 0 ? sprintf('%d,', $start) : '', $limit) : ''
        );
        $res = $this->_database->query($sql);

        foreach ($res as $row) {
            $data[] = $row;
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
                $this->dataObject->title = $data['title'];
            }
            if (isset($data['shortDesc'])) {
                $this->dataObject->shortDesc = $data['shortDesc'];
            }
            if (isset($data['content'])) {
                $this->dataObject->content = $data['content'];
            }
            if (isset($data['imageFile'])) {
                $this->dataObject->imageFile = $data['imageFile'];
            }
            if (isset($data['userId'])) {
                $this->dataObject->userId = $data['userId'];
            }
            if (isset($data['featuredPos'])) {
                $this->dataObject->featuredPos = $data['featuredPos'];
            }
            
            if (empty($this->dataObject->userId)) {
                //set user id if post is broken
                $this->dataObject->userId= SecurityService::getService()->getUserId();
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
                $this->dataObject->title = $data['title'];
            }
            if (isset($data['shortDesc'])) {
                $this->dataObject->shortDesc = $data['shortDesc'];
            }
            if (isset($data['content'])) {
                $this->dataObject->content = $data['content'];
            }
            if (isset($data['imageFile'])) {
                $this->dataObject->imageFile = $data['imageFile'];
            }
            if (isset($data['featuredPos'])) {
                $this->dataObject->featuredPos = $data['featuredPos'];
            }
         
            $this->dataObject->save(); //if there is a problem with data exception will be thrown

            return $this->dataObject->getId();
        } else {
            print 'aaaaaaaaaa';
            throw new Exception('Can not edit a post without id.');
        }
    }

    public function getFeatured() {
        $users = new UserObject();
        $data = array();
        $sql = sprintf('select %1$s.%3$s as id, %1$s.%4$s as title, %1$s.%5$s as shortDesc, %1$s.%6$s as createdAt, %1$s.%7$s as userId, %1$s.%8$s as content,
                    %1$s.%9$s as imageFile,%1$s.%12$s as featuredPos, %10$s.fullname as userFullName
                       from %1$s  left join %10$s on %1$s.%7$s=%10$s.%11$s
                       where 
                       true %2$s
                       order by featuredPos desc, createdAt desc
                       limit 3'
                , $this->dataObject->getTableName()
                , $this->dataObject->getDeletedWhere($this->dataObject->getTableName())
                , $this->_database->escapeFieldName($this->dataObject->getTableKeyField())
                , $this->dataObject->getFieldByAttribute('title')
                , $this->dataObject->getFieldByAttribute('shortDesc')
                //6
                , $this->dataObject->getFieldByAttribute('createdAt')
                , $this->dataObject->getFieldByAttribute('userId')
                , $this->dataObject->getFieldByAttribute('content')
                , $this->dataObject->getFieldByAttribute('imageFile')
                , $users->getTableName()
                //11
                , $this->_database->escapeFieldName($users->getTableKeyField())
                , $this->dataObject->getFieldByAttribute('featuredPos')
        );
        $res = $this->_database->query($sql);

        foreach ($res as $row) {
            $data[] = $row;
        }

        return $data;
    }

}
