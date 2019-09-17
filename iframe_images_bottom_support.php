<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../proxy_info.php');

mysql_query("SET NAMES UTF8");
$product_id =-1;

/* if(!empty($_GET["product_id"])){
   $product_id = $_GET["product_id"];
} */
$bottom_support_imgurl="";
if(!empty($_GET["bottom_support_imgurl"])){
    $bottom_support_imgurl=$configutil->splash_new($_GET["bottom_support_imgurl"]);
}else{
	 if($customer_id>0){
	   $query2="select bottom_support_imgurl from weixin_commonshops where isvalid=true and  customer_id=".$customer_id;
		$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
		   $bottom_support_imgurl=$row2->bottom_support_imgurl;
		}
	}
}
$op="";
if(!empty($_GET["op"])){
   $op = $configutil->splash_new($_GET["op"]);
   if($op=="del"){
      $i_id = $configutil->splash_new($_GET["i_id"]);
	  if($customer_id>0){
		  $query="update weixin_commonshops set bottom_support_imgurl='' where customer_id=".$customer_id;
		  mysql_query($query);
	  }
	  $bottom_support_imgurl="";
   }
}
$new_baseurl = BaseURL."back_commonshop/";

$n_width=500;
$n_height=250;
 
?>

<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link href="css/global.css" rel="stylesheet" type="text/css">
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="operamasks-ui.css" rel="stylesheet" type="text/css">
<link href="css/shop.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery.uploadify-3.1.min.js?ver=<?php echo rand(0,9999);?>"></script>
<link href="css/uploadify.css" rel="stylesheet" type="text/css" />

</head>
<body style="font-size:12px;">
<div id="products" class="r_con_wrap" style="background:none">
<span class="input">
<span class="upload_file">
	<div>
	   <form action="save_bottom_support_imgurl.php?customer_id=<?php echo $customer_id_en; ?>" id="frm_img" enctype="multipart/form-data" method="post">
			<div class="up_input">
			<input name="upfile" id="upfile" type="file"  width="120" height="30" value="Submit">
			
			<div id="PicUploadQueue" class="om-fileupload-queue"></div>
			</div>
			<input type=hidden name="customer_id" id="customer_id" value="<?php echo $customer_id_en; ?>" />
		</form>
		<div class="tips" style="font-size:12px;">上传1张图片，作为底部信息的LOGO图片。图片大小建议：96px*32px像素</div>
		<div class="clear"></div>
	</div>
</span>


<div class="img" id="PicDetail">
  
        <?php if(!empty($bottom_support_imgurl)){ ?>
		<div style="width:100%;height:120px;">
			 <a href="<?php echo $new_baseurl.$bottom_support_imgurl; ?>" target="_blank">
			 <img style="width:100%;height:120px;" src="<?php echo $new_baseurl.$bottom_support_imgurl; ?>"></a>
			 <span style="top:100px;width:100%;" onclick="delImg();">删除</span>
		</div>
        <?php } ?>
  
</div>
</span>
<div class="clear"></div>
</div>
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
	parent.setBottomSupportimgurl('<?php echo $bottom_support_imgurl; ?>');
	
	
	function delImg(id){
	   url = "iframe_images_bottom_support.php?op=del&i_id="+id+"&customer_id=<?php echo $customer_id_en; ?>";
	   document.location= url;
	}
  
</script>  
  
<script type="text/javascript">  
    upload();  
</script>  
</body>
</html>