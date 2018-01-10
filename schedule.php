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

if($_SESSION['priv'] != 1)
{
  header('Location: games.php');
}

require_once('field.php');
require_once('game.php');

$id = "";
$isEdit = false;
$date = "";
$timeHr = "";
$timeMin = "";
$isPM = false;
if(isset($_GET['id']))
{
  $id = $_GET['id'];
  $isEdit = true;
  getGame($id);
  
  list($year, $month, $day, $hr, $min, $sec) = explode('[-: ]', $games[$id]['scheduledate']);
  if($hr >= 12)
  {
    $isPM = true;
    
    if($hr != 12)
    {
      $hr -= 12;
    }
  }
  
  $date = $month . "/" . $day . "/" . $year;
  
  if($hr != 0)
  {
    $timeHr = $hr;
    $timeMin = $min;
  }
}

getFields();

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Schedule Game &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/forms.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/formcheck.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/datepicker.css" type="text/css" media="screen" />
    <script type="text/javascript" src="js/core.js"></script> 
	<script type="text/javascript" src="js/more.js"></script>
	<script type="text/javascript" src="js/Locale.en-US.DatePicker.js"></script>
	<script type="text/javascript" src="js/Picker.js"></script>
	<script type="text/javascript" src="js/Picker.Attach.js"></script>
	<script type="text/javascript" src="js/Picker.Date.js"></script>
	<script type="text/javascript" src="js/en.js"></script>
	<script type="text/javascript" src="js/formcheck.js"></script>
	<script type="text/javascript">
      window.addEvent('domready', function(){
        new FormCheck('schedule');
      });
	</script>
	<script>
	  window.addEvent('domready', function(){

	    myPicker = new Picker.Date($$('input.date'), {
		  toggle: $$('date'),
		  positionOffset: {x: -10, y: 20}
		});

		$$('date').addEvent('click', function(e){
			e.stop();
			myPicker.toggle();
		});

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
    
    <div id="schedulegame">
      <form method="POST" action="schedulesubmit.php" class="schedule standard-form" id="schedule">
        <input type="hidden" name="isEdit" value="<?php echo $isEdit ?>" />
        <input type="hidden" name="gameid" value="<?php echo $id ?>" />
        <ul>
          <li><label>* Date:</label> <input type="text" name="date" size="8" class="validate['required','length[25]'] text-input date" value="<?php echo $date; ?>"> </li>
          <li><label>Time:</label> <input type="text" name="hr" size="2" class="validate['digit[1,12]'] text-input" value="<?php echo $timeHr; ?>"> : <input type="text" name="min" size="2" class="validate['digit[0,59]'] text-input" value="<?php echo $timeMin; ?>"> <select name="ampm"><option>AM</option><option <?php if(!$isEdit || $isPM || $hr == 0){echo "selected=\"selected\"";} ?>>PM</option></select></li>
          <li><label>* Field:</label> 
          	<select name="field" class="validate['required'] text-input">
          	  <option>Please select a value</option>
          	  <option value="tbd" <?php if($isEdit && empty($games[$id]['fieldid'])){echo "selected=\"selected\"";} ?>>TBD</option>
              <?php
                foreach($fields as $field)
                {
                  echo "<option value=\"" . $field['id'] . "\" " . ($isEdit && $games[$id]['fieldid'] == $field['id'] ? "selected=\"selected\"" : "") . ">" . $field['name'] . "</option>";
                } 
              ?>
            </select> Fields must be added on games tab in order to schedule a game
          </li>
          <li><label>* Opponent:</label><input type="text" name="opponent" class="validate['required','length[20]','nodigit'] text-input" value="<?php echo $games[$id]['opponent']; ?>"></li>
          <li><label>* Type:</label>
            <select name="type">
              <option value="season">Season</option>
              <option value="postseason" <?php if($isEdit && $games[$id]['type'] == "postseason"){echo "selected=\"selected\"";} ?>>Post Season</option>
              <option value="preseason" <?php if($isEdit && $games[$id]['type'] == "preseason"){echo "selected=\"selected\"";} ?>>Pre Sesaon</option>
            </select>
          </li>
          <li><input type="submit" value="Save" class="btn-action"> or <a href="games.php">Cancel</a></li>
        </ul>
      </form>
    </div>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>
