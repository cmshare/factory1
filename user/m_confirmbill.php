<?php
function GetClientIP() {
  if(($cip=@$_SERVER["HTTP_CLIENT_IP"])) return $cip;
  else if(($cip=@$_SERVER["HTTP_X_FORWARDED_FOR"])) return $cip;
  else if(($cip=@$_SERVER["REMOTE_ADDR"])) return $cip;
  else return NULL;
}

$product_count=$conn->query('select count(*) from `mg_favorites` where userid='.$LoginUserID.' and state>1 and amount>0')->fetchColumn(0);
if(empty($product_count)) PageReturn('对不起，您的购物车中还没有商品！',WEB_ROOT.'usrmgr.htm?action=mycart'); 


$row=$conn->query('select username,deposit,support,lastip from `mg_users` where id='.$LoginUserID.' and grade='.$LoginUserGrade,PDO::FETCH_ASSOC)->fetch();
if($row && $row['lastip']==GetClientIP()){
  $LoginUserName=$row['username'];
  $ShopUserDeposit=$row['deposit'];
  $Order_Support=$row['support'];
  if($Order_Support) $Order_Operator=$conn->query('select username from `mg_admins` where idnumber<='.$Order_Support.' and idnumber2>='.$Order_Support)->fetchColumn(0);
  else{
    $row=$conn->query('select support,operator from `mg_orders` order by actiontime desc limit 1',PDO::FETCH_ASSOC)->fetch();
    if($row){
      $Order_Support=$row['support'];
      $Order_Operator=$row['operator'];
    }
    else $Order_Operator='';
  }
}else LoginReturn('paybill.php');

  
function ProductURL($pid){
  if(WEB_SITE>1)return WEB_ROOT.'product.htm?id='.$pid;
  else return '/products/'.$pid.'.htm';
}

function GenOrderNO(){
  return date('ymdHis').sprintf('%04d',rand(1,9999));
}
 

$Order_Receipt=FilterText(trim($_POST['receipt']));
if(empty($Order_Receipt)) PageReturn('请填写收货人姓名！');

$Order_Address=FilterText(trim($_POST['address']));
if(empty($Order_Address)) PageReturn('请填写详细地址！');

$Order_Usertel=FilterText(trim($_POST['usertel']));
if(empty($Order_Usertel)) PageReturn('请填写电话！');

$Order_Usermail=FilterText(trim($_POST['usermail']));
if(empty($Order_Usermail)) PageReturn('请填写电子邮件！');

$Order_PayMethod=$_POST['paymethod'];
//if(empty($Order_PayMethod)) PageReturn('请填写支付方式！');

$Order_DeliveryID=$_POST['deliveryid'];
if(!is_numeric($Order_DeliveryID)||$Order_DeliveryID<1) PageReturn('请填写送货方式！');



$row=$conn->query('select subject,fee from `mg_delivery` where id='.$Order_DeliveryID,PDO::FETCH_NUM)->fetch();
if($row){
   $Order_DeliveryMethod=$row[0];
   $Order_DeliveryFee=$row[1];
}

$row=$conn->query('select receipt,address,usermail,usertel,support from `mg_users` where id='.$LoginUserID,PDO::FETCH_ASSOC)->fetch();
if($row){
  $sql='';
  if(empty($row['receipt']))$sql.=",receipt='$Order_Receipt'";
  if(empty($row['address']))$sql.=",address='$Order_Address'";
  if(empty($row['usertel']))$sql.=",usertel='$Order_Usertel'";
  if($row['usermail']!=$Order_Usermail)$sql.=",usermail='$Order_Usermail'";
  if($Order_Support && $row['support']!=$Order_Support)$sql.=",support='$Order_Support'";
  if($sql){
    $sql='update `mg_users` set '.substr($sql,1).' where id='.$LoginUserID;
    $conn->exec($sql);
  } 
}

$Order_UserRemark=trim(FilterText($_POST['userremark']));
if(strlen($Order_UserRemark)>255) $Order_UserRemark=substr($Order_UserRemark,0,250).'...';

