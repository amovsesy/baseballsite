<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('database.php');
require_once('constants.php');

$update = "update games set ";
$update2 = " where id = ";

$selectGameStats = "select stats from games where id = ";
$selectPlayerCareerStats = "select stats from player where id = ";
$selectPlayerPostSeasonCareerStats = "select postseasonstats from player where id = ";
$updateCareerStats = "update player set stats = ";
$updatePostSeasonCareerStats = "update player set postseasonstats = ";
$selectPlayerSeasonStats = "select stats from seasonstats where player = ";
$selectPlayerPostSeasonStats = "select stats from postseasonstats where player = ";
$updateSeasonStats = "update seasonstats set stats = ";
$updatePostSeasonStats = "update postseasonstats set stats = ";
$insertSeasonStats = "insert into seasonstats (player, year, stats) values ";
$insertPostSeasonStats = "insert into postseasonstats (player, year, stats) values ";

$batting_stats = array();
$pitching_stats = array();
$fielding_stats = array();
$player_stats = array();
$players_stats = array();
$post_players_stats = array();
$season_players_stats = array();
$post_season_players_stats = array();
$box_score = array();
$stats = array();
$game_stats = array();
$career_stats = array();
$season_stats = array();
$post_career_stats = array();
$post_season_stats = array();
$stat_years = array();

function addGameToDB($gameID, $result, $oppScore, $teamScore)
{
  global $db, $boxString, $game_score, $update, $update2, $statsString;

  unset($GLOBALS['stats']);
  $GLOBALS['stats'] = array();

  $GLOBALS['stats'][$statsString] = $GLOBALS['players_stats'];
  $GLOBALS['stats'][$boxString] = $GLOBALS['box_score'];
  $gameStats = serialize($GLOBALS['stats']);
  error_log("game " . $gameID . " putting game stats " . $gameStats, 1, "sfmaddogslogs@gmail.com");
  $db->query($update . "stats = '" . $db->escape_value($gameStats) . "', result = '" . $result . "', score = '" . $db->escape_value($teamScore) . "-" . $db->escape_value($oppScore) . "'" . $update2 . $gameID);
}

function gameBoxScore($scoreBreakdown)
{
  unset($GLOBALS['box_score']);
  $GLOBALS['box_score'] = array();

  $GLOBALS['box_score'] = $scoreBreakdown;
}

function playerGameStats($year, $gameID, $playerID, $BAB, $BR, $BH, $BDOUB, $BTRIP, $BHR, $BRBI, $BTB, $BSO, $BBB, $BSB, $BHBP, $BSF, $BCS, $PW, $PL, $PCG, $PSHO, $PSV, $PSVO, $PIP, $PH, $PER, $PHR, $PHBP, $PBB, $PSO, $preseason, $postseason, $isEdit)
{
  gameBattingStats($year, $gameID, $playerID, $BAB, $BR, $BH, $BDOUB, $BTRIP, $BHR, $BRBI, $BTB, $BSO, $BBB, $BSB, $BHBP, $BSF, $BCS, $preseason, $postseason, $isEdit);
  
  gamePitchingStats($year, $gameID, $playerID, $PW, $PL, $PCG, $PSHO, $PSV, $PSVO, $PIP, $PH, $PER, $PHR, $PHBP, $PBB, $PSO, $preseason, $postseason, $isEdit);

  $GLOBALS['players_stats'][$playerID] = $GLOBALS['player_stats'];
}

function gameBattingStats($year, $gameID, $playerID, $AB, $R, $H, $DOUB, $TRIP, $HR, $RBI, $TB, $SO, $BB, $SB, $HBP, $SF, $CS, $preseason, $postseason, $isEdit)
{
  global $at_bats, $runs, $hits, $doubles, $triples, $home_runs, $runs_batted_in, $total_bases, $strike_outs, $walks, $stolen_bases;
  global $hit_by_pitch, $sacrifice_flies, $caught_stealing, $on_base_percentage, $slugging_percentage, $batting_average, $batting_stats_string;

  unset($GLOBALS['batting_stats']);
  $GLOBALS['batting_stats'] = array();

  if(!$preseason && !$postseason)
  {
    addToCareerBattingStats($gameID, $playerID, $AB, $R, $H, $DOUB, $TRIP, $HR, $RBI, $TB, $SO, $BB, $SB, $HBP, $SF, $CS, $isEdit);
    addToSeasonBattingStats($gameID, $year, $playerID, $AB, $R, $H, $DOUB, $TRIP, $HR, $RBI, $TB, $SO, $BB, $SB, $HBP, $SF, $CS, $isEdit);
  }
  else if(!$preseason)
  {
    addToPostSeasonCareerBattingStats($gameID, $playerID, $AB, $R, $H, $DOUB, $TRIP, $HR, $RBI, $TB, $SO, $BB, $SB, $HBP, $SF, $CS, $isEdit);
    addToPostSeasonBattingStats($gameID, $year, $playerID, $AB, $R, $H, $DOUB, $TRIP, $HR, $RBI, $TB, $SO, $BB, $SB, $HBP, $SF, $CS, $isEdit);
  }

  $GLOBALS['batting_stats'][$at_bats] = $AB;
  $GLOBALS['batting_stats'][$runs] = $R;
  $GLOBALS['batting_stats'][$hits] = $H;
  $GLOBALS['batting_stats'][$doubles] = $DOUB;
  $GLOBALS['batting_stats'][$triples] = $TRIP;
  $GLOBALS['batting_stats'][$home_runs] = $HR;
  $GLOBALS['batting_stats'][$runs_batted_in] = $RBI;
  $GLOBALS['batting_stats'][$total_bases] = $TB;
  $GLOBALS['batting_stats'][$strike_outs] = $SO;
  $GLOBALS['batting_stats'][$walks] = $BB;
  $GLOBALS['batting_stats'][$stolen_bases] = $SB;
  $GLOBALS['batting_stats'][$hit_by_pitch] = $HBP;
  $GLOBALS['batting_stats'][$sacrifice_flies] = $SF;
  $GLOBALS['batting_stats'][$caught_stealing] = $CS;
  
  if(($HBP + $AB + $BB + $SF) != 0)
  {
    $GLOBALS['batting_stats'][$on_base_percentage] = number_format(($HBP + $H + $BB) / ($HBP + $AB + $BB + $SF), 3);
  }
  else
  {
    $GLOBALS['batting_stats'][$on_base_percentage] = number_format(0, 3);
  }
  
  if(!empty($AB))
  {
    $GLOBALS['batting_stats'][$slugging_percentage] = number_format($TB / $AB, 3);
    $GLOBALS['batting_stats'][$batting_average] = number_format($H / $AB, 3);
  }
  else
  {
    $GLOBALS['batting_stats'][$slugging_percentage] = number_format(0, 3);
    $GLOBALS['batting_stats'][$batting_average] = number_format(0, 3);
  }

  $GLOBALS['player_stats'][$batting_stats_string] = $GLOBALS['batting_stats'];
}

function addToCareerBattingStats($gameID, $playerID, $AB, $R, $H, $DOUB, $TRIP, $HR, $RBI, $TB, $SO, $BB, $SB, $HBP, $SF, $CS, $isEdit)
{
  global $db, $selectPlayerCareerStats, $batting_stats_string;
  global $at_bats, $runs, $hits, $doubles, $triples, $home_runs, $runs_batted_in, $total_bases, $strike_outs, $walks, $stolen_bases;
  global $hit_by_pitch, $sacrifice_flies, $caught_stealing, $on_base_percentage, $slugging_percentage, $batting_average;
  global $selectGameStats, $statsString;
  
  unset($statistics);
  $statistics = array();
  
  $res = $db->query($selectPlayerCareerStats . $playerID);
  while($row = $db->getRows($res))
  {
    $statistics = unserialize($row['stats']);
  }
  
  if($isEdit)
  {
    unset($remove_player_stats);
    unset($remove_stats);
    $remove_stats = array();
    $remove_player_stats = array();
  
    $res = $db->query($selectGameStats . $gameID);
  
    while($row = $db->getRows($res))
    {
      $remove_stats = unserialize($row['stats']);
    }
    
    $remove_player_stats = $remove_stats[$statsString][$playerID][$batting_stats_string];
    
    $RAB = (isset($remove_player_stats[$at_bats]))? $remove_player_stats[$at_bats] : 0;
    $RR = (isset($remove_player_stats[$runs]))? $remove_player_stats[$runs] : 0;
    $RH = (isset($remove_player_stats[$hits]))? $remove_player_stats[$hits] : 0;
    $RDOUB = (isset($remove_player_stats[$doubles]))? $remove_player_stats[$doubles] : 0;
    $RTRIP = (isset($remove_player_stats[$triples]))? $remove_player_stats[$triples] : 0;
    $RHR = (isset($remove_player_stats[$home_runs]))? $remove_player_stats[$home_runs] : 0;
    $RRBI = (isset($remove_player_stats[$runs_batted_in]))? $remove_player_stats[$runs_batted_in] : 0;
    $RTB = (isset($remove_player_stats[$total_bases]))? $remove_player_stats[$total_bases] : 0;
    $RSO = (isset($remove_player_stats[$strike_outs]))? $remove_player_stats[$strike_outs] : 0;
    $RBB = (isset($remove_player_stats[$walks]))? $remove_player_stats[$walks] : 0;
    $RSB = (isset($remove_player_stats[$stolen_bases]))? $remove_player_stats[$stolen_bases] : 0;
    $RHBP = (isset($remove_player_stats[$hit_by_pitch]))? $remove_player_stats[$hit_by_pitch] : 0;
    $RSF = (isset($remove_player_stats[$sacrifice_flies]))? $remove_player_stats[$sacrifice_flies] : 0;
    $RCS = (isset($remove_player_stats[$caught_stealing]))? $remove_player_stats[$caught_stealing] : 0;
    
    $statistics[$batting_stats_string][$at_bats] -= $RAB;
    $statistics[$batting_stats_string][$runs] -= $RR;
    $statistics[$batting_stats_string][$hits] -= $RH;
    $statistics[$batting_stats_string][$doubles] -= $RDOUB;
    $statistics[$batting_stats_string][$triples] -= $RTRIP;
    $statistics[$batting_stats_string][$home_runs] -= $RHR;
    $statistics[$batting_stats_string][$runs_batted_in] -= $RRBI;
    $statistics[$batting_stats_string][$total_bases] -= $RTB;
    $statistics[$batting_stats_string][$strike_outs] -= $RSO;
    $statistics[$batting_stats_string][$walks] -= $RBB;
    $statistics[$batting_stats_string][$stolen_bases] -= $RSB;
    $statistics[$batting_stats_string][$hit_by_pitch] -= $RHBP;
    $statistics[$batting_stats_string][$sacrifice_flies] -= $RSF;
    $statistics[$batting_stats_string][$caught_stealing] -= $RCS;
  }
  
  $statistics[$batting_stats_string][$at_bats] += $AB;
  $statistics[$batting_stats_string][$runs] += $R;
  $statistics[$batting_stats_string][$hits] += $H;
  $statistics[$batting_stats_string][$doubles] += $DOUB;
  $statistics[$batting_stats_string][$triples] += $TRIP;
  $statistics[$batting_stats_string][$home_runs] += $HR;
  $statistics[$batting_stats_string][$runs_batted_in] += $RBI;
  $statistics[$batting_stats_string][$total_bases] += $TB;
  $statistics[$batting_stats_string][$strike_outs] += $SO;
  $statistics[$batting_stats_string][$walks] += $BB;
  $statistics[$batting_stats_string][$stolen_bases] += $SB;
  $statistics[$batting_stats_string][$hit_by_pitch] += $HBP;
  $statistics[$batting_stats_string][$sacrifice_flies] += $SF;
  $statistics[$batting_stats_string][$caught_stealing] += $CS;
  
  
  $THBP = $statistics[$batting_stats_string][$hit_by_pitch];
  $TH = $statistics[$batting_stats_string][$hits];
  $TBB = $statistics[$batting_stats_string][$walks];
  $TAB = $statistics[$batting_stats_string][$at_bats];
  $TSF = $statistics[$batting_stats_string][$sacrifice_flies];
  $TTB = $statistics[$batting_stats_string][$total_bases];
  
  if(($THBP + $TAB + $TBB + $TSF) != 0)
  {
    $statistics[$batting_stats_string][$on_base_percentage] = number_format(($THBP + $TH + $TBB) / ($THBP + $TAB + $TBB + $TSF), 3);
  }
  else 
  {
    $statistics[$batting_stats_string][$on_base_percentage] = number_format(0, 3);
  }
  
  if(!empty($TAB))
  {
    $statistics[$batting_stats_string][$slugging_percentage] = number_format($TTB / $TAB, 3);
    $statistics[$batting_stats_string][$batting_average] = number_format($TH / $TAB, 3);
  }
  else
  {
    $statistics[$batting_stats_string][$slugging_percentage] = number_format(0, 3);
    $statistics[$batting_stats_string][$batting_average] = number_format(0, 3);
  }
  
  $GLOBALS['career_stats'][$batting_stats_string] = $statistics[$batting_stats_string];
}

