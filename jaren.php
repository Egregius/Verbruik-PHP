<?php
require_once'/var/www/verbruik.egregius.be/secure/settings.php';
require_once'/var/www/verbruik.egregius.be/secure/chart.php';
session_start();
$time=time();
$colors=array();
$selectedusers=array();
$legend='<div class="legend"><form method="GET" id="filter">';
foreach($users as $u=>$p){
	if(isset($_GET[$u]))$_SESSION[$u]=true;else $_SESSION[$u]=false;
	if($_SESSION[$u])array_push($selectedusers,$u);
	if($_SESSION[$u])$legend.='&nbsp;<input type="submit" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="hidden '.$u.'" value="On" checked><label for="'.$u.'" class="btna">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
	else $legend.='&nbsp;<input type="submit" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="hidden '.$u.'" value="On"><label for="'.$u.'">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
}
$legend.='</form></div>';
$legendzon='<div class="legend"><form method="GET" id="filter">';
foreach($users as $u=>$p){
	if(isset($_GET[$u]))$_SESSION[$u]=true;else $_SESSION[$u]=false;
	if($users[$u]['zon']==1){
		if($_SESSION[$u])$legendzon.='&nbsp;<input type="checkbox" name="'.$u.'" id="'.$u.'" onChange="this.form.submit()" class="'.$u.'" checked><label for="'.$u.'" class="btna">'.$u.' '.strftime("%e/%m",strtotime($users[$u]['maxdate'])).'</label>';
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
if(isset($_REQUEST['f_maand']))$_SESSION['f_maand']=$_REQUEST['f_maand'];
if(!isset($_SESSION['f_maand']))$_SESSION['f_maand']=12;
if(isset($_REQUEST['f_jaren']))$_SESSION['f_jaren']=$_REQUEST['f_jaren'];
if(!isset($_SESSION['f_jaren']))$_SESSION['f_jaren']=3;
if(isset($_REQUEST['euro']))$_SESSION['euro']=$_REQUEST['euro'];
if(!isset($_SESSION['euro']))$_SESSION['euro']='false';

if(!isset($_SERVER['HTTP_USER_AGENT']))die('No user agent specified');
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'iPhone')!==false)$udevice='iPhone';
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'iPad')!==false)$udevice='iPad';
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'Macintosh')!==false)$udevice='Mac';
else $udevice='other';
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Verbruik - Grafiek per maand</title>
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
			<form action="jaren.php" method="GET">';
			foreach($selectedusers as $s)echo '<input type="hidden" name="'.$s.'" value="on"/>';
			echo '<select name="f_jaren" class="btn b4 btna" onchange="this.form.submit()"/>';
			$months=array(2,3,4,5,6,7,8,9,10);
			foreach($months as $k){
				if($k==$_SESSION['f_jaren'])echo '<option value="'.$k.'" selected>'.$k.' jaar</option>';
				else echo '<option value="'.$k.'">'.$k.' jaar</option>';
			}
			echo '
			</select>
			</form>
			<form action="jaar.php" method="GET">';
			foreach($selectedusers as $s)echo '<input type="hidden" name="'.$s.'" value="on"/>';
			echo '<input type="submit" value="jaar" class="btn b4"/></form><br>
		</div>';
