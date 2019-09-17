<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../common/utility.php');
$customer_id = $_SESSION['customer_id'];
$user_id = $_SESSION['user_id_'.$customer_id];


$type		 = 	$configutil->splash_new($_POST["op"]);
$id 		 = -1;
$name        = 	'';//收货人名字
$phone       = 	'';//联系电话
$address     = 	'';//自定义街道等信息
$location_p  = 	'';//省
$location_c  = 	'';//市
$location_a  = 	'';//镇区
$is_default  = 	 0;//是否默认



if($type == 'delete'){

	$id=$configutil->splash_new($_POST["id"]);
	$query = "UPDATE weixin_commonshop_addresses SET isvalid=false where id=".$id;
	//echo $query;die;
	mysql_query($query);
	$up_num = mysql_affected_rows();
	echo json_encode($up_num);
	return false;

}elseif($type == 'check'){
	$query  = "SELECT id,address,name,phone,location_p,location_c,location_a,is_default FROM weixin_commonshop_addresses WHERE isvalid=true and user_id=".$user_id;
	$result = mysql_query($query);
	$arr = array();
	$i=0;
	while( $row = mysql_fetch_object($result) ){
		$id 		 = $row->id;
	    $name        = $row->name;
	    $phone       = $row->phone;
	    $address     = $row->address;
	    $location_p  = $row->location_p;
	    $location_c  = $row->location_c;
	    $location_a  = $row->location_a;
	    $is_default  = $row->is_default;

	    $arr[$i]['location_p']	= $location_p;
	    $arr[$i]['location_c']	= $location_c;
	    $arr[$i]['location_a']	= $location_a;
	    $arr[$i]['is_default']	= $is_default;
	    $arr[$i]['address']		= $address;
	    $arr[$i]['phone']		= $phone;
	    $arr[$i]['name']		= $name;
	    $arr[$i]['id'] 			= $id;
	    $i++;
	   
	}
	 echo json_encode($arr);
}





?>