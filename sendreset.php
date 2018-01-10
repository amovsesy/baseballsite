<?php
// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Reset Password &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/forms.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/resetPass.css" type="text/css" media="screen" />
    <script type="text/javascript" src="js/core.js"></script> 
	<script type="text/javascript" src="js/more.js"></script>
	<script type="text/javascript" src="js/en.js"> </script>
	<script type="text/javascript" src="js/formcheck.js"> </script>
	<link rel="stylesheet" href="css/formcheck.css" type="text/css" media="screen" />
	<script type="text/javascript">
      window.addEvent('domready', function(){
        new FormCheck('sendreset');
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
    
    <div id="info">An email will be sent. Follow the link to reset your password.</div>
    <div id="login">
      <form method="POST" action="sendresetsubmit.php" class="sendreset standard-form" id="sendreset">
        <ul>
          <li><label>Email:</label> <input type="text" name="email" class="validate['required','email'] text-input"></li>
          <li><input type="submit" value="Reset" class="btn-action"></li>
        </ul>
      </form>
    </div>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>