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

$id = $_GET['id'];
$street = "";
$city = "";
if(!empty($id))
{
  getField($id);
  list($street, $city, $state) = explode(', ', $fields[$id]['address']);
}

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Add Field &#124; MadDogs</title>
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
        new FormCheck('field');
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
    
    <div id="addfield">
      <form method="POST" action="addfieldsubmit.php" class="field standard-form" id="field">
        <input type="hidden" name="fieldid" value="<?php echo $id ?>" />
        <ul>
          <li><label>* Name:</label> <input type="text" name="name" class="validate['required','length[50]','alphanum'] text-input" value="<?php echo $fields[$id]['name']; ?>"></li>
          <li><label>* Street:</label> <input type="text" name="street" class="validate['required','length[50]','alphanum'] text-input" value="<?php echo $street; ?>"></li>
          <li><label>* City:</label> <input type="text" name="city" class="validate['required','length[30]','alphanum'] text-input" value="<?php echo $city; ?>"></li>
          <li><input type="submit" value="Save" class="btn-action"> or <a href="games.php">Cancel</a></li>
        </ul>
      </form>
    </div>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>
