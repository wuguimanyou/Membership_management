<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php'); //配置
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
mysql_query("SET NAMES UTF8");

$customer_id = -1;
$user_id = -1;
$order_type = '';
$order_num = 0;
$order_num_end = 0;
$array = array();
$data = array();
$batchcode_arr = array();
$n = 0;

$customer_id = $_POST["customer_id"];
$user_id = $_POST["user_id"];
$order_type = $_POST["order_type"];			//订单类型
$order_num = $_POST["order_num"];
$order_num_end = $_POST["order_num_end"];


switch($order_type){//判断获取数据的类型
	case 'all':
		$i = 0;
		$query = "select batchcode from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." group by batchcode desc limit ".$order_num.",".$order_num_end;	//获取订单号
		$result = mysql_query($query) or die('Query failed'.mysql_error());
		while($row = mysql_fetch_object($result)){
			$batchcode_arr[$i] = $row->batchcode;
			$i++;
		}
		
		break;
		
	case 'daifukuan':
		$i = 0;
		$query = "select batchcode from weixin_commonshop_orders where isvalid=true and paystatus=0 and status!=-1 and customer_id=".$customer_id." and user_id=".$user_id." group by batchcode desc limit ".$order_num.",".$order_num_end;	//获取订单号
		$result = mysql_query($query) or die('Query failed'.mysql_error());
		while($row = mysql_fetch_object($result)){
			$batchcode_arr[$i] = $row->batchcode;
			$i++;
		}
		
		break;
		
	case 'daifahuo':
		$i = 0;
		$query = "select batchcode from weixin_commonshop_orders where isvalid=true and paystatus=1 and status!=-1 and sendstatus=0 and customer_id=".$customer_id." and user_id=".$user_id." group by batchcode desc limit ".$order_num.",".$order_num_end;	//获取订单号
		$result = mysql_query($query) or die('Query failed'.mysql_error());
		while($row = mysql_fetch_object($result)){
			$batchcode_arr[$i] = $row->batchcode;
			$i++;
		}
		
		break;
		
	case 'daishouhuo':
		$i = 0;
		$query = "select batchcode from weixin_commonshop_orders where isvalid=true and paystatus=1 and status!=-1 and sendstatus=1 and customer_id=".$customer_id." and user_id=".$user_id." group by batchcode desc limit ".$order_num.",".$order_num_end;	//获取订单号
		$result = mysql_query($query) or die('Query failed'.mysql_error());
		while($row = mysql_fetch_object($result)){
			$batchcode_arr[$i] = $row->batchcode;
			$i++;
		}
		
		break;
		
	case 'yiwancheng':
		$i = 0;
		$query = "select batchcode from weixin_commonshop_orders where isvalid=true and paystatus=1 and status!=-1 and sendstatus=2 and customer_id=".$customer_id." and user_id=".$user_id." group by batchcode desc limit ".$order_num.",".$order_num_end;	//获取订单号
		$result = mysql_query($query) or die('Query failed'.mysql_error());
		while($row = mysql_fetch_object($result)){
			$batchcode_arr[$i] = $row->batchcode;
			$i++;
		}
		
		break;
		
	case 'shouhouzhong':
		$i = 0;
		$query = "select batchcode from weixin_commonshop_orders where isvalid=true and status=0 and ((sendstatus=2 and aftersale_type!=0 and aftersale_state in (1,2,3)) or sendstatus=3 or sendstatus=5) and customer_id=".$customer_id." and user_id=".$user_id." group by batchcode desc limit ".$order_num.",".$order_num_end;	//获取订单号
		$result = mysql_query($query) or die('Query failed'.mysql_error());
		while($row = mysql_fetch_object($result)){
			$batchcode_arr[$i] = $row->batchcode;
			$i++;
		}
		
		break;
		
	default:
		break;
}


		$b_length = count($batchcode_arr);
		for($j=0;$j<$b_length;$j++){
			$batchcode = $batchcode_arr[$j];
			if($order_type == 'all'){
				$query2 = "select status,paystatus,sendstatus,return_type,aftersale_type,aftersale_state,is_discuss from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and batchcode=".$batchcode." limit 1";
				$result2 = mysql_query($query2) or die('query failed2'.mysql_error());
				while($row2 = mysql_fetch_object($result2)){
					$status = $row2->status;					//订单状态
					$paystatus = $row2->paystatus;				//支付状态
					$sendstatus = $row2->sendstatus;			//发货状态
					$return_type = $row2->return_type;			//退货类型
					$aftersale_type = $row2->aftersale_type;	//售后类型
					$aftersale_state = $row2->aftersale_state;	//售后状态
					$is_discuss = $row2->is_discuss;
				}

				$order_status = '';		//订单状态
				$order_btn = -1;		//显示按钮类型
						/* 判断订单状态 start*/
						if($status==0 || $status==1){
							if($paystatus==1){
								if($sendstatus==0){
									$order_status = '待发货';
									$order_btn = 1;
									$order_str = 'fahuo';
								}else if($sendstatus==1){
									$order_status = '待收货';
									$order_btn = 2;
									$order_str = 'shouhuo';
								}else if($sendstatus==2 && $aftersale_type==2){
									if($aftersale_state<3){
										$order_status = '退货中';
										$order_btn = 3;
										$order_str = 'houzhong';
									}else if($aftersale_state==3){
										$order_status = '商家已驳回申请';
										$order_btn = 4;
										$order_str = 'wancheng';
									}else{
										$order_status = '退货成功';
										$order_btn = 4;
										$order_str = 'wancheng';
									}
								}else if($sendstatus==2 && $aftersale_type==3){
									if($aftersale_state<3){
										$order_status = '换货中';
										$order_btn = 3;
										$order_str = 'houzhong';
									}else if($aftersale_state==3){
										$order_status = '商家已驳回申请';
										$order_btn = 4;
										$order_str = 'wancheng';
									}else{
										$order_status = '换货成功';
										$order_btn = 4;
										$order_str = 'wancheng';
									}
								}else if($return_type==0){
									if($sendstatus==3){
										$order_status = '退款中';
										$order_btn = 5;
										$order_str = 'houzhong';
									}else{
										$order_status = '退款成功';
										$order_btn = 6;
										$order_str = 'wancheng';
									}
								}else if($return_type==1){
									if($sendstatus==3){
										$order_status = '退货中';
										$order_btn = 5;
										$order_str = 'houzhong';
									}else{
										$order_status = '退货成功';
										$order_btn = 6;
										$order_str = 'wancheng';
									}
								}else if($return_type==2){
									if($sendstatus==3){
										$order_status = '换货中';
										$order_btn = 5;
										$order_str = 'houzhong';
									}else{
										$order_status = '换货成功';
										$order_btn = 6;
										$order_str = 'wancheng';
									}
								}else if($sendstatus==2){
									$order_status = '交易完成';
									$order_btn = 4;
									$order_str = 'wancheng';
								}else if($sendstatus==4){
									$order_status = '退货成功';
									$order_btn = 4;
									$order_str = 'wancheng';
								}else if($sendstatus==5){
									$order_status = '退款中';
									$order_btn = 6;
									$order_str = 'houzhong';
								}else if($sendstatus==6){
									$order_status = '退款成功';
									$order_btn = 6;
									$order_str = 'wancheng';
								}
							}else{
								$order_status = '待付款';
								$order_btn = 7;
								$order_str = 'fukuan';
							}
						}else{
							$order_status = '已取消订单';
							$order_str = 'wancheng';
						}
					}else if($order_type == 'daifukuan'){
						$order_status = '待付款';
						$order_str = 'fukuan';
					}else if($order_type == 'daifahuo'){
						$order_status = '待发货';
						$order_str = 'fahuo';
					}else if($order_type == 'daishouhuo'){
						$order_status = '待收货';
						$order_str = 'shouhuo';
					}else if($order_type == 'yiwancheng'){
						$order_status = '交易成功';
						$order_str = 'wancheng';
				/* 判断订单是否已评价 start*/
				$dcount = 0;
				$dcount1 = 0;
				$query_c = "select count(1) as dcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and batchcode=".$batchcode." and is_discuss=0";
				$result_c = mysql_query($query_c) or die('query_c failed2'.mysql_error());
				while($row_c = mysql_fetch_object($result_c)){
					$dcount = $row_c->dcount;
				}
				if($dcount==0){
					$query_c = "select count(1) as dcount from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and batchcode=".$batchcode." and is_discuss=1";
					$result_c = mysql_query($query_c) or die('query_c failed2'.mysql_error());
					while($row_c = mysql_fetch_object($result_c)){
						$dcount1 = $row_c->dcount;
					}
				}
				/* 判断订单是否已评价 end*/
			}else if($order_type == 'shouhouzhong'){
				$order_status = '售后中';
				$order_str = 'houzhong';
			}
			/* 判断订单状态 end*/
			/* 店铺 start*/
			$sql = "select pid from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and batchcode=".$batchcode." limit 1";
			$res = mysql_query($sql) or die('sql failed'.mysql_error());
			while($row_s = mysql_fetch_object($res)){
				$pid = $row_s->pid;							//商品ID
			}
			$sql1 = "select name from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
			$res1 = mysql_query($sql1) or die('query failed1'.mysql_error());
			while($row_s1 = mysql_fetch_object($res1)) {
				$shop_name = $row_s1->name;					//商家名
			}
			$sql2 = "select is_supply_id from weixin_commonshop_products where customer_id=".$customer_id." and id=".$pid;
			$res2 = mysql_query($sql2) or die('sql failed2'.mysql_error());
			while($row_s2 = mysql_fetch_object($res2)){
				$is_supply_id = $row_s2->is_supply_id;		//供应商ID
			}
			$sql3 = "select id,shopName from weixin_commonshop_applysupplys where user_id=".$is_supply_id;
			$res3 = mysql_query($sql3) or die('sql failed3'.mysql_error());
			while($row_s3 = mysql_fetch_object($res3)){
				$supply_id = $row_s3->id;					//店铺ID
				$shop_name = $row_s3->shopName;				//店铺名
			}
			/* 店铺 end*/
			
			// $tmp=array(
				// "order_btn"=>$order_btn,
				// "supply_id"=>$supply_id,
				// "shop_name"=>$shop_name
			// );
			
			$data[$n][0][0] = $order_btn;		//按钮类型
			$data[$n][0][1] = $supply_id;		//供应商ID
			$data[$n][0][2] = $shop_name;		//店铺名
			$data[$n][0][3] = $order_status;	//订单状态
			$data[$n][0][4] = $batchcode;		//订单号
			$data[$n][0][5] = $order_str;		//订单详情页
			if($order_type == 'yiwancheng'){
				$data[$n][0][6] = $dcount;		//是否评价
				$data[$n][0][7] = $dcount1;		//是否追加评价
			}
			
			$totalcount = 0;	//商品总数量
			$totalprice = 0;	//商品总价格
			if($order_type == 'shouhouzhong'){
				$query3 = "select pid,rcount,totalprice,prvalues from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and batchcode=".$batchcode." and status=0 and ((sendstatus=2 and aftersale_type!=0 and aftersale_state in (1,2,3)) or sendstatus=3 or sendstatus=5)";
			}else{
				$query3 = "select pid,rcount,totalprice,prvalues from weixin_commonshop_orders where isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." and batchcode=".$batchcode;
			}
			$result3 = mysql_query($query3) or die('query failed3'.mysql_error());
			while($row3 = mysql_fetch_object($result3)){
				$pid = $row3->pid;				//商品ID
				$rcount = $row3->rcount;		//商品数量
				$price = $row3->totalprice;		//商品价格
				$prvalues = $row3->prvalues;	//商品属性
				
				$totalcount = $rcount + $totalcount;
				$totalprice = $price + $totalprice;
				
				$prvstr="";
				if(!empty($prvalues)){
					$prvarr= explode("_",$prvalues);						
					for($i=0;$i<count($prvarr);$i++){
						$prvid = $prvarr[$i];
						if($prvid>0){
							$parent_id = -1;
							$prname = '';
							$query4 = "select name,parent_id from weixin_commonshop_pros where isvalid=true and id=".$prvid;
							$result4 = mysql_query($query4) or die('query failed4'.mysql_error());
							while($row4 = mysql_fetch_object($result4)){
								$parent_id = $row4->parent_id;		//是否子属性
								$prname = $row4->name;				//属性名
							}
							$p_prname = '';
							$query5 = "select name from weixin_commonshop_pros where isvalid=true and id=".$parent_id;
							$result5 = mysql_query($query5) or die('query failed5'.mysql_error());
							while($row5 = mysql_fetch_object($result5)){
								$p_prname = $row5->name;		//属性名
								$prvstr = $prvstr.$p_prname.":".$prname."  ";
							}
						}
					}
				}
						
				$query6 = "select id,name,orgin_price,now_price,is_virtual,default_imgurl from weixin_commonshop_products where isvalid=true and customer_id=".$customer_id." and id=".$pid;
				$result6 = mysql_query($query6) or die('query failed6'.mysql_error());
				while($row6 = mysql_fetch_object($result6)){
					$product_id = $row6->id;						//商品ID
					$product_name = $row6->name;					//商品名
					$product_orgin_price = $row6->orgin_price;		//商品原价
					$product_now_price = $row6->now_price;			//商品现价
					$product_is_virtual = $row6->is_virtual;		//是否虚拟产品
					$product_default_imgurl = $row6->default_imgurl;//商品封面图
				}
				$query7 = "select price,ExpressPrice,rcount from weixin_commonshop_order_prices where isvalid=true and batchcode=".$batchcode;
				$result7 = mysql_query($query7) or die('query failed7'.mysql_error());
				while($row7 = mysql_fetch_object($result7)){
					// $price = $row7->price;
					$ExpressPrice = $row7->ExpressPrice;		//运费
					// $totalcount = $row7->rcount;
				}
				$totalprice = $totalprice + $ExpressPrice;		//商品总价格加上运费
				$tmp2=array(
					"product_default_imgurl"=>$product_default_imgurl,
					"product_name"=>$product_name,
					"product_now_price"=>$product_now_price,
					"prvstr"=>$prvstr,
					"rcount"=>$rcount,
					"totalcount"=>$totalcount,
					"totalprice"=>$totalprice,
					"ExpressPrice"=>$ExpressPrice
				);
				
				array_push($data[$n],$tmp2);
			}
			
			$n++;
		}
		echo json_encode($data);
?>