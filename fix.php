<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

if($_SESSION['priv'] != 1 || $_SESSION['firstname'] != "Aleksandr")
{
  header('Location: index.php');
}

require_once('stats.php');
require_once('player.php');
require_once('constants.php');

getPlayers(date("Y"));

foreach ($players as $player)
{
  addToCareerBattingStats(0, $player['id'], 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, false);
  addToSeasonBattingStats(0, date("Y"), $player['id'], 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, false);
  
  addToCareerPitchingStats(0, $player['id'], 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, false);
  addToSeasonPitchingStats(0, date("Y"), $player['id'], 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, false);
  
  addCarrerStatsToDB($player['id']);
  addSeasonStatsToDB($player['id'], date("Y"));
}

?>
