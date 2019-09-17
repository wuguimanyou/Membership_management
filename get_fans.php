<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]

$user_id = $configutil->splash_new($_POST["user_id"]);
$p_tcount = 0;
$query_p = "SELECT count(distinct p.user_id) as p_tcount FROM promoters p INNER JOIN weixin_users w ON w.id=p.user_id  WHERE  w.isvalid=TRUE AND p.isvalid=TRUE AND w.isvalid=true AND p.status=1 and p.customer_id=".$customer_id." and w.customer_id=".$customer_id."  AND match(w.gflag) against (',".$user_id.",')";		
//echo $query_p;
$result = mysql_query($query_p) or die ('query_p faild' .mysql_error());
while( $row = mysql_fetch_object( $result ) ){
   $p_tcount = $row -> p_tcount;									   
}
$f_tcount = 0;
$query_f="SELECT count(1) as f_tcount FROM  weixin_users WHERE  isvalid=TRUE and customer_id=".$customer_id."  AND match(gflag) against (',".$user_id.",')";		

$result = mysql_query($query_f) or die ('query_f faild' .mysql_error());
while( $row = mysql_fetch_object( $result ) ){
   $f_tcount = $row -> f_tcount;
}
$data=array();
$data['p_tcount'] = $p_tcount;   //推广员
$data['f_tcount'] = $f_tcount;   //粉丝
$data=json_encode($data);
echo $data;
?>
