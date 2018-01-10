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

require_once('player.php');

$id = $_SESSION['playerid'];

getSinglePlayer($id);

if(!empty($players[$id]['img']) && !unlink($players[$id]['img']))
{
  error_log("file " . $players[$id]['img'] . " was not deleted.", 1, "sfmaddogslogs@gmail.com");
}

addImage($id, "");

header('Location: profile.php');

?>
