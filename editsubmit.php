<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('database.php');
require_once('player.php');


$fname = $_POST['firstname'];
$lname = $_POST['lastname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$ft = $_POST['heightFT'];
$in = $_POST['heightIN'];
$height = $ft * 12 + $in;
$weight = $_POST['weight'];
$positions = $_POST['positions'];
$pos = ":";
$privilege = 2;

while (list ($key,$val) = @each ($positions))
{
  $pos .= $val . ":";
  if($val = "Manager")
  {
    $privilege = 1;
  }
}

$bats = $_POST['bats'];
$throws = $_POST['throws'];
$birth = $_POST['birthdate'];
$nick = $_POST['nickname'];

$birthdate = "";

if(!empty($birth))
{
  list($month, $day, $year) = explode('/', $birth);
  $birthdate = date("Y-m-d H:i:s", mktime(1, 0, 0, $month, $day, $year));
}

if(empty($fname) || empty($lname) || empty($email))
{
  $_SESSION['error'] = "First name, last name, and email are required. Please enter all required fields and resubmit.";
  header('Location: edit.php');
}

updatePlayer($fname, $lname, $email, $phone, $height, $weight, $pos, $bats, $throws,$nick, $birthdate, $_SESSION['playerid'],$privilege);

header('Location: profile.php');

?>
