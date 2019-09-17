<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

require('../proxy_info.php');
$data=array();
//$user_id = $configutil->splash_new($_GET["user_id"]);
$dis_pagenum	= $configutil->splash_new($_POST["dis_pagenum"]);
$pid 			= $configutil->splash_new($_POST["pid"]);
$type 			= $configutil->splash_new($_POST["type"]);
$end 			= 10;
$start 			= ($dis_pagenum-1) * $end;


$query = "select id,user_id,level,discuss,createtime,discussimg,batchcode,is_anonymous from weixin_commonshop_product_evaluations where isvalid=true and status=1 and type=1 and product_id=".$pid."";
if( $type > 0 ){
	$query .= " and level=".$type;
}
$query .= " order by id desc limit ".$start.",".$end."";
$result = mysql_query($query) or die('Query failed1: ' . mysql_error());
$num = mysql_num_fields($result);
$str			= "";
$batchcode		= "";//订单号
$discussimg		= "";//上传图片
$discuss		= "";//评价
$id				= "";
$level			= 1;
$is_anonymous	= 0;//匿名开关
$i 				= 0;
while ($row = mysql_fetch_object($result)) {
	$id 			= $row->id;
	$user_id 		= $row->user_id;
	$level 			= $row->level;
	$discuss 		= $row->discuss;
	$createtime		= $row->createtime;
	$discussimg 	= $row->discussimg;
	$batchcode 		= $row->batchcode;
	$is_anonymous 	= $row->is_anonymous;
	$imgs		= explode(",",$discussimg);
	$img_div	= '';
 	if(!empty($discussimg)){
		foreach ($imgs as $img){
		//echo $img = substr($img,5);
		$img = substr($img,2);
		$img = '<a href="http://'.$http_host.'/weixinpl'.$img.'"><img class="pingjia_image" src="http://'.$http_host.'/weixinpl'.$img.'" /></a>';
		$img_div .= $img;
		}
	}	
   
	$query2 = "select name,weixin_headimgurl,weixin_name from weixin_users where isvalid=true and id=".$user_id." limit 0,1";
	$result2 	= mysql_query($query2) or die('Query failed2: ' . mysql_error());
	$username	= "";
	$headimgurl	= "";
	
   while ($row2 = mysql_fetch_object($result2)) {
      $username		= $row2->name;
	  $headimgurl 	= $row2->weixin_headimgurl;
	  $weixin_name	= $row2->weixin_name;
	  break;
   }
   if(empty($username)){
      $username = $weixin_name;
   }
   if( $is_anonymous > 0 ){
	   $username = "匿名";
   }
   if(empty($headimgurl)){
      $headimgurl="../up/default/default_head.jpg";
   }
 
	//商家回复
	$discuss_author		= "";
	$createtime_author	= "";
	$discuss_au	= "";	
	if(!empty($batchcode)){
		$query_author = 'SELECT discuss,createtime FROM weixin_commonshop_product_evaluations where status=true and  isvalid=true and type=3 and batchcode=' . $batchcode . ' and reply_id=' . $id;
		$result_author = mysql_query($query_author) or die('Query failed_author: ' . mysql_error()); 
		while ($row_author = mysql_fetch_object($result_author)) {
			$discuss_au 		= $row_author->discuss;
			$createtime_author 	= $row_author->createtime;
			//$discuss_author=	'<a style="color:red;font-weight:bold;">商家回复:</a>'.$discuss_au;
		}
	} 
 
	//追加评论
	$discuss2		= "";
	$imgs2			= "";
	$discussimg2	= "";
	$discuss_zj		= "";
	$createtime2	= "";
	//商家回复追加评论
	$discuss_author2	= "";
	$createtime_author2	= "";	
	if(!empty($batchcode)){
		$query_zj= 'SELECT id,discuss,discussimg,createtime FROM weixin_commonshop_product_evaluations where  status=true  and isvalid=true and type=2 and batchcode='.$batchcode;
		$result_zj = mysql_query($query_zj) or die('Query failed_zj: ' . mysql_error()); 
		$discuss2 = "";
		$img_div2='';
		while ($row_zj = mysql_fetch_object($result_zj)) {
			$id2 = $row_zj->id;
			$discuss2 	 = $row_zj->discuss;
			$discussimg2 = $row_zj->discussimg;
			$createtime2 = $row_zj->createtime;
			$imgs2=explode(",",$discussimg2);
				
				if(!empty($discussimg2)){
					foreach ($imgs2 as $img2){
					$img2 = substr($img2,2);
					$img2 = '<a href="http://'.$http_host.'/weixinpl'.$img2.'"><img class="pingjia_image" src="http://'.$http_host.'/weixinpl'.$img2.'" /></a>';
					$img_div2 .= $img2;
					}
				}

			//$discuss_zj=	'<a style="color:red;">追加评论:</a>'.$discuss2.$img_div2;
			
			//商家回复追加评论
			if(!empty($batchcode)){
				$query_author= 'SELECT discuss,createtime FROM weixin_commonshop_product_evaluations where status=true and isvalid=true and type=3 and batchcode=' . $batchcode . ' and reply_id=' . $id2;
				$result_author = mysql_query($query_author) or die('Query failed_author2: ' . mysql_error()); 
				while ($row_author = mysql_fetch_object($result_author)) {
					$discuss_au2 		= $row_author->discuss;
					$createtime_author2 = $row_author->createtime;
					$discuss_author2=	/* '<a style="color:red;font-weight:bold;">商家回复:</a>'. */$discuss_au2;
				}
			}			
			
		}
	}   
   
 /*   $str = $str."{user_id:".$user_id.",username:'".$username."',headimgurl:'".$headimgurl."',level:".$level.",discuss:'".$discuss.$img_div."',discuss_zj:'".$discuss_zj."',discuss_author:'".$discuss_author."',discuss_author2:'".$discuss_author2."',timestr:'".$createtime."',timestr2:'".$createtime2."',timestr_author:'".$createtime_author."',timestr_author2:'".$createtime_author2."'},"; */
$data[$i]["user_id"] 			= $user_id;
$data[$i]["username"] 			= $username;
$data[$i]["headimgurl"] 		= $headimgurl;
$data[$i]["level"] 				= $level;
$data[$i]["discuss"] 			= $discuss;//评论
$data[$i]["img_div"] 			= $img_div;//评论图片
$data[$i]["timestr"] 			= $createtime;//评论时间
$data[$i]["discuss_author"] 	= $discuss_au;//商家回复
$data[$i]["timestr_author"] 	= $createtime_author;//商家回复时间
$data[$i]["discuss_zj"] 		= $discuss2;//粉丝追加评论
$data[$i]["img_div2"] 			= $img_div2;//粉丝追加图片
$data[$i]["timestr2"]			= $createtime2;//粉丝追加评论时间
$data[$i]["discuss_author2"] 	= $discuss_author2;//商家回复追加评论
$data[$i]["timestr_author2"]	= $createtime_author2;//商家回复追加评论时间

 $i++;
}

$error = mysql_error();
//echo $error;
mysql_close($link);
if( empty( $num ) ){
	die(0);
}
 $data=json_encode($data);
//$data="[".$data."]";
echo $data;
?>