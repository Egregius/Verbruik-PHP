<?php error_reporting(E_ALL);ini_set("display_errors","on");
$Guy='#F15854';
$Kevin='#FAA43A';
$Miguel='#DECF3F';
$Patrick='#60BD68';
$Sammy='#5DA5DA';

$cookie='Verbruik';
$telegrambot='123456789:ABCD-xCRhO-RBfUqICiJs8q9A_3YIr9irxI';
$telegramchatid=87654321;

$db=new mysqli('localhost','home','H0m€','verbruik');
if($db->connect_errno>0){die('Unable to connect to database ['.$db->connect_error.']');}
$query="SELECT name,pwd,mindate,zon,nacht FROM `users` order by name asc";
if(!$result=$db->query($query)){die('There was an error running the query ['.$query.'-'.$db->error.']');}
$users=array();
while ($row=$result->fetch_assoc()){
	$users[$row['name']]['pwd']=$row['pwd'];
	$users[$row['name']]['mindate']=$row['mindate'];
	$users[$row['name']]['zon']=$row['zon'];
	$users[$row['name']]['nacht']=$row['nacht'];
}
$result->free();
foreach($users as $u=>$p){
	$tabel=$u.'dag';
	$last=mysqli_fetch_assoc(mysqli_query($db, "SELECT date FROM $tabel order by date desc limit 0,1"));
	$users[$u]['maxdate']=$last['date'];
}