function addToPostSeasonCareerBattingStats($gameID, $playerID, $AB, $R, $H, $DOUB, $TRIP, $HR, $RBI, $TB, $SO, $BB, $SB, $HBP, $SF, $CS, $isEdit)
{
  global $db, $selectPlayerPostSeasonCareerStats, $batting_stats_string;
  global $at_bats, $runs, $hits, $doubles, $triples, $home_runs, $runs_batted_in, $total_bases, $strike_outs, $walks, $stolen_bases;
  global $hit_by_pitch, $sacrifice_flies, $caught_stealing, $on_base_percentage, $slugging_percentage, $batting_average;
  global $selectGameStats, $statsString;
  
  unset($statistics);
  $statistics = array();
  
  $res = $db->query($selectPlayerPostSeasonCareerStats . $playerID);
  while($row = $db->getRows($res))
  {
    $statistics = unserialize($row['postseasonstats']);
  }
  
  if($isEdit)
  {
    unset($remove_player_stats);
    unset($remove_stats);
    $remove_stats = array();
    $remove_player_stats = array();
  
    $res = $db->query($selectGameStats . $gameID);
  
    while($row = $db->getRows($res))
    {
      $remove_stats = unserialize($row['stats']);
    }
    
    $remove_player_stats = $remove_stats[$statsString][$playerID][$batting_stats_string];
    
    $RAB = (isset($remove_player_stats[$at_bats]))? $remove_player_stats[$at_bats] : 0;
    $RR = (isset($remove_player_stats[$runs]))? $remove_player_stats[$runs] : 0;
    $RH = (isset($remove_player_stats[$hits]))? $remove_player_stats[$hits] : 0;
    $RDOUB = (isset($remove_player_stats[$doubles]))? $remove_player_stats[$doubles] : 0;
    $RTRIP = (isset($remove_player_stats[$triples]))? $remove_player_stats[$triples] : 0;
    $RHR = (isset($remove_player_stats[$home_runs]))? $remove_player_stats[$home_runs] : 0;
    $RRBI = (isset($remove_player_stats[$runs_batted_in]))? $remove_player_stats[$runs_batted_in] : 0;
    $RTB = (isset($remove_player_stats[$total_bases]))? $remove_player_stats[$total_bases] : 0;
    $RSO = (isset($remove_player_stats[$strike_outs]))? $remove_player_stats[$strike_outs] : 0;
    $RBB = (isset($remove_player_stats[$walks]))? $remove_player_stats[$walks] : 0;
    $RSB = (isset($remove_player_stats[$stolen_bases]))? $remove_player_stats[$stolen_bases] : 0;
    $RHBP = (isset($remove_player_stats[$hit_by_pitch]))? $remove_player_stats[$hit_by_pitch] : 0;
    $RSF = (isset($remove_player_stats[$sacrifice_flies]))? $remove_player_stats[$sacrifice_flies] : 0;
    $RCS = (isset($remove_player_stats[$caught_stealing]))? $remove_player_stats[$caught_stealing] : 0;
    
    $statistics[$batting_stats_string][$at_bats] -= $RAB;
    $statistics[$batting_stats_string][$runs] -= $RR;
    $statistics[$batting_stats_string][$hits] -= $RH;
    $statistics[$batting_stats_string][$doubles] -= $RDOUB;
    $statistics[$batting_stats_string][$triples] -= $RTRIP;
    $statistics[$batting_stats_string][$home_runs] -= $RHR;
    $statistics[$batting_stats_string][$runs_batted_in] -= $RRBI;
    $statistics[$batting_stats_string][$total_bases] -= $RTB;
    $statistics[$batting_stats_string][$strike_outs] -= $RSO;
    $statistics[$batting_stats_string][$walks] -= $RBB;
    $statistics[$batting_stats_string][$stolen_bases] -= $RSB;
    $statistics[$batting_stats_string][$hit_by_pitch] -= $RHBP;
    $statistics[$batting_stats_string][$sacrifice_flies] -= $RSF;
    $statistics[$batting_stats_string][$caught_stealing] -= $RCS;
  }
  
  $statistics[$batting_stats_string][$at_bats] += $AB;
  $statistics[$batting_stats_string][$runs] += $R;
  $statistics[$batting_stats_string][$hits] += $H;
  $statistics[$batting_stats_string][$doubles] += $DOUB;
  $statistics[$batting_stats_string][$triples] += $TRIP;
  $statistics[$batting_stats_string][$home_runs] += $HR;
  $statistics[$batting_stats_string][$runs_batted_in] += $RBI;
  $statistics[$batting_stats_string][$total_bases] += $TB;
  $statistics[$batting_stats_string][$strike_outs] += $SO;
  $statistics[$batting_stats_string][$walks] += $BB;
  $statistics[$batting_stats_string][$stolen_bases] += $SB;
  $statistics[$batting_stats_string][$hit_by_pitch] += $HBP;
  $statistics[$batting_stats_string][$sacrifice_flies] += $SF;
  $statistics[$batting_stats_string][$caught_stealing] += $CS;
  
  $THBP = $statistics[$batting_stats_string][$hit_by_pitch];
  $TH = $statistics[$batting_stats_string][$hits];
  $TBB = $statistics[$batting_stats_string][$walks];
  $TAB = $statistics[$batting_stats_string][$at_bats];
  $TSF = $statistics[$batting_stats_string][$sacrifice_flies];
  $TTB = $statistics[$batting_stats_string][$total_bases];
  
  if(($THBP + $TAB + $TBB + $TSF) != 0)
  {
    $statistics[$batting_stats_string][$on_base_percentage] = number_format(($THBP + $TH + $TBB) / ($THBP + $TAB + $TBB + $TSF), 3);
  }
  else 
  {
    $statistics[$batting_stats_string][$on_base_percentage] = number_format(0, 3);
  }
  
  if(!empty($TAB))
  {
    $statistics[$batting_stats_string][$slugging_percentage] = number_format($TTB / $TAB, 3);
    $statistics[$batting_stats_string][$batting_average] = number_format($TH / $TAB, 3);
  }
  else
  {
    $statistics[$batting_stats_string][$slugging_percentage] = number_format(0, 3);
    $statistics[$batting_stats_string][$batting_average] = number_format(0, 3);
  }
  
  $GLOBALS['post_career_stats'][$batting_stats_string] = $statistics[$batting_stats_string];
}

