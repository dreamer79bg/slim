<?php

declare(strict_types=1);

namespace App\Application\CRUD;

use App\CoreComponents\MySQLDatabase as Database;
use Exception;

abstract class CRUDObject {

    protected $_TABLENAME = '';
    protected $_REALDELETE = false;
    protected $_DELETEDFIELDNAME = 'deleted';

    /**
     * name=>array(
     * 'attribute'=>
     * ,'type'=> bool, string, datetime, int, float for mysql function calls such as now() - @now() etc.
     * ,'isPrimary'=>bool/int
     * ,'canNotChange'=>bool/int
     * ) for table fields
     * 
     * @var array
     */
    protected array $_dataFields = array(
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
    protected Database $_database;
    protected array $_data = array();
    protected array $_tableKeyAttribute = array(); //attributeName,fieldName,type
    protected array $_attributeTypes = array();
    protected array $_readOnlyAttributes = array();

    public function __construct($databaseKey = null) {
        $this->_database = new Database();

        reset($this->_dataFields);
        foreach ($this->_dataFields as $fieldName => $attributeData) {
            if (!empty($attributeData['isPrimary'])) {
                $this->_tableKeyAttribute = array($attributeData['attribute'], $fieldName, $attributeData['type']);
            }

            $this->_attributeTypes[$attributeData['attribute']] = $attributeData['type'];
            if (!empty($attributeData['canNotChange'])) {
                $this->_readOnlyAttributes[$attributeData['attribute']] = 1;
            }
        }

        if ($databaseKey === null) {
            $this->clearData();
        } else {
            $this->read($databaseKey);
        }
    }

    protected function clearData() {
        $this->_data = array();
        reset($this->_dataFields);
        foreach ($this->_dataFields as $attributeData) {
            $this->_data[$attributeData['attribute']] = null;
        }

        foreach ($this->_nestedObjects as $attributeName => $className) {
            if (is_string($className)) {
                $this->_data[$attributeName] = new $className();
            } else if (is_array($className)) {
                $this->_data[$attributeName] = new $className[0]();
            }
        }
    }

    protected function getEscapedTableKeyField() {
        return $this->_database->escapeFieldName($this->_tableKeyAttribute[1]);
    }

    protected function getEscapedTableKeyValue() {
        return $this->getEscapedAttributeValue($this->_tableKeyAttribute[0]);
    }

    protected function getTableKeyValue() {
        return $this->_data[$this->_tableKeyAttribute[0]];
    }

    protected function getEscapedAttributeValue($attributeName) {

        if (!array_key_exists($attributeName, $this->_data)) {
            throw new \Exception('Attempt to read a non existing attribute');
        }

        $type = $this->_attributeTypes[$attributeName];
        $attributeValue = $this->_data[$attributeName];

        if (substr($type, 0, 1) == '@') {
            $data = substr($type, 1, 1000); //a mysql function call from type 
        } else {
            if ($attributeValue === null) {
                $data = 'null';
            } else {


                switch ($type) {
                    case 'bool':
                        $data = $attributeValue ? '1' : '0';
                        break;
                    case 'string':
                        $data = $this->_database->fullEscape($attributeValue);
                        break;
                    case 'datetime':
                        $data = $this->_database->fullEscape(date('Y-m-d H:i:s', strtotime($attributeValue)));
                        break;
                    case 'int':
                        $data = sprintf('%d', $attributeValue);
                        break;
                    case 'float':
                        $data = sprintf('%f', $attributeValue);
                        break;
                    default:
                        $data = 'null';
                }
            }
        }

        return $data;
    }

    protected function getEscapedValueByAttribute($attributeName, $attributeValue) {

        if (!array_key_exists($attributeName, $this->_data)) {
            throw new \Exception('Attempt to read a non existing attribute');
        }

        $type = $this->_attributeTypes[$attributeName];

        if (substr($type, 0, 1) == '@') {
            $data = substr($type, 1, 1000); //a mysql function call from type 
        } else {
            if ($attributeValue === null) {
                $data = 'null';
            } else {


                switch ($type) {
                    case 'bool':
                        $data = $attributeValue ? '1' : '0';
                        break;
                    case 'string':
                        $data = $this->_database->fullEscape($attributeValue);
                        break;
                    case 'datetime':
                        $data = $this->_database->fullEscape(date('Y-m-d H:i:s', strtotime($attributeValue)));
                        break;
                    case 'int':
                        $data = sprintf('%d', $attributeValue);
                        break;
                    case 'float':
                        $data = sprintf('%f', $attributeValue);
                        break;
                    default:
                        $data = 'null';
                }
            }
        }

        return $data;
    }

    public function read($id = null) {
        $this->clearData();

        if ($id !== null) {
            $sql = sprintf('select * from %1$s where %2$s=%3$s %4$s'
                    , $this->_database->escapeTableName($this->_TABLENAME)
                    , $this->getEscapedTableKeyField()
                    , $this->getEscapedValueByAttribute($this->_tableKeyAttribute[0], $id)
                    , !$this->_REALDELETE ? sprintf(' and %s=0 ', $this->_database->escapeFieldName($this->_DELETEDFIELDNAME)) : '');
            $result = $this->_database->query($sql);
            $foundData= 0;
                    
            foreach ($result as $data) {
                $this->_data = array();
                reset($this->_dataFields);
                foreach ($this->_dataFields as $fieldName => $attributeData) {
                    $attributeName = $attributeData['attribute'];
                    if (!array_key_exists($attributeName, $this->_data)) {
                        $this->_data[$attributeName] = $data[$fieldName];
                    } else {
                        $this->_data[$attributeName] = null;
                    }
                }
                
                $foundData=1;
                break;
            }

            if (!$foundData) {
                throw new Exception('Item '.$id.' not found');
            }
            
            reset($this->_nestedObjects);
            foreach ($this->_nestedObjects as $attributeName => $className) {
                if (is_string($className)) {
                    $this->_data[$attributeName] = new $className($id);
                } else if (is_array($className)) {
                    $this->_data[$attributeName] = new $className[0]($this->_data[$className[1]]);
                }
            }
        }
    }

    public function save() {
        $sqlFields = array();
        $sqlData = array();
        $updateData = array();

        if (!$this->validateData()) {
            throw new Exception('Data is not valid for save');
        }

        $id = $this->getTableKeyValue();

        $this->_database->beginTransaction();
        try {
            reset($this->_dataFields);
            foreach ($this->_dataFields as $fieldName => $attributeData) {
                if (empty($attributeData['canNotChange']) || empty($id)) {
                    if (empty($attributeData['isPrimary'])) {
                        $field=$this->_database->escapeFieldName($fieldName);
                        $sqlFields[] = $this->_database->escapeFieldName($fieldName);
                        $val = $this->getEscapedAttributeValue($attributeData['attribute']);
                        $sqlData[] = $val;
                        $updateData[] = $field.'='.$val;
                    } else {
                        //primary key is considered to not allow null
                        if (!is_object($this->_data[$attributeData['attribute']]) && $this->_data[$attributeData['attribute']] !== null) {
                            $field=$this->_database->escapeFieldName($fieldName);
                            $sqlFields[] = $this->_database->escapeFieldName($fieldName);
                            $val = $this->getEscapedAttributeValue($attributeData['attribute']);
                            $sqlData[] = $val;
                            $updateData[] = $field.'='.$val;
                        }
                    }
                }
            }

            $sql = sprintf('insert into %1$s (%2$s) values (%3$s) on duplicate key update %4$s'
                    , $this->_database->escapeTableName($this->_TABLENAME)
                    , join(',', $sqlFields)
                    , join(',', $sqlData)
                    , join(',', $updateData)
            );

            $this->_database->execute($sql);
            if (empty($this->_data[$this->_tableKeyAttribute[0]])) {
                $this->_data[$this->_tableKeyAttribute[0]] = $this->_database->insertId();
            }

            $id = $this->_data[$this->_tableKeyAttribute[0]];

            //save nested
            reset($this->_dataFields);
            foreach ($this->_dataFields as $fieldName => $attributeData) {
                if (is_object($this->_data[$attributeData['attribute']])) {
                    $this->_data[$attributeData['attribute']]->setId($id);
                    $this->_data[$attributeData['attribute']]->save();
                }
            }

            $this->_database->commit();
        } catch (\Exception $e) {
            $this->_database->rollback();
            throw new Exception('Database error :' . $e->getMessage());
        }
    }

    public function setId($id) {
        if (empty($this->_data[$this->_tableKeyAttribute[0]])) {
            $this->_data[$this->_tableKeyAttribute[0]] = $id;
        } else {
            throw new \Exception('Can not change primary key of existing objects!');
        }
    }

    public function getId() {
        return $this->_data[$this->_tableKeyAttribute[0]];
    }

    public function validateData(): bool {
        $result = true;

        reset($this->_validators);
        foreach ($this->_validators as $attributeName => $validators) {
            $value = $this->_data[$attributeName];

            if (is_array($validators)) {
                reset($validators);
                foreach ($validators as $validator) {
                    $result = $result && $this->$validator($value);
                }
            } else {
                $result = $result && $this->$validators($value);
            }
        }

        return $result;
    }

    protected function validateAttribute(string $attributeName, $value): bool {
        if (isset($this->_validators[$attributeName])) {
            if (is_array($this->_validators[$attributeName])) {
                reset($this->_validators[$attributeName]);
                foreach ($this->_validators[$attributeName] as $validator) {
                    $this->$validator($value);
                }
            } else {
                $this->{$this->_validators[$attributeName]}($value);
            }
        }
    }

    public function delete() {
        $id = $this->getTableKeyValue();
        $fieldName = $this->getEscapedTableKeyField();
        if (!$this->_REALDELETE) {
            $sql = sprintf('update %1$s set %2$s=1 where %3$s=%4$s'
                    , $this->_database->escapeTableName($this->_TABLENAME)
                    , $this->_database->escapeFieldName($this->_DELETEDFIELDNAME)
                    , $this->getEscapedTableKeyField()
                    , $this->getEscapedTableKeyValue()
            );
            $this->_database->execute($sql);
        } else {
            $sql = sprintf('delete from %1$s where %2$s=%3$s'
                    , $this->_database->escapeTableName($this->_TABLENAME)
                    , $this->getEscapedTableKeyField()
                    , $this->getEscapedTableKeyValue()
            );
            $this->_database->execute($sql);
        }
    }

    public function __get($name) {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }
        return null;
    }

    public function __set($name, $value) {
        if (is_object($this->_data[$name])) {
            throw new \Exception('Can not set object attributes (' . $name . ')');
        }

        if (!array_key_exists($name, $this->_data)) {
            var_dump($this->_data);
            throw new \Exception('Can not set attributes not present in data ' . $name . ' ' . $this->_data[$name]);
        }

        if (isset($this->_readOnlyAttributes[$name]) && !empty($this->getTableKeyValue()) || $this->_tableKeyAttribute[0] == $name) {
            throw new \Exception('Can not change read only attributes (' . $name . ')');
        }

        $this->_data[$name] = $value;
    }

}
