<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

$id = $_GET['id'];

require_once('game.php');

getGame($id);

list($year, $month, $day, $hr, $min, $sec) = explode('[-: ]', $games[$id]['scheduledate']);
            
$isPM = false;

if($hr >= 12)
{
  $isPM = true;

  if($hr != 12)
  {
    $hr -= 12;
  }
}

//ABQIAAAADoIzOCKXtlt4rgH5D_GdWxSqd1JVstbSLw4jekMIvGUCE365sBSIphvgcGD5Ab-1Xs3u3himN7WX7A
//api key for sfmaddogs.com

?>

<!DOCTYPE html>
<html>

  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/forms.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/info.css" type="text/css" media="screen" />
    <script src="http://maps.google.com/maps/api/js?sensor=true"
            type="text/javascript"></script>
    <script language="javascript" type="text/javascript" src="js/map.js"></script>
    <title>Game Info &#124; MadDogs</title>
  </head>

  <?php echo "<body onload=\"initialize('" . $games[$id]['address'] . "')\" onunload=\"GUnload()\">" ?>
    <?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
    <div id="teams">Mad Dogs v.s <?php echo $games[$id]['opponent']; ?></div>
    <div id="topinfo">
    <div id="info">
      <ul>
        <li><label>Date:</label> <?php echo $month . "/" . $day . "/" . $year; ?></li>
        <li><label>Time:</label> <?php if($hr != 0){echo $hr . ":" . $min; if($isPM){echo " PM";}else{echo " AM";}}else{echo "TBD";} ?></li>
        <li><label>Field name:</label> <?php echo $games[$id]['field']; ?></li>
        <li><label>Field address:</label> <?php echo $games[$id]['address']; ?></li>
      </ul>
	</div>
	<div id="directions">
	  <p>Get Directions: </p>
	  <?php echo "<form action=\"#\" class=\"standard-form sided\" onsubmit=\"calcRoute(this.street.value, this.city.value, '" . $games[$id]['address'] . "');return false;\">"; ?>
	    <?php echo "<input type=\"hidden\" name=\"id\" id=\"id\" value=\"" . $id . "\" />"; ?>
	    <ul>
          <li><label>Street:</label> <input type="text" name="street"></li>
          <li><label>City:</label> <input type="text" name="city"></li>
          <li><input type="submit" value="Go"  class="btn-action"></li>
        </ul>
      <?php echo "</form>"; ?>
	</div>
	</div>
	<div id="map"></div>
	<div id="directionsPanel"></div> 
	</div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>
