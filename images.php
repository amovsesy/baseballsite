<?php 
// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('player.php');

if (!isset($_SESSION['playerid'])) 
{
  header('Location: index.php');
}

$id = $_SESSION['playerid'];

getSinglePlayer($id);

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Image Upload &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/forms.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/images.css" type="text/css" media="screen" />
  </head>

  <body>
    <?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
    <?php 
      /*
       *
       * This should be an error div that gets set by the js.
       * Each field should have an error string underneath each element
       * in order to surface the errors that matter.
       */
      if(isset($_SESSION['error']))
      {
        echo "<div id=\"error\"><p>" . $_SESSION['error'] . "</p></div>";
        unset($_SESSION['error']);
      }
      	
    ?>
    
    <div id="imageupload">
    	<img src="<?php if(!empty($players[$id]['img'])){echo $players[$id]['img'];}else{echo "images/nophoto.jpg";} ?>" />
    	<form method="POST" enctype="multipart/form-data" action="imagessubmit.php" class="standard-form">
    	<ul>
    		<li><input type="file" name="image"> <input name="Submit" type="submit" value="Upload image" class="btn-action"></li>
    	</ul>
    	</form>
    	<form method="POST" action="imagesremovesubmit.php" class="standard-form">
    		<input name="remove" type="submit" value="Remove Image" class="btn-action">
    	</form>
    </div>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>