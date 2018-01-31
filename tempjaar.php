<?php
require_once'/var/www/verbruik.egregius.be/secure/settings.php';
//require_once'/var/www/verbruik.egregius.be/secure/chart.php';

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

echo '
<div style="width:920px">
	<form action="index.php"><input type="submit" value="home" class="btn b3"/></form>
	<form action="tempmaand.php"><input type="submit" value="maand" class="btn b3"/></form>
	<form action="tempjaar.php"><input type="submit" value="jaar" class="btn b3 btna"/></form>
</div>';
$query="SELECT left(stamp,4) as jaar FROM temp_buiten group by left(stamp,4)";
if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
while($row=$result->fetch_assoc()){
	$jaren[]=$row['jaar'];
}
$result->free();
$aantaljaren=count($jaren);
$colors=array('FF0000','00FF00','0000FF','FF0000','00FF00','0000FF');
foreach($jaren as $jaar){
	for($k=0;$k<=11;$k++){
		$items[$k]['Datum']=$k+1;
		$items[$k][$jaar.'-min']=0;
		$items[$k][$jaar.'-max']=0;
		$items[$k][$jaar.'-avg']=0;
	}
	$query="SELECT right(left(stamp,7),2) as stamp, min(min) as min, max(max) as max, avg(avg) as avg FROM temp_buiten where left(stamp,4) like '$jaar' group by right(left(stamp,7),2)";
	if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
	$q=$q+1;
	$k=0;
	while($row=$result->fetch_assoc()){
		$maand=number_format($row['stamp'],0);
		$items[$maand-1]['Datum']=$maand;
		$items[$maand-1][$jaar.'-min']=number_format($row['min'],1);
		$items[$maand-1][$jaar.'-max']=number_format($row['max'],1);
		$items[$maand-1][$jaar.'-avg']=number_format($row['avg'],1);
		$k=$k+1;
	}
	$result->free();
}
echo '<pre>';
//print_r($items);
//echo '<pre>colors<br>';print_r($colors);echo '</pre>';
//echo '<pre>selectedusers<br>';print_r($selectedusers);echo '</pre>';
//echo '<pre>items<br>';print_r($items);echo '</pre>';

//echo '<pre>graphgas<br>';print_r($graphgas);echo '</pre>';
//echo '<pre>graphelec<br>';print_r($graphelec);echo '</pre>';
//echo '<pre>graphzon<br>';print_r($graphzon);echo '</pre>';
//echo '<pre>graphverbr<br>';print_r($graphverbr);echo '</pre>';

//echo date("d/m/Y", $startdate).' - '.date("d/m/Y", $maxdatum);
$line_styles=array('lineDashStyle:[0,0]','lineDashStyle:[0,0]');
echo '<div style="width:920px"><h1>Temperaturen:</h1>'.$legend.'</div>';
//$args=array('chart'=>'ColumnChart','width'=>900,'height'=>850,'hide_legend'=>false,'responsive'=>false,'background_color'=>'#000','chart_div'=>'graph','colors'=>$colors,'margins'=>array(10,10,50,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),'text_style'=>array('fontSize'=>12,'color'=>'CCCCCC'),'line_styles'=>$line_styles);
//$chart=array_to_chart($items,$args);
//echo $chart['script'];
//echo $chart['div'];
//unset($chart);

echo '<style>td{text-align:right;font-size:1.1em;}</style>';
if($udevice=='iPhone')echo '<style>td{text-align:right;font-size:1.6em;}</style>';
echo '<table border=1>
	<tr>
		<th></th>';
foreach($jaren as $jaar){
	echo'<th colspan=3>'.$jaar.'</th>';
}
	echo '</tr>
	<tr>
		<th>Maand</th>';
foreach($jaren as $jaar)	echo '
		<th>Min</th>
		<th>Avg</th>
		<th>Max</th>';
	echo '
	</tr>
	';
foreach($items as $i){
	echo '<tr>
		';
	echo'<td>'.$i['Datum'].'</td>';
	foreach($jaren as $jaar){
		echo'<td>'.$i[$jaar.'-min'].'</td>';
		echo'<td>'.$i[$jaar.'-avg'].'</td>';
		echo'<td>'.$i[$jaar.'-max'].'</td>';
	}
	echo '</tr>';
}
echo '</table>';
echo 'Queries used: '.$q;
echo '</body></html>';
?>