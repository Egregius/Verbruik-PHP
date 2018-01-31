<?php
require_once'/var/www/verbruik.egregius.be/secure/settings.php';
require_once'/var/www/verbruik.egregius.be/secure/chart.php';
session_start();
if(isset($_REQUEST['jaar']))$_SESSION['jaar']=$_REQUEST['jaar'];else $_SESSION['jaar']=strftime("%Y",time());
if(isset($_REQUEST['maand']))$_SESSION['maand']=$_REQUEST['maand'];else $_SESSION['maand']=strftime("%m",time());
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Verbruik</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
		<meta name="HandheldFriendly" content="true"/>
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui"/>
		<link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
		<link rel="shortcut icon" href="images/domoticzphp48.png"/>
		<link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
		<meta name="mobile-web-app-capable" content="yes">
		<link href="style.php" rel="stylesheet" type="text/css"/>
		<script type="text/javascript">function navigator_Go(url){window.location.assign(url);}</script>
	</head>
	<body>';
$legend='<div class="legend"><form method="GET">';
$legend.='</form></div>';
$colors=array('0000FF','FF0000','FFFF00');

echo '
<div style="width:920px">
	<form action="index.php"><input type="submit" value="home" class="btn b3"/></form>
	<form action="tempmaand.php"><input type="submit" value="maand" class="btn b3 btna"/></form>
	<form action="tempjaar.php"><input type="submit" value="jaar" class="btn b3"/></form>
</div>';
$query="SELECT left(stamp,4) as jaar FROM temp_buiten group by left(stamp,4)";
if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
while($row=$result->fetch_assoc()){
	$jaren[]=$row['jaar'];
}
$result->free();
$jaar=$_SESSION['jaar'];
$query="SELECT left(right(stamp,5),2) as maand FROM temp_buiten where left(stamp,4) like '$jaar' group by left(right(stamp,5),2)";
if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
while($row=$result->fetch_assoc()){
	$maanden[]=$row['maand'];
}
$result->free();
echo '<br><form method="POST">';
echo '<select name="jaar" onChange="this.form.submit()" class="btn b2 center">';
foreach($jaren as $jaar){
	if($_SESSION['jaar']==$jaar)echo '<option value="'.$jaar.'" selected>'.$jaar.'</option>';
	else echo '<option value="'.$jaar.'">'.$jaar.'</option>';
}
echo '</select><select name="maand" onChange="this.form.submit()" class="btn b2 center">';
foreach($maanden as $maand){
	if($_SESSION['maand']==$maand)echo '<option value="'.$maand.'" selected>'.$maand.'</option>';
	else echo '<option value="'.$maand.'">'.$maand.'</option>';
}
echo '</select></form>';
$selectedmonth=$_SESSION['jaar'].'-'.$_SESSION['maand'];
$query="SELECT stamp, min, max, avg FROM temp_buiten where left(stamp,7) like '$selectedmonth'";
if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
$q=$q+1;
$k=0;
if($result->num_rows==0)die( '<br><br>No data for this month');
while($row=$result->fetch_assoc()){
	$items[$k]['Datum']=$row['stamp'];
	$items[$k]['min']=number_format($row['min'],1);
	$items[$k]['max']=number_format($row['max'],1);
	$items[$k]['avg']=number_format($row['avg'],1);
	$k=$k+1;
}
$result->free();
$line_styles=array('lineDashStyle:[0,0]','lineDashStyle:[0,0]');
echo '<div style="width:920px"><h1>Temperaturen:</h1>'.$legend.'</div>';
$args=array('width'=>900,'height'=>850,'hide_legend'=>false,'responsive'=>false,'background_color'=>'#000','chart_div'=>'graph','colors'=>$colors,'margins'=>array(10,10,50,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),'text_style'=>array('fontSize'=>12,'color'=>'CCCCCC'),'line_styles'=>$line_styles);
$chart=array_to_chart($items,$args);
echo $chart['script'];
echo $chart['div'];
unset($chart);


echo '</body></html>';
?>