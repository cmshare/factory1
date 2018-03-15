<?php require('include/conn.php');
require('user/m_reviews.php');
OpenDB();
CheckLogin(0);

$id=$_GET['id'];
if(is_numeric($id) && $id>0){
  $PresentScore=0;
  $roware=$conn->query('select * from `mg_product` where id='.$id.' and recommend>=0',PDO::FETCH_ASSOC)->fetch();  
}
else{
  $id=$_GET['pid'];
  if(is_numeric($id) && $id>0){
    $roware=$conn->query('select score,available,remarks from `mg_present` where productid='.$id,PDO::FETCH_NUM)->fetch();
    if($roware){
      $PresentScore=$roware[0];
      $PresentAvailable=$roware[1];
      $PresentRemarks=$roware[2];
      $roware=$conn->query('select * from `mg_product` where id='.$id,PDO::FETCH_ASSOC)->fetch();  
    }
  }
}
if(empty($roware)){
  echo '<script language="javascript">alert("此产品不存在或已经下架！");</script>';
  echo '<p align=center><a href="'.WEB_ROOT.'#">点击这里返回主页</a></p>';
  CloseDB();
  exit(0);
}

#遍历父级品牌分类 
$LinkSortGuider='';
$brand = $roware['brand'];
$PID = $brand; 
while($PID){
  $row1=$conn->query('select id,title,parent,isbrand from `mg_brand` where id='.$PID,PDO::FETCH_ASSOC)->fetch();
  if($row1){
    if($row1['isbrand'] && empty($ProductBrand)) $ProductBrand=$row1['title']; 
  }
  else{
    echo '<script LANGUAGE="javascript">alert("您输入的参数非法，请正确操作！");history.go(-1);</script>';
    CloseDB();
    exit(0);
  }
  $LinkSortGuider = '&nbsp;&gt;&gt;&nbsp;<a href="/category/cat'.$row1['id'].'.htm">'.$row1['title'].'</a>'.$LinkSortGuider;
  if(empty($ParentBrand)) $ParentBrand=$row1['parent'];
  $PID = $row1['parent'];
}
if(empty($ProductBrand)) $ProductBrand='其它品牌';  


#遍历子级品牌分类 
function sorts($selec){
 global $conn,$CatList;
 $res=$conn->query('select id from `mg_brand` where parent = '.$selec.' order by sortorder',PDO::FETCH_NUM);
 foreach($res as $row1){
    $CatList.=', '.$row1[0];
    sorts($row1[0]);
 }
}
$CatList =$brand;
sorts($brand);


$StarRecommend=$roware['recommend'];
if($StarRecommend<3)$StarRecommend=3;
else if($StarRecommend>9)$StarRecommend=5;
else $StarRecommend=4;

