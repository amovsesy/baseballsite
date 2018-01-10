<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('player.php');

getAllPlayers();

$hash = $_GET['ohno'];
$_SESSION['playerID'] = -1;

foreach($players as $player)
{
  if(md5($player['email']) == $hash)
  {
    $_SESSION['playerID'] = $player['id'];
    $_SESSION['email'] = $player['email'];
    break;
  }
}

if($playerID == -1)
{
  header('Location: index.php');
}

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Reset Password &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/forms.css" type="text/css" media="screen" />
    <script type="text/javascript" src="js/core.js"></script> 
	<script type="text/javascript" src="js/more.js"></script>
	<script type="text/javascript" src="js/en.js"> </script>
	<script type="text/javascript" src="js/formcheck.js"> </script>
	<link rel="stylesheet" href="css/formcheck.css" type="text/css" media="screen" />
	<script type="text/javascript">
      window.addEvent('domready', function(){
        new FormCheck('resetform');
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
    
    <div id="reset">
      <form method="POST" action="resetsubmit.php" class="resetform standard-form" id="resetform">
        <ul>
          <li><label>* New Password:</label> <input type="password" name="newPassword" id="newPassword" class="validate['required','length[5,-1]','alphanum'] text-input"></li>
          <li><label>* Confirm Password:</label> <input type="password" name="passwordConfirm" class="text-input validate['confirm:newPassword']"></li>
          <li><input type="submit" value="Change" class="btn-action"></li>
        </ul>
      </form>
    </div>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>