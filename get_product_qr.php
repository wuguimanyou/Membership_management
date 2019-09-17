<?php
header("Content-type: text/html; charset=utf-8"); 
require('../config.php');
$link = mysql_connect(DB_HOST,DB_USER,DB_PWD);
mysql_select_db(DB_NAME) or die('Could not select database');
require('../customer_id_decrypt.php'); //导入文件,获取customer_id_en[加密的customer_id]以及customer_id[已解密]
require('../proxy_info.php'); //解决fenxiao无法获取正常路径
include '../common/phpqrcode/phpqrcode.php';  

function autowrap($fontsize, $angle, $fontface, $string, $num) {
	// 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度 
	$content = "";
	// 将字符串拆分成一个个单字 保存到数组 letter 中
	//echo mb_strlen($string);
/* 	if(mb_strlen($string) > 78){
		$string = mb_substr($string, 0, 78);
	} */
	for ($i=0;$i<mb_strlen($string,'utf-8');$i++) {
		$letter[] = mb_substr($string, $i, 1,'utf-8');
	}
	foreach ($letter as $l) {
		$teststr = $content.$l;
		$testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
		// 判断拼接后的字符串是否超过预设的宽度
		/* if (($testbox[2] > $width) && ($content !== "")) {
			//$content = $content."\r\n";
			$content = $content.PHP_EOL;
		} */
		if( is_int(mb_strlen($teststr,'utf-8')/$num) ){
			$content = $content.PHP_EOL;
		}
		$content = $content.$l;
	}
	return $content;
}

		
$new_baseurl    = "http://".$http_host; //新商城图片显示
$pid            = $configutil -> splash_new($_POST["pid"]);//产品id
$owner_id       = $configutil -> splash_new($_POST["owner_id"]);//微店id
$user_id        = $configutil -> splash_new($_POST["user_id"]);
$shop_name      = $configutil -> splash_new($_POST["shop_name"]);//商城名称
//$p_name         = $configutil -> splash_new($_POST["p_name"]);//产品名称
//$p_now_price    = $configutil -> splash_new($_POST["p_now_price"]);//产品价格
//$default_imgurl = $configutil -> splash_new($_POST["default_imgurl"]);//产品封面图

$query = "SELECT default_imgurl,name,now_price,orgin_price FROM weixin_commonshop_products where  isvalid=true and id='" . $pid."'";
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
$default_imgurl     = "";//封面图片
$p_name             = "";
$p_now_price        = 0;
$orgin_price        = 0;
while ($row = mysql_fetch_object($result)) {
	$default_imgurl      = $row->default_imgurl;
    $p_name              = $row->name;
	$p_now_price         = $row->now_price;
	$orgin_price         = $row->orgin_price;
}


$default_imgurl = $new_baseurl.$default_imgurl;
//echo $default_imgurl;return;
/* $query = "SELECT weixin_name from weixin_users where isvalid=true and  customer_id=".$customer_id." and id=".$user_id." limit 0,1";
$weixin_name = "";
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
while ($row = mysql_fetch_object($result)) {
  $weixin_name = $row->weixin_name;
  break;
} */

$url="../up/product/".$customer_id."/".$user_id."/".$pid."/";
if(!file_exists($url)){
	mkdir($url,0777,true);
}

/*二维码开始*/
$value = 'http://'.$http_host.'/weixinpl/common_shop/jiushop/forward.php?pid='.$pid.'&exp_user_id='.$user_id.'&owner_id='.$owner_id.'&type=3&customer_id='.$customer_id_en; //二维码内容 
$errorCorrectionLevel = 'L';//容错级别 
//$matrixPointSize = 13.07;//生成图片大小 
$matrixPointSize = 4.0;//生成图片大小 
$qr_name=$url.$pid.'_qrcode.png';
QRcode::png($value, $qr_name, $errorCorrectionLevel, $matrixPointSize, 2);
/*二维码结束*/

/*封面图开始*/
$filename=$default_imgurl;
list($width, $height, $type) = getimagesize($filename); //获取图片宽高

$percent = 640/$width; //计算倍数
$new_width = 640; 
$new_height = $height * $percent; 
$image_p = imagecreatetruecolor($new_width, $new_height); 
switch ($type) { 
	case 1 : 
		$image = imageCreateFromGif($filename); 
	break; 
	case 2 : 
		$image = imageCreateFromJpeg($filename); 
	break; 
	case 3 : 
		$image = imageCreateFromPng($filename); 
	break; 
} 
//$image = imagecreatefromjpeg($filename); 
imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
Imagejpeg($image_p,$url.$pid.'.jpg'); 
/*封面图结束*/
  
