<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
require('../back_init.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');

require('../proxy_info.php');

mysql_query("SET NAMES UTF8");
$product_id =-1;

if(!empty($_GET["product_id"])){
   $product_id = $_GET["product_id"];
}
$detail_template_type = 1;
if(!empty($_GET["detail_template_type"])){
   $detail_template_type = $_GET["detail_template_type"];
}else{
	$query="select detail_template_type from weixin_commonshops where isvalid=true and customer_id=".$customer_id;
	 $result = mysql_query($query) or die('Query failed: ' . mysql_error());
     while ($row = mysql_fetch_object($result)) {
		 $detail_template_type = $row->detail_template_type;
		 break;
     }	
}

$op="";
if(!empty($_GET["op"])){
   $op = $_GET["op"];
   if($op=="del"){
      $i_id = $_GET["i_id"];
	  $query="update weixin_commonshop_product_imgs set isvalid=false where id=".$i_id;
	  mysql_query($query);
   }
}
$new_baseurl = BaseURL."back_commonshop/";

$n_width=400;
$n_height=400;
if($detail_template_type>1){
   $n_width=640;
   $n_height=320;
}
?>

<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title></title>
<link href="css/global.css" rel="stylesheet" type="text/css">
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="css/shop.css" rel="stylesheet" type="text/css">
<script src="uppic5/jquery.min.js" type="text/javascript"></script>
<script src="uppic5/jquery.uploadifive.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="uppic5/uploadifive.css">
<link rel="stylesheet" type="text/css" href="uppic5/style.css" />
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
		var imgids="";
		var img_id_upload=new Array();//初始化数组，存储已经上传的图片名
		var i=0;//初始化数组下标
		<?php $timestamp = time();?>
		$(function() {
			$('#file_upload').uploadifive({
				'auto'             : true,
				'checkScript'      : 'uppic5/check-exists.php',
				'method'   : 'post',//方法，默认为post			
				'buttonText'      : '选择图片', //设置按钮文本				
				'formData'         : {
											'timestamp' : '<?php echo $timestamp;?>',
											'token'    : '<?php echo md5('unique_salt' . $timestamp);?>',
											'product_id' :'<?php  echo $product_id;  ?>'											
				                     },
				'queueID'          : 'queue',
				'fileType'     : 'image/*', //允许类型：图片
				'uploadLimit' : 10, //一次最多只允许上传10张图片
				'uploadScript'     : 'uppic5/uploadifive.php',
				'removeCompleted'  :true, //上传完毕后删除
				'fileSizeLimit' : '1024KB', //限制上传的图片不得超过1M 
				'onUploadComplete' : function(file, data) { console.log(data); },
			   'onQueueComplete' : function(queueData) {  //上传队列全部完成后执行的回调函数
						alert("上传完毕！");
						location.href="iframe_images.php?customer_id=<?php echo $customer_id;?>&product_id=<?php echo $product_id?>";				
				}  				
			});
		});
</script>
</head>
<body style="font-size:12px;">

<div id="products" class="r_con_wrap">
	<form>
		<div id="queue"></div>
		<input id="file_upload" name="file_upload" type="file" multiple="true">
		<div  style="font-size:12px;">共可上传<span id="pic_count">5</span>张图片，图片大小建议：<?php echo $n_width; ?>*<?php echo $n_height; ?>像素</div>
	</form>
<span class="input">
<span class="upload_file">
	<div>
		<div class="clear"></div>
	</div>
</span>

<div class="img" id="PicDetail">
 <?php 
    $query2 = "";
    $query2="select imgurl,id from weixin_commonshop_product_imgs where isvalid=true and product_id=-1 and  customer_id=".$customer_id." order by id desc";
	$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
	while ($row2 = mysql_fetch_object($result2)) {
	   $imgurl=$row2->imgurl;
	   $i_id = $row2->id;
		
  ?>
		<script>
		   imgids = imgids + "<?php echo $i_id; ?> "+"_";
		</script>
    <div>
	     <a href="<?php echo $new_baseurl.$imgurl; ?>" target="_blank">
		 <img src="<?php echo $new_baseurl.$imgurl; ?>"></a>
	     <span onclick="delImg(<?php echo $i_id; ?>);">删除</span>
		<input type="hidden" name="PicPath[]" value="<?php echo $imgurl; ?>">
	</div>
	<?php }?>

<?php 
    $query2 = "";
    if($product_id>0){
	   $query2="select imgurl,id from weixin_commonshop_product_imgs where isvalid=true and  product_id=".$product_id." and  customer_id=".$customer_id." order by id desc";
		$result2 = mysql_query($query2) or die('Query failed: ' . mysql_error());
		while ($row2 = mysql_fetch_object($result2)) {
		   $imgurl=$row2->imgurl;
		   $i_id = $row2->id;
		
	  ?>
		<script>
		   imgids = imgids + "<?php echo $i_id; ?> "+"_";
		</script>
		<div>
			 <a href="<?php echo $new_baseurl.$imgurl; ?>" target="_blank">
			 <img src="<?php echo $new_baseurl.$imgurl; ?>"></a>
			 <span onclick="delImg(<?php echo $i_id; ?>);">删除</span>
			<input type="hidden" name="PicPath[]" value="<?php echo $imgurl; ?>">
		</div>
<?php } 
 }
 ?>
  
</div>
</span>
<div class="clear"></div>
</div>

<?php 
mysql_close($link);
?>
<script type="text/javascript">  	
	if(imgids.length>0){
	   imgids= imgids.substring(0,imgids.length-1);
	   parent.setParentImgIds(imgids);
	}	
	function delImg(id){
	   url = "iframe_images.php?op=del&i_id="+id+"&customer_id=<?php echo $customer_id; ?>&product_id=<?php echo $product_id; ?>";
	   document.location= url;
	}
  
</script>  
</body>
</html>