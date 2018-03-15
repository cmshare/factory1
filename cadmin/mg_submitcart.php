<?php require('includes/dbconn.php');
 CheckLogin();
$ShopUserID=@$_GET['userid'];
if(is_numeric($ShopUserID) && $ShopUserID>0){
  OpenDB();
  $bExist=$conn->query('select id from mg_favorites where userid='.$ShopUserID.' and (state&0x2) and amount>0 limit 1')->fetchColumn(0);
  if(!$bExist)PageReturn('对不起，您的购物车中还没有商品！',-1);
} else PageReturn('<p align=center>参数错误</p>',0);?>
<html>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<title>提交订单</title>
<style type="text/css">
<!--
A{TEXT-DECORATION: none;}
A:link    {COLOR: #000000; TEXT-DECORATION: none}
A:visited {COLOR: #000000; TEXT-DECORATION: none}
A:hover   {COLOR: #FF0000; TEXT-DECORATION: underline}
TD   {FONT-FAMILY:宋体;FONT-SIZE: 9pt;line-height: 150%;}
-->
</style>
<body topmargin="0" leftmargin="0"> 
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr>
  <td background="images/topbg.gif" height=30>
<b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_checkcart.php?id=<?php echo $ShopUserID;?>">购物车管理</a> -&gt; <font color=#FF0000>递交订单</font></b>
  </td>
</tr>
<tr> 
   <td  height="100%" valign="top" bgcolor="#FFFFFF"><?php
$row_user=$conn->query('select mg_users.*,mg_usrgrade.title as viptitle from (mg_users inner join mg_usrgrade on mg_users.grade=mg_usrgrade.id) where mg_users.id='.$ShopUserID,PDO::FETCH_ASSOC)->fetch();
if($row_user){
  $ShopUserName=$row_user['username'];
  $ShopUserGrade=$row_user['grade'];  
}
else PageReturn('此用户名不存在！',0);

$AdminDepotIndex=GetAdminDepot();

function GenOrderNO(){
  return date('ymdHis').sprintf('%04d',rand(1,9999));
}
  
if(@$_GET['mode']=='confirmbill'){
     $Order_Receipt=FilterText(trim($_POST['receipt']));
     if(empty($Order_Receipt)) PageReturn('请填写收货人姓名！',-1);
  
     $Order_Address=FilterText(trim($_POST['address']));
     if(empty($Order_Address)) PageReturn('请填写详细地址！',-1);

     $Order_Usertel=FilterText(trim($_POST['usertel']));
     if(empty($Order_Usertel)) PageReturn('请填写电话！',-1);
  
     $Order_IDNumber=trim($_POST['operator']);
     if(is_numeric($Order_IDNumber) && $Order_IDNumber>0){
       $row=$conn->query('select username,depot from mg_admins where idnumber<='.$Order_IDNumber.' and idnumber2>='.$Order_IDNumber,PDO::FETCH_NUM)->fetch();
       if($row){
          $Order_Operator=$row[0];
          $Order_Depot=$row[1];
       }
       else goto label_invalid_operator;
     }
     else{
       label_invalid_operator:
       PageReturn('请选择订单处理客服！',-1);
     }

     $Order_DeliveryMethod=FilterText(trim($_POST['deliverymethod']));
     $Order_PayMethod=FilterText(trim($_POST['paymethod']));
     $Order_UserRemark=FilterText(trim($_POST['userremark']));

     if($_POST['ordertype']=='1'){//内部订单
       	$State=-1;
        $Exporter=0;
      }
      else{
       	$State=2;
       	$Exporter=$Order_Depot;
      } 
  
     $conn->exec('lock tables mg_orders write'); 

     label_gen_ordername:
     $OrderName=GenOrderNO();
     $bExist=$conn->query('select id from mg_orders where ordername=\''.$OrderName.'\'')->fetchColumn(0);
     if($bExist)goto label_gen_ordername;
     $sql="mg_orders set ordername='$OrderName',state=$State,receipt='$Order_Receipt',adjust=0,deliveryfee=0,weight=0,importer=0,exporter=$Exporter,address='$Order_Address',paymethod='$Order_PayMethod',deliverymethod='$Order_DeliveryMethod',deliverycode='',usertel='$Order_Usertel',userremark='$Order_UserRemark',adminremark='',username='$ShopUserName',operator='$Order_Operator',support=$Order_IDNumber,actiontime=unix_timestamp()";
     if(!$conn->exec('update '.$sql.' where state=0 limit 1')&&!$conn->exec('insert into '.$sql))PageReturn('未知错误',-1);
     $conn->exec('unlock tables');  
       
       //rs_goods.open "select * from `OrderGoods` where OrderName=''",conn,1,3
    $res=$conn->query('select mg_product.id,mg_product.name,mg_product.score,mg_product.price0,mg_product.price'.$ShopUserGrade.' as myprice,mg_product.stock'.$AdminDepotIndex.' as stock,mg_product.onsale,mg_favorites.remark,mg_favorites.amount from (mg_product inner join mg_favorites on mg_product.id=mg_favorites.productid) where mg_favorites.userid='.$ShopUserID.' and (mg_favorites.state&0x2) and mg_favorites.amount>0',PDO::FETCH_ASSOC);
    $TotalPrice = 0;
    $TotalScore = 0;
    foreach($res as $row){
      $ProductID=$row['id']; 
      $ProductName=$row['name'];
      $Amount=$row['amount'];
      $ProScore=$row['score'];
      $myprice=$row['myprice'];

      if(($row['onsale']&0xf)>0 && $ShopUserGrade>2){
        if($row['onsale']>time() && $row['price0']<$myprice) $myprice=$row['price0'];
      }
      $GoodsPaid=round($myprice*$Amount,2);
      $TotalPrice+=$GoodsPaid;
      $TotalScore+=$ProScore*$Amount;
      $sql="mg_ordergoods set ordername='$OrderName',productid=$ProductID,productname='$ProductName',amount=$Amount,price=$myprice,score=$ProScore,remark='{$row['remark']}',audit=1";
      if(!$conn->exec('update '.$sql.' where ordername is null limit 1') && !$conn->exec('insert into '.$sql)) PageReturn('未知错误');
    }
    $conn->exec("update mg_orders set totalprice=$TotalPrice,totalscore=$TotalScore where ordername='$OrderName'");
    $conn->exec("update mg_favorites set state=state-2 where userid=$ShopUserID and (state&0x2)");      
    PageReturn('<br><br><p align=center>订单递交成功！订单号：'.$OrderName.'<ul><li><a href="mg_checkorder.php?ordername='.$OrderName.'" style="color:#0000FF">查看订单</a></li><li><a href="mg_checkcart.php?id='.$ShopUserID.'" style="color:#0000FF">返回购物车</a></li></ul></p>',0);
}?>

<SCRIPT LANGUAGE="JavaScript">
<!--
String.prototype.trim = function() 
{ return this.replace(/(^\s*)|(\s*$)/g, ""); 
} 

function GetRandInt(min,max){
 return Math.round(Math.random()*(max-min))+min;
}

function checkform(myform){
  if(myform.receipt.value.trim()==""){
    myform.receipt.focus();
    alert("对不起，请填写收货人姓名！");
    return false;
  }
 
  if(myform.address.value.trim()==""){
    myform.address.focus();
    alert("对不起，请填写收货人详细收货地址！");
    return false;
  }
 
  if(myform.usertel.value.trim()==""){
    myform.usertel.focus();
    alert("对不起，请留下收货人联系电话！");
    return false;
  }

  if(myform.operator.selectedIndex==0){
    alert("您还没有选择客服！");
    return false;
  }
  if(confirm("订单递交后购物车自动清空，确定要递交订单？")){
    if(myform.operator.value=='0'){
       myform.operator.selectedIndex=GetRandInt(1,myform.operator.length-2); 
    }
  }
  else{
    return false;
  }
  myform.submitbtn.disabled=true;   
}
//-->
</SCRIPT>
<table width="800" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#FFFFFF">
<tr>
  <td width="100%" bgcolor="#656565">
    <table border="0" cellspacing="1" cellpadding="4" align="center" width="100%" bgcolor="#f2f2f2">
    <tr bgcolor="#f7f7f7" height="25" align="center">
      <td width="50"><span class="style3">编 号</span></td>
      <td width="450"><span class="style3">商 品 名 称</span></td>
      <td width="50"><span class="style3">数 量</span></td>
      <td width="50"><span class="style3">单 价</span></td>
      <td width="50"><span class="style3">合 计</span></td>
      <td width="50"><span class="style3">备 注</span></td>
    </tr><?php
    $res=$conn->query('select mg_product.id,mg_product.name,mg_product.score,mg_product.price0,mg_product.price'.$ShopUserGrade.' as myprice,mg_product.stock'.$AdminDepotIndex.' as stock,mg_product.onsale,mg_favorites.remark,mg_favorites.amount from (mg_product inner join mg_favorites on mg_product.id=mg_favorites.productid) where mg_favorites.userid='.$ShopUserID.' and mg_favorites.state>1 and mg_favorites.amount>0 order by mg_product.name',PDO::FETCH_ASSOC);

    $TotalPrice = 0;
    $TotalScore = 0;
    $TotalRecords=0;
    foreach($res as $row){
      $TotalRecords++;
      $Amount=$row['amount'];
      $ProScore=$row['score'];
      $myprice=$row['myprice'];
      if(($row['onsale']&0xf)>0 && $ShopUserGrade>2){
        if($row['onsale']>time() && $row['price0']<$myprice) $myprice=$row['price0'];
      }
      $GoodsPaid=round($myprice*$Amount,2);
      $TotalPrice+=$GoodsPaid;
      $TotalScore+=$ProScore*$Amount;
      if($row['remark'])$WareRemark='<MARQUEE onmouseover="this.stop()" onmouseout="this.start()" scrollAmount="2" scrollDelay="100" width="100%">'.$row['remark'].'</MARQUEE>';
      else $WareRemark='无';
      $ProductTitle='<a href="'.GenProductLink($row['id']).'" target="_blank"';
      if($Amount>$row['stock']) $ProductTitle.=' style="text-decoration:line-through" title="库存不足，目前该商品本地库存'.$row['stock'].'件">'.$row['name'].' <img src="images/lack.gif" border=0 width=16 height=16></a>';
      else $ProductTitle.='>'.$row['name'].'</a>';
    echo '<tr bgcolor="#FFFFFF" height="25" align="center">
      <td height="30">'.GenProductCode($row['id']).'</td>
      <td height="30" align="left">&nbsp;'.$ProductTitle.'</td>
      <td height="30">'.$Amount.'</td>
      <td height="30">'.FormatPrice($myprice).'</td>
      <td height="30">'.FormatPrice($GoodsPaid).'</td>
      <td height="30">'.$WareRemark.'</td></tr>';
    }
    if($TotalRecords==0)PageReturn('对不起，您的购物车中还没有商品！',-1);?>
    
    <tr align="right" bgcolor="#FFFFFF">
       <td height="30" colspan="8"> 会员名：<font color="#FF6600"><?php echo $ShopUserName;?></font>&nbsp;(<?php echo $row_user['viptitle'];?>) &nbsp; 商品总计：<font color="#FF6600"><?php echo FormatPrice($TotalPrice);?></font>&nbsp;元&nbsp; 获得积分：<font color="#FF6600"><?php echo $TotalScore;?></font>&nbsp;分 </td>
    </tr></table></td>

        </tr>
      </td>
  <tr><td>    
      <table width="100%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC">
      <form name="receiveaddr" method="post" action="?mode=confirmbill&userid=<?php echo $ShopUserID;?>" onsubmit="return checkform(this);">
      <tr align="center" bgcolor="#FDDFEF">
        <td height="30" colspan="2" bgcolor="#f7f7f7"><strong>请填写订单附加信息</strong></td>
      </tr>
      <tr>
        <td width="20%" align="right" bgcolor="#f7f7f7" nowrap>收 货 人：</td>
        <td width="80%"  bgcolor="#FFFFFF"><input name="receipt" type="text" class="input_sr" value="<?php echo $row_user['receipt'];?>" size="40">
          ＊ 填写收货人真实姓名</td>
      </tr>
      <tr>
        <td align="right" bgcolor="#f7f7f7">收货地址：</td>
        <td bgcolor="#FFFFFF"><input name="address" type="text" class="input_sr" value="<?php echo $row_user['address'];?>" size="40"> ＊ 明细：省市-区-街道-小区-门牌号数-楼层-房间</td>
      </tr>
      <tr>
        <td align="right" bgcolor="#f7f7f7">联系电话：</td>
        <td bgcolor="#FFFFFF"><input name="usertel" type="text" class="input_sr" value="<?php echo $row_user['usertel'];?>" size="40" maxlength="50">
          ＊ 收货人的联系电话，可填多个号码(空格隔开)。</td>
      </tr>
       <tr> 
        <td align="right" bgcolor="#f7f7f7">支付方式：</td>
        <td bgcolor="#FFFFFF"><select name="paymethod" style="width:95%"><?php
          $res=$conn->query('select * from mg_delivery where method=1 order by sortorder',PDO::FETCH_ASSOC);
          foreach($res as $row)echo '<option value="'.$row['subject'].'">'.$row['subject'].'</option>';?></select></td>        
      </tr>        
        <tr> 
          <td align="right" bgcolor="#f7f7f7">配送方式：</td>
          <td bgcolor="#FFFFFF">
          	 <select name="deliverymethod" style="width:95%"><?php
 $res=$conn->query('select * from `mg_delivery` where method=0 order by sortorder',PDO::FETCH_ASSOC);
              foreach($res as $row)echo '<option value="'.$row['subject'].'">'.$row['subject'].'</option>';?></select></td>        
      </tr> 
      
      <tr><td align="right" bgcolor="#f7f7f7">客服选择：</td>
            <td bgcolor="#FFFFFF"><select name="operator"><option value="">...</option><?php
              //8010作为主管，仅后台提交时可以看到。
$res=$conn->query('select idnumber,username from mg_admins where idverified order by idnumber desc',PDO::FETCH_ASSOC);
foreach($res as $row){
  $selectcode=($row['username']==$AdminUsername)?' selected':'';
  echo '<option value="'.$row['idnumber'].'"'.$selectcode.'>'.$row['idnumber'].'</option>';
}?><option value="0">随机</option></select> ＊ &nbsp; &nbsp; &nbsp; 订单类型：<select name="ordertype"><option value="0">客户订单</option><option value="1">内部订单</option></select> ＊ </td>
        </tr>
        
        <tr>
          <td align="right" bgcolor="#f7f7f7">订单备注：</td>
          <td bgcolor="#FFFFFF"><textarea name="userremark" cols="78" rows="8"></textarea></td>
        </tr>
       
    <tr>
      <td colspan="2" align="right" bgcolor="#FFFFFF"><input name="submitbtn" type="submit" class="input_bot"  value="提交订单"></td>
      </tr></form>
  </table>
</td></tr></table> 

</td>
</tr>
</table>
</body>
</html><?php CloseDB();?>
