<?php
require_once('/var/www/verbruik.egregius.be/secure/settings.php');
echo '<h1>Updating/Installing database verbruik.egregius.be</h1>';



foreach($users as $user=>$value){
	echo '<h2>Checking user '.$user.'</h2>';
	$table=$user;
	$result=mysqli_fetch_assoc(mysqli_query($db,"SELECT count(*) as aantal FROM information_schema.TABLES WHERE (TABLE_SCHEMA = 'verbruik') AND (TABLE_NAME = '$table')"));
	if($result['aantal']==0){
		$query="CREATE TABLE $table (
			date date PRIMARY KEY,
			gas float(8,3) NOT NULL,
			elec float(8,1) NOT NULL,
			zon float(6,1) NOT NULL,
			water float(7,3) NOT NULL
			)";
		echo '<hr>'.$query;
		if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
	}
	$table=$user.'dag';
	$result=mysqli_fetch_assoc(mysqli_query($db,"SELECT count(*) as aantal FROM information_schema.TABLES WHERE (TABLE_SCHEMA = 'verbruik') AND (TABLE_NAME = '$table')"));
	if($result['aantal']==0){
		$query="CREATE TABLE $table (
			date date PRIMARY KEY,
			gas float(15,12) NOT NULL,
			elec float(15,12) NOT NULL,
			zon float(15,12) NOT NULL,
			water float(15,12) NULL
			)";
		echo '<hr>'.$query;
		if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
	}
	$table=$user;
	$result=mysqli_fetch_assoc(mysqli_query($db,"SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table' AND COLUMN_NAME = 'elec';"));
	if($result['COLUMN_TYPE']!='float(8,1)'){
		$query="ALTER TABLE `$table` CHANGE `elec` `elec` FLOAT(8,1) NOT NULL;";
		echo '<hr>'.$query;
		if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
	}
	$table=$user;
	$result=mysqli_fetch_assoc(mysqli_query($db,"SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table' AND COLUMN_NAME = 'generated';"));
	if($result['COLUMN_TYPE']!='tinyint(1)'){
		$query="ALTER TABLE `$table`  ADD `generated` BOOLEAN NOT NULL DEFAULT FALSE  AFTER `water`;";
		echo '<hr>'.$query;
		if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
	}
	$table=$user.'euro';
	$result=mysqli_fetch_assoc(mysqli_query($db,"SELECT count(*) as aantal FROM information_schema.TABLES WHERE (TABLE_SCHEMA = 'verbruik') AND (TABLE_NAME = '$table')"));
	if($result['aantal']==0){
		$query="CREATE TABLE `$table` (
			  `date` date NOT NULL,
			  `gas` int(11) NOT NULL,
			  `elec` int(11) NOT NULL,
			  `zon` int(11) NOT NULL,
			  `water` int(11) NOT NULL
			)";
		echo '<hr>'.$query;
		if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
				$query="ALTER TABLE `$table` ADD PRIMARY KEY (`date`);";
		echo '<hr>'.$query;
		if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
	}
	$table=$user.'dag';
	$result=mysqli_fetch_assoc(mysqli_query($db,"SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table' AND COLUMN_NAME = 'gaseuro';"));
	if($result['COLUMN_TYPE']!='mediumint(9)'){
		$query="ALTER TABLE `$table`  ADD `gaseuro` MEDIUMINT NULL  AFTER `water`,  ADD `eleceuro` MEDIUMINT NULL  AFTER `gaseuro`,  ADD `zoneuro` MEDIUMINT NULL  AFTER `eleceuro`,  ADD `watereuro` MEDIUMINT NULL  AFTER `zoneuro`;";
		echo '<hr>'.$query;
		if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
	}
	$table=$user.'dag';
	$result=mysqli_fetch_assoc(mysqli_query($db,"SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table' AND COLUMN_NAME = 'verbruik';"));
	if($result['COLUMN_TYPE']!='mediumint(9)'){
		$query="ALTER TABLE `$table`  ADD `verbruik` float(15,12) NULL  AFTER `elec`,  ADD `verbruikeuro` MEDIUMINT NULL  AFTER `eleceuro`;";
		echo '<hr>'.$query;
		if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
	}
}
?>