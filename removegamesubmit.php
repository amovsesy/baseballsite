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

require_once('game.php');
require_once('stats.php');
require_once('player.php');

$id = $_GET['id'];

getGame($id);
list($year, $month, $day, $hr, $min, $sec) = explode('[-: ]', $games[$id]['scheduledate']);

getPlayers($year);

foreach($players as $player)
{
  removeCareerSeasonStatsForGame($id, $player['id'], $year, $games[$id]['type']);
}

removeGame($id);

header('Location: games.php');

?>
