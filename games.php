<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

$isLoggedInGames = false;

if (isset($_SESSION['playerid'])) 
{
  $isLoggedInGames = true;
}

$pageUp = $_GET['pageUp'];
$pageRes = $_GET['pageRes'];
$tab = $_GET['tab'];

if(empty($pageUp))
{
  $pageUp = 1;
}

if(empty($pageRes))
{
  $pageRes = 1;
}

if(empty($tab))
{
  $tab = "upcoming";
}

$actualYear = date("Y");
$curGameYear = isset($_GET['year'])?$_GET['year']:date("Y");

require_once('game.php');

getGameYears();
getUpcomingGames(15, $pageUp, $curGameYear);
getDoneGames(15, $pageRes, $curGameYear);

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Games &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/games.css" type="text/css" media="screen" />
    <script type="text/javascript" src="js/core.js"></script>
  </head>

  <body>
    <?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
    <div id="actionscontainer">
    <div id="actions">
    <?php 
      if($isLoggedInGames)
      {
        if($_SESSION['priv'] == 1){echo "<p><form method=\"GET\" action=\"schedule.php\"><input class=\"btn-action\" type=\"submit\" value=\"Schedule a game\"></form></p>";}
        if($_SESSION['priv'] == 1){echo "<p><form method=\"GET\" action=\"addfield.php\"><input class=\"btn-action\" type=\"submit\" value=\"Add a field\"></form></p>";}
        echo "<p><form method=\"GET\" action=\"allfields.php\"><input class=\"btn-action\" type=\"submit\" value=\"See all fields\"></form></p>";
      } 
      
      echo "<p><form method=\"GET\" action=\"games.php\">";
        echo "<label>Season: </label>";
        echo "<select name=\"year\">";
          if(!array_key_exists($actualYear, $gameYears))
          {
            echo "<option" . ($curYear==$actualYear?" selected=\"selected\"":"") . " value=\"" . $actualYear . "\">" . $actualYear . "</option>";
          }
           
          foreach($gameYears as $gameYear)
          {
            echo "<option" . ($curGameYear==$gameYear['year']?" selected=\"selected\"":"") . " value=\"" . $gameYear['year'] . "\">" . $gameYear['year'] . "</option>";
          }
        echo "</select>";
      echo"<input class=\"btn-action\" type=\"submit\" value=\"Go\"></form></p>"; 
    ?>
    </div>
    </div>
    <?php
        echo "<div id=\"games\" class=\"";
        
        if($tab == "upcoming" && $curGameYear == date("Y"))
        {
          echo "upcoming";
        }
        else 
        {
          echo "results";
        }
        
        echo "\">";
        echo "<ul class=\"tabs\">";
          if($curGameYear == date("Y")){echo "<li class=\"up\"><a class=\"upcoming\">Upcoming</a></li>";}
          echo "<li class=\"res\"><a class=\"results\">Completed</a></li>";
        echo "</ul>";
        
        echo "<div id=\"gamescontainer\">";
      
      echo "<div class=\"upcoming\">";
      if(!empty($upcominggames))
      {
          echo "<table>";
            echo "<tr>";
              echo "<th>Date</th>";
              echo "<th>Opponent</th>";
              echo "<th>Type</th>";
              echo "<th>Field</th>";
              echo "<th>More Info</th>";
              if($isLoggedInGames && $_SESSION['priv'] == 1){echo "<th>Edit Link</th>";}
              if($isLoggedInGames && $_SESSION['priv'] == 1){echo "<th>Delete Link</th>";}
            echo "</tr>";

          foreach($upcominggames as $game)
          {
            echo "<tr>";
              list($year, $month, $day, $hr, $min, $sec) = explode('[-: ]', $game['scheduledate']);
            
              $isPM = false;

              if($hr >= 12)
              {
                $isPM = true;
                
                if($hr != 12)
                {
                  $hr -= 12;
                }
              }

              echo "<tr>";
              echo "<td>" . $month . "/" . $day . "/" . $year . "&nbsp;&nbsp;&nbsp;&nbsp;"; 
              
              if($hr != 0)
              {
                echo $hr . ":" . $min;
                if($isPM)
                {
                  echo " PM";
                }
                else
                {
                  echo " AM";
                }
              }
              else 
              {
                echo "TBD";
              }
              
              echo "</td>";
              echo "<td>" . $game['opponent'] . "</td>";
              echo "<td>" . $game['type'] . "</td>";
              echo "<td>" . $game['field'] . "</td>";
              echo "<td><a href=\"gameinfo.php?id=" . $game['id'] . "\">See More Details</a></td>";
              if($isLoggedInGames && $_SESSION['priv'] == 1){echo "<td><a href=\"schedule.php?id=" . $game['id'] . "\">Edit Game Info</a></td>";}
              if($isLoggedInGames && $_SESSION['priv'] == 1){echo "<td><a href=\"removegamesubmit.php?id=" . $game['id'] . "\">Remove</a></td>";}
            echo "</tr>";
          }

          echo "</table>";
          if($upResults > 15)
          {
            echo "<div class=\"changePage\">";
              if($pageUp > 1)
              {
                echo "<p class=\"prev\"><a href=\"games.php?pageUp=" . ($pageUp-1) . "&pageRes=". $pageRes ."\">Prev</a></p>";
              }
              
              if((15 * $pageUp) < $upResults)
              {
                echo "<p><a href=\"games.php?pageUp=" . ($pageUp+1) . "&pageRes=". $pageRes ."\">Next</a></p>";
              }
            echo "</div>";
          }
      }
      else 
      {
        echo "<h1>There are no upcoming games</h1>";
      }
      echo "</div>";

      echo "<div class=\"results\">";
      if(!empty($games))
      {
          echo "<table>";
            echo "<tr>";
              echo "<th>Date</th>";
              echo "<th>Opponent</th>";
              echo "<th>Type</th>";
              echo "<th>Field</th>";
              echo "<th>Score</th>";
              echo "<th>Result</th>";
              echo "<th>More Info</th>";
              if($isLoggedInGames && $_SESSION['priv'] == 1){echo "<th>Edit Link</th>";}
              if($isLoggedInGames && $_SESSION['priv'] == 1){echo "<th>Delete Link</th>";}
            echo "</tr>";

          foreach($games as $game)
          {
            echo "<tr>";
              list($year, $month, $day, $hr, $min, $sec) = explode('[-: ]', $game['scheduledate']);
            
              $isPM = false;

              if($hr >= 12)
              {
                $isPM = true;
                
                if($hr != 12)
                {
                  $hr -= 12;
                }
              }

              echo "<tr>";
              echo "<td>" . $month . "/" . $day . "/" . $year . "&nbsp;&nbsp;&nbsp;&nbsp;"; 
              
              if($hr != 0)
              {
                echo $hr . ":" . $min;
                if($isPM)
                {
                  echo " PM";
                }
                else
                {
                  echo " AM";
                }
              }
              else 
              {
                echo "TBD";
              }
              
              echo "</td>";
              echo "<td>" . $game['opponent'] . "</td>";
              echo "<td>" . $game['type'] . "</td>";
              echo "<td>" . $game['field'] . "</td>";
              echo "<td>" . $game['score'] . "</td>";
              echo "<td>" . $game['result'] . "</td>";
              echo "<td><a href=\"gameresults.php?id=" . $game['id'] . "\">See Results</a></td>";
              if($isLoggedInGames && $_SESSION['priv'] == 1){echo "<td><a href=\"editgameresults.php?id=" . $game['id'] . "\">Edit Results</a></td>";}
              if($isLoggedInGames && $_SESSION['priv'] == 1){echo "<td><a href=\"removegamesubmit.php?id=" . $game['id'] . "\">Remove</a></td>";}
            echo "</tr>";
          }

          echo "</table>";
          if($gameResults > 15)
          {
            echo "<div class=\"changePage\">";
              if($pageRes > 1)
              {
                echo "<p class=\"prev\"><a href=\"games.php?pageRes=" . ($pageRes-1) . "&tab=results&pageUp=". $pageUp ."\">Prev</a></p>";
              }
              
              if((15 * $pageRes) < $gameResults)
              {
                echo "<p><a href=\"games.php?pageRes=" . ($pageRes+1) . "&tab=results&pageUp=". $pageUp ."\">Next</a></p>";
              }
            echo "</div>";
          }
      }
      else 
      {
        echo "<h1>There are no completed games</h1>";
      }
      echo "</div>";

        echo "</div>";
        echo "</div>";
    ?>
	<script type="text/javascript">
	  $$('#games .tabs a').addEvent('click', function(evt) {
	    $('games').setProperty('class', new Event(evt).target.getProperty('class'));
	  });
	</script>
  </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>
