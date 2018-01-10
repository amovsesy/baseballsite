<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('database.php');
require_once('player.php');

$newPass = $db->escape_value(md5($_POST['newPassword']));
$confirm = $db->escape_value(md5($_POST['passwordConfirm']));
$id = $_SESSION['playerID'];
$email = $_SESSION['email'];

if($newPass != $confirm)
{
  $_SESSION['error'] = "Pasword mismatch. Please reenter and submit.";
  header('Location: reset.php');
}

updateResetPass($id, $newPass);

login($email, $newPass);

?>