function addToSeasonBattingStats($gameID, $year, $playerID, $AB, $R, $H, $DOUB, $TRIP, $HR, $RBI, $TB, $SO, $BB, $SB, $HBP, $SF, $CS, $isEdit)
{
  global $db, $selectPlayerSeasonStats, $batting_stats_string, $selectGameDate, $statsString, $selectGameStats;
  global $at_bats, $runs, $hits, $doubles, $triples, $home_runs, $runs_batted_in, $total_bases, $strike_outs, $walks, $stolen_bases;
  global $hit_by_pitch, $sacrifice_flies, $caught_stealing, $on_base_percentage, $slugging_percentage, $batting_average;
  
  unset($statistics);
  $statistics = array();
  
  $res = $db->query($selectPlayerSeasonStats . $playerID . " and year = " . $year);
  while($row = $db->getRows($res))
  {
    $statistics = unserialize($row['stats']);
  }
  
  if($isEdit)
  {
    unset($remove_player_stats);
    unset($remove_stats);
    $remove_stats = array();
    $remove_player_stats = array();
  
    $res = $db->query($selectGameStats . $gameID);
  
    while($row = $db->getRows($res))
    {
      $remove_stats = unserialize($row['stats']);
    }
    
    $remove_player_stats = $remove_stats[$statsString][$playerID][$batting_stats_string];
    
    $RAB = (isset($remove_player_stats[$at_bats]))? $remove_player_stats[$at_bats] : 0;
    $RR = (isset($remove_player_stats[$runs]))? $remove_player_stats[$runs] : 0;
    $RH = (isset($remove_player_stats[$hits]))? $remove_player_stats[$hits] : 0;
    $RDOUB = (isset($remove_player_stats[$doubles]))? $remove_player_stats[$doubles] : 0;
    $RTRIP = (isset($remove_player_stats[$triples]))? $remove_player_stats[$triples] : 0;
    $RHR = (isset($remove_player_stats[$home_runs]))? $remove_player_stats[$home_runs] : 0;
    $RRBI = (isset($remove_player_stats[$runs_batted_in]))? $remove_player_stats[$runs_batted_in] : 0;
    $RTB = (isset($remove_player_stats[$total_bases]))? $remove_player_stats[$total_bases] : 0;
    $RSO = (isset($remove_player_stats[$strike_outs]))? $remove_player_stats[$strike_outs] : 0;
    $RBB = (isset($remove_player_stats[$walks]))? $remove_player_stats[$walks] : 0;
    $RSB = (isset($remove_player_stats[$stolen_bases]))? $remove_player_stats[$stolen_bases] : 0;
    $RHBP = (isset($remove_player_stats[$hit_by_pitch]))? $remove_player_stats[$hit_by_pitch] : 0;
    $RSF = (isset($remove_player_stats[$sacrifice_flies]))? $remove_player_stats[$sacrifice_flies] : 0;
    $RCS = (isset($remove_player_stats[$caught_stealing]))? $remove_player_stats[$caught_stealing] : 0;
    
    $statistics[$batting_stats_string][$at_bats] -= $RAB;
    $statistics[$batting_stats_string][$runs] -= $RR;
    $statistics[$batting_stats_string][$hits] -= $RH;
    $statistics[$batting_stats_string][$doubles] -= $RDOUB;
    $statistics[$batting_stats_string][$triples] -= $RTRIP;
    $statistics[$batting_stats_string][$home_runs] -= $RHR;
    $statistics[$batting_stats_string][$runs_batted_in] -= $RRBI;
    $statistics[$batting_stats_string][$total_bases] -= $RTB;
    $statistics[$batting_stats_string][$strike_outs] -= $RSO;
    $statistics[$batting_stats_string][$walks] -= $RBB;
    $statistics[$batting_stats_string][$stolen_bases] -= $RSB;
    $statistics[$batting_stats_string][$hit_by_pitch] -= $RHBP;
    $statistics[$batting_stats_string][$sacrifice_flies] -= $RSF;
    $statistics[$batting_stats_string][$caught_stealing] -= $RCS;
  }
  
  $statistics[$batting_stats_string][$at_bats] += $AB;
  $statistics[$batting_stats_string][$runs] += $R;
  $statistics[$batting_stats_string][$hits] += $H;
  $statistics[$batting_stats_string][$doubles] += $DOUB;
  $statistics[$batting_stats_string][$triples] += $TRIP;
  $statistics[$batting_stats_string][$home_runs] += $HR;
  $statistics[$batting_stats_string][$runs_batted_in] += $RBI;
  $statistics[$batting_stats_string][$total_bases] += $TB;
  $statistics[$batting_stats_string][$strike_outs] += $SO;
  $statistics[$batting_stats_string][$walks] += $BB;
  $statistics[$batting_stats_string][$stolen_bases] += $SB;
  $statistics[$batting_stats_string][$hit_by_pitch] += $HBP;
  $statistics[$batting_stats_string][$sacrifice_flies] += $SF;
  $statistics[$batting_stats_string][$caught_stealing] += $CS;
  
  
  $THBP = $statistics[$batting_stats_string][$hit_by_pitch];
  $TH = $statistics[$batting_stats_string][$hits];
  $TBB = $statistics[$batting_stats_string][$walks];
  $TAB = $statistics[$batting_stats_string][$at_bats];
  $TSF = $statistics[$batting_stats_string][$sacrifice_flies];
  $TTB = $statistics[$batting_stats_string][$total_bases];
  
  if(($THBP + $TAB + $TBB + $TSF) != 0)
  {
    $statistics[$batting_stats_string][$on_base_percentage] = number_format(($THBP + $TH + $TBB) / ($THBP + $TAB + $TBB + $TSF), 3);
  }
  else
  {
    $statistics[$batting_stats_string][$on_base_percentage] = number_format(0, 3);
  }
  
  if(!empty($TAB))
  {
    $statistics[$batting_stats_string][$slugging_percentage] = number_format($TTB / $TAB, 3);
    $statistics[$batting_stats_string][$batting_average] = number_format($TH / $TAB, 3);
  }
  else
  {
    $statistics[$batting_stats_string][$slugging_percentage] = number_format(0, 3);
    $statistics[$batting_stats_string][$batting_average] = number_format(0, 3);
  }
  
  $GLOBALS['season_stats'][$batting_stats_string] = $statistics[$batting_stats_string];
}

function addToPostSeasonBattingStats($gameID, $year, $playerID, $AB, $R, $H, $DOUB, $TRIP, $HR, $RBI, $TB, $SO, $BB, $SB, $HBP, $SF, $CS, $isEdit)
{
  global $db, $selectPlayerPostSeasonStats, $batting_stats_string, $selectGameDate, $statsString, $selectGameStats;
  global $at_bats, $runs, $hits, $doubles, $triples, $home_runs, $runs_batted_in, $total_bases, $strike_outs, $walks, $stolen_bases;
  global $hit_by_pitch, $sacrifice_flies, $caught_stealing, $on_base_percentage, $slugging_percentage, $batting_average;
  
  unset($statistics);
  $statistics = array();
  
  $res = $db->query($selectPlayerPostSeasonStats . $playerID . " and year = " . $year);
  while($row = $db->getRows($res))
  {
    $statistics = unserialize($row['stats']);
  }
  
  if($isEdit)
  {
    unset($remove_player_stats);
    unset($remove_stats);
    $remove_stats = array();
    $remove_player_stats = array();
  
    $res = $db->query($selectGameStats . $gameID);
  
    while($row = $db->getRows($res))
    {
      $remove_stats = unserialize($row['stats']);
    }
    
    $remove_player_stats = $remove_stats[$statsString][$playerID][$batting_stats_string];
    
    $RAB = (isset($remove_player_stats[$at_bats]))? $remove_player_stats[$at_bats] : 0;
    $RR = (isset($remove_player_stats[$runs]))? $remove_player_stats[$runs] : 0;
    $RH = (isset($remove_player_stats[$hits]))? $remove_player_stats[$hits] : 0;
    $RDOUB = (isset($remove_player_stats[$doubles]))? $remove_player_stats[$doubles] : 0;
    $RTRIP = (isset($remove_player_stats[$triples]))? $remove_player_stats[$triples] : 0;
    $RHR = (isset($remove_player_stats[$home_runs]))? $remove_player_stats[$home_runs] : 0;
    $RRBI = (isset($remove_player_stats[$runs_batted_in]))? $remove_player_stats[$runs_batted_in] : 0;
    $RTB = (isset($remove_player_stats[$total_bases]))? $remove_player_stats[$total_bases] : 0;
    $RSO = (isset($remove_player_stats[$strike_outs]))? $remove_player_stats[$strike_outs] : 0;
    $RBB = (isset($remove_player_stats[$walks]))? $remove_player_stats[$walks] : 0;
    $RSB = (isset($remove_player_stats[$stolen_bases]))? $remove_player_stats[$stolen_bases] : 0;
    $RHBP = (isset($remove_player_stats[$hit_by_pitch]))? $remove_player_stats[$hit_by_pitch] : 0;
    $RSF = (isset($remove_player_stats[$sacrifice_flies]))? $remove_player_stats[$sacrifice_flies] : 0;
    $RCS = (isset($remove_player_stats[$caught_stealing]))? $remove_player_stats[$caught_stealing] : 0;
    
    $statistics[$batting_stats_string][$at_bats] -= $RAB;
    $statistics[$batting_stats_string][$runs] -= $RR;
    $statistics[$batting_stats_string][$hits] -= $RH;
    $statistics[$batting_stats_string][$doubles] -= $RDOUB;
    $statistics[$batting_stats_string][$triples] -= $RTRIP;
    $statistics[$batting_stats_string][$home_runs] -= $RHR;
    $statistics[$batting_stats_string][$runs_batted_in] -= $RRBI;
    $statistics[$batting_stats_string][$total_bases] -= $RTB;
    $statistics[$batting_stats_string][$strike_outs] -= $RSO;
    $statistics[$batting_stats_string][$walks] -= $RBB;
    $statistics[$batting_stats_string][$stolen_bases] -= $RSB;
    $statistics[$batting_stats_string][$hit_by_pitch] -= $RHBP;
    $statistics[$batting_stats_string][$sacrifice_flies] -= $RSF;
    $statistics[$batting_stats_string][$caught_stealing] -= $RCS;
  }
  
  $statistics[$batting_stats_string][$at_bats] += $AB;
  $statistics[$batting_stats_string][$runs] += $R;
  $statistics[$batting_stats_string][$hits] += $H;
  $statistics[$batting_stats_string][$doubles] += $DOUB;
  $statistics[$batting_stats_string][$triples] += $TRIP;
  $statistics[$batting_stats_string][$home_runs] += $HR;
  $statistics[$batting_stats_string][$runs_batted_in] += $RBI;
  $statistics[$batting_stats_string][$total_bases] += $TB;
  $statistics[$batting_stats_string][$strike_outs] += $SO;
  $statistics[$batting_stats_string][$walks] += $BB;
  $statistics[$batting_stats_string][$stolen_bases] += $SB;
  $statistics[$batting_stats_string][$hit_by_pitch] += $HBP;
  $statistics[$batting_stats_string][$sacrifice_flies] += $SF;
  $statistics[$batting_stats_string][$caught_stealing] += $CS;
  
  
  $THBP = $statistics[$batting_stats_string][$hit_by_pitch];
  $TH = $statistics[$batting_stats_string][$hits];
  $TBB = $statistics[$batting_stats_string][$walks];
  $TAB = $statistics[$batting_stats_string][$at_bats];
  $TSF = $statistics[$batting_stats_string][$sacrifice_flies];
  $TTB = $statistics[$batting_stats_string][$total_bases];
  
  if(($THBP + $TAB + $TBB + $TSF) != 0)
  {
    $statistics[$batting_stats_string][$on_base_percentage] = number_format(($THBP + $TH + $TBB) / ($THBP + $TAB + $TBB + $TSF), 3);
  }
  else
  {
    $statistics[$batting_stats_string][$on_base_percentage] = number_format(0, 3);
  }
  
  if(!empty($TAB))
  {
    $statistics[$batting_stats_string][$slugging_percentage] = number_format($TTB / $TAB, 3);
    $statistics[$batting_stats_string][$batting_average] = number_format($TH / $TAB, 3);
  }
  else
  {
    $statistics[$batting_stats_string][$slugging_percentage] = number_format(0, 3);
    $statistics[$batting_stats_string][$batting_average] = number_format(0, 3);
  }
  
  $GLOBALS['post_season_stats'][$batting_stats_string] = $statistics[$batting_stats_string];
}

