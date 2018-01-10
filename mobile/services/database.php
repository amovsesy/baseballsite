<?php

require_once('config.php');

abstract class Database {
  public $connection;
  public $magic_quotes_active;
  public $real_escape_string_exists;

  abstract function open_connection();
  abstract function close_connection();
  abstract function query($sql);
  abstract function escape_value($value);
  abstract function fetch_array($result);
  abstract function num_rows($result);
  abstract function insert_id();
  abstract function affected_rows();
  abstract function getRows($results);

  public function confirm_query($sql, $result, $error) 
  {
    if(!$result)
    {
      $output = "Database query failed: " . $error;
      if(DEBUG)
      {
        $output = $output . "<br /><br />Failed query: " . $sql;
      }
      
      die($output);
    }
  }

}

class MySqlDatabase extends Database 
{

  function __construct() 
  {
    $this->open_connection();
    $this->magic_quotes_active = get_magic_quotes_gpc();
    $this->real_escape_string_exists = function_exists("mysql_real_escape_string");
  }

  public function open_connection() 
  {
    if(!isset($this->connection))
    {
      $this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS);
      
      if(!$this->connection)
      {
        die("Database connection failed: " . mysql_error());
      } 
      else 
      {
        $db_select = mysql_select_db(DB_NAME, $this->connection);
        
        if(!$db_select)
        {
          die("Database selection failed: " . mysql_error());
        }
      }
    }
  }

  public function close_connection() 
  {
    if(isset($this->connection)) 
    {
      mysql_close($this->connection);
      unset($this->connection);
    }
  }

  public function query($sql) 
  {
    $result = mysql_query($sql, $this->connection);
    $this->confirm_query($sql, $result, mysql_error());
    return $result;
  }

  public function getRows($result) 
  {
    return mysql_fetch_assoc($result);
  }

  public function escape_value($value) 
  {
    if($this->real_escape_string_exists)
    {
      if($this->magic_quotes_active)
      {
        $value = stripcslashes($value);
      }
      	
      $value = mysql_real_escape_string($value);
    } 
    else 
    {
      if(!$this->magic_quotes_active) 
      {
        $value = addslashes($value);
      }
    }

    return $value;
  }

  public function fetch_array($result) 
  {
    return mysql_fetch_array($result);
  }

  public function num_rows($result) 
  {
    return mysql_num_rows($result);
  }

  public function insert_id() 
  {
    return mysql_insert_id($this->connection);
  }

  public function affected_rows() 
  {
    return mysql_affected_rows($this->connection);
  }
}

$db = new MySqlDatabase();

?>