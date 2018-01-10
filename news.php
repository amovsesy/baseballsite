<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('database.php');
require_once('constants.php');

$news = array();

function getNews()
{
  global $db;
  
  unset($GLOBALS['news']);
  $GLOBALS['news'] = array();
  $res = $db->query("select * from news order by postdate desc limit 0, 50");
  
  while($row = $db->getRows($res))
  {
    $GLOBALS['news'][$row['id']]['id'] = $row['id'];
    $GLOBALS['news'][$row['id']]['playerid'] = $row['playerid'];
    $GLOBALS['news'][$row['id']]['message'] = $row['message'];
    $GLOBALS['news'][$row['id']]['postdate'] = $row['postdate'];
  }
}

function addNews($id, $message)
{
  global $db;
  
  $db->query("insert into news (playerid, message, postdate) values (" . $id . ", '" . $message . "', NOW())");
}

?>