if(!isset($_SERVER['HTTP_USER_AGENT']))die('No user agent specified');
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'iPhone')!==false)$udevice='iPhone';
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'iPad')!==false)$udevice='iPad';
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'Macintosh')!==false)$udevice='Mac';
else $udevice='other';
if(substr($_SERVER['REMOTE_ADDR'],0,10)=='192.168.2.')$local=true;else $local=false;
if(isset($_REQUEST['uitloggen'])){
	if(isset($_REQUEST['username']))$user=$_POST['username'];
	setcookie($cookie,NULL,time()-86400,'/');
	telegram('Verbruik: '.$user.' logged out',true);
	header("Location:/index.php");
	die("Redirecting to:/index.php");
}
if(getenv('HTTP_CLIENT_IP'))$ipaddress=getenv('HTTP_CLIENT_IP');
elseif(getenv('HTTP_X_FORWARDED_FOR'))$ipaddress=getenv('HTTP_X_FORWARDED_FOR');
elseif(getenv('HTTP_X_FORWARDED'))$ipaddress=getenv('HTTP_X_FORWARDED');
elseif(getenv('HTTP_X_REAL_IP'))$ipaddress=getenv('HTTP_X_REAL_IP');
elseif(getenv('HTTP_FORWARDED_FOR'))$ipaddress=getenv('HTTP_FORWARDED_FOR');
elseif(getenv('HTTP_FORWARDED'))$ipaddress=getenv('HTTP_FORWARDED');
elseif(getenv('REMOTE_ADDR'))$ipaddress=getenv('REMOTE_ADDR');
else $ipaddress='UNKNOWN';
if(isset($_COOKIE[$cookie])){
	$user=$_COOKIE[$cookie];
	if(array_key_exists($user,$users)){
		$authenticated=true;
		$Usleep=80000;
		$tabel=$user;
		$tabeldag=$user.'dag';
		$query="UPDATE users set views=views+1 where name like '$user'";
		if(!$result=$db->query($query)){die('There was an error running the query ['.$query.' - '.$db->error.']');}
	}
}elseif(isset($_REQUEST['username'])&&isset($_REQUEST['password'])){
	$subuser=$_REQUEST['username'];
	$subpass=$_REQUEST['password'];
	if(isset($users[$subuser])){
		if($users[$subuser]['pwd']==$subpass&&strlen($subuser)>=3&&strlen($subuser)<=7&&strlen($subpass)>=4&&strlen($subpass)<=22){
			lg(print_r($_SERVER,true));
			koekje($subuser,time()+31536000);
			telegram('Verbruik '.$subuser.' logged in.'.PHP_EOL.'IP '.$ipaddress.PHP_EOL.$_SERVER['HTTP_USER_AGENT'],false);
			header("Location:/index.php");
			die("Redirecting to:/index.php");
		}else{
			fail2ban($ipaddress.' FAILED wrong password');
			$msg="Verbruik Failed login attempt (Wrong password): ";
			if(isset($subuser))$msg.=PHP_EOL."USER=".$subuser;
			if(isset($subpass))$msg.=PHP_EOL."PSWD=".$subpass;

			$msg.=PHP_EOL."IP=".$ipaddress;
			if(isset($_SERVER['REQUEST_URI']))$msg.=PHP_EOL."REQUEST=".$_SERVER['REQUEST_URI'];
			if(isset($_SERVER['HTTP_USER_AGENT']))$msg.=PHP_EOL."AGENT=".$_SERVER['HTTP_USER_AGENT'];
			lg($msg);
			telegram($msg,false);
			die('Wrong password!<br>Try again in 10 minutes.<br>After second fail you are blocked for a week!');
		}
	}else{
		fail2ban($ipaddress.' FAILED unknown user');
		$msg="Verbruik Failed login attempt (Unknown user): ";
		if(isset($subuser))$msg.="__USER=".$subuser;
		if(isset($subpass))$msg.="__PSWD=".$subpass;
		$msg.="__IP=".$ipaddress;
		if(isset($_SERVER['REQUEST_URI']))$msg.=PHP_EOL."REQUEST=".$_SERVER['REQUEST_URI'];
		if(isset($_SERVER['HTTP_USER_AGENT']))$msg.=PHP_EOL."AGENT=".$_SERVER['HTTP_USER_AGENT'];
		lg($msg);
		telegram($msg,false);
		die('Unknown user!<br>Try again in 10 minutes.<br>After second fail you are blocked for a week!');
	}
}else{
	echo '<html><head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<meta name="HandheldFriendly" content="true" />
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="viewport" content="width=device-width,height=device-width, initial-scale=1, user-scalable=yes, minimal-ui" />
	<link rel="icon" type="image/png" href="images/kodi.png"/>
	<link rel="shortcut icon" href="images/kodi.png"/>
	<link rel="apple-touch-startup-image" href="images/kodi.png"/>
	<link rel="apple-touch-icon" href="images/kodi.png"/>
	<title>Inloggen</title>
	<style>
	html{padding:0;margin:0;color:#ccc;font-family:sans-serif;height:100%;}
	body{padding:0;margin:0;background:#000;width:100%;height:100%;background-image:url(\'/images/_firework.jpg\');background-size:contain;background-repeat:no-repeat;background-attachment:fixed;background-position:center bottom;}

	input[type=text]  {height:35px;width:100%;cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;background-color:#444;color:#ccc;display:inline-block;border:0px solid transparent;padding:2px;margin:1px 0px 1px 1px;-webkit-appearance:none;white-space:nowrap;overflow:hidden;}
	input[type=password]{height:35px;width:100%;cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;background-color:#444;color:#ccc;display:inline-block;border:0px solid transparent;padding:2px;margin:1px 0px 1px 1px;-webkit-appearance:none;white-space:nowrap;overflow:hidden;}
	input[type=submit]{height:35px;width:100%;cursor:pointer;-webkit-appearance:none;border-radius:0;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;background-color:#444;color:#ccc;display:inline-block;border:0px solid transparent;padding:2px;margin:1px 0px 1px 1px;-webkit-appearance:none;white-space:nowrap;overflow:hidden;}
	</style>
    </head>
	<body>
		<div style="position:fixed;top:10px;left:10px;">
		<form method="POST">
		<table>
			<tr><td><input type="text" name="username" placeholder="Gebruikersnaam" size="50"/></td></tr>
			<tr><td><input type="password" name="password" placeholder="Wachtwoord" size="50"/></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td><input type="submit" value="Inloggen"/></td></tr>
		</table>
		</form>
		</div>
		</body>
		</html>';
	die('');
}
function koekje($user,$expirytime){
	global $cookie;
	setcookie($cookie,$user,$expirytime,'/');
}
function lg($msg){
	$fp=fopen('/var/log/floorplanlog.log',"a+");
	$time=microtime(true);
	$dFormat="Y-m-d H:i:s";
	$mSecs=$time-floor($time);
	$mSecs=substr(number_format($mSecs,3),1);
	fwrite($fp,sprintf("%s%s %s\n",date($dFormat),$mSecs,$msg));
	fclose($fp);
}
function fail2ban($ip){
	$time=microtime(true);$dFormat="Y-m-d H:i:s";$mSecs=$time-floor($time);$mSecs=substr(number_format($mSecs,3),1);
	$fp=fopen('/var/log/home2ban.log',"a+");
	fwrite($fp,sprintf("%s %s\n",date($dFormat),$ip));
	fclose($fp);
}
function telegram($msg,$silent=true,$to=1){
	$msg=str_replace('__',PHP_EOL,$msg);
	global $telegrambot,$telegramchatid,$telegramchatid2;
	for($x=1;$x<=10;$x++){
		$result=json_decode(file_get_contents('https://api.telegram.org/bot'.$telegrambot.'/sendMessage?chat_id='.$telegramchatid.'&text='.urlencode($msg).'&disable_notification='.$silent),true);
		if(isset($result['ok']))
			if($result['ok']===true){lg('telegram sent to 1: '.$msg);break;}
			else {lg('telegram sent failed');sleep($x*3);}
	}
}
?>