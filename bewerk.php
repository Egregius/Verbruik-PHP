<?php
require_once'/var/www/verbruik.egregius.be/secure/settings.php';
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
    <link rel="apple-touch-startup-image" href="images/domoticzphp450.png"/>
    <link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
    <meta name="msapplication-TileColor" content="#000000">
    <meta name="msapplication-TileImage" content="images/domoticzphp48.png">
    <meta name="msapplication-config" content="browserconfig.xml">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifests/budget.json">
    <meta name="theme-color" content="#000000">
    <link href="/styles/budget.php" rel="stylesheet" type="text/css"/>
    <script type="text/javascript">
      function navigator_Go(url){window.location.assign(url);}
      $(document).ready(function() {
        $(window).keydown(function(event){
          if(event.keyCode == 13) {
            event.preventDefault();
            return false;
          }
        });
      });
    </script>
  </head>
  <body>';

//session_start();
//if(isset($_REQUEST['clear']))unset($_REQUEST,$_SESSION);
//if(isset($_REQUEST['f_startdate']))$_SESSION['f_startdate']=$_REQUEST['f_startdate'];else $_SESSION['f_startdate']=date("Y-m",time()).'-01';
//if(isset($_REQUEST['f_enddate']))$_SESSION['f_enddate']=$_REQUEST['f_enddate'];else $_SESSION['f_enddate']=date("Y-m",time()).'-'.date("t",time());
//echo '<pre>';print_r($_REQUEST);print_r($_SESSION);echo '</pre>';
if(isset($_REQUEST['date'])&&isset($_REQUEST['delete'])){
  $date=$_REQUEST['date'];
  $query="delete from verbruik where date = $date";
  if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
}
if(isset($_REQUEST['id'])&&isset($_REQUEST['date'])&&isset($_REQUEST['gas'])&&isset($_REQUEST['elec'])&&isset($_REQUEST['zon'])&&isset($_REQUEST['water'])){
  $date=$_REQUEST['date'];
  $gas=$_REQUEST['gas'];
  $elec=$_REQUEST['elec'];
  $zon=$_REQUEST['zon'];
  $water=$_REQUEST['water'];
  $query="UPDATE `verbruik` SET
    `gas` = '$gas',
    `elec` = '$elec',
    `zon` = '$zon',
    `water` = '$water'
    WHERE `date` = $date;";
  if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
}
echo '
  <div class="box">
    <a href=\'javascript:navigator_Go("edit.php");\'><h2>Edit Entries</h2></a>
      <table>
          <tr>
            <td>Date</td>
            <td>Gas</td>
            <td>Elec</td>
            <td>Zon</td>
            <td>Water</td>
            <td></td>
          </tr>
  <tr>
  <td colspan="6"><hr></td></tr>
';

$query="SELECT date, gas, elec, zon, water FROM verbruik order by date desc";
if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
$items=array();
while($row=$result->fetch_assoc())$items[]=$row;$result->free();
foreach($items as $item){echo '
<form method="GET" onkeypress="return event.keyCode != 13;">
  <tr>
    <td>
      <input type="date" name="date" value="'.$item['date'].'" class="edit" onchange="this.form.submit()"/>
    </td>
    <td>
      <input type="number" pattern="[0-9]*" inputmode="numeric" name="gas" value="'.$item['gas'].'" class="edit" autocomplete="off" onchange="this.form.submit()"/>
    </td>
    <td>
      <input type="number" pattern="[0-9]*" inputmode="numeric" name="elec" value="'.$item['elec'].'" class="edit" autocomplete="off" onchange="this.form.submit()"/>
    </td>
    <td>
      <input type="number" pattern="[0-9]*" inputmode="numeric" name="zon" value="'.$item['zon'].'" class="edit" autocomplete="off" onchange="this.form.submit()"/>
    </td>
    <td align="right">
      <input type="number" pattern="[0-9]*" inputmode="numeric" name="water" value="'.$item['water'].'" class="edit" autocomplete="off" onchange="this.form.submit()"/>
    </td>
    <td>
      <input type="submit" name="delete" value="delete" class="delete" tabindex="-1" onclick="return confirm(\'Do you really want to remove this entry?\');">
    </td>
</tr>
</form>';
}
echo '</table></div>';


//$db->close;
echo '<div class="bottom">
  <form action="summary.php"><input type="submit" value="Summary" class="btn"/></form>
  <form action="index.php"><input type="submit" value="Home" class="btn"/></form>
  </div>';
echo '</body></html>';
