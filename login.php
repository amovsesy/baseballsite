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
    <title>Sign In &#124; MadDogs</title>
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
        new FormCheck('login');
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
    
    <div id="login">
      <form method="POST" action="loginsubmit.php" class="login standard-form" id="login">
        <ul>
          <li><label for="email">Email:</label> <input type="text" id="email" size="30" name="email" class="validate['required','email'] text-input"></li>
          <li><label for="password">Password:</label> <input type="password"  size="30" id="password" name="password" class="validate['required','length[5,-1]','alphanum'] text-input"></li>
          <li><input type="submit" value="Login" class="btn-action"> or <a href="sendreset.php">Reset your password</a></li>
        </ul>
      </form>
    </div>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>