<?php

if (!isset($_SESSION['playerid'])) 
{
  header('Location: index.php');
}

if($_SESSION['priv'] != 1)
{
  header('Location: games.php');
}

require_once('config.php');
require_once('database.php');
require_once('game.php');

$date = $_POST['date'];
$timeHr = $_POST['hr'];
$timeMin = $_POST['min'];
$ampm = $_POST['ampm'];
$field = $_POST['field'];
$opponent = $_POST['opponent'];
$type = $_POST['type'];
$isEdit = $_POST['isEdit'];
$id = $_POST['gameid'];

if(empty($date) || empty($field) || empty($opponent))
{
  $_SESSION['error'] = "Date, field, and opponent are required. Please fill in all of these fields and resubmit.";
}

if(!empty($timeHr))
{
  if($ampm == "PM" && $timeHr != 12)
  {
    $timeHr += 12;
  }
  else if ($ampm == "AM" && $timeHr == 12)
  {
    $timeHr -= 12;
  }
}

list($month, $day, $year) = explode('/', $date);

$time = "";
if(!empty($timeHr))
{
  $time = date("Y-m-d H:i:s", mktime($timeHr, $timeMin, 0, $month, $day, $year));
}
else 
{
  $time = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, $day, $year));
}

if(!$isEdit)
{
  addGame($time, $field, $opponent, $type);
}
else 
{
  updateGame($id, $time, $field, $opponent, $type);
}

header('Location: games.php');

?>
