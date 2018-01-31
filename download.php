<?php
require_once'/var/www/verbruik.egregius.be/secure/settings.php';
if(isset($_REQUEST['tellerstanden'])){
	$query="SELECT date, gas, elec, zon, water FROM $user order by date asc";
	if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
	echo 'Datum;gas;electriciteit;zon;water<br>';
	while($row=$result->fetch_assoc()){
		echo strftime("%e/%m/%Y",strtotime($row['date'])).';'.$row['gas'].';'.$row['elec'].';'.$row['zon'].';'.$row['water'].'<br>';
	}
	$result->free();
}elseif(isset($_REQUEST['verbruik'])){
	$tabel=$user.'dag';
	$query="SELECT date, gas, elec, zon, water FROM $tabel order by date asc";
	if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
	echo 'Datum;gas;electriciteit;zon;water<br>';
	while($row=$result->fetch_assoc()){
		echo strftime("%e/%m/%Y",strtotime($row['date'])).';'.$row['gas'].';'.$row['elec'].';'.$row['zon'].';'.$row['water'].'<br>';
	}
	$result->free();
}