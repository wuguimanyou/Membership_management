<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]		
$location_p		= $configutil->splash_new($_POST["location_p"]);
$express_ids 	= $configutil->splash_new($_POST["express_ids"]);
$express_id_arr = substr($express_ids,0,-1);
$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

	
$query = "select id,name,price from weixin_expresses where isvalid=true and customer_id=".$customer_id." and ((is_include=0 and region like '%".$location_p."%' ) or (is_include=1 and region not like '%".$location_p."%') or region='') and id in(".$express_id_arr.") group by price asc limit 1";

$result = mysql_query($query) or die('Query failed: ' . mysql_error()); 

$obj_id 	= -1;
$obj_price 	= "没有匹配到合适快递";
$obj_title 	= "";
$json	= array();
//print_r(mysql_fetch_object($result));
while ($row = mysql_fetch_object($result)) {
	//echo "fdgf";
	$obj_id		= $row->id;
	$obj_title	= $row->name;
	$obj_price	= $row->price;

	if( $obj_price == 0 ){
		$obj_price = "免邮";
	}
}

$json["obj_id"]		= $obj_id;
$json["obj_title"]	= $obj_title;
$json["obj_price"]	= $obj_price;
	
mysql_close($link);
$jsons=json_encode($json);
die($jsons);
?>