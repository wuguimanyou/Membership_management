<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
require('../common/utility_fun.php');
//头文件----start
// require('../common/common_from.php');
//头文件----end
//session_start();
$customer_id = $_SESSION['customer_id'];
$user_id = $_SESSION['user_id_'.$customer_id];



$type 		= $configutil->splash_new($_POST["type"]);
$parent_id 	= $configutil->splash_new($_POST["parent_id"]);
switch ($type) {
	
	case 'order_money':

		$arr = array();

		//查消费总额--------start
		
		// $query = "SELECT SUM(totalprice) as totalprice FROM weixin_commonshop_orders WHERE isvalid=TRUE AND sendstatus<3 AND return_status IN(0,3,9) AND user_id=".$user_id." LIMIT 1";
		// $result= mysql_query($query) or die('Query failed23: ' . mysql_error());
		// while( $row = mysql_fetch_object($result) ){
		// 	$totalprice = cut_num($row->totalprice,2);
		// }
		// if($totalprice==NULL){
		// 	$totalprice = 0;
		// }
		$query = "SELECT total_money FROM my_total_money WHERE isvalid=true AND user_id=$user_id LIMIT 1";
		$result = mysql_query($query) or die('Query failed23: ' . mysql_error());
		while( $row = mysql_fetch_object($result) ){
			$totalprice = $row->total_money;
		}
		if($totalprice==''){
			$totalprice = 0;
		}
		//查消费总额--------end

		//查钱包余额--------start
		$balance = 0;
		$query = "SELECT balance FROM moneybag_t where isvalid=true AND user_id=".$user_id." LIMIT 1";
		$result= mysql_query($query) or die('Query failed32: ' . mysql_error());
		while($row=mysql_fetch_object($result)){
			$balance = $row->balance;
		}

		if($parent_id>0){
			$weixin_name = '';
			$query = "SELECT weixin_name FROM weixin_users WHERE isvalid=true AND id=".$parent_id." LIMIT 1";
			$result= mysql_query($query) or die('Query failed39: ' . mysql_error());
			while( $row = mysql_fetch_object($result) ){
				$arr['weixin_name'] = $row->weixin_name;

			}
		}

		//查钱包余额--------end
		$arr['totalprice'] 	= cut_num($totalprice,2);
		$arr['balance'] 	= cut_num($balance,2);

		echo json_encode($arr);

	break;

	case 'my_data':
		$name = '';
		$query = "SELECT weixin_name FROM weixin_users WHERE isvalid=true AND id=".$parent_id." LIMIT 1";
		$result= mysql_query($query) or die('Query failed62: ' . mysql_error());
		while( $row = mysql_fetch_object($result) ){
			$name = $row->weixin_name;
		}
		echo json_encode($name);
	break;

	
	default:
		# code...
		break;
}


?>