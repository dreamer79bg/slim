<?php

declare(strict_types=1);

namespace App\Application\CRUD;

use App\CoreComponents\DatabaseConnection;
use Exception;
use App\CoreComponents\DatabaseInterface;

/**
 * A mini ORM class for CRUD objects. Controls CRUD operations of its descendants automatically based on setup
 * protected attributes.
 */
abstract class CRUDObject {

    protected $_TABLENAME = '';
    protected $_REALDELETE = false;
    protected $_DELETEDFIELDNAME = 'deleted';

    /**
     * field name=>array(
     * 'attribute'=>
     * ,'type'=> bool, string, datetime, int, float for mysql function calls such as now() - @now() etc.
     * ,'isPrimary'=>bool/int
     * ,'canNotChange'=>bool/int
     * ,'setterPreprocessor'=>string a method to be called to preprocess the value on set
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
     * array of validator method names - attribute name=>validator method / attribute name=>array(validator method)
     * @var array
     */
    protected array $_validators = array(
    );
    
    /**
     * attribute=>preprocessor method
     * @var array
     */
     protected array $_setterPreprocessors = array();
    
    protected DatabaseInterface $_database;
    protected array $_data = array();
    protected array $_tableKeyAttribute = array(); //attributeName,fieldName,type
    protected array $_attributeTypes = array();
    protected array $_readOnlyAttributes = array();
   
    protected array $_attributeToField = array();

    public function __construct($databaseKey = null) {

        $this->_database = DatabaseConnection::getDatabase();

        reset($this->_dataFields);
        foreach ($this->_dataFields as $fieldName => $attributeData) {
            if (!empty($attributeData['isPrimary'])) {
                $this->_tableKeyAttribute = array($attributeData['attribute'], $fieldName, $attributeData['type']);
            }

            $this->_attributeTypes[$attributeData['attribute']] = $attributeData['type'];
            $this->_attributeToField[$attributeData['attribute']] = $fieldName;

            if (!empty($attributeData['canNotChange'])) {
                $this->_readOnlyAttributes[$attributeData['attribute']] = 1;
            }

            if (!empty($attributeData['setterPreprocessor'])) {
                $this->_setterPreprocessors[$attributeData['attribute']] = $attributeData['setterPreprocessor'];
            }
        }

        if ($databaseKey === null) {
            $this->clearData();
        } else {
            $this->read($databaseKey);
        }
    }

    /**
     * Returns the object table name
     * @return string
     */
    public function getTableName(): string {
        return $this->_TABLENAME;
    }

    /**
     * Returns an extra statement fof filtering soft deleted records
     * @param string $alias alias of the table if used in the statement
     * @return string
     */
    public function getDeletedWhere(string $alias = ''): string {
        return $this->_REALDELETE ? '' : sprintf(' and %2$s%1$s=0 ', $this->_DELETEDFIELDNAME
                        , !empty($alias) ? $this->_database->escapeFieldName($alias) . '.' : ''
        );
    }

    /**
     * Return field name by attribute to a data service
     * @param string $attributeName
     * @return string
     */
    public function getFieldByAttribute(string $attributeName, $escape=true): string {
        if (empty($attributeName) || !isset($this->_attributeToField[$attributeName])) {
            return null;
        }
        if ($escape) {
            return $this->_database->escapeFieldName($this->_attributeToField[$attributeName]);
        } else {
            return $this->_attributeToField[$attributeName];
        }
    }

