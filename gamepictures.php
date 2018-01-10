<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

$isLoggedInGamePictures = false;

if (isset($_SESSION['playerid'])) 
{
  $isLoggedInGamePictures = true;
}

require_once('game.php');

$gameid = $_GET['id'];
getPicsOfGame($gameid);

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Game Pictures &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/gamepics.css" type="text/css" media="screen" />
	<link rel="stylesheet" type="text/css" href="css/Lightbox/style.css" />
	<script type="text/javascript" src="js/core.js"></script>
	<script type="text/javascript" src="js/more.js"></script>
	<script type="text/javascript" src="js/XtLightbox.js"></script>
	<script type="text/javascript" src="js/Adaptor.js"></script>
	<script type="text/javascript" src="js/Renderer.js"></script>
	<script type="text/javascript" src="js/Adaptor/Image.js"></script>
	<script type="text/javascript" src="js/Adaptor/YouTube.js"></script>
	<script type="text/javascript" src="js/Renderer/Lightbox.js"></script>
	<script type="text/javascript">
		window.addEvent('domready', function(){
			new XtLightbox('.gallery a', {
				loop: true,
				adaptorOptions: {
					Image: {
						lightboxCompat: false
					}
				}
			});
		});
	</script>
  </head>
  <body>
    <?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
      <?php 
        if($isLoggedInGamePictures)
        {
          echo "<div id=\"actions\">";            
            echo "<form method=\"GET\" action=\"gameimageupload.php\"><input type=\"hidden\" name=\"id\" value=\"" . $gameid . "\" /><input type=\"hidden\" name=\"goback\" value=\"gamepics\" /><input class=\"btn-action\" type=\"submit\" value=\"Upload Images\"></form>";
          echo "</div>";
        } 
      ?>
      <div class="gallery" id="pics">
        <?php
          foreach($gameImages as $pic)
          {
            echo "<a href=\"" . $pic['img'] ."\"><img src=\"" . $pic['img'] ."\" /></a>";
          }
        ?>
      </div>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>