function gamePitchingStats($year, $gameID, $playerID, $W, $L, $CG, $SHO, $SV, $SVO, $IP, $H, $ER, $HR, $HBP, $BB, $SO, $preseason, $postseason, $isEdit)
{
  global $wins, $losses, $complete_games, $shut_outs, $saves, $save_opportunities, $innings_pitched, $hits_allowed;
  global $earned_runs, $home_runs_allowed, $hit_batters, $walks_allowed, $strike_outs, $runs_allowed, $earned_runs_average, $pitching_stats_string;

  unset($GLOBALS['$pitching_stats']);
  $GLOBALS['$pitching_stats'] = array();

  if(!$preseason && !$postseason)
  {
    addToCareerPitchingStats($gameID, $playerID, $W, $L, $CG, $SHO, $SV, $SVO, $IP, $H, $ER, $HR, $HBP, $BB, $SO, $isEdit);
    addToSeasonPitchingStats($gameID, $year, $playerID, $W, $L, $CG, $SHO, $SV, $SVO, $IP, $H, $ER, $HR, $HBP, $BB, $SO, $isEdit);
  }
  else if(!$preseason)
  {
    addToPostSeasonCareerPitchingStats($gameID, $playerID, $W, $L, $CG, $SHO, $SV, $SVO, $IP, $H, $ER, $HR, $HBP, $BB, $SO, $isEdit);
    addToPostSeasonPitchingStats($gameID, $year, $playerID, $W, $L, $CG, $SHO, $SV, $SVO, $IP, $H, $ER, $HR, $HBP, $BB, $SO, $isEdit);
  }

  $GLOBALS['$pitching_stats'][$wins] = $W;
  $GLOBALS['$pitching_stats'][$losses] = $L;
  $GLOBALS['$pitching_stats'][$complete_games] = $CG;
  $GLOBALS['$pitching_stats'][$shut_outs] = $SHO;
  $GLOBALS['$pitching_stats'][$saves] = $SV;
  $GLOBALS['$pitching_stats'][$save_opportunities] = $SVO;
  $GLOBALS['$pitching_stats'][$innings_pitched] = $IP;
  $GLOBALS['$pitching_stats'][$hits_allowed] = $H;
  $GLOBALS['$pitching_stats'][$earned_runs] = $ER;
  $GLOBALS['$pitching_stats'][$home_runs_allowed] = $HR;
  $GLOBALS['$pitching_stats'][$hit_batters] = $HBP;
  $GLOBALS['$pitching_stats'][$walks_allowed] = $BB;
  $GLOBALS['$pitching_stats'][$strike_outs] = $SO;
  
  if(!empty($IP))
  {
    $GLOBALS['$pitching_stats'][$earned_runs_average] = number_format(($ER * 9) / $IP, 2);
  }
  else if(!empty($ER))
  {
    $GLOBALS['$pitching_stats'][$earned_runs_average] = "Infinate";
  }
  else 
  {
    $GLOBALS['$pitching_stats'][$earned_runs_average] = number_format(0, 2);
  }

  $GLOBALS['player_stats'][$pitching_stats_string] = $GLOBALS['$pitching_stats'];
}

function addToCareerPitchingStats($gameID, $playerID, $W, $L, $CG, $SHO, $SV, $SVO, $IP, $H, $ER, $HR, $HBP, $BB, $SO, $isEdit)
{
  global $db, $selectPlayerCareerStats, $pitching_stats_string, $selectGameStats, $statsString;
  global $wins, $losses, $complete_games, $shut_outs, $saves, $save_opportunities, $innings_pitched, $hits_allowed;
  global $earned_runs, $home_runs_allowed, $hit_batters, $walks_allowed, $strike_outs, $runs_allowed, $earned_runs_average;
  
  $statistics = array();
  
  $res = $db->query($selectPlayerCareerStats . $playerID);
  while($row = $db->getRows($res))
  {
    $statistics = unserialize($row['stats']);
  }
  
  if($isEdit)
  {
    unset($remove_player_stats);
    unset($remove_stats);
    $remove_stats = array();
    $remove_player_stats = array();
  
    $res = $db->query($selectGameStats . $gameID);
  
    while($row = $db->getRows($res))
    {
      $remove_stats = unserialize($row['stats']);
    }
    
    $remove_player_stats = $remove_stats[$statsString][$playerID][$pitching_stats_string];
    
    $RW = (isset($remove_player_stats[$wins]))? $remove_player_stats[$wins] : 0;
    $RL = (isset($remove_player_stats[$losses]))? $remove_player_stats[$losses] : 0;
    $RCG = (isset($remove_player_stats[$complete_games]))? $remove_player_stats[$complete_games] : 0;
    $RSHO = (isset($remove_player_stats[$shut_outs]))? $remove_player_stats[$shut_outs] : 0;
    $RSV = (isset($remove_player_stats[$saves]))? $remove_player_stats[$saves] : 0;
    $RSVO = (isset($remove_player_stats[$save_opportunities]))? $remove_player_stats[$save_opportunities] : 0;
    $RIP = (isset($remove_player_stats[$innings_pitched]))? $remove_player_stats[$innings_pitched] : 0;
    $RH = (isset($remove_player_stats[$hits_allowed]))? $remove_player_stats[$hits_allowed] : 0;
    $RER = (isset($remove_player_stats[$earned_runs]))? $remove_player_stats[$earned_runs] : 0;
    $RHR = (isset($remove_player_stats[$home_runs_allowed]))? $remove_player_stats[$home_runs_allowed] : 0;
    $RHBP = (isset($remove_player_stats[$hit_batters]))? $remove_player_stats[$hit_batters] : 0;
    $RBB = (isset($remove_player_stats[$walks_allowed]))? $remove_player_stats[$walks_allowed] : 0;
    $RSO = (isset($remove_player_stats[$strike_outs]))? $remove_player_stats[$strike_outs] : 0;
    
    $statistics[$pitching_stats_string][$wins] -= $RW;
    $statistics[$pitching_stats_string][$losses] -= $RL;
    $statistics[$pitching_stats_string][$complete_games] -= $RCG;
    $statistics[$pitching_stats_string][$shut_outs] -= $RSHO;
    $statistics[$pitching_stats_string][$saves] -= $RSV;
    $statistics[$pitching_stats_string][$save_opportunities] -= $RSVO;
    $statistics[$pitching_stats_string][$innings_pitched] -= $RIP;
    $statistics[$pitching_stats_string][$hits_allowed] -= $RH;
    $statistics[$pitching_stats_string][$earned_runs] -= $RER;
    $statistics[$pitching_stats_string][$home_runs_allowed] -= $RHR;
    $statistics[$pitching_stats_string][$hit_batters] -= $RHBP;
    $statistics[$pitching_stats_string][$walks_allowed] -= $RBB;
    $statistics[$pitching_stats_string][$strike_outs] -= $RSO;
  }
  
  $statistics[$pitching_stats_string][$wins] += $W;
  $statistics[$pitching_stats_string][$losses] += $L;
  $statistics[$pitching_stats_string][$complete_games] += $CG;
  $statistics[$pitching_stats_string][$shut_outs] += $SHO;
  $statistics[$pitching_stats_string][$saves] += $SV;
  $statistics[$pitching_stats_string][$save_opportunities] += $SVO;
  $statistics[$pitching_stats_string][$innings_pitched] += $IP;
  $statistics[$pitching_stats_string][$hits_allowed] += $H;
  $statistics[$pitching_stats_string][$earned_runs] += $ER;
  $statistics[$pitching_stats_string][$home_runs_allowed] += $HR;
  $statistics[$pitching_stats_string][$hit_batters] += $HBP;
  $statistics[$pitching_stats_string][$walks_allowed] += $BB;
  $statistics[$pitching_stats_string][$strike_outs] += $SO;
  
  $TIP = $statistics[$pitching_stats_string][$innings_pitched];
  $TER = $statistics[$pitching_stats_string][$earned_runs];
  
  if(!empty($TIP))
  {
    $statistics[$pitching_stats_string][$earned_runs_average] = number_format(($TER * 9) / $TIP, 2);
  }
  else if(!empty($TER))
  {
    $statistics[$pitching_stats_string][$earned_runs_average] = "Infinate";
  }
  else 
  {
    $statistics[$pitching_stats_string][$earned_runs_average] = number_format(0, 2);
  }
  
  $GLOBALS['career_stats'][$pitching_stats_string] = $statistics[$pitching_stats_string];
}

function addToPostSeasonCareerPitchingStats($gameID, $playerID, $W, $L, $CG, $SHO, $SV, $SVO, $IP, $H, $ER, $HR, $HBP, $BB, $SO, $isEdit)
{
  global $db, $selectPlayerPostSeasonCareerStats, $pitching_stats_string, $selectGameStats, $statsString;
  global $wins, $losses, $complete_games, $shut_outs, $saves, $save_opportunities, $innings_pitched, $hits_allowed;
  global $earned_runs, $home_runs_allowed, $hit_batters, $walks_allowed, $strike_outs, $runs_allowed, $earned_runs_average;
  
  $statistics = array();
  
  $res = $db->query($selectPlayerPostSeasonCareerStats . $playerID);
  while($row = $db->getRows($res))
  {
    $statistics = unserialize($row['stats']);
  }
  
  if($isEdit)
  {
    unset($remove_player_stats);
    unset($remove_stats);
    $remove_stats = array();
    $remove_player_stats = array();
  
    $res = $db->query($selectGameStats . $gameID);
  
    while($row = $db->getRows($res))
    {
      $remove_stats = unserialize($row['stats']);
    }
    
    $remove_player_stats = $remove_stats[$statsString][$playerID][$pitching_stats_string];
    
    $RW = (isset($remove_player_stats[$wins]))? $remove_player_stats[$wins] : 0;
    $RL = (isset($remove_player_stats[$losses]))? $remove_player_stats[$losses] : 0;
    $RCG = (isset($remove_player_stats[$complete_games]))? $remove_player_stats[$complete_games] : 0;
    $RSHO = (isset($remove_player_stats[$shut_outs]))? $remove_player_stats[$shut_outs] : 0;
    $RSV = (isset($remove_player_stats[$saves]))? $remove_player_stats[$saves] : 0;
    $RSVO = (isset($remove_player_stats[$save_opportunities]))? $remove_player_stats[$save_opportunities] : 0;
    $RIP = (isset($remove_player_stats[$innings_pitched]))? $remove_player_stats[$innings_pitched] : 0;
    $RH = (isset($remove_player_stats[$hits_allowed]))? $remove_player_stats[$hits_allowed] : 0;
    $RER = (isset($remove_player_stats[$earned_runs]))? $remove_player_stats[$earned_runs] : 0;
    $RHR = (isset($remove_player_stats[$home_runs_allowed]))? $remove_player_stats[$home_runs_allowed] : 0;
    $RHBP = (isset($remove_player_stats[$hit_batters]))? $remove_player_stats[$hit_batters] : 0;
    $RBB = (isset($remove_player_stats[$walks_allowed]))? $remove_player_stats[$walks_allowed] : 0;
    $RSO = (isset($remove_player_stats[$strike_outs]))? $remove_player_stats[$strike_outs] : 0;
    
    $statistics[$pitching_stats_string][$wins] -= $RW;
    $statistics[$pitching_stats_string][$losses] -= $RL;
    $statistics[$pitching_stats_string][$complete_games] -= $RCG;
    $statistics[$pitching_stats_string][$shut_outs] -= $RSHO;
    $statistics[$pitching_stats_string][$saves] -= $RSV;
    $statistics[$pitching_stats_string][$save_opportunities] -= $RSVO;
    $statistics[$pitching_stats_string][$innings_pitched] -= $RIP;
    $statistics[$pitching_stats_string][$hits_allowed] -= $RH;
    $statistics[$pitching_stats_string][$earned_runs] -= $RER;
    $statistics[$pitching_stats_string][$home_runs_allowed] -= $RHR;
    $statistics[$pitching_stats_string][$hit_batters] -= $RHBP;
    $statistics[$pitching_stats_string][$walks_allowed] -= $RBB;
    $statistics[$pitching_stats_string][$strike_outs] -= $RSO;
  }
  
  $statistics[$pitching_stats_string][$wins] += $W;
  $statistics[$pitching_stats_string][$losses] += $L;
  $statistics[$pitching_stats_string][$complete_games] += $CG;
  $statistics[$pitching_stats_string][$shut_outs] += $SHO;
  $statistics[$pitching_stats_string][$saves] += $SV;
  $statistics[$pitching_stats_string][$save_opportunities] += $SVO;
  $statistics[$pitching_stats_string][$innings_pitched] += $IP;
  $statistics[$pitching_stats_string][$hits_allowed] += $H;
  $statistics[$pitching_stats_string][$earned_runs] += $ER;
  $statistics[$pitching_stats_string][$home_runs_allowed] += $HR;
  $statistics[$pitching_stats_string][$hit_batters] += $HBP;
  $statistics[$pitching_stats_string][$walks_allowed] += $BB;
  $statistics[$pitching_stats_string][$strike_outs] += $SO;
  
  $TIP = $statistics[$pitching_stats_string][$innings_pitched];
  $TER = $statistics[$pitching_stats_string][$earned_runs];
  
  if(!empty($TIP))
  {
    $statistics[$pitching_stats_string][$earned_runs_average] = number_format(($TER * 9) / $TIP, 2);
  }
  else if(!empty($TER))
  {
    $statistics[$pitching_stats_string][$earned_runs_average] = "Infinate";
  }
  else 
  {
    $statistics[$pitching_stats_string][$earned_runs_average] = number_format(0, 2);
  }
  
  $GLOBALS['post_career_stats'][$pitching_stats_string] = $statistics[$pitching_stats_string];
}

