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
$table=$user.'euro';
if(isset($_POST['wis'])){
	$date=$_POST['date'];
	$sql="DELETE FROM $table WHERE date = '$date';";
	if(!$result=$db->query($sql)){die('There was an error running the query ['.$sql.'-'.$db->error.']');}
	shell_exec("curl -s 'https://verbruik.egregius.be/berekendageuro.php?user=$user'");
}elseif(isset($_POST['new'])){
	$date=$_POST['date'];
	$gas=$_POST['gas'];
	$elec=$_POST['elec'];
	$zon=$_POST['zon'];
	$water=$_POST['water'];
	$query="INSERT INTO `$table` (`date`,`gas`,`elec`,`zon`,`water`) VALUES ('$date','$gas','$elec','$zon','$water') ON DUPLICATE KEY UPDATE `gas`='$gas',`elec`='$elec',`zon`='$zon',`water`='$water';";
	if(!$result=$db->query($query)){die('There was an error running the query ['.$query.'-'.$db->error.']');}
	shell_exec("curl -s 'https://verbruik.egregius.be/berekendageuro.php?user=$user'");
}
$sql="SELECT date,gas,elec,zon,water FROM $table ORDER BY date desc LIMIT 0,150;";
if(!$result=$db->query($sql)){die('There was an error running the query ['.$sql.'-'.$db->error.']');}
echo '<table>
	<tr>
		<td>datum</td>
		<td>gas</td>
		<td>elec</td>
		<td>zon</td>
		<td>water</td>
	</tr>
	<tr>
		<form method="POST">
		<td><input type="date" name="date" class="btn"/></td>
		<td><input type="numeric" name="gas" class="btn"/></td>
		<td><input type="numeric" name="elec" class="btn"/></td>
		<td><input type="numeric" name="zon" class="btn"/></td>
		<td><input type="numeric" name="water" class="btn"/></td>
		<td><input type="submit" name="new" value="Opslaan" class="btn"/></td>
		</form>
	</tr>';
$prev=time();
while($row=$result->fetch_assoc()){
	echo '<tr>
			<form method="POST">
			<td><input type="date" name="date" value="'.$row['date'].'" class="btn"/></td>
			<td><input type="numeric" name="gas" value="'.$row['gas'].'" class="btn"/></td>
			<td><input type="numeric" name="elec" value="'.$row['elec'].'" class="btn"/></td>
			<td><input type="numeric" name="zon" value="'.$row['zon'].'" class="btn"/></td>
			<td><input type="numeric" name="water" value="'.$row['water'].'" class="btn"/></td>
			<td><input type="submit" name="new" value="Opslaan" class="btn"/></td>
			<td><input type="submit" name="wis" value="Wissen" class="btn"/></td>
			</form>
		</tr>';
}
echo '</table></div></body></html>';