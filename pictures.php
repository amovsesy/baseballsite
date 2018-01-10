<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('game.php');

$actualYear = date("Y");
$curYear = isset($_GET['year'])?$_GET['year']:date("Y");

getGameYears();
getGamesWithPictures($curYear);

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Pictures &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/pictures.css" type="text/css" media="screen" />
  </head>
  <body>
    <?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
      <?php
        if(isset($_SESSION['error']))
        {
          echo "<div id=\"error\"><p>" . $_SESSION['error'] . "</p></div>";
          unset($_SESSION['error']);
        }
      ?>
      <div id="actions">
        <form method="GET" action="pictures.php">
          <label>Season: </label>
          <select name="year">
            <?php
              if(!array_key_exists($actualYear, $gameYears))
              {
                echo "<option" . ($curYear==$actualYear?" selected=\"selected\"":"") . " value=\"" . $actualYear . "\">" . $actualYear . "</option>";
              } 
                
              foreach($gameYears as $gameYear)
              {
                echo "<option" . ($curYear==$gameYear['year']?" selected=\"selected\"":"") . " value=\"" . $gameYear['year'] . "\">" . $gameYear['year'] . "</option>";
              } 
            ?>
          </select>
          <input class="btn-action" type="submit" value="Go">
        </form>
      </div>
      <table>
        <?php
          if(!empty($gameids))
          {
  		    $i=0;
  		    foreach($gameids as $gameid)
  		    {
  		      getFirstPicOfGame($gameid['id']);
  		      getGame($gameid['id']);
  		      list($year, $month, $day, $hr, $min, $sec) = explode('[-: ]', $games[$gameid['id']]['scheduledate']);
  		      if($i == 0)
  		      {
  		        echo "<tr>";
  		      }
  		    
  		      echo "<td>";
  		        echo "<a href=\"gamepictures.php?id=" . $gameid['id'] ."\"><img src=\"" . $gameImage . "\" /></a>";
  		        echo "<a href=\"gamepictures.php?id=" . $gameid['id'] ."\">Mad Dogs v.s " . $games[$gameid['id']]['opponent']  . "&nbsp;&nbsp;&nbsp;&nbsp;" . $month . "/" . $day . "/" . $year ."</a>";
  		      echo "</td>";
  		     
              if($i == 3)
  		      {
  		        echo "</tr>";
  		        $i = -1;
  		      }
  		    
  		      $i++;
  		    }
  		  
  		    while($i < 4 && $i != 0)
  		    {
  		      echo "<td></td>";
  		      $i++;
  		    }
  		  
  		    if($i != 0)
  		    {
  		      echo "</tr>";
  		    }
  		  }
  		  else
  		  {
  		    echo "<td class=\"nopics\">There are no games with pictures the " . $curYear . " season</td>";
  		  }
  		?>
      </table>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>