function addToSeasonPitchingStats($gameID, $year, $playerID, $W, $L, $CG, $SHO, $SV, $SVO, $IP, $H, $ER, $HR, $HBP, $BB, $SO, $isEdit)
{
  global $db, $selectGameDate, $pitching_stats_string, $selectPlayerSeasonStats, $selectGameStats, $statsString;
  global $wins, $losses, $complete_games, $shut_outs, $saves, $save_opportunities, $innings_pitched, $hits_allowed;
  global $earned_runs, $home_runs_allowed, $hit_batters, $walks_allowed, $strike_outs, $runs_allowed, $earned_runs_average;
  
  $statistics = array();
  
  $res = $db->query($selectPlayerSeasonStats . $playerID . " and year = " . $year);
  while($row = $db->getRows($res))
  {
    $statistics = unserialize($row['stats']);
  }
  
  if($isEdit)
  {
    unset($remove_player_stats);
    unset($remove_stats);
    $remove_stats = array();
    $remove_player_stats = array();
  
    $res = $db->query($selectGameStats . $gameID);
  
    while($row = $db->getRows($res))
    {
      $remove_stats = unserialize($row['stats']);
    }
    
    $remove_player_stats = $remove_stats[$statsString][$playerID][$pitching_stats_string];
    
    $RW = (isset($remove_player_stats[$wins]))? $remove_player_stats[$wins] : 0;
    $RL = (isset($remove_player_stats[$losses]))? $remove_player_stats[$losses] : 0;
    $RCG = (isset($remove_player_stats[$complete_games]))? $remove_player_stats[$complete_games] : 0;
    $RSHO = (isset($remove_player_stats[$shut_outs]))? $remove_player_stats[$shut_outs] : 0;
    $RSV = (isset($remove_player_stats[$saves]))? $remove_player_stats[$saves] : 0;
    $RSVO = (isset($remove_player_stats[$save_opportunities]))? $remove_player_stats[$save_opportunities] : 0;
    $RIP = (isset($remove_player_stats[$innings_pitched]))? $remove_player_stats[$innings_pitched] : 0;
    $RH = (isset($remove_player_stats[$hits_allowed]))? $remove_player_stats[$hits_allowed] : 0;
    $RER = (isset($remove_player_stats[$earned_runs]))? $remove_player_stats[$earned_runs] : 0;
    $RHR = (isset($remove_player_stats[$home_runs_allowed]))? $remove_player_stats[$home_runs_allowed] : 0;
    $RHBP = (isset($remove_player_stats[$hit_batters]))? $remove_player_stats[$hit_batters] : 0;
    $RBB = (isset($remove_player_stats[$walks_allowed]))? $remove_player_stats[$walks_allowed] : 0;
    $RSO = (isset($remove_player_stats[$strike_outs]))? $remove_player_stats[$strike_outs] : 0;
    
    $statistics[$pitching_stats_string][$wins] -= $RW;
    $statistics[$pitching_stats_string][$losses] -= $RL;
    $statistics[$pitching_stats_string][$complete_games] -= $RCG;
    $statistics[$pitching_stats_string][$shut_outs] -= $RSHO;
    $statistics[$pitching_stats_string][$saves] -= $RSV;
    $statistics[$pitching_stats_string][$save_opportunities] -= $RSVO;
    $statistics[$pitching_stats_string][$innings_pitched] -= $RIP;
    $statistics[$pitching_stats_string][$hits_allowed] -= $RH;
    $statistics[$pitching_stats_string][$earned_runs] -= $RER;
    $statistics[$pitching_stats_string][$home_runs_allowed] -= $RHR;
    $statistics[$pitching_stats_string][$hit_batters] -= $RHBP;
    $statistics[$pitching_stats_string][$walks_allowed] -= $RBB;
    $statistics[$pitching_stats_string][$strike_outs] -= $RSO;
  }
  
  $statistics[$pitching_stats_string][$wins] += $W;
  $statistics[$pitching_stats_string][$losses] += $L;
  $statistics[$pitching_stats_string][$complete_games] += $CG;
  $statistics[$pitching_stats_string][$shut_outs] += $SHO;
  $statistics[$pitching_stats_string][$saves] += $SV;
  $statistics[$pitching_stats_string][$save_opportunities] += $SVO;
  $statistics[$pitching_stats_string][$innings_pitched] += $IP;
  $statistics[$pitching_stats_string][$hits_allowed] += $H;
  $statistics[$pitching_stats_string][$earned_runs] += $ER;
  $statistics[$pitching_stats_string][$home_runs_allowed] += $HR;
  $statistics[$pitching_stats_string][$hit_batters] += $HBP;
  $statistics[$pitching_stats_string][$walks_allowed] += $BB;
  $statistics[$pitching_stats_string][$strike_outs] += $SO;
  
  $TIP = $statistics[$pitching_stats_string][$innings_pitched];
  $TER = $statistics[$pitching_stats_string][$earned_runs];
  
  if(!empty($TIP))
  {
    $statistics[$pitching_stats_string][$earned_runs_average] = number_format(($TER * 9) / $TIP, 2);
  }
  else if(!empty($TER))
  {
    $statistics[$pitching_stats_string][$earned_runs_average] = "Infinate";
  }
  else 
  {
    $statistics[$pitching_stats_string][$earned_runs_average] = number_format(0, 2);
  }
  
  $GLOBALS['season_stats'][$pitching_stats_string] = $statistics[$pitching_stats_string];
}

function addToPostSeasonPitchingStats($gameID, $year, $playerID, $W, $L, $CG, $SHO, $SV, $SVO, $IP, $H, $ER, $HR, $HBP, $BB, $SO, $isEdit)
{
  global $db, $selectGameDate, $pitching_stats_string, $selectPlayerPostSeasonStats, $selectGameStats, $statsString;
  global $wins, $losses, $complete_games, $shut_outs, $saves, $save_opportunities, $innings_pitched, $hits_allowed;
  global $earned_runs, $home_runs_allowed, $hit_batters, $walks_allowed, $strike_outs, $runs_allowed, $earned_runs_average;
  
  $statistics = array();
  
  $res = $db->query($selectPlayerPostSeasonStats . $playerID . " and year = " . $year);
  while($row = $db->getRows($res))
  {
    $statistics = unserialize($row['stats']);
  }
  
  if($isEdit)
  {
    unset($remove_player_stats);
    unset($remove_stats);
    $remove_stats = array();
    $remove_player_stats = array();
  
    $res = $db->query($selectGameStats . $gameID);
  
    while($row = $db->getRows($res))
    {
      $remove_stats = unserialize($row['stats']);
    }
    
    $remove_player_stats = $remove_stats[$statsString][$playerID][$pitching_stats_string];
    
    $RW = (isset($remove_player_stats[$wins]))? $remove_player_stats[$wins] : 0;
    $RL = (isset($remove_player_stats[$losses]))? $remove_player_stats[$losses] : 0;
    $RCG = (isset($remove_player_stats[$complete_games]))? $remove_player_stats[$complete_games] : 0;
    $RSHO = (isset($remove_player_stats[$shut_outs]))? $remove_player_stats[$shut_outs] : 0;
    $RSV = (isset($remove_player_stats[$saves]))? $remove_player_stats[$saves] : 0;
    $RSVO = (isset($remove_player_stats[$save_opportunities]))? $remove_player_stats[$save_opportunities] : 0;
    $RIP = (isset($remove_player_stats[$innings_pitched]))? $remove_player_stats[$innings_pitched] : 0;
    $RH = (isset($remove_player_stats[$hits_allowed]))? $remove_player_stats[$hits_allowed] : 0;
    $RER = (isset($remove_player_stats[$earned_runs]))? $remove_player_stats[$earned_runs] : 0;
    $RHR = (isset($remove_player_stats[$home_runs_allowed]))? $remove_player_stats[$home_runs_allowed] : 0;
    $RHBP = (isset($remove_player_stats[$hit_batters]))? $remove_player_stats[$hit_batters] : 0;
    $RBB = (isset($remove_player_stats[$walks_allowed]))? $remove_player_stats[$walks_allowed] : 0;
    $RSO = (isset($remove_player_stats[$strike_outs]))? $remove_player_stats[$strike_outs] : 0;
    
    $statistics[$pitching_stats_string][$wins] -= $RW;
    $statistics[$pitching_stats_string][$losses] -= $RL;
    $statistics[$pitching_stats_string][$complete_games] -= $RCG;
    $statistics[$pitching_stats_string][$shut_outs] -= $RSHO;
    $statistics[$pitching_stats_string][$saves] -= $RSV;
    $statistics[$pitching_stats_string][$save_opportunities] -= $RSVO;
    $statistics[$pitching_stats_string][$innings_pitched] -= $RIP;
    $statistics[$pitching_stats_string][$hits_allowed] -= $RH;
    $statistics[$pitching_stats_string][$earned_runs] -= $RER;
    $statistics[$pitching_stats_string][$home_runs_allowed] -= $RHR;
    $statistics[$pitching_stats_string][$hit_batters] -= $RHBP;
    $statistics[$pitching_stats_string][$walks_allowed] -= $RBB;
    $statistics[$pitching_stats_string][$strike_outs] -= $RSO;
  }
  
  $statistics[$pitching_stats_string][$wins] += $W;
  $statistics[$pitching_stats_string][$losses] += $L;
  $statistics[$pitching_stats_string][$complete_games] += $CG;
  $statistics[$pitching_stats_string][$shut_outs] += $SHO;
  $statistics[$pitching_stats_string][$saves] += $SV;
  $statistics[$pitching_stats_string][$save_opportunities] += $SVO;
  $statistics[$pitching_stats_string][$innings_pitched] += $IP;
  $statistics[$pitching_stats_string][$hits_allowed] += $H;
  $statistics[$pitching_stats_string][$earned_runs] += $ER;
  $statistics[$pitching_stats_string][$home_runs_allowed] += $HR;
  $statistics[$pitching_stats_string][$hit_batters] += $HBP;
  $statistics[$pitching_stats_string][$walks_allowed] += $BB;
  $statistics[$pitching_stats_string][$strike_outs] += $SO;
  
  $TIP = $statistics[$pitching_stats_string][$innings_pitched];
  $TER = $statistics[$pitching_stats_string][$earned_runs];
  
  if(!empty($TIP))
  {
    $statistics[$pitching_stats_string][$earned_runs_average] = number_format(($TER * 9) / $TIP, 2);
  }
  else if(!empty($TER))
  {
    $statistics[$pitching_stats_string][$earned_runs_average] = "Infinate";
  }
  else 
  {
    $statistics[$pitching_stats_string][$earned_runs_average] = number_format(0, 2);
  }
  
  $GLOBALS['post_season_stats'][$pitching_stats_string] = $statistics[$pitching_stats_string];
}

