<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

$shop_id = $configutil->splash_new($_POST["id"]);

$shopName = "";
if( $shop_id > 0 ){
	$query = 'SELECT shopName FROM weixin_commonshop_applysupplys where user_id ='.$shop_id;
	$result = mysql_query($query) or die('Query failed1: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $shopName = $row->shopName;
	}
}else{
	$query="select name from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	$result = mysql_query($query) or die('Query failed2: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
		$shopName = $row->name;
	}
}

echo $shopName;
?>