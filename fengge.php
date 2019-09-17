<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../proxy_info.php');
require('../auth_user.php');

require('../common/utility_4m.php');
mysql_query("SET NAMES UTF8");

$template_id=-1;
if(!empty($_GET["template_id"])){
    $template_id=$configutil->splash_new($_GET["template_id"]);
	$query="update weixin_commonshops set template_id=".$template_id." where customer_id=".$customer_id;

	mysql_query($query);
	
	//贴牌oem编号
	$adminuser_id = $configutil->splash_new($_GET["adminuser_id"]);
	
}else{
	$query ="select template_id from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $template_id = $row->template_id;
	}
}
$query ="select isOpenPublicWelfare from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	while ($row = mysql_fetch_object($result)) {
	   $isOpenPublicWelfare = $row->isOpenPublicWelfare;
	}

$u4m = new Utiliy_4m();
$rearr = $u4m->is_4M($customer_id);

//是4m分销
$is_shopgeneral = $rearr[0]  ;
//厂家编号
$adminuser_id = $rearr[1] ;
//是否是厂家总店
$is_samelevel = $rearr[2] ;
//总店模板编号
$general_template_id = $rearr[3] ;
//总店商家编号
$general_customer_id = $rearr[4] ;

//1：厂家总店； 2：代理商总店
$owner_general = $rearr[5] ;

$orgin_adminuser_id = $rearr[6] ;

if($adminuser_id>0 and $is_shopgeneral){
	   //查找总部商店
   if(!empty($_GET["general_customer_id"])){
		$general_customer_id_2 = $configutil->splash_new($_GET["general_customer_id"]);
		if($general_customer_id_2>0 and $template_id>0){
		   //是总部商家，则更新所有下级商城的模板编号
		   $query="update weixin_commonshops set template_id=".$template_id." where customer_id in (select id from customers where isvalid=true and adminuser_id in (select id from adminusers where isvalid=true and parent_id=".$adminuser_id."))";
		   mysql_query($query);
		}
   }
  
}

//新增客户
$new_customer_count =0;
//今日销售
$today_totalprice=0;
//新增订单
$new_order_count =0;
//新增推广员
$new_qr_count =0;

$nowtime = time();
$year = date('Y',$nowtime);
$month = date('m',$nowtime);
$day = date('d',$nowtime);

$query="select count(distinct batchcode) as new_order_count from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = mysql_query($query) or die('Query failed19: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_order_count = $row->new_order_count;
   break;
}

$query="select sum(totalprice) as today_totalprice from weixin_commonshop_orders where paystatus=1 and sendstatus!=4 and isvalid=true and customer_id=".$customer_id." and year(paytime)=".$year." and month(paytime)=".$month." and day(paytime)=".$day;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $today_totalprice = $row->today_totalprice;
   break;
}
$today_totalprice = round($today_totalprice,2);

$query="select count(1) as new_customer_count from weixin_commonshop_customers where isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_customer_count = $row->new_customer_count;
   break;
}

$query="select count(1) as new_qr_count from promoters where status=1 and isvalid=true and customer_id=".$customer_id." and year(createtime)=".$year." and month(createtime)=".$month." and day(createtime)=".$day;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());  
 //  echo $query;
while ($row = mysql_fetch_object($result)) {
   $new_qr_count = $row->new_qr_count;
   break;
}

$is_distribution=0;//渠道取消代理商功能
//代理模式,分销商城的功能项是 266
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scdl' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_distribution=1;
}
$is_supplierstr=0;//渠道取消供应商功能
//供应商模式,渠道开通与不开通
$query1="select cf.id,c.filename from customer_funs cf inner join columns c where c.isvalid=true and cf.isvalid=true and cf.customer_id=".$customer_id." and c.filename='scgys' and c.id=cf.column_id";
$result1 = mysql_query($query1) or die('Query failed: ' . mysql_error());  
$dcount= mysql_num_rows($result1);
if($dcount>0){
   $is_supplierstr=1;
}
?>
<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link href="css/global.css" rel="stylesheet" type="text/css">
<link href="css/main.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/global.js"></script>
<script>
  var customer_id = '<?php echo $customer_id_en; ?>';
  var general_customer_id = <?php echo $general_customer_id; ?>;
  var adminuser_id = <?php echo $adminuser_id; ?>;