$KeywordsArray=array('品牌','原装','进口','美白','去斑','祛斑','抗皱','消痘','网络热销','正品','热卖','香水','香薰','精油','面膜','护肤品','彩妆','韩国','日本','欧美','香港','台湾','澳门','上海','北京','重庆','江苏','南京','镇江','常州','无锡','宜兴','江阴','苏州','昆山','张家港','连云港','扬州','徐州','宿迁','淮安','盱眙','盐城','泰安','泰州','泰兴','靖江','南通','宁波','杭州','山东','济南','威海','廊坊','青岛','日照','成都','广东','广州','惠州','深圳','武汉','珠海','汕头','汕尾','厦门','浙江','温州','绍兴','金华','义乌','安徽','芜湖','六安','马鞍山','蚌埠','安庆','黄山','合肥','阜阳','滁州','山西','阳泉','洛阳','天津','太原','吉林','沈阳','辽宁','长春','长沙','大连','石家庄','西安','新疆','东莞','福州','湖北','湖南','福建','黑龙江','湖州','东营','中山','临夏','临汾','临沂','临沧','丹东','丽水','丽江','乌兰察布盟','乌海','乌鲁木齐','乐山','九江','云南','昆明','三明','三门峡','上饶','东川','七台河','万县','三亚','云浮','亳州','伊克昭盟','伊春','伊犁哈萨克','佛山','佳木斯','保定','保山','信阳','克孜勒苏柯尔克孜','克拉玛依','六盘水','兰州','兴安盟','其','内江','内蒙古','包头','北海','十堰','南充','南宁','南宁','南平','南昌','南阳','博尔塔拉蒙古自治州','双鸭山','台州','吉安','吐鲁番','吕梁','周口','呼伦贝尔盟','呼和浩特','和田','咸宁','哈密','哈尔滨','哲里木盟','唐山','商丘','商洛','喀什','嘉兴','嘉峪关','四川','四平','固原','塔城','大兴安岭','大同','大庆','大理','天水','娄底','孝感','宁夏','宁德','安康','安阳','安顺','定西','宜宾','宜昌','宜春','宝鸡','宣州','宿州','山南','岳阳','巢湖','巴中','巴彦淖尔盟','巴音郭楞','常德','平凉','平顶山','广元','广安','广西','庆阳','延安','延边','开封','张家界','张家口','张掖','德宏','德州','德阳','忻州','怀化','怒江','思茅','恩施','承德','抚州','抚顺','拉萨','揭阳','攀枝花','文山','新乡','新余','日喀则','昌吉','昌都','昭通','晋中','晋城','景德镇','曲靖','朔州','朝阳','本溪','松原','松花江','林芝','果洛','枣庄','柳州','株洲','桂林','梅州','梧州','楚雄','榆林','武威','毕节','永州','汉中','江西','江门','池州','沧州','河北','河南','河池','河源','泉州','泸州','济宁','海东','海北','海南','海口','海西','涪陵','淄博','淮北','淮南','清远','渭南','湘潭','湘西','湛江','滨州','漯河','漳州','潍坊','潮州','濮阳','烟台','焦作','牡丹江','玉林','玉溪','甘南','甘孜','甘肃','白城','白山','白银','百色','益阳','盘锦','石嘴山','石河子','秦皇岛','红河','绥化','绵阳','聊城','自贡','舟山','茂名','荆沙','荆门','莆田','莱芜','菏泽','萍乡','营口','衡水','衡阳','衢州','襄樊','西双版纳','西宁','西藏','许昌','贵州','贵港','贵阳','赣州','赤峰','辽源','辽阳','达川','运城','迪庆','通化','遂宁','遵义','邢台','那曲','邯郸','邵阳','郑州','郴州','鄂州','酒泉','金昌','钦州','铁岭','铜仁','铜川','铜陵','银南','银川','锡林郭勒盟','锦州','锦西','长治','阜新','防城港','阳江','阿克苏','阿勒泰','阿坝','阿拉善盟','阿里','陇南','陕西','雅安','青海','鞍山','韶关','驻马店','鸡西','鹤壁','鹤岗','鹰潭','黄冈','黄南','黄石','黑河','黔江','黔西','齐齐哈尔','龙岩');

$HotKeyword=$KeywordsArray[$id % count($KeywordsArray)].'化妆品批发';
$PageKeywords=$roware['name'].',化妆品批发加盟,'.$HotKeyword;
$PageDescription='产品介绍：'.$roware['name'].'的详细功效、规格、图片、价格等信息,本公司提供各种品牌化妆品批发,诚招'.$HotKeyword.'加盟代理，产品包括护肤品批发,彩妆批发,洗发水批发,沐浴露批发,精油香水批发,支持小额批发,一手货源好,最低价格,现货供应,质量保证';
$PageTitle=$roware['name'].'－【招商代理】加盟'.$HotKeyword.'网';
include('include/page_head.php');?>
<TABLE align="center" cellSpacing="0" cellPadding="0" border="0" width="1000" style="background:url(/images/bg_mid.gif) repeat-x;">
<tr >
  <td width="200" valign="TOP">
  <table cellSpacing="0" cellPadding="0" width="100%" height="100%" border="0" style="background:url(/images/bg_left.gif) repeat-y;margin-top:30px;">
  <tr><td height="1%"><SCRIPT language="JavaScript" src="/include/guide_sort.js" type="text/javascript"></SCRIPT></td></tr>
  <tr><td height="99%"><?php include('include/guide_blank.php');?></td></tr>
  </table>
  </td>
  
  <td valign="top" width="800"  height="100%">
 	
  <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100% align="center"  border="0">
  <TR>
     <TD height="30" valign="bottom">&nbsp;&nbsp;<img src="/images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a><?php echo $LinkSortGuider;?></TD>
      </TR>
  <TR>
     <TD valign="top" style="background-image:url(/images/productbg.jpg);BACKGROUND-POSITION: center bottom; BACKGROUND-REPEAT: repeat-y;">
     
