<?php
/** -------------------------------------------------------------------------------------*
* Version: 1.2                                                                           *
* License: Free to use                               *
* ---------------------------------------------------------------------------------------*
* DEVELOPED BY                                                                           *
* Mohammad Hafijur Rahman                                                                *
* mail4hafij@yahoo.com, mail4hafij@gmail.com                                             *
* ------------------------------------------------------------------------------------ **/

class Database {
  private $link = null;
  private $db = null;
  private $alterTable = true;
  private $queryLog = array();
  private $inTransaction = false;

  /**
  * Public constructor. Connect with the mysql database by using mysql_connect
  * @param string $host
  * @param string $db
  * @param string $username
  * @param string $pass
  */
  public function __construct($host, $db, $username, $pass){
    if(empty($host) || empty($db) || empty($username)) {
      throw new Exception('Missing information to connect to database.');
    }
    
    $this->link = mysqli_connect($host, $username, $pass, $db);
    if(mysqli_connect_errno()) {
      throw new Exception("Can not select database ". $db);
    }
    
    $this->db = $db;
  }

  /**
  * Responsible for creating/altering table from a table definition.
  * @param TableDefinition $table
  * @return void.
  */
  public function createTable(TableDefinition $table) {
    if(!$this->isTableExist($table->getTableName())) {
      $sql = sprintf('CREATE TABLE %s(%s INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(%s))',
                      self::wrapName($table->getTableName()),
                      self::wrapName($table->getPrimaryKeyName()),
                      self::wrapName($table->getPrimaryKeyName())
             );
      
      $this->query($sql);
      $this->addColumns($table);
      $this->addUniqueKeys($table);
      //$this->addTriggers($table);
      
    } else if($this->alterTable) {
      $this->addColumns($table);
      $this->addUniqueKeys($table);
      //$this->addTriggers($table);
    }
  }

  /**
  * private function. Can only be used by createTable
  * Responsible for creating column from a table definition.
  * @param TableDefinition $table
  */
  private function addColumns(TableDefinition $table) {
    $table_name = $table->getTableName();
    $columns = $table->getColumns();

    foreach($columns as $column) {
      if(!$this->isColumnExist($table_name, $column['name'])){
        // Now we will check if the column type is nonNull or can
        // accept null.        
        $nonNull = $column['non_null'];
        if($nonNull) {
          if($column['default'] === null) {
            $sql = sprintf('ALTER TABLE %s ADD COLUMN %s %s NOT NULL',
                        self::wrapName($table_name),
                        self::wrapName($column['name']),
                        $column['type']);
          } else {
            $wrap_default = $column['wrap_default'];
            $default = $column['default'];
            if($wrap_default) {
              $default = self::wrapValue($default);
            }

            $sql = sprintf('ALTER TABLE %s ADD COLUMN %s %s NOT NULL DEFAULT %s',
                        self::wrapName($table_name),
                        self::wrapName($column['name']),
                        $column['type'],
                        $default);
          }
        } else {
          if($column['default'] === null) {
            $sql = sprintf('ALTER TABLE %s ADD COLUMN %s %s NULL',
                        self::wrapName($table_name),
                        self::wrapName($column['name']),
                        $column['type']
                   );
          } else {
            // Now some time we dont want to wrap the value for default.
            // like for TIMESTAMP colum type if we want the default value
            // CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP then we should
            // not wrap the value.
            $wrap_default = $column['wrap_default'];
            $default = $column['default'];
            if($wrap_default) {
              $default = self::wrapValue($default);
            }

            $sql = sprintf('ALTER TABLE %s ADD COLUMN %s %s DEFAULT %s',
                          self::wrapName($table_name),
                          self::wrapName($column['name']),
                          $column['type'],
                          $default);
          }
        }
        
        $this->query($sql);
      }
    }

    $columns[] = array('name' => $table->getPrimaryKeyName());
    $cols = $this->getAllColumnNames($table_name);
    foreach($cols as $c) {
      $flag = true;
      foreach($columns as $column) {
        if($c == $column['name']) {
          $flag = false;
          break;
        }
      }

      if($flag) {
        $sql = sprintf('ALTER TABLE %s DROP COLUMN %s',
                        self::wrapName($table_name),
                        self::wrapName($c)
               );
        $this->query($sql);
      }
    }
  }

