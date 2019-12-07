<?php
/** -------------------------------------------------------------------------------------*
* Version: 2.0                                                                           *
* License: Free to use                               *
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman                                                                *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/

abstract class Model {
  private $tableDefinition = null;
  private $info = array();

  /**
  * Abstract function must be implement by the child class.
  */
  abstract function getTableDefinition();
  
  /**
  * construct the model and call the table definition to be loaded.
  */
  public function __construct(Database $db = null) {
    $this->tableDefinition = $this->getTableDefinition();
    if($this->tableDefinition == null) {
      throw new Exception('Can not load table definition.');
    }
    
    if($db != null) {
      $db->createTable($this->tableDefinition);
    }
  }
  
  /**
  * Magic __get return a property value
  * @param string $name
  * @return mixed
  */
  public function __get($name) {
    $name = strtolower($name);
    $pk = $this->tableDefinition->getPrimaryKeyName();
    if($pk == $name) {
      if(isset($this->info[$name])) {
        return $this->info[$name];
      }
      
      return null;
    }

    $value = null;
    $flag = true;
    foreach($this->info as $key => $val) {
      if($key == $name) {
        $value = $val;
        $flag = false;
        break;
      }
    }
    
    if($flag) {
      throw new Exception(get_class($this)." does not have property ".$name);
    }
    
    return $value;
  }

  /**
  * Magic __set. Set a property value.
  * @param string $name
  * @param mixed $value
  */
  public function __set($name, $value) {
    $name = strtolower($name);
    $pk = $this->tableDefinition->getPrimaryKeyName();
    if($pk == $name) {
      throw new Exception('Primary key can not be set from property.');
    }
    
    $columns = $this->tableDefinition->getColumns();
    $flag = true;
    foreach($columns as $c) {
      if($c['name'] == $name) {
        $this->info[$name] = $value;
        $flag = false;        
        break;
      } 
    }

    // Try with foreign keys
    if($flag) {
      $foreign_keys = $this->tableDefinition->getForeignKeys();
      if(in_array($name, array_map("strtolower", array_values($foreign_keys)))) {
        $this->info[$name] = $value;
        $flag = false;
      }
    }
      
    if($flag) {
      throw new Exception(get_class($this)." does not have property ".$name);
    }
  }

  /**
  * Only Database will use this function.
  * Never try from anywhere else. I said never.
  */
  public function setPrimaryKey($value) {
    if(empty($value)) {
      throw new Exception('Primary key value can not be empty');
    }
    
    $this->info[$this->tableDefinition->getPrimaryKeyName()] = $value;
  }

  /**
   * Only should be used from ServiceModel class.
   * @param type $info
   */
  public function setInfo($info) {
    $this->info = $info;
  }
  
  /**
   * Used it from IService classes.
   * Return the info array
   */
  public function getInfo() {
    return $this->info;
  }
  
  /**
   * Return all the properties that are only be used by this model object.
   * Excluding the indexes of the foreign keys.
   * @return array
   */
  public function getAllProperties() {
    $foreign_keys = array_map("strtolower", array_values($this->tableDefinition->getForeignKeys()));
    $info = array();
    foreach($this->info as $key => $val) {
      if(in_array($key, $foreign_keys) != true) {
        $info[$key] = $val;
      }
    }
    return $info;
  }
  
  /**
   * Cascade delete.
   * @param Database $db
   * @throws Exception
   */
  public function cascadeDelete(Database $db) {
    if(!$db->isInTransaction()) {
      $db->startTransaction();
    }
    
    try {
      $table_list = $db->getAllTableNames();
      foreach($table_list as $table) {
        $class_name = ucfirst($table);
        $obj = new $class_name();
        $foreign_keys = $obj->getTableDefinition()->getForeignKeys();
        $allow_cascade = $obj->getTableDefinition()->getForeignKeysCascadeStatus();
        foreach($foreign_keys as $id => $model_name) {
          if(strtolower($model_name) == $this->tableDefinition->getTableName()) {
            // Here we need to know, if the column was nullable.
            foreach($obj->getTableDefinition()->getColumns() as $col) {
              if($col['name'] == $id && $col['non_null'] == true && 
                $allow_cascade[$id] == true) {
                // Then delete
                $primary_key = $this->tableDefinition->getPrimaryKeyName();
                $db->setDeletedAll($class_name, sql("$id = %s", $this->$primary_key));
                break;
                
              } else if($col['name'] == $id && $col['non_null'] == false && 
                $allow_cascade[$id] == true) {
                // Then set null.
                $primary_key = $this->tableDefinition->getPrimaryKeyName();
                $db->setNullAll($class_name, $id, sql("$id = %s", $this->$primary_key));
                break;
                
              } else if($col['name'] == $id && $allow_cascade[$id] == false) {
                // Here we should throw exception. Because, this object can not be 
                // deleted since it is forbidden in reference object 
                // But we should only throw exception when the object is found in 
                // the reference data list.
                $primary_key = $this->tableDefinition->getPrimaryKeyName();
                $count = $db->countAll($class_name, sql("$id = %s", $this->$primary_key));
                if($count > 0) {
                  throw new Exception($model_name." is not allowed to be deleted in $class_name");
                }
              }
            }
            // Foreign key matches this object. So no need to loop again.
            break;
          }
        }
      }
      
      $this->deleted = true;
      $db->update($this);
      
      if($db->isInTransaction()) {
        $db->commit();
      }
    } catch (Exception $e) {
      if($db->isInTransaction()) {
        $db->rollback();
      }
      throw $e; 
    }
  }
}
?>
