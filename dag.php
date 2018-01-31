<?php
require_once'/var/www/verbruik.egregius.be/secure/settings.php';
require_once'/var/www/verbruik.egregius.be/secure/chart.php';
session_start();
$time=time();
$colors=array();
$selectedusers=array();
foreach($users as $u=>$p){
	if(isset($_GET[$u]))$_SESSION[$u]=true;else $_SESSION[$u]=false;
	if($_SESSION[$u])array_push($colors,${$u});
	if($_SESSION[$u])array_push($colors,${$u});
	if($_SESSION[$u])array_push($selectedusers,$u);
}
$numberusers=0;
$numberzonusers=0;
$maxgraphdate=time();
foreach($selectedusers as $u){
	$numberusers=$numberusers+1;
	if($users[$u]['zon']==1)$numberzonusers=$numberzonusers+1;
	if(strtotime($users[$u]['maxdate'])<$maxgraphdate)$maxgraphdate=strtotime($users[$u]['maxdate']);

}
if(isset($_REQUEST['f_startdate']))$_SESSION['f_startdate']=$_REQUEST['f_startdate'];
if(isset($_REQUEST['f_enddate']))$_SESSION['f_enddate']=$_REQUEST['f_enddate'];
if(isset($_REQUEST['euro']))$_SESSION['euro']=$_REQUEST['euro'];
if(!isset($_SESSION['f_startdate']))$_SESSION['f_startdate']=date("Y-m-d",$maxgraphdate-(86400*30));
if(!isset($_SESSION['f_enddate']))$_SESSION['f_enddate']=date("Y-m-d",$maxgraphdate);
if(!isset($_SESSION['euro']))$_SESSION['euro']='false';
if(isset($_REQUEST['clear'])){$_SESSION['f_startdate']=$_REQUEST['r_startdate'];$_SESSION['f_startdate']=$_REQUEST['r_startdate'];}
if($_SESSION['f_startdate']>$_SESSION['f_enddate'])$_SESSION['f_enddate']=$_SESSION['f_startdate'];
$f_startdate=$_SESSION['f_startdate'];
$f_enddate=$_SESSION['f_enddate'];
$r_startdate=date("Y-m-d",$time);
$r_enddate=date("Y-m-d",$time);
if(!isset($_SERVER['HTTP_USER_AGENT']))die('No user agent specified');
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'iPhone')!==false)$udevice='iPhone';
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'iPad')!==false)$udevice='iPad';
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'Macintosh')!==false)$udevice='Mac';
else $udevice='other';
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Verbruik - Grafiek per dag</title>
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
		<link href="style.php?v=2" rel="stylesheet" type="text/css"/>
		<script type="text/javascript">function navigator_Go(url){window.location.assign(url);}</script>
		<script src="jquery-3.2.1.min.js"></script>
		<script src="jquery.stickytable.min.js"></script>
	</head>
	<body>';
echo '
		<div class="menu">
			<form action="index.php">
				<input type="submit" value="home" class="btn b4"/>
			</form>
			<form action="dag.php" method="GET">';
			foreach($selectedusers as $s)echo '
				<input type="hidden" name="'.$s.'" value="on"/>';
			echo '
				<input type="submit" value="dag" class="btn b4 btna"/>
			</form>
			<form action="maand.php" method="GET">';
			foreach($selectedusers as $s)echo '
				<input type="hidden" name="'.$s.'" value="on"/>';
			echo '
				<input type="submit" value="maand" class="btn b4"/>
			</form>
			<form action="jaar.php" method="GET">';
			foreach($selectedusers as $s)echo '
				<input type="hidden" name="'.$s.'" value="on"/>';
			echo '
				<input type="submit" value="jaar" class="btn b4"/>
			</form>
			<br>
			<input form="filter" type="date" class="btn b2 datum" name="f_startdate" value="'.$f_startdate.'" onchange="this.form.submit()"/>
			<input form="filter" type="date" class="btn b2 datum" name="f_enddate" value="'.$f_enddate.'" onchange="this.form.submit()"/>
		</div>';
		//if($user=='Guy'){echo '<pre>';print_r($_SESSION);echo '</pre>';}