  /**
  * private function. Can only be used by createTable
  * Responsible for creating unique key constraints.
  * @param TableDefinition $table
  */
  private function addUniqueKeys(TableDefinition $table) {
    $table_name = $table->getTableName();
    $uniques = $table->getUniqueKeys();
    foreach($uniques as $u) {
      $name = $u['name'];
      $cols = $u['cols'];
      if(!$this->isConstraintExist($table_name, $name)) {
        $columns = "";
        foreach($cols as $c) {
          $columns = $columns. self::wrapName($c) . ", ";
        }
        $columns = rtrim($columns, ", ");
        if(!empty($columns)) {
          $sql = sprintf('ALTER TABLE %s ADD CONSTRAINT %s UNIQUE (%s)',
                          self::wrapName($table_name),
                          self::wrapName($name),
                          $columns
                 );
          $this->query($sql);
        }
      }
    }
    $un = $this->getAllConstraintNames($table_name);
    foreach($un as $u) {
      $flag = true;
      foreach($uniques as $uniq){
        if($u == $uniq['name']) {
          $flag = false;
          break;
        }
      }
      if($flag && $u != 'PRIMARY') {
        $sql = sprintf('ALTER TABLE %s DROP INDEX %s',
                        self::wrapName($table_name),
                        self::wrapName($u)
               );
        $this->query($sql);
      }
    }
  }

  /**
   * Not implemented yet.
   * @param TableDefinition $table
   * @return void
   */
  private function addTriggers(TableDefinition $table) {
    return;
    $table_name = $table->getTableName();
    $triggers = $table->getTriggers();
    foreach($triggers as $t) {
      $name = $t['name'];
      $time = $t['before'] ? 'BEFORE' : 'AFTER';
      $event = $t['event'];
      $sql = $t['sql'];
    }
  }

  /**
  * runs mysql_query and add the query to the query log.
  * @param string $sql
  * @exception if the given query is empty.
  * @return resultset array
  */
  public function query($sql) {
    if(empty($sql)) {
      throw new Exception('Query can not be empty');
    }
    
    $this->queryLog[] = $sql;
    $result =  mysqli_query($this->link, $sql);
    
    if(!$result) {
      throw new Exception(mysqli_error($this->link));
    }
    return $result;
  }

  /**
  * return all the table name from the database.
  * @return array
  */
  public function getAllTableNames() {
    $sql = sprintf('SHOW TABLES FROM %s', $this->getDbName());
    $list = $this->query($sql);
    
    $tables = array();
    while($row = mysqli_fetch_array($list)) {
      $tables[] = $row[0];
    }
    return $tables;
  }

  /**
  * Return all the column names of the table.
  * @param string $table_name
  * @exception if the table name does not exist.
  * @return array
  */
  public function getAllColumnNames($table_name) {
    if(!$this->isTableExist($table_name)) {
      throw new Exception('Table does not exist.');
    }
    
    $sql = sprintf('SHOW COLUMNS FROM %s', $table_name);
    $list = $this->query($sql);
    
    $columns = array();
    while($row = mysqli_fetch_array($list)) {
      $columns[] = $row[0];
    }
    return $columns;
  }

  /**
  * Return all the constraints name and type.
  * @param string $table_name
  * @exception if the table does not exist.
  * @return array
  */
  public function getAllConstraintNames($table_name) {
    if(!$this->isTableExist($table_name)) {
      throw new Exception('Table does not exist.');
    }
    
    $sql = sprintf('SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE
                    TABLE_SCHEMA = schema() AND TABLE_NAME = %s',
                    self::wrapValue($table_name)
           );

    $rs = $this->query($sql);
    $constraint = array();
    while($row = mysqli_fetch_array($rs)) {
      $constraint[] = $row[2];
    }
    return $constraint;
  }

  /**
  * Return the database name.
  * @return string
  */
  public function getDbName() {
    return $this->db;
  }

