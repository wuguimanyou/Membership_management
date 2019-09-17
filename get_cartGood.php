<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../proxy_info.php');

$data	= array();//返回数组
$pro_id	= -1;	//产品id
$i		= 0;
$query = "select pro_id from weixin_commonshop_guess_you_like where isvalid=true and customer_id=".$customer_id."  ORDER BY RAND() LIMIT 3";

$result = mysql_query($query) or die('Query failed1: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$pro_id			= $row->pro_id;
	
	$query1 = "select name,default_imgurl,now_price from weixin_commonshop_products where isvalid=true and customer_id=".$customer_id." and id=".$pro_id." ";
	$name 				= '';
	$default_imgurl 	= '';
	$now_price		 	= '';
	$result1=mysql_query($query1)or die('L102 Query failed'.mysql_error());
	while($row1=mysql_fetch_object($result1)){
		$name 			= $row1->name;
		$default_imgurl = $row1->default_imgurl;
		$now_price 		= $row1->now_price;
		break;
	}
	$data[$i]["pid"] 			= $pro_id;
	$data[$i]["now_price"]		= $now_price;
	$data[$i]["name"]			= $name;
	$data[$i]["default_imgurl"]	= "http://".$http_host.$default_imgurl;
	 $i++;
}

mysql_close($link);
$data=json_encode($data);
die($data);	
?>