//echo '<pre>';print_r($_SESSION);echo '</pre>';
if($numberusers==0){
	echo $legend;
	exit;
}
$time=time();
$maxdate=$time;
$maxdatum=$maxdate;
$x=$_SESSION['f_maand'];
$startdate=strtotime(" -$x month",$maxdate);
$vanafjaar=strftime("%Y",$time)+1-$_SESSION['f_jaren'];
foreach($selectedusers as $s){
	$selecteduser=$s;
	$tabel=$s.'dag';
	$items=array();
	for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++){
		$months=array('01'=>'Januari','02'=>'Februari','03'=>'Maart','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Augustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'December');
		foreach($months as $m=>$ms){
			$items[$j][$m]['gas']=0;
			$items[$j][$m]['elec']=0;
			$items[$j][$m]['verbruik']=0;
			$items[$j][$m]['zon']=0;
			$items[$j][$m]['water']=0;
			$graphgas[$m]['Maand']=$ms;
			$graphgas[$m][$j]=0;
			$graphgas[$m][$j.' gemiddelde']=0;
			$graphelec[$m]['Maand']=$ms;
			$graphelec[$m][$j]=0;
			$graphelec[$m][$j.' gemiddelde']=0;
			$graphzon[$m]['Maand']=$ms;
			$graphzon[$m][$j]=0;
			$graphzon[$m][$j.' gemiddelde']=0;
			$graphverbr[$m]['Maand']=$ms;
			$graphverbr[$m][$j]=0;
			$graphverbr[$m][$j.' gemiddelde']=0;
			$graphwater[$m]['Maand']=$ms;
			$graphwater[$m][$j]=0;
			$graphwater[$m][$j.' gemiddelde']=0;
		}
	}
	if($_SESSION['euro']=='true'){$query="SELECT left(date,4) as jaar,right(left(date,7),2) as maand, avg(gaseuro) as gas, avg(eleceuro) as elec, avg(verbruikeuro) as verbruik, avg(zoneuro) as zon, avg(watereuro) as water FROM $tabel group by left(date,4),right(left(date,7),2) order by left(date,4),right(left(date,7),2)";$factor=100;$ex='in euro';}
	else{$query="SELECT left(date,4) as jaar,right(left(date,7),2) as maand, avg(gas) as gas, avg(elec) as elec, avg(verbruik) as verbruik, avg(zon) as zon, avg(water) as water FROM $tabel group by left(date,4),right(left(date,7),2) order by left(date,4),right(left(date,7),2)";$factor=1;$ex='in volume';}
	if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
	while($row=$result->fetch_assoc()){
		if($row['jaar']>=$vanafjaar){
			$items[$row['jaar']][$row['maand']]['gas']=$row['gas']/$factor;
			$items[$row['jaar']][$row['maand']]['elec']=$row['elec']/$factor;
			$items[$row['jaar']][$row['maand']]['verbruik']=$row['verbruik']/$factor;
			$items[$row['jaar']][$row['maand']]['zon']=$row['zon']/$factor;
			$items[$row['jaar']][$row['maand']]['water']=$row['water']/$factor;
		}
	}
	$result->free();
}
//echo '<pre>selectedusers<br>';print_r($selectedusers);echo '</pre>';
//echo '<pre>items<br>';print_r($items);echo '</pre><pre>';
$index=0;
foreach($items as $jaar=>$maanden){
	$i=0;
	$totalgas=0;
	$totalelec=0;
	$totalzon=0;
	$totalverbruik=0;
	$totalwater=0;
	foreach($maanden as $maand=>$d){
		//echo $jaar.$maand.'<br>';
		if(@$d['gas']>0||@$d['elec']>0)$i++;
		if($i>0){
			$totalgas=$totalgas+@$d['gas'];
			$totalelec=$totalelec+@$d['elec'];
			$totalzon=$totalzon+@$d['zon'];
			$totalverbruik=$totalverbruik+@$d['verbruik'];
			$totalwater=$totalwater+@$d['water'];
			//$graphgas[$maand]['Maand']='1-'.$maand.'-'.$jaar;
			$graphgas[$maand][$jaar]=$d['gas'];
			$graphgas[$maand][$jaar.' gemiddelde']=number_format(@($totalgas/$i),3);
			//$graphelec[$maand]['Maand']='1-'.$maand.'-'.$jaar;
			$graphelec[$maand][$jaar]=$d['elec'];
			$graphelec[$maand][$jaar.' gemiddelde']=number_format(@($totalelec/$i),3);
			//$graphzon[$maand]['Maand']='1-'.$maand.'-'.$jaar;
			$graphzon[$maand][$jaar]=$d['zon'];
			$graphzon[$maand][$jaar.' gemiddelde']=number_format(@($totalzon/$i),3);
			//$graphverbr[$maand]['Maand']='1-'.$maand.'-'.$jaar;
			$graphverbr[$maand][$jaar]=$d['verbruik'];
			$graphverbr[$maand][$jaar.' gemiddelde']=number_format(@($totalverbruik/$i),3);
			//$graphwater[$maand]['Maand']='1-'.$maand.'-'.$jaar;
			$graphwater[$maand][$jaar]=$d['water'];
			$graphwater[$maand][$jaar.' gemiddelde']=number_format(@($totalwater/$i),3);
		}
	}
}
//echo '<pre>graphgas<br>';print_r($graphgas);echo '</pre>';
//echo '<pre>graphelec<br>';print_r($graphelec);echo '</pre>';
//echo '<pre>graphzon<br>';print_r($graphzon);echo '</pre>';
//echo '<pre>graphverbr<br>';print_r($graphverbr);echo '</pre>';
//echo '<pre>graphwater<br>';print_r($graphwater);echo '</pre>';

