<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('database.php');
require_once('constants.php');
require_once('field.php');

$upcominggames = array();
$games = array();
$gameids = array();
$gameImages = array();
$gameYears = array();
$gameImage = "";
$upResults = 0;
$gameResults = 0;


$insertSQLGame = "insert into games (scheduledate, fieldid, opponent, type) values ";
$insertSQLGameNoField = "insert into games (scheduledate, opponent, type) values ";
$selectSQLDone = "select * from games where DATE_ADD(scheduledate, INTERVAL 3 HOUR) <  NOW() and YEAR(scheduledate) = YEAR(CURDATE()) order by scheduledate desc limit ";
$selectSQLDoneByYear = "select * from games where DATE_ADD(scheduledate, INTERVAL 3 HOUR) <  NOW() and YEAR(scheduledate) = ";
$selectSQLDoneByYear2 = " order by scheduledate desc limit ";
$selectDistinctYears = "select distinct YEAR(scheduledate) as year from games where YEAR(scheduledate) > 2010 order by year desc";
$selectSQLUp = "select * from games where DATE_ADD(scheduledate, INTERVAL 3 HOUR) >=  NOW() order by scheduledate asc limit ";
$selectSQLDoneCount = "select count(*) from games where DATE_ADD(scheduledate, INTERVAL 3 HOUR) <  NOW() and YEAR(scheduledate) = ";
$selectSQLUpCount = "select count(*) from games where DATE_ADD(scheduledate, INTERVAL 3 HOUR) >=  NOW()";
$selectSpecificSQLGame = "select * from games where id = ";
$updateGameSQL = "update games set ";
$updateGameSQL2 = " where id = ";
$deleteGame = "delete from games where id = ";

function addGame($time, $field, $opponent, $type)
{
  global $db, $insertSQLGame, $insertSQLGameNoField;

  if($field != "tbd")
  {
    $db->query($insertSQLGame . "('" . $db->escape_value($time) . "', " . $db->escape_value($field) . ", '" . $db->escape_value($opponent) . "', '" . $db->escape_value($type) . "')");
  }
  else 
  {
    $db->query($insertSQLGameNoField . "('" . $db->escape_value($time) . "', '" . $db->escape_value($opponent) . "', '" . $db->escape_value($type) . "')");
  }
}

function updateGame($id, $time, $field, $opponent, $type)
{
  global $db, $updateGameSQL, $updateGameSQL2;
  
  if($field != "tbd")
  {
    $db->query($updateGameSQL . " scheduledate = '" . $db->escape_value($time) . "', fieldid = " . $db->escape_value($field) . ", opponent = '" . $db->escape_value($opponent) . "', type = '" . $db->escape_value($type) . "'" . $updateGameSQL2 . $id);
  }
  else 
  {
    $db->query($updateGameSQL . " scheduledate = '" . $db->escape_value($time) . "', fieldid = 0" . ", opponent = '" . $db->escape_value($opponent) . "', type = '" . $db->escape_value($type) . "'" . $updateGameSQL2 . $id);
  }
}

function removeGame($id)
{
  global $db, $deleteGame;
  
  $db->query($deleteGame . $id);
}

function getGames($max, $page)
{
  global $db,$selectSQLDone, $selectSQLUp;

  unset($GLOBALS['games']);
  $GLOBALS['games'] = array();
  
  unset($GLOBALS['upcominggames']);
  $GLOBALS['upcominggames'] = array();
  
  unset($GLOBALS['upResults']);
  $GLOBALS['upResults'] = 0;
  
  unset($GLOBALS['gameResults']);
  $GLOBALS['gameResults'] = 0;
  
  $j = $max * ($page-1);
  $res = $db->query($selectSQLDone . $j . ", " . $max);
  $res2 = $db->query($selectSQLUp . $j . ", " . $max);

  if($db->num_rows($res) > 0)
  {
    while($row = $db->getRows($res))
    {
      $GLOBALS['games'][$row['id']]['id'] = $row['id'];
      $GLOBALS['games'][$row['id']]['scheduledate'] = $row['scheduledate'];
      $GLOBALS['games'][$row['id']]['opponent'] = $row['opponent'];
      $GLOBALS['games'][$row['id']]['result'] = $row['result'];
      $GLOBALS['games'][$row['id']]['type'] = $row['type'];
      $GLOBALS['games'][$row['id']]['score'] = $row['score'];
      $GLOBALS['games'][$row['id']]['fieldid'] = $row['fieldid'];

      if(!empty($row['fieldid']))
      {
        getField($row['fieldid']);
       
        $GLOBALS['games'][$row['id']]['field'] = $GLOBALS['fields'][$row['fieldid']]['name'];
        $GLOBALS['games'][$row['id']]['address'] = $GLOBALS['fields'][$row['fieldid']]['address'];
      }
      else 
      {
        $GLOBALS['games'][$row['id']]['field'] = "TBD";
      }
    }
  }
  
  if($db->num_rows($res2) > 0)
  {
    while($row = $db->getRows($res2))
    {
      $GLOBALS['upcominggames'][$row['id']]['id'] = $row['id'];
      $GLOBALS['upcominggames'][$row['id']]['scheduledate'] = $row['scheduledate'];
      $GLOBALS['upcominggames'][$row['id']]['opponent'] = $row['opponent'];
      $GLOBALS['upcominggames'][$row['id']]['type'] = $row['type'];
      $GLOBALS['upcominggames'][$row['id']]['fieldid'] = $row['fieldid'];

      if(!empty($row['fieldid']))
      {
        getField($row['fieldid']);
         
        $GLOBALS['upcominggames'][$row['id']]['field'] = $GLOBALS['fields'][$row['fieldid']]['name'];
        $GLOBALS['upcominggames'][$row['id']]['address'] = $GLOBALS['fields'][$row['fieldid']]['address'];
      }
      else 
      {
        $GLOBALS['upcominggames'][$row['id']]['field'] = "TBD";
      }
    }
  }
}

