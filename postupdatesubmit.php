<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('database.php');
require_once('news.php');

$message = $db->escape_value($_POST['message']);
$id = $_SESSION['playerid'];

if(empty($message))
{
  $_SESSION['error'] = "Please enter a message to submit.";
  header('Location: postupdate.php');
}

addNews($id, $message);

header('Location: index.php');

?>