<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
$tid = $configutil->splash_new($_GET["type_id"]);

$pos=0;
if(!empty($_GET["pos"])){
    $pos =$configutil->splash_new($_GET["pos"]);
}
$pid=0;
if(!empty($_GET["pid"])){
    $pid =$configutil->splash_new($_GET["pid"]);
}
$callback = $configutil->splash_new($_GET["callback"]);

$query="select id,name from weixin_commonshop_products where isvalid=true ";
$query=$query." and (type_id=".$tid." or (LOCATE('".$tid."', type_ids)>0))";
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  

$str="{pos:".$pos."},{pid:".$pid."}";
while ($row = mysql_fetch_object($result)) {

    $pid = $row->id;
	$pname = $row->name;
	
	$str = $str.",{pid:".$pid.",pname:'".$pname."'}";
	
}


 //echo $error;
 mysql_close($link);
  
 
 echo $callback."([".$str;
echo "]);";
echo $callback;
 

?>