<!--------商品基本信息----------->
<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
<tr>
  <td width="10"></td><td width="300" valign="bottom"><a href="/viewpic.php?id=<?php echo $id;?>" target="_blank"><img src="<?php echo product_pic($id,2);?>" width=300 height=300 border=0 alt="<?php echo $roware['name'];?>"></a></td>
  <td width="5"></td><td width="485" valign="top">
    <table width="100%"  border="0" cellpadding="4" cellspacing="1" Bgcolor="#f2f2f2" id="TProduct">
    <tr>
      <td height=60 colspan=4 class="producttitle"><?php
        echo $roware['name'];
    	if($roware['onsale']>0) echo '<img src="/images/onsale.gif" height=16 width=16 alt="特价产品">';?></td>
    </tr>
    <tr>
      <td width="18%" height="30" bgcolor="#f7f7f7">【商品编号】</td>
      <td width="32%" bgcolor="#FFFFFF"><?php echo substr('0000'.$id,-5);?></td>
      <td width="18%" bgcolor="#f7f7f7">【商品品牌】</td>
      <td width="32%" bgcolor="#FFFFFF"><font color="#FF0000"><?php echo $ProductBrand;?></font></td>
    </tr>
    <tr>
      <td height="30" bgcolor="#f7f7f7">【推荐指数】</td>
      <td bgcolor="#FFFFFF"><img src="/images/<?php echo $StarRecommend;?>star.gif" width=64 height=12></td>
      <td height="30" bgcolor="#f7f7f7">【<?php echo ($roware['recommend']>0)?'上':'下';?>架时间】</td>
      <td bgcolor="#FFFFFF"><?php echo date('Y-m-d H:i:s',$roware['addtime']);?></td>
    </tr>  
    <tr>
      <td height="30" bgcolor="#f7f7f7">【商品规格】</td>
      <td bgcolor="#FFFFFF"><?php echo $roware['spec'];?>&nbsp;<?php echo $roware['unit'];?></td>
      <td height="30" bgcolor="#f7f7f7">【商品库存】</td>
      <td bgcolor="#FFFFFF"><?php echo ($roware['stock0']>0)?'<font color="#00BB00">有现货</font>':'<font color="#FF0000">无现货</font>';?></td>
    </tr><?php 
    if($PresentScore>0){?> 
    <tr>
      <td height="30" bgcolor="#f7f7f7">【赠品库存】</td>
      <td bgcolor="#FFFFFF"><?php echo ($PresentAvailable>$roware['stock0'])?$roware['stock0']:$PresentAvailable;?></td>
      <td bgcolor="#f7f7f7">【兑购积分】</td>
      <td bgcolor="#FFFFFF"><font color="#FF0000"><?php echo $PresentScore;?></font></td>
    </tr>
    </table>
    <table width="490"  border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>   
       <td height="56" style="BACKGROUND-IMAGE:url(/images/PresentRemarks.gif); BACKGROUND-REPEAT: no-repeat;BACKGROUND-POSITION: left center%"></td>
    </tr>  
    <tr>
       <td valign="top" style="height:70px;padding-left:8px;padding-right:8px;BORDER-COLLAPSE: collapse; border-left:1px solid #cccccc;border-right:1px solid #cccccc;border-bottom:1px solid #cccccc"><?php echo ($PresentRemarks)?$PresentRemarks:'暂无';?></td>
    </tr>
    </table><?php
    }
    else if($roware['recommend']>0){?>

    <tr><td colspan="4" height="18" bgcolor="#f7f7f7"></td></tr>             
    <tr>
       <td height="30" bgcolor="#f7f7f7">【市 场 价】</td>
       <td bgcolor="#FFFFFF" style="font-weight:bold;text-decoration:line-through">￥<?php echo round($roware['price1'],2);?>元</td>
	     <td bgcolor="#f7f7f7">【批 发 价】</td> 
	     <td bgcolor="#FFFFFF" style="font-weight:bold;color:#FF0000">￥<?php echo round($roware['price3'],2);?>元</td>
    </tr>
    <tr>
       <td height="30" bgcolor="#f7f7f7">【<font color=#FF3300>大客户价</font>】</td>
       <td bgcolor="#FFFFFF"><font color=#888888>非等级查看</font></td>
       <td bgcolor="#f7f7f7">【订购数量】</td> 
       <td bgcolor="#FFFFFF"><table height="23px" border="0" cellpadding="0" cellspacing="0" class="buyaction"><tr><td width="13" onclick="changeNumber(0)"></td><td width="50"><input type="text" id="orderamount" value="1" size="4"></td><td width="13" onclick="changeNumber(1)"></td><td>&nbsp;件</td></td></tr></table></td>
    </tr>
    </table> 
    <table width="100%" border="0" cellpadding="0" cellspacing="0" id="warepad">
    <tr valign="bottom"><td colspan="4"></td></tr>
    <tr valign="bottom">
    	<td width="50%" height="65"></td>
    	<td width="50%"><a href="javascript:execAdd2Cart()"><img src="/images/add2cart.gif" width="182" height="41" border="0"></a><a href="javascript:AddToFavor(<?php echo $id;?>)"><img src="/images/add2fav.gif" width="55" height="22" border="0"></a></td>
    </tr>
    </table><script>function execAdd2Cart(){var obj=document.getElementById('orderamount');var amount=(obj)?obj.value:'1';AddToCart(<?php echo $id;?>,amount);}	function changeNumber(ki){var elem = document.getElementById("orderamount");var num = parseInt(elem.value);if(ki)elem.value=num+1;else if(num>1)elem.value=num-1;} </script><?php
    }
    else{?>
    <tr>
    <td height="160" bgcolor="#f7f7f7" colspan="4" align="center" style="font-size:16px;color:#0000FF">抱歉，此产品已经下架！</td>
    </tr>
   </table><?php
    }?>
  </td>
