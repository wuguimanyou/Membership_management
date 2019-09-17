<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$pid = $configutil->splash_new($_POST["pid"]);
$pos = $configutil->splash_new($_POST["pos"]);
$resultArr				= array();//返回数组
$pos_arr 				= array();//返回数组
$price_arr	 			= array();//属性数组
$posArr_one 			= array();//属性数组
$posArr_two 			= array();//属性数组

$propertyarr = explode("_",$pos);
$pcount = count($propertyarr);

for( $i = 0; $i < $pcount; $i++ ){
	//$posArr_two 			= array();//属性数组
	$query = "select p1.name,p1.id,p2.id as parent_id,p2.name as parent_name from weixin_commonshop_pros p1 LEFT JOIN weixin_commonshop_pros p2 on p1.parent_id=p2.id where p1.id=".$propertyarr[$i];
	//echo $query;
	$result = mysql_query($query) or die('Query failed3: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$pos_name				= $row->name;
		$id						= $row->id;
		$parent_id				= $row->parent_id;
		$pos_parent_name		= $row->parent_name;
	}	
	$posArr_two["id"]		= $id;
	$posArr_two["pos_name"]	= $pos_name;

	if( array_key_exists( $parent_id , $pos_arr ) ){
		array_push( $pos_arr[$parent_id]["two_class"] , $posArr_two );
	}else{
		$pos_arr[$parent_id]["one_name"] = $pos_parent_name;
		$pos_arr[$parent_id]["one_id"] = $parent_id;
		$pos_arr[$parent_id]["two_class"] = array();
		array_push( $pos_arr[$parent_id]["two_class"] , $posArr_two );
	}	
}
$Pnow_price 	= 0;
$Pstorenum 	= 0;
$Pneed_score = 0;
$now_price 	= 0;
$storenum 	= 0;
$need_score = 0;
$proids		= "";
$i 			= 1;
$query = "select now_price,storenum,need_score from weixin_commonshop_products where id=".$pid;
$result = mysql_query($query) or die('Query failed3: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$Pnow_price		= $row->now_price;
	$Pstorenum		= $row->storenum;
	$Pneed_score	= $row->need_score;
	
	$price_arr[0]["proids"]		= "";
	$price_arr[0]["now_price"]	= $Pnow_price;
	$price_arr[0]["need_score"]	= $Pneed_score;
	$price_arr[0]["storenum"]	= $Pstorenum;
}
$query = "select now_price,storenum,need_score,proids from weixin_commonshop_product_prices where product_id=".$pid;
$result = mysql_query($query) or die('Query failed3: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$now_price	= $row->now_price;
	$storenum	= $row->storenum;
	$need_score	= $row->need_score;
	$proids		= $row->proids;
	
	$price_arr[$i]["proids"]		= $proids;
	$price_arr[$i]["now_price"]		= $now_price;
	$price_arr[$i]["need_score"]	= $need_score;
	$price_arr[$i]["storenum"]		= $storenum;
	$i++;
}	
$resultArr["pos"]	= $pos_arr;
$resultArr["price"]	= $price_arr;
//print_r($resultArr);
//var_dump($resultArr);
mysql_close($link);
$jsons=json_encode($resultArr);
die($jsons);	
	
?>