<!--
foot_num：当前页面的按钮位置：1 2 3 4
-->
<style>
.footer{position: fixed;bottom: 0px;left: 0px;width: 100%;height: 53px;background:#fff;z-index: 1110;line-height: 24px;border-top: 1px solid #d1d1d1;}
.footer .footer-box{margin:0 auto;width: 100%;height: 53px;}
.footer .footer-box .weidian{width: 25%;height: 53px;float: left;text-align: center;margin-top: 4px;}
.footer .footer-box .weidian img{width: 20px;height: 20px;vertical-align: middle;}
.footer .footer-box .weidian p{font-size: 12px;color: #a1a1a1;margin: 0;}
.footer .footer-box .weidian.active p{color:#64b83c;white-space:nowrap;text-overflow:clip;overflow: hidden;}
.footer .footer-box .weidian p.foot_grey{color: #a1a1a1;}

</style>
	<!--底部按钮-->
	<div class="footer">
      <div class="footer-box">
          <div class="weidian">
              <a onclick="onloadP(1);" >
                  <img src="./<?php echo $images_skin?>/firstPage/my_tab_1<?php if($page_type=="list")echo "_sel"?>.png" alt="">
                  <p class="<?php if($page_type=="list"){echo "foot_select";}else{echo "foot_grey";}?>">商品</p>
              </a>
          </div>
          <div class="weidian">
              <a onclick="onloadP(2);" >
                 <img src="./<?php echo $images_skin?>/firstPage/my_tab_2<?php if($page_type=="class_page")echo "_sel"?>.png" alt="">
                  <p class=" <?php if($page_type=="class_page"){echo "foot_select";}else{echo "foot_grey";}?>">分类</p>
              </a>
          </div>
          <div class="weidian">
              <a onclick="onloadP(3);" >
                  <img src="./images/firstPage/my_tab_3.png" alt="">
                 <p>购物车</p>
              </a>
          </div>
          <div class="weidian">
              <a onclick="onloadP(4);"  >
                  <img src="./images/firstPage/my_tab_4.png" alt="">
                  <p>我的</p>
              </a>
          </div>
      </div>
    </div>
    <div style="height:50px;"></div>
	<!--底部按钮-->
	
	<!--悬浮按钮-->
	<?php  include_once('float.php');?>
	<!--悬浮按钮-->
	
<script>
var foot_num = '<?php echo $foot_num ;?>';

var foot_sel_png = new Array(
'./images/firstPage/my_tab_1_sel.png',
'./images/firstPage/my_tab_2_sel-orange.png',
'./images/firstPage/my_tab_3_sel.png',
'./images/firstPage/my_tab_4_sel.png'
);

	//设置颜色
	if(foot_num == 1){
		$('.foot').eq(foot_num-1).find('img').attr('src',foot_sel_png[0]);
	}else if(foot_num == 2){
		$('.foot').eq(foot_num-1).find('img').attr('src',foot_sel_png[1]);
	}else if(foot_num == 3){
		$('.foot').eq(foot_num-1).find('img').attr('src',foot_sel_png[2]);
	}else if(foot_num == 4){
		$('.foot').eq(foot_num-1).find('img').attr('src',foot_sel_png[3]);
	}



    function onloadP(type){ // Tab Selection
		console.log(type);
    	if(type == 1){
    		window.location.href="list.php?customer_id="+'<?php echo $customer_id_en;?>';
    	}else if(type == 2){
    		window.location.href="class_page.php?customer_id="+'<?php echo $customer_id_en;?>';
    	}else if(type == 3){
    		window.location.href="order_cart.php?customer_id="+'<?php echo $customer_id_en;?>';
    	}
      else if(type == 4){
        window.location.href="personal_center.php?customer_id="+'<?php echo $customer_id_en;?>';
      }
    }

	$(function(){
	
		$("#totop").click(function(){ // 返回顶部
			$('body,html').animate({scrollTop:0},500);
			return false;
		});
	});
</script>	