    /**
     * Clears the current object data
     */
    public function clearData() {
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

    /**
     * Returns the escaped name of the index field of the table
     * @return string
     */
    protected function getEscapedTableKeyField(): string {
        return $this->_database->escapeFieldName($this->_tableKeyAttribute[1]);
    }

    /**
     * Returns the name of the index field of the table
     * @return string
     */
    public function getTableKeyField(): string {
        return $this->_tableKeyAttribute[1];
    }

    /**
     * Returns the escaped value of the current table key(primary index field)
     * @return mixed
     */
    protected function getEscapedTableKeyValue() {
        return $this->getEscapedAttributeValue($this->_tableKeyAttribute[0]);
    }

    /**
     * Returns the value of the current table key(primary index field)
     * @return mixed
     */
    protected function getTableKeyValue() {
        return $this->_data[$this->_tableKeyAttribute[0]];
    }

    /**
     * returns the escaped value of and attribute.
     * 
     * @param type $attributeName
     * @return string
     * @throws Exception
     */
    protected function getEscapedAttributeValue($attributeName): string {

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

    /**
     * Returns an escaped value based on attribute specification
     * @param type $attributeName
     * @param type $attributeValue
     * @return string
     * @throws Exception
     */
    protected function getEscapedValueByAttribute($attributeName, $attributeValue): string {

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

    /**
     * Fills the object with data by id(primary key value) or throws an exception
     * @param mixed $id
     * @throws Exception
     */
    public function read($id = null) {
        $this->clearData();

        if ($id !== null) {
            $sql = sprintf('select * from %1$s where %2$s=%3$s %4$s'
                    , $this->_database->escapeTableName($this->_TABLENAME)
                    , $this->getEscapedTableKeyField()
                    , $this->getEscapedValueByAttribute($this->_tableKeyAttribute[0], $id)
                    , !$this->_REALDELETE ? sprintf(' and %s=0 ', $this->_database->escapeFieldName($this->_DELETEDFIELDNAME)) : '');
            $result = $this->_database->query($sql);
            $foundData = 0;

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

                $foundData = 1;
                break;
            }

            if (!$foundData) {
                throw new Exception('Item ' . $id . ' not found');
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

    /**
     * Save the record in the database
     * @throws Exception
     */
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
                if (empty($attributeData['canNotChange']) || empty($id) || !empty($attributeData['isPrimary'])) {
                    if (empty($attributeData['isPrimary'])) {
                        $field = $this->_database->escapeFieldName($fieldName);
                        $sqlFields[] = $field;
                        $val = $this->getEscapedAttributeValue($attributeData['attribute']);
                        $sqlData[] = $val;
                        $updateData[] = $field . '=values(' . $field . ')';
                    } else {
                        //primary key is considered to not allow null
                        if (!is_object($this->_data[$attributeData['attribute']]) && $this->_data[$attributeData['attribute']] !== null) {
                            $field = $this->_database->escapeFieldName($fieldName);
                            $sqlFields[] = $field;
                            $val = $this->getEscapedAttributeValue($attributeData['attribute']);
                            $sqlData[] = $val;
                            $updateData[] = $field . '=values(' . $field . ')';
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
            
            $this->postProccess();

            $this->_database->commit();
        } catch (\Exception $e) {
            $this->_database->rollback();
            throw new Exception('Database error :' . $e->getMessage());
        }
    }

    /**
     * Postprocessing hook
     * Example: Change featuredPos on posts requires unsetting all others
     */
    protected function postProccess() {
        
    }
    
    /**
     * Used to set id if needed
     * @param type $id
     * @throws Exception
     */
    public function setId($id) {
        if (empty($this->_data[$this->_tableKeyAttribute[0]])) {
            $this->_data[$this->_tableKeyAttribute[0]] = $id;
        } else {
            throw new \Exception('Can not change primary key of existing objects!');
        }
    }

    /**
     * Returns the id(primary key attribute) of the record
     * @return type
     */
    public function getId() {
        return $this->_data[$this->_tableKeyAttribute[0]];
    }

    /**
     * Full validation of the object data. Calls all validators and returns if they have failed or not.
     * @return bool
     */
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

    /**
     * Validate a single attribute of the data object calling all validators for the attribute.
     * @param string $attributeName
     * @param type $value
     * @return bool
     */
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

    /**
     * delete the record from the database;
     */
    public function delete() {
        $id = $this->getTableKeyValue();
        $fieldName = $this->getEscapedTableKeyField();
        if (!empty($id)) {
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

        if (isset($this->_setterPreprocessors[$name])) {
            $value = $this->{$this->_setterPreprocessors[$name]}($value);
        }

        $this->_data[$name] = $value;
    }

    /**
     * Validates a field that should not be empty
     * @param type $value
     * @return bool
     */
    protected function notEmptyValidator($value): bool {
        return !empty($value);
    }

}
