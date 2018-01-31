<?php
if(isset($_REQUEST['user'])){
	$db=new mysqli('127.0.0.1','home','H0m€','verbruik');
	if($db->connect_errno>0){die('Unable to connect to database ['.$db->connect_error.']');}
	$tabel=$_REQUEST['user'];
	$tabeldag=$tabel.'dag';
	$tabeleuro=$tabel.'euro';
	$sql="SELECT date,gas,elec,zon,water FROM $tabeldag ORDER BY date desc LIMIT 0,5000";
	if(!$result=$db->query($sql)){die('There was an error running the query ['.$sql.'-'.$db->error.']');}
	$values=array();
	while($row=$result->fetch_assoc())$values[]=$row;$result->free();
	foreach($values as $value){
		$euro=array();
		$date=$value['date'];
		$sql="SELECT date,gas,elec,zon,water FROM $tabeleuro WHERE date < '$date' ORDER BY date desc LIMIT 0,1";
		if(!$result=$db->query($sql)){die('There was an error running the query ['.$sql.'-'.$db->error.']');}
		while($row=$result->fetch_assoc())$euro[]=$row;$result->free();
		$vgaseuro=round($value['gas']*$euro[0]['gas']);
		$veleceuro=round($value['elec']*$euro[0]['elec']);
		$vverbruik=$value['elec']+$value['zon'];
		$vverbruikeuro=round($vverbruik*$euro[0]['elec']);
		$vzoneuro=round($value['zon']*($euro[0]['elec']+$euro[0]['zon']));
		$vwatereuro=round($value['water']*$euro[0]['water']);
		$query="UPDATE `$tabeldag` set `verbruik`='$vverbruik',`gaseuro`='$vgaseuro',`eleceuro`='$veleceuro',`verbruikeuro`='$vverbruikeuro',`zoneuro`='$vzoneuro',`watereuro`='$vwatereuro' where date = '$date';";
		echo $query.'<br>';
		if(!$result=$db->query($query)){die('There was an error running the query ['.$query .' - '.$db->error.']');}
		//print_r($value);
		echo '<hr>';
	}
}
?>