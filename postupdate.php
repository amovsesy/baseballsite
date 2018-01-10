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

/*
 * 
 * needed tags for rich text
    <script language="JavaScript" type="text/javascript" src="cbrte/html2xhtml.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="cbrte/richtext_compressed.js"></script>
 * 
 * script for rich text
 * 
 * <!-- script language="JavaScript" type="text/javascript">
	      <!--
	        function submitForm() {
	          //make sure hidden and iframe values are in sync for all rtes before submitting form
	          updateRTEs();
	
	          return true;
            }

            //Usage: initRTE(imagesPath, includesPath, cssFile, genXHTML, encHTML)
           initRTE("cbrte/images/", "cbrte/", "", true);
           //>
         </script>
         <script language="JavaScript" type="text/javascript">
	       <!--
	         //build new richTextEditor
		     var rte1 = new richTextEditor('message');
		     rte1.html = '';
		     //rte1.toggleSrc = false;
		     rte1.build();
	       //>
	    </script-->
 */

?>

<!DOCTYPE html>
<html>

  <head>
    <title>Post an update &#124; MadDogs</title>
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/forms.css" type="text/css" media="screen" />
  </head>

  <body>
  	<?php include 'top.php'; ?>
    <?php include 'menu.php'; ?>
    <div id="contents">
    
    <!-- form method="POST" action="postupdatesubmit.php" onsubmit="return submitForm();"-->
    <form method="POST" action="postupdatesubmit.php" class="post standard-form" id="post">
  	  <div id="postupdate">
  	    <ul>
  	    <li><label>Message:</label><textarea rows="5" cols="25" name="message"></textarea></li>
  	    <li><input type="submit" value="Post" class="btn-action"> or <a href="index.php">Cancel</a></li>
  	    </ul>
  	  </div>
  	</form>
  	</div>
    <script language="javascript" type="text/javascript" src="js/analytics.js"></script>
  </body>
</html>