/*背景图开始*/
list($qr_width, $qr_height) = getimagesize($qr_name); //获取二维码图片宽高
$new_width2 = 640; 
$new_height2 = $new_height+$qr_height+60; 
$image_p2 = imagecreatetruecolor($new_width2, $new_height2); 
$bg = imagecolorallocate($image_p2, 255, 255, 255);
$p_name_color= imagecolorallocate($image_p2, 0, 0, 0);
$p_now_price_color= imagecolorallocate($image_p2, 255, 0, 0);
$orgin_price_color= imagecolorallocate($image_p2, 166, 166, 166);
imagefill($image_p2, 0, 0, $bg);

$p_name_width = 50;//产品名字距离背景图的长度
$new_qr_height = $new_height+30;//二维码距离背景图的高度 
$new_p_name_height = $new_height+80;//产品名距离背景图的高度 

$font = "../common/tupian/simhei.ttf";
$num = 12;//名字18个后换行
//$p_name ="长按识别二维码或扫一扫购买和法国和风格和法国问问惹人未发生地方";
$p_name = autowrap(21, 0, $font, $p_name, $num); // 自动换行处理
$p_name_box = imagettfbbox(20, 0, $font, $p_name);
//print_r($p_name_box);
$p_name_h = $p_name_box[3]- $p_name_box[5];
$new_now_price_height = $new_p_name_height+$p_name_h+30;//价钱距离背景图的高度 
$p_now_price_box =  imagettfbbox(30, 0, $font, "￥".$p_now_price);//获取现价信息
//print_r($p_now_price_box);
$p_now_price_w = $p_name_width+$p_now_price_box[2]+15;//原价X轴的长度
$line = "-";
$shu = round($p_now_price_box[2]/30);
for($i=0;$i<=$shu;$i++){
	$line .= "-";
}

imagettftext($image_p2,21,0,$p_name_width,$new_p_name_height,$p_name_color,$font,$p_name);
imagettftext($image_p2,30,0,$p_name_width,$new_now_price_height,$p_now_price_color,$font,"￥".$p_now_price);
imagettftext($image_p2,25,0,$p_now_price_w,$new_now_price_height,$orgin_price_color,$font,"￥".$orgin_price);
imagettftext($image_p2,30,0,$p_now_price_w,$new_now_price_height,$orgin_price_color,$font,$line);

$image2 = imagecreatefromjpeg($filename2); 
imagecopyresampled($image_p2, $image2, 0, 0, 0, 0, $new_width2, $new_height2, $width2, $height2); 
Imagejpeg($image_p2,$url.'bg.jpg');
/*背景图结束*/
/*二维码背景边框开始*/
$qrcode_bg = imagecreatetruecolor($qr_height+2, $qr_height+2); 
imagefill($qrcode_bg, 0, 0, $orgin_price_color);
Imagejpeg($qrcode_bg,$url.'qrcode_bg.jpg');
/*二维码背景边框开始*/
/*水印开始*/  
$imgs = array();
$imgs[0] = $url.$pid.'.jpg';
$imgs[1] = $url.'qrcode_bg.jpg';
$imgs[2] = $url.$pid.'_qrcode.png';

$target  =$url.'bg.jpg'; //背景图片

$target_img = Imagecreatefromjpeg($target);
 
$source= array();

foreach ($imgs as $k=>$v){
$imageSize = getimagesize($v);
switch($imageSize[2])
{
	case 1:$source[$k]['source'] = imagecreatefromgif($v);break;
	case 2:$source[$k]['source'] = imagecreatefromjpeg($v);break;
	case 3:$source[$k]['source'] = imagecreatefrompng($v);break;
}
 
$source[$k]['size'] = getimagesize($v);  
} 

imagecopy($target_img,$source[0]['source'],0,0,0,0,$source[0]['size'][0],$source[0]['size'][1]);
imagecopy($target_img,$source[1]['source'],399,$new_qr_height-1,0,0,$source[1]['size'][0],$source[1]['size'][1]);
imagecopy($target_img,$source[2]['source'],400,$new_qr_height,0,0,$source[2]['size'][0],$source[2]['size'][1]);




Imagejpeg($target_img,$url.'product_qr_'.$pid.'.jpg');
$data = $url."product_qr_".$pid.".jpg?ver=".time(); 
echo $data;
?>
