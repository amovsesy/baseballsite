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
require_once('stats.php');
require_once('player.php');
require_once('game.php');

$gameid = $_POST['gameid'];
$innings = $_POST['innings'];
$isEdit = $_POST['isEdit'];

getGame($gameid);

list($year, $month, $day, $hr, $min, $sec) = explode('[-: ]', $GLOBALS['games'][$gameid]['scheduledate']);

getPlayers($year);

unset($GLOBALS['players_stats']);
$GLOBALS['players_stats'] = array();
unset($box_score);
$game_box_score = array();

for($i=1; $i <= $innings; $i++)
{
  $game_box_score['m'.$i] = $_POST['m'.$i];
  $game_box_score['o'.$i] = $_POST['o'.$i];
}

$game_box_score['mr'] = $_POST['mr'];
$game_box_score['mh'] = $_POST['mh'];
$game_box_score['me'] = $_POST['me'];

$game_box_score['or'] = $_POST['or'];
$game_box_score['oh'] = $_POST['oh'];
$game_box_score['oe'] = $_POST['oe'];
$game_box_score['innings'] = $innings;

gameBoxScore($game_box_score);

foreach($players as $player)
{
  unset($GLOBALS['player_stats']);
  $GLOBALS['player_stats'] = array();
  
  unset($GLOBALS['career_stats']);
  $GLOBALS['career_stats'] = array();
  
  unset($GLOBALS['season_stats']);
  $GLOBALS['season_stats'] = array();
  
  unset($GLOBALS['post_career_stats']);
  $GLOBALS['post_career_stats'] = array();
  
  unset($GLOBALS['post_season_stats']);
  $GLOBALS['post_season_stats'] = array();
  
  $BAB = !empty($_POST['bab'. $player['id']]) ? $_POST['bab'. $player['id']] : 0;
  $BR = !empty($_POST['br'. $player['id']]) ? $_POST['br'. $player['id']] : 0;
  $BH = !empty($_POST['bh'. $player['id']]) ? $_POST['bh'. $player['id']] : 0;
  $BDOUB = !empty($_POST['b2b'. $player['id']]) ? $_POST['b2b'. $player['id']] : 0;
  $BTRIP = !empty($_POST['b3b'. $player['id']]) ? $_POST['b3b'. $player['id']] : 0;
  $BHR = !empty($_POST['bhr'. $player['id']]) ? $_POST['bhr'. $player['id']] : 0;
  $BRBI = !empty($_POST['brbi'. $player['id']]) ? $_POST['brbi'. $player['id']] : 0;
  $BTB = !empty($_POST['btb'. $player['id']]) ? $_POST['btb'. $player['id']] : 0;
  $BSO = !empty($_POST['bso'. $player['id']]) ? $_POST['bso'. $player['id']] : 0;
  $BBB = !empty($_POST['bbb'. $player['id']]) ? $_POST['bbb'. $player['id']] : 0;
  $BSB = !empty($_POST['bsb'. $player['id']]) ? $_POST['bsb'. $player['id']] : 0;
  $BHBP = !empty($_POST['bhbp'. $player['id']]) ? $_POST['bhbp'. $player['id']] : 0;
  $BSF = !empty($_POST['bsf'. $player['id']]) ? $_POST['bsf'. $player['id']] : 0;
  $BCS = !empty($_POST['bcs'. $player['id']]) ? $_POST['bcs'. $player['id']] : 0;
  $PW = ($_POST['pw'. $player['id']] == "Y") ? 1 : 0;
  $PL = ($_POST['pl'. $player['id']] == "Y") ? 1 : 0;
  $PCG = ($_POST['pcg'. $player['id']] == "Y") ? 1 : 0;
  $PSHO = ($_POST['psho'. $player['id']] == "Y") ? 1 : 0;
  $PSV = ($_POST['psv'. $player['id']] == "Y") ? 1 : 0;
  $PSVO = ($_POST['psvo'. $player['id']] == "Y") ? 1 : 0;
  $PIP = !empty($_POST['pip'. $player['id']]) ? $_POST['pip'. $player['id']] : 0;
  $PH = !empty($_POST['ph'. $player['id']]) ? $_POST['ph'. $player['id']] : 0;
  $PER = !empty($_POST['per'. $player['id']]) ? $_POST['per'. $player['id']] : 0;
  $PHR = !empty($_POST['phra'. $player['id']]) ? $_POST['phra'. $player['id']] : 0;
  $PHBP = !empty($_POST['phb'. $player['id']]) ? $_POST['phb'. $player['id']] : 0;
  $PBB = !empty($_POST['pbb'. $player['id']]) ? $_POST['pbb'. $player['id']] : 0;
  $PSO = !empty($_POST['pso'. $player['id']]) ? $_POST['pso'. $player['id']] : 0;
  
  playerGameStats($year, $gameid, $player['id'], $BAB, $BR, $BH, $BDOUB, $BTRIP, $BHR, $BRBI, $BTB, $BSO, $BBB, $BSB, $BHBP, $BSF, $BCS, $PW, $PL, $PCG, $PSHO, $PSV, $PSVO, $PIP, $PH, $PER, $PHR, $PHBP, $PBB, $PSO, ($GLOBALS['games'][$gameid]['type'] == "preseason"), ($GLOBALS['games'][$gameid]['type'] == "postseason"), $isEdit);
  
  if(!empty($GLOBALS['career_stats']))
  {
    addCarrerStatsToDB($player['id']);
  }
  
  if(!empty($GLOBALS['season_stats']))
  {
    addSeasonStatsToDB($player['id'], $year);
  }
  
  if(!empty($GLOBALS['post_career_stats']))
  {
    addPostSeasonCarrerStatsToDB($player['id']);
  }
  
  if(!empty($GLOBALS['post_season_stats']))
  {
    addPostSeasonStatsToDB($player['id'], $year);
  }
}

$result = 'W';

if($game_box_score['or'] > $game_box_score['mr'])
{
  $result = 'L';
}
else if (!empty($game_box_score['or']) && $game_box_score['or'] == $game_box_score['mr'])
{
  $result = 'T';
}
else if (empty($game_box_score['or']) && $game_box_score['or'] != 0)
{
  $result = "-";
}
addGameToDB($gameid, $result, $game_box_score['or'], $game_box_score['mr']);

header('Location: gameresults.php?id=' . $gameid);

?>
