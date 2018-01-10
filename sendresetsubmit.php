<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('player.php');

$email = $_POST['email'];

if(empty($email))
{
  $_SESSION['error'] = "Please enter an email and resubmit.";
  header('Location: sendreset.php');
}

resetPassword($email);

?>