function getGameStats($gameID)
{
  global $db, $selectGameStats, $boxString, $statsString;
  
  unset($GLOBALS['stats']);
  $GLOBALS['stats'] = array();
  
  unset($GLOBALS['players_stats']);
  $GLOBALS['players_stats'] = array();
  
  unset($GLOBALS['box_score']);
  $GLOBALS['box_score'] = array();
  
  $res = $db->query($selectGameStats . $gameID);
  
  while($row = $db->getRows($res))
  {
    $GLOBALS['stats'] = unserialize($row['stats']);
  }
  
  $GLOBALS['players_stats'] = $GLOBALS['stats'][$statsString];
  $GLOBALS['box_score'] = $GLOBALS['stats'][$boxString];
}

function addCarrerStatsToDB($playerid)
{
  global $db, $updateCareerStats, $update2;
  
  $stats = serialize($GLOBALS['career_stats']);
  $db->query($updateCareerStats . "'" . $db->escape_value($stats) . "'" . $update2 . $playerid);
}

function addPostSeasonCarrerStatsToDB($playerid)
{
  global $db, $updatePostSeasonCareerStats, $update2;
  
  $stats = serialize($GLOBALS['post_career_stats']);
  $db->query($updatePostSeasonCareerStats . "'" . $db->escape_value($stats) . "'" . $update2 . $playerid);
}

function addSeasonStatsToDB($playerid, $year)
{
  global $db, $updateSeasonStats, $insertSeasonStats;
  
  $selectCountSeasonStats = "select count(*) from seasonstats where player = " . $playerid . " and year = " . $year . " limit 0,1";
  $count = $db->query($selectCountSeasonStats);
  $curCount = 0;
  
  if($db->num_rows($count) > 0)
  {
    while($row = $db->getRows($count))
    {
      $curCount = $row['count(*)'];
    }
  }
  
  $stats = serialize($GLOBALS['season_stats']);
  
  if($curCount > 0)
  {
    $db->query($updateSeasonStats . "'" . $db->escape_value($stats) . "' where player = " . $playerid . " and year = " . $year);
  }
  else 
  {
    $db->query($insertSeasonStats . "(" . $playerid . ", " . $year . ", '" . $db->escape_value($stats) . "')");
  }
}

function addPostSeasonStatsToDB($playerid, $year)
{
  global $db, $updatePostSeasonStats, $insertPostSeasonStats;
  
  $selectCountSeasonStats = "select count(*) from postseasonstats where player = " . $playerid . " and year = " . $year . " limit 0,1";
  $count = $db->query($selectCountSeasonStats);
  $curCount = 0;
  
  if($db->num_rows($count) > 0)
  {
    while($row = $db->getRows($count))
    {
      $curCount = $row['count(*)'];
    }
  }
  
  $stats = serialize($GLOBALS['post_season_stats']);
  
  if($curCount > 0)
  {
    $db->query($updatePostSeasonStats . "'" . $db->escape_value($stats) . "' where player = " . $playerid . " and year = " . $year);
  }
  else 
  {
    $db->query($insertPostSeasonStats . "(" . $playerid . ", " . $year . ", '" . $db->escape_value($stats) . "')");
  }
}

function getCareerPlayerStats($playerID)
{
  global $db;
  
  unset($GLOBALS['career_stats']);
  $GLOBALS['career_stats'] = array();
  
  unset($GLOBALS['post_career_stats']);
  $GLOBALS['post_career_stats'] = array();
  
  $selectCareerTeamStats = "select * from player where id = ";
  $res = $db->query($selectCareerTeamStats . $playerID);
  
  while($row = $db->getRows($res))
  {
    $GLOBALS['career_stats'] = unserialize($row['stats']);
    $GLOBALS['post_career_stats'] = unserialize($row['postseasonstats']);
  }
}

function getCareerTeamStats()
{
  global $db;
  
  unset($GLOBALS['players_stats']);
  $GLOBALS['players_stats'] = array();
  
  $selectCareerTeamStats = "select * from player";
  $res = $db->query($selectCareerTeamStats);
  
  while($row = $db->getRows($res))
  {
    $GLOBALS['players_stats'][$row['id']] = unserialize($row['stats']);
    $GLOBALS['post_players_stats'][$row['id']] = unserialize($row['postseasonstats']);
  }
}

function getSeasonPlayerStats($playerID, $year)
{
  global $db;
  
  unset($GLOBALS['season_stats']);
  $GLOBALS['season_stats'] = array();
  
  $selectCountSeasonStats = "select * from seasonstats where player = " . $playerID . " and year = " . $year;
  $res = $db->query($selectCountSeasonStats);
  
  while($row = $db->getRows($res))
  {
    $GLOBALS['season_stats'] = unserialize($row['stats']);
  }
}

function getPostSeasonPlayerStats($playerID, $year)
{
  global $db;
  
  unset($GLOBALS['post_season_stats']);
  $GLOBALS['post_season_stats'] = array();
  
  $selectCountSeasonStats = "select * from postseasonstats where player = " . $playerID . " and year = " . $year;
  $res = $db->query($selectCountSeasonStats);
  
  while($row = $db->getRows($res))
  {
    $GLOBALS['post_season_stats'] = unserialize($row['stats']);
  }
}

function getSeasonTeamStats($year)
{
  global $db;
  
  unset($GLOBALS['season_players_stats']);
  $GLOBALS['season_players_stats'] = array();
  
  $selectCountSeasonStats = "select * from seasonstats where year = " . $year;
  $res = $db->query($selectCountSeasonStats);
  
  while($row = $db->getRows($res))
  {
    $GLOBALS['season_players_stats'][$row['player']] = unserialize($row['stats']);
  }
}

function getPostSeasonTeamStats($year)
{
  global $db;
  
  unset($GLOBALS['post_season_players_stats']);
  $GLOBALS['post_season_players_stats'] = array();
  
  $selectCountSeasonStats = "select * from postseasonstats where year = " . $year;
  $res = $db->query($selectCountSeasonStats);
  
  while($row = $db->getRows($res))
  {
    $GLOBALS['post_season_players_stats'][$row['player']] = unserialize($row['stats']);
  }
}

