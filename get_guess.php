<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
$pid 			= $configutil->splash_new($_POST["pid"]);
require('../proxy_info.php');
$data=array();

$pr_pid			= -1;//产品id
$now_price 		= 0;//现价
$name 			= "";//产品名
$default_imgurl = "";//封面图
$i 				= 0;

$query = "select pid from products_relation_t where isvalid = true and parent_pid=".$pid."  ORDER BY RAND() LIMIT 3";
$result = mysql_query($query) or die('Query failed1: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$pr_pid	=  $row->pid;
	
	$query2 = "select name,default_imgurl,now_price from weixin_commonshop_products where isvalid = true and id=".$pr_pid;
	$result2 = mysql_query($query2) or die('Query failed2: ' . mysql_error());
	while ($row2 = mysql_fetch_object($result2)) {	
		$name			=  $row2->name;
		$now_price		=  $row2->now_price;
		$default_imgurl	=  $row2->default_imgurl;
	}
	$data[$i]["pid"] 			= $pr_pid;
	$data[$i]["now_price"]		= $now_price;
	$data[$i]["name"]			= $name;
	$data[$i]["default_imgurl"]	= "http://".$http_host.$default_imgurl;
	$i++;
}


$error = mysql_error();
//echo $error;
mysql_close($link);
//print_r($data);
 $data=json_encode($data);
//$data="[".$data."]";
echo $data;
?>