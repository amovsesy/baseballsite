<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

$isLoggedInEmail = false;

if (isset($_SESSION['playerid'])) 
{
  $isLoggedInEmail = true;
}

require_once('player.php');

$id = $_SESSION['playerid'];

getPlayers(date("Y"));

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Players &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/forms.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/email.css" type="text/css" media="screen" />
    <script type="text/javascript" src="js/core.js"></script> 
	<script type="text/javascript" src="js/more.js"></script>
	<script type="text/javascript" src="js/en.js"> </script>
	<script type="text/javascript" src="js/formcheck.js"> </script>
	<link rel="stylesheet" href="css/formcheck.css" type="text/css" media="screen" />
	<script type="text/javascript">
      window.addEvent('domready', function(){
        new FormCheck('email');
      });
	</script>
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
    <form method="POST" action="emailsubmit.php" class="email standard-form" id="email">
  	  <div id="email">
  	    <ul>
  	      <?php if(!$isLoggedInEmail){echo "<label>Name: </label><input type=\"text\" id=\"name\" name=\"name\" class=\"validate['required','length[3,60]','nodigit'] text-input\"/>";} ?>
  	      <li><label>From: </label> <?php if($isLoggedInEmail){echo $players[$id]['firstname'] . " " . $players[$id]['lastname'];}else{echo "<input type=\"text\" id=\"email\" name=\"email\" class=\"validate['required','email'] text-input\"/>";} ?></li>
  	      <li><label>To: </label>
  	        <select name="toPerson">
  	    	  <?php
  	    	    if($isLoggedInEmail)
  	    	    {
  	    	      echo "<option value=\"all\">All Members</option>";
  	    	      foreach($players as $player)
  	    	      {
  	    	        if($player['id'] != $id)
  	    	        {
  	    	          echo "<option value=\"" . $player['id'] . "\">" . $player['firstname'] . " " . $player['lastname'] . "</option>";
  	    	        }
  	    	      }
  	    	    }
  	    	    else 
  	    	    {
  	    	      echo "<option value=\"team\">SF Mad Dogs</option>";
  	    	    }
  	    	  ?>
	        </select>
  	      </li>
  	      <li><label>Subject: </label> <input type="text" name="subject" size="30" class="validate['length[50]','alphanum'] text-input"></li>
  	      <li><label>Message:</label><textarea name="message" rows="7" cols="60"></textarea></li>
  	      <li><input type="submit" value="Send" class="btn-action"> or <a href="contact.php">Cancel</a></li>
  	    </ul>
  	  </div>
  	</form>
  	<?php 
  	  if(!$isLoggedInEmail)
  	  {
  	    echo "<div id=\"notice\"><p><span class=\"note\">*Note:</span> Sending a message will send a message to the manager of the Mad Dogs.  If the message goes through you will be redirected to the home page with no message. If there was an error sending your message an error will appear at the top of the page.</p>";
  	  }
  	?>
  	</div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>