  /**
  * Check whether the table exist in the database or not.
  * @param string $table_name
  * @exception if the given table name is empty.
  * @return bool
  */
  public function isTableExist($table_name) {
    if(empty($table_name)) {
      throw new Exception('Table name can not be empty');
    }
    
    $tables = $this->getAllTableNames();
    return in_array($table_name, $tables);
  }

  /**
  * Check whether the column exist in the table or not.
  * @param string $table_name
  * @param string $column_name
  * @exception if the given table name or column name is empty.
  * @return bool
  */
  public function isColumnExist($table_name, $column_name) {
    if(empty($table_name) || empty($column_name)) {
      throw new Exception('Table or column name can not be empty');
    }
    
    $columns = $this->getAllColumnNames($table_name);
    return in_array($column_name, $columns);
  }

  /**
  * Return whether the constraint exist or not.
  * @param string $table_name
  * @param string $constraint_name
  * @exception if the table or constraint name is empty
  * @return bool
  */
  public function isConstraintExist($table_name, $constraint_name) {
    if(empty($table_name) || empty($constraint_name)) {
      throw new Exception('Table or constraint name can not be empty');
    }
    
    $constraints = $this->getAllConstraintNames($table_name);
    return in_array($constraint_name, $constraints);
  }

  /**
  * start a mysql transaction.
  * @throws Exception if the transaction already been started.
  */
  public function startTransaction() {
    if($this->inTransaction) {
      throw new Exception('Can not start new transaction.');
    }
    
    $this->query("SET AUTOCOMMIT=0");
    $this->query('START TRANSACTION');
    $this->inTransaction = true;
  }

  /**
  * commit a mysql transaction.
  * @throws Exception if no transaction has been started.
  */
  public function commit() {
    if(!$this->inTransaction) {
      throw new Exception('Can not commit without a transaction.');
    }
    
    $this->query('COMMIT');
    $this->query("SET AUTOCOMMIT=1");
    $this->inTransaction = false;
  }

  /**
  * rollback a mysql transaction.
  * @throws Exception if no transaction has been started.
  */
  public function rollback() {
    if(!$this->inTransaction) {
      throw new Exception('Can not rollback without a transaction.');
    }
    
    $this->query('ROLLBACK');
    $this->query("SET AUTOCOMMIT=1");
    $this->inTransaction = false;
  }

  /**
  * Return if we have any transaction open or not.
  * @return bool
  */
  public function isInTransaction() {
    return $this->inTransaction;
  }

  /**
  * if $bool true then the table will be altered every time
  * when we make any changes to the tabledefinition.
  * @param bool $bool
  */
  public function setAlterTable($bool = true) {
    $this->alterTable = $bool;
  }

  /**
  * return a quoted name. We use the quoted name for
  * the table or column name.
  * @param string $name
  * @return string
  */
  public static function wrapName($name) {
    return '`'.$name.'`';
  }

  /**
  * Return a qouted value string. We use this quoted string when
  * we insert data to the database.
  * @param string $value
  * @return string
  */
  public static function wrapValue($value) {
    return is_null($value) ? 'NULL' : "'".addslashes($value)."'"; 
  }

  /**
  * Depricated. Will be removed in the next version.
  * ------------------------------------------------
  * Escape special character in a given string
  * @param string $str
  * @return string
  */
  public static function escapeString($str) {
    // return mysql_real_escape_string($str);
    return addslashes($str);
  }

  /**
  * Deprecated. Will be removed in the next version.
  * ------------------------------------------------
  * Escape html special characters.
  * @param string $html
  * @return string
  */
  public static function escapeHTML($html) {
    return htmlspecialchars($html);
  }

