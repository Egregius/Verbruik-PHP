<?php
header("Content-type:text/css;charset:UTF-8");
header("Cache-Control:must-revalidate");
//header("Expires:".gmdate("D, d M Y H:i:s",time()+259200)." GMT");// 3 dagen
//header("Expires:".gmdate("D, d M Y H:i:s",time()+7200)." GMT");// 2 uren
header("Expires:".gmdate("D, d M Y H:i:s",time()+1)." GMT");// Direct
if(strpos($_SERVER['HTTP_USER_AGENT'],'iPad')!==false)$udevice='iPad';
elseif(strpos($_SERVER['HTTP_USER_AGENT'],'iPhone')!==false)$udevice='iPhone';
else $udevice='other';
?>
html{padding:0;margin:0;color:#ccc;font-family:sans-serif;height:100%;}
body{padding:0;margin:0;background:#000;/*width:100%;height:100%;*/}
.navbar{position:fixed;top:0px;left:0px;width:100%;padding:2px 0px 2px 0px;z-index:100;background-color:#111;}

a:link{text-decoration:none;color:#ccc}
a:visited{text-decoration:none;color:#ccc}
a:hover{text-decoration:none;color:#ccc;}
a:active{text-decoration:none;color:#ccc}
form{display:inline;margin:0px;padding:0px;}
input[type=text]  {cursor:pointer;-webkit-appearance:none;border-radius:5px;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;margin-top:6px;}
input[type=number]{cursor:pointer;-webkit-appearance:none;border-radius:5px;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;margin-top:6px;}
input[type=submit]{cursor:pointer;-webkit-appearance:none;border-radius:5px;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;margin-top:6px;}
input[type=select]{cursor:pointer;-webkit-appearance:none;border-radius:5px;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;margin-top:6px;}
input[type=date]  {cursor:pointer;-webkit-appearance:none;border-radius:5px;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;border:0px solid transparent;margin-top:6px;}

.menu{position:fixed;top:0px;left:0px;width:920px;height:136px;z-index:100;background-color:#000;}
.legend{width:920px;padding:10px 0px 10px 0px;font-size:1.5em;}
.first{padding-top:130px;}
.hidden{display:none;}
*, *::before, *::after{box-sizing:border-box;}
input[type=checkbox]{position:absolute;left:-9999px;}
label{margin:1px;padding:14px 3px 14px 3px;border:0px solid #fff;border-radius:14px;color:#fff;background-color:#555;cursor:pointer;user-select:none;}
input.Guy:checked + label{background-color:#F15854;color:#000;}
input.Kevin:checked + label{background-color:#FAA43A;color:#000;}
input.Patrick:checked + label{background-color:#60BD68;color:#000;}
input.Sammy:checked + label{background-color:#5DA5DA;color:#000;}
input.Miguel:checked + label{background-color:#DECF3F;color:#000;}



.btn{height:60px;font-size:1.5em;background-color:#333;color:#ccc;text-align:center;text-align-last:center;vertical-align:middle;display:inline-block;border:0px solid transparent;padding:2px;margin:1px 0px 1px 1px;-webkit-appearance:none;white-space:nowrap;overflow:hidden;}
.btn:hover{color:#000;background-color:#ffba00;cursor:pointer;}
.btna{color:#000;background-color:#ffba00;}
.bottom{position:fixed;bottom:0;width:100%;}
.clear{clear:both;}
.box{text-align:center;left:0px;background:#222;padding:6px;margin:6px;}
.right{text-align:right;}

.red{background-color:#F00;}
.green{background-color:#0F0;}
.blue{background-color:#00F;}
.yellow{background-color:#FF0;}

.content{/*height:100%;*/min-height:80%;margin:0 auto;padding-top:55px;}
.b1{width:1500px;max-width:99%}
.b2{width:1500px;max-width:48.55%}
.b3{width:1500px;max-width:32.2%}
.b4{width:1500px;max-width:24%}
.b5{width:1500px;max-width:19%}
.b6{width:1500px;max-width:15.7%}
.b7{width:1500px;max-width:13.2%}
.b8{width:1500px;max-width:12%}
.b9{width:1500px;max-width:10%}
.b10{width:1500px;max-width:10%}

.sticky-table{width:900px;max-height:1200px;overflow:auto;border-top:1px solid #ddd;border-bottom:1px solid #ddd;padding:0!important}
.sticky-table table{margin-bottom:0;width:100%;max-width:100%;border-spacing:0}
.sticky-table table tr.sticky-row td,.sticky-table table tr.sticky-row th{background-color:#000;border-top:0;position:relative;outline:#ddd solid 1px;z-index:5}
.sticky-table table td.sticky-cell,.sticky-table table th.sticky-cell{background-color:#000;outline:#ddd solid 0px;position:relative;padding:0 10px;z-index:10}
.sticky-table table tr.sticky-row td.sticky-cell,.sticky-table table tr.sticky-row th.sticky-cell{z-index:15}

tr:nth-child(even) {background: #333}
tr:nth-child(odd) {background: #000}

h2{font-size:32px;padding: 0px;margin:0px;}
td{padding:0 14px;}
th{text-align:center;}

.delete{height:20px;padding:3px;box-sizing: border-box;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background:#666;color: #eee;outline: none;}
.lastdate{position:fixed;}
<?php if($udevice=='other'){ ?>
  .edit{width:100px;height:20px;padding:3px;box-sizing: border-box;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background:#666;color: #eee;outline: none;border:0px solid transparent;}

<?php }elseif($udevice=='iPad'){ ?>
@media only screen and (orientation: portrait) {
  .edit{width:100px;height:20px;padding:3px;box-sizing: border-box;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background:#666;color: #eee;outline: none;border:0px solid transparent;}

}
@media only screen and (orientation: landscape) {
  .edit{width:100px;height:20px;padding:3px;box-sizing: border-box;-webkit-appearance:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background:#666;color: #eee;outline: none;border:0px solid transparent;}

}
<?php } ?>