</tr>  
</table>
<!-------商品介绍---------->
<table width="100%"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#ffffff">
<tr>   
  <td height="56" style="BACKGROUND-IMAGE:url(/images/wareresume.gif); BACKGROUND-REPEAT: no-repeat;BACKGROUND-POSITION: center center"></td>
</tr>  
<tr>
  <td style="padding-left:12px;padding-top:6px;line-height: normal" valign="top"><div style="width:755px;OVERFLOW:hidden;" oncontextmenu="event.cancelBubble=true;return false;"><?php echo $roware['description'];?></div></td>
</tr>
</table>
<!-------商品评论---------->
<table width="100%"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#ffffff">
<tr>   
  <td height="55" style="BACKGROUND-IMAGE:url(/images/wareremark.gif); BACKGROUND-REPEAT: no-repeat;BACKGROUND-POSITION: center center%"></td>
</tr>  
<tr>
  <td width="100%"><div id="productreviews"><?php if($LoginUserID)show_product_reviews($LoginUserID,$id);?></div>
     <form name="reviews" method="post" action="/user/review.php" style="margin:0px">
     <table style="border:1px solid #dfdfdf;" width="99%" align="center" cellSpacing="0" cellPadding="0">
     <tr>
       <td width="100%" colspan="2">
         <input type="hidden" name="mode" value="add"><input type="hidden" name="productid" value="<?php echo $id;?>">
         <textarea name="remark" rows="3" wrap="VIRTUAL" style="width: 100%; font-size: 9pt; border: 1 solid #DFDFDF;" cols="20"><?php if($LoginUserID==0)echo '登录本站后，您才能发表评论！！！';?></textarea>
       </td>
     </tr>
     <tr>
       <td width="80%"><select name="vote" size="1"><option value="0">　　打 分</option><option value="1">☆</option><option value="2">☆☆</option><option value="3">☆☆☆</option><option value="4">☆☆☆☆</option><option value="5">☆☆☆☆☆</option></select>
       </td>
       <td width="20%" align="right" nowrap>
         (256个字以内)&nbsp;<input type="submit" name="send_review" <?php if($LoginUserID==0) echo 'disabled';?> value="发表评论" onclick="if(this.form.remark.value==''){alert('评论不能为空！');return(false);}">
       </td>
     </tr>
     </table></form>
  </td>
