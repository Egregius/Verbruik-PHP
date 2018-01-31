<?php
require_once'/var/www/verbruik.egregius.be/secure/settings.php';
require_once'/var/www/verbruik.egregius.be/secure/chart.php';
session_start();
$time=time();
$colors=array();
$selectedusers=array();
$legend='<div class="legend"><form method="GET">';
foreach($users as $u=>$p){
	if(isset($_GET[$u]))$_SESSION[$u]=true;else $_SESSION[$u]=false;
	if($_SESSION[$u])array_push($colors,${$u});
	if($_SESSION[$u])array_push($colors,${$u});
	if($_SESSION[$u])array_push($selectedusers,$u);
	if($_SESSION[$u])$legend.='&nbsp;<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'" checked><label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
	else $legend.='&nbsp;<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'"><label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
}
$legend.='</form></div>';
$legendzon='<div class="legend"><form method="GET">';
foreach($users as $u=>$p){
	if(isset($_GET[$u]))$_SESSION[$u]=true;else $_SESSION[$u]=false;
	if($users[$u]['zon']==1){
		if($_SESSION[$u])$legendzon.='&nbsp;<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'" checked><label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
		else $legendzon.='&nbsp;<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'"><label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
	}
}
$legendzon.='</form></div>';
$numberusers=0;
$numberzonusers=0;
$maxgraphdate=time();
foreach($selectedusers as $u){
	$numberusers=$numberusers+1;
	if($users[$u]['zon']==1)$numberzonusers=$numberzonusers+1;
	if(strtotime($users[$u]['maxdate'])<$maxgraphdate)$maxgraphdate=strtotime($users[$u]['maxdate']);
}
if(isset($_REQUEST['f_jaar']))$_SESSION['f_jaar']=$_REQUEST['f_jaar'];
if(isset($_REQUEST['euro']))$_SESSION['euro']=$_REQUEST['euro'];
if(!isset($_SESSION['f_jaar']))$_SESSION['f_jaar']=10;
if(!isset($_SESSION['euro']))$_SESSION['euro']='false';

if(!isset($_SERVER['HTTP_USER_AGENT']))die('No user agent specified');
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'iPhone')!==false)$udevice='iPhone';
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'iPad')!==false)$udevice='iPad';
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'Macintosh')!==false)$udevice='Mac';
else $udevice='other';
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Verbruik - Grafiek per jaar</title>
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
		<div style="width:920px">
			<form action="index.php"><input type="submit" value="home" class="btn b4"/></form>
			<form action="dag.php" method="GET">';
			foreach($selectedusers as $s)echo '<input type="hidden" name="'.$s.'" value="on"/>';
			echo '<input type="submit" value="dag" class="btn b4"/></form>
			<form action="maand.php" method="GET">';
			foreach($selectedusers as $s)echo '<input type="hidden" name="'.$s.'" value="on"/>';
			echo '<input type="submit" value="maand" class="btn b4"/>
			</form>
			<form action="jaar.php" method="GET">';
			foreach($selectedusers as $s)echo '<input type="hidden" name="'.$s.'" value="on"/>';
			echo '<select name="f_jaar" class="btn b4 btna" onchange="this.form.submit()"/>';
			$months=array(2,3,4,5,6,7,8,9,10);
			foreach($months as $k){
				if($k==$_SESSION['f_jaar'])echo '<option value="'.$k.'" selected>'.$k.' jaar</option>';
				else echo '<option value="'.$k.'">'.$k.' jaar</option>';
			}
			echo '
			</select>
			</form>
			<br>
		</div>';
//echo '<pre>';print_r($_SESSION);echo '</pre>';

