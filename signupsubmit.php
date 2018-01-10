<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('database.php');
require_once('player.php');

$em = $db->escape_value($_POST['email']);

if(doesUserExist($em))
{
  $_SESSION['error'] = "A user with that email already exists";
  header('Location: signup.php');
}

$fname = $_POST['firstname'];
$lname = $_POST['lastname'];
$email = $_POST['email'];
$password = md5($_POST['password']);
$confirm = md5($_POST['passwordConfirm']);
$bats = $_POST['bats'];
$throws = $_POST['throws'];

if($password != $confirm)
{
  $_SESSION['error'] = "Password mismatch. Please try again.";
  header('Location: signup.php');
}

$phone = $_POST['phone'];
$birth = $_POST['birthdate'];
$nick = $_POST['nickname'];
$signupkey = $_POST['teamPassword'];

$positions = $_POST['positions'];
$pos = "";
$privilege = 2;

if($signupkey != "maddogs2012")
{
  $_SESSION['error'] = "This site is only for SF Mad Dog team members";
  header('Location: index.php');
}
else 
{
  if(empty($fname) || empty($lname) || empty($email) || empty($password))
  {
    $_SESSION['error'] = "First name, last name, email, and password are required. Please fill in at minimum these fields and resubmit.";
    header('Location: signup.php');
  }
  
  if(!empty($positions))
  {
    $pos = ":";
  
    while (list ($key,$val) = @each ($positions))
    {
      $pos .= $val . ":";
      if($val == "Manager")
      {
        $privilege = 1;
      }
    }
  }
  
  $birthdate = "";
  
  if(!empty($birth))
  {
    list($month, $day, $year) = explode('/', $birth);
    $birthdate = date("Y-m-d H:i:s", mktime(1, 0, 0, $month, $day, $year));
  }
  
  $ft = $_POST['heightFT'];
  $in = $_POST['heightIN'];
  $height = $ft * 12 + $in;
  
  $weight = $_POST['weight'];
  if(empty($weight))
  {
    $weight = 0;
  }
  
  addPlayer($fname, $lname, $email, $phone, $height, $weight, $pos, $bats, $throws, $nick, $birthdate, $password, $privilege);
  
  $email = $db->escape_value($_POST['email']);
  $password = $db->escape_value(md5($_POST['password']));
  
  login($email, $password);
}
?>
