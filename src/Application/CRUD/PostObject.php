<?php

declare(strict_types=1);

namespace App\Application\CRUD;

use App\Application\CRUD\CRUDObject;
use Exception;

/**
 * @property int $id 
 * @property string $title
 * @property string $shortDesc
 * @property string $featuredPos
 * @property string $createdAt
 * @property string $content
 * @property string $imageFile
 * @property string $lastUpdated
 * @property string $userId
 */
class PostObject extends CRUDObject {

    const TABLENAME = 'posts';
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
        'title' => array('attribute' => 'title', 'type' => 'string'),
        'userid' => array('attribute' => 'userId', 'type' => 'int', 'canNotChange' => true),
        //'categoryid' => array('attribute' => 'categoryId', 'type' => 'int'),
        'imagefile' => array('attribute' => 'imageFile', 'type' => 'string'),
        'created' => array('attribute' => 'createdAt', 'type' => '@now()', 'canNotChange' => true),
        'lastupdated' => array('attribute' => 'lastUpdated', 'type' => '@now()'),
        'shortdesc' => array('attribute' => 'shortDesc', 'type' => 'string'),
        'content' => array('attribute' => 'content', 'type' => 'string'),
        'featuredpos' => array('attribute' => 'featuredPos', 'type' => 'int'),
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
        'title' => array('notEmptyValidator'),
        'content' => 'notEmptyValidator',
        'imageFile' => 'notEmptyValidator',
        'shortDesc' => 'notEmptyValidator',
        'userId' => 'notEmptyValidator'
    );

    /**
     * attribute=>preprocessor method
     * @var array
     */
     protected array $_setterPreprocessors = array(
         'imageFile'=>'preprocessImage'
     );

    /**
     * if value contains binary save it to file and return file path :)
     * @param type $value
     */
    protected function preprocessImage($value) {
        $valHdr=substr($value,0,50);
        if (str_contains($valHdr, 'data:image/')&&str_contains($valHdr, 'base64')) {
            $com= strpos($valHdr,',');
            if ($com>0) {
                $value= substr($value,$com+1,strlen($value));
                global $publicRootDir;
                if (!is_dir($publicRootDir.'/images/upload')) {
                    mkdir($publicRootDir.'/images/upload');
                    chmod($publicRootDir.'/images/upload',0666);
                }
                
                $fileName='upload/'.str_replace(' ','',microtime(false)).'.jpg';
                $f= fopen($publicRootDir.'/images/'.$fileName,'wb');
                fwrite($f, base64_decode($value));
                fclose($f);
                return $fileName;
            }
        }
        
        if (empty($value)) {
            $value='NoImage.png';
        }
       return $value;//tbd 
    }
    
    protected function postProccess() {
        if ($this->featuredPos>0) {
            $id= $this->id;
            
            $sql= sprintf('update %1$s set %2$s=0 where %2$s=%3$d and %4$s<>%5$s'
                    , $this->_database->escapeTableName($this->_TABLENAME)
                    , $this->getFieldByAttribute('featuredPos')
                    , $this->featuredPos
                    , $this->getEscapedTableKeyField()
                    , $this->getEscapedTableKeyValue()
                    );
            
            $this->_database->execute($sql);
        }
    }
    
}
