<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('database.php');
require_once('player.php');

$email = $db->escape_value($_POST['email']);
$password = $db->escape_value(md5($_POST['password']));

login($email, $password);

?>