  /**
  * List of $class_name objects
  * @param string $class_name
  * @param string | array $where
  * @param string $orderBy
  * @param int $page
  * @param int $limit
  * @return List of $class_name objects
  */
  public function loadAll($class_name, $where = null, $orderBy = null,
                                 $page = 1, $limit = null) {
    $obj = new $class_name();
    $table_name = $obj->getTableDefinition()->getTableName();
    
    $where_clause = "";
    if(is_string($where)) {
      $where_clause = "WHERE ".$where;
      
    } else if(is_array($where)) {
      foreach($where as $key => $value) {
        $where_clause = $where_clause.self::wrapName($key)." = ".
        self::wrapValue($value)." AND ";
      }

      if(!empty($where_clause)) {
        $where_clause = "WHERE ".rtrim($where_clause, 'AND ');
      }
      
    } else if(empty($where)) {
      // its ok.
    } else {
      throw new Exception('WHERE clause is not valid.');
    }


    $orderby_clause = "";
    if(is_string($orderBy)) {
      $orderby_clause = "ORDER BY ".$orderBy;
    } else if(empty($orderBy)) {
      // its ok.
    } else {
      throw new Exception('ORDER BY clause is not valid.');
    }

    $limit_clause = "";
    if(!empty($limit)) {
      $offset = ($page - 1) * $limit;
      $limit_clause = "LIMIT ".$offset.", ".$limit;
    }

    $sql = sprintf('SELECT * FROM %s %s %s %s',self::wrapName($table_name),
    $where_clause, $orderby_clause, $limit_clause);
    $rs = $this->query($sql);
    $list = $this->rsToObjects($class_name, $rs);
    return $list;
  }

  /**
   * Not recommended. Very bad performance.
   * @param type $class_name
   * @param type $where
   * @param type $orderBy
   * @param type $page
   * @param type $limit
   * @return type
   * @throws Exception
   */
  public function loadAllMany($class_name, $where = null, $orderBy = null,
                                 $page = 1, $limit = null) {
    $obj = new $class_name();
    $table_name = $obj->getTableDefinition()->getTableName();
    
    $where_clause = "";
    if(is_string($where)) {
      $where_clause = "WHERE ".$where;
      
    } else if(is_array($where)) {
      foreach($where as $key => $value) {
        $where_clause = $where_clause.self::wrapName($key)." = ".
        self::wrapValue($value)." AND ";
      }

      if(!empty($where_clause)) {
        $where_clause = "WHERE ".rtrim($where_clause, 'AND ');
      }
      
    } else if(empty($where)) {
      // its ok.
    } else {
      throw new Exception('WHERE clause is not valid.');
    }


    $orderby_clause = "";
    if(is_string($orderBy)) {
      $orderby_clause = "ORDER BY ".$orderBy;
    } else if(empty($orderBy)) {
      // its ok.
    } else {
      throw new Exception('ORDER BY clause is not valid.');
    }

    $limit_clause = "";
    if(!empty($limit)) {
      $offset = ($page - 1) * $limit;
      $limit_clause = "LIMIT ".$offset.", ".$limit;
    }

    $sql = sprintf('SELECT * FROM %s %s %s %s',self::wrapName($table_name),
    $where_clause, $orderby_clause, $limit_clause);
    $rs = $this->query($sql);
    $list = $this->rsToManyObjects($class_name, $rs);
    return $list;
  }
  
  /**
   * inner join.
   * @param type $class_name
   * @param type $where
   * @param type $orderBy
   * @param type $page
   * @param type $limit
   * @return type
   * @throws Exception
   */
  public function loadAllJoin($class_name, $where = null, $orderBy = null,
                                 $page = 1, $limit = null) {
    $obj = new $class_name();
    $table_name = $obj->getTableDefinition()->getTableName();
    $table_name_clause = self::wrapName($table_name);
    
    $where_clause = "";
    if(is_string($where)) {
      $where_clause = "WHERE ".$where;
      
    } else if(is_array($where)) {
      foreach($where as $key => $value) {
        $where_clause = $where_clause.self::wrapName($key)." = ".
        self::wrapValue($value)." AND ";
      }

      if(!empty($where_clause)) {
        $where_clause = "WHERE ".rtrim($where_clause, 'AND ');
      }
      
    } else if(empty($where)) {
      // its ok.
    } else {
      throw new Exception('WHERE clause is not valid.');
    }

    
    $foreign_keys = $obj->getTableDefinition()->getForeignKeys();
    foreach($foreign_keys as $id => $model_name) {
      $model_name = strtolower($model_name);
      $table_name_clause = $table_name_clause.", ".self::wrapName($model_name);
      $where_clause = $where_clause." AND ".$model_name.".".$model_name."_id = ".strtolower($class_name).".".$id;
    }
    
    
    $orderby_clause = "";
    if(is_string($orderBy)) {
      $orderby_clause = "ORDER BY ".$orderBy;
    } else if(empty($orderBy)) {
      // its ok.
    } else {
      throw new Exception('ORDER BY clause is not valid.');
    }

    $limit_clause = "";
    if(!empty($limit)) {
      $offset = ($page - 1) * $limit;
      $limit_clause = "LIMIT ".$offset.", ".$limit;
    }

    $col_name_clause = $obj->getTableDefinition()->getColNameClause();
    
    $sql = sprintf('SELECT %s FROM %s %s %s %s', $col_name_clause, 
      $table_name_clause, $where_clause, $orderby_clause, $limit_clause);
    
    $rs = $this->query($sql);
    $list = $this->rsToJoinObjects($class_name, $rs);
    return $list;
  }
  
  
  /**
  * Return a $class_name object
  * @param string $class_name
  * @param string | array $where
  * @param string $orderBy
  * @return $class_name objects || null if the object can not be found.
  */
  public function loadOnly($class_name, $where = null, $orderBy = null) {
    $list = $this->loadAll($class_name, $where, $orderBy, 1, 1);
    if(empty($list)) {
      throw new Exception($class_name . " was not found");
    }
    
    return $list[0];
  }
  
