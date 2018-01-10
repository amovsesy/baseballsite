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

//TODO: change so that they see players by year
getAllPlayers();

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Players &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/players.css" type="text/css" media="screen" />
  </head>

  <body>
  	<?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
  	<div id="players">
  	  <table id="playersTable">
  		<?php
  		  $i=0;
  		  foreach($players as $player)
  		  {
  		    if($i == 0)
  		    {
  		      echo "<tr>";
  		    }
  		    
  		    echo "<td>";
  		    if(!empty($player['img']))
  		    {
  		      echo "<img src=\"" . $player['img'] . "\" />";
  		    }
  		    else
  		    {
  		      echo "<img src=\"images/nophoto.jpg\" />";
  		    }
  		   
  		    echo "<p>";
  		    if($player['id'] != $_SESSION['playerid']){echo "<a href=\"profile.php?id=" . $player['id'] . "\">";}
  		      echo $player['firstname'] . " " . $player['lastname'];
  		      if($player['id'] != $_SESSION['playerid']){echo "</a>";}
  		    echo "</p>";
  		    
  		    if(!empty($player['positions']))
            {
              $positions = explode(':', $player['positions']);
              $result = "";
              foreach($positions as $position)
              {
                if($position != "")
                {
                  $result .= $position . ", ";
                } 
              }
              echo "<p>" . substr($result, 0, strlen($result)-2) . "</p>";
            }
            echo "</td>";
             
            if($i == 1)
  		    {
  		      echo "</tr>";
  		      $i = -1;
  		    }
  		    
  		    $i++;
  		  }
  		  
  		  if($i == 1)
  		  {
  		    echo "<td></td>";
  		    echo "</tr>";
  		  }
  		?>
  	  </table>
  	</div>
  	</div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>