//echo date("d/m/Y", $startdate).' - '.date("d/m/Y", $maxdate);
$colors=array(	'#0082c8','#0082c8',
				'#f58231','#f58231',
				'#911eb4','#911eb4',
				'#46f0f0','#46f0f0',
				'#f032e6','#f032e6',
				'#d2f53c','#d2f53c',
				'#fabebe','#fabebe',
				'#008080','#008080',
				'#e6beff','#e6beff',
				'#fffac8','#fffac8',
				'#e6194b','#e6194b',
				'#3cb44b','#3cb44b',
				'#ffe119','#ffe119'
			);
$slice=-$_SESSION['f_jaren']*2;
$colors=array_slice($colors,$slice);
echo '<div style="width:920px"><a name="gas"></a><h1>Verbruik Gas (tot '.strftime("%e/%m",$maxgraphdate).') '.$ex.':</h1>'.$legend.'</div>';
$args=array('width'=>900,
		'height'=>850,
		'hide_legend'=>false,
		'responsive'=>false,
		'background_color'=>'#000',
		'colors'=>$colors,
		'margins'=>array(80,0,170,50),
		'y_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),
		'x_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),
		'text_style'=>array('fontSize'=>12,'color'=>'CCCCCC'),
		'x_axis_gridlines'=>8,'y_axis_gridlines'=>6,
		'raw_options'=>'legend:{position: "top",
						maxLines:5,
						textStyle: {color: "white", fontSize: 16}},
						lineWidth:3,
						crosshair:{trigger:"both"},
						series:{
							0:{lineDashStyle:[0,0],pointSize:4},
							1:{lineDashStyle:[4,4],pointSize:0},
							2:{lineDashStyle:[0,0],pointSize:4},
							3:{lineDashStyle:[4,4],pointSize:0},
							4:{lineDashStyle:[0,0],pointSize:4},
							5:{lineDashStyle:[4,4],pointSize:0},
							6:{lineDashStyle:[0,0],pointSize:4},
							7:{lineDashStyle:[4,4],pointSize:0},
							8:{lineDashStyle:[0,0],pointSize:4},
							9:{lineDashStyle:[4,4],pointSize:0},
							10:{lineDashStyle:[0,0],pointSize:4},
							11:{lineDashStyle:[4,4],pointSize:0},
							12:{lineDashStyle:[0,0],pointSize:4},
							13:{lineDashStyle:[4,4],pointSize:0},
							14:{lineDashStyle:[0,0],pointSize:4},
							15:{lineDashStyle:[4,4],pointSize:0},
							16:{lineDashStyle:[0,0],pointSize:4},
							17:{lineDashStyle:[4,4],pointSize:0},
							18:{lineDashStyle:[0,0],pointSize:4},
							19:{lineDashStyle:[4,4],pointSize:0}
						},vAxis: {
						  viewWindowMode:\'explicit\',
						  viewWindow:{
							min:0
						  }
						}
		');
