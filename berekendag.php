<?php
if(isset($_REQUEST['user'])){
	$db=new mysqli('127.0.0.1','home','H0m€','verbruik');
	if($db->connect_errno>0){die('Unable to connect to database ['.$db->connect_error.']');}
	$tabel=$_REQUEST['user'];
	$tabeldag=$tabel.'dag';
	if($tabel=='Guy'){
		$sql="DELETE FROM $tabel WHERE `calculated` = '1'";
		if(!$result=$db->query($sql)){die('There was an error running the query ['.$sql.'-'.$db->error.']');}
	}
	$sql="SELECT date,gas,elec,zon,water FROM $tabel ORDER BY date desc LIMIT 0,5";
	if(!$result=$db->query($sql)){die('There was an error running the query ['.$sql.'-'.$db->error.']');}
	$values=array();
	$prevdate=0;
	while($row=$result->fetch_assoc())$values[]=$row;$result->free();
	foreach($values as $value){
		$newdate=strtotime($value['date']);
		if($newdate<$prevdate){
			$period=floor(($prevdate-$newdate)/86400);
			$newgas=$value['gas'];
			$newelec=$value['elec'];
			$newzon=$value['zon'];
			$newwater=$value['water'];
			$vgas=number_format(($prevgas-$newgas)/$period,12,'.',',');
			$velec=number_format(($prevelec-$newelec)/$period,12,'.',',');
			$vzon=number_format(($prevzon-$newzon)/$period,12,'.',',');
			$vverbruik=number_format($velec+$vzon,12,'.',',');
			$vwater=number_format(($prevwater-$newwater)/$period,12,'.',',');
			$tabeleuro=$tabel.'euro';
			for($k=1;$k<=$period;$k++){
				$euro=array();
				$curday=strftime("%Y-%m-%d",($prevdate-(86400*($k-1))));
				echo $k.' - '.$curday.' gas: '.$vgas.' - elec: '.$velec.' - zon:'.$vzon.'<br>';
				$sql="SELECT date,gas,elec,zon,water FROM $tabeleuro WHERE date <= '$curday' ORDER BY date desc LIMIT 0,1";
				if(!$result=$db->query($sql)){die('There was an error running the query ['.$sql.'-'.$db->error.']');}
				while($row=$result->fetch_assoc())$euro[]=$row;$result->free();
				$vgaseuro=$vgas*$euro[0]['gas'];
				$veleceuro=$velec*$euro[0]['elec'];
				$vverbruikeuro=$vverbruik*$euro[0]['elec'];
				$vzoneuro=$vzon*($euro[0]['elec']+$euro[0]['zon']);
				$vwatereuro=$vwater*$euro[0]['water'];
				$query="INSERT INTO `$tabeldag` (`date`,`gas`,`elec`,`verbruik`,`zon`,`water`,`gaseuro`,`eleceuro`,`verbruikeuro`,`zoneuro`,`watereuro`) VALUES ('$curday','$vgas','$velec','$vverbruik','$vzon','$vwater','$vgaseuro','$veleceuro','$vverbruikeuro','$vzoneuro','$vwatereuro') ON DUPLICATE KEY UPDATE `gas`='$vgas',`elec`='$velec',`verbruik`='$vverbruik',`zon`='$vzon',`water`='$vwater',`gaseuro`='$vgaseuro',`eleceuro`='$veleceuro',`verbruikeuro`='$vverbruikeuro',`zoneuro`='$vzoneuro',`watereuro`='$vwatereuro';";
				echo $query.'<br>';
				if(!$result=$db->query($query)){die('There was an error running the query ['.$query .' - '.$db->error.']');}
			}
			$prevgas=$newgas;
			$prevelec=$newelec;
			$prevzon=$newzon;
			$prevwater=$newwater;
		}else{
			$prevgas=$value['gas'];
			$prevelec=$value['elec'];
			$prevzon=$value['zon'];
			$prevwater=$value['water'];
		}
		//print_r($value);
		$prevdate=$newdate;
		echo '<hr>';
	}
}
?>