$legend='
		<div class="legend first">
			<form method="GET" id="legend">legend';
foreach($users as $u=>$p){
	if($_SESSION[$u])$legend.='
				<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'" checked>
				<label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
	else $legend.='
				<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'">
				<label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
}
$legend.='
			</form>
		</div>';
if($numberusers==0){
	echo $legend;
	exit;
}
$items=array();
$qmindate=$_SESSION['f_startdate'];
$qmaxdate=$_SESSION['f_enddate'];

foreach($selectedusers as $s){
	$tabel=$s.'dag';
	if($_SESSION['euro']=='true'){$query="SELECT date, gaseuro as gas, eleceuro as elec, verbruikeuro as verbruik, zoneuro as zon, watereuro as water FROM $tabel WHERE date >= '$qmindate' AND date <= '$qmaxdate' order by date asc";$factor=100;$ex='in euro';}
	else{$query="SELECT date, gas, elec, verbruik, zon, water FROM $tabel WHERE date >= '$qmindate' AND date <= '$qmaxdate' order by date asc";$factor=1;$ex='in volume';}
	if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
	if($result->num_rows==0)break;
	while($row=$result->fetch_assoc()){
		$items[$row['date']]['datum']=$row['date'];
		$items[$row['date']][$s.'gas']=$row['gas']/$factor;
		$items[$row['date']][$s.'elec']=$row['elec']/$factor;
		$items[$row['date']][$s.'zon']=$row['zon']/$factor;
		$items[$row['date']][$s.'verbruik']=$row['verbruik']/$factor;
		$items[$row['date']][$s.'water']=$row['water']/$factor;
	}
	$result->free();
}
//echo '<pre>selectedusers=<br>';print_r($selectedusers);echo '</pre>';
//echo '<pre>items=<br>';print_r($items);echo '</pre>';
//$items=array_reverse($items);
foreach($selectedusers as $s){
	$index=0;
	$i=1;
	$totalgas=0;
	$totalelec=0;
	$totalzon=0;
	$totalverbruik=0;
	$totalwater=0;
	foreach($items as $item){
		if(isset($item[$s.'gas']))$totalgas=$totalgas+$item[$s.'gas'];
		if(isset($item[$s.'elec']))$totalelec=$totalelec+$item[$s.'elec'];
		if(isset($item[$s.'zon']))$totalzon=$totalzon+$item[$s.'zon'];
		if(isset($item[$s.'verbruik']))$totalverbruik=$totalverbruik+$item[$s.'elec']+$item[$s.'zon'];
		if(isset($item[$s.'water']))$totalwater=$totalwater+$item[$s.'water'];
		$graphgas[$index]['datum']=$item['datum'];
		if(isset($item[$s.'gas']))$graphgas[$index][$s]=number_format($item[$s.'gas'],3);else $graphgas[$index][$s]=0;
		$graphgas[$index][$s.' gemiddelde']=number_format($totalgas/$i,3);
		$graphelec[$index]['datum']=$item['datum'];
		if(isset($item[$s.'elec']))$graphelec[$index][$s]=number_format($item[$s.'elec'],3);else $graphelec[$index][$s]=0;
		$graphelec[$index][$s.' gemiddelde']=number_format($totalelec/$i,3);
		if($users[$s]['zon']==1){
			$graphzon[$index]['datum']=$item['datum'];
			if(isset($item[$s.'zon']))$graphzon[$index][$s]=number_format($item[$s.'zon'],3);else $graphzon[$index][$s]=0;
			if(isset($item[$s.'zon']))$graphzon[$index][$s.' gemiddelde']=number_format($totalzon/$i,3);else $graphzon[$index][$s.' gemiddelde']=0;
		}
		$graphverbr[$index]['datum']=$item['datum'];
		if(isset($item[$s.'verbruik']))$graphverbr[$index][$s]=number_format($item[$s.'verbruik'],3);else $graphverbr[$index][$s]=0;
		$graphverbr[$index][$s.' gemiddelde']=number_format($totalverbruik/$i,3);
		$graphwater[$index]['datum']=$item['datum'];
		if(isset($item[$s.'water']))$graphwater[$index][$s]=number_format($item[$s.'water'],3);else $graphwater[$index][$s]=0;
		$graphwater[$index][$s.' gemiddelde']=number_format($totalwater/$i,3);
		$index=$index+1;
		if(isset($item[$s.'gas']))$i=$i+1;
	}
}
//echo '<pre>graphgas=<br>';print_r($graphgas);echo '</pre>';
//echo '<pre>graphelec=<br>';print_r($graphelec);echo '</pre>';
//echo '<pre>graphzon=<br>';print_r($graphzon);echo '</pre>';
//echo '<pre>graphverbr=<br>';print_r($graphverbr);echo '</pre>';
//echo '<pre>graphwater=<br>';print_r($graphwater);echo '</pre>';

