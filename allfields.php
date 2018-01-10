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

require_once('field.php');

getFields();

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Add Field &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/allfields.css" type="text/css" media="screen" />
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
        if($isLoggedIn)
        {
          echo "<div id=\"actionscontainer\">";
            echo "<div id=\"actions\">";
              if($_SESSION['priv'] == 1){echo "<form method=\"GET\" action=\"addfield.php\"><input class=\"btn-action\" type=\"submit\" value=\"Add a field\"></form>";}
            echo "</div>"; 
          echo"</div>";
        } 
      ?>
      <div id="fields">
      <table>
        <tr>
          <th>Name</th>
          <th>Address</th>
          <?php if($_SESSION['priv'] == 1){echo "<th>Edit Info</th>";} ?>
          <?php if($_SESSION['priv'] == 1){echo "<th>Remove Link</th>";} ?>
        </tr>
        <?php
          foreach($fields as $field)
          {
            echo "<tr>";
              echo "<td>" . $field['name'] . "</td>";
              echo "<td>" . $field['address'] . "</td>";
              if($_SESSION['priv'] == 1){echo "<td><a href=\"addfield.php?id=" . $field['id'] . "\">Edit</a></td>";}
              if($_SESSION['priv'] == 1){echo "<td><a href=\"removefieldsubmit.php?id=" . $field['id'] . "\">Remove</a></td>";}
            echo "</tr>";
          } 
        ?>
      </table>
      </div>
    </div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>