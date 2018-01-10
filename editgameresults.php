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
require_once('player.php');
require_once('stats.php');
require_once('constants.php');

$id = $_GET['id'];

getGame($id);

list($year, $month, $day, $hr, $min, $sec) = explode('[-: ]', $games[$id]['scheduledate']);
            
$isPM = false;

if($hr >= 12)
{
  $isPM = true;

  if($hr != 12)
  {
    $hr -= 12;
  }
}
else if ($hr == 0)
{
  $hr += 12;
}

getPlayers($year);

getGameStats($id);

$isEdit = false;

if(!empty($players_stats))
{
  $isEdit = true;
}

$isEditBoxscore = false;

if(!empty($box_score))
{
  $isEditBoxscore = true;
}

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Edit Results &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/formcheck.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/editresults.css" type="text/css" media="screen" />
    <script language="javascript" type="text/javascript" src="js/edittable.js"></script>
    <script type="text/javascript" src="js/core.js"></script>
	<script type="text/javascript" src="js/more.js"></script>
	<script type="text/javascript" src="js/en.js"> </script>
	<script type="text/javascript" src="js/formcheck.js"> </script>
	<script type="text/javascript">
      window.addEvent('domready', function(){
        new FormCheck('editgamestats');
      });
	</script>
  </head>

  <body>
    <?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
      <div id="teams">Mad Dogs v.s <?php echo $games[$id]['opponent']; ?></div>
      <form method="POST" action="editgameresultssubmit.php" class="editgamestats" id="editgamestats">
      <input type="hidden" name="gameid" value="<?php echo $id ?>" />
      <input type="hidden" name="isEdit" value="<?php echo $isEdit ?>" />
      
      <?php
        if($isEditBoxscore)
        {
          echo "<input type=\"hidden\" name=\"innings\" id=\"innings\" value=\"" . $box_score['innings']  . "\" />";
        }
        else
        {
          echo "<input type=\"hidden\" name=\"innings\" id=\"innings\" value=\"9\" />";
        } 
      ?>
      
      <div id="actions">
        <input type="button" class="btn-action" value="Add inning" onclick="addColumn('scores')" />
	    <input type="button" class="btn-action" value="Remove inning" onclick="deleteColumn('scores')" />
        <input type="submit" class="btn-action" value="Save">
      </div>
      <div id="boxscore">
      <?php
        echo "<table id=\"scores\">";
          if(!$isEditBoxscore)
          { 
            echo "<thead>";
              echo "<tr>";
                echo "<th>Team</th>";
                echo "<th>1</th>";
                echo "<th>2</th>";
                echo "<th>3</th>";
                echo "<th>4</th>";
                echo "<th>5</th>";
                echo "<th>6</th>";
                echo "<th>7</th>";
                echo "<th>8</th>";
                echo "<th>9</th>";
              echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
              echo "<tr>";
                echo "<td>Mad Dogs</td>";
                echo "<td><input type=\"text\" name=\"m1\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"m2\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"m3\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"m4\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"m5\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"m6\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"m7\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"m8\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"m9\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
              echo "</tr>";
              echo "<tr>";
                echo "<td>" . $games[$id]['opponent'] . "</td>";
                echo "<td><input type=\"text\" name=\"o1\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"o2\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"o3\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"o4\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"o5\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"o6\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"o7\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"o8\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
                echo "<td><input type=\"text\" name=\"o9\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\"/></td>";
              echo "</tr>";
            echo "</tbody>";
          }
          else 
          {
            $innings = $box_score['innings'];
            $header = "<tr><th>Team</th>";
            $maddogs = "<tr><td>Mad Dogs</td>";
            $opponent = "<tr><td>" . $games[$id]['opponent'] . "</td>";
            
            for($i=1; $i <= $innings; $i++)
            {
              $header .= "<th>" . $i . "</th>";
              $maddogs .= "<td><input type=\"text\" name=\"m" . $i . "\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\" value=\"" . $box_score['m' . $i] . "\"/></td>";
              $opponent .= "<td><input type=\"text\" name=\"o" . $i . "\" size=\"2\" class=\"validate['length[2]','digit[0,99]']\" value=\"" . $box_score['o' . $i] . "\"/></td>";
            }
            
            echo "<thead>";
            echo $header;
            echo "</thead>";
            echo "<tbody>";
            echo $maddogs;
            echo $opponent;
            echo "</tbody>";
          }
        echo "</table>";
      ?>
        <table>
          <tr>
            <th>R</th>
            <th>H</th>
            <th>E</th>
          </tr>
          <tr>
            <td><input type="text" name="mr" size="2" class="validate['length[2]','digit[0,99]']" value="<?php if($isEditBoxscore){echo $box_score['mr'];}?>"/></td>
            <td><input type="text" name="mh" size="2" class="validate['length[2]','digit[0,99]']" value="<?php if($isEditBoxscore){echo $box_score['mh'];}?>"/></td>
            <td><input type="text" name="me" size="2" class="validate['length[2]','digit[0,99]']" value="<?php if($isEditBoxscore){echo $box_score['me'];}?>"/></td>
          </tr>
          <tr>
            <td><input type="text" name="or" size="2" class="validate['length[2]','digit[0,99]']" value="<?php if($isEditBoxscore){echo $box_score['or'];}?>"/></td>
            <td><input type="text" name="oh" size="2" class="validate['length[2]','digit[0,99]']" value="<?php if($isEditBoxscore){echo $box_score['oh'];}?>"/></td>
            <td><input type="text" name="oe" size="2" class="validate['length[2]','digit[0,99]']" value="<?php if($isEditBoxscore){echo $box_score['oe'];}?>"/></td>
          </tr>
        </table>
      </div>
      <div id="gameresults">
        <div id="batting">
          <table>
            <tr>
              <th>Name</th>
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
            </tr>
            <?php
              foreach($players as $player)
              {
                echo "<tr>";
                  echo "<td>" . $player['firstname'] . " " . $player['lastname'] . "</td>";
                  echo "<td><input type=\"text\" name=\"bab" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$at_bats] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"br" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$runs] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"bh" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$hits] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"b2b" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$doubles] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"b3b" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$triples] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"bhr" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$home_runs] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"brbi" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$runs_batted_in] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"btb" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$total_bases] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"bso" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$strike_outs] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"bbb" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$walks] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"bsb" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$stolen_bases] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"bhbp" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$hit_by_pitch] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"bsf" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$sacrifice_flies] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"bcs" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$batting_stats_string][$caught_stealing] : "") . "\"/></td>";
                echo "</tr>";
              } 
            ?>
          </table>
        </div>
        <div id="pitching">
          <table>
            <tr>
              <th>Name</th>
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
            </tr>
            <?php 
              foreach($players as $player)
              {
                echo "<tr>";
                  echo "<td>" . $player['firstname'] . " " . $player['lastname'] . "</td>";
                  echo "<td><select name=\"pw" . $player['id'] ."\"><option>Y</option><option " . (!$isEdit || $players_stats[$player['id']][$pitching_stats_string][$wins] != 1 ? "selected=\"selected\"" : "") . ">N</option></select></td>";
                  echo "<td><select name=\"pl" . $player['id'] ."\"><option>Y</option><option " . (!$isEdit || $players_stats[$player['id']][$pitching_stats_string][$losses] != 1 ? "selected=\"selected\"" : "") . ">N</option></select></td>";
                  echo "<td><select name=\"pcg" . $player['id'] ."\"><option>Y</option><option " . (!$isEdit || $players_stats[$player['id']][$pitching_stats_string][$complete_games] != 1 ? "selected=\"selected\"" : "") . ">N</option></select></td>";
                  echo "<td><select name=\"psho" . $player['id'] ."\"><option>Y</option><option " . (!$isEdit || $players_stats[$player['id']][$pitching_stats_string][$shut_outs] != 1 ? "selected=\"selected\"" : "") . ">N</option></select></td>";
                  echo "<td><select name=\"psv" . $player['id'] ."\"><option>Y</option><option " . (!$isEdit || $players_stats[$player['id']][$pitching_stats_string][$saves] != 1 ? "selected=\"selected\"" : "") . ">N</option></select></td>";
                  echo "<td><select name=\"psvo" . $player['id'] ."\"><option>Y</option><option " . (!$isEdit || $players_stats[$player['id']][$pitching_stats_string][$save_opportunities] != 1 ? "selected=\"selected\"" : "") . ">N</option></select></td>";
                  echo "<td><input type=\"text\" name=\"pip" . $player['id'] . "\" size=\"2\" value=\"" . ($isEdit ? $players_stats[$player['id']][$pitching_stats_string][$innings_pitched] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"ph" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$pitching_stats_string][$hits_allowed] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"per" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$pitching_stats_string][$earned_runs] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"phra" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$pitching_stats_string][$home_runs_allowed] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"phb" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$pitching_stats_string][$hit_batters] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"pbb" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$pitching_stats_string][$walks_allowed] : "") . "\"/></td>";
                  echo "<td><input type=\"text\" name=\"pso" . $player['id'] . "\" size=\"2\" class=\"validate['length[3]','digit[0,999]']\" value=\"" . ($isEdit ? $players_stats[$player['id']][$pitching_stats_string][$strike_outs] : "") . "\"/></td>";
                echo "</tr>";
              }
            ?>
          </table>
        </div>
      </div>
    </form>
  </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>