</script>
</head>

<body>

<style type="text/css">body, html{background:url(images/main-bg.jpg) left top fixed no-repeat;}</style>
<div class="div_line">
		   <div class="div_line_item" onclick="show_newOrder('<?php echo $customer_id_en; ?>');">
		      今日订单: <span style="padding-left:10px;font-size:18px;font-weight:bold"><?php echo $new_order_count; ?></span>
		   </div>
		   <div class="div_line_item_split"></div>
		   <div class="div_line_item"  onclick="show_todayMoney('<?php echo $customer_id_en; ?>');">
		      今日销售: <span style="padding-left:10px;color:red;font-size:18px;font-weight:bold">￥<?php echo $today_totalprice; ?></span>
		   </div>
		   <div class="div_line_item_split"></div>
		   <div class="div_line_item"  onclick="show_newCustomer('<?php echo $customer_id_en; ?>');">
		       新增客户: <span style="padding-left:10px;font-size:18px;font-weight:bold"><?php echo $new_customer_count; ?></span>
		   </div>
		   <div class="div_line_item_split"></div>
		   <div class="div_line_item"  onclick="show_newQrsell('<?php echo $customer_id_en; ?>');">
		      新增推广员: <span style="padding-left:10px;font-size:18px;font-weight:bold"><?php echo $new_qr_count; ?></span>
		   </div>
		   <div class="div_line_item_split"></div>
		   <?php
		   $sql_stock = "select stock_remind from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
		   $res_stock = mysql_query($sql_stock) or die('Query failed: ' . mysql_error());
		   while ($row_sql_stock = mysql_fetch_object($res_stock)) {
				$stock_remind = $row_sql_stock->stock_remind;
			}
  		    $stock_mun=0;
			$stock_pidarr="";
			$query_stock1="select id from weixin_commonshop_products where isvalid=true and storenum<".$stock_remind." and isout=0 and customer_id=".$customer_id;
			//echo $query_stock1;
			$result_stock1 = mysql_query($query_stock1) or die('Query failed: ' . mysql_error());
			$stock_mun1 = mysql_num_rows($result_stock1);
			while ($row_stock1 = mysql_fetch_object($result_stock1)) {
				$stock_pid1 = $row_stock1->id;
				if(!empty($stock_pidarr)){
					$stock_pidarr=$stock_pidarr."_".$stock_pid1;
				}else{
					$stock_pidarr=$stock_pid1;
				}
				
			}
			
			$query_stock2="select id,propertyids,storenum from weixin_commonshop_products where isvalid=true and isout=0 and storenum>".$stock_remind." and customer_id=".$customer_id;
			$result_stock2 = mysql_query($query_stock2) or die('Query failed: ' . mysql_error());
			$stock_mun2=0;
			while ($row_stock2 = mysql_fetch_object($result_stock2)) {
				$stock_pid = $row_stock2->id;			
				$stock_storenum = $row_stock2->storenum;			
				$stock_propertyids = $row_stock2->propertyids;			
				if(!empty($stock_propertyids)){
				   $query_stock3="SELECT * FROM weixin_commonshop_product_prices WHERE storenum<".$stock_remind." and product_id='".$stock_pid."' limit 0,1";
				   //echo  $query_stock3;
				   $result_stock3 = mysql_query($query_stock3) or die('Query failed: ' . mysql_error());
				   $result_stock3_mun1 = mysql_num_rows($result_stock3);
				   while ($row_stock3 = mysql_fetch_object($result_stock3)) {
						$stock_pid2 = $row_stock3->product_id;
					}
				   if($result_stock3_mun1 !=0){
					   $stock_mun2=$stock_mun2 + 1;
					   if(!empty($stock_pidarr)){
							$stock_pidarr=$stock_pidarr."_".$stock_pid2;
						}else{
							$stock_pidarr=$stock_pid2;
						}
				   }				   
				}
			}
			$stock_mun=$stock_mun1+$stock_mun2; 
			
		   ?>
		   <div class="div_line_item"  onclick="show_stock('<?php echo $customer_id_en; ?>','<?php echo $stock_pidarr; ?>');">
		      库存提醒: 已有<span style="padding-left:10px;color:red;font-size:18px;font-weight:bold"><?php echo $stock_mun; ?></span>个商品库存不足了
		   </div>
		</div>