function removeCareerSeasonStatsForGame($gameID, $playerID, $year, $type)
{
  global $pitching_stats_string, $statsString, $batting_stats_string;
  global $wins, $losses, $complete_games, $shut_outs, $saves, $save_opportunities, $innings_pitched, $hits_allowed;
  global $earned_runs, $home_runs_allowed, $hit_batters, $walks_allowed, $strike_outs, $runs_allowed, $earned_runs_average;
  
  global $at_bats, $runs, $hits, $doubles, $triples, $home_runs, $runs_batted_in, $total_bases, $strike_outs, $walks, $stolen_bases;
  global $hit_by_pitch, $sacrifice_flies, $caught_stealing, $on_base_percentage, $slugging_percentage, $batting_average;
  
  getGameStats($gameID);
  getCareerPlayerStats($playerID);
  
  $BAB = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$at_bats])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$at_bats] : 0;
  $BR = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$runs])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$runs] : 0;
  $BH = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$hits])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$hits] : 0;
  $BDOUB = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$doubles])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$doubles] : 0;
  $BTRIP = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$triples])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$triples] : 0;
  $BHR = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$home_runs])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$home_runs] : 0;
  $BRBI = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$runs_batted_in])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$runs_batted_in] : 0;
  $BTB = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$total_bases])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$total_bases] : 0;
  $BSO = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$strike_outs])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$strike_outs] : 0;
  $BBB = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$walks])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$walks] : 0;
  $BSB = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$stolen_bases])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$stolen_bases] : 0;
  $BHBP = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$hit_by_pitch])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$hit_by_pitch] : 0;
  $BSF = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$sacrifice_flies])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$sacrifice_flies] : 0;
  $BCS = isset($GLOBALS['players_stats'][$playerID][$batting_stats_string][$caught_stealing])? $GLOBALS['players_stats'][$playerID][$batting_stats_string][$caught_stealing] : 0;
  
  $PW = isset($GLOBALS['players_stats'][$playerID][$pitching_stats_string][$wins])? $GLOBALS['players_stats'][$playerID][$pitching_stats_string][$wins] : 0;
  $PL = isset($GLOBALS['players_stats'][$playerID][$pitching_stats_string][$losses])? $GLOBALS['players_stats'][$playerID][$pitching_stats_string][$losses] : 0;
  $PCG = isset($GLOBALS['players_stats'][$playerID][$pitching_stats_string][$complete_games])? $GLOBALS['players_stats'][$playerID][$pitching_stats_string][$complete_games] : 0;
  $PSHO = isset($GLOBALS['players_stats'][$playerID][$pitching_stats_string][$shut_outs])? $GLOBALS['players_stats'][$playerID][$pitching_stats_string][$shut_outs] : 0;
  $PSV = isset($GLOBALS['players_stats'][$playerID][$pitching_stats_string][$saves])? $GLOBALS['players_stats'][$playerID][$pitching_stats_string][$saves] : 0;
  $PSVO = isset($GLOBALS['players_stats'][$playerID][$pitching_stats_string][$save_opportunities])? $GLOBALS['players_stats'][$playerID][$pitching_stats_string][$save_opportunities] : 0;
  $PIP = isset($GLOBALS['players_stats'][$playerID][$pitching_stats_string][$innings_pitched])? $GLOBALS['players_stats'][$playerID][$pitching_stats_string][$innings_pitched] : 0;
  $PH = isset($GLOBALS['players_stats'][$playerID][$pitching_stats_string][$hits_allowed])? $GLOBALS['players_stats'][$playerID][$pitching_stats_string][$hits_allowed] : 0;
  $PER = isset($GLOBALS['players_stats'][$playerID][$pitching_stats_string][$earned_runs])? $GLOBALS['players_stats'][$playerID][$pitching_stats_string][$earned_runs] : 0;
  $PHR = isset($GLOBALS['players_stats'][$playerID][$pitching_stats_string][$home_runs_allowed])? $GLOBALS['players_stats'][$playerID][$pitching_stats_string][$home_runs_allowed] : 0;
  $PHBP = isset($GLOBALS['players_stats'][$playerID][$pitching_stats_string][$hit_batters])? $GLOBALS['players_stats'][$playerID][$pitching_stats_string][$hit_batters] : 0;
  $PBB = isset($GLOBALS['players_stats'][$playerID][$pitching_stats_string][$walks_allowed])? $GLOBALS['players_stats'][$playerID][$pitching_stats_string][$walks_allowed] : 0;
  $PSO = isset($GLOBALS['players_stats'][$playerID][$pitching_stats_string][$strike_outs])? $GLOBALS['players_stats'][$playerID][$pitching_stats_string][$strike_outs] : 0;
  
  if($type == "season")
  {
    getSeasonPlayerStats($playerID, $year);
    
    $GLOBALS['season_stats'][$batting_stats_string][$at_bats] -= $BAB;
    $GLOBALS['season_stats'][$batting_stats_string][$runs] -= $BR;
    $GLOBALS['season_stats'][$batting_stats_string][$hits] -= $BH;
    $GLOBALS['season_stats'][$batting_stats_string][$doubles] -= $BDOUB;
    $GLOBALS['season_stats'][$batting_stats_string][$triples] -= $BTRIP;
    $GLOBALS['season_stats'][$batting_stats_string][$home_runs] -= $BHR;
    $GLOBALS['season_stats'][$batting_stats_string][$runs_batted_in] -= $BRBI;
    $GLOBALS['season_stats'][$batting_stats_string][$total_bases] -= $BTB;
    $GLOBALS['season_stats'][$batting_stats_string][$strike_outs] -= $BSO;
    $GLOBALS['season_stats'][$batting_stats_string][$walks] -= $BBB;
    $GLOBALS['season_stats'][$batting_stats_string][$stolen_bases] -= $BSB;
    $GLOBALS['season_stats'][$batting_stats_string][$hit_by_pitch] -= $BHBP;
    $GLOBALS['season_stats'][$batting_stats_string][$sacrifice_flies] -= $BSF;
    $GLOBALS['season_stats'][$batting_stats_string][$caught_stealing] -= $BCS;
    
    
    $BTHBP = $GLOBALS['season_stats'][$batting_stats_string][$hit_by_pitch];
    $BTH = $GLOBALS['season_stats'][$batting_stats_string][$hits];
    $BTBB = $GLOBALS['season_stats'][$batting_stats_string][$walks];
    $BTAB = $GLOBALS['season_stats'][$batting_stats_string][$at_bats];
    $BTSF = $GLOBALS['season_stats'][$batting_stats_string][$sacrifice_flies];
    $BTTB = $GLOBALS['season_stats'][$batting_stats_string][$total_bases];
    
    if(($BTHBP + $BTAB + $BTBB + $BTSF) != 0)
    {
      $GLOBALS['season_stats'][$batting_stats_string][$on_base_percentage] = number_format(($BTHBP + $BTH + $BTBB) / ($BTHBP + $BTAB + $BTBB + $BTSF), 3);
    }
    else
    {
      $GLOBALS['season_stats'][$batting_stats_string][$on_base_percentage] = number_format(0, 3);
    }
    
    if(!empty($BTAB))
    {
      $GLOBALS['season_stats'][$batting_stats_string][$slugging_percentage] = number_format($BTTB / $BTAB, 3);
      $GLOBALS['season_stats'][$batting_stats_string][$batting_average] = number_format($BTH / $BTAB, 3);
    }
    else
    {
      $GLOBALS['season_stats'][$batting_stats_string][$slugging_percentage] = number_format(0, 3);
      $GLOBALS['season_stats'][$batting_stats_string][$batting_average] = number_format(0, 3);
    }
    
    $GLOBALS['season_stats'][$pitching_stats_string][$wins] -= $PW;
    $GLOBALS['season_stats'][$pitching_stats_string][$losses] -= $PL;
    $GLOBALS['season_stats'][$pitching_stats_string][$complete_games] -= $PCG;
    $GLOBALS['season_stats'][$pitching_stats_string][$shut_outs] -= $PSHO;
    $GLOBALS['season_stats'][$pitching_stats_string][$saves] -= $PSV;
    $GLOBALS['season_stats'][$pitching_stats_string][$save_opportunities] -= $PSVO;
    $GLOBALS['season_stats'][$pitching_stats_string][$innings_pitched] -= $PIP;
    $GLOBALS['season_stats'][$pitching_stats_string][$hits_allowed] -= $PH;
    $GLOBALS['season_stats'][$pitching_stats_string][$earned_runs] -= $PER;
    $GLOBALS['season_stats'][$pitching_stats_string][$home_runs_allowed] -= $PHR;
    $GLOBALS['season_stats'][$pitching_stats_string][$hit_batters] -= $PHBP;
    $GLOBALS['season_stats'][$pitching_stats_string][$walks_allowed] -= $PBB;
    $GLOBALS['season_stats'][$pitching_stats_string][$strike_outs] -= $PSO;
    $PTIP = $GLOBALS['season_stats'][$pitching_stats_string][$innings_pitched];
    $PTER = $GLOBALS['season_stats'][$pitching_stats_string][$earned_runs];
    
    if(!empty($PTIP))
    {
      $GLOBALS['season_stats'][$pitching_stats_string][$earned_runs_average] = number_format(($PTER * 9) / $PTIP, 2);
    }
    else if(!empty($PTER))
    {
      $GLOBALS['season_stats'][$pitching_stats_string][$earned_runs_average] = "Infinate";
    }
    else 
    {
      $GLOBALS['season_stats'][$pitching_stats_string][$earned_runs_average] = number_format(0, 2);
    }
    
    
    $GLOBALS['career_stats'][$batting_stats_string][$at_bats] -= $BAB;
    $GLOBALS['career_stats'][$batting_stats_string][$runs] -= $BR;
    $GLOBALS['career_stats'][$batting_stats_string][$hits] -= $BH;
    $GLOBALS['career_stats'][$batting_stats_string][$doubles] -= $BDOUB;
    $GLOBALS['career_stats'][$batting_stats_string][$triples] -= $BTRIP;
    $GLOBALS['career_stats'][$batting_stats_string][$home_runs] -= $BHR;
    $GLOBALS['career_stats'][$batting_stats_string][$runs_batted_in] -= $BRBI;
    $GLOBALS['career_stats'][$batting_stats_string][$total_bases] -= $BTB;
    $GLOBALS['career_stats'][$batting_stats_string][$strike_outs] -= $BSO;
    $GLOBALS['career_stats'][$batting_stats_string][$walks] -= $BBB;
    $GLOBALS['career_stats'][$batting_stats_string][$stolen_bases] -= $BSB;
    $GLOBALS['career_stats'][$batting_stats_string][$hit_by_pitch] -= $BHBP;
    $GLOBALS['career_stats'][$batting_stats_string][$sacrifice_flies] -= $BSF;
    $GLOBALS['career_stats'][$batting_stats_string][$caught_stealing] -= $BCS;
    
    
    $BTHBP = $GLOBALS['career_stats'][$batting_stats_string][$hit_by_pitch];
    $BTH = $GLOBALS['career_stats'][$batting_stats_string][$hits];
    $BTBB = $GLOBALS['career_stats'][$batting_stats_string][$walks];
    $BTAB = $GLOBALS['career_stats'][$batting_stats_string][$at_bats];
    $BTSF = $GLOBALS['career_stats'][$batting_stats_string][$sacrifice_flies];
    $BTTB = $GLOBALS['career_stats'][$batting_stats_string][$total_bases];
    
    if(($BTHBP + $BTAB + $BTBB + $BTSF) != 0)
    {
      $GLOBALS['career_stats'][$batting_stats_string][$on_base_percentage] = number_format(($BTHBP + $BTH + $BTBB) / ($BTHBP + $BTAB + $BTBB + $BTSF), 3);
    }
    else
    {
      $GLOBALS['career_stats'][$batting_stats_string][$on_base_percentage] = number_format(0, 3);
    }
    
    if(!empty($BTAB))
    {
      $GLOBALS['career_stats'][$batting_stats_string][$slugging_percentage] = number_format($BTTB / $BTAB, 3);
      $GLOBALS['career_stats'][$batting_stats_string][$batting_average] = number_format($BTH / $BTAB, 3);
    }
    else
    {
      $GLOBALS['career_stats'][$batting_stats_string][$slugging_percentage] = number_format(0, 3);
      $GLOBALS['career_stats'][$batting_stats_string][$batting_average] = number_format(0, 3);
    }
    
    $GLOBALS['career_stats'][$pitching_stats_string][$wins] -= $PW;
    $GLOBALS['career_stats'][$pitching_stats_string][$losses] -= $PL;
    $GLOBALS['career_stats'][$pitching_stats_string][$complete_games] -= $PCG;
    $GLOBALS['career_stats'][$pitching_stats_string][$shut_outs] -= $PSHO;
    $GLOBALS['career_stats'][$pitching_stats_string][$saves] -= $PSV;
    $GLOBALS['career_stats'][$pitching_stats_string][$save_opportunities] -= $PSVO;
    $GLOBALS['career_stats'][$pitching_stats_string][$innings_pitched] -= $PIP;
    $GLOBALS['career_stats'][$pitching_stats_string][$hits_allowed] -= $PH;
    $GLOBALS['career_stats'][$pitching_stats_string][$earned_runs] -= $PER;
    $GLOBALS['career_stats'][$pitching_stats_string][$home_runs_allowed] -= $PHR;
    $GLOBALS['career_stats'][$pitching_stats_string][$hit_batters] -= $PHBP;
    $GLOBALS['career_stats'][$pitching_stats_string][$walks_allowed] -= $PBB;
    $GLOBALS['career_stats'][$pitching_stats_string][$strike_outs] -= $PSO;
    $PTIP = $GLOBALS['career_stats'][$pitching_stats_string][$innings_pitched];
    $PTER = $GLOBALS['career_stats'][$pitching_stats_string][$earned_runs];
    
    if(!empty($PTIP))
    {
      $GLOBALS['career_stats'][$pitching_stats_string][$earned_runs_average] = number_format(($PTER * 9) / $PTIP, 2);
    }
    else if(!empty($PTER))
    {
      $GLOBALS['career_stats'][$pitching_stats_string][$earned_runs_average] = "Infinate";
    }
    else 
    {
      $GLOBALS['career_stats'][$pitching_stats_string][$earned_runs_average] = number_format(0, 2);
    }
    
    addCarrerStatsToDB($playerID);
    addSeasonStatsToDB($playerID, $year);
  }
  else if($type == "postseason")
  {
    getPostSeasonPlayerStats($playerID, $year);
    
    $GLOBALS['post_season_stats'][$batting_stats_string][$at_bats] -= $BAB;
    $GLOBALS['post_season_stats'][$batting_stats_string][$runs] -= $BR;
    $GLOBALS['post_season_stats'][$batting_stats_string][$hits] -= $BH;
    $GLOBALS['post_season_stats'][$batting_stats_string][$doubles] -= $BDOUB;
    $GLOBALS['post_season_stats'][$batting_stats_string][$triples] -= $BTRIP;
    $GLOBALS['post_season_stats'][$batting_stats_string][$home_runs] -= $BHR;
    $GLOBALS['post_season_stats'][$batting_stats_string][$runs_batted_in] -= $BRBI;
    $GLOBALS['post_season_stats'][$batting_stats_string][$total_bases] -= $BTB;
    $GLOBALS['post_season_stats'][$batting_stats_string][$strike_outs] -= $BSO;
    $GLOBALS['post_season_stats'][$batting_stats_string][$walks] -= $BBB;
    $GLOBALS['post_season_stats'][$batting_stats_string][$stolen_bases] -= $BSB;
    $GLOBALS['post_season_stats'][$batting_stats_string][$hit_by_pitch] -= $BHBP;
    $GLOBALS['post_season_stats'][$batting_stats_string][$sacrifice_flies] -= $BSF;
    $GLOBALS['post_season_stats'][$batting_stats_string][$caught_stealing] -= $BCS;
    
    
    $BTHBP = $GLOBALS['post_season_stats'][$batting_stats_string][$hit_by_pitch];
    $BTH = $GLOBALS['post_season_stats'][$batting_stats_string][$hits];
    $BTBB = $GLOBALS['post_season_stats'][$batting_stats_string][$walks];
    $BTAB = $GLOBALS['post_season_stats'][$batting_stats_string][$at_bats];
    $BTSF = $GLOBALS['post_season_stats'][$batting_stats_string][$sacrifice_flies];
    $BTTB = $GLOBALS['post_season_stats'][$batting_stats_string][$total_bases];
    
    if(($BTHBP + $BTAB + $BTBB + $BTSF) != 0)
    {
      $GLOBALS['post_season_stats'][$batting_stats_string][$on_base_percentage] = number_format(($BTHBP + $BTH + $BTBB) / ($BTHBP + $BTAB + $BTBB + $BTSF), 3);
    }
    else
    {
      $GLOBALS['post_season_stats'][$batting_stats_string][$on_base_percentage] = number_format(0, 3);
    }
    
    if(!empty($BTAB))
    {
      $GLOBALS['post_season_stats'][$batting_stats_string][$slugging_percentage] = number_format($BTTB / $BTAB, 3);
      $GLOBALS['post_season_stats'][$batting_stats_string][$batting_average] = number_format($BTH / $BTAB, 3);
    }
    else
    {
      $GLOBALS['post_season_stats'][$batting_stats_string][$slugging_percentage] = number_format(0, 3);
      $GLOBALS['post_season_stats'][$batting_stats_string][$batting_average] = number_format(0, 3);
    }
    
    $GLOBALS['post_season_stats'][$pitching_stats_string][$wins] -= $PW;
    $GLOBALS['post_season_stats'][$pitching_stats_string][$losses] -= $PL;
    $GLOBALS['post_season_stats'][$pitching_stats_string][$complete_games] -= $PCG;
    $GLOBALS['post_season_stats'][$pitching_stats_string][$shut_outs] -= $PSHO;
    $GLOBALS['post_season_stats'][$pitching_stats_string][$saves] -= $PSV;
    $GLOBALS['post_season_stats'][$pitching_stats_string][$save_opportunities] -= $PSVO;
    $GLOBALS['post_season_stats'][$pitching_stats_string][$innings_pitched] -= $PIP;
    $GLOBALS['post_season_stats'][$pitching_stats_string][$hits_allowed] -= $PH;
    $GLOBALS['post_season_stats'][$pitching_stats_string][$earned_runs] -= $PER;
    $GLOBALS['post_season_stats'][$pitching_stats_string][$home_runs_allowed] -= $PHR;
    $GLOBALS['post_season_stats'][$pitching_stats_string][$hit_batters] -= $PHBP;
    $GLOBALS['post_season_stats'][$pitching_stats_string][$walks_allowed] -= $PBB;
    $GLOBALS['post_season_stats'][$pitching_stats_string][$strike_outs] -= $PSO;
    $PTIP = $GLOBALS['post_season_stats'][$pitching_stats_string][$innings_pitched];
    $PTER = $GLOBALS['post_season_stats'][$pitching_stats_string][$earned_runs];
    
    if(!empty($PTIP))
    {
      $GLOBALS['post_season_stats'][$pitching_stats_string][$earned_runs_average] = number_format(($PTER * 9) / $PTIP, 2);
    }
    else if(!empty($PTER))
    {
      $GLOBALS['post_season_stats'][$pitching_stats_string][$earned_runs_average] = "Infinate";
    }
    else 
    {
      $GLOBALS['post_season_stats'][$pitching_stats_string][$earned_runs_average] = number_format(0, 2);
    }
    
    
    $GLOBALS['post_career_stats'][$batting_stats_string][$at_bats] -= $BAB;
    $GLOBALS['post_career_stats'][$batting_stats_string][$runs] -= $BR;
    $GLOBALS['post_career_stats'][$batting_stats_string][$hits] -= $BH;
    $GLOBALS['post_career_stats'][$batting_stats_string][$doubles] -= $BDOUB;
    $GLOBALS['post_career_stats'][$batting_stats_string][$triples] -= $BTRIP;
    $GLOBALS['post_career_stats'][$batting_stats_string][$home_runs] -= $BHR;
    $GLOBALS['post_career_stats'][$batting_stats_string][$runs_batted_in] -= $BRBI;
    $GLOBALS['post_career_stats'][$batting_stats_string][$total_bases] -= $BTB;
    $GLOBALS['post_career_stats'][$batting_stats_string][$strike_outs] -= $BSO;
    $GLOBALS['post_career_stats'][$batting_stats_string][$walks] -= $BBB;
    $GLOBALS['post_career_stats'][$batting_stats_string][$stolen_bases] -= $BSB;
    $GLOBALS['post_career_stats'][$batting_stats_string][$hit_by_pitch] -= $BHBP;
    $GLOBALS['post_career_stats'][$batting_stats_string][$sacrifice_flies] -= $BSF;
    $GLOBALS['post_career_stats'][$batting_stats_string][$caught_stealing] -= $BCS;
    
    
    $BTHBP = $GLOBALS['post_career_stats'][$batting_stats_string][$hit_by_pitch];
    $BTH = $GLOBALS['post_career_stats'][$batting_stats_string][$hits];
    $BTBB = $GLOBALS['post_career_stats'][$batting_stats_string][$walks];
    $BTAB = $GLOBALS['post_career_stats'][$batting_stats_string][$at_bats];
    $BTSF = $GLOBALS['post_career_stats'][$batting_stats_string][$sacrifice_flies];
    $BTTB = $GLOBALS['post_career_stats'][$batting_stats_string][$total_bases];
    
    if(($BTHBP + $BTAB + $BTBB + $BTSF) != 0)
    {
      $GLOBALS['post_career_stats'][$batting_stats_string][$on_base_percentage] = number_format(($BTHBP + $BTH + $BTBB) / ($BTHBP + $BTAB + $BTBB + $BTSF), 3);
    }
    else
    {
      $GLOBALS['post_career_stats'][$batting_stats_string][$on_base_percentage] = number_format(0, 3);
    }
    
    if(!empty($BTAB))
    {
      $GLOBALS['post_career_stats'][$batting_stats_string][$slugging_percentage] = number_format($BTTB / $BTAB, 3);
      $GLOBALS['post_career_stats'][$batting_stats_string][$batting_average] = number_format($BTH / $BTAB, 3);
    }
    else
    {
      $GLOBALS['post_career_stats'][$batting_stats_string][$slugging_percentage] = number_format(0, 3);
      $GLOBALS['post_career_stats'][$batting_stats_string][$batting_average] = number_format(0, 3);
    }
    
    $GLOBALS['post_career_stats'][$pitching_stats_string][$wins] -= $PW;
    $GLOBALS['post_career_stats'][$pitching_stats_string][$losses] -= $PL;
    $GLOBALS['post_career_stats'][$pitching_stats_string][$complete_games] -= $PCG;
    $GLOBALS['post_career_stats'][$pitching_stats_string][$shut_outs] -= $PSHO;
    $GLOBALS['post_career_stats'][$pitching_stats_string][$saves] -= $PSV;
    $GLOBALS['post_career_stats'][$pitching_stats_string][$save_opportunities] -= $PSVO;
    $GLOBALS['post_career_stats'][$pitching_stats_string][$innings_pitched] -= $PIP;
    $GLOBALS['post_career_stats'][$pitching_stats_string][$hits_allowed] -= $PH;
    $GLOBALS['post_career_stats'][$pitching_stats_string][$earned_runs] -= $PER;
    $GLOBALS['post_career_stats'][$pitching_stats_string][$home_runs_allowed] -= $PHR;
    $GLOBALS['post_career_stats'][$pitching_stats_string][$hit_batters] -= $PHBP;
    $GLOBALS['post_career_stats'][$pitching_stats_string][$walks_allowed] -= $PBB;
    $GLOBALS['post_career_stats'][$pitching_stats_string][$strike_outs] -= $PSO;
    $PTIP = $GLOBALS['post_career_stats'][$pitching_stats_string][$innings_pitched];
    $PTER = $GLOBALS['post_career_stats'][$pitching_stats_string][$earned_runs];
    
    if(!empty($PTIP))
    {
      $GLOBALS['post_career_stats'][$pitching_stats_string][$earned_runs_average] = number_format(($PTER * 9) / $PTIP, 2);
    }
    else if(!empty($PTER))
    {
      $GLOBALS['post_career_stats'][$pitching_stats_string][$earned_runs_average] = "Infinate";
    }
    else 
    {
      $GLOBALS['post_career_stats'][$pitching_stats_string][$earned_runs_average] = number_format(0, 2);
    }
    
    addPostSeasonCarrerStatsToDB($playerID);
    addPostSeasonStatsToDB($playerID, $year);
  }
}

function getStatYears()
{
  global $db;
  
  unset($GLOBALS['stat_years']);
  $GLOBALS['stat_years'] = array();
  
  $statYearsSQL = "select distinct year from seasonstats order by year desc";
  $res = $db->query($statYearsSQL);
  
  while($row = $db->getRows($res))
  {
    $GLOBALS['stat_years'][$row['year']]['year'] = $row['year'];
  }
}

?>
