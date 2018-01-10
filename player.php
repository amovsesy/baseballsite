<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('database.php');
require_once('constants.php');

$players = array();


$insertSQL = "insert into player (firstname, lastname, email, phone, height, weight, positions, bats, throws, nickname, birthdate, password, privilege) values (";
$insertIMG = "update player set img = '";
$selectSQL = "select * from player where LOCATE('";
$selectSQL2 = "', yearsonteam) > 0 order by firstname asc";
$selectSpecificSQL = "select * from player where id = ";
$findIfUserExists = "select * from player where email = '";

function doesUserExist($email)
{
  global $db, $findIfUserExists;

  $res = $db->query($findIfUserExists . $db->escape_value($email) . "'");
  return !($db->num_rows($res) == 0);
}

function addPlayer($fname, $lname, $email, $phone, $height, $weight, $pos, $bats, $throws, $nick, $birthdate, $password, $privilege)
{
  global $db, $insertSQL;

  $db->query($insertSQL . "'" . $db->escape_value($fname) . "', '" . $db->escape_value($lname) . "', '" . $db->escape_value($email) . "', '" . $db->escape_value($phone) . "', " . $db->escape_value($height) . ", " . $db->escape_value($weight) . ", '" . $db->escape_value($pos) . "', '" . $db->escape_value($bats) . "', '" . $db->escape_value($throws) . "', '" . $db->escape_value($nick) . "', '" . $db->escape_value($birthdate) . "', '" . $db->escape_value($password) . "', " . $db->escape_value($privilege) . ")");
}

function updatePlayer($fname, $lname, $email, $phone, $height, $weight, $pos, $bats, $throws, $nick, $birthdate, $id, $privilege)
{
  global $db;
  
  $updateSQL = "update player set privilege = " . $privilege . ", firstname = '" . $db->escape_value($fname) . "', lastname = '" . $db->escape_value($lname) . "', email = '" . $db->escape_value($email) . "', phone = '" . $db->escape_value($phone) . "', height = " . $db->escape_value($height) . ", weight = " . $db->escape_value($weight) . ", positions = '" . $db->escape_value($pos) . "', bats = '" . $db->escape_value($bats) . "', throws = '" . $db->escape_value($throws) . "', birthdate = '" . $db->escape_value($birthdate) . "', nickname = '" . $db->escape_value($nick) . "' where id = " . $id;

  $db->query($updateSQL);
}

function addImage($playerId, $img)
{
  global $db, $insertIMG;
  
  $db->query($insertIMG . $db->escape_value($img) . "' where id = " . $playerId);
}

function resetPassword($email)
{
  $to = $email;
  $headers = "From: noreply@sfmaddogs.com";
  $subject = "Password reset request";
  $message = "A password reset has been requested. If you did this please go to www.sfmaddogs.com/reset.php?ohno=" . md5($email);
  $mail_sent = @mail( $to, $subject, $message, $headers );

  if(!$mail_sent)
  {
    $_SESSION['error'] = "There was a problem sending your message to reset your password. Please try again";
    header('Location: sendreset.php');
  }
  
  header('Location: index.php');
}

function updateResetPass($id, $newPass)
{
  global $db;
  
  $login = $db->query("SELECT * FROM player WHERE id = " . $id);

  if($db->num_rows($login) != 1)
  {
    $_SESSION['error'] = "Could not find the user.";
    header('Location: index.php');
  }
  
  $result = $db->query("update player set password = '" . $newPass . "' where id = " . $id);
  
  if($db->num_rows($result) <= 0)
  {
    $_SESSION['error'] = "There was a problem updating your password";
    header('Location: index.php');
  }
}

function updatePassword($id, $oldPass, $newPass)
{
  global $db;
  
  $login = $db->query("SELECT * FROM player WHERE (id = " . $id . ") and (password = '" . $oldPass . "')");
  
  if($db->num_rows($login) != 1)
  {
    $_SESSION['error'] = "Your password did not match our records in the database";
    header('Location: changepass.php');
  }
  
  $result = $db->query("update player set password = '" . $newPass . "' where id = " . $id);
  
  if($db->num_rows($result) <= 0)
  {
    $_SESSION['error'] = "There was a problem updating your password";
    header('Location: changepass.php');
  }
  
  header('Location: profile.php');
}

//only for index, reset password, and players pages
function getAllPlayers()
{
  global $db, $selectSQL, $selectSQL2;

  unset($GLOBALS['players']);
  $GLOBALS['players'] = array();
  $res = $db->query("select * from player order by firstname asc");

  while($row = $db->getRows($res))
  {
    $GLOBALS['players'][$row['id']]['id'] = $row['id'];
    $GLOBALS['players'][$row['id']]['firstname'] = $row['firstname'];
    $GLOBALS['players'][$row['id']]['lastname'] = $row['lastname'];
    $GLOBALS['players'][$row['id']]['email'] = $row['email'];
    $GLOBALS['players'][$row['id']]['phone'] = $row['phone'];
    $inches = $row['height'] % 12;
    $GLOBALS['players'][$row['id']]['heightIN'] = $inches;
    $GLOBALS['players'][$row['id']]['heightFT'] = ($row['height'] - $inches) / 12;
    $GLOBALS['players'][$row['id']]['weight'] = $row['weight'];
    $GLOBALS['players'][$row['id']]['positions'] = $row['positions'];
    $GLOBALS['players'][$row['id']]['bats'] = $row['bats'];
    $GLOBALS['players'][$row['id']]['throws'] = $row['throws'];
    
    if(!empty($row['birthdate']) && $row['birthdate'] != "0000-00-00")
    {
      list($year, $month, $day) = explode('-', $row['birthdate']);
      $birthdate = $month . "/" . $day . "/" . $year;
      $GLOBALS['players'][$row['id']]['birthdate'] = $birthdate;
    }
    
    $GLOBALS['players'][$row['id']]['nickname'] = $row['nickname'];
    $GLOBALS['players'][$row['id']]['img'] = $row['img'];
  }
}