function getUpcomingGames($max, $page, $gameYear)
{
  global $db, $selectSQLUp, $selectSQLUpCount;
  
  unset($GLOBALS['upcominggames']);
  $GLOBALS['upcominggames'] = array();
  
  unset($GLOBALS['upResults']);
  $GLOBALS['upResults'] = 0;
  
  $count2 = $db->query($selectSQLUpCount);
  
  if($db->num_rows($count2) > 0)
  {
    while($row = $db->getRows($count2))
    {
      $GLOBALS['upResults'] = $row['count(*)'];
    }
  }
  
  $j = $max * ($page-1);
  $res2 = $db->query($selectSQLUp . $j . ", " . $max);
  
  if($db->num_rows($res2) > 0)
  {
    while($row = $db->getRows($res2))
    {
      $GLOBALS['upcominggames'][$row['id']]['id'] = $row['id'];
      $GLOBALS['upcominggames'][$row['id']]['scheduledate'] = $row['scheduledate'];
      $GLOBALS['upcominggames'][$row['id']]['opponent'] = $row['opponent'];
      $GLOBALS['upcominggames'][$row['id']]['type'] = $row['type'];
      $GLOBALS['upcominggames'][$row['id']]['fieldid'] = $row['fieldid'];
       
      if(!empty($row['fieldid']))
      {
        getField($row['fieldid']);
         
        $GLOBALS['upcominggames'][$row['id']]['field'] = $GLOBALS['fields'][$row['fieldid']]['name'];
        $GLOBALS['upcominggames'][$row['id']]['address'] = $GLOBALS['fields'][$row['fieldid']]['address'];
      }
      else 
      {
        $GLOBALS['upcominggames'][$row['id']]['field'] = "TBD";
      }
    }
  }
}

function getGameYears()
{
  global $db, $selectDistinctYears;
  
  unset($GLOBALS['gameYears']);
  $GLOBALS['gameYears'] = array();
  
  $years = $db->query($selectDistinctYears);
  while($row = $db->getRows($years))
  {
    $GLOBALS['gameYears'][$row['year']]['year'] = $row['year'];
  }
}

function getDoneGames($max, $page, $gameYear)
{
  global $db,$selectSQLDoneByYear, $selectSQLDoneByYear2, $selectSQLDoneCount;

  unset($GLOBALS['games']);
  $GLOBALS['games'] = array();
  
  unset($GLOBALS['gameResults']);
  $GLOBALS['gameResults'] = 0;
  
  $count = $db->query($selectSQLDoneCount . $gameYear);
  
  if($db->num_rows($count) > 0)
  {
    while($row = $db->getRows($count))
    {
      $GLOBALS['gameResults'] = $row['count(*)'];
    }
  }
  
  $j = $max * ($page-1);
  $res = $db->query($selectSQLDoneByYear . $gameYear . $selectSQLDoneByYear2 . $j . ", " . $max);

  if($db->num_rows($res) > 0)
  {
    while($row = $db->getRows($res))
    {
      $GLOBALS['games'][$row['id']]['id'] = $row['id'];
      $GLOBALS['games'][$row['id']]['scheduledate'] = $row['scheduledate'];
      $GLOBALS['games'][$row['id']]['opponent'] = $row['opponent'];
      $GLOBALS['games'][$row['id']]['result'] = $row['result'];
      $GLOBALS['games'][$row['id']]['score'] = $row['score'];
      $GLOBALS['games'][$row['id']]['type'] = $row['type'];
      $GLOBALS['games'][$row['id']]['stats'] = unserialize($row['stats']);
      $GLOBALS['games'][$row['id']]['fieldid'] = $row['fieldid'];
       
      if(!empty($row['fieldid']))
      {
        getField($row['fieldid']);
         
        $GLOBALS['games'][$row['id']]['field'] = $GLOBALS['fields'][$row['fieldid']]['name'];
        $GLOBALS['games'][$row['id']]['address'] = $GLOBALS['fields'][$row['fieldid']]['address'];
      }
      else 
      { 
        $GLOBALS['games'][$row['id']]['field'] = "TBD";
      }
    }
  }
}

