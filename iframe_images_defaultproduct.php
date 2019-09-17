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
$default_imgurl="";
if(!empty($_GET["default_imgurl"])){
    $default_imgurl=$configutil->splash_new($_GET["default_imgurl"]);
}else{
	 if($product_id>0){
	   $query2="select default_imgurl from weixin_commonshop_products where isvalid=true and  id=".$product_id;
		$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
		   $default_imgurl=$row2->default_imgurl;
		}
	}
}
$op="";
if(!empty($_GET["op"])){
   $op = $configutil->splash_new($_GET["op"]);
   if($op=="del"){
      $i_id = $configutil->splash_new($_GET["i_id"]);
	  if($product_id>0){
		  $query="update weixin_commonshop_products set default_imgurl='' where id=".$product_id;
		  mysql_query($query);
	  }
	  $default_imgurl="";
   }
}

$new_baseurl = "http://".$http_host;  


//查询选择模板设置图片尺寸
$template_id=-1;
$query ="select template_id from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
   $template_id = $row->template_id;
}
switch($template_id){
	case 13:
			$n_width=307;
			$n_height=160;
			break;
	case 1:
	case 2:
	case 4:
	case 5:		
	case 29:
	case 30:
	case 34:
	case 36:
	case 38:
	case 41:
	case 42:
	case 43:
	case 44:
	case 45:
	case 46:
	case 47:
	case 49:
	case 55:
			$n_width=300;
			$n_height=300;
			break;
	case 6:		
	case 12:		
	case 19:		
	case 32:
	case 33:	
	case 35:		
	case 40:		
			$n_width=450;
			$n_height=300;
			break;
	case 51:		
			$n_width=394;
			$n_height=124;
			break;		
	default:
			$n_width=100;
			$n_height=100;
			break;
}

 
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
<form action="save_productimg_defaultproduct.php?customer_id=<?php echo $customer_id_en; ?>&product_id=<?php echo $product_id; ?>" id="frm_img" enctype="multipart/form-data" method="post">
	
<dl class="i_dl" style="margin-top: 30px;">
	<dt class="i_dt">
	<?php if(empty($default_imgurl)){?>
		<img src="../../../common/images_V6.0/contenticon/pic_icon.png">
	<?php }else{?>
	<a href="<?php echo $new_baseurl.$default_imgurl; ?>" target="_blank">
		<img src="<?php echo $new_baseurl.$default_imgurl; ?>">
	</a>
	<?php }?>
	
		<span onclick="delImg();" class="i_dt_span">删除</span>
	</dt>
	<dd class="WSY_bulkboxdd03 i_dd">
		<?php if($n_width==-1 && $n_height==-1){?>
	<a style="display:block;">上传1张图片，作为首页的图片。图片大小建议：<?php echo $n_width; ?>*<?php echo $n_height; ?>像素,70k以下</a>
		<?php }else{?>
		<a style="display:block;">上传1张图片，作为首页的图片。图片大小建议：<?php echo $n_width; ?>*<?php echo $n_height; ?>像素,70k以下</a>
		<?php }?>
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
	parent.setParentDefaultimgurl('<?php echo $default_imgurl; ?>');
	
	
	function delImg(id){
	   url = "iframe_images_defaultproduct.php?op=del&i_id="+id+"&customer_id=<?php echo $customer_id_en; ?>&product_id=<?php echo $product_id; ?>";
	   document.location= url;
	}
  
</script>  
  
<script type="text/javascript">  
    upload();  
</script>  
</body>
</html>