$conn->exec('lock tables `mg_orders` write'); 
label_gen_ordername:$ordername=GenOrderNO();
$bExist=$conn->query('select id from `mg_orders` where ordername=\''.$ordername.'\'')->fetchColumn();
if($bExist)goto label_gen_ordername;

$sql="`mg_orders` set ordername='$ordername',receipt='$Order_Receipt',adjust=0,weight=0,state=1,exporter=".MAIN_DEPOT.",importer=0,address='$Order_Address',paymethod='$Order_PayMethod',deliverymethod='$Order_DeliveryMethod',deliverycode=null,deliveryfee=$Order_DeliveryFee,usertel='$Order_Usertel',userremark='$Order_UserRemark',adminremark=null,username='$LoginUserName',support=$Order_Support,operator='$Order_Operator',actiontime=unix_timestamp()";
  if(!$conn->exec('update '.$sql.' where state=0 limit 1') && !$conn->exec('insert into '.$sql)) PageReturn('发生未知错误！');
$conn->exec('unlock tables'); ?> 

<table width="100%" height="100%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
<tr>
  <td>
  	
<table  width="100%"  border="0" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC">
<tr bgcolor="#f7f7f7" height="25" align="center">
  <td width="75">编 号</td>
  <td width="600">商 品 名 称</td>
  <td width="75">数 量</td>
  <td width="75">单 价</td>
  <td width="75">合 计</td>
  <td width="100">备 注</td>
</tr><?php
$sql='select mg_product.id,mg_product.name,mg_product.score,mg_product.price0,mg_product.price'.$LoginUserGrade.' as myprice,mg_product.stock0,mg_product.onsale,mg_favorites.remark,mg_favorites.amount from (mg_product inner join mg_favorites on mg_product.id=mg_favorites.productid) where mg_favorites.userid='.$LoginUserID.' and mg_favorites.state>1 and mg_favorites.amount>0 order by mg_product.id';
$res=$conn->query($sql,PDO::FETCH_ASSOC);

$TotalPrice = 0;
$TotalScore = 0;

foreach($res as $row){
  $Amount=$row['amount'];
  $ProScore = $row['score'];
  $myprice = $row['myprice'];
  if(($row['onsale']&0xf)>0 && $LoginUserGrade>2 && $row['onsale']>time() && $row['price0']<$myprice) $myprice=$row['price0'];
  $GoodsPaid=round($myprice*$Amount,2);
  $TotalPrice += $GoodsPaid;
  $TotalScore += $ProScore*$Amount;
  $WareRemarks =$row['remark'];
  $ProductTitle='<a href="'.ProductURL($row['id']).'" target="_blank"';
  #if($Amount>$row['stock0'])
  if(1>$row['stock0']) $ProductTitle.=' style="text-decoration:line-through" title="库存不足，目前该商品库存'.$row['stock0'].'件，请联系客服核实">'.$row['name'].' <img src="images/lack.gif" border=0 width=16 height=16></a>';
  else $ProductTitle.='>'.$row['name'].'</a>';?>
    <tr bgcolor="#FFFFFF" height="30"align="center">
      <td><?php echo substr('0000'.$row['id'],-5);?></td>
      <td align="left">&nbsp;<?php echo $ProductTitle;?></td>
      <td><?php echo $Amount;?></td>
      <td><?php echo round($myprice,2);?></td>
      <td><?php echo round($GoodsPaid,2);?></td>
      <td><?php
      if($WareRemarks)echo '<MARQUEE onmouseover="this.stop()" onmouseout="this.start()" scrollAmount="2" scrollDelay="100" width="100%">'.$WareRemarks.'</MARQUEE>';
      else echo '无';?>
      </td>
    </tr><?php
    $sql='`mg_ordergoods` set ordername=\''.$ordername.'\',productid='.$row['id'].',productname=\''.$row['name'].'\',amount='.$Amount.',price='.$myprice.',score='.$row['score'].',remark=\''.$WareRemarks.'\',audit=1';
    if(!$conn->exec('update '.$sql.' where ordername is null limit 1')) $conn->exec('insert into '.$sql);
}?>
<tr><td colspan="6" align="right" bgcolor="#f7f7f7" >商品总计：<font color="#FF6600"><?php echo round($TotalPrice,2);?></font>&nbsp;元&nbsp; 获得积分：<font color="#FF6600"><?php echo $TotalScore;?></font>&nbsp;分 &nbsp; </td></tr>
</table></td></tr><?php
 $TotalPrice += $Order_DeliveryFee;
 $conn->exec('update mg_orders set totalprice='.$TotalPrice.',totalscore='.$TotalScore.' where ordername=\''.$ordername.'\'');
 $conn->exec('update `mg_favorites` set state=state-2 where userid='.$LoginUserID.' and (state=2 or state=3)');
 
 if(WEB_SITE==1) $paymethod_url=WEB_ROOT.'help/help6.htm'; #www.gdhzp.com
 else if(WEB_SITE==3) $paymethod_url=WEB_ROOT.'help.asp?id=96'; #www.meray.hk
 else $paymethod_url=WEB_ROOT.'help.asp?title=付款方式';?>

