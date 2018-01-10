<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

$isLoggedInGameResults = false;

if (isset($_SESSION['playerid'])) 
{
  $isLoggedInGameResults = true;
}

$id = $_GET['id'];

require_once('stats.php');
require_once('game.php');
require_once('player.php');
require_once('constants.php');

getGame($id);
getGameStats($id);

list($year, $month, $day, $hr, $min, $sec) = explode('[-: ]', $games[$id]['scheduledate']);

getPlayers($year);

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Game Results &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/gameresults.css" type="text/css" media="screen" />
    <script type="text/javascript" src="js/core.js"></script>
  </head>
  
  <body>
    <?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
      <?php 
        if($isLoggedInGameResults)
        {
          echo "<div id=\"actions\">";            
            if($_SESSION['priv'] == 1){echo "<form method=\"GET\" action=\"editgameresults.php\"><input type=\"hidden\" name=\"id\" value=\"" . $id . "\" /><input class=\"btn-action\" type=\"submit\" value=\"Edit Results\"></form>";}
            echo "<form method=\"GET\" action=\"gameimageupload.php\"><input type=\"hidden\" name=\"id\" value=\"" . $id . "\" /><input class=\"btn-action\" type=\"submit\" value=\"Upload Images\"></form>";
          echo "</div>";
        } 
      ?>
      <div id="teams">Mad Dogs v.s <?php echo $games[$id]['opponent']; ?></div>
      <div id="boxscore">
        <?php 
          $innings = (!empty($box_score['innings']) ? $box_score['innings'] : 9);
          echo "<table>";
            $header = "<tr><th>Team</th>";
            $maddogs = "<tr><td>Mad Dogs</td>";
            $opponent = "<tr><td>" . $games[$id]['opponent'] . "</td>";
            for($i=1; $i <= $innings; $i++)
            {
              $header .= "<th>" . $i . "</th>";
              $maddogs .= "<td>" . (isset($box_score['m' . $i]) && $box_score['m' . $i] != ""?$box_score['m' . $i]:"-") . "</td>";
              $opponent .= "<td>" . (isset($box_score['o' . $i]) && $box_score['o' . $i] != ""?$box_score['o' . $i]:"-") . "</td>";
            }
            $header .= "<th>R</th><th>H</th><th>E</th>";
            $maddogs .= "<td>" . (isset($box_score['mr']) && $box_score['mr'] != ""?$box_score['mr']:"-") . "</td><td>" . (isset($box_score['mh']) && $box_score['mh'] != ""?$box_score['mh']:"-") . "</td><td>" . (isset($box_score['me']) && $box_score['me'] != ""?$box_score['me']:"-") . "</td>";
            $opponent .= "<td>" . (isset($box_score['or']) && $box_score['or'] != ""?$box_score['or']:"-") . "</td><td>" . (isset($box_score['oh']) && $box_score['oh'] != ""?$box_score['oh']:"-") . "</td><td>" . (isset($box_score['oe']) && $box_score['oe'] != ""?$box_score['oe']:"-") . "</td>";
            
            $header .= "</tr>";
            $maddogs .= "</tr>";
            $opponent .= "</tr>";
            echo $header;
            echo $maddogs;
            echo $opponent;
          echo "</table>";
          
          $wp = "";
          $lp = "";
          $sv = "";
          foreach($players as $player)
          {
            if(!empty($players_stats[$player['id']][$pitching_stats_string][$wins]))
            {
              $wp .= "WP: " . $player['firstname'] . " " . $player['lastname'];
            }
            
            if(!empty($players_stats[$player['id']][$pitching_stats_string][$losses]))
            {
              $lp .= "LP: " . $player['firstname'] . " " . $player['lastname'];
            }
            
            if(!empty($players_stats[$player['id']][$pitching_stats_string][$complete_games]))
            {
              $wp .= " CG";
            }
            
            if(!empty($players_stats[$player['id']][$pitching_stats_string][$shut_outs]))
            {
              $wp .= " SHO";
            }
            
            if(!empty($players_stats[$player['id']][$pitching_stats_string][$saves]))
            {
              $sv .= "SV: " . $player['firstname'] . " " . $player['lastname'];
            }
          }
          
          if(!empty($wp))
          {
            echo "<p>" . $wp . "</p>";
          }
          
          if(!empty($lp))
          {
            echo "<p>" . $lp . "</p>";
          }
          if(!empty($sv))
          {
            echo "<p>" . $sv . "</p>";
          }
        ?>
      </div>
      <?php
        if($isLoggedInGameResults)
        {
          echo "<div id=\"stats\" class=\"batting\">";
            echo "<ul class=\"tabs\">";
              echo "<li class=\"bat\"><a class=\"batting\">Batting</a></li>";
              echo "<li class=\"pit\"><a class=\"pitching\">Pitching</a></li>";
            echo "</ul>"; 
            echo "<div id=\"gamestats\">";
              echo "<div class=\"batting\">";
                echo "<table>";
                  echo "<tr>";
                    echo "<th>Player</th>";
                    echo "<th>AB</th>";
                    echo "<th>R</th>";
                    echo "<th>H</th>";
                    echo "<th>2B</th>";
                    echo "<th>3B</th>";
                    echo "<th>HR</th>";
                    echo "<th>RBI</th>";
                    echo "<th>TB</th>";
                    echo "<th>SO</th>";
                    echo "<th>BB</th>";
                    echo "<th>SB</th>";
                    echo "<th>HBP</th>";
                    echo "<th>SF</th>";
                    echo "<th>CS</th>";
                    echo "<th>OBP</th>";
                    echo "<th>SLG</th>";
                    echo "<th>AVG</th>";
                  echo "</tr>";
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
                echo "</table>";
              echo"</div>";
              echo "<div class=\"pitching\">";
                echo "<table>";
                  echo "<tr>";
                    echo "<th>Player</th>";
                    echo "<th>IP</th>";
                    echo "<th>H</th>";
                    echo "<th>ER</th>";
                    echo "<th>HRA</th>";
                    echo "<th>HB</th>";
                    echo "<th>BB</th>";
                    echo "<th>SO</th>";
                    echo "<th>ERA</th>";
                  echo "</tr>";
                  foreach($players as $player)
                  {
                    echo "<tr>";
                      echo "<td>" . $player['firstname'] . " " . $player['lastname'] . "</td>";
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
                echo "</table>";
              echo "</div>";
            echo "</div>";
          echo "</div>";
          echo "<script type=\"text/javascript\">";
	        echo "$$('#stats .tabs a').addEvent('click', function(evt) {";
	          echo "$('stats').setProperty('class', new Event(evt).target.getProperty('class'));";
	        echo "});";
	      echo "</script>";
        }
      ?>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>
