<?php 
// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

require_once('config.php');
require_once('database.php');

$selectSQLUp = "select * from games where DATE_ADD(scheduledate, INTERVAL 3 HOUR) >=  NOW() order by scheduledate";
$selectSpecificSQLField = "select * from field where id = ";
$upcominggames = array();

$res = $db->query($selectSQLUp);

if($db->num_rows($res) > 0)
{
	while($row = $db->getRows($res))
	{
		$id = $row['id'];
		$upcominggames[$id]['id'] = $row['id'];
		
		list($year, $month, $day, $hr, $min, $sec) = split('[-: ]', $row['scheduledate']);            
		$isPM = false;

		if($hr >= 12)
		{
			$isPM = true;
			
			if($hr != 12)
			{
				$hr -= 12;
			}
		}
		$upcominggames[$id]['date'] = $month . "/" . $day . "/" . $year;
		$time = $hr . ":" . $min . " ";
		if($isPM)
		{
			$time .= "PM";
		}
		else
		{
			$time .= "AM";
		}
		$upcominggames[$id]['time'] = $time;
		
		$upcominggames[$id]['opponent'] = $row['opponent'];
		$upcominggames[$id]['type'] = $row['type'];
		$upcominggames[$id]['fieldid'] = $row['fieldid'];

		if(!empty($row['fieldid']))
		{
			$res2 = $db->query($selectSpecificSQLField . $row['fieldid']);
			
			while($row = $db->getRows($res2))
			{
				$upcominggames[$id]['field'] = $row['name'];
				$upcominggames[$id]['address'] = $row['address'];
			}
		}
		else 
		{
			$upcominggames[$id]['field'] = "TBD";
		}
	}
}

echo json_encode($upcominggames);

?>