if($numberusers==0){
	echo $legend;
	exit;
}
$maxdate=time();
$maxdatum=$maxdate;
$x=$_SESSION['f_jaar'];
$startdate=strtotime(" -$x year",$maxdate);
$items=array();
foreach($selectedusers as $s){
	$max=strtotime($users[$s]['maxdate']);
	${'last'.$s}=$max;
	if($max<$maxdate)$maxdate=$max;
	$tabel=$s.'dag';
	for($k=0;$k<=9;$k++){
		$mindate=strtotime(' -1 year',$maxdate);
		if($mindate<$startdate)$mindate=$startdate;
		$qmindate=date("Y-m-d", $mindate);
		$qmaxdate=date("Y-m-d", $maxdate);
		if($_SESSION['euro']=='true'){$query="SELECT date, avg(gaseuro) as gas, avg(eleceuro) as elec, avg(verbruikeuro) as verbruik, avg(zoneuro) as zon, avg(watereuro) as water FROM $tabel WHERE date >= '$qmindate' AND date <= '$qmaxdate'";$factor=100;$ex='in euro';}
		else{$query="SELECT date, avg(gas) as gas, avg(elec) as elec, avg(verbruik) as verbruik, avg(zon) as zon, avg(water) as water FROM $tabel WHERE date >= '$qmindate' AND date <= '$qmaxdate'";$factor=1;$ex='in volume';}
		if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
		if($result->num_rows==0)break;
		while($row=$result->fetch_assoc()){
			$items[$k][$s.'gas']=$row['gas']/$factor;
			$items[$k][$s.'elec']=$row['elec']/$factor;
			$items[$k][$s.'zon']=$row['zon']/$factor;
			$items[$k][$s.'verbruik']=$row['verbruik']/$factor;
			$items[$k][$s.'water']=$row['water']/$factor;
			$items[$k]['mindate']=strftime("%e/%m/%Y",strtotime($qmindate));
			$items[$k]['maxdate']=strftime("%e/%m/%Y",strtotime($qmaxdate));
		}
		$result->free();
		if($mindate<=$startdate)break;
		$maxdate=$mindate;
		$a=mysqli_fetch_assoc(mysqli_query($db, "SELECT count(date) as aantaldagen FROM $tabel WHERE date >= '$qmindate' AND date <= '$qmaxdate' limit 1"));
		if($a['aantaldagen']<365)break;
	}
	$maxdate=$maxdatum;
}
//echo '<pre>colors<br>';print_r($colors);echo '</pre>';
//echo '<pre>selectedusers<br>';print_r($selectedusers);echo '</pre>';
//echo '<pre>items<br>';print_r($items);echo '</pre>';
$items=array_reverse($items);
foreach($selectedusers as $s){
	$index=0;
	$i=1;
	$totalgas=0;
	$totalelec=0;
	$totalzon=0;
	$totalverbruik=0;
	$totalwater=0;
	foreach($items as $item){
		$totalgas=$totalgas+@$item[$s.'gas'];
		$totalelec=$totalelec+@$item[$s.'elec'];
		$totalzon=$totalzon+@$item[$s.'zon'];
		$totalverbruik=$totalverbruik+@$item[$s.'verbruik'];
		$totalwater=$totalwater+@$item[$s.'water'];
		$graphgas[$index]['year']=$item['maxdate'];
		if(isset($item[$s.'gas']))$graphgas[$index][$s]=number_format($item[$s.'gas'],1,'.',',');else $graphgas[$index][$s]=0;
		if(isset($item[$s.'gas']))$graphgas[$index][$s.' gemiddelde']=number_format($totalgas/$i,1,'.',',');else $graphgas[$index][$s.' gemiddelde']=0;
		$graphelec[$index]['year']=$item['maxdate'];
		if(isset($item[$s.'elec']))$graphelec[$index][$s]=number_format($item[$s.'elec'],1,'.',',');else $graphelec[$index][$s]=0;
		if(isset($item[$s.'elec']))$graphelec[$index][$s.' gemiddelde']=number_format($totalelec/$i,1,'.',',');else $graphelec[$index][$s.' gemiddelde']=0;
		if($users[$s]['zon']==1){
			$graphzon[$index]['year']=$item['maxdate'];
			if(isset($item[$s.'zon']))$graphzon[$index][$s]=number_format($item[$s.'zon'],1,'.',',');else $graphzon[$index][$s]=0;
			if(isset($item[$s.'zon']))$graphzon[$index][$s.' gemiddelde']=number_format($totalzon/$i,1,'.',',');else $graphzon[$index][$s.' gemiddelde']=0;
		}
		$graphverbr[$index]['year']=$item['maxdate'];
		if(isset($item[$s.'verbruik']))$graphverbr[$index][$s]=number_format($item[$s.'verbruik'],1,'.',',');else $graphverbr[$index][$s]=0;
		if(isset($item[$s.'verbruik']))$graphverbr[$index][$s.' gemiddelde']=number_format($totalverbruik/$i,1,'.',',');else $graphverbr[$index][$s.' gemiddelde']=0;
		$graphwater[$index]['year']=$item['maxdate'];
		if(isset($item[$s.'water']))$graphwater[$index][$s]=number_format($item[$s.'water'],3,'.',',');else $graphwater[$index][$s]=0;
		if(isset($item[$s.'water']))$graphwater[$index][$s.' gemiddelde']=number_format($totalwater/$i,3,'.',',');else $graphwater[$index][$s.' gemiddelde']=0;
		$index=$index+1;
		if($totalgas>0)$i=$i+1;
	}
}
//echo '<pre>graphgas<br>';print_r($graphgas);echo '</pre>';
//echo '<pre>graphelec<br>';print_r($graphelec);echo '</pre>';
//echo '<pre>graphzon<br>';print_r($graphzon);echo '</pre>';
//echo '<pre>graphverbr<br>';print_r($graphverbr);echo '</pre>';
//echo '<pre>graphwater<br>';print_r($graphwater);echo '</pre>';

