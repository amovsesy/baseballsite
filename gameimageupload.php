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

$id = $_GET['id'];
$goback = $_GET['goback'];

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Upload Images &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/gameimageupload.css" type="text/css" media="screen" />
    <script language="javascript" type="text/javascript" src="js/addrow.js"></script>
  </head>
  <body>
    <?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
      <form method="POST" enctype="multipart/form-data"  action="gameimageuploadsubmit.php" class="standard-form">
        <input type="hidden" name="gameid" value="<?php echo $id ?>" />
        <input type="hidden" name="goback" value="<?php echo $goback ?>" />
        <input type="hidden" name="numimages" id="numimages" value="1" />
        <div id="actionscontainer">
          <div id="actions">
            <input type="button" class="btn-action" value="Add image to upload" onclick="addRowToTable('images');" />
	        <input type="button" class="btn-action" value="Remove image to upload" onclick="removeRowFromTable('images');" />
            <input type="submit" class="btn-action" value="Upload images">
          </div>
        </div>
        <div id="imageupload">
          <p>Max image size is 300 kb per image (note the bigger the images the longer it will take to upload to the server)</p>
          <table id="images">
            <tr>
              <td><input type="file" name="image1"></td>
            </tr>
          </table>
        </div>
      </form>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>