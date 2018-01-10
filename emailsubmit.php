<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

$isLoggedIn = false;

require_once('config.php');
require_once('database.php');
require_once('player.php');

getPlayers(date("Y"));

$headers = "From: ";
$email = "";
$name = $_POST['name'];
$person = $_POST['toPerson'];
$subject = $_POST['subject'];
$message = $_POST['message'];

$to = "";

if (isset($_SESSION['playerid'])) 
{
  $isLoggedIn = true;
  $headers .= $players[$_SESSION['playerid']]['email'];
}
else 
{
  $headers .= $name . "<" . $_POST['email'] . ">";
}

if($person == "all")
{ 
  foreach($players as $player)
  {
    $to .= $player['email'] . ",";
  }
  
  $to = substr($to, 0, strlen($to)-1);
}
else if($person == "team")
{
  $to = "sfmaddogs@gmail.com";
}
else
{
  $to = $players[$person]['email'];
}

$mail_sent = @mail( $to, $subject, $message, $headers );

$error = false;

if(!$mail_sent)
{
  $_SESSION['error'] = "There was a problem sending your message. Please try again";
  $error = true;
}

if($error)
{
  header('Location: email.php');
}
else if($isLoggedIn)
{
  header('Location: contact.php');
}
else 
{
  header('Location: index.php');
}
?>