//echo date("d/m/Y", $startdate).' - '.date("d/m/Y", $maxdatum);


echo '<div style="width:920px"><a name="gas"></a><h1>Verbruik Gas (tot '.strftime("%e/%m",$maxgraphdate).') '.$ex.':</h1>'.$legend.'</div>';
$args=array('width'=>900,'height'=>850,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#000','chart_div'=>'graphgas','colors'=>$colors,'margins'=>array(10,10,170,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),'x_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),'text_style'=>array('fontSize'=>12,'color'=>'CCCCCC'),'x_axis_gridlines'=>8,'y_axis_gridlines'=>6,'raw_options'=>'lineWidth:4,crosshair:{trigger:"both"},series:{0:{lineDashStyle:[0,0],pointSize:7},1:{lineDashStyle:[4,4],curveType:"function"},2:{lineDashStyle:[0,0],pointSize:7},3:{lineDashStyle:[4,4],curveType:"function"},4:{lineDashStyle:[0,0],pointSize:7},5:{lineDashStyle:[4,4],curveType:"function"},6:{lineDashStyle:[0,0],pointSize:7},7:{lineDashStyle:[4,4],curveType:"function"},8:{lineDashStyle:[0,0],pointSize:7},9:{lineDashStyle:[4,4],curveType:"function"},},');
$argspositive=array('width'=>900,'height'=>850,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#000','chart_div'=>'graphgas','colors'=>$colors,'margins'=>array(10,10,170,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),'x_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),'text_style'=>array('fontSize'=>12,'color'=>'CCCCCC'),'x_axis_gridlines'=>8,'y_axis_gridlines'=>6,'raw_options'=>'lineWidth:4,crosshair:{trigger:"both"},series:{0:{lineDashStyle:[0,0],pointSize:7},1:{lineDashStyle:[4,4],curveType:"function"},2:{lineDashStyle:[0,0],pointSize:7},3:{lineDashStyle:[4,4],curveType:"function"},4:{lineDashStyle:[0,0],pointSize:7},5:{lineDashStyle:[4,4],curveType:"function"},6:{lineDashStyle:[0,0],pointSize:7},7:{lineDashStyle:[4,4],curveType:"function"},8:{lineDashStyle:[0,0],pointSize:7},9:{lineDashStyle:[4,4],curveType:"function"},},vAxis: {
						  viewWindowMode:\'explicit\',
						  viewWindow:{
							min:0
						  }
						}');
$argspositive['chart_div']='graphgas';
$chart=array_to_chart($graphgas,$argspositive);
echo $chart['script'];
echo $chart['div'];
unset($chart);

echo '<div style="width:920px"><a name="elec"></a><h1>Verbruik Elektriciteit (tot '.strftime("%e/%m",$maxgraphdate).') '.$ex.':</h1>'.$legend.'</div>';
$argspositive['chart_div']='graphverbr';
$chart=array_to_chart($graphverbr,$argspositive);
echo $chart['script'];
echo $chart['div'];
unset($chart);
if($numberzonusers>0){
	echo '<div style="width:920px"><a name="teller"></a><h1>Elektriciteitsteller (tot '.strftime("%e/%m",$maxgraphdate).') '.$ex.':</h1>'.$legend.'</div>';
	$args['chart_div']='graphelec';
	$chart=array_to_chart($graphelec,$args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);

	echo '<div style="width:920px"><a name="zon"></a><h1>Opbrengst zon (tot '.strftime("%e/%m",$maxgraphdate).') '.$ex.':</h1>'.$legendzon.'</div>';
	$argspositive['chart_div']='graphzon';
	$chart=array_to_chart($graphzon,$argspositive);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);
}
echo '<div style="width:920px"><a name="water"></a><h1>Verbruik Water (tot '.strftime("%e/%m",$maxgraphdate).') '.$ex.':</h1>'.$legend.'</div>';
$argspositive['chart_div']='graphwater';
$chart=array_to_chart($graphwater,$argspositive);
echo $chart['script'];
echo $chart['div'];
unset($chart);