//echo date("d/m/Y", $startdate).' - '.date("d/m/Y", $maxdate);
$args=array('width'=>900,'height'=>850,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#000','chart_div'=>'graphgas','colors'=>$colors,'margins'=>array(10,10,170,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),'x_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),'text_style'=>array('fontSize'=>12,'color'=>'CCCCCC'),'x_axis_gridlines'=>8,'y_axis_gridlines'=>6,'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"},series:{0:{lineDashStyle:[0,0],pointSize:5},1:{lineDashStyle:[4,4],curveType:"function"},2:{lineDashStyle:[0,0],pointSize:5},3:{lineDashStyle:[4,4],curveType:"function"},4:{lineDashStyle:[0,0],pointSize:5},5:{lineDashStyle:[4,4],curveType:"function"},6:{lineDashStyle:[0,0],pointSize:5},7:{lineDashStyle:[4,4],curveType:"function"},8:{lineDashStyle:[0,0],pointSize:5},9:{lineDashStyle:[4,4],curveType:"function"},},');
$argsnegative=array('width'=>900,'height'=>850,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#000','chart_div'=>'graphgas','colors'=>$colors,'margins'=>array(10,10,170,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),'x_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),'text_style'=>array('fontSize'=>12,'color'=>'CCCCCC'),'x_axis_gridlines'=>8,'y_axis_gridlines'=>6,'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"},series:{0:{lineDashStyle:[0,0],pointSize:5},1:{lineDashStyle:[4,4],curveType:"function"},2:{lineDashStyle:[0,0],pointSize:5},3:{lineDashStyle:[4,4],curveType:"function"},4:{lineDashStyle:[0,0],pointSize:5},5:{lineDashStyle:[4,4],curveType:"function"},6:{lineDashStyle:[0,0],pointSize:5},7:{lineDashStyle:[4,4],curveType:"function"},8:{lineDashStyle:[0,0],pointSize:5},9:{lineDashStyle:[4,4],curveType:"function"},},');
echo '
		<div class="legend first">
			<h1>Verbruik Gas: '.$ex.'</h1>
			<form method="GET" id="filter">';
foreach($users as $u=>$p){
	if($_SESSION[$u])echo'
				<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'" checked>
				<label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
	else echo'
				<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'">
				<label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
}
echo '
			</form>
	</div>';
$args['chart_div']='graphgas';
$chart=array_to_chart($graphgas,$args);
echo $chart['script'];
echo $chart['div'];
unset($chart);

echo '
		<div class="legend">
			<h1>Verbruik Electriciteit: '.$ex.'</h1>
			<form method="GET" id="filter">';
foreach($users as $u=>$p){
	if($_SESSION[$u])echo'
				<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'" checked>
				<label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
	else echo'
				<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'">
				<label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
}
echo '
			</form>
	</div>';
$argsnegative['chart_div']='graphverbr';
$chart=array_to_chart($graphverbr,$argsnegative);
echo $chart['script'];
echo $chart['div'];
unset($chart);
if($numberzonusers>0){
	echo '
			<div class="legend">
				<h1>Electriciteitsteller: '.$ex.'</h1>
				<form method="GET" id="filter">';
	foreach($users as $u=>$p){
		if($_SESSION[$u])echo'
					<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'" checked>
					<label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
		else echo'
					<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'">
					<label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
	}
	echo '
				</form>
		</div>';
	$args['chart_div']='graphelec';
	$chart=array_to_chart($graphelec,$args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);

	echo '
			<div class="legend">
				<h1>Opbrengst zon: '.$ex.'</h1>
				<form method="GET" id="filter">';
	foreach($users as $u=>$p){
		if($users[$u]['zon']){
			if($_SESSION[$u])echo'
						<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'" checked>
						<label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
			else echo'
						<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'">
						<label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
		}
	}
	echo '
				</form>
		</div>';
	$args['chart_div']='graphzon';
	$chart=array_to_chart($graphzon,$args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);
}
echo '
		<div class="legend">
			<h1>Verbruik Water: '.$ex.'</h1>
			<form method="GET" id="filter">';
foreach($users as $u=>$p){
	if($_SESSION[$u])echo'
				<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'" checked>
				<label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
	else echo'
				<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'">
				<label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
}
echo '
			</form>
	</div>';
$args['chart_div']='graphwater';
$chart=array_to_chart($graphwater,$args);
echo $chart['script'];
echo $chart['div'];
unset($chart);

echo '<style>td{text-align:right;font-size:1.1em;}th{text-align:center;font-size:1.1em;}</style>';
if($udevice=='iPhone')echo '<style>td{text-align:right;font-size:3em;}th{text-align:center;font-size:2.1em;}</style>';
echo '<div id="table-container" class="sticky-table sticky-headers sticky-ltr-cells">
<table class="table table-striped">
	<thead>
	<tr class="sticky-row">
		<th class="sticky-cell"></th>
		<th colspan='.$numberusers.' nowrap>Verbruik Gas</th>
		<th colspan='.$numberusers.' nowrap>Verbruik Elektriciteit</th>';
if($numberzonusers>0) echo '
		<th colspan='.$numberusers.' nowrap>Eletriciteitsteller</th>
		<th colspan='.$numberzonusers.' nowrap>Opbrengst Zon</th>';
echo '		<th colspan='.$numberusers.' nowrap>Verbruik Water</th>';
if($_SESSION['euro']=='true')echo '<th colspan='.$numberusers.' nowrap>Som Verbruik</th>';
echo '
	</tr>
	<tr class="sticky-row">
		<th class="sticky-cell">Datum</th>';
foreach($selectedusers as $s)echo'
			<th>'.$s.'</th>';
foreach($selectedusers as $s)echo'
			<th>'.$s.'</th>';
if($numberzonusers>0){
	foreach($selectedusers as $s)echo'
				<th>'.$s.'</th>';
	foreach($selectedusers as $s)if($users[$s]['zon']==1)echo'
				<th>'.$s.'</th>';
}
foreach($selectedusers as $s)echo'
			<th>'.$s.'</th>';
if($_SESSION['euro']=='true'){
	foreach($selectedusers as $s)echo'
				<th>'.$s.'</th>';
}
echo '
		</tr>
	</thead>
	<tbody>';
$items=array_reverse($items);

foreach($items as $i){
	echo '
		<tr>
			<th class="sticky-cell" nowrap>'.$i['datum'].'</th>';
	foreach($selectedusers as $s)echo'
			<td>'.number_format(@$i[$s.'gas'],1,',','.').'</td>';
	foreach($selectedusers as $s)echo'
			<td>'.number_format(@$i[$s.'verbruik'],1,',','.').'</td>';
if($numberzonusers>0){
	foreach($selectedusers as $s)echo'
			<td>'.number_format(@$i[$s.'elec'],1,',','.').'</td>';
	foreach($selectedusers as $s){if($users[$s]['zon']==1)echo'
			<td>'.number_format(@$i[$s.'zon'],1,',',',').'</td>';}
}
	foreach($selectedusers as $s)echo'
			<td>'.number_format(@$i[$s.'water'],3,',','.').'</td>';
if($_SESSION['euro']=='true'){
	foreach($selectedusers as $s)echo'
			<td>'.number_format(@$i[$s.'gas']+@$i[$s.'verbruik']+@$i[$s.'water'],2,',','.').'</td>';
}
echo '
		</tr>';
}
echo '
	</tbody>
</table>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
</div>
</body></html>';
?>