  /**
  * Return an object of type $class_name
  * @param string $class_name
  * @param int $id
  * @return $class_name object
  */
  public function loadById($class_name, $id) {
    if(empty($id)) {
      throw new Exception('Id can not be empty');
    }
    
    $obj = new $class_name();
    $pk = $obj->getTableDefinition()->getPrimaryKeyName();
    $table_name = $obj->getTableDefinition()->getTableName();
    $sql = sprintf('SELECT * FROM %s WHERE %s = %s', self::wrapName($table_name),
                    self::wrapName($pk), self::wrapValue($id)
           );
    
    $rs = $this->query($sql);
    $list = $this->rsToObjects($class_name, $rs);
    if(count($list) != 1) {
      throw new Exception($class_name . " with id #".$id." was not found");
    }
    
    $obj = $list[0];
    return $obj;
  }

  /**
   * Return the total number of objects with the given where clause.
   * @param string $class_name
   * @param string | array $where
   * @return int
   */
  public function countAll($class_name, $where) {
    $obj = new $class_name();
    $table_name = $obj->getTableDefinition()->getTableName();

    $where_clause = "";
    if(is_string($where)) {
      $where_clause = "WHERE ".$where;

    } else if(is_array($where)) {
      foreach($where as $key => $value) {
        $where_clause = $where_clause.self::wrapName($key)." = ".
        self::wrapValue($value)." AND ";
      }

      if(!empty($where_clause)) {
        $where_clause = "WHERE ".rtrim($where_clause, 'AND ');
      }
      
    } else if(empty($where)) {
      // its ok.
    } else {
      throw new Exception('WHERE clause is not valid.');
    }


    $sql = sprintf('SELECT COUNT(*) c FROM %s %s',self::wrapName($table_name), $where_clause);
    $rs = $this->query($sql);
    $row = mysqli_fetch_array($rs);
    return $row['c'];
  }
  
  
  public function countAllJoin($class_name, $where) {
    $obj = new $class_name();
    $table_name = $obj->getTableDefinition()->getTableName();
    $table_name_clause = self::wrapName($table_name);

    $where_clause = "";
    if(is_string($where)) {
      $where_clause = "WHERE ".$where;

    } else if(is_array($where)) {
      foreach($where as $key => $value) {
        $where_clause = $where_clause.self::wrapName($key)." = ".
        self::wrapValue($value)." AND ";
      }

      if(!empty($where_clause)) {
        $where_clause = "WHERE ".rtrim($where_clause, 'AND ');
      }
      
    } else if(empty($where)) {
      // its ok.
    } else {
      throw new Exception('WHERE clause is not valid.');
    }

    $foreign_keys = $obj->getTableDefinition()->getForeignKeys();
    foreach($foreign_keys as $id => $model_name) {
      $model_name = strtolower($model_name);
      $table_name_clause = $table_name_clause.", ".self::wrapName($model_name);
      $where_clause = $where_clause." AND ".$model_name.".".$model_name."_id = ".strtolower($class_name).".".$id;
    }

    $sql = sprintf('SELECT COUNT(*) c FROM %s %s', $table_name_clause, $where_clause);
    $rs = $this->query($sql);
    $row = mysqli_fetch_array($rs);
    return $row['c'];
  }
  
  
  /**
  *
  * @param string $class_name
  * @param array $rs
  * @return List of object type $class_name
  */
  public function rsToObjects($class_name, $rs) {
    $list = array();
    while($row = mysqli_fetch_array($rs)) {
      $obj = new $class_name();
      $pk = $obj->getTableDefinition()->getPrimaryKeyName();
      $obj->setPrimaryKey($row[$pk]);
      $cols = $obj->getTableDefinition()->getColumns();
      foreach($cols as $c) {
        $obj->$c['name'] = $row[$c['name']];
      }
      $list[] = $obj;
    }
    return $list;
  }
  