<div id="iframe_page">
	<div class="iframe_content">
			<link href="css/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/shop.js"></script>
	<div class="r_nav">
		<ul>
			<li id="auth_page0" class=""><a href="base.php?customer_id=<?php echo $customer_id_en; ?>">基本设置</a></li>
			<li id="auth_page1" class="cur"><a href="fengge.php?customer_id=<?php echo $customer_id_en; ?>">风格设置</a></li>
			<li id="auth_page2" class=""><a href="defaultset.php?customer_id=<?php echo $customer_id_en; ?>&default_set=1">首页设置</a></li>
			<li id="auth_page3" class=""><a href="product.php?customer_id=<?php echo $customer_id_en; ?>">产品管理</a></li>
			<li id="auth_page4" class=""><a href="order.php?customer_id=<?php echo $customer_id_en; ?>&status=-1">订单管理</a></li>
			<?php if($is_supplierstr){?><li id="auth_page5" class=""><a href="supply.php?customer_id=<?php echo $customer_id_en; ?>">供应商</a></li><?php }?>
			<?php if($is_distribution){?><li id="auth_page6" class=""><a href="agent.php?customer_id=<?php echo $customer_id_en; ?>">代理商</a></li><?php }?>
			<li id="auth_page7" class=""><a href="qrsell.php?customer_id=<?php echo $customer_id_en; ?>">推广员</a></li>
			<li id="auth_page8" class=""><a href="customers.php?customer_id=<?php echo $customer_id_en; ?>">顾客</a></li>
		    <li id="auth_page9"><a href="shops.php?customer_id=<?php echo $customer_id_en; ?>">门店</a></li>
			<?php if($isOpenPublicWelfare){?><li id="auth_page10"><a href="publicwelfare.php?customer_id=<?php echo $customer_id_en; ?>">公益基金</a></li><?php }?>
			
		</ul>
	</div>
<script language="javascript">$(document).ready(shop_obj.skin_init);</script>
<div id="skin" class="r_con_wrap">
  <!--  <span style="color:red;height:30px;">注：风格10～18还在调试中</span>-->
	<ul>
	    <?php 
		  $query="select id,name,imgurl from weixin_commonshop_templates where isvalid=true";

		  //if($is_shopgeneral and $is_samelevel==0){
		  if($is_shopgeneral and $is_samelevel==0 and $general_template_id>0){
		  
		     //总部模板只有37号
		     $query= $query." and id=".$general_template_id;
		  }
		  
		  $result = mysql_query($query) or die('Query failed: ' . mysql_error());
		  while ($row = mysql_fetch_object($result)) {
		     $tid=$row->id;
			 $tname = $row->name;
			 $imgurl = $row->imgurl;
		?>
		<li class="<?php if($template_id==$tid){?>cur<?php } ?>">
			<div class="item" sid="<?php echo $tid; ?>" title="点击选择微商城风格">
				<div class="img"><img src="<?php echo $imgurl; ?>"></div>
				<div class="title"><?php echo $tname; ?>
				</div>
			</div>
		</li>
		<?php 
		} 
		?>
		
			</ul>
	<div class="clear"></div>
</div>	</div>
<div>

<?php 

mysql_close($link);
?>
</div></div></body></html>