echo '<style>td{text-align:right;font-size:1.1em;}th{text-align:center;font-size:1.1em;}</style>';
if($udevice=='iPhone')echo '<style>td{text-align:right;font-size:3em;}th{text-align:center;font-size:2.1em;}</style>';
echo '<div id="table-container" class="sticky-table sticky-headers sticky-ltr-cells">
<table class="table table-striped">
	<thead>
	<tr class="sticky-row">
		<th colspan=2 class="sticky-cell">Periode</th>
		<th colspan='.$numberusers.' nowrap>Verbruik Gas</th>
		<th colspan='.$numberusers.' nowrap>Verbruik Elektriciteit</th>';
if($numberzonusers>0) echo '<th colspan='.$numberusers.' nowrap>Eletriciteitsteller</th>
		<th colspan='.$numberzonusers.' nowrap>Opbrengst Zon</th>';
echo '		<th colspan='.$numberusers.' nowrap>Verbruik Water</th>';
if($_SESSION['euro']=='true')echo '<th colspan='.$numberusers.' nowrap>Som Verbruik</th>';
echo '
	</tr>
	<tr class="sticky-row">
		<th class="sticky-cell">Van</th>
		<th class="sticky-cell">Tot</th>';
foreach($selectedusers as $s)echo'<th>'.$s.'</th>';
foreach($selectedusers as $s)echo'<th>'.$s.'</th>';
if($numberzonusers>0){
	foreach($selectedusers as $s)echo'<th>'.$s.'</th>';
	foreach($selectedusers as $s)if($users[$s]['zon']==1)echo'<th>'.$s.'</th>';
}
foreach($selectedusers as $s)echo'<th>'.$s.'</th>';
if($_SESSION['euro']=='true')foreach($selectedusers as $s)echo'<th>'.$s.'</th>';
echo '</tr></thead><tbody>';
$items=array_reverse($items);

foreach($items as $i){
	echo '<tr>
		<th class="sticky-cell" nowrap>'.$i['mindate'].'</th>
		<th class="sticky-cell" nowrap>'.$i['maxdate'].'</th>';
	foreach($selectedusers as $s)echo'<td>'.number_format(@$i[$s.'gas'],1,',','.').'</td>';
	foreach($selectedusers as $s)echo'<td>'.number_format(@$i[$s.'verbruik'],1,',','.').'</td>';
	if($numberzonusers>0){
		foreach($selectedusers as $s)echo'<td>'.number_format(@$i[$s.'elec'],1,',','.').'</td>';
		foreach($selectedusers as $s){if($users[$s]['zon']==1)echo'<td>'.number_format(@$i[$s.'zon'],1,',','.').'</td>';}
	}
	foreach($selectedusers as $s)echo'<td>'.number_format(@$i[$s.'water'],1,',','.').'</td>';
	if($_SESSION['euro']=='true')foreach($selectedusers as $s)echo'<td>'.number_format(@$i[$s.'gas']+@$i[$s.'verbruik']+@$i[$s.'water'],2,',','.').'</td>';
	echo '</tr>';
}
echo '</tbody></table><br><br><br><br><br><br><br><br></div>
</body></html>';
?>