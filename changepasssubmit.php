<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('database.php');
require_once('player.php');

$password = $db->escape_value(md5($_POST['password']));
$newPass = $db->escape_value(md5($_POST['newPassword']));
$confirm = $db->escape_value(md5($_POST['passwordConfirm']));

if($newPass != $confirm)
{
  $_SESSION['error'] = "New password mismatch.";
  header('Location: changepass.php');
}

if(empty($password) || empty($newPass))
{
  $_SESSION['error'] = "Current and new password are required. Please enter these fields and resubmit.";
  header('Location: changepass.php');
}

updatePassword($_SESSION['playerid'], $password, $newPass);

?>