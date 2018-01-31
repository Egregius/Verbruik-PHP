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
	</head>
	<body>
  <div>';
if(!isset($_REQUEST['new'])){
	session_start();
	if(isset($_REQUEST['euro']))$_SESSION['euro']=$_REQUEST['euro'];
	if(!isset($_SESSION['euro']))$_SESSION['euro']='false';
	if($user=='Guy')echo '<form action="https://home.egregius.be/floorplan.php"><input type="submit" value="Floorplan" class="btn b1"/></form>';
	elseif($user=='Kevin')echo '<form action="https://kevin.minja.be/floorplan.php"><input type="submit" value="Floorplan" class="btn b1"/></form>';
	echo '<form method="GET">
			<input type="hidden" name="new" value="true"/>
			<input type="submit" value="Nieuwe invoer" class="btn b1">
		</form>
		<form action="dag.php">
			<input type="hidden" name="'.$user.'" value="on"/>
			<input type="submit" value="Grafiek per dag" class="btn b1"/>
		</form>
		<form action="maand.php">
			<input type="hidden" name="'.$user.'" value="on"/>
			<input type="submit" value="Grafiek per maand" class="btn b1"/>
		</form>
		<form action="jaar.php">
			<input type="hidden" name="'.$user.'" value="on"/>
			<input type="submit" value="Grafiek per jaar" class="btn b1"/>
		</form>
		<form action="jaren.php">
			<input type="hidden" name="'.$user.'" value="on"/>
			<input type="submit" value="Overzicht jaren" class="btn b1"/>
		</form>
		<form action="tempjaar.php">
			<input type="submit" value="Temperaturen" class="btn b1"/>
		</form>
		<br><br>
		<form method="POST">';
		if($_SESSION['euro']=='false')echo '<input type="hidden" name="euro" value="true"/><input type="submit" name="submit" value="Grafieken in verbruik" class="btn b1"/></form>';
		else echo '<input type="hidden" name="euro" value="false"/><input type="submit" name="submit" value="Grafieken in euro" class="btn b1"/></form>';
		echo '<br><br><br><br><br><br><br><br><br><br><br><br>
		<form method="POST" action="euro.php">
			<input type="hidden" name="verbruik" value="true"/>
			<input type="submit" value="Prijzen invoeren" class="btn b1">
		</form>
		<form method="POST" action="wis.php">
			<input type="hidden" name="verbruik" value="true"/>
			<input type="submit" value="Wis tellerstand" class="btn b1">
		</form>
		<form method="POST" action="download.php">
			<input type="hidden" name="verbruik" value="true"/>
			<input type="submit" value="Verbruik per dag als CSV" class="btn b1">
		</form>
		<form method="POST" action="download.php">
			<input type="hidden" name="tellerstanden" value="true"/>
			<input type="submit" value="Tellerstanden als CSV" class="btn b1">
		</form>
		<form method="POST">
			<input type="hidden" name="uitloggen" value="true"/>
			<input type="hidden" name="username" value="'.$user.'"/>
			<input type="submit" value="Uitloggen" class="btn b1">
		</form>
    </div>';
}else{
	$last=mysqli_fetch_assoc(mysqli_query($db,"SELECT gas,elec,zon,water FROM `$tabel` order by date desc limit 0,1"));
	//echo '<pre>';print_r($_REQUEST);echo '</pre>';
	echo '<br><br><br><table>';
	if(!isset($_REQUEST['gas'])){
		echo '
		<h2>Gas: '.number_format($last['gas'],3,',','').'</h2>
		<form method="GET"><input type="hidden" name="new" value="true"/>
		  <input type="number" step="0.001" min="'.($last['gas']-500000).'.000" max="'.($last['gas']+500000).'.999" name="gas" value="'.number_format($last['gas'],0,'.','').'" class="btn b1" autofocus/>
		  <input type="submit" value="Verder" class="btn b1">
		</form>
		<div class="bottom"><form action="index.php"><input type="submit" value="Annuleer" class="btn b1"/></form></div>';
	}elseif(!isset($_REQUEST['elec'])){
		echo '<tr><td><h2>Gas:</h2></td><td align="right"><h2>'.number_format($_REQUEST['gas'],3,',','').'</h2></td></tr>';
		echo '<h2>Electriciteit: '.number_format($last['elec'],1,',','').'</h2>';
		echo '<form method="GET"><input type="hidden" name="new" value="true"/>
		<input type="hidden" name="gas" value="'.$_REQUEST['gas'].'"/>';
		if($users[$user]['nacht']) echo '<input type="number" name="elec" step="0.001" value="" class="btn b1" autofocus/>
			<input type="number" name="nacht" step="0.001" value="" class="btn b1"/>';
		else echo '<input type="number" name="elec" step="0.001" min="'.($last['elec']-500000).'.000" max="'.($last['elec']+500000).'.999" value="'.number_format($last['elec'],0,'.','').'" class="btn b1" autofocus/>';
		echo '<input type="submit" value="Verder" class="btn b1">
		</form>
		<div class="bottom"><form action="index.php"><input type="submit" value="Annuleer" class="btn b1"/></form></div>';
	}elseif(!isset($_REQUEST['zon'])&&$users[$user]['zon']){
		echo '<tr><td><h2>Gas:</h2></td><td align="right"><h2>'.number_format($_REQUEST['gas'],3,',','').'</h2></td></tr>';
		echo '<tr><td><h2>Elec:</h2></td><td align="right"><h2>'.number_format($_REQUEST['elec']+@$_REQUEST['nacht'],3,',','').'</h2></td></tr>';
		if($user=='Guy')$zon=file_get_contents('http://secure.egregius.be/totaalzon2verbruik.php');
		else $zon=number_format($last['zon'],0,'','');
		echo '<h2>Zon: '.number_format($zon,3,',','').'</h2>';
		echo '<form method="GET"><input type="hidden" name="new" value="true"/>
		<input type="hidden" name="gas" value="'.$_REQUEST['gas'].'"/>
		<input type="hidden" name="elec" value="'.$_REQUEST['elec'].'"/>
		<input type="hidden" name="nacht" value="'.@$_REQUEST['nacht'].'"/>
		<input type="number" name="zon" step="0.001" value="'.$zon.'" class="btn b1" autofocus/>
		<input type="submit" value="Verder" class="btn b1">
		</form>
		<div class="bottom"><form action="index.php"><input type="submit" value="Annuleer" class="btn b1"/></form></div>';
	}elseif(!isset($_REQUEST['water'])){
		echo '<tr><td><h2>Gas:</h2></td><td align="right"><h2>'.number_format($_REQUEST['gas'],3,',','').'</h2></td></tr>';
		echo '<tr><td><h2>Elec:</h2></td><td align="right"><h2>'.number_format($_REQUEST['elec']+@$_REQUEST['nacht'],1,',','').'</h2></td></tr>';
		if($users[$user]['zon'])echo '<tr><td><h2>Zon:</h2></td><td align="right"><h2>'.number_format(@$_REQUEST['zon'],3,',','').'</h2></td></tr>';
		echo '<h2>Water: '.number_format($last['water'],3,',','').'</h2>';
		echo '<form method="GET"><input type="hidden" name="new" value="true"/>
		<input type="hidden" name="gas" value="'.$_REQUEST['gas'].'"/>
		<input type="hidden" name="elec" value="'.$_REQUEST['elec'].'"/>
		<input type="hidden" name="nacht" value="'.@$_REQUEST['nacht'].'"/>
		<input type="hidden" name="zon" value="'.@$_REQUEST['zon'].'"/>
		<input type="number" name="water" step="0.001" min="'.($last['water']-500000).'.000" max="'.($last['water']+500000).'.999" value="'.number_format(floor($last['water']),0,'.','').'" class="btn b1" autofocus/>
		<input type="submit" value="Verder" class="btn b1">
		</form>
		<div class="bottom"><form action="index.php"><input type="submit" value="Annuleer" class="btn b1"/></form></div>';
	}elseif(!isset($_REQUEST['date'])){
		echo '<tr><td><h2>Gas:</h2></td><td align="right"><h2>'.number_format($_REQUEST['gas'],3,',','').'</h2></td></tr>';
		echo '<tr><td><h2>Elec:</h2></td><td align="right"><h2>'.number_format($_REQUEST['elec']+@$_REQUEST['nacht'],1,',','').'</h2></td></tr>';
		if($users[$user]['zon'])echo '<tr><td><h2>Zon:</h2></td><td align="right"><h2>'.@number_format(@$_REQUEST['zon'],3,',','').'</h2></td></tr>';
		echo '<tr><td><h2>Water:</h2></td><td align="right"><h2>'.number_format($_REQUEST['water'],3,',','').'</h2></td></tr>';
		echo '<h2>Datum tellerstand:</h2>';
		echo '<form method="GET"><input type="hidden" name="new" value="true"/>
		<input type="hidden" name="gas" value="'.$_REQUEST['gas'].'"/>
		<input type="hidden" name="elec" value="'.$_REQUEST['elec'].'"/>
		<input type="hidden" name="nacht" value="'.@$_REQUEST['nacht'].'"/>
		<input type="hidden" name="water" value="'.$_REQUEST['water'].'"/>
		<input type="hidden" name="zon" value="'.@$_REQUEST['zon'].'"/>
		<input type="date" name="date" value="'.date("Y-m-d",time()).'" class="btn b1" autocomplete="off" autofocus/>
		<input type="submit" value="Opslaan" class="btn b1">
		</form>';
		echo '<div class="bottom"><form action="index.php"><input type="submit" value="Annuleer" class="btn b1"/></form></div>';
	}else{
		$date=$_REQUEST['date'];
		$elec=$_REQUEST['elec']+@$_REQUEST['nacht'];
		$gas=$_REQUEST['gas'];
		$water=$_REQUEST['water'];
		$zon=@$_REQUEST['zon'];
		$query="INSERT INTO `$tabel` (`date`,`gas`,`elec`,`zon`,`water`,`calculated`) VALUES ('$date','$gas','$elec','$zon','$water','0') ON DUPLICATE KEY UPDATE `gas`='$gas',`elec`='$elec',`zon`='$zon',`water`='$water',`calculated`='0';";
		if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
		shell_exec("curl -s 'https://verbruik.egregius.be/berekendag.php?user=$user'");
		header("Location: index.php");die("Redirecting to: index.php");
	}
	echo '</table>';
}
echo '
  </body>
</html>';
