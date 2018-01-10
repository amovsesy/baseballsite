<?php 
// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('database.php');

$selectSQL = "select * from player where privilege < 3 order by firstname asc";
$players = array();

$res = $db->query($selectSQL);

while($row = $db->getRows($res))
{
  $players[$row['id']]['id'] = $row['id'];
  $players[$row['id']]['firstname'] = $row['firstname'];
  $players[$row['id']]['lastname'] = $row['lastname'];
  $players[$row['id']]['email'] = $row['email'];
  $players[$row['id']]['phone'] = $row['phone'];
  $inches = $row['height'] % 12;
  $players[$row['id']]['heightIN'] = $inches;
  $players[$row['id']]['heightFT'] = ($row['height'] - $inches) / 12;
  $players[$row['id']]['weight'] = $row['weight'];
  $players[$row['id']]['positions'] = $row['positions'];
  $players[$row['id']]['bats'] = $row['bats'];
  $players[$row['id']]['throws'] = $row['throws'];
  
  if(!empty($row['birthdate']) && $row['birthdate'] != "0000-00-00")
  {
		list($year, $month, $day) = split('-', $row['birthdate']);
		$birthdate = $month . "/" . $day . "/" . $year;
		$players[$row['id']]['birthdate'] = $birthdate;
  }
  
  $players[$row['id']]['nickname'] = $row['nickname'];
  $players[$row['id']]['img'] = $row['img'];
}

echo json_encode($players);

?>