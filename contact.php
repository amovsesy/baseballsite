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

getPlayers(date("Y"));

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Contact &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/contact.css" type="text/css" media="screen" />
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
    
  	<div id="contact">
  	  <div id="emailcontainer">
  	  <div id="email">
  	    <form method="GET" action="email.php">
  	      <input class="btn-action" type="submit" value="Send a message">
  	    </form>
  	  </div>
  	  </div>
  	  <div id="contacttable">
  	  <table>
  	  	<tr>
  	  	  <th>Name</th>
  	  	  <th>Phone</th>
  	  	  <th>Email</th>
  	  	</tr>
  	  	<?php
  		  foreach($players as $player)
  		  {
  		    echo "<tr>";
  		      echo "<td>" . $player['firstname'] . " " . $player['lastname'] . "</td>";
  		      echo "<td>" . $player['phone'] . "</td>";
  		      echo "<td>" . $player['email'] . "</td>";
  		    echo "</tr>";
  		  }
  	    ?>
  	  </table>
  	  </div>
  	</div>
  	</div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>