<?php
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]1
require('../../../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../common/utility_shop.php');

require('../../../proxy_info.php');

mysql_query("SET NAMES UTF8");
require('../../../auth_user.php');

$pagenum = 1;
$condition = '';
if(!empty($_GET["pagenum"])){
   $pagenum = $_GET["pagenum"];
}
/*if(!empty($_GET["condition"])){		//筛选条件
   $condition = $_GET["condition"];
   
}*/
$search_name="";
if(!empty($_GET["search_name"])){
    $search_name = $configutil->splash_new($_GET["search_name"]);
}
$search_phone="";
if(!empty($_GET["search_phone"])){
    $search_phone = $configutil->splash_new($_GET["search_phone"]);
}
$search_user_id="";
if(!empty($_GET["search_user_id"])){
    $search_user_id = $configutil->splash_new($_GET["search_user_id"]);
}
if(!empty($_POST["search_user_id"])){
    $search_user_id = $configutil->splash_new($_POST["search_user_id"]);
}

$begintime = '';
if(!empty($_GET['begintime'])){
	$begintime = urldecode($_GET['begintime']);
	file_put_contents("0207.txt","begintime=====".$begintime."\r\n",FILE_APPEND);
}
$endtime = '';
if(!empty($_GET['endtime'])){
	$endtime = urldecode($_GET['endtime']);
	file_put_contents("0207.txt","endtime=====".$endtime."\r\n",FILE_APPEND);
}

$start = ($pagenum-1) * 20;
$end = 20;

//查询粉丝详情
  $query="select id,name,weixin_name,weixin_headimgurl,fromw,phone,parent_id,generation,province,city ,createtime,has_change from weixin_users where customer_id=".$customer_id." and isvalid=true  ";
 

 
if(!empty($search_name)){	
		   
	$query2 = $query2." and (name like '%".$search_name."%' or weixin_name like '%".$search_name."%')";
}
				 
if(!empty($search_phone)){
	
	$query2 = $query2." and phone like '%".$search_phone."'";
}
if(!empty($search_user_id)){
	
	$query2 = $query2." and id  like '%".$search_user_id."%'";
}
if(!empty($begintime)){
	$query2 = $query2." and UNIX_TIMESTAMP(createtime)>=".strtotime($begintime);
}
if(!empty($endtime)){
	$query2 = $query2." and UNIX_TIMESTAMP(createtime)<=".strtotime($endtime);
}

file_put_contents("0207.txt","query2=====".$query2."\r\n",FILE_APPEND);

	$query_user = $query.$query2.' order by id desc limit '.$start.",".$end."";

file_put_contents("0207.txt","query_user=====".$query_user."\r\n",FILE_APPEND);

$query_num = 'select count(1) as rcount from weixin_users where isvalid=true and customer_id='.$customer_id; 
$query_num = $query_num.$query2;
$result_num = mysql_query($query_num) or die('Query failed_num: ' . mysql_error());
while($row = mysql_fetch_object($result_num)) {
	$rcount_num =$row->rcount;
} 
 
$page=ceil($rcount_num/$end);

 

	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>粉丝管理</title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/contentblue.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content<?php echo $theme; ?>.css">
<link rel="stylesheet" type="text/css" href="../../Common/css/Product/product.css"><!--内容CSS配色·蓝色-->
<script type="text/javascript" src="../../../common/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../common/js_V6.0/jscolor.js"></script><!--拾色器js-->
<script type="text/javascript" src="../../../js/WdatePicker.js"></script> 

<style>
.aright {    
    margin-right:5px!important;;
}
.left{
	
    margin-top: 10px;
    padding-left: 20px;
    font-size: 14px;
    color: #2eade8;
    background-image: url(../../../common/images_V6.0/table_icon/icon01.png);
    background-repeat: no-repeat;
    background-position: left 0%;
    margin-left: 20px;
}
#caozuo a img{
	width: 18px;
    height: 18px;
	vertical-align: baseline;	
}
#caozuo{
	height: 80px;
	padding-top: 20px !important;
    padding-bottom: 20px !important;
	
}
.WSY_table a{
	color:#06a7e1;
	
}	
.time {
	background-color: #FAFAFA!important;
    width: 140px!important;
    color: black!important;
	border:solid 1px #ccc !important;
}	
</style>
</head>

