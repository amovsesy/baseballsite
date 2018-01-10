<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

// Delete certain session
unset($_SESSION['playerid']);
unset($_SESSION['firstname']);
unset($_SESSION['lastname']);
// Delete all session variables
// session_destroy();

// Jump to index page
header('Location: index.php');

?>