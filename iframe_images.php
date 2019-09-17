<?php
require('../logs.php');   
header("Content-type: text/html; charset=utf-8"); 
require('../../../config.php');
require('../../../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../../../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../../../proxy_info.php');

mysql_query("SET NAMES UTF8");
$product_id =-1;

if(!empty($_GET["product_id"])){
   $product_id = $configutil->splash_new($_GET["product_id"]);
}

$op="";
if(!empty($_GET["op"])){
   $op = $configutil->splash_new($_GET["op"]);
   if($op=="del"){
      $i_id = $configutil->splash_new($_GET["i_id"]);
	  $query="update weixin_commonshop_product_imgs set isvalid=false where id=".$i_id;
	  mysql_query($query);
	  exit();
   }
}

$detail_template_type = 1;
if(!empty($_GET["detail_template_type"])){//详情模板分类类型
   $detail_template_type = $configutil->splash_new($_GET["detail_template_type"]);
}else{
	$query="select detail_template_type from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	 $result = mysql_query($query) or die('Query failed: ' . mysql_error());
     while ($row = mysql_fetch_object($result)) {
		 $detail_template_type = $row->detail_template_type;
		 break;
     }	
}


//$new_baseurl = $http_host."/weixinpl/back_newshops/";  
$new_baseurl = "http://".$http_host;  

$n_width=640;
$n_height=640;
?>

<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/content.css">
<link rel="stylesheet" type="text/css" href="../../../common/css_V6.0/contentblue.css"><!--内容CSS配色·蓝色-->
<link rel="stylesheet" type="text/css" href="../../Common/css/Product/product.css">
<link href="../../Common/css/Product/product/global.css" rel="stylesheet" type="text/css">
<link href="../../Common/css/Product/product/main.css" rel="stylesheet" type="text/css">
<link href="../../Common/css/Product/product/operamasks-ui.css" rel="stylesheet" type="text/css">
<link href="../../Common/css/Product/product/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../../common/js_V6.0/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../../Common/js/Product/product/jquery.uploadify-3.1.min.js?ver=<?php echo rand(0,9999);?>"></script>
<link href="../../Common/css/Product/product/uploadify.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.uploadifive-button {
	float: left;
	margin-top:50px;
	margin-left:10px;
	margin-right:10px;	
}
#queue {
	border: 1px solid #E5E5E5;
	height: 83px;
	overflow: auto;
	margin-bottom: 3px;
	padding: 0 3px 3px;
	width: 350px;
	background:#fff;
	float: left;
}
</style>

<script type="text/javascript">
		var httphost = '<?php echo $new_baseurl;?>'
		var imgids="";
		var img_id_upload=new Array();//初始化数组，存储已经上传的图片名
		var i=0;//初始化数组下标
		<?php $timestamp = time();?>
		$(function() {
			$('#file_upload').uploadifive({
				'auto'             : true,
				//'checkScript'      : 'uppic5/check-exists.php',
				'method'   : 'post',//方法，默认为post			
				'buttonText' : '选择图片', //设置按钮文本				
				'formData': {
									'timestamp' : '<?php echo $timestamp;?>',
									'token'    : '<?php echo md5('unique_salt' . $timestamp);?>',
									'product_id' :'<?php  echo $product_id;  ?>'											
							 },
				'queueID'          : 'queue',
				'fileType'     : 'image/*', //允许类型：图片
				'uploadLimit' : 5, //一次最多只允许上传5张图片
				'uploadScript'     : 'uppic5/uploadifive.php',
				'removeCompleted'  :true, //上传完毕后删除
				'fileSizeLimit' : '1024KB', //限制上传的图片不得超过1M 
				'onUploadComplete' : function(file, data) { 
						console.log(file.name+" == "+data);
						var lastimg = $("#show_imgs .i_dd_100").eq(4);
						var piccount = $("#show_imgs .i_dd_100").length;
						
						if(piccount == 5){
							alert("最多只能5张图片！");
							return;
						}
						
						if(lastimg){
							lastimg.remove();
						}
						$("#show_imgs").append('<dd class="i_dd_100"><img src="'+data+'" data-id="0" class="imgPro"><span class="i_dd_span">删除</span></dd>');
				},
			   'onQueueComplete' : function(queueData) {  //上传队列全部完成后执行的回调函数
						//alert("上传成功");
						//location.href="iframe_images.php?customer_id=<?php echo $customer_id_en;?>&product_id=<?php echo $product_id?>";				
				}  				
			});
		});
</script>
</head>
<body style="font-size:12px;background-color:inherit!important;margin:auto">
<dl class="i_dl">
	<div id="queue"></div>
	<dd class="WSY_bulkboxdd03 i_dt" style="width:150px">
		<a>共可上传<span id="pic_count">5</span>张图片,图片大小建议：<?php echo $n_width; ?>*<?php echo $n_height; ?>像素</a>
		<!--上传文件代码开始-->
		<div class="uploader white">
		<!--
			<input type="text" class="filename" readonly/>
			<input type="button" name="file" class="button" value="上传..."/> -->
			<input type="file" size="30" id="file_upload" name="file_upload"/>
		</div>
		<!--上传文件代码结束-->
		<span class="upload_file">
			<div>
				<div class="clear"></div>
			</div>
		</span>
	</dd>
</dl>
<dl class="WSY_bulkbox02img i_dl" id="show_imgs">
	
	
	<?php 
    $query2 = "";
    if($product_id>0){
	   $query2="select imgurl,id from weixin_commonshop_product_imgs where isvalid=true and  product_id=".$product_id." and  customer_id=".$customer_id." order by id asc";
		$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
		   $imgurl=$row2->imgurl;
		   $i_id = $row2->id;
		
	  ?>
	<dd class="i_dd_100">
	<a href="<?php echo $new_baseurl.$imgurl; ?>" target="_blank">
	<img class="imgPro" data-id="<?php echo $i_id;?>" src="<?php echo $imgurl;?>">
	</a>
		<span class="i_dd_span">删除</span>
	</dd>
	<?php } 
	}
 ?>
</dl>

<?php 
mysql_close($link);
?>
<script type="text/javascript">  	
	if(imgids.length>0){
	   imgids= imgids.substring(0,imgids.length-1);
	   parent.setParentImgIds(imgids);
	}	
	function delImg(id){
	   url = "iframe_images.php?op=del&i_id="+id+"&customer_id=<?php echo $customer_id_en; ?>&product_id=<?php echo $product_id; ?>";
	   document.location= url;
	}
  $(function(){
	  $("#show_imgs").on("click",".i_dd_span",function(){
		  var parent = $(this).parent();
		  var img = parent.find("img");
		  var id = img.data("id");
		  if(id > 0){
			$.get("iframe_images.php",{op:"del",i_id:id},function(data){
				parent.remove();
				return;
			});  
		  }  
		  parent.remove();
	  });
  });
</script> 
<script src="uppic5/jquery.min.js" type="text/javascript"></script>
<script src="uppic5/jquery.uploadifive.min.js" type="text/javascript"></script> 
</body>
</html>