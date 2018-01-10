<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

$isLoggedInIndex = false;

if (isset($_SESSION['playerid'])) 
{
  $isLoggedInIndex = true;
}

require_once('news.php');
require_once('game.php');
require_once('player.php');

function rteSafe($strText) 
{
  //returns safe code for preloading in the RTE
  $tmpString = $strText;

  //convert all types of single quotes
  $tmpString = str_replace(chr(145), chr(39), $tmpString);
  $tmpString = str_replace(chr(146), chr(39), $tmpString);
  $tmpString = str_replace("'", "&#39;", $tmpString);

  //convert all types of double quotes
  $tmpString = str_replace(chr(147), chr(34), $tmpString);
  $tmpString = str_replace(chr(148), chr(34), $tmpString);
  //	$tmpString = str_replace("\"", "\"", $tmpString);

  //replace carriage returns & line feeds
  $tmpString = str_replace(chr(10), " ", $tmpString);
  $tmpString = str_replace(chr(13), " ", $tmpString);

  return $tmpString;
}

getNews();
getRandomPic();

?>

<!DOCTYPE html>
<html>

  <head>
    <title>MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/index.css" type="text/css" media="screen" />
    <script type="text/javascript" src="js/core.js"></script>
  </head>

  <body>
    <?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
    <div id="pic"><img src="<?php echo $gameImages[1]['img']; ?>" /></div>
    <?php 
      if(isset($_SESSION['error']))
      {
        echo "<div id=\"error\"><p>" . $_SESSION['error'] . "</p></div>";
        unset($_SESSION['error']);
      }
      	
    ?>
    <?php
      if($isLoggedInIndex && !empty($news))
      {
        $isFirst = true;
        getAllPlayers();
        echo "<div id=\"news\">";
        echo "<ul class=\"postlist\">";
          foreach($news as $post)
          { 
            echo "<li class=\"post";
            if(!$isFirst){echo " nonfirst";}
            $isFirst = false;  
            echo "\">";
            echo "<span class=\"playernews\">";
              if(!empty($players[$post['playerid']]['img']))
  		      {
  		        echo "<img src=\"" . $players[$post['playerid']]['img'] . "\" />";
  		      }
  		      else
  		      {
  		        echo "<img src=\"images/nophoto.jpg\" />";
  		      }
  		      echo "<p>" . $players[$post['playerid']]['firstname'] . " " . $players[$post['playerid']]['lastname'] . "</p>";
  		      list($year, $month, $day, $hr, $min, $sec) = explode('[-: ]', $post['postdate']);
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
              
              echo "<p>" . $month . "/" . $day . "/" . $year . "<br />" . $hr . ":" . $min;
              if($isPM)
              {
                echo " PM";
              }
              else
              {
                echo " AM";
              }
              echo "</p>";
            echo "</span>";
            echo "<span class=\"newspost\">";
              echo rteSafe($post['message']);
            echo "</span>";
          }
        echo "</ul>";
        echo "</div>";
      }
      else
      {
        echo "<div id=\"about\">";
          echo "<h1>About the Mad Dogs</h1>";
          echo "<p>The Maddogs are at it again, still searching for that elusive title.  The 2012 Maddogs look stronger than ever and are hoping to change their unfortunate playoff luck of years past.  Despite going 63-34-3 (.630) over the last 5 years, the Maddogs have consistently run into dominating pitching in the playoffs and fallen short.  The 2012 version has added both depth and firepower and hopes to make a deep run this year.  Come out and catch a game during what promises to be a very exciting 2012 season.  Look below or click on the games tab to see our upcoming games.</p>";
        echo "</div>";
      }

      getGames(5, 1);

        echo "<div id=\"games\" class=\"upcoming\">";
        echo "<ul class=\"tabs\">";
          echo "<li class=\"up\"><a class=\"upcoming\">Upcoming</a></li>";
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
            echo "</tr>";
          }

          echo "</table>";
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
            echo "</tr>";

          foreach($games as $game)
          {
            echo "<tr>";
              list($year, $month, $day, $hr, $min, $sec) = explode('[-: ]', $game['scheduledate']);
            
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
              echo "<td>" . $game['result'] . "</td>";
              echo "<td>" . $game['score'] . "</td>";
              echo "<td><a href=\"gameresults.php?id=" . $game['id'] . "\">See Results</a></td>";
            echo "</tr>";
          }

          echo "</table>";
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
