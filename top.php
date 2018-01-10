<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

$isLoggedInTop = false;

if (isset($_SESSION['playerid']))
{
  $isLoggedInTop = true;
}

?>

<div id="top">
  <a href="index.php"><img src="images/maddogslogo.png" /></a>
  <p><?php if($isLoggedInTop){echo "Welcome " . $_SESSION['firstname'] . " " . $_SESSION['lastname'];} ?></p>
  <p><?php if($isLoggedInTop){echo "<a href=\"postupdate.php\">Post an update</a>  &#124; ";} if ($isLoggedInTop){ echo "<a href=\"logout.php\">Log out</a>";} else {echo "<a href=\"login.php\">Sign in</a>";} ?></p>
</div>