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

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Change Password &#124; MadDogs</title>
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
        new FormCheck('changepassform');
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
    
    <div id="changepass">
      <form method="POST" action="changepasssubmit.php" class="changepassform  standard-form" id="changepassform">
        <ul>
          <li><label>* Current Password:</label> <input type="password" name="password" size="30" class="validate['required','length[5,-1]','alphanum'] text-input"></li>
          <li><label>* New Password:</label> <input type="password" name="newPassword" id="newPassword" size="30" class="validate['required','length[5,-1]','alphanum'] text-input"></li>
          <li><label>* Confirm Password:</label> <input type="password" name="passwordConfirm" size="30" class="text-input validate['confirm:newPassword']"></li>
          <li><input type="submit" value="Change" class="btn-action"> or <a href="profile.php">Cancel</a></li>
        </ul>
      </form>
    </div>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>