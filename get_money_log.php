<?php
header("Content-type: text/html; charset=utf-8");     
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../common/utility_fun.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD); 
mysql_select_db(DB_NAME) or die('Could not select database');
//头文件----start
// $customer_id = $_SESSION['customer_id'];			//谁修改了这个上去？说个原因？
// $user_id = $_SESSION['user_id_'.$customer_id];

//头文件----start
require('../common/common_from.php');//2016-0804--qiao(修改)
//头文件----end
//$user_id = 300647;
//头文件----end

$from 		= $configutil->splash_new($_POST["from"]);

switch ( $from ) {
	//查询购物币日志

	case 'currency':
	
		$pagenum = 0;
		if(!empty($_POST['pagenum'])){
			$pagenum = $_POST['pagenum'];
		}	

		$start = ($pagenum-1) * 10;
		$end = 10;
		$type = $configutil->splash_new($_POST["type"]);
		$query = "SELECT id,cost_currency,remark,createtime,type FROM weixin_commonshop_currency_log WHERE isvalid=true AND user_id=".$user_id;
		switch ($type) {
			case 'currency_all':
				$query = $query." ORDER BY createtime desc limit ".$start.",".$end;
				break;
			case 'currency_in':
				$query = $query." AND type=1 ORDER BY createtime desc limit ".$start.",".$end;
				break;
			case 'currency_out':
				$query = $query." AND type=0 ORDER BY createtime desc limit ".$start.",".$end;
				break;				
		}

			$result  = mysql_query($query) or die('Query failed 201: ' . mysql_error());

		//echo $query;die;

			$arr = array();
			$i   = 0;
			while( $row = mysql_fetch_object($result) ){
				$arr[$i]['id']			= $row->id;
				$arr[$i]['type']		= $row->type;
				$arr[$i]['currency'] 	= round($row->cost_currency,2);
				$remark 				= $row->remark;
				$arr[$i]['remark']	 	= $remark;/* mb_substr($remark,4,18,'utf-8'); */
				$arr[$i]['createtime']	= $row->createtime;
				$i++;
			}
			echo json_encode($arr);			
	break;

	case 'moneybag':

		$page = $configutil->splash_new($_POST["page"]);
		
		$pagenum = 20;
		$start = ($page-1)*$pagenum;
		$end = $pagenum;

		$type = $configutil->splash_new($_POST["type"]);

		$query = "SELECT id,createtime,remark,money,type FROM moneybag_log where isvalid=true and user_id=".$user_id;
		switch ($type) {
			case 'moneybag_all':
				$query = $query." ORDER BY createtime desc LIMIT ".$start.",".$end;
				break;
			case 'moneybag_in':
				$query = $query." AND type=0 ORDER BY createtime desc LIMIT ".$start.",".$end;
				break;
			case 'moneybag_out':
				$query = $query." AND type=1 ORDER BY createtime desc LIMIT ".$start.",".$end;
				break;
		}

		$result  = mysql_query($query) or die('Query failed67: ' . mysql_error());  
			$arr = array();
			$i   = 0;
			while( $row = mysql_fetch_object($result) ){
				$arr[$i]['id']			= $row->id;
				$arr[$i]['type']		= $row->type;
				$arr[$i]['money'] 		= cut_num($row->money,2);
				$remark 				= $row->remark;
				$arr[$i]['remark']	 	= mb_substr($remark,0,20,'utf-8');
				$arr[$i]['createtime']	= $row->createtime;
				$i++;
			}
			echo json_encode($arr);	

	break;
		
	case 'out_in':

		$where = $configutil->splash_new($_POST["where"]);
		$id    = $configutil->splash_new($_POST["id"]);
		switch ($where) {
			case 'moneybag':
				$query 	= "SELECT money,createtime,batchcode,pay_style,type,remark FROM moneybag_log where isvalid=true and user_id=$user_id and id=".$id;
				$result = mysql_query($query) or die('Query failed90: ' . mysql_error());  
				$arr = array();
				//$i = 0;
				while( $row = mysql_fetch_object($result) ){
					$money 		= $row->money;
					$remark 	= $row->remark;
					if($remark=='' || $remark==NULL){
						$remark = '';
					}
					$createtime = $row->createtime;
					$batchcode 	= $row->batchcode;
					$pay_style  = $row->pay_style;
					switch ($pay_style) {
						case '0':
							$pay_style = "商城消费";
							break;
						case '1':
							$pay_style = "返佣";
							break;
						case '2':
							$pay_style = "消费返现";
							break;
						case '3':
							$pay_style = "大礼包";
							break;
						case '4':
							$pay_style = "后台零钱充值";
							break;
						case '5':
							$pay_style = "用户提现申请";
							break;							
					}

					$arr['money'] 		= round($money,2);
					$arr['remark']		= $remark;
					$arr['createtime'] 	= $createtime;
					$arr['batchcode'] 	= $batchcode;
					$arr['consume_type']= $pay_style;

				}	
				echo json_encode($arr);

			break;

			case 'currency':
				$query = "SELECT cost_currency,after_currency,createtime,batchcode,class,remark FROM weixin_commonshop_currency_log where id=".$id;
				$result = mysql_query($query) or die('Query failed129: ' . mysql_error());  
				$arr = array();
				$i = 0;
				while( $row = mysql_fetch_object($result) ){
					$consume_way 		= $row->class;
					$remark 			= $row->remark;
					$cost_currency 		= $row->cost_currency;
					$createtime 		= $row->createtime;
					$batchcode 			= $row->batchcode;
					$after_currency		= $row->after_currency;
					switch ($consume_way) {
						case '1':
							$consume_type = "商城消费";
							break;
						case '2':
							$consume_type = "转赠收支";
							break;
						case '3':
							$consume_type = "商城返佣";
							break;
						case '0':
							$consume_type = "购物币充值";
							break;						
					}

					$arr['money'] 		= round($cost_currency,2);
					$arr['remark']		= $remark;
					$arr['createtime'] 	= $createtime;
					$arr['batchcode'] 	= $batchcode;
					$arr['after_money'] = $after_currency;
					$arr['consume_type']= $consume_type;
					

				}	
				echo json_encode($arr);

			break;

			case 'score':
				$arr = array();
				$id    = $configutil->splash_new($_POST["id"]);
				$query = "SELECT score,createtime,remark,type FROM weixin_card_score_records where isvalid=true and id=".$id." LIMIT 1";
				$result= mysql_query($query);
				while( $row = mysql_fetch_object($result) ){
					$arr["score"]		= $row->score;
					$type				= $row->type;
					switch ($type) {
						case '1':
							$arr['consume_type'] = '签到积分';
						break;
						case '2':
							$arr['consume_type'] = '消费奖励积分';
						break;
						case '3':
							$arr['consume_type'] = '充值积分';
						break;
						case '4':
							$arr['consume_type'] = '酒店分佣积分';
						break;
						case '5':
							$arr['consume_type'] = '分享奖励积分';
						break;
						case '6':
							$arr['consume_type'] = '推广员推广积分';
						break;
						case '7':
							$arr['consume_type'] = '微促销所获积分';
						break;
						case '8':
							$arr['consume_type'] = '旅游攻略分享积分';
						break;
						case '9':
							$arr['consume_type'] = '话费流量充值送积分';
						break;
						// case '10':
							// $arr['consume_type'] = '话费流量充值送积分';
						// break;
						case '11':
							$arr['consume_type'] = '商城消费';
						break;
						case '12':
							$arr['consume_type'] = '商城退款加回';
						break;
						// case '13':
						// 	$arr['consume_type'] = '积分转赠';
						// break;
						case '14':
							$arr['consume_type'] = '积分转赠';
						break;
						case '15':
							$arr['consume_type'] = '投票3扣除积分';
						break;
						case '16':
							$arr['consume_type'] = '一元购扣除积分';
						break;
					}

					$arr["remark"]		= $row->remark;
					$arr["createtime"]	= $row->createtime;
					$arr["money"]		= $row->score;
				}
				echo json_encode($arr);

			break;
			
			
		}

	break;

	case 'score'://查询积分列表

		$type = $configutil->splash_new($_POST["type"]);
		$pagenum = 0;
		if(!empty($_POST['pagenum'])){
			$pagenum = $_POST['pagenum'];
		}	

		$start = ($pagenum-1) * 10;
		$end = 10;
		$card_query = "SELECT id FROM weixin_card_members WHERE user_id=".$user_id." AND card_id=(SELECT shop_card_id FROM weixin_commonshops WHERE customer_id=".$customer_id." limit 1)";
		$card_result= mysql_query($card_query)or die( 'Query failed in 177: ' . mysql_error() );
		while($row=mysql_fetch_object($card_result)){
			$card_id = $row->id;
		}
		$log_query = "SELECT id,remark,score,createtime FROM weixin_card_score_records where card_member_id=".$card_id;
		switch ($type) {
			case 'score_all':
				$query = $log_query." order by createtime desc limit ".$start.",".$end;
			break;

			case 'score_in':
				$query = $log_query." AND score>0 order by createtime desc limit ".$start.",".$end;
			break;

			case 'score_out':
				$query = $log_query." AND score<=0 order by createtime desc limit ".$start.",".$end;
			break;
		}
		//echo $query;
		$result2 = mysql_query($query)or die( 'Query failed in 196: ' . mysql_error() );
		//echo $query;
		$arr = array();
		$i=0;
		while($row2 = mysql_fetch_object($result2) ){
			$arr[$i]['id'] 				= $row2->id;
			$arr[$i]['remark']	 		= $row2->remark;
			$arr[$i]['score']			= $row2->score;
			$arr[$i]['createtime'] 		= $row2->createtime;
			if($arr[$i]['score']>0){
				$arr[$i]['type'] 		= 0;//进账积分
			}elseif($arr[$i]['score']<=0){
				$arr[$i]['type'] 		= 1;//支出积分
			}
			$i++;
			
		}

		echo json_encode($arr);


	break;

	case 'profit'://累计收益
		$time = '';
		$type = $configutil->splash_new($_POST["type"]);
		$page = isset($_POST["page"])?$configutil->splash_new($_POST["page"]):1;
		$time = $configutil->splash_new($_POST["time"]);
		$start = ($page-1)*10;
		$end = 10;
		if( !empty($time) ){
			$start_time = strtotime($time);
			$OneDay 	= date('Y-m-01',strtotime("$time-1"));					    //当月第一天时间戳	
			$end_time 	= strtotime(date('Y-m-d', strtotime("$OneDay +1 month "))); //当月最后一天时间戳	
			//列表sql
			$query = "SELECT own_user_name,createtime,reward,paytype,id_new,batchcode FROM weixin_commonshop_order_promoters where isvalid=true and user_id=".$user_id." AND UNIX_TIMESTAMP(createtime)<=$end_time AND UNIX_TIMESTAMP(createtime)>=$start_time  ";//AND (paytype=1 or paytype=3)

		}else{
			$query = "SELECT own_user_name,createtime,reward,paytype,id_new,batchcode FROM weixin_commonshop_order_promoters where isvalid=true and user_id=".$user_id;//and (paytype=1 or paytype=3)

		}
		
		switch ($type) {
			case '0':
				$query = $query." and type IN(0,1,2,9)";
			break;
			case '1'://所有分佣
				$query = $query." and type IN(0,1,2,9) order by createtime desc LIMIT ".$start.",".$end;
			break;

			case '2'://普通分佣
				$query = $query." AND type=0 or type=3 order by createtime desc LIMIT ".$start.",".$end;
			break;

			case '3'://团队奖励
				$query = $query." AND type=1 or type=4  order by createtime desc  LIMIT ".$start.",".$end;
			break;

			case '4'://股东分红
				$query = $query." AND type=2 or type=5  order by createtime desc  LIMIT ".$start.",".$end;
			break;

			case '5'://全球分红
				$query = $query." AND type=9 order by createtime desc  LIMIT ".$start.",".$end;
			break;

			default:
				# code...
				break;
		}
		$arr = array();
		//echo $query;
		$result = mysql_query($query) or die('Query failed269: ' . mysql_error());  
		$i=0;
		while( $row=mysql_fetch_object($result) ){
			$arr[$i]['id'] 				= $row->id_new;
			$arr[$i]['reward'] 			= $row->reward;
			$paytype					= $row->paytype;
			switch ($paytype) {
				case '0':
					$arr[$i]["paytype"] = "待结算";
				break;
				case '1':
					$arr[$i]["paytype"] = '已结算';
				break;
				case '2':
					$arr[$i]["paytype"] = "已退货";
				break;
				case '3':
					$arr[$i]["paytype"] = "已发红包";
				break;
				case '4':
					$arr[$i]["paytype"] = "已退款";
				break;
				
				default:
					# code...
					break;
			}
			$arr[$i]['createtime'] 		= $row->createtime;
			$arr[$i]['own_user_name'] 	= $row->own_user_name;
			$arr[$i]['batchcode']		= $row->batchcode;
			$i++;
		}
		
		echo json_encode($arr);


	break;

	case "profit_chart"://累计
		$startTime = $configutil->splash_new($_POST["startTime"]);
		$endTime   = $configutil->splash_new($_POST["endTime"]);
		$unt_end_time = strtotime($endTime." +24 hours");
		$endTime  = date('Y-m-d',$unt_end_time);
		//echo $endTime;
		$type 	   = $configutil->splash_new($_POST["type"]);
		
 
		$query = "SELECT SUM(reward) AS total_money,UNIX_TIMESTAMP(createtime) as createtime  FROM weixin_commonshop_order_promoters WHERE createtime>='".$startTime."' AND createtime<='".$endTime."' AND user_id=".$user_id." and paytype in (1,3)  AND isvalid = true";
		//echo $query;
		switch ($type) {
			case '1':
				$query = $query." GROUP BY DAY(createtime) ORDER BY createtime asc";
				break;
			case '2':
				$query = $query." AND type IN (0,3,6) GROUP BY DAY(createtime) ORDER BY createtime asc";
				break;
			case '3':
				$query = $query." AND type IN (1,4,7) GROUP BY DAY(createtime) ORDER BY createtime asc";
				break;
			case '4':
				$query = $query." AND type IN (2,5,8) GROUP BY DAY(createtime) ORDER BY createtime asc";
				break;
			case '5':
				$query = $query." AND type=9 GROUP BY DAY(createtime) ORDER BY createtime asc";
				break;
		}
		//echo $query;
		$result= mysql_query($query) or die('Query failed 201: ' . mysql_error());
		$arr = array();
		$i=0;
		while($row=mysql_fetch_object($result)){
			$total_money 	= $row->total_money;
			$createtime 	= $row->createtime;
			$createtime 	= date('Y-m-d',$createtime);//得到年月日时间
			// $arr[$i]["total_money"] = intval($total_money);
			$arr[$i]["total_money"] = round($total_money,2);
			$arr[$i]["createtime"] 	= $createtime;

			$i++;
		}
		//var_dump($arr);
		
		echo json_encode($arr);
	break;

	case 'profit_Mon':
		$time = $configutil->splash_new($_POST["time"]);
		$type = $configutil->splash_new($_POST["type"]);

		if(!empty($time)){
			$start_time = strtotime($time);
			$OneDay 	= date('Y-m-01',strtotime("$time-1"));					    //当月第一天时间戳	
			$end_time 	= strtotime(date('Y-m-d', strtotime("$OneDay +1 month "))); //当月最后一天时间戳

			//总返佣
			$sum_total = "SELECT SUM(reward) AS total_money FROM weixin_commonshop_order_promoters WHERE isvalid=TRUE AND (paytype=1 OR paytype=3) AND UNIX_TIMESTAMP(createtime)<=$end_time AND UNIX_TIMESTAMP(createtime)>=$start_time AND user_id=".$user_id." and type IN(0,1,2,9)";
			//待返佣
			$hold_total = "SELECT SUM(reward) as total_profiy FROM weixin_commonshop_order_promoters WHERE isvalid=true AND UNIX_TIMESTAMP(createtime)<=$end_time AND UNIX_TIMESTAMP(createtime)>=$start_time AND user_id=".$user_id." and paytype=0 and type IN(0,1,2,9)";	
		}else{
			//总返佣
			$sum_total = "SELECT SUM(reward) AS total_money FROM weixin_commonshop_order_promoters WHERE isvalid=TRUE AND (paytype=1 OR paytype=3) AND user_id=".$user_id." and type IN(0,1,2,9)";
			//待返佣
			$hold_total = "SELECT SUM(reward) as total_profiy FROM weixin_commonshop_order_promoters WHERE isvalid=true AND user_id=".$user_id." and paytype=0 and type IN(0,1,2,9)";
		}
		switch ($type) {
			case '0':
				$sum_total = $sum_total;
				$hold_total= $hold_total;
			break;
			case '1'://所有分佣
				$sum_total = $sum_total;
				$hold_total= $hold_total;
			break;

			case '2'://普通分佣
				
				$sum_total = $sum_total." AND type IN (0,3,6)";
				$hold_total= $hold_total." AND type IN (0,3,6)";
			break;

			case '3'://团队奖励
				//$query = $query." AND type=1";
				$sum_total = $sum_total." AND type IN (1,4,7)";
				$hold_total= $hold_total." AND type IN (1,4,7)";
			break;

			case '4'://股东分红
				//$query = $query." AND type=2";
				$sum_total = $sum_total." AND type IN (2,5,8)";
				$hold_total= $hold_total." AND type IN (2,5,8)";
			break;

			case '5'://全球分红
				//$query = $query." AND type=9";
				$sum_total = $sum_total." AND type=9";
				$hold_total= $hold_total." AND type=9";
			break;
		}

		// echo $sum_total."</br>";
		// echo $hold_total."</br>";

		$arr = array();
		$total_profiy = 0;//待审核返佣
		$total_money  = 0;//已返佣
		$result_sum = mysql_query($sum_total)or die('Query failed 466: ' . mysql_error());
		while( $row = mysql_fetch_object($result_sum) ){
			$total_money = $row->total_money;
			if($total_money == '' || $total_money == NULL){
				$total_money = 0;
			}
		}

		$result_pro = mysql_query($hold_total)or die('Query failed 472: ' . mysql_error());
		while( $row2 = mysql_fetch_object($result_pro)){
			$total_profiy = $row2->total_profiy;
			if($total_profiy == '' || $total_profiy == NULL){
				$total_profiy = 0;
			}
		}
		if($time == ''){
			$arr['mon'] 		= 'all';
		}else{
			$arr['mon'] 		= date('m',strtotime($time));
		}
		
		$arr['total_money'] = round($total_money,2);
		$arr['total_profiy']= round($total_profiy,2);
		echo json_encode($arr);
		return false;


	break;

	case 'profit_detail':

		$batchcode = $configutil->splash_new($_POST["b"]); //查询的订单号
		$id 	   = $configutil->splash_new($_POST["id"]);
		$query = "SELECT p.reward,p.user_id,p.type,p.createtime,p.batchcode,p.paytype,p.remark,o.user_id as by_user_id FROM weixin_commonshop_order_promoters p LEFT JOIN weixin_commonshop_orders o ON p.batchcode=o.batchcode WHERE p.isvalid=true AND p.batchcode=$batchcode AND p.user_id=$user_id and p.id_new=$id LIMIT 1";
		//echo $query;
		$result= mysql_query($query)or die('Query failed 414: ' . mysql_error());

		$type 		= -1;	//0:普通分佣 1:团队奖励;2:股东分红;3:礼包普通分佣 4:礼包团队奖励;
		$batchcode 	= '';	
		$paytype 	= -1;	//0:支付；1确定；2：退货；3：红包；4：退款 ;
		$remark 	= '';
		$by_user_id = -1;	//购买者id
		$weixin_name= '';	//购买者微信名
		$arr 		= array();
		while( $row = mysql_fetch_object($result) ){
				$reward 		= $row->reward;				//返佣金额
				$user_id 		= $row->user_id;			//被返佣id
				$type 			= $row->type;				//返佣类型
				$createtime 	= $row->createtime;			//时间
				$batchcode 		= $row->batchcode;			//订单号
				$paytype 		= $row->paytype;			//支付状态
				$remark 		= $row->remark;				//备注
				$by_user_id 	= $row->by_user_id;			//购买者id
		}
		if($by_user_id>0){
			$sql = "SELECT weixin_name FROM weixin_users WHERE isvalid=true AND id=".$by_user_id." LIMIT 1";
			$res = mysql_query($sql)or die('Query failed 425: ' . mysql_error());
			while( $row = mysql_fetch_object($res) ){
				$weixin_name = $row->weixin_name;
			}
		}
		
		$arr['reward'] 	= $reward;
		$arr['user_id'] = $user_id;
		switch ($paytype) {
			case '-1':
				$arr['paytype'] = "已到账";
				break;
			case '0':
				$arr['paytype'] = "已支付";
				break;
			case '1':
				$arr['paytype'] = "已完成";
				break;
			case '2':
				$arr['paytype'] = "退货";
				break;
			case '3':
				$arr['paytype'] = "红包";
				break;
			case '4':
				$arr['paytype'] = "退款";
				break;
		}
		$arr['createtime']   = $createtime;
		$arr['batchcode']	 = $batchcode;
		if($type==10){//如果是购物币则查询购物币自定义名
			$query = "SELECT custom FROM weixin_commonshop_currency WHERE customer_id=$customer_id LIMIT 1";
			$result = mysql_query($query)or die('Query failed 532: ' . mysql_error());
			while( $row = mysql_fetch_object($result) ){
				$custom = $row->custom;
			}
		}
		switch ($type) {
			case '0':
				$arr['type'] = "三级分佣";
				break;
			case '1':
				$arr['type'] = "区域奖励";
				break;
			case '2':
				$arr['type'] = "股东分红";
				break;
			case '3':
				$arr['type'] = "礼包普通分佣";
				break;
			case '4':
				$arr['type'] = "礼包团队奖励";
				break;
			case '9':
				$arr['type'] = "全球分红奖励";
				break;
			case '10':
				$arr['type'] = $custom."奖励";
				break;
		}
		$arr['remark']		= $remark;
		$arr['weixin_name'] = $weixin_name;

		echo json_encode($arr);
		return false;

	break;

	case 'profit_time': //累计收益（时间）

		$type = $configutil->splash_new($_POST["type"]); //需要查询的类型 
		$search_time = $configutil->splash_new($_POST["time"]); //时间段
		$pagenum = 0;									 //页数
		if(!empty($_POST['page'])){
			$pagenum = $_POST['page'];
		}
		//$error_time = 60*60*6;//有误差？6小时
		$sear_being  = strtotime($search_time);		//搜索月第一天时间戳			
		$BeginDate	 = date('Y-m-01', strtotime($search_time));
		$end_time 	 = strtotime(date('Y-m-d', strtotime("$BeginDate +1 month ")));//当月最后一天时间戳	

		$start = ($pagenum-1) * 10;
		$end = 10;

		$query = "SELECT own_user_name,createtime,reward,paytype,id_new,batchcode FROM weixin_commonshop_order_promoters where isvalid=true and user_id=".$user_id." AND UNIX_TIMESTAMP(createtime)<=$end_time AND UNIX_TIMESTAMP(createtime)>=$sear_being";
		//echo $query;die;
		switch ($type) {
			case '1':
				$query = $query." order by createtime desc LIMIT ".$start.",".$end;
				break;
			case '2':
				$query = $query." AND type = 0 order by createtime desc LIMIT ".$start.",".$end;
				break;
			case '3':
				$query = $query." AND type = 1 order by createtime desc LIMIT ".$start.",".$end;
				break;
			case '4':
				$query = $query." AND type = 2 order by createtime desc LIMIT ".$start.",".$end;
				break;
			
			default:
				# code...
				break;
		}
		$arr = array();
		$i=0;
		$result = mysql_query($query) or die('Query failed 405: ' . mysql_error());
		while( $row = mysql_fetch_object($result) ){
			$arr[$i]['own_user_name']	= $row->own_user_name;
			$arr[$i]['batchcode'] 		= $row->batchcode;
			$arr[$i]['createtime']		= $row->createtime;
			$arr[$i]['reward']			= $row->reward;
			$paytype					= $row->paytype;
			switch ($paytype) {
				case '0':
					$arr[$i]["paytype"] = "已支付";
				break;
				case '1':
					$arr[$i]["paytype"] = "已完成";
				break;
				case '2':
					$arr[$i]["paytype"] = "退货";
				break;
				case '3':
					$arr[$i]["paytype"] = "红包";
				break;
				case '4':
					$arr[$i]["paytype"] = "退款";
				break;
				
				default:
					# code...
					break;
			}
			$arr[$i]['id_new']			= $row->id_new;
			$i++;
		} 
		echo json_encode($arr);

	break;

	case 'profit_all'://查询所有的日期

		 $query = "SELECT createtime FROM weixin_commonshop_order_promoters WHERE isvalid=true AND user_id=$user_id AND paytype=1 order by createtime";

		 $sql = $query." desc limit 1";
		 $result= mysql_query($sql) or die('Query failed 423: ' . mysql_error());
		while( $row = mysql_fetch_object($result) ){
			$now_time = $row->createtime;
		}

		$sql2 = $query." asc limit 1";
		$result2= mysql_query($sql2) or die('Query failed 405: ' . mysql_error());
		while( $row = mysql_fetch_object($result2) ){
			$first_time = $row->createtime;
		}

		$arr = array('first_time'=>$first_time,'now_time'=>$now_time);
		echo json_encode($arr);


	break;

	case 'redbag':
		$arr = array();
		$i = 0;
		$query = "select id,remark,red_money,createtime,type,customer_red_id from weixin_red_log where customer_id=".$customer_id." and type in (1,3) and user_id=".$user_id." order by id desc";
		$result= mysql_query($query) or die('Query failed 443: ' . mysql_error());
		while( $row = mysql_fetch_object($result) ){
			$arr[$i]['id'] 				= $row->id;
			$arr[$i]['remark'] 			= mb_substr($row->remark,0,4,'utf-8');
			$arr[$i]['red_money'] 		= $row->red_money;
			$arr[$i]['createtime'] 		= $row->createtime;
			$arr[$i]['customer_red_id'] = $row->customer_red_id;
			$type 						= $row->type;
			if($type==2){
				$arr[$i]['type'] 		= "用户组红包";
			}else{
				$arr[$i]['type'] 		= "佣金红包";
			}
			$i++;
		}
		echo json_encode($arr);

	break;	
	
	case 'vp':
		$page  = -1;
		$start = -1;
		$end   = -1;
		if(!empty($_POST['page'])){//页数
			$page = $configutil->splash_new($_POST["page"]);	
		}
		$start = ($page - 1)*10;
		$end   = 10;
		$arr   = array();
		$i     = 0;
		$query = "select id,vp,remark,createtime from weixin_commonshop_vp_logs where status=1 and isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." order by id desc limit ".$start.",".$end;
		$result= mysql_query($query) or die('Query failed 443: ' . mysql_error());
		while( $row = mysql_fetch_object($result) ){
			$arr[$i]['id'] 			= $row->id;
			$arr[$i]['vp'] 		    = $row->vp;
			$arr[$i]['remark'] 	    = $row->remark;
			$arr[$i]['createtime'] 	= $row->createtime;
			$i++;
		}
		echo json_encode($arr);
	break;

	case 'get_agent':
		$type = $configutil->splash_new($_POST["type"]);
		if(!empty($_POST['pagenum'])){//页数
			$page = $configutil->splash_new($_POST["pagenum"]);	
		}
		$start = ($page - 1)*10;
		$end   = 10;

		$arr   = array();
		$i     = 0;
		//$user_id=190452;
		switch ($type) {
			case 'agent_all':
				$query = "SELECT id,batchcode,price,detail,createtime,type FROM weixin_commonshop_agentfee_records WHERE isvalid=true AND (type=2 or type=4) AND user_id=".$user_id." order by createtime desc LIMIT ".$start.",".$end;
				$result= mysql_query($query) or die('Query failed 557: ' . mysql_error());
				while( $row = mysql_fetch_object($result) ){
					$arr[$i]['id']			= $row->id;
					$arr[$i]['batchcode'] 	= $row->batchcode;
					$arr[$i]['type'] 		= $row->type;
					$arr[$i]['price'] 		= round($row->price,2);
					$arr[$i]['detail'] 		= $row->detail;
					$arr[$i]['createtime'] 	= $row->createtime;
					$i++;
				}
				echo json_encode($arr);
			break;
			
			case 'agent_in'://代理商入账
				$query = "SELECT id,batchcode,price,detail,createtime,type FROM weixin_commonshop_agentfee_records WHERE isvalid=true AND type=2 AND user_id=".$user_id." order by createtime desc LIMIT ".$start.",".$end;
				$result= mysql_query($query) or die('Query failed 562: ' . mysql_error());
				while( $row = mysql_fetch_object($result) ){
					$arr[$i]['id']			= $row->id;
					$arr[$i]['batchcode'] 	= $row->batchcode;
					$arr[$i]['type'] 		= $row->type;
					$arr[$i]['price'] 		= round($row->price,2);
					$arr[$i]['detail'] 		= $row->detail;
					$arr[$i]['createtime'] 	= $row->createtime;
					$i++;
				}
				echo json_encode($arr);
			break;

			case 'agent_out'://代理商出账
				$query = "SELECT id,batchcode,price,detail,createtime,type FROM weixin_commonshop_agentfee_records WHERE isvalid=true AND type=4 AND user_id=".$user_id." order by createtime desc LIMIT ".$start.",".$end;
				$result= mysql_query($query) or die('Query failed 567: ' . mysql_error());
				while( $row = mysql_fetch_object($result) ){
					$arr[$i]['id']			= $row->id;
					$arr[$i]['batchcode'] 	= $row->batchcode;
					$arr[$i]['type'] 		= $row->type;
					$arr[$i]['price'] 		= round($row->price,2);
					$arr[$i]['detail'] 		= $row->detail;
					$arr[$i]['createtime'] 	= $row->createtime;
					$i++;
				}
				echo json_encode($arr);
			break;


			default:
				# code...
			break;
		}

	break;

	case 'agent':
			$arr = array();
			$id      = $configutil->splash_new($_POST["id"]);
			$query   = "SELECT batchcode,price,detail,createtime,after_inventory,Commission_Money,after_getmoney,withdrawal_id,type FROM weixin_commonshop_agentfee_records where isvalid=true and id=".$id;
			$result= mysql_query($query);
			while( $row = mysql_fetch_object($result) ){
				$arr["batchcode"]		 = $row->batchcode;
				$arr["price"]			 = $row->price;
				$arr["detail"]			 = $row->detail;
				$arr["createtime"]		 = $row->createtime;
				$arr["after_inventory"]	 = $row->after_inventory;
				$arr["Commission_Money"] = $row->Commission_Money;
				$arr["after_getmoney"]	 = $row->after_getmoney;
				$arr["withdrawal_id"]	 = $row->withdrawal_id;
				$type		 			 = $row->type;	
				switch ($type) {
					case '2':
						$arr["type"] =  "入账";
					break;
					case '4':
						$arr["type"] =  "出账";
						$arr["price"] = substr($arr["price"],1);
					break;
				}
			}
			echo json_encode($arr);	
	break;
	
	case 'cash_log':

		$user_id     = -1;
		$search_time = "";
		$sear_being  = "";
		$BeginDate   = "";
		$end_time    = "";
		if(!empty($_POST["user_id"])){
			$user_id = $configutil->splash_new($_POST["user_id"]);
		}
		$search_time = "";
		if(!empty($_POST['search_time'])){//搜索的时间戳
			$search_time = $configutil->splash_new($_POST['search_time']);						
			$sear_being  = strtotime($search_time);			//搜索月第一天时间戳			
			$BeginDate	 = date('Y-m-01', strtotime($search_time));
			$end_time 	 = strtotime(date('Y-m-d', strtotime("$BeginDate +1 month ")));//当月最后一天时间戳
		}
		$arr = array();
		$query = "SELECT id,getmoney,status,cash_type,createtime,batchcode FROM weixin_cash_being_log WHERE isvalid=true AND customer_id=$customer_id AND user_id=$user_id order by createtime desc";

		if(!empty($search_time)){
		    $query = "SELECT id,getmoney,status,cash_type,createtime,batchcode FROM weixin_cash_being_log WHERE isvalid=true AND customer_id=$customer_id AND user_id=$user_id AND  UNIX_TIMESTAMP(createtime)>=$sear_being and UNIX_TIMESTAMP(createtime)<=$end_time  order by createtime desc";
		}

		$result = mysql_query($query);
		while($info = mysql_fetch_assoc($result)){
		     $key_m = date('m', strtotime($info['createtime']));
		     $arr[$key_m][] = $info;
		}
		echo json_encode($arr);
	break;

	case 'currencyTurn':
		$page  = -1;
		$start = -1;
		$end   = -1;
		if(!empty($_POST['page'])){//页数
			$page = $configutil->splash_new($_POST["page"]);	
		}
		$start = ($page - 1)*10;
		$end   = 10;
		$arr   = array();
		$i     = 0;
		$query = "select id,cost_currency,type,remark,createtime from weixin_commonshop_currency_log where status=1 and class=2 and isvalid=true and customer_id=".$customer_id." and user_id=".$user_id." order by id desc limit ".$start.",".$end;
		$result= mysql_query($query) or die('Query failed 443: ' . mysql_error());
		while( $row = mysql_fetch_object($result) ){
			$arr[$i]['id'] 			  = $row->id;
			$arr[$i]['cost_currency'] = cut_num($row->cost_currency,2);
			$arr[$i]['remark'] 	      = mb_substr($row->remark,4,13,'utf-8');
			$arr[$i]['createtime']    = $row->createtime;
			$arr[$i]['type']          = $row->type;
			$i++;
		}
		echo json_encode($arr);
	break;
	
	default:
		# code...
		break;
}



?>