function getGame($id)
{
  global $db, $selectSpecificSQLGame;

  unset($GLOBALS['games']);
  $GLOBALS['games'] = array();
  $res2 = $db->query($selectSpecificSQLGame . $id);

  if($db->num_rows($res2) > 0)
  {
    while($row = $db->getRows($res2))
    {
      $GLOBALS['games'][$row['id']]['id'] = $row['id'];
      $GLOBALS['games'][$row['id']]['scheduledate'] = $row['scheduledate'];
      $GLOBALS['games'][$row['id']]['opponent'] = $row['opponent'];
      $GLOBALS['games'][$row['id']]['result'] = $row['result'];
      $GLOBALS['games'][$row['id']]['score'] = $row['score'];
      $GLOBALS['games'][$row['id']]['type'] = $row['type'];
      $GLOBALS['games'][$row['id']]['stats'] = unserialize($row['stats']);
      $GLOBALS['games'][$row['id']]['fieldid'] = $row['fieldid'];
       
      if(!empty($row['fieldid']))
      {
        getField($row['fieldid']);
         
        $GLOBALS['games'][$row['id']]['field'] = $GLOBALS['fields'][$row['fieldid']]['name'];
        $GLOBALS['games'][$row['id']]['address'] = $GLOBALS['fields'][$row['fieldid']]['address'];
      }
      else 
      {
        $GLOBALS['games'][$row['id']]['field'] = "TBD";
      }
    }
  }
}

function addImageToGame($id, $img)
{
  global $db;
  $insert = "insert into gameimages (gameid, img) values (" . $id . ", '" . $db->escape_value($img) . "')";
  
  $res = $db->query($insert);
}

function getGamesWithPictures($year)
{
  global $db;
  
  unset($GLOBALS['gameids']);
  $GLOBALS['gameids'] = array();
  
  $selectDistinct = "select distinct gameid, scheduledate from gameimages, games where gameid = id and YEAR(scheduledate) = " . $year . " order by scheduledate desc";
  $res = $db->query($selectDistinct);
  
  if($db->num_rows($res) > 0)
  {
    while($row = $db->getRows($res))
    {
      $GLOBALS['gameids'][$row['gameid']]['id'] = $row['gameid'];
    }
  }
}

function getFirstPicOfGame($id)
{
  global $db;
  
  unset($GLOBALS['gameImage']);
  $GLOBALS['gameImage'] = "";
  
  $selectFirst = "select * from gameimages where gameid = " . $id . " limit 0,1";
  $res = $db->query($selectFirst);
  
  if($db->num_rows($res) > 0)
  {
    while($row = $db->getRows($res))
    {
      $GLOBALS['gameImage'] = $row['img'];
    }
  }
}

function getPicsOfGame($id)
{
  global $db;
  
  unset($GLOBALS['gameImages']);
  $GLOBALS['gameImages'] = array();
  
  $selectAll = "select * from gameimages where gameid = " . $id;
  $res = $db->query($selectAll);
  
  if($db->num_rows($res) > 0)
  {
    $i = 1;
    while($row = $db->getRows($res))
    {
      $GLOBALS['gameImages'][$i]['img'] = $row['img'];
      $i++;
    }
  }
}

function getRandomPic()
{
  global $db;
  
  unset($GLOBALS['gameImages']);
  $GLOBALS['gameImages'] = array();
  
  $count = 0;
  $countSel = $db->query("select count(*) from gameimages");
  
  if($db->num_rows($countSel) > 0)
  {
    while($row = $db->getRows($countSel))
    {
      $count = $row['count(*)'];
    }
  }
  
  $rand = rand(0, $count);
  $getRandom = $db->query("select * from gameimages limit " . $rand . ",1");
  
  if($db->num_rows($getRandom) > 0)
  {
    $i = 1;
    while($row = $db->getRows($getRandom))
    {
      $GLOBALS['gameImages'][$i]['img'] = $row['img'];
      $i++;
    }
  }
}

?>