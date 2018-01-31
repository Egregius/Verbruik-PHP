<?php
require_once'/var/www/verbruik.egregius.be/secure/settings.php';
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Verbruik</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
		<meta http-equiv="Cache-control" content="no-cache, must-revalidate"/>
		<meta http-equiv="Pragma" content="no-cache"/>
		<meta name="HandheldFriendly" content="true"/>
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui"/>
		<link rel="icon" href="/favicon.ico"/>
		<link rel="shortcut icon" href="/favicon.ico"/>
		<link rel="apple-touch-icon" href="/favicon.ico"/>
		<meta name="mobile-web-app-capable" content="yes">
		<link href="style.php" rel="stylesheet" type="text/css"/>
		<script type="text/javascript">function navigator_Go(url){window.location.assign(url);}</script>
		<style>
			.btn{min-width:200px;}
		</style>
	</head>
	<body>
  <div>
  <div class="menu">
		<form action="index.php">
			<input type="submit" value="home" class="btn b1"/>
		</form>
	</div>
	<div class="first"><br>';
if(isset($_POST['wis'])){
	$date=$_POST['wis'];
	$sql="DELETE FROM $user WHERE date = '$date';";
	if(!$result=$db->query($sql)){die('There was an error running the query ['.$sql.'-'.$db->error.']');}
	shell_exec("curl -s 'https://verbruik.egregius.be/berekendag.php?user=$user'");
}
$sql="SELECT date,gas,elec,zon,water FROM $user ORDER BY date desc LIMIT 0,150;";
if(!$result=$db->query($sql)){die('There was an error running the query ['.$sql.'-'.$db->error.']');}
echo '<table><tr><td>datum</td><td>dagen</td><td>gas</td><td>elec</td><td>zon</td><td>water</td></tr>';
$prev=time();
while($row=$result->fetch_assoc()){
	$date=strtotime($row['date']);
	$since=floor(($prev-$date)/86400);
	echo '<tr><td><form method="POST" onsubmit="return confirm(\'Wil je deze tellerstand wissen?\');"><input type="submit" name="date" value="'.strftime("%a %e/%m/%Y",strtotime($row['date'])).'" class="btn btna"/><input type="hidden" name="wis" value="'.$row['date'].'"/></form></td><td>'.$since.'</td><td>'.$row['gas'].'</td><td>'.$row['elec'].'</td><td>'.$row['zon'].'</td><td>'.$row['water'].'</td></tr>';
	$prev=$date;
}
echo '</table></div></body></html>';