<?php 
header("Content-type: text/html; charset=utf-8"); 
$search_status=1;
if(!empty($_GET["search_status"])){
    $search_status = $configutil->splash_new($_GET["search_status"]);
}
if(!empty($_POST["search_status"])){
    $search_status = $configutil->splash_new($_POST["search_status"]);
}

				
		


?>
	<div class="WSY_column_header">
	 	<div class="WSY_columnnav">
			<a class='<?php if($search_status==1){echo 'white1';}?>' href="promoter.php?customer_id=<?php echo $customer_id_en;?>&search_status=1">所有</a>
			<a class='<?php if($search_status==2){echo 'white1';}?>' href="promoter.php?customer_id=<?php echo $customer_id_en;?>&search_status=2">待审核</a>
			<a class='<?php if($search_status==3){echo 'white1';}?>' href="promoter.php?customer_id=<?php echo $customer_id_en;?>&search_status=3">已确认</a>
			<a class='<?php if($search_status==-1){echo 'white1';}?>' href="promoter.php?customer_id=<?php echo $customer_id_en;?>&search_status=-1">驳回/取消</a>				
		</div> 
	</div>