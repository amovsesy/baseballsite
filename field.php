<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('database.php');
require_once('constants.php');

$fields = array();


$insertFieldSQL = "insert into field (name, address) values ";
$updateFieldSQL = "update field set ";
$updateFieldSQL2 = " where id = ";
$selectSQLField = "select * from field order by name asc";
$selectSpecificSQLField = "select * from field where id = ";
$deleteField = "delete from field where id = ";

function addField($name, $address)
{
  global $db, $insertFieldSQL;

  $db->query($insertFieldSQL . "('" . $db->escape_value($name) . "', '" . $db->escape_value($address) . "')");
}

function updateField($id, $name, $address)
{
  global $db, $updateFieldSQL, $updateFieldSQL2;
  
  $db->query($updateFieldSQL . "name = '" . $db->escape_value($name) . "', address = '" . $db->escape_value($address) . "'" . $updateFieldSQL2 . $id);
}

function removeField($id)
{
  global $db, $deleteField;
  
  $db->query($deleteField . $id);
}

function getFields()
{
  global $db, $selectSQLField;

  unset($GLOBALS['fields']);
  $GLOBALS['fields'] = array();
  $res = $db->query($selectSQLField);

  while($row = $db->getRows($res))
  {
    $GLOBALS['fields'][$row['id']]['id'] = $row['id'];
    $GLOBALS['fields'][$row['id']]['name'] = $row['name'];
    $GLOBALS['fields'][$row['id']]['address'] = $row['address'];
  }
}

function getField($id)
{
  global $db, $selectSpecificSQLField;

  unset($GLOBALS['fields']);
  $GLOBALS['fields'] = array();
  $res = $db->query($selectSpecificSQLField . $id);

  while($row = $db->getRows($res))
  {
    $GLOBALS['fields'][$row['id']]['id'] = $row['id'];
    $GLOBALS['fields'][$row['id']]['name'] = $row['name'];
    $GLOBALS['fields'][$row['id']]['address'] = $row['address'];
  }
}

?>