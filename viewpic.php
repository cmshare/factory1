<?php require('include/conn.php');
$id=@$_GET["id"];
$granted=false;
if(is_numeric($id)){
  OpenDB();
  $row=$conn->query("select name,price0,price1,price3,onsale from `mg_product` where id=$id",PDO::FETCH_NUM)->fetch();
  if($row){
    $ProductName=$row[0];
    $PriceMarket=$row[2];
    $PriceWholesale=($row[4]>0 && $row[5]>time())?$row[1]:$row[3];
    $ShopUserID=CheckLogin(0);
    if($ShopUserID>0){
      $row=$conn->query("select grade,deposit,score from `mg_users` where id=$ShopUserID",PDO::FETCH_NUM)->fetch();
      $ShopUserGrade=$row[0];
      $ShopUserDeposit=$row[1];
      $ShopUserScore=$row[2];
      if($ShopUserGrade>2 && ($ShopUserScore>0 || $ShopUserDeposit>100))$granted=true;
    }
  }
  CloseDB(); 
  $pic_url=product_pic($id,$granted?2:1);
}
?><HTML>
<HEAD>	
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<META http-equiv="Keywords" content="产品图片,化妆品,南京化妆品批发,韩国化妆品批发,进口化妆品批发,欧美化妆品批发">
<META http-equiv="Description" content="主要让用户查看化妆品高清晰大图片,产品图片。">
<title>产品图片- <?php echo $ProductName;?> - 化妆品批发</title>
<STYLE type=text/css>
<!--
  BODY{FONT-FAMILY:ARIAL;FONT-SIZE:9pt;COLOR:#000000;MARGIN:0px;}
  A:link { COLOR: #000000; TEXT-DECORATION: none;}
  A:visited {	COLOR:#000000; TEXT-DECORATION: none}
  A:hover {COLOR: red; TEXT-DECORATION: underline}
  TD {COLOR: #000000; FONT-FAMILY: "宋体"; FONT-SIZE: 13px}
-->
</style>
<script>
var OriginWidth,OriginHeight,MyImage,ZoomRatio=1,ZoomStep=0.2;
function GetOriginSize(ImgD){
  MyImage=ImgD;
  OriginWidth=MyImage.width;
  OriginHeight=MyImage.height;
}
function ImageReset(){
  MyImage.width=OriginWidth;
  MyImage.height=OriginHeight;
  ZoomRatio=1;
}
function ImageZoomIn(){
  ZoomRatio+=ZoomStep;
  MyImage.width=OriginWidth*ZoomRatio;
  MyImage.height=OriginHeight*ZoomRatio;
}
function ImageZoomOut(){
  if(ZoomRatio-ZoomStep>0.01){
    ZoomRatio-=ZoomStep;
    MyImage.width=OriginWidth*ZoomRatio;
    MyImage.height=OriginHeight*ZoomRatio;
  }
}
function WareInfo(){self.location.href="/products/<?php echo $id;?>.htm";}
function NaviToHome(){self.location.href="/#";}
</script>
</HEAD>
<body topmargin="0" leftmargin="0">
<table border=0 cellSpacing=0 cellPadding=0 width="100%" height="100%" valign="top">
<tr><td align="center" height="25"><table border=0 width="100%" height="25" cellSpacing=0 cellPadding=0 background="/images/topbar1.gif" valign="top"><td nowrap width="20%"><img src="/images/arrow2.gif" width="6" height="7">&nbsp;当前位置：<a href="http://<?php echo WEB_DOMAIN;?>/#"><?php echo WEB_NAME;?></a> &gt;&gt; 产品图片仓库</td><td width="60%" align="center" nowrap><a href="/products/<?php echo $id;?>.htm" style="COLOR: #FF6600;font-weight:bold; TEXT-DECORATION: underline"><?php echo $ProductName;?></a></td><td nowrap width="20%" align="right">价格：<font color="#8F8F8F">￥<strike><?php echo round($PriceMarket,2);?></strike>元</font> <font color="#FF0000">￥<b><?php echo round($PriceWholesale,2);?></b>元</font></td></tr></table></td></tr>
<tr><td align="center" height="95%">
<img src="<?php echo $pic_url;?>" onmousemove="window.event.returnValue=false"  onload="GetOriginSize(this)" alt="<?php echo $ProductName;?>"></td></tr>
<tr><td align="center" height="5%"><input type="button" value="返回首页" onclick="NaviToHome()">&nbsp;<input type="button" value=" 缩 小 " onclick="ImageZoomOut()">&nbsp;<input type="button" value="原始尺寸" onclick="ImageReset()">&nbsp;<input type="button" value=" 放 大 " onclick="ImageZoomIn()">&nbsp;<input type="button" value="产品介绍" onclick="WareInfo()"></td></tr>
</table>
</body>
</html>
