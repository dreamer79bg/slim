<?php

declare(strict_types=1);

namespace App\Application\CRUD;
use App\Application\CRUD\CRUDObject;

/**
 * @property int $id 
 * @property string $userName
 * @property string $fullName
 * @property string $password
 * @property string $createdAt
 */
class UserObject extends CRUDObject {
    
    const TABLENAME='users';
    const REALDELETE=false;
    const DELETEDFIELDNAME='deleted';
    
    public function __construct($databaseKey = null) {
        $this->_TABLENAME= self::TABLENAME;
        $this->_REALDELETE= self::REALDELETE;
        $this->_DELETEDFIELDNAME= self::DELETEDFIELDNAME;
        
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
        'id'=>array('attribute'=>'id','type'=>'int','isPrimary'=>true,'canNotChange'=>true),
        'created'=>array('attribute'=>'createdAt','type'=>'@now()','canNotChange'=>true),
        'username'=>array('attribute'=>'userName','type'=>'string','canNotChange'=>true),
        'fullname'=>array('attribute'=>'fullName','type'=>'string'),
        'password'=>array('attribute'=>'password','type'=>'string'),
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
    );
}