  /**
   * Not recommended. Very bad performance.
   * @param type $class_name
   * @param type $rs
   * @return \class_name
   */
  public function rsToManyObjects($class_name, $rs) {
    $list = array();
    while($row = mysqli_fetch_array($rs)) {
      $obj = new $class_name();
      $pk = $obj->getTableDefinition()->getPrimaryKeyName();
      $obj->setPrimaryKey($row[$pk]);
      $cols = $obj->getTableDefinition()->getColumns();
      foreach($cols as $c) {
        $obj->$c['name'] = $row[$c['name']];
      }
      
      $foreign_keys = $obj->getTableDefinition()->getForeignKeys();
      foreach($foreign_keys as $id => $model_name) {
        if($obj->$id != null) {
          $model_name_id = strtolower($model_name)."_id";
          $obj->$model_name = $this->loadOnly($model_name, 
            sprintf("$model_name_id = %s", self::wrapValue($obj->$id))); 
        } else {
          $obj->$model_name = null;
        }
      }
      
      $list[] = $obj;
    }
    return $list;
  }
  
  /**
   * Inner join
   * @param type $class_name
   * @param type $rs
   * @return \class_name
   */
  public function rsToJoinObjects($class_name, $rs) {
    $list = array();
    while($row = mysqli_fetch_array($rs)) {
      $obj = new $class_name();
      $table_name = $obj->getTableDefinition()->getTableName();
      $pk = $obj->getTableDefinition()->getPrimaryKeyName();
      $obj->setPrimaryKey($row[$table_name."_".$pk]);
      $cols = $obj->getTableDefinition()->getColumns();
      foreach($cols as $c) {
        $obj->$c['name'] = $row[$table_name."_".$c['name']];
      }
      
      $foreign_keys = array_values($obj->getTableDefinition()->getForeignKeys());
      foreach($foreign_keys as $model_name) {
        $model = new $model_name();
        $table_name = $model->getTableDefinition()->getTableName();
        $pk = $model->getTableDefinition()->getPrimaryKeyName();
        $model->setPrimaryKey($row[$table_name."_".$pk]);
        $cols = $model->getTableDefinition()->getColumns();
        foreach($cols as $c) {
          $model->$c['name'] = $row[$table_name."_".$c['name']];
        }
        $obj->$model_name = $model;
      }
      
      $list[] = $obj;
    }
    return $list;
  }

  /**
  * Insert the model to the database and return its id.
  * @param Model $model
  * @return int id of this new inserted model.
  */
  public function store(Model $model) {
    $table_name = $model->getTableDefinition()->getTableName();
    $pk = $model->getTableDefinition()->getPrimaryKeyName();
    $properties = $model->getAllProperties();
    $col = "";
    $val = "";
    
    foreach($properties as $prop => $value) {
      if($prop != $pk) {
        $col = $col.self::wrapName($prop).", ";
        $val = $val.self::wrapValue($value).", ";
      }
    }
    
    $col = rtrim($col, ", ");
    $val = rtrim($val, ", ");
    $sql = sprintf('INSERT INTO %s (%s) VALUES(%s)', self::wrapName($table_name),$col, $val);
    $this->query($sql);
    $lastId = mysqli_insert_id($this->link);
    if(empty($lastId)) {
      throw new Exception('Could not store object.');
    }
    
    return $lastId;
  }