$argsnegative=array('width'=>900,
		'height'=>850,
		'hide_legend'=>false,
		'responsive'=>false,
		'background_color'=>'#000',
		'colors'=>$colors,
		'margins'=>array(80,0,170,50),
		'y_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),
		'x_axis_text_style'=>array('fontSize'=>18,'color'=>'CCCCCC'),
		'text_style'=>array('fontSize'=>12,'color'=>'CCCCCC'),
		'x_axis_gridlines'=>8,'y_axis_gridlines'=>6,
		'raw_options'=>'legend:{position: "top",
						maxLines:5,
						textStyle: {color: "white", fontSize: 16}},
						lineWidth:3,
						crosshair:{trigger:"both"},
						series:{
							0:{lineDashStyle:[0,0],pointSize:4},
							1:{lineDashStyle:[4,4],pointSize:0},
							2:{lineDashStyle:[0,0],pointSize:4},
							3:{lineDashStyle:[4,4],pointSize:0},
							4:{lineDashStyle:[0,0],pointSize:4},
							5:{lineDashStyle:[4,4],pointSize:0},
							6:{lineDashStyle:[0,0],pointSize:4},
							7:{lineDashStyle:[4,4],pointSize:0},
							8:{lineDashStyle:[0,0],pointSize:4},
							9:{lineDashStyle:[4,4],pointSize:0},
							10:{lineDashStyle:[0,0],pointSize:4},
							11:{lineDashStyle:[4,4],pointSize:0},
							12:{lineDashStyle:[0,0],pointSize:4},
							13:{lineDashStyle:[4,4],pointSize:0},
							14:{lineDashStyle:[0,0],pointSize:4},
							15:{lineDashStyle:[4,4],pointSize:0},
							16:{lineDashStyle:[0,0],pointSize:4},
							17:{lineDashStyle:[4,4],pointSize:0},
							18:{lineDashStyle:[0,0],pointSize:4},
							19:{lineDashStyle:[4,4],pointSize:0}
						},
		');
$args['chart_div']='graphgas';
$chart=array_to_chart($graphgas,$args);
echo $chart['script'];
echo $chart['div'];
unset($chart);

echo '<div style="width:920px"><a name="elec"></a><h1>Verbruik Elektriciteit (tot '.strftime("%e/%m",$maxgraphdate).') '.$ex.':</h1>'.$legend.'</div>';
$args['chart_div']='graphverbr';
$chart=array_to_chart($graphverbr,$args);
echo $chart['script'];
echo $chart['div'];
unset($chart);
if($numberzonusers>0){
	echo '<div style="width:920px"><a name="teller"></a><h1>Elektriciteitsteller (tot '.strftime("%e/%m",$maxgraphdate).') '.$ex.':</h1>'.$legend.'</div>';
	$argsnegative['chart_div']='graphelec';
	$chart=array_to_chart($graphelec,$argsnegative);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);

	echo '<div style="width:920px"><a name="zon"></a><h1>Opbrengst zon (tot '.strftime("%e/%m",$maxgraphdate).') '.$ex.':</h1>'.$legendzon.'</div>';
	$args['chart_div']='graphzon';
	$chart=array_to_chart($graphzon,$args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);
}
echo '<div style="width:920px"><a name="water"></a><h1>Verbruik Water (tot '.strftime("%e/%m",$maxgraphdate).') '.$ex.':</h1>'.$legend.'</div>';
$args['chart_div']='graphwater';
$chart=array_to_chart($graphwater,$args);
echo $chart['script'];
echo $chart['div'];
unset($chart);

echo '<style>td{text-align:right;font-size:1.1em;}th{text-align:center;font-size:1.1em;}</style>';
if($udevice=='iPhone')echo '<style>td{text-align:right;font-size:3em;}th{text-align:center;font-size:2.1em;}</style>';
echo '<div id="table-container" class="sticky-table sticky-headers sticky-ltr-cells">';
//echo '<pre>';print_r($items);echo '</pre>';

echo '<h1>Tabel per maand</h1><table class="table table-striped">
	<thead>
	<tr class="sticky-row">
		<th class="sticky-cell"></th>
		<th colspan='.$_SESSION['f_jaren'] .' nowrap>Verbruik Gas</th>
		<th colspan='.$_SESSION['f_jaren'] .' nowrap>Verbruik Elektriciteit</th>';
if($numberzonusers>0)echo '
		<th colspan='.$_SESSION['f_jaren'] .' nowrap>Eletriciteitsteller</th>
		<th colspan='.$_SESSION['f_jaren'] .' nowrap>Opbrengst Zon</th>';
		echo '
		<th colspan='.$_SESSION['f_jaren'] .' nowrap>Verbruik Water</th>';
if($_SESSION['euro']=='true')echo '<th colspan='.$_SESSION['f_jaren'] .' nowrap>Som Verbruik</th>';
echo '	</tr>
	<tr class="sticky-row">
		<th class="sticky-cell">Maand</th>';
