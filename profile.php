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
require_once('stats.php');
require_once('constants.php');

$isView = false;
$id = $_SESSION['playerid'];

if(isset($_GET['id']) && $_SESSION['playerid'] != $_GET['id'])
{
  $isView = true;
  $id = $_GET['id'];
}

$actualYear = date("Y");
$yearToGet = isset($_GET['year'])?$_GET['year']:date("Y");

getStatYears();
getSinglePlayer($id);
getCareerPlayerStats($id);
getSeasonPlayerStats($id, $yearToGet);
getPostSeasonPlayerStats($id, $yearToGet);

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Profile &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/profile.css" type="text/css" media="screen" />
    <script type="text/javascript" src="js/core.js"></script>
  </head>

  <body>
    <?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
    <div id="profile">
      <?php 
        if(!$isView)
        {
          echo "<div id=\"edit\">";
            echo "<form method=\"GET\" action=\"edit.php\"><input class=\"btn-action\" type=\"submit\" value=\"Edit\"></form>";
            echo "<form method=\"GET\" action=\"changepass.php\"><input class=\"btn-action\" type=\"submit\" value=\"Change Password\"></form>";
          echo "</div>";
        }
      ?>
        <div id="picture">
          <img src="<?php if(!empty($players[$id]['img'])){echo $players[$id]['img'];}else{echo "images/nophoto.jpg";} ?>" />
				
          <?php
            if(!$isView)
            {
              echo "<form method=\"GET\" action=\"images.php\"><input class=\"btn-action\" type=\"submit\" value=\"Change Image\"></form>";
            }
          ?>
        </div>
        
        <div id="info">
        <div id="infoLeft">
          <ul>
            <li><label>First Name:</label> <?php echo $players[$id]['firstname']; ?></li>
            <li><label>Last Name:</label> <?php echo $players[$id]['lastname']; ?></li>
            <?php if(!empty($players[$id]['nickname'])){echo "<li><label>Nickname:</label> " . $players[$id]['nickname'] . "</li>";} ?>
            <li><label>Email:</label> <?php echo $players[$id]['email']; ?></li>
            <?php if(!empty($players[$id]['phone'])){echo "<li><label>Phone:</label> " . $players[$id]['phone'] . "</li>";} ?>
            <?php if(!empty($players[$id]['birthdate'])){echo "<li><label>Birthday:</label> " . $players[$id]['birthdate'] . "</li>";}?>
          </ul>
        </div>
		
        <div id="infoRight">
          <ul>
            <?php 
              if(!empty($players[$id]['positions']))
              {
                echo "<li><label>Positions:</label> ";
                $positions = explode(':', $players[$id]['positions']);
                $result = "";
                foreach($positions as $position)
                {
                  if($position != "")
                  {
                    $result .= $position . ", ";
                  } 
                }
                echo substr($result, 0, strlen($result)-2);
                echo "</li>";
              } 
            ?>
            <li><label>Bats:</label> <?php echo $players[$id]['bats']; ?></li>
            <li><label>Throws:</label> <?php echo $players[$id]['throws']; ?></li>
            <?php if($players[$id]['heightFT'] > 0){echo "<li><label>Height:</label> " . $players[$id]['heightFT'] . " ft " . $players[$id]['heightIN'] . " in</li>";} ?>
            <?php if($players[$id]['weight'] > 0){echo "<li><label>Weight:</label> " . $players[$id]['weight'] . " lbs</li>";} ?>
          </ul>
        </div>
        </div>
      </div>
      <div id="actions">
        <form method="GET" action="profile.php">
          <label>Season: </label>
          <input type="hidden" name="id" value="<?php echo $id ?>" />
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
              <tr>
                <td><?php echo (isset($season_stats[$batting_stats_string][$at_bats]) ? $season_stats[$batting_stats_string][$at_bats] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$runs]) ? $season_stats[$batting_stats_string][$runs] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$hits]) ? $season_stats[$batting_stats_string][$hits] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$doubles]) ? $season_stats[$batting_stats_string][$doubles] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$triples]) ? $season_stats[$batting_stats_string][$triples] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$home_runs]) ? $season_stats[$batting_stats_string][$home_runs] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$runs_batted_in]) ? $season_stats[$batting_stats_string][$runs_batted_in] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$total_bases]) ? $season_stats[$batting_stats_string][$total_bases] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$strike_outs]) ? $season_stats[$batting_stats_string][$strike_outs] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$walks]) ? $season_stats[$batting_stats_string][$walks] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$stolen_bases]) ? $season_stats[$batting_stats_string][$stolen_bases] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$hit_by_pitch]) ? $season_stats[$batting_stats_string][$hit_by_pitch] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$sacrifice_flies]) ? $season_stats[$batting_stats_string][$sacrifice_flies] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$caught_stealing]) ? $season_stats[$batting_stats_string][$caught_stealing] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$on_base_percentage]) ? $season_stats[$batting_stats_string][$on_base_percentage] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$slugging_percentage]) ? $season_stats[$batting_stats_string][$slugging_percentage] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$batting_stats_string][$batting_average]) ? $season_stats[$batting_stats_string][$batting_average] : "-"); ?></td>
              </tr>
            </table>
            <table>
              <tr>
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
              <tr>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$wins]) ? $season_stats[$pitching_stats_string][$wins] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$losses]) ? $season_stats[$pitching_stats_string][$losses] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$complete_games]) ? $season_stats[$pitching_stats_string][$complete_games] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$shut_outs]) ? $season_stats[$pitching_stats_string][$shut_outs] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$saves]) ? $season_stats[$pitching_stats_string][$saves] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$save_opportunities]) ? $season_stats[$pitching_stats_string][$save_opportunities] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$innings_pitched]) ? $season_stats[$pitching_stats_string][$innings_pitched] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$hits_allowed]) ? $season_stats[$pitching_stats_string][$hits_allowed] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$earned_runs]) ? $season_stats[$pitching_stats_string][$earned_runs] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$home_runs_allowed]) ? $season_stats[$pitching_stats_string][$home_runs_allowed] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$hit_batters]) ? $season_stats[$pitching_stats_string][$hit_batters] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$walks_allowed]) ? $season_stats[$pitching_stats_string][$walks_allowed] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$strike_outs]) ? $season_stats[$pitching_stats_string][$strike_outs] : "-"); ?></td>
                <td><?php echo (isset($season_stats[$pitching_stats_string][$earned_runs_average]) ? $season_stats[$pitching_stats_string][$earned_runs_average] : "-"); ?></td>
              </tr>
            </table>
          </div>
          <div class="career">
            <table>
              <tr>
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
              <tr>
                <td><?php echo (isset($career_stats[$batting_stats_string][$at_bats]) ? $career_stats[$batting_stats_string][$at_bats] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$runs]) ? $career_stats[$batting_stats_string][$runs] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$hits]) ? $career_stats[$batting_stats_string][$hits] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$doubles]) ? $career_stats[$batting_stats_string][$doubles] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$triples]) ? $career_stats[$batting_stats_string][$triples] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$home_runs]) ? $career_stats[$batting_stats_string][$home_runs] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$runs_batted_in]) ? $career_stats[$batting_stats_string][$runs_batted_in] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$total_bases]) ? $career_stats[$batting_stats_string][$total_bases] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$strike_outs]) ? $career_stats[$batting_stats_string][$strike_outs] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$walks]) ? $career_stats[$batting_stats_string][$walks] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$stolen_bases]) ? $career_stats[$batting_stats_string][$stolen_bases] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$hit_by_pitch]) ? $career_stats[$batting_stats_string][$hit_by_pitch] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$sacrifice_flies]) ? $career_stats[$batting_stats_string][$sacrifice_flies] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$caught_stealing]) ? $career_stats[$batting_stats_string][$caught_stealing] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$on_base_percentage]) ? $career_stats[$batting_stats_string][$on_base_percentage] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$slugging_percentage]) ? $career_stats[$batting_stats_string][$slugging_percentage] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$batting_stats_string][$batting_average]) ? $career_stats[$batting_stats_string][$batting_average] : "-"); ?></td>
              </tr>
            </table>
            <table>
              <tr>
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
              <tr>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$wins]) ? $career_stats[$pitching_stats_string][$wins] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$losses]) ? $career_stats[$pitching_stats_string][$losses] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$complete_games]) ? $career_stats[$pitching_stats_string][$complete_games] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$shut_outs]) ? $career_stats[$pitching_stats_string][$shut_outs] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$saves]) ? $career_stats[$pitching_stats_string][$saves] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$save_opportunities]) ? $career_stats[$pitching_stats_string][$save_opportunities] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$innings_pitched]) ? $career_stats[$pitching_stats_string][$innings_pitched] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$hits_allowed]) ? $career_stats[$pitching_stats_string][$hits_allowed] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$earned_runs]) ? $career_stats[$pitching_stats_string][$earned_runs] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$home_runs_allowed]) ? $career_stats[$pitching_stats_string][$home_runs_allowed] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$hit_batters]) ? $career_stats[$pitching_stats_string][$hit_batters] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$walks_allowed]) ? $career_stats[$pitching_stats_string][$walks_allowed] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$strike_outs]) ? $career_stats[$pitching_stats_string][$strike_outs] : "-"); ?></td>
                <td><?php echo (isset($career_stats[$pitching_stats_string][$earned_runs_average]) ? $career_stats[$pitching_stats_string][$earned_runs_average] : "-"); ?></td>
              </tr>
            </table>
          </div>
          <div class="postseason">
            <table>
              <tr>
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
              <tr>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$at_bats]) ? $post_season_stats[$batting_stats_string][$at_bats] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$runs]) ? $post_season_stats[$batting_stats_string][$runs] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$hits]) ? $post_season_stats[$batting_stats_string][$hits] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$doubles]) ? $post_season_stats[$batting_stats_string][$doubles] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$triples]) ? $post_season_stats[$batting_stats_string][$triples] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$home_runs]) ? $post_season_stats[$batting_stats_string][$home_runs] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$runs_batted_in]) ? $post_season_stats[$batting_stats_string][$runs_batted_in] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$total_bases]) ? $post_season_stats[$batting_stats_string][$total_bases] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$strike_outs]) ? $post_season_stats[$batting_stats_string][$strike_outs] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$walks]) ? $post_season_stats[$batting_stats_string][$walks] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$stolen_bases]) ? $post_season_stats[$batting_stats_string][$stolen_bases] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$hit_by_pitch]) ? $post_season_stats[$batting_stats_string][$hit_by_pitch] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$sacrifice_flies]) ? $post_season_stats[$batting_stats_string][$sacrifice_flies] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$caught_stealing]) ? $post_season_stats[$batting_stats_string][$caught_stealing] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$on_base_percentage]) ? $post_season_stats[$batting_stats_string][$on_base_percentage] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$slugging_percentage]) ? $post_season_stats[$batting_stats_string][$slugging_percentage] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$batting_stats_string][$batting_average]) ? $post_season_stats[$batting_stats_string][$batting_average] : "-"); ?></td>
              </tr>
            </table>
            <table>
              <tr>
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
              <tr>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$wins]) ? $post_season_stats[$pitching_stats_string][$wins] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$losses]) ? $post_season_stats[$pitching_stats_string][$losses] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$complete_games]) ? $post_season_stats[$pitching_stats_string][$complete_games] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$shut_outs]) ? $post_season_stats[$pitching_stats_string][$shut_outs] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$saves]) ? $post_season_stats[$pitching_stats_string][$saves] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$save_opportunities]) ? $post_season_stats[$pitching_stats_string][$save_opportunities] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$innings_pitched]) ? $post_season_stats[$pitching_stats_string][$innings_pitched] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$hits_allowed]) ? $post_season_stats[$pitching_stats_string][$hits_allowed] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$earned_runs]) ? $post_season_stats[$pitching_stats_string][$earned_runs] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$home_runs_allowed]) ? $post_season_stats[$pitching_stats_string][$home_runs_allowed] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$hit_batters]) ? $post_season_stats[$pitching_stats_string][$hit_batters] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$walks_allowed]) ? $post_season_stats[$pitching_stats_string][$walks_allowed] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$strike_outs]) ? $post_season_stats[$pitching_stats_string][$strike_outs] : "-"); ?></td>
                <td><?php echo (isset($post_season_stats[$pitching_stats_string][$earned_runs_average]) ? $post_season_stats[$pitching_stats_string][$earned_runs_average] : "-"); ?></td>
              </tr>
            </table>
          </div>
          <div class="postcareer">
            <table>
              <tr>
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
              <tr>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$at_bats]) ? $post_career_stats[$batting_stats_string][$at_bats] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$runs]) ? $post_career_stats[$batting_stats_string][$runs] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$hits]) ? $post_career_stats[$batting_stats_string][$hits] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$doubles]) ? $post_career_stats[$batting_stats_string][$doubles] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$triples]) ? $post_career_stats[$batting_stats_string][$triples] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$home_runs]) ? $post_career_stats[$batting_stats_string][$home_runs] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$runs_batted_in]) ? $post_career_stats[$batting_stats_string][$runs_batted_in] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$total_bases]) ? $post_career_stats[$batting_stats_string][$total_bases] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$strike_outs]) ? $post_career_stats[$batting_stats_string][$strike_outs] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$walks]) ? $post_career_stats[$batting_stats_string][$walks] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$stolen_bases]) ? $post_career_stats[$batting_stats_string][$stolen_bases] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$hit_by_pitch]) ? $post_career_stats[$batting_stats_string][$hit_by_pitch] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$sacrifice_flies]) ? $post_career_stats[$batting_stats_string][$sacrifice_flies] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$caught_stealing]) ? $post_career_stats[$batting_stats_string][$caught_stealing] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$on_base_percentage]) ? $post_career_stats[$batting_stats_string][$on_base_percentage] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$slugging_percentage]) ? $post_career_stats[$batting_stats_string][$slugging_percentage] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$batting_stats_string][$batting_average]) ? $post_career_stats[$batting_stats_string][$batting_average] : "-"); ?></td>
              </tr>
            </table>
            <table>
              <tr>
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
              <tr>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$wins]) ? $post_career_stats[$pitching_stats_string][$wins] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$losses]) ? $post_career_stats[$pitching_stats_string][$losses] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$complete_games]) ? $post_career_stats[$pitching_stats_string][$complete_games] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$shut_outs]) ? $post_career_stats[$pitching_stats_string][$shut_outs] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$saves]) ? $post_career_stats[$pitching_stats_string][$saves] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$save_opportunities]) ? $post_career_stats[$pitching_stats_string][$save_opportunities] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$innings_pitched]) ? $post_career_stats[$pitching_stats_string][$innings_pitched] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$hits_allowed]) ? $post_career_stats[$pitching_stats_string][$hits_allowed] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$earned_runs]) ? $post_career_stats[$pitching_stats_string][$earned_runs] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$home_runs_allowed]) ? $post_career_stats[$pitching_stats_string][$home_runs_allowed] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$hit_batters]) ? $post_career_stats[$pitching_stats_string][$hit_batters] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$walks_allowed]) ? $post_career_stats[$pitching_stats_string][$walks_allowed] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$strike_outs]) ? $post_career_stats[$pitching_stats_string][$strike_outs] : "-"); ?></td>
                <td><?php echo (isset($post_career_stats[$pitching_stats_string][$earned_runs_average]) ? $post_career_stats[$pitching_stats_string][$earned_runs_average] : "-"); ?></td>
              </tr>
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
