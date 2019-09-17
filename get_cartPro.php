<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$pid = $configutil->splash_new($_POST["pid"]);
$pos = $configutil->splash_new($_POST["pos"]);
$resultArr 			= array();//返回数组
$posArr 			= array();//属性数组
$isout				= 0;	//上架下架, 1:下架 0:上架
$p_name				= "";	//产品名
$need_score			= 0;	//需要的积分
$p_storenum         = 1;	//库存
$propertyids        = "";	//属性id
$p_now_price        = 0;	//现价
$default_imgurl     = "";	//封面图片
$pos_name			= "";
$pos_parent_name	= "";
$query = 'SELECT 	
			name,
			isout,
			storenum,
			now_price,
			need_score,			
			propertyids,
			default_imgurl
			FROM weixin_commonshop_products where  isvalid=true and id=' . $pid;

$result = mysql_query($query) or die('Query failed1: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
	$isout			= $row->isout;
	$p_name			= $row->name;
	$need_score		= $row->need_score;
	$p_storenum		= $row->storenum;
	$p_now_price	= $row->now_price;
	$propertyids	= $row->propertyids;
	$default_imgurl	= $row->default_imgurl;
}
if( !empty( $pos ) ){
	$query = 'SELECT 	
			need_score,
			now_price,
			storenum
			FROM weixin_commonshop_product_prices where product_id=' . $pid." and proids='".$pos."'";
	$result = mysql_query($query) or die('Query failed2: ' . mysql_error());
	$p_storenum = 0;
	while ($row = mysql_fetch_object($result)) {
		$need_score		= $row->need_score;
		$p_storenum		= $row->storenum;
		$p_now_price	= $row->now_price;
	}
	
	/*查找属性名*/
	$propertyarr = explode("_",$pos);
	//var_dump($propertyarr);
	$pcount = count($propertyarr);
	for( $i = 0; $i < $pcount; $i++){
		$query = "select p1.name,p2.name as parent_name from weixin_commonshop_pros p1 LEFT JOIN weixin_commonshop_pros p2 on p1.parent_id=p2.id where p1.id=".$propertyarr[$i];
		//echo $query;
		$result = mysql_query($query) or die('Query failed3: ' . mysql_error());
		while ($row = mysql_fetch_object($result)) {
			$pos_name			= $row->name;
			$pos_parent_name	= $row->parent_name;
		}
		$posArr[$i]["pos_name"]			= $pos_name;
		$posArr[$i]["pos_parent_name"]	= $pos_parent_name;
	}
}

$error  = mysql_error();
if(!empty($error)){
	$resultArr["code"] = 0;
}else{
	$resultArr["code"] 				= 1;
	$resultArr["name"] 				= $p_name;
	$resultArr["isout"] 			= $isout;
	$resultArr["storenum"] 			= $p_storenum;
	$resultArr["now_price"] 		= $p_now_price;
	$resultArr["need_score"]		= $need_score;	
	$resultArr["propertyids"] 		= $propertyids;
	$resultArr["default_imgurl"]	= $default_imgurl;
	$resultArr["posArr"]			= $posArr;
}
mysql_close($link);
$jsons=json_encode($resultArr);
die($jsons);	
?>