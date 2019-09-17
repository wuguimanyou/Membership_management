<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]

$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../common/utility.php');

//头文件----start
require('../common/common_from.php');
//头文件----end
require('select_skin.php');
//echo $user_id."*******";

/******传值跳转******/
//$from_href = $configutil->splash_new($_GET["f_h"]);	//下方JS定义跳转页面

/******传值跳转******/

$titlestr = '忘记密码';
$mobile = '';
$is_bind = 0; //是否绑定 0未绑定 1已绑定
$query = "SELECT account FROM system_user_t WHERE isvalid=true AND user_id=$user_id AND customer_id = $customer_id LIMIT 1";
$result= mysql_query($query) or die('Query failed: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$mobile = $row->account;
}
if( $mobile!= '' ||$mobile != NULL ){
	$is_bind = 1;
}


			
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $titlestr ;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="no" name="apple-touch-fullscreen">
    <meta name="MobileOptimized" content="320"/>
    <meta name="format-detection" content="telephone=no">
    <meta name=apple-mobile-web-app-capable content=yes>
    <meta name=apple-mobile-web-app-status-bar-style content=black>
    <meta http-equiv="pragma" content="nocache">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8">
    
    <link type="text/css" rel="stylesheet" href="./assets/css/amazeui.min.css" />
    <link type="text/css" rel="stylesheet" href="./css/order_css/global.css" />
    <link type="text/css" rel="stylesheet" href="./css/css_<?php echo $skin ?>.css" />     
    

</head>
<style>
  .curPhoneTitle{width:100%;height:40px;line-height:40px;color:#888;padding-left:10px;}   
  .phoneEdit{width:100%;height:50px;line-height:50px;background-color:white;padding-left:10px;border-bottom:1px solid #f8f8f8;}
  .phoneEdit .area{width:20%;float:left;}
  .phoneEdit .phoneTxt{width:46%;float:left;}
  .sendBtn{width:30%;float:left;text-align:right;}
  .sendBtn span{background-color:black;color:white;height:45px;line-height:45px; padding: 5px 8px;}
  .checkCode{width:100%;height:50px;line-height:50px;background-color:white;padding-left:10px;border-bottom:#f8f8f8;border:none;}
  .btn{width:80%;margin:20px auto;text-align:center;}
  .btn span{width:100%;height:45px;line-height:45px; padding:10px;letter-spacing:3px;}
  #check_code,#password,#password_again{color:#888;width:100%;border:none;}
  #phone_num{color:#888;width:100%;border:none;}
  .checkCode div{float:left;}
  .area span{color:black;}
  #send_msg{padding: 5px 8px;}
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button{
	-webkit-appearance: none !important;
	margin: 0; 
}
</style>

<body id="mainBody" data-ctrl=true style="background:#f8f8f8;">
    <div id="mainDiv" style="width: 100%;height:100%;">
	   <!--  <header data-am-widget="header" class="am-header am-header-default">
		    <div class="am-header-left am-header-nav" onclick="goBack();">
			    <img class="am-header-icon-custom" src="./images/center/nav_bar_back.png" style="vertical-align:middle;"/><span style="margin-left:5px;">返回</span>
		    </div>
	        <h1 class="am-header-title" style="font-size:18px;"><?php echo $titlestr ;?></h1>
	    </header>
        <div class="topDiv"></div> --><!-- 暂时隐藏头部导航栏 -->
		<?php /* if($account!=''){ ?>
        <div class="curPhoneTitle" id="cur_phone"><span>当前的手机号码是 <?php echo $account ; ?></span></div>
		<?php }*/?>
        <div class="phoneEdit">
        	<div class="area"><span>中国+86</span></div>
        	<div class="phoneTxt"><input type="number" id="phone_num" placeholder="" value="<?php echo $mobile;?>" disabled></div>
<!--        	<div class="sendBtn" style="" onclick="/*send_checkcode();*/"><button id="send_msg" style="">短信验证</button></div>-->
        	<div class="sendBtn" style="" onclick="/*send_checkcode();*/"></div>
        </div>
<!--        <div class="checkCode">-->
<!--        	<div style="width:80%;"><input type="number" placeholder="请输入验证码" id="check_code" value=""></div>-->
<!--        </div>-->
		<div class="checkCode">
        	<div style="width:80%;"><input type="number" placeholder="请设置六位长度的支付密码" id="password" value="" ></div>
        </div>
		<div class="checkCode">
        	<div style="width:80%;"><input style="border:none;" type="number" placeholder="请再次确认支付密码" id="password_again" value="" ></div>
        </div>
		
		
        <div class="btn" onclick="comfirm();"><span>确认</span></div>

	    <script type="text/javascript" src="./assets/js/jquery.min.js"></script>    
	    <script type="text/javascript" src="./assets/js/amazeui.js"></script>
	    <script type="text/javascript" src="./js/global.js"></script>
	    <script type="text/javascript" src="./js/loading.js"></script>
	    <script src="./js/jquery.ellipsis.js"></script>
	    <script src="./js/jquery.ellipsis.unobtrusive.js"></script>   
</body>		

<script type="text/javascript">

    var winWidth = $(window).width();
    var winheight = $(window).height();
	var customer_id="<?php echo $customer_id_en;?>";
    var jcrop_api; 
    var zoom = 1;
    
	$(function() {
        $("#mainDiv").show();
        $(document.body).css("background:","#f8f8f8");
        
	});
	
	
	//倒计时
	function start_sms_button(obj){
		var count = 1 ;
		var sum = 180;		//60秒
		var i = setInterval(function(){
		  if(count > sum-1){
			obj.attr('disabled',false);
			obj.text('短信验证');
			clearInterval(i);
		  }else{
			obj.text('剩余'+parseInt(sum - count)+'秒');
			//console.log(parseInt(sum - count));
		  }
		  count++;

		},1000);
	}

  
	$(function(){
	//发送验证码
	var is_bind = <?php echo $is_bind;?>;
	if( is_bind == 0 ){
		function callbackfunc(){
            window.location.href = "bind_phone.php?customer_id=<?php echo $customer_id_en;?>";
        }
        showConfirmMsg("提示","您尚未绑定手机，请立即前往绑定","确定","取消",callbackfunc);
        return false;
	}
	
		$('#send_msg').click(function(){
			var phone_obj = $('#phone_num');
			var send_obj = $('#send_msg');
			var val = phone_obj.val();
		//	val = '15920200230';
			if(val){
				if(chkPhoneNumber(val)){		
				  send_obj.attr('disabled',"disabled");
				  //60秒后重新启动发送按钮
				  start_sms_button(send_obj);
				$.ajax({
					url:'checkuser_login.php',
					data:{op:"send_paypw_msg",phone:val,customer_id:customer_id},
					dataType:'json',
					type:'post',
					beforeSend:function(){
					  send_obj.attr('disabled',"disabled");
					},

					success:function(res){
						console.log(res);
						showAlertMsg ("提示：",res,"知道了");
						
					}
				  });
				}else{					
					showAlertMsg ("提示：","手机号的格式错误","知道了");
				}
			}else{
				showAlertMsg ("提示：","手机号不能为空","知道了");
			}
		});
	});
  

	function comfirm(){
		var password = $('#password').val();
		var password_again = $('#password_again').val();
		var check_code = $('#check_code').val();
		var number = /^[0-9]*$/;
		//输入验证码
//		if (!check_code || check_code.length != 6) {		//6位验证码
//			showAlertMsg ("提示：","请输入正确的验证码","知道了");
//			return false;
//		}
		if(!number.test(password)){
			showAlertMsg ("提示：","请输入六位数字的支付密码","知道了");
			return false;
		}
		
		
		// //验证登陆密码		
		// var passwords_rtn = check_login_password(password,'支付密码');
		
		// if(passwords_rtn['code'] == 0){
		// 	showAlertMsg ("提示：",passwords_rtn['msg'],"知道了");				
		// 	return false;
		// }
		

		if(password!=password_again){ //判断两个密码是否一样
			showAlertMsg ("提示：","两次密码输入不一样","知道了");
			return false;
		}

		var pay_password_rtn = check_pay_pwd(password,6);
		if(pay_password_rtn['code'] == 0){
			showAlertMsg ("提示：",pay_password_rtn['msg'],"知道了");				
			return false;
		}
		
		//验证验证码正确与否
		var phone = $('#phone_num').val();				
		$.ajax({
			url:'checkuser_login.php',
			data:{op:"edit_paypassword",yzm: check_code,customer_id:customer_id,phone:phone,password:password},
			dataType:'json',
			type:'post',
						
			success:function(result){
				console.log(result);
				if(result.code==10010){
					showAlertMsg ("提示：",result.msg,"知道了",jump);
					function jump(){
						history.go(-2);
					}		
				}else{
					showAlertMsg ("提示：",result.msg,"知道了");
				}
				
			}
		});
        	
	}
</script>

<script type="text/javascript" src="../common/js/common.js"></script>
<!--引入侧边栏 start-->
<?php  include_once('float.php');?>
<!--引入侧边栏 end-->
<?php require('../common/share.php'); ?>
</html>