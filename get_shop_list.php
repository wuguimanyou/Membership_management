<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]		
$callback = $_GET["callback"];
$cat_id =$configutil->splash_new($_GET["parent_class"]);

$link =mysql_connect(DB_HOST,DB_USER, DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');


$typeson_id=array();	
$query = "select id,name from weixin_commonshop_products where isvalid=true and isout=false and customer_id=".$customer_id;
if($cat_id >0){
	$type_son="select id from weixin_commonshop_types where isvalid=true and is_shelves=1 and parent_id=".$cat_id." and customer_id=".$customer_id."";
	$result_typeson=mysql_query($type_son) or die ('typeson faild ' .mysql_error());
	while($row=mysql_fetch_object($result_typeson)){

		$typeson_id[]=$row->id;
	}
	if(empty($typeson_id)){
		$typeson_id=$cat_id; 
	}else{
		array_push($typeson_id,$cat_id);
		$typeson_id=implode(',',$typeson_id);
	}
		$query = $query." and (";
		$typeson_id_arr = explode(",",$typeson_id);
		$typeson_id_count = count($typeson_id_arr);
		for($j=0;$j<$typeson_id_count;$j++){
			$o_typeid = $typeson_id_arr[$j];
			if($j==0){
				$query = $query."( LOCATE(',".$o_typeid.",', type_ids)>0)";
				}else{
				$query = $query." or (LOCATE(',".$o_typeid.",', type_ids)>0)";
			}
		}
		$query = $query.")";
		unset($typeson_id);
	
}


$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
$obj_id = -1;
$obj_title = "";
while ($row = mysql_fetch_object($result)) {
  $obj_id = $row->id;
  $obj_title = $row->name;
  $str = $str."{obj_id:".$obj_id.",obj_title:'".$obj_title."'},";
}
if($str!=""){
  $str = rtrim($str,",");
}
	
mysql_close($link);
echo $callback."([".$str;
echo "]);";
echo $callback;

?>