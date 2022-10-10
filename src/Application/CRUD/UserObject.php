<?php

declare(strict_types=1);

namespace App\Application\CRUD;

use App\Application\CRUD\CRUDObject;
use Exception;

/**
 * @property int $id 
 * @property string $userName
 * @property string $fullName
 * @property string $password
 * @property string $createdAt
 */
class UserObject extends CRUDObject {

    const TABLENAME = 'users';
    const REALDELETE = false;
    const DELETEDFIELDNAME = 'deleted';

    public function __construct($databaseKey = null) {
        $this->_TABLENAME = self::TABLENAME;
        $this->_REALDELETE = self::REALDELETE;
        $this->_DELETEDFIELDNAME = self::DELETEDFIELDNAME;

        parent::__construct($databaseKey);
    }

    /**
     * field=>array(
     * 'attribute'=>
     * ,'type'=> bool, string, datetime, int, float for mysql function calls such as now() - @now() etc.
     * ,'isPrimary'=>bool/int
     * ,'canNotChange'=>bool/int
     * ) for table fields
     * 
     * @var array
     */
    protected array $_dataFields = array(
        'id' => array('attribute' => 'id', 'type' => 'int', 'isPrimary' => true, 'canNotChange' => true),
        'created' => array('attribute' => 'createdAt', 'type' => '@now()', 'canNotChange' => true),
        'username' => array('attribute' => 'userName', 'type' => 'string', 'canNotChange' => true),
        'fullname' => array('attribute' => 'fullName', 'type' => 'string'),
        'password' => array('attribute' => 'password', 'type' => 'string', 'setterPreprocessor' => 'encryptPassword'),
    );

    /**
     * name=>array(class,id field) for links by other fields
     * name=>class - for links by id
     * 
     * @var array
     */
    protected array $_nestedObjects = array(
    );

    /**
     * array of validator method names - fieldname=>validator method / fieldname=>array(validator method)
     * @var array
     */
    protected array $_validators = array(
        'userName' => array('userNameValidator', 'notEmptyValidator'),
        'fullName' => 'notEmptyValidator',
        'password' => 'notEmptyValidator',
    );

    protected function encryptPassword($value) {
        return sha1($value);
    }

    protected function userNameValidator($value): bool {
        $id=$this->getEscapedTableKeyValue();
        //check for same user name and different id
        $sql = sprintf('select %3$s from %1$s where %5$s=%6$s and %2$s=0 and %3$s%4$s limit 1'
                , $this->_database->escapeTableName($this->_TABLENAME)
                , $this->_database->escapeFieldName($this->_DELETEDFIELDNAME)
                , $this->getEscapedTableKeyField()
                , $id!='null'?'<>'.$id:' is not null'
                , $this->_attributeToField['userName']
                //6
                , $this->_database->fullEscape($this->_data['userName'])
        );
        
        $res = $this->_database->query($sql);
        $found = 0;

        foreach ($res as $row) {
            $found = 1;
        }

        return $found != 1;
    }

}