<body>
<!--内容框架开始-->
<div class="WSY_content" id="WSY_content_height">

       <!--列表内容大框开始-->
	<div class="WSY_columnbox">	
	<!--头部导航start-->
	<?php require('head.php');?>	
	<!--头部导航end-->
	
	
    <!--产品管理代码开始-->
    <div class="WSY_data">
    	<div class="WSY_agentsbox">
			<form class="search" id="search_form">		
			<!-- <div class="WSY_search_q">					
					<li class="left"><a>粉丝管理</a></li>	
			</div> -->	
				<div class="WSY_search_q search" id="search_form">
				
					<li>粉丝编号： <input type=text name="search_user_id" id="search_user_id" value="<?php echo $search_user_id; ?>" style="width:150px;" /></li>		
					<li>姓名： <input type=text name="search_name" id="search_name" value="<?php echo $search_name; ?>" style="width:150px;" /></li>	
					<li>电话： <input type=text name="search_phone" id="search_phone" value="<?php echo $search_phone; ?>"  style="width:150px;" /></li>
					<li>时间：					
						<span class="time">
							<input class="date_picker time" type="text" name="begintime" id="begintime" value="<?php echo $begintime; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});">
							<span class="om-calendar-trigger"></span>
						</span>
						-
						<span class="time" >
							<input class="date_picker time" type="text" name="endtime" id="endtime" value="<?php echo $endtime; ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'});">
							
						</span>&nbsp;  
					</li>
					<li class="WSY_bottonliss"><input style="padding-right:0" type="submit" class="search_btn"  onclick="searchForm();" value="搜索"></li>
					
					
				</div>
				
			</form> 

            <table width="97%" class="WSY_table" id="WSY_t1">
			  <thead class="WSY_table_header">
					
					<th width="7%" nowrap="nowrap">粉丝编号</th>
					<th width="7%" nowrap="nowrap">姓名</th>
					<th width="5%" nowrap="nowrap">角色</th>
					<th width="5%" nowrap="nowrap">所在省</th>
					<th width="5%" nowrap="nowrap">所在市</th>
					<th width="5%" nowrap="nowrap">钱包</th>
					<th width="7%" nowrap="nowrap">已完成订单金额</th>
					<th width="7%" nowrap="nowrap">已完成订单数量</th>
					<th width="8%" nowrap="nowrap">最近购买时间</th>
					<th width="8%" nowrap="nowrap">来源</th>	
					<th width="8%" nowrap="nowrap">推荐人</th>
					<th width="6%" nowrap="nowrap">创建时间</th>
					<th width="10%" nowrap="nowrap" class="last">操作</th>
			  </thead>
			  
			<?php 
			$query = "select is_ncomission,exp_name from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
			$result = mysql_query($query) or die('w21 Query failed: ' . mysql_error());
			$is_ncomission  =0;	//是否开启3*3分佣
			$exp_name       ="粉丝";	
			while ($row = mysql_fetch_object($result)) {
				$is_ncomission   =$row->is_ncomission; 
				$exp_name        =$row->exp_name; 
			}
			$user_id = -1;
			$user_weixin_headimgurl = "";
			$fromw = 1;
			$username = "";
			$weixin_name = "";
			$userphone = "";
			$parent_id = -1;
			$generation = 0;
			$province ="";
			$city ="";
			$createtime ="";
			$parent_weixin_name = ''; 
			$result_user=mysql_query($query_user)or die('Query failed'.mysql_error());
			while($row=mysql_fetch_object($result_user)){
					$user_id = $row->id;
					$user_weixin_headimgurl = $row->weixin_headimgurl;
					$fromw = $row->fromw;
					$username = $row->name;
					$weixin_name = $row->weixin_name;
					$userphone = $row->phone;
					$parent_id = $row->parent_id;
					$generation = $row->generation;
					$province =$row->province;
					$city =$row->city;
					$createtime =$row->createtime;
					$has_change = $row->has_change;
						
					$generation=$generation."代";
					if($fromw==1){
						$laiyuan = '主动关注';
					}elseif($fromw==2){
						$laiyuan = '朋友圈';
					}elseif($fromw==3){
						$laiyuan = '二维码';
					}
					$commision_level = 0;
					$query2="select status,commision_level from promoters where user_id=".$user_id." and isvalid=true limit 1";
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					while ($row2 = mysql_fetch_object($result2)) {
					    $status 		 = $row2->status;	
					    $commision_level = $row2->commision_level;	
					}
					
					$exp_name_user = "粉丝";
					if(0 < $commision_level && $status == 1){
						$exp_name_user = $exp_name;
					}
					
					/* 查询个人总VP值 start */
					$my_vpscore     =  0; //个人vp值
					$query_vp = "SELECT my_vpscore from weixin_user_vp where isvalid=true and customer_id=" . $customer_id . " and user_id=" . $user_id . " limit 0,1";
					$result_vp = mysql_query($query_vp) or die('W447 Query failed: ' . mysql_error());
					while ($row_vp = mysql_fetch_object($result_vp)) {
						$my_vpscore  	 = $row_vp->my_vpscore;
					}
					/* 查询个人总VP值 end */
					
					//开启3*3分佣
					if(1 == $is_ncomission){
						//3*3等级推广员自定义名称
						$query_commisions="select exp_name from weixin_commonshop_commisions where isvalid=true and customer_id=".$customer_id." and level=".$commision_level." limit 0,1";
						$result_commisions = mysql_query($query_commisions) or die('w94 Query failed: ' . mysql_error());
						while ($row = mysql_fetch_object($result_commisions)) {	
							$exp_name = $row->exp_name; //3*3等级推广员自定义名称 			
						}
					}
					
					//查询已完成订单金额
					$sum_totalprice = 0;
					$query="select sum(totalprice) as sum_totalprice from weixin_commonshop_orders where paystatus=1 and status=1 and sendstatus=2 and isvalid=true and customer_id=".$customer_id." and user_id=".$user_id."";
					$result = mysql_query($query) or die('Query failed_1: ' . mysql_error());  
					 //  echo $query;
					while ($row = mysql_fetch_object($result)) {
					   $sum_totalprice = $row->sum_totalprice;
					   break;
					}
					$sum_totalprice = round($sum_totalprice,2);
					
					//查询已完成订单数量
					$order_count = 0;
					$query="select count(1) as order_count from weixin_commonshop_orders where  status=1  and isvalid=true and customer_id=".$customer_id." and user_id=".$user_id."";
					$result = mysql_query($query) or die('Query failed_2: ' . mysql_error());  
					 //  echo $query;
					while ($row = mysql_fetch_object($result)) {
					   $order_count = $row->order_count;
					   break;
					}
					//查询最近购买时间
					$buy_time = '';
					$query="select createtime from weixin_commonshop_orders where  isvalid=true and customer_id=".$customer_id." and user_id=".$user_id."";
					$result = mysql_query($query) or die('Query failed_3: ' . mysql_error());  
					 //  echo $query;
					while ($row = mysql_fetch_object($result)) {
					   $buy_time = $row->createtime;
					   break;
					}
					//查自己的推广员
					
					if($parent_id>0){
						$query="select weixin_name from weixin_users where isvalid=true and customer_id=".$customer_id." and id=".$parent_id."";
						$result=mysql_query($query)or die('Query failed'.mysql_error());
						while($row=mysql_fetch_object($result)){
							$parent_weixin_name = $row->weixin_name;
						}
					}else{
							$parent_weixin_name = '';
					}
					
					$query2="select isAgent,is_consume,generation,qrsell_orderothers from promoters where isvalid=true and user_id=".$user_id;
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					$isAgent = -1; //默认为-1 代表不是推广员
					$is_consume = 0;
					
					$qrsell_orderothers = "";
					while ($row2 = mysql_fetch_object($result2)) {
						$isAgent = $row2->isAgent;	//判断 0为推广员 1为代理商 2为顶级推广员
						$is_consume = $row2->is_consume;	//判断 0:不是无限级奖励 1:无限级奖励
						
						$qrsell_orderothers = $row2->qrsell_orderothers;	//推广员申请自定义自动
						break;
					}
					
					$query2="select all_areaname from weixin_commonshop_team_area where isvalid=true and area_user=".$user_id." and customer_id=".$customer_id;//团队奖励 此人分配的区域
					$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
					$all_areaname = "";//团队奖励 分配区域的全称
					while ($row2 = mysql_fetch_object($result2)) {
					    $all_areaname = $row2->all_areaname;
						break;
					}
					$query4="select a_name,b_name,c_name,d_name from weixin_commonshop_shareholder where isvalid=true and customer_id=".$customer_id." limit 0,1";
					$result4 = mysql_query($query4);
					while($row4 = mysql_fetch_object($result4)){
						$a_name=$row4->a_name;
						$b_name=$row4->b_name;
						$c_name=$row4->c_name;
						$d_name=$row4->d_name;
					}
					$consume_name ="";
					if($is_team==1 && $is_shareholder==0){
						if($is_consume>0){
							$consume_name = "(无限级奖励)";
						}
					}else if($is_shareholder==1){ 
						switch($is_consume){
							case 1: $consume_name = "(股东分红-".$d_name.")"; break;
							case 2: $consume_name = "(股东分红-".$c_name.")"; break;
							case 3: $consume_name = "(股东分红-".$b_name.")"; break;
							case 4: $consume_name = "(股东分红-".$a_name.")"; break;
						}
					}
					$agentname="";
					switch($isAgent){
						case 1:
							$agentname = "(代理商)";
							break;
						case 2:
							$agentname = "(顶级".$exp_name.")";
							break;
						case 3:
							$agentname = "(供应商)";
							break;
						case 5:
							$agentname = "(区代)";
							break;
						case 6:
							$agentname = "(市代)";
							break;
						case 7:
							$agentname = "(省代)";
							break;
						
					}
					/*** 钱包零钱 start***/
					$balance = 0;
					$query_moneybag = "SELECT balance FROM moneybag_t WHERE isvalid=true AND user_id=".$user_id." AND customer_id=".$customer_id." LIMIT 1";
					$result_moneybag= mysql_query($query_moneybag) or die('query_moneybag failed 37: ' . mysql_error());
					while( $row_moneybag = mysql_fetch_object($result_moneybag) ){
						$balance = $row_moneybag->balance;
					}
					$balance = substr(sprintf("%.3f", $balance),0,-1); 
					/*** 钱包零钱 end***/
					/*** 购物币 start***/
					$currency 		= 0;
					$currencyCustom = "";
					$isOpenCurrency = 0;
					$query = "SELECT id,currency FROM weixin_commonshop_user_currency WHERE isvalid=true AND user_id=$user_id LIMIT 1";
					$result= mysql_query($query) or die('Query failed 23: ' . mysql_error());
					while( $row = mysql_fetch_object($result) ){
					    $id       = $row->id;
					    $currency = round($row->currency,2);

					}
					$query_currency = "SELECT custom,isOpen FROM weixin_commonshop_currency WHERE isvalid=TRUE AND customer_id=".$customer_id."  LIMIT 1";
					$result_currency = mysql_query($query_currency) or die('query_currency failed 28: ' . mysql_error());
					while($row_currency = mysql_fetch_object($result_currency)){						
						$currencyCustom	= $row_currency->custom;
						$isOpenCurrency = $row_currency->isOpen;
					}
					$currency = substr(sprintf("%.3f", $currency),0,-1); 
					/*** 购物币 end***/
			?>
			   <tr>
					<td  style="padding-top: 10px;padding-bottom: 5px;">
						<span style="display:block"><img src="<?php echo $user_weixin_headimgurl?>" style="width:50px;height:50px;"></span>
						<span style="display:block;margin-top: 5px;">ID:<?php echo $user_id;?></span>
						
					</td>
			   
					<td  >
					<?php echo $weixin_name."(".$username.")"; ?></br>
					<?php echo $userphone; ?>
					</td>
					 <td >
						<?php if($commision_level>0 && $status == 1){ ?><span style="display:block"><?php echo $generation;?></span><?php } ?>
						<span style="display:block"><?php echo $exp_name_user;?></span>
						<span style="display:block"><?php echo $agentname;?></span>
						<span style="display:block"><?php echo $all_areaname;?></span>
						<span style="display:block"><?php echo $consume_name;?></span>
					</td>
					<td><?php echo $province ;?></td>
					<td><?php echo $city ;?></td>		
					<td >
						<dt><span>vp：</span><a href="../promoter/vp_detail.php?customer_id=<?php echo $customer_id_en ;?>&user_id=<?php echo passport_encrypt($user_id) ;?>&pagenum=<?php echo $pagenum;?>"><?php echo $my_vpscore ;?></a></dt>
						<dt><span>零钱：</span><a href="../../Base/moneybag/user_detail.php?customer_id=<?php echo $customer_id_en;?>&user_id=<?php echo $user_id;?>">￥<?php echo $balance;?></a></dt>
						
						<dt><span><?php echo $currencyCustom;?>：</span><a href="../../Base/pay_set/pay_currency_log.php?customer_id=<?php echo $customer_id_en;?>&promoter=<?php echo $user_id;?>"><?php echo $currency;?></a></dt>
						
					</td>
					<td ><?php echo $sum_totalprice ;?></td>
					<td ><?php echo $order_count ;?></td>
					<td ><?php echo $buy_time ;?></td>
					<td ><?php echo $laiyuan ;?></td>
					<td ><?php echo $parent_weixin_name ;?></td>
					<td ><?php echo $createtime ;?></td>
					<td id="caozuo">				      
						<a href="change_role.php?customer_id=<?php echo $customer_id_en ;?>&fromw=<?php echo $fromw ;?>&user_id=<?php echo $user_id ;?>&isAgent=<?php echo $isAgent ;?>&pagenum=<?php echo $pagenum;?>&parent_id=<?php echo $parent_id;?>">
						<img src="../../../common/images_V6.0/operating_icon/icon05.png" align="absmiddle" alt="编辑分销商" title="编辑分销商"></a>
						<?php if(1 == $is_ncomission){?>
						<a href="../promoter/change_pro_level.php?customer_id=<?php echo $customer_id_en ;?>&user_id=<?php echo passport_encrypt($user_id);?>&pagenum=<?php echo $pagenum;?>&from=fans"><img src="../../../common/images_V6.0/operating_icon/icon52.png" align="absmiddle" alt="编辑分销商等级" title="编辑分销商等级"></a>
						<?php }?>
						<?php if($isAgent == -1 && $has_change == 0){ ?>
							<a onclick="if(confirm('粉丝关系只能手动调整一次,调整后关系将会锁定，是否确定修改？') == false) return false;"
							href="change_relation.php?customer_id=<?php echo $customer_id_en ;?>&fromw=<?php echo $fromw ;?>&user_id=<?php echo $user_id ;?>&isAgent=<?php echo $isAgent ;?>&pagenum=<?php echo $pagenum;?>&old_parent_id=<?php echo $parent_id; ?>">
							<img src="../../../common/images_V6.0/operating_icon/icon15.png" align="absmiddle" alt="更改关系" title="更改关系"></a>							
						<?php }?>
						<a href="../promoter/qrsell_account_detail.php?customer_id=<?php echo $customer_id_en ?>&user_id=<?php echo $user_id;?>"><img src="../../../common/images_V6.0/operating_icon/icon15.png" align="absmiddle" alt="更改关系详情" title="更改关系详情"></a>
						<a href="../promoter/reset_paypassword.php?customer_id=<?php echo $customer_id_en;?>&user_id=<?php echo $user_id;?>" onclick="if(!confirm(&#39;重置后支付密码为：888888。继续？&#39;)){return false};"><img src="../../../common/images_V6.0/operating_icon/icon61.png" align="absmiddle" alt="重置支付密码" title="重置支付密码"></a> 
					</td>	
				</tr>
				<?php } ?>
			</table>
		</div>
        <!--翻页开始-->
        <div class="WSY_page">
        	
        </div>
        <!--翻页结束-->
    </div>
    <!--产品管理代码结束-->
	</div>

	<div style="width:100%;height:20px;"></div>
