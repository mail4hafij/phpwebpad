<?php
/** -------------------------------------------------------------------------------------*
* Version: 4.0                                                                           *
* framework: https://github.com/mail4hafij/phpwebpad                                     *
* License: Free to use                                                                   *
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman                                                                *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/

/**
 * Define a table.
 */
class TableDefinition {
  private $table_name = null;
  private $columns = array();
  private $unique_keys = array();
  private $foreign_keys = array();
  private $allow_cascade = array();
  private $triggers = array();
  private $auto_increament = 1;

  public function  __construct($table_name) {
    if(empty($table_name)) {
      throw new Exception('Table name can not be empty');
    }
    
    $this->table_name = strtolower($table_name);
    $this->addNonNullColumn('created', 'DATETIME');
    $this->addNonNullColumn('modified', 'TIMESTAMP', 
            'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', null, false);
    $this->addNonNullColumn('deleted', 'BOOL', 0);
  }

  public function setAutoIncreament($inc = 1) {
    if(!is_numeric($inc)) {
      throw new Exception('Auto increamen must be numeric');
    }
    $this->auto_increament = $inc;
  }
  
  public function getAutoIncreament() {
    return $this->auto_increament;
  }
  
  /**
   * @param string $column_name
   * @param string $type
   * @param mixed $default
   * @param string $comment
   * @param bool $wrap_default
   */
  public function addColumn($column_name, $type, $default = null, $comment = null,
                            $wrap_default = true) {
    if(empty($column_name) || empty($type)) {
      throw new Exception('Column name or type can not be empty');
    }
    
    $this->columns[] = array('name'         => strtolower($column_name),
                             'type'         => $type,
                             'default'      => $default,
                             'comment'      => $comment,
                             'wrap_default' => $wrap_default,
                             'non_null'     => false
                        );
  }

  /**
   * @param string $column_name
   * @param string $type
   * @param mixed $default
   * @param string $comment
   * @param bool $wrap_default
   */
  public function addNonNullColumn($column_name, $type, $default = null, $comment = null,
                                   $wrap_default = true) {
    if(empty($column_name) || empty($type)) {
      throw new Exception('Column name or type can not be empty');
    }
    
    $this->columns[] = array('name'         => strtolower($column_name),
                             'type'         => $type,
                             'default'      => $default,
                             'comment'      => $comment,
                             'wrap_default' => $wrap_default ,
                             'non_null'     => true
                       );
  }

  /**
   * @param string $name
   * @param array $columns
   */
  public function addUniqueKey($name, $columns) {
    if(empty($name)) {
      throw new Exception('Unique key name can not be empty');
    }
    
    if(is_array($columns)) {
      $this->unique_keys[] = array('name' => strtolower($name), 'cols' => $columns);
    } else {
      throw new Exception('Columns must be an array');
    }
  }

  /**
   * This framework actually never add foreign key index to the database,
   * rather it manages the foreign keys virtually.
   * @param type $column_name
   * @param type $model_name
   */
  public function addForeignKey($column_name, $model_name, $allow_cascade = true) {
    $this->foreign_keys[$column_name] = $model_name;
    $this->allow_cascade[$column_name] = $allow_cascade;
  }
  
  /**
   * Add a trigger on this table.
   * @param string $name
   * @param bool $before
   * @param string $event
   * @param string $sql
   */
  public function addTrigger($name, $before = true, $event = "INSERT", $sql = null) {
    throw new Exception('not emplemented yet');
    
    if(empty($name)) {
      throw new Exception('Trigger name can not be empty');
    }
    
    if(in_array($event, array('INSERT', 'UPDATE', 'DELETE'))) {
      throw new Exception('Event is not valid');
    }
    
    $this->triggers[] = array('name'    => strtolower($name),
                              'before'  => $before,
                              'event'   => $event,
                              'sql'     => $sql
                        );
  }
  
  /**
   * Return the table name.
   * @return string
   */
  public function getTableName() {
    return $this->table_name;
  }

  /**
   * @return array
   */
  public function getColumns() {
    return $this->columns;
  }
  
  /**
   * Return all the column names in string to perform select operation
   * by the rsToJoinObjects method in Database.
   * @return string
   */
  public function getColNameClause() {
    $cn = $this->table_name;
    $col_name_clause = $cn.".".$cn."_id ".$cn."_".$cn."_id";
    $cols = $this->columns;
    foreach($cols as $c) {
      $col_name_clause = $col_name_clause.", ".$cn.".".$c['name']." ".$cn."_".$c['name'];
    }
    
    $foreign_keys = array_values($this->foreign_keys);
      foreach($foreign_keys as $model_name) {
        $model = new $model_name();
        $model_name = strtolower($model_name);
        $col_name_clause = $col_name_clause.", ".$model_name.".".$model_name."_id ".$model_name."_".$model_name."_id";
        $cols = $model->getTableDefinition()->getColumns();
        foreach($cols as $c) {
          $col_name_clause = $col_name_clause.", ".$model_name.".".$c['name']." ".$model_name."_".$c['name'];
        }
      }
    
    return $col_name_clause;
  }
  
  /**
   * return all the foreign keys
   * @return type
   */
  public function getForeignKeys() {
    return $this->foreign_keys;
  }
  
  /**
   * return all the foreign keys with cascade status
   * @return type
   */
  public function getForeignKeysCascadeStatus() {
    return $this->allow_cascade;
  }
  
  /**
   * @return array
   */
  public function getUniqueKeys() {
    return $this->unique_keys;
  }

  /**
   * @return array
   */
  public function getTriggers() {
    return $this->triggers;
  }
  
  /**
   * return the primary key name for this table definition.
   * @return string.
   */
  public function getPrimaryKeyName() {
    return $this->table_name.'_id';
  }
}
?>
