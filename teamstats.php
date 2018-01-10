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

require_once('stats.php');
require_once('player.php');
require_once('constants.php');

$actualYear = date("Y");
$yearToGet = isset($_GET['year'])?$_GET['year']:date("Y");

getStatYears();
getPlayers($yearToGet);
getCareerTeamStats();
getSeasonTeamStats($yearToGet);
getPostSeasonTeamStats($yearToGet);

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Team Stats &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <script type="text/javascript" src="js/core.js"></script>
  </head>
  
  <body>
    <?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
      <div id="actions">
        <form method="GET" action="teamstats.php">
          <label>Season: </label>
          <select name="year">
            <?php
              if(!array_key_exists($actualYear, $stat_years))
              {
                echo "<option" . ($yearToGet==$actualYear?" selected=\"selected\"":"") . " value=\"" . $actualYear . "\">" . $actualYear . "</option>";
              }
               
              foreach($stat_years as $stat_year)
              {
                echo "<option" . ($yearToGet==$stat_year['year']?" selected=\"selected\"":"") . " value=\"" . $stat_year['year'] . "\">" . $stat_year['year'] . "</option>";
              } 
            ?>
          </select>
          <input class="btn-action" type="submit" value="Go">
        </form>
      </div>
      <div id="stats" class="season">
        <ul class="tabs">
          <li class="sea"><a class="season">Season</a></li>
          <li class="car"><a class="career">Career</a></li>
          <li class="possea"><a class="postseason">Post Season</a></li>
          <li class="poscar"><a class="postcareer">Post Season Career</a></li>
        </ul>
        <div id="playerstats">
          <div class="season">
            <table>
              <tr>
                <th>Player</th>
                <th>AB</th>
                <th>R</th>
                <th>H</th>
                <th>2B</th>
                <th>3B</th>
                <th>HR</th>
                <th>RBI</th>
                <th>TB</th>
                <th>SO</th>
                <th>BB</th>
                <th>SB</th>
                <th>HBP</th>
                <th>SF</th>
                <th>CS</th>
                <th>OBP</th>
                <th>SLG</th>
                <th>AVG</th>
              </tr>
              <?php
                foreach($players as $player)
                {
                  echo "<tr>";
                    echo "<td>" . $player['firstname'] . " " . $player['lastname'] . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$at_bats])? $season_players_stats[$player['id']][$batting_stats_string][$at_bats] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$runs])? $season_players_stats[$player['id']][$batting_stats_string][$runs] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$hits])? $season_players_stats[$player['id']][$batting_stats_string][$hits] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$doubles])? $season_players_stats[$player['id']][$batting_stats_string][$doubles] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$triples])? $season_players_stats[$player['id']][$batting_stats_string][$triples] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$home_runs])? $season_players_stats[$player['id']][$batting_stats_string][$home_runs] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$runs_batted_in])? $season_players_stats[$player['id']][$batting_stats_string][$runs_batted_in] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$total_bases])? $season_players_stats[$player['id']][$batting_stats_string][$total_bases] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$strike_outs])? $season_players_stats[$player['id']][$batting_stats_string][$strike_outs] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$walks])? $season_players_stats[$player['id']][$batting_stats_string][$walks] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$stolen_bases])? $season_players_stats[$player['id']][$batting_stats_string][$stolen_bases] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$hit_by_pitch])? $season_players_stats[$player['id']][$batting_stats_string][$hit_by_pitch] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$sacrifice_flies])? $season_players_stats[$player['id']][$batting_stats_string][$sacrifice_flies] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$caught_stealing])? $season_players_stats[$player['id']][$batting_stats_string][$caught_stealing] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$on_base_percentage])? $season_players_stats[$player['id']][$batting_stats_string][$on_base_percentage] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$slugging_percentage])? $season_players_stats[$player['id']][$batting_stats_string][$slugging_percentage] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$batting_stats_string][$batting_average])? $season_players_stats[$player['id']][$batting_stats_string][$batting_average] : "-") . "</td>";
                  echo "</tr>";
                } 
              ?>
            </table>
            <table>
              <tr>
                <th>Player</th>
                <th>W</th>
                <th>L</th>
                <th>CG</th>
                <th>SHO</th>
                <th>SV</th>
                <th>SVO</th>
                <th>IP</th>
                <th>H</th>
                <th>ER</th>
                <th>HRA</th>
                <th>HB</th>
                <th>BB</th>
                <th>SO</th>
                <th>ERA</th>
              </tr>
              <?php
                foreach($players as $player)
                {
                  echo "<tr>";
                    echo "<td>" . $player['firstname'] . " " . $player['lastname'] . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$wins])? $season_players_stats[$player['id']][$pitching_stats_string][$wins] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$losses])? $season_players_stats[$player['id']][$pitching_stats_string][$losses] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$complete_games])? $season_players_stats[$player['id']][$pitching_stats_string][$complete_games] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$shut_outs])? $season_players_stats[$player['id']][$pitching_stats_string][$shut_outs] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$saves])? $season_players_stats[$player['id']][$pitching_stats_string][$saves] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$save_opportunities])? $season_players_stats[$player['id']][$pitching_stats_string][$save_opportunities] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$innings_pitched])? $season_players_stats[$player['id']][$pitching_stats_string][$innings_pitched] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$hits_allowed])? $season_players_stats[$player['id']][$pitching_stats_string][$hits_allowed] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$earned_runs])? $season_players_stats[$player['id']][$pitching_stats_string][$earned_runs] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$home_runs_allowed])? $season_players_stats[$player['id']][$pitching_stats_string][$home_runs_allowed] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$hit_batters])? $season_players_stats[$player['id']][$pitching_stats_string][$hit_batters] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$walks_allowed])? $season_players_stats[$player['id']][$pitching_stats_string][$walks_allowed] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$strike_outs])? $season_players_stats[$player['id']][$pitching_stats_string][$strike_outs] : "-") . "</td>";
                    echo "<td>" . (isset($season_players_stats[$player['id']][$pitching_stats_string][$earned_runs_average])? $season_players_stats[$player['id']][$pitching_stats_string][$earned_runs_average] : "-") . "</td>";
                  echo "</tr>";
                } 
              ?>
            </table>
          </div>
          <div class="career">
            <table>
              <tr>
                <th>Player</th>
                <th>AB</th>
                <th>R</th>
                <th>H</th>
                <th>2B</th>
                <th>3B</th>
                <th>HR</th>
                <th>RBI</th>
                <th>TB</th>
                <th>SO</th>
                <th>BB</th>
                <th>SB</th>
                <th>HBP</th>
                <th>SF</th>
                <th>CS</th>
                <th>OBP</th>
                <th>SLG</th>
                <th>AVG</th>
              </tr>
              <?php
                foreach($players as $player)
                {
                  echo "<tr>";
                    echo "<td>" . $player['firstname'] . " " . $player['lastname'] . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$at_bats])? $players_stats[$player['id']][$batting_stats_string][$at_bats] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$runs])? $players_stats[$player['id']][$batting_stats_string][$runs] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$hits])? $players_stats[$player['id']][$batting_stats_string][$hits] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$doubles])? $players_stats[$player['id']][$batting_stats_string][$doubles] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$triples])? $players_stats[$player['id']][$batting_stats_string][$triples] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$home_runs])? $players_stats[$player['id']][$batting_stats_string][$home_runs] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$runs_batted_in])? $players_stats[$player['id']][$batting_stats_string][$runs_batted_in] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$total_bases])? $players_stats[$player['id']][$batting_stats_string][$total_bases] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$strike_outs])? $players_stats[$player['id']][$batting_stats_string][$strike_outs] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$walks])? $players_stats[$player['id']][$batting_stats_string][$walks] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$stolen_bases])? $players_stats[$player['id']][$batting_stats_string][$stolen_bases] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$hit_by_pitch])? $players_stats[$player['id']][$batting_stats_string][$hit_by_pitch] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$sacrifice_flies])? $players_stats[$player['id']][$batting_stats_string][$sacrifice_flies] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$caught_stealing])? $players_stats[$player['id']][$batting_stats_string][$caught_stealing] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$on_base_percentage])? $players_stats[$player['id']][$batting_stats_string][$on_base_percentage] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$slugging_percentage])? $players_stats[$player['id']][$batting_stats_string][$slugging_percentage] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$batting_stats_string][$batting_average])? $players_stats[$player['id']][$batting_stats_string][$batting_average] : "-") . "</td>";
                  echo "</tr>";
                } 
              ?>
            </table>
            <table>
              <tr>
                <th>Player</th>
                <th>W</th>
                <th>L</th>
                <th>CG</th>
                <th>SHO</th>
                <th>SV</th>
                <th>SVO</th>
                <th>IP</th>
                <th>H</th>
                <th>ER</th>
                <th>HRA</th>
                <th>HB</th>
                <th>BB</th>
                <th>SO</th>
                <th>ERA</th>
              </tr>
              <?php
                foreach($players as $player)
                {
                  echo "<tr>";
                    echo "<td>" . $player['firstname'] . " " . $player['lastname'] . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$wins])? $players_stats[$player['id']][$pitching_stats_string][$wins] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$losses])? $players_stats[$player['id']][$pitching_stats_string][$losses] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$complete_games])? $players_stats[$player['id']][$pitching_stats_string][$complete_games] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$shut_outs])? $players_stats[$player['id']][$pitching_stats_string][$shut_outs] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$saves])? $players_stats[$player['id']][$pitching_stats_string][$saves] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$save_opportunities])? $players_stats[$player['id']][$pitching_stats_string][$save_opportunities] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$innings_pitched])? $players_stats[$player['id']][$pitching_stats_string][$innings_pitched] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$hits_allowed])? $players_stats[$player['id']][$pitching_stats_string][$hits_allowed] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$earned_runs])? $players_stats[$player['id']][$pitching_stats_string][$earned_runs] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$home_runs_allowed])? $players_stats[$player['id']][$pitching_stats_string][$home_runs_allowed] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$hit_batters])? $players_stats[$player['id']][$pitching_stats_string][$hit_batters] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$walks_allowed])? $players_stats[$player['id']][$pitching_stats_string][$walks_allowed] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$strike_outs])? $players_stats[$player['id']][$pitching_stats_string][$strike_outs] : "-") . "</td>";
                    echo "<td>" . (isset($players_stats[$player['id']][$pitching_stats_string][$earned_runs_average])? $players_stats[$player['id']][$pitching_stats_string][$earned_runs_average] : "-") . "</td>";
                  echo "</tr>";
                } 
              ?>
            </table>
          </div>
          <div class="postseason">
            <table>
              <tr>
                <th>Player</th>
                <th>AB</th>
                <th>R</th>
                <th>H</th>
                <th>2B</th>
                <th>3B</th>
                <th>HR</th>
                <th>RBI</th>
                <th>TB</th>
                <th>SO</th>
                <th>BB</th>
                <th>SB</th>
                <th>HBP</th>
                <th>SF</th>
                <th>CS</th>
                <th>OBP</th>
                <th>SLG</th>
                <th>AVG</th>
              </tr>
              <?php
                foreach($players as $player)
                {
                  echo "<tr>";
                    echo "<td>" . $player['firstname'] . " " . $player['lastname'] . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$at_bats])? $post_season_players_stats[$player['id']][$batting_stats_string][$at_bats] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$runs])? $post_season_players_stats[$player['id']][$batting_stats_string][$runs] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$hits])? $post_season_players_stats[$player['id']][$batting_stats_string][$hits] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$doubles])? $post_season_players_stats[$player['id']][$batting_stats_string][$doubles] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$triples])? $post_season_players_stats[$player['id']][$batting_stats_string][$triples] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$home_runs])? $post_season_players_stats[$player['id']][$batting_stats_string][$home_runs] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$runs_batted_in])? $post_season_players_stats[$player['id']][$batting_stats_string][$runs_batted_in] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$total_bases])? $post_season_players_stats[$player['id']][$batting_stats_string][$total_bases] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$strike_outs])? $post_season_players_stats[$player['id']][$batting_stats_string][$strike_outs] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$walks])? $post_season_players_stats[$player['id']][$batting_stats_string][$walks] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$stolen_bases])? $post_season_players_stats[$player['id']][$batting_stats_string][$stolen_bases] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$hit_by_pitch])? $post_season_players_stats[$player['id']][$batting_stats_string][$hit_by_pitch] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$sacrifice_flies])? $post_season_players_stats[$player['id']][$batting_stats_string][$sacrifice_flies] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$caught_stealing])? $post_season_players_stats[$player['id']][$batting_stats_string][$caught_stealing] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$on_base_percentage])? $post_season_players_stats[$player['id']][$batting_stats_string][$on_base_percentage] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$slugging_percentage])? $post_season_players_stats[$player['id']][$batting_stats_string][$slugging_percentage] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$batting_stats_string][$batting_average])? $post_season_players_stats[$player['id']][$batting_stats_string][$batting_average] : "-") . "</td>";
                  echo "</tr>";
                } 
              ?>
            </table>
            <table>
              <tr>
                <th>Player</th>
                <th>W</th>
                <th>L</th>
                <th>CG</th>
                <th>SHO</th>
                <th>SV</th>
                <th>SVO</th>
                <th>IP</th>
                <th>H</th>
                <th>ER</th>
                <th>HRA</th>
                <th>HB</th>
                <th>BB</th>
                <th>SO</th>
                <th>ERA</th>
              </tr>
              <?php
                foreach($players as $player)
                {
                  echo "<tr>";
                    echo "<td>" . $player['firstname'] . " " . $player['lastname'] . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$wins])? $post_season_players_stats[$player['id']][$pitching_stats_string][$wins] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$losses])? $post_season_players_stats[$player['id']][$pitching_stats_string][$losses] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$complete_games])? $post_season_players_stats[$player['id']][$pitching_stats_string][$complete_games] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$shut_outs])? $post_season_players_stats[$player['id']][$pitching_stats_string][$shut_outs] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$saves])? $post_season_players_stats[$player['id']][$pitching_stats_string][$saves] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$save_opportunities])? $post_season_players_stats[$player['id']][$pitching_stats_string][$save_opportunities] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$innings_pitched])? $post_season_players_stats[$player['id']][$pitching_stats_string][$innings_pitched] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$hits_allowed])? $post_season_players_stats[$player['id']][$pitching_stats_string][$hits_allowed] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$earned_runs])? $post_season_players_stats[$player['id']][$pitching_stats_string][$earned_runs] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$home_runs_allowed])? $post_season_players_stats[$player['id']][$pitching_stats_string][$home_runs_allowed] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$hit_batters])? $post_season_players_stats[$player['id']][$pitching_stats_string][$hit_batters] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$walks_allowed])? $post_season_players_stats[$player['id']][$pitching_stats_string][$walks_allowed] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$strike_outs])? $post_season_players_stats[$player['id']][$pitching_stats_string][$strike_outs] : "-") . "</td>";
                    echo "<td>" . (isset($post_season_players_stats[$player['id']][$pitching_stats_string][$earned_runs_average])? $post_season_players_stats[$player['id']][$pitching_stats_string][$earned_runs_average] : "-") . "</td>";
                  echo "</tr>";
                } 
              ?>
            </table>
          </div>
          
          <div class="postcareer">
            <table>
              <tr>
                <th>Player</th>
                <th>AB</th>
                <th>R</th>
                <th>H</th>
                <th>2B</th>
                <th>3B</th>
                <th>HR</th>
                <th>RBI</th>
                <th>TB</th>
                <th>SO</th>
                <th>BB</th>
                <th>SB</th>
                <th>HBP</th>
                <th>SF</th>
                <th>CS</th>
                <th>OBP</th>
                <th>SLG</th>
                <th>AVG</th>
              </tr>
              <?php
                foreach($players as $player)
                {
                  echo "<tr>";
                    echo "<td>" . $player['firstname'] . " " . $player['lastname'] . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$at_bats])? $post_players_stats[$player['id']][$batting_stats_string][$at_bats] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$runs])? $post_players_stats[$player['id']][$batting_stats_string][$runs] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$hits])? $post_players_stats[$player['id']][$batting_stats_string][$hits] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$doubles])? $post_players_stats[$player['id']][$batting_stats_string][$doubles] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$triples])? $post_players_stats[$player['id']][$batting_stats_string][$triples] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$home_runs])? $post_players_stats[$player['id']][$batting_stats_string][$home_runs] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$runs_batted_in])? $post_players_stats[$player['id']][$batting_stats_string][$runs_batted_in] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$total_bases])? $post_players_stats[$player['id']][$batting_stats_string][$total_bases] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$strike_outs])? $post_players_stats[$player['id']][$batting_stats_string][$strike_outs] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$walks])? $post_players_stats[$player['id']][$batting_stats_string][$walks] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$stolen_bases])? $post_players_stats[$player['id']][$batting_stats_string][$stolen_bases] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$hit_by_pitch])? $post_players_stats[$player['id']][$batting_stats_string][$hit_by_pitch] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$sacrifice_flies])? $post_players_stats[$player['id']][$batting_stats_string][$sacrifice_flies] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$caught_stealing])? $post_players_stats[$player['id']][$batting_stats_string][$caught_stealing] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$on_base_percentage])? $post_players_stats[$player['id']][$batting_stats_string][$on_base_percentage] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$slugging_percentage])? $post_players_stats[$player['id']][$batting_stats_string][$slugging_percentage] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$batting_stats_string][$batting_average])? $post_players_stats[$player['id']][$batting_stats_string][$batting_average] : "-") . "</td>";
                  echo "</tr>";
                } 
              ?>
            </table>
            <table>
              <tr>
                <th>Player</th>
                <th>W</th>
                <th>L</th>
                <th>CG</th>
                <th>SHO</th>
                <th>SV</th>
                <th>SVO</th>
                <th>IP</th>
                <th>H</th>
                <th>ER</th>
                <th>HRA</th>
                <th>HB</th>
                <th>BB</th>
                <th>SO</th>
                <th>ERA</th>
              </tr>
              <?php
                foreach($players as $player)
                {
                  echo "<tr>";
                    echo "<td>" . $player['firstname'] . " " . $player['lastname'] . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$wins])? $post_players_stats[$player['id']][$pitching_stats_string][$wins] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$losses])? $post_players_stats[$player['id']][$pitching_stats_string][$losses] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$complete_games])? $post_players_stats[$player['id']][$pitching_stats_string][$complete_games] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$shut_outs])? $post_players_stats[$player['id']][$pitching_stats_string][$shut_outs] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$saves])? $post_players_stats[$player['id']][$pitching_stats_string][$saves] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$save_opportunities])? $post_players_stats[$player['id']][$pitching_stats_string][$save_opportunities] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$innings_pitched])? $post_players_stats[$player['id']][$pitching_stats_string][$innings_pitched] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$hits_allowed])? $post_players_stats[$player['id']][$pitching_stats_string][$hits_allowed] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$earned_runs])? $post_players_stats[$player['id']][$pitching_stats_string][$earned_runs] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$home_runs_allowed])? $post_players_stats[$player['id']][$pitching_stats_string][$home_runs_allowed] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$hit_batters])? $post_players_stats[$player['id']][$pitching_stats_string][$hit_batters] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$walks_allowed])? $post_players_stats[$player['id']][$pitching_stats_string][$walks_allowed] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$strike_outs])? $post_players_stats[$player['id']][$pitching_stats_string][$strike_outs] : "-") . "</td>";
                    echo "<td>" . (isset($post_players_stats[$player['id']][$pitching_stats_string][$earned_runs_average])? $post_players_stats[$player['id']][$pitching_stats_string][$earned_runs_average] : "-") . "</td>";
                  echo "</tr>";
                } 
              ?>
            </table>
          </div>
          
          
        </div>
      </div>
      <script type="text/javascript">
	  $$('#stats .tabs a').addEvent('click', function(evt) {
	    $('stats').setProperty('class', new Event(evt).target.getProperty('class'));
	  });
	</script>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>