function getPlayers($year)
{
  global $db, $selectSQL, $selectSQL2;

  unset($GLOBALS['players']);
  $GLOBALS['players'] = array();
  $res = $db->query($selectSQL . $year . $selectSQL2);

  while($row = $db->getRows($res))
  {
    $GLOBALS['players'][$row['id']]['id'] = $row['id'];
    $GLOBALS['players'][$row['id']]['firstname'] = $row['firstname'];
    $GLOBALS['players'][$row['id']]['lastname'] = $row['lastname'];
    $GLOBALS['players'][$row['id']]['email'] = $row['email'];
    $GLOBALS['players'][$row['id']]['phone'] = $row['phone'];
    $inches = $row['height'] % 12;
    $GLOBALS['players'][$row['id']]['heightIN'] = $inches;
    $GLOBALS['players'][$row['id']]['heightFT'] = ($row['height'] - $inches) / 12;
    $GLOBALS['players'][$row['id']]['weight'] = $row['weight'];
    $GLOBALS['players'][$row['id']]['positions'] = $row['positions'];
    $GLOBALS['players'][$row['id']]['bats'] = $row['bats'];
    $GLOBALS['players'][$row['id']]['throws'] = $row['throws'];
    
    if(!empty($row['birthdate']) && $row['birthdate'] != "0000-00-00")
    {
      list($year, $month, $day) = explode('-', $row['birthdate']);
      $birthdate = $month . "/" . $day . "/" . $year;
      $GLOBALS['players'][$row['id']]['birthdate'] = $birthdate;
    }
    
    $GLOBALS['players'][$row['id']]['nickname'] = $row['nickname'];
    $GLOBALS['players'][$row['id']]['img'] = $row['img'];
  }
}

function getSinglePlayer($id)
{
  global $db, $selectSpecificSQL;

  unset($GLOBALS['players']);
  $GLOBALS['players'] = array();
  $res = $db->query($selectSpecificSQL . $id);

  while($row = $db->getRows($res))
  {
    $GLOBALS['players'][$row['id']]['id'] = $row['id'];
    $GLOBALS['players'][$row['id']]['firstname'] = $row['firstname'];
    $GLOBALS['players'][$row['id']]['lastname'] = $row['lastname'];
    $GLOBALS['players'][$row['id']]['email'] = $row['email'];
    $GLOBALS['players'][$row['id']]['phone'] = $row['phone'];
    $inches = $row['height'] % 12;
    $GLOBALS['players'][$row['id']]['heightIN'] = $inches;
    $GLOBALS['players'][$row['id']]['heightFT'] = ($row['height'] - $inches) / 12;
    $GLOBALS['players'][$row['id']]['weight'] = $row['weight'];
    $GLOBALS['players'][$row['id']]['positions'] = $row['positions'];
    $GLOBALS['players'][$row['id']]['bats'] = $row['bats'];
    $GLOBALS['players'][$row['id']]['throws'] = $row['throws'];
    
    if(!empty($row['birthdate']) && $row['birthdate'] != "0000-00-00")
    {
      list($year, $month, $day) = explode('-', $row['birthdate']);
      $birthdate = $month . "/" . $day . "/" . $year;
      $GLOBALS['players'][$row['id']]['birthdate'] = $birthdate;
    }
    
    $GLOBALS['players'][$row['id']]['nickname'] = $row['nickname'];
    $GLOBALS['players'][$row['id']]['img'] = $row['img'];
  }
}

function login($email, $password)
{
  global $db;

  $loginEmailCheck = $db->query("SELECT * FROM player WHERE (email = '" . $email . "')");
  $login = $db->query("SELECT * FROM player WHERE (email = '" . $email . "') and (password = '" . $password . "')");

  $routePage = "";

  if($db->num_rows($loginEmailCheck) != 1)
  {
    $_SESSION['error'] = "No users exists with email: " . $email;
    $routePage .= "login.php";
  }
  else if($db->num_rows($login) != 1)
  {
    $_SESSION['error'] = "Your email and password did not match our records in the database";
    $routePage .= "login.php";
  }
  else
  {
    while($row = $db->getRows($login))
    {
      $_SESSION['playerid'] = $row['id'];
      $_SESSION['priv'] = $row['privilege'];
      $_SESSION['firstname'] = $row['firstname'];
      $_SESSION['lastname'] = $row['lastname'];
    }
    $routePage .= "index.php";
  }

  header('Location: ' . $routePage);
}

?>