</tr>
<tr>   
  <td height="80" style="BACKGROUND-IMAGE:url(/images/buysteps.gif); BACKGROUND-REPEAT: no-repeat;BACKGROUND-POSITION: center bottom"></td>
</tr>   
</table><?php
#同类商品滚动栏 开始

$res=$conn->query('select id,name,price1,price3 from `mg_product` where brand in  ('.$CatList.') and recommend>0 and id<>'.$id.' order by recommend desc,addtime desc limit 12',PDO::FETCH_ASSOC); 
if($res->fetch()){?>
<TABLE cellSpacing=0 cellPadding=0 width="100%" align="center"  border="0" bgcolor="#ffffff">
<tr>
  <td height="56" colspan="2" style="BACKGROUND-IMAGE:url(/images/waretongleihot.gif); BACKGROUND-REPEAT: no-repeat;BACKGROUND-POSITION: center center%"></td>
</tr>
<tr>
  <td align="center">
    <div id="MarqueeDemoA" style="OVERFLOW: hidden; WIDTH: 798px; height:230px; COLOR: #ffffff;">
    <table cellSpacing=0 cellPadding=0 width="100%" align="center" border="0">
    <tr>
    	 <td id="MarqueeDemoB">
      	 <table cellSpacing=0 cellPadding=0 border="0" class="WareShow"><?php

foreach($res as $row){?>
    <td><div class="pimg"><a href="/products/<?php echo $row['id'];?>.htm"><img width="160" height="160" src="<?php echo product_pic($row['id'],0);?>" alt="<?php echo $row['name'];?>" border="0"></a></div>
    <div class="pbox"><a href="/products/<?php echo $row['id'];?>.htm" class="plink"><?php echo $row['name'];?></a><span class="price3">￥<?php echo round($row['price3'],2);?>元</span><span class="price1">￥<?php echo round($row['price1'],2);?>元</span><img class="pbuy" src="/images/gobuy.gif" width="22" height="12" alt="将该商品放入购物车" onClick="AddToCart(<?php echo $row['id'];?>)"></div>
    </td><?php
}
$product_links=$HotKeyword;  #seo优化以形成所有product页面链接的闭环。
$res=$conn->query('select id,name from `mg_product` where id='.($id-1).' or id='.($id+1),PDO::FETCH_NUM);
foreach($res as $row){
  $product_links.=' | <a href="/products/'.$row[0].'.htm">'.$row[1].'</a>';
}?>
    </tr></table></td>
    <td id="MarqueeDemoC"><?php echo $product_links;?></td>
   </tr>
   </table></div></td>
     
  </tr>
</table><?php
}?>
<!-------同类商品滚动栏 结束---------->
 
        </td>
      </tr>
    </table>
    <!-------商品信息结束---------->
  </td>
</tr>
<tr>
  <td height="5"><script>UpdateProductInfo(<?php echo $id;?>);</script></td>
</tr>	
</table><?php
include('include/page_bottom.htm');
CloseDB();?>
</body>
</html>