</div>
<?php 

mysql_close($link);
?>
<!--内容框架结束-->
<script src="../../../js/fenye/jquery.page1.js"></script>
<script>
  var search_name = '<?php echo $search_name; ?>'; 
  var search_phone = '<?php echo $search_phone; ?>';  
  var search_user_id = '<?php echo $search_user_id; ?>';  
  var pagenum = <?php echo $pagenum ?>;
  var count =<?php echo $page ?>;//总页数
  	//pageCount：总页数
	//current：当前页
	$(".WSY_page").createPage({
        pageCount:count,
        current:pagenum,
        backFn:function(p){
		 document.location= "fans.php?customer_id=<?php echo $customer_id_en; ?>&pagenum="+p+"&search_name="+search_name+"&search_phone="+search_phone+"&search_user_id="+search_user_id;
	   }
    });

  var pagenum = <?php echo $pagenum ?>;
   var page = <?php echo $page ?>;
  function jumppage(){
	var a=parseInt($("#WSY_jump_page").val());
	if((a<1) || (a==pagenum) || (a>page) || isNaN(a)){
		return false;
	}else{
	document.location= "fans.php?customer_id=<?php echo $customer_id_en?>&pagenum="+a+"&search_name="+search_name+"&search_phone="+search_phone+"&search_user_id="+search_user_id;
	}
  }
    function searchForm(){
		
		search_name = document.getElementById("search_name").value; 
		search_phone = document.getElementById("search_phone").value; 
		search_user_id = document.getElementById("search_user_id").value;
		begintime = document.getElementById('begintime').value;
		endtime = document.getElementById('endtime').value; 
		document.location= "fans.php?pagenum=1&search_name="+search_name+"&search_phone="+search_phone+"&search_user_id="+search_user_id+"&customer_id=<?php echo $customer_id_en;?>&begintime="+begintime+"&endtime="+endtime;
  }
</script>

</body>
</html>