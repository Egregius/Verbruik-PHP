<?php
if(isset($_REQUEST['gpio'])){
	if($_REQUEST['gpio']==20){$veld='gas';$value=0.001;}
	elseif($_REQUEST['gpio']==21){$veld='water';$value=0.001;}
	else die('Unknown');
	$datum=date("Y-m-d",time());
	$db=new mysqli('localhost','home','H0m€','verbruik');
	if($db->connect_errno>0){die('Unable to connect to database ['.$db->connect_error.']');}
	if(!$result=$db->query("update `Guydag` set `$veld`=`$veld`+$value where `date` = '$datum';")){die('There was an error running the query ['.$query.'-'.$db->error.']');}
	if($db->affected_rows==1){
		if(!$result=$db->query("update `Guy` set `$veld`=`$veld`+$value where `date` = '$datum';")){die('There was an error running the query ['.$query.'-'.$db->error.']');}
		//echo "update `Guy` set `$veld`=`$veld`+$value where `date` = '$datum';<br>";
		//echo 'aantalrijen='.$db->affected_rows.'<br>';
		if($db->affected_rows==1){
			echo 'OK';
		}
	}
}