<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

if (!isset($_SESSION['playerid'])) 
{
  header('Location: index.php');
}

if($_SESSION['priv'] != 1)
{
  header('Location: games.php');
}

require_once('field.php');

$id = $_GET['id'];

removeField($id);

header('Location: allfields.php');

?>