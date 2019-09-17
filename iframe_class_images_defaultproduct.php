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
$class_imgurl="";
if(!empty($_GET["class_imgurl"])){
    $class_imgurl=$configutil->splash_new($_GET["class_imgurl"]); 
}else{
	 if($product_id>0){
	   $query2="select class_imgurl from weixin_commonshop_products where isvalid=true and  id=".$product_id;
		$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
		   $class_imgurl=$row2->class_imgurl;
		}
	}
}
$op="";
if(!empty($_GET["op"])){
   $op = $configutil->splash_new($_GET["op"]);
   if($op=="del"){
      $i_id = $configutil->splash_new($_GET["i_id"]);
	  if($product_id>0){
		  $query="update weixin_commonshop_products set class_imgurl='' where id=".$product_id;
		  mysql_query($query);
	  }
	  $class_imgurl="";
   }
}
$new_baseurl = "http://".$http_host;  

 
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

</head>
<body style="font-size:12px;background-color:inherit!important;margin:auto">

<form action="save_productimg_class_defaultproduct.php?customer_id=<?php echo $customer_id_en; ?>&product_id=<?php echo $product_id; ?>" id="frm_img" enctype="multipart/form-data" method="post">
	
<dl class="i_dl" style="margin-top: 30px;">
	<dt class="i_dt">
	<?php if(empty($class_imgurl)){?>
		<img src="../../../common/images_V6.0/contenticon/pic_icon.png">
	<?php }else{?>
	<a href="<?php echo $new_baseurl.$class_imgurl; ?>" target="_blank">
		<img src="<?php echo $new_baseurl.$class_imgurl; ?>">
	</a>
	<?php }?>
		<span onclick="delImg();" class="i_dt_span">删除</span>
	</dt>
	<dd class="WSY_bulkboxdd03 i_dd">
		<a>上传1张图片，作为分类页的图片。图片大小建议：①风格1: <span style="color:#e74c3c"> 300*300</span>像素 ②风格2：<span style="color:#e74c3c">352*235</span>
		像素 ③风格3:<span style="color:#e74c3c">300*300</span>像素 ④风格4:<span style="color:#e74c3c">瀑布流格式格式不作要求</span>(70k以下)</a>
		<!--上传文件代码开始-->
		<div class="uploader white">
			<input type="text" class="filename" readonly/>
			<input type="button" name="file" class="button" value="上传..."/>
			<input  name="upfile" id="upfile" type="file"  size="30" value="Submit"/>
		</div>
		<input type=hidden name="customer_id" id="customer_id" value="<?php echo $customer_id_en; ?>" />
		<!--上传文件代码结束-->
	</dd>

</dl>
</form>

<?php 

mysql_close($link);
?>
<script type="text/javascript">  
  
    function upload(){  
        var element = document.getElementById("upfile");  
        if("\v"=="v")  
        {  
            element.onpropertychange = uploadHandle;  
        }  
        else  
        {  
            element.addEventListener("change",uploadHandle,false);  
        }  
  
        function uploadHandle()  
        {  
            if(element.value)  
            {  
              
			  $("#frm_img").submit();
  
            }  
        }  
  
    } 
	parent.setParentClassDefaultimgurl('<?php echo $class_imgurl; ?>');
	
	
	function delImg(id){
	   url = "iframe_class_images_defaultproduct.php?op=del&i_id="+id+"&customer_id=<?php echo $customer_id_en; ?>&product_id=<?php echo $product_id; ?>";
	   document.location= url;
	}
  
</script>  
  
<script type="text/javascript">  
    upload();  
</script>  
</body>
</html>