for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo'<th>'.$j.'</th>';
for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo'<th>'.$j.'</th>';
for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo'<th>'.$j.'</th>';
if($numberzonusers>0){
	for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo'<th>'.$j.'</th>';
	for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo'<th>'.$j.'</th>';
}
if($_SESSION['euro']=='true')for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo'<th>'.$j.'</th>';
echo '</tr></thead><tbody>';
//$items=array_reverse($items);
foreach($months as $m=>$ml){
	echo '<tr><th class="sticky-cell" nowrap>'.$ml.'</th>';
	for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo '<td>'.@number_format($graphgas[$m][$j],1,',','.').'</td>';
	for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo '<td>'.@number_format($graphverbr[$m][$j],1,',','.').'</td>';
	if($numberzonusers>0){
		for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo '<td>'.@number_format($graphelec[$m][$j],1,',','.').'</td>';
		for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo '<td>'.@number_format($graphzon[$m][$j],1,',','.').'</td>';
	}
	for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo '<td>'.@number_format($graphwater[$m][$j],1,',','.').'</td>';
	if($_SESSION['euro']=='true')for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo '<td>'.@number_format($graphgas[$m][$j]+$graphverbr[$m][$j]+$graphwater[$m][$j],1,',','.').'</td>';
	echo '</tr>';
}
echo '</tbody></table>
	</div>
	<div id="table-container" class="sticky-table sticky-headers sticky-ltr-cells">
	<h1>Tabel lopend gemiddelde per maand</h1><table class="table table-striped">
	<thead>
	<tr class="sticky-row">
		<th class="sticky-cell"></th>
		<th colspan='.$_SESSION['f_jaren'] .' nowrap>Verbruik Gas</th>
		<th colspan='.$_SESSION['f_jaren'] .' nowrap>Verbruik Elektriciteit</th>';
	if($numberzonusers>0) echo '
		<th colspan='.$_SESSION['f_jaren'] .' nowrap>Eletriciteitsteller</th>
		<th colspan='.$_SESSION['f_jaren'] .' nowrap>Opbrengst Zon</th>';
	echo '<th colspan='.$_SESSION['f_jaren'] .' nowrap>Verbruik Water</th>';
if($_SESSION['euro']=='true')echo '<th colspan='.$_SESSION['f_jaren'] .' nowrap>Som Verbruik</th>';
echo '	</tr>
	<tr class="sticky-row">
		<th class="sticky-cell">Maand</th>';
for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo'<th>'.$j.'</th>';
for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo'<th>'.$j.'</th>';
for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo'<th>'.$j.'</th>';
if($numberzonusers>0){
	for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo'<th>'.$j.'</th>';
	for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo'<th>'.$j.'</th>';
}
if($_SESSION['euro']=='true')for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo'<th>'.$j.'</th>';
echo '</tr></thead><tbody>';
//$items=array_reverse($items);
foreach($months as $m=>$ml){
	echo '<tr><th class="sticky-cell" nowrap>'.$ml.'</th>';
	for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo '<td>'.number_format($graphgas[$m][$j.' gemiddelde'],1,',','.').'</td>';
	for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo '<td>'.@number_format($graphverbr[$m][$j.' gemiddelde'],1,',','.').'</td>';
	if($numberzonusers>0){
		for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo '<td>'.@number_format($graphelec[$m][$j.' gemiddelde'],1,',','.').'</td>';
		for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo '<td>'.@number_format($graphzon[$m][$j.' gemiddelde'],1,',','.').'</td>';
	}
	for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo '<td>'.@number_format($graphwater[$m][$j.' gemiddelde'],1,',','.').'</td>';
	if($_SESSION['euro']=='true')for($j=$vanafjaar;$j<=strftime("%Y",$time);$j++)echo '<td>'.@number_format($graphgas[$m][$j.' gemiddelde']+$graphverbr[$m][$j.' gemiddelde']+$graphwater[$m][$j.' gemiddelde'],2,',','.').'</td>';
	echo '</tr>';
}
echo '</tbody></table><br><br><br><br><br><br><br><br></div>
</body></html>';
?>