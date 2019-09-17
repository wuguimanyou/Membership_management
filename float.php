<?php
$is_nav = 0;
$query = "SELECT is_nav FROM weixin_commonshops_extend WHERE isvalid=true AND customer_id=$customer_id LIMIT 1";
$result= mysql_query($query) or die('Query failed 3: ' . mysql_error());
while( $row = mysql_fetch_object($result) ){
	$is_nav = $row->is_nav;
}
?>
<style>
/*share_start*/
#share{position:fixed;bottom:62px;left:90%;width:30px;zoom:1;z-index:9000;opacity: 0.8;}
#share a{
	background-image:url(/weixinpl/mshop/images/list_image/wa2.png);background-repeat:no-repeat;display:block;width:30px;height:30px;margin-bottom:2px;overflow:hidden;text-indent:-999px;
	background-size: cover;
}
#share .hideicon{background-position:0 0;position:absolute;bottom:180px;}
#share a.hideicon:hover{background-position:0 0;}
#share .hideicon.close{bottom:30px;transform:rotate(180deg);-webkit-transform:rotate(180deg);}
#share .homepage{background-position:0 -30px;position:absolute;bottom:150px;}
#share a.homepage:hover{background-position:0 -30px;}
#share .personal{background-position:0 -60px;position:absolute;bottom:120px;}
#share a.personal:hover{background-position:0 -60px;}
#share .eye{background-position:0 -90px;position:absolute;bottom:90px;}
#share a.eye:hover{background-position:0 -90px;}
#share .heart{background-position:0 -120px;position:absolute;bottom:60px;}
#share a.heart:hover{background-position:0 -120px;}
#share .chat{background-position:0 -150px;position:absolute;bottom:30px;}
#share a.chat:hover{background-position:0 -150px;}
#share a#totop{background-position:0 -180px;position:absolute;bottom:0px;cursor:pointer;display: none;}
#share a#totop:hover{background-position:0 -180px;}
</style>

	<?php 
	$query_online = "select need_online,online_type,online_qq,is_applymoney_startdate,is_applymoney_enddate  
	,advisory_telephone from weixin_commonshops where isvalid=true and customer_id=".$customer_id." limit 0,1";
	$online_qq 		= 0;
	$online_type    = 1;
	$online_qq      = '';
	$result_online=mysql_query($query_online);
	while($row_online=mysql_fetch_object($result_online)){
		$need_online 	= $row_online->need_online;
		$online_type 	= $row_online->online_type;
		$online_qq		= $row_online->online_qq;
	}
	if($need_online){ 
	  $online_url="/weixinpl/online/show_online.php?customer_id=".$customer_id_en;
	  if($online_type==2){
		 //qq咨询
		  $online_url="http://wpa.qq.com/msgrd?v=3&uin=".$online_qq."&site=qq&menu=yes";
	  }
	}
	?>
	<!--悬浮按钮-->
	<div id="share">

	    <a class="hideicon <?php if($is_nav==1){ echo 'hideicon';}else{ echo 'hideicon ui-link ui-btn-up-undefined close';}?>" title="隐藏" >隐藏图标</a>
		<a id="totop" title="返回顶部" >返回顶部</a>
		
		<div class="all" style="display:<?php if($is_nav==1){ echo 'block';}else{ echo 'none';}?>" >
		<a class="homepage"  onclick="locatP(1);">homepage</a>
		<a class="personal"  onclick="locatP(5);">personal</a>
		<a class="eye"   onclick="locatP(2);">eye</a>
		<a class="heart"  onclick="locatP(3);">heart</a>
		<a class="chat"  onclick="locatP(4);">chat</a>
		</div>
	</div>
	<!--悬浮按钮-->
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
$(function(){

	$('.hideicon').click(function(){
		$('.all').fadeToggle();
		$('.hideicon').toggleClass('close');
	})
	$("#totop").click(function(){
		$("body,html").animate({scrollTop:0},500);
	})
	$(window).scrollTop();
	$(window).scroll(function(){
		if($(window).scrollTop()>0)
		{
			$('#totop').show();
		}
		else{
			$('#totop').hide();
		}
	})
})
	    function locatP(type){ // Tab Selection
    	if(type == 1){
    		window.location.href="/weixinpl/common_shop/jiushop/index.php?customer_id="+'<?php echo $customer_id_en;?>';
    	}else if(type == 2){
    		window.location.href="/weixinpl/mshop/my_visit.php?customer_id="+'<?php echo $customer_id_en;?>';
    	}else if(type == 3){
    		window.location.href="/weixinpl/mshop/my_collect.php?customer_id="+'<?php echo $customer_id_en;?>';
    	}else if(type == 4){
			var need_online  ='<?php echo $need_online;?>';	//是否开启在线客服
			var online_type  ='<?php echo $online_type;?>';	//在线客服类型 0:在线客服 1:QQ 2:多客服
			if( need_online == 1){
				
				if(online_type == 3){
					wx.closeWindow();
				}else{
					window.location.href="<?php echo $online_url;?>";
				}
			}else{
				alert('商家未开启客服');
				return;
			}
				
      }else if(type == 5){
      	window.location.href="/weixinpl/mshop/personal_center.php?customer_id="+'<?php echo $customer_id_en;?>';
      }
    }
</script>