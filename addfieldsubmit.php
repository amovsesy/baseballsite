<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

if($_SESSION['priv'] != 1)
{
  header('Location: games.php');
}

require_once('config.php');
require_once('database.php');
require_once('field.php');

$name = $_POST['name'];
$address = $_POST['street'] . ", " . $_POST['city'] . ", CA";
$id = $_POST['fieldid'];

if(empty($name) || empty($_POST['street']) || empty($_POST['city']))
{
  $_SESSION['error'] = "Name, street, and city are required, please fill out these fileds";
  header('Location: addfield.php');
}

if(empty($id))
{
  addField($name, $address);
}
else
{
  updateField($id, $name, $address);
}

header('Location: allfields.php');

?>