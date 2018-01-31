<?php
if(isset($_REQUEST['verbruik'])){
	$user=$_REQUEST['user'];
	$tabeleuro=$user.'euro';
	$tabeldag=$user.'dag';
	$gas=0;
	$elec=0;
	$zon=0;
	$water=0;
	$db=new mysqli('localhost','home','H0m€','verbruik');
	if($db->connect_errno>0){die('Unable to connect to database ['.$db->connect_error.']');}
	$date=date("Y-m-d",time());
	$dbz=new mysqli('localhost','zonphp_u','jO7knokS3eId6mYb8Di8Hy0Hig4Daw9','egregius_zonphp');
	if($dbz->connect_errno>0){die('Unable to connect to database ['.$dbz->connect_error.']');}
	$stamp=date("Y-m-d", time());
	$query="SELECT Geg_Maand FROM `tgeg_maand` WHERE `Datum_Maand` = '$stamp  0:00:00';";
	echo $query.'<hr>';
	if(!$result=$dbz->query($query))echo('There was an error running the query "'.$query.'" '.$dbz->error);
	while($row=$result->fetch_assoc())$zon=$row['Geg_Maand'];$result->free();$dbz->close();
	$query="SELECT date,gas,elec,zon,water FROM `$user` WHERE `calculated` = '0' order by date desc limit 0,1;";
	echo $query.'<hr>';
	if(!$result=$db->query($query))echo('There was an error running the query "'.$query.'"  '.$db->error);
	while($row=$result->fetch_assoc()){
		$lastreal=$row['date'];
		$lastgas=$row['gas'];
		$lastelec=$row['elec'];
		$lastzon=$row['zon'];
		$lastwater=$row['water'];
	}
	$result->free();
	if($lastreal==$date)die('Manuele tellerstand al ingevuld voor vandaag');
	$query="SELECT date FROM `$user` WHERE `calculated` = '1' order by date desc limit 0,1;";
	echo $query.'<hr>';
	if(!$result=$db->query($query))echo('There was an error running the query "'.$query.'"  '.$db->error);
	while($row=$result->fetch_assoc()){
		$lastcalculated=$row['date'];
	}

	$result->free();
	if(isset($_REQUEST['gas']))$gas=$_REQUEST['gas'];
	if(isset($_REQUEST['verbruik'])){$elec=$_REQUEST['verbruik']-$zon;$verbruik=$_REQUEST['verbruik'];}
	if(isset($_REQUEST['water']))$water=$_REQUEST['water'];
	$query="SELECT sum(`gas`) as gas,sum(`elec`) as elec,sum(`zon`) as zon,sum(`water`) as water FROM `$tabeldag` WHERE `date` > '$lastreal' AND `date` < '$date';";
	echo $query.'<hr>';
	if(!$result=$db->query($query))echo('There was an error running the query "'.$query.'"  '.$db->error);
	while($row=$result->fetch_assoc()){
		$sumgas=$row['gas']+$lastgas+$gas;
		$sumelec=$row['elec']+$lastelec+$elec;
		$sumzon=$row['zon']+$lastzon+$zon;
		$sumwater=$row['water']+$lastwater+$water;
	}$result->free();
	$euro=array();
	$sql="SELECT date,gas,elec,zon,water FROM $tabeleuro WHERE date <= '$date' ORDER BY date desc LIMIT 0,1";
	echo $sql.'<br>';
	if(!$result=$db->query($sql)){die('There was an error running the query ['.$sql.']<br>'.$db->error);}
	while($row=$result->fetch_assoc())$euro[]=$row;$result->free();
	$gaseuro=$gas*$euro[0]['gas'];
	$eleceuro=$elec*$euro[0]['elec'];
	$verbruikeuro=$verbruik*$euro[0]['elec'];
	$zoneuro=$zon*($euro[0]['elec']+$euro[0]['zon']);
	$watereuro=$water*$euro[0]['water'];

	$query="INSERT INTO `$tabeldag` (`date`,`gas`,`elec`,`verbruik`,`zon`,`water`,`gaseuro`,`eleceuro`,`verbruikeuro`,`zoneuro`,`watereuro`) VALUES ('$date','$gas','$elec','$verbruik','$zon','$water','$gaseuro','$eleceuro','$verbruikeuro','$zoneuro','$watereuro') ON DUPLICATE KEY UPDATE `gas`='$gas',`elec`='$elec',`verbruik`='$verbruik',`zon`='$zon',`water`='$water',`gaseuro`='$gaseuro',`eleceuro`='$eleceuro',`verbruikeuro`='$verbruikeuro',`zoneuro`='$zoneuro',`watereuro`='$watereuro'";
	echo $query.'<hr>';
	if(!$result=$db->query($query)){echo('There was an error running the query "'.$query.'" - '.$db->error);}
	$query="INSERT INTO `$user` (`date`,`gas`,`elec`,`zon`,`water`,`calculated`) VALUES ('$date','$sumgas','$sumelec','$sumzon','$sumwater','1') ON DUPLICATE KEY UPDATE `gas`='$sumgas',`elec`='$sumelec',`zon`='$sumzon',`water`='$sumwater'";
	echo $query.'<hr>';
	if(!$result=$db->query($query)){echo('There was an error running the query "'.$query.'" - '.$db->error);}
}
unset($_COOKIE,$_GET,$_POST,$_FILES,$_SERVER,$db,$dbz,$query,$result,$v,$row);
echo '<hr><pre>';print_r(GET_DEFINED_VARS());echo '</pre>';