  /**
  * Update the model in database.
  * @param Model $model
  */
  public function update(Model $model) {
    $table_name = $model->getTableDefinition()->getTableName();
    $pk = $model->getTableDefinition()->getPrimaryKeyName();
    $pk_value = null;
    $properties = $model->getAllProperties();
    $update = "";

    foreach($properties as $prop => $value) {
      if($prop != $pk) {
        $update = $update . self::wrapName($prop) . " = " . self::wrapValue($value) . ", ";
      } else {
        $pk_value = $value;
      }
    }
    
    if(empty($pk_value)) {
      throw new Exception('Primary key value not found.');
    }
    $update = rtrim($update, ", ");
      
    if(!empty($update)) {
      $sql = sprintf('UPDATE %s SET %s WHERE %s = %s',
                      self::wrapName($table_name),
                      $update,
                      self::wrapName($pk),
                      self::wrapValue($pk_value));
      
      $this->query($sql);
    }
  }

  /**
   * Delete all the rows with the given where condition.
   * @param string $class_name
   * @param array | string $where
   */
  public function deleteAll($class_name, $where = null) {
    $obj = new $class_name();
    $table_name = $obj->getTableDefinition()->getTableName();

    $where_clause = "";
    if(is_string($where)) {
      $where_clause = "WHERE ".$where;

    } else if(is_array($where)) {
      foreach($where as $key => $value) {
        $where_clause = $where_clause . self::wrapName($key) . " = " .
        self::wrapValue($value) . " AND ";
      }

      if(!empty($where_clause)) {
        $where_clause = "WHERE " . rtrim($where_clause, 'AND ');
      }
      
    } else if(empty($where)) {
      // its ok.
    } else {
      throw new Exception('WHERE clause is not valid.');
    }

    $sql = sprintf('DELETE FROM %s %s', 
                    self::wrapName($table_name), 
                    $where_clause);
    
    $this->query($sql);
  }
  
  /**
   * Set deleted column to true.
   * @param type $class_name
   * @param type $where
   * @throws Exception
   */
  public function setDeletedAll($class_name, $where = null) {
    $obj = new $class_name();
    $table_name = $obj->getTableDefinition()->getTableName();

    $where_clause = "";
    if(is_string($where)) {
      $where_clause = "WHERE ".$where;

    } else if(is_array($where)) {
      foreach($where as $key => $value) {
        $where_clause = $where_clause . self::wrapName($key) . " = " .
        self::wrapValue($value) . " AND ";
      }

      if(!empty($where_clause)) {
        $where_clause = "WHERE " . rtrim($where_clause, 'AND ');
      }
      
    } else if(empty($where)) {
      // its ok.
    } else {
      throw new Exception('WHERE clause is not valid.');
    }

    $sql = sprintf('UPDATE %s SET deleted = 1 %s',
                      self::wrapName($table_name),
                      $where_clause);
    
    $this->query($sql);
  }
  
  /**
   * Set a specific column to null.
   * @param type $class_name
   * @param type $where
   * @throws Exception
   */
  public function setNullAll($class_name, $column_name, $where = null) {
    $obj = new $class_name();
    $table_name = $obj->getTableDefinition()->getTableName();

    $where_clause = "";
    if(is_string($where)) {
      $where_clause = "WHERE ".$where;

    } else if(is_array($where)) {
      foreach($where as $key => $value) {
        $where_clause = $where_clause . self::wrapName($key) . " = " .
        self::wrapValue($value) . " AND ";
      }

      if(!empty($where_clause)) {
        $where_clause = "WHERE " . rtrim($where_clause, 'AND ');
      }
      
    } else if(empty($where)) {
      // its ok.
    } else {
      throw new Exception('WHERE clause is not valid.');
    }
      
    $sql = sprintf('UPDATE %s SET %s = %s %s',
                      self::wrapName($table_name),
                      self::wrapName($column_name),
                      self::wrapValue(null),
                      $where_clause);
    
    $this->query($sql);
  }
  
  /**
  * Return the query log.
  * @return array
  */
  public function getQueryLog() {
    return $this->queryLog;
  }
}
?>