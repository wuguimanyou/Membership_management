<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");
include('../common/phpqrcode/phpqrcode.php'); 
require('../proxy_info.php');

define("InviteUrl","http://".$http_host."/weixinpl/mshop/WeChatPay/weipay_payother.php?customer_id=");
$linkurl =InviteUrl.$customer_id_en;

$payother_desc_id = $configutil->splash_new($_POST["payother_desc_id"]);
$user_id 		  = $configutil->splash_new($_POST["user_id"]);

$linkurl  = $linkurl."&payother_desc_id=".$payother_desc_id;
$p_qrcode_url  = $linkurl."&user_id=".$user_id."&showwxpaytitle=1";
if(!empty($_POST["pay_batchcode"])){
	$pay_batchcode = $configutil->splash_new($_POST["pay_batchcode"]);
	$p_qrcode_url  = $p_qrcode_url."&pay_batchcode=".$pay_batchcode;
}

	$timestr = time();
	   // 生成的文件名 
   $filename = '../pic/qrs/';
   if(!file_exists($filename)){
	   mkdir($filename);	   
   }
   $filename = '../pic/qrs/'.$customer_id."/";
   if(!file_exists($filename)){
	   mkdir($filename);	   
   }
   $filename = '../pic/qrs/'.$customer_id."/person/";
   if(!file_exists($filename)){
	   mkdir($filename);	   
   }
   $filename=$filename.$timestr.".png";
   
   
  // echo $filename;
   // 纠错级别：L、M、Q、H 
   $errorCorrectionLevel = 'L';  
   // 点的大小：1到10 
   $matrixPointSize = 4;
   
  // echo $p_qrcode_url;
   QRcode::png($p_qrcode_url, $filename, $errorCorrectionLevel, $matrixPointSize, 2); 

   $qr_imgurl = BaseURL."pic/qrs/".$customer_id."/person/".$timestr.".png";
   
   $sql="update weixin_commonshop_otherpay_descs set qr_imgurl='".$qr_imgurl."' where id=".$payother_desc_id;
   mysql_query($sql);
   $error = mysql_error();
 //echo $error;
 mysql_close($link);
 
 echo json_encode($qr_imgurl);
?>