<tr><td>
<table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#f2f2f2">
<tr>
  <td width="10%" height="25" bgcolor="#FFFFFF">&nbsp; 订 单 号：</td>
  <td width="40%" height="25" bgcolor="#f7f7f7"><?php echo $ordername;?></td>
  <td width="10%" height="25" bgcolor="#FFFFFF">&nbsp; 收 货 人：</td>
  <td width="40%" height="25" bgcolor="#f7f7f7"><?php echo $Order_Receipt;?></td>
</tr>
<tr>
  <td height="25" bgcolor="#FFFFFF">&nbsp; 联系电话：</td>
  <td height="25" bgcolor="#f7f7f7"><?php echo $Order_Usertel;?></td>
  <td height="25" bgcolor="#FFFFFF">&nbsp; 电子邮件：</td>
  <td height="25" bgcolor="#f7f7f7"><?php echo $Order_Usermail;?>
  </td>
</tr>
<tr>
  <td height="25" bgcolor="#FFFFFF" nowrap>&nbsp; 配送方式：</td>
  <td height="25" bgcolor="#f7f7f7"><?php echo $Order_DeliveryMethod;?><font color="#FF0000">(配送费用<?php echo $Order_DeliveryFee;?>元，多退少补)</font></td>
  <td height="25" bgcolor="#FFFFFF" nowrap>&nbsp; 订单总额：</td>
  <td height="25" bgcolor="#f7f7f7"><font color="#FF0000">￥<?php echo round($TotalPrice,2);?>元</font></td>
</tr>
<tr>
  <td height="25" bgcolor="#FFFFFF">&nbsp; 详细地址：</td>
  <td height="25" colspan="3" bgcolor="#f7f7f7"><?php echo $Order_Address;?></td>
</tr>
<tr>
  <td height="25" bgcolor="#FFFFFF">&nbsp; 订单备注：</td>
  <td height="25" colspan="3" bgcolor="#f7f7f7"><?php echo $Order_UserRemark;?></td>
</tr>
</table></td></tr>

<tr><td>   		
<table width="100%"  border="0" cellpadding="4" cellspacing="1" bgcolor="#f2f2f2">   	     
<tr height="40" bgcolor="#f7f7f7">
  <td nowrap><font color="#ff6600" size=3><b><?php
if($ShopUserDeposit>$TotalPrice)echo '&nbsp;您的订单已成功提交，我们会在一个工作日内确认您的订单！请耐心等待！！！';
else echo '&nbsp;您的订单流程已经完成！请您在72小时内按您选择的支付方式进行汇款，并及时联系我们的客服!<br>如果您没有申请在线支付，您还可以在以下银行柜台办理汇款，请注意随身携带物品的安全！';?></b></font></td>
  <td align="right"><a href="<?php echo $paymethod_url;?>"><b><font color="#0000EE"><u> 查看付款方式...</u></font></b></a> &nbsp; </td></tr>
<tr>
  <td colspan="2" align="center" bgcolor="#FFFFFF" height="50">
     <a href="<?php echo WEB_ROOT;?>usrmgr.htm?action=myorders" style="color:#0000FF;font-size:16px">...[返回]...</a>  
  </td>
</tr>
</table></td></tr>
</table> 
