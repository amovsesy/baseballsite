<?php
// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

$isLoggedInMenu = false;

if (isset($_SESSION['playerid']))
{
  $isLoggedInMenu = true;
}

?>

<div id="menu">
  <ul>
    <li><a href="index.php">Home</a></li>
    <?php if($isLoggedInMenu){echo "<li><a href=\"profile.php\">Profile</a></li>";} ?>
    <li><a href="pictures.php">Pictures</a></li>
    <li><a href="games.php">Games</a></li>
    <?php 
      if($isLoggedInMenu)
      {
        echo "<li><a href=\"players.php\">Players</a></li>";
        echo "<li><a href=\"teamstats.php\">Stats</a></li>";
      }
    ?>
    <li <?php if(!$isLoggedInMenu){echo "class=\"last\"";} ?>><a href="<?php if(!$isLoggedInMenu){echo "email.php";}else{echo "contact.php";} ?>">Contact <?php if(!$isLoggedInMenu){echo "Us";} ?></a></li>
  </ul>
</div>