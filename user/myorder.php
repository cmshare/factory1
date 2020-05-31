<?php require('../include/conn.php');
CheckLogin();

$ordername=FilterText(@$_GET['ordername']); 
if(empty($ordername))PageReturn('参数无效！');

db_open();

$mode=@$_GET['mode'];
if($mode){
  switch($mode){
    case 'amount':change_amount();break;
    case 'remark':change_remark();break; 
    case 'delete': delete_myorder();break;
    case 'complete':complete_order();break;
    case 'addnew':addto_order();break;
    case 'deliverinfo':save_deliverinfo();break;
  }
}
function ProductURL($pid){
  return (WEB_SITE>1)?WEB_ROOT.'product.htm?id='.$pid:'/products/'.$pid.'.htm';
}

function IsOrderGoodsAllowEdit($order_goods_ID){
  global $conn,$LoginUserID;
  $orderUserID=$conn->query('select `mg_users`.id from `mg_users`,`mg_ordergoods`,`mg_orders` where `mg_ordergoods`.id='.$order_goods_ID.' and `mg_ordergoods`.ordername=`mg_orders`.ordername and `mg_orders`.username=`mg_users`.username')->fetchColumn(0);
  return ($orderUserID==$LoginUserID);
}

function ReturnWarnning($warninfo){
  $return_url=$_SERVER['HTTP_REFERER'];
  echo '<body><table border="0" width="100%" height="100%" cellspacing="0" cellpadding="0"><tr><td width="100%" style="font-size: 9pt; line-height: 12pt"><p align="center"><font color="#FF0000">'.$warninfo.'</font></p><p align="center"><font color=#FF0000><b id="timerlabel">3</b></font> 秒钟后自动<a href="'.$return_url.'">返回</a> ...  </td></tr></table><script>var invercount=3;function inversecounter(){if (--invercount>=0)document.getElementById("timerlabel").innerHTML=invercount;else self.location.href="'.$return_url.'";} setInterval("inversecounter()",1000);</script></body></html>';
  db_close();
  exit(0);
}

function change_amount(){
  $newValue=$_POST['newvalue'];
  $selectid=$_GET['selectid'];
  if(is_numeric($newValue) && is_numeric($selectid)){
    if(IsOrderGoodsAllowEdit($selectid)){
      if($GLOBALS['conn']->exec('update `mg_ordergoods` set amount ='.$newValue.' where id='.$selectid)){
        setcookie('NewAddProduct',$selectid);
        ReturnWarnning('商品数量修改成功！');
      }
    }
    else
    { ReturnWarnning('修改失败，请联系客服人员！');
    }
  }
  ReturnWarnning('参数无效!');
}

function change_remark(){ 
  $selectid=$_GET['selectid'];
  if(is_numeric($selectid) && IsOrderGoodsAllowEdit($selectid)){
     $remark=trim(FilterText($_POST['newvalue']));
     if(strlen($remark)>255) $remark=substr($remark,0,250).'...';
     if($GLOBALS['conn']->exec("update `mg_ordergoods` set remark='$remark' where id=$selectid")){
       setcookie('NewAddProduct',$selectid);
       ReturnWarnning('商品备注修改成功！');
     } 
  }
  ReturnWarnning('参数无效!');
}
  
function delete_myorder(){
  global $conn,$ordername,$LoginUserID;
  if($conn->exec("update (`mg_orders` inner join `mg_users` on `mg_orders`.username=`mg_users`.username) set `mg_orders`.state=0 where `mg_orders`.ordername='$ordername' and `mg_orders`.state=1 and `mg_users`.id=$LoginUserID")){
    $conn->exec("update `mg_ordergoods` set ordername=null where ordername='$ordername'");
    echo '<script>alert("订单删除成功！");self.location.href="'.WEB_ROOT.'usrmgr.htm?action=myorders";</Script>';
    db_close();
    exit(0);
  }
  else ReturnWarnning('订单删除失败！');
}

function complete_order(){
  global $conn,$ordername,$LoginUserID;
  if($conn->exec("update (`mg_orders` inner join `mg_users` on `mg_orders`.username=`mg_users`.username) set `mg_orders`.state=6 where `mg_orders`.state=5 and `mg_orders`.ordername='$ordername' and `mg_users`.id=$LoginUserID")) ReturnWarnning('订单完成！');
  else ReturnWarnning('参数错误！');
}

function addto_order(){
  global $conn,$ordername,$LoginUserID;
  $product_ID=$_GET['productid'];
  $product_amount=$_GET['amount'];
  if(is_numeric($product_ID) && is_numeric($product_amount)){
     $product_remark=trim(FilterText($_POST['newvalue']));
     if(strlen($product_remark)>255) $product_remark=substr($product_remark,0,250).'...';
  
     $ShopUserGrade=$conn->query("select `mg_users`.grade from (`mg_users` inner join `mg_orders` on `mg_users`.username=`mg_orders`.username) where `mg_users`.id=$LoginUserID and `mg_orders`.ordername='$ordername' and `mg_orders`.state=1")->fetchColumn(0);
  }
  if(empty($ShopUserGrade)) PageReturn('该订单不存在或者不允许修改！');  
  $row=$conn->query('select id,name,score,price0,price'.$ShopUserGrade.' as myprice,onsale from `mg_product` where id='.$product_ID.' and recommend>0',PDO::FETCH_ASSOC)->fetch();
  if($row){
    $product_name=$row['name'];
    $product_score=$row['score'];
    $product_price=$row['myprice'];
    if(($row['onsale']&0xf)>0 && $ShopUserGrade>2 && $row['onsale']>time() && $row['price0']<$product_price) $product_price=$row['price0'];
  }
  else PageReturn('该商品编号不存在！'); 


  $sql="`mg_ordergoods` set productname='$product_name',price=$product_price,score=$product_score,amount=$product_amount,remark='$product_remark',audit=1";
  $editid=$conn->query("select id from `mg_ordergoods` where ordername='$ordername' and productid=$product_ID")->fetchColumn(0);
  if($editid) $conn->exec("update $sql where id=$editid");
  else{
     $conn->exec("insert into $sql ,ordername='$ordername',productid=$product_ID");
  }
  setcookie('NewAddProduct',$editid);
  ReturnWarnning('订单商品添加成功！');
}

function save_deliverinfo(){
  global $conn,$ordername,$LoginUserID;
  $order_receipt=trim(FilterText($_POST['receipt']));
  $order_address=trim(FilterText($_POST['address']));
  $order_usertel=trim(FilterText($_POST['usertel']));
  $order_userremark=trim(FilterText($_POST['userremark']));
  if(strlen($order_userremark)>255) $order_userremark=substr($order_userremark,0,250).'...';
  if($conn->exec("update (`mg_orders` inner join `mg_users` on `mg_orders`.username=`mg_users`.username) set `mg_orders`.receipt='$order_receipt',`mg_orders`.address='$order_address',`mg_orders`.usertel='$order_usertel',`mg_orders`.userremark='$order_userremark' where `mg_orders`.ordername='$ordername' and `mg_users`.id=$LoginUserID and `mg_orders`.state=1")) ReturnWarnning('订单配送信息修改成功！');
  else ReturnWarnning('订单信息没有修改！');		  
}

$row=$conn->query("select `mg_orders`.*,`mg_users`.deposit,`mg_users`.score,`mg_usrgrade`.title from `mg_orders`,`mg_users`,`mg_usrgrade` where `mg_orders`.ordername='$ordername' and `mg_orders`.username=`mg_users`.username and `mg_users`.grade=`mg_usrgrade`.id and `mg_users`.id=$LoginUserID and `mg_orders`.state>0",PDO::FETCH_ASSOC)->fetch();
if($row){
  $username=$row['username'];
  $deposit=$row['deposit'];
  $score=$row['score'];
  $OriginOrderTotalPrice=$row['totalprice'];
  $OriginOrderTotalScore=$row['totalscore'];
  $DeliveryFee=$row['deliveryfee'];
  $Order_State=$row['state'];
  $Order_Receipt=$row['receipt'];
  $Order_Address=$row['address'];
  $Order_UserTel=$row['usertel'];
  $Order_DeliveryMethod=$row['deliverymethod'];
  $Order_Adjust=$row['adjust'];
  $Order_ActionTime=$row['actiontime'];
  $Order_DeliveryCode=$row['deliverycode'];
  $Order_IDNumber=$row['support'];
  $Order_UserRemark=$row['userremark'];
  $Order_AdminRemark=$row['adminremark'];
  $UserGradeTitle=$row['title'];
}
else{
  db_close();
  echo '<p align=center>订单不存在！</p>';
  exit(0);
}

$res=$conn->query("select `mg_ordergoods`.id,`mg_ordergoods`.productid,`mg_ordergoods`.price,`mg_ordergoods`.amount,`mg_ordergoods`.remark,`mg_ordergoods`.productname,`mg_ordergoods`.score,`mg_product`.stock0 from ((`mg_ordergoods` inner join `mg_product` on `mg_ordergoods`.productid=`mg_product`.id) left join `mg_category` on `mg_product`.brand=`mg_category`.id) where `mg_ordergoods`.ordername='$ordername' order by `mg_category`.sortindex,`mg_ordergoods`.productname",PDO::FETCH_ASSOC);
?><html>
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<META http-equiv="Keywords" content="订单、化妆品、化妆品批发,南京化妆品批发">
<META http-equiv="Description" content="订单明细-<?php echo WEB_NAME;?>">
<SCRIPT language="JavaScript" src="cmbase.js" type="text/javascript"></SCRIPT><SCRIPT language="JavaScript" src="myorder.js" type="text/javascript"></SCRIPT>
<title>订单详细资料 - <?php echo WEB_NAME?></title>
<style type="text/css">
<!--
A{TEXT-DECORATION: none;}
A:link    {COLOR: #000000; TEXT-DECORATION: none}
A:visited {COLOR: #000000; TEXT-DECORATION: none}
A:hover   {COLOR: #FF0000; TEXT-DECORATION: underline}
TD   {FONT-FAMILY:宋体;FONT-SIZE: 9pt;line-height: 150%;}
TR.pprow TD{BACKGROUND-IMAGE:url(<?php echo WEB_ROOT;?>images/topbg.gif); }
.ProName{text-align:left;padding-left:5px;}
-->
</style>
</head>
<body topmargin="0" leftmargin="0" onload="UpdatePagePosition(1)" onunload="UpdatePagePosition(0)">          
<table width="99%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
<td background="<?php echo WEB_ROOT;?>images/topbg.gif">
    <TABLE width="100%%" border="0" cellpadding="0" cellspacing="0">
  <TR>
    <TD width="55%" nowrap><b><img src="<?php echo WEB_ROOT;?>images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="<?php echo WEB_ROOT;?>"><?php echo WEB_NAME;?></a> -&gt; <a href="<?php echo WEB_ROOT;?>usrmgr.htm">会员中心</a> -&gt; <a href="<?php echo WEB_ROOT;?>usrmgr.htm?action=myorders">我的订单</a>  -&gt; <font color=#FF0000>查看订单</font></b></td>
    <TD width="45%" nowrap align="right" id="QQService"></td>
  </TR>
  </TABLE>	
</td>
</tr>
<tr> 
<td height="200" valign="top" bgcolor="#FFFFFF">
    <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
    <tr>
      <td background="<?php echo WEB_ROOT;?>images/topbg.gif" nowrap>
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
                  <td width="70%" nowrap><img src="<?php echo WEB_ROOT;?>images/pic17.gif" width="17" height="15" align="absmiddle" />订单号：<b><font color="#FF0000"><?php echo $ordername;?></font></b> ，<img src="<?php echo WEB_ROOT;?>images/pic18.gif" width="17" height="15" align="absmiddle" />下单用户：<b><?php echo $username;?></b>（<?php echo $UserGradeTitle;?>,预存款余额<font color="#FF0000"><?php echo round($deposit,2);?></font>元，可用积分<font color="#FF6600"><?php echo $score;?></font>分）</td>
                  <td width="30%" nowrap align="right"></td>
                </tr>
                </table>
      </td>
    </tr>
  </table>
  
    <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <tr>
     <td background="<?php echo WEB_ROOT;?>images/topbg.gif" nowrap>
               <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
         <tr><td nowrap>
           <img src="<?php echo WEB_ROOT;?>images/pic21.gif" width="17" height="15" align="absmiddle" />订单状态：
      
           <input type="checkbox" <?php if($Order_State>1) echo  'checked';?> DISABLED >订单确认-&gt;<input type="checkbox" <?php if($Order_State>2) echo 'checked';?> DISABLED >配货打包-&gt;<input type="checkbox" <?php if($Order_State>3) echo 'checked';?> DISABLED >仓库出货-&gt;<input type="checkbox" <?php if($Order_State>4) echo 'checked';?> DISABLED >财务结算-&gt;<?php
            if($Order_State==5) echo '<input type="button" value="确认收货" onclick="CompleteOrder()">';
            else if($Order_State<5) echo '<input type="checkbox" DISABLED>客户签收';
            else echo '<input type="checkbox" CHECKED DISABLED>客户签收';?>  
         </td>
        <td align="right" nowrap><?php	
        if($Order_State<4){ 
          $disable_option=($Order_State>1)?' disabled ':'';
          echo '<input name="AddWare" type="button" value="新增商品到订单.." '.$disable_option.' onclick="AddNewProductToOrder();">';
        }?>
        <input name="Submit4" type="button" value="下载订单" onclick="window.open('downorder.php?ordername=<?php echo $ordername;?>')">
         </td>
         
        </tr></table>
     </td>
  </tr>
  </table>

  <table id="MyTableID" width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <tr height="25" align="center" bgcolor="#F7F7F7" class="pprow"> 
      <td WIDTH="10%"><strong><strong>编号</strong></strong></td>
      <td WIDTH="60%"><strong><strong>名称</strong></strong></td>
      <td WIDTH="10%"><strong>数量</strong></td>
      <td WIDTH="10%"><strong>单价</strong></td>
      <td WIDTH="10%"><strong>备 注</strong></td>
  </tr><?php     
$TotalPrice=0;  #价格总计
$TotalScore=0;
$TotalProduct=0;#商品总件数
$TotalCount=0;  #商品条目数


foreach($res as $row){
  $Amount = $row['amount'];
  $Price =  $row['price'];
  $TotalCount++;
  $TotalProduct=$TotalProduct+$Amount;
  if($Amount>0){
    $TotalScore += $Amount*$row['score'];
    $TotalPrice += $Amount*$Price; 
  }

  $Remarks =$row['remark'];					            
  if($Remarks) $Remarks='<MARQUEE onmouseover="this.stop()" onmouseout="this.start()"  style="cursor:pointer" width=100% scrollAmount=2 scrollDelay=100>'.$Remarks.'</MARQUEE>';
  else $Remarks='&nbsp;';?>
<tr align="center" stock="<?php echo $row['stock0'];?>" goodsID="<?php echo $row['id'];?>"  height="20" bgcolor="#FFFFFF" onMouseOut="this.bgColor='#FFFFFF';" onMouseOver="this.bgColor='#FFFF00';"> 
<TD><?php echo $row['productid'];?></td>
<TD class="ProName"><a href="<?php echo ProductURL($row['productid']);?>" target="_blank"><?php echo $row['productname'];?></a></TD> 
<TD><?php echo $Amount;?></td>
<TD><?php echo round($Price,2);?></td>		
<TD><?php echo $Remarks;?></td>
</TR><?php
}?>
  
   <tr height="25" align="center"  class="pprow"> 
      <td colspan="2"><b>合计</b></td>
      <td><font color="#FF0000"><?php echo $TotalProduct;?></font>/<?php echo $TotalCount;?></td>
      <td><font color="#FF0000"><?php echo round($TotalPrice,2);?></font></td>
      <td>&nbsp;</td>
  </tr>
  </table>
  
  <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <tr class="pprow">
    <td align="center"><?php
      $Order_Adjust_signed=round($Order_Adjust,2);
      if($Order_Adjust>0) $Order_Adjust_signed='+'.$Order_Adjust_signed;
      echo '配送费用<font color=#FF0000>'.round($DeliveryFee,2).'</font>元';
      if($Order_Adjust) echo ' &nbsp;  折扣调整<font color=#FF0000>'.$Order_Adjust_signed.'</font>元';
      
      $TotalPrice=round($TotalPrice+$DeliveryFee+$Order_Adjust,2);
      if($TotalPrice!=$OriginOrderTotalPrice || $OriginOrderTotalScore!=$TotalScore){ 
        if($Order_State<5){ #没有收款时可以调整
          $conn->exec("update `mg_orders` set totalprice=$TotalPrice,totalscore=$TotalScore where ordername='$ordername' and username='$username'");
        }     
        else{
          $TotalPrice=$OriginOrderTotalPrice;
          $TotalScore=$OriginOrderTotalScore;
        }
      }?>
      &nbsp; =&gt; &nbsp;  订单总额：￥<B><FONT color="#FF0000"><?php echo round($TotalPrice,2);?></font></B>元
      &nbsp; &nbsp; | &nbsp; &nbsp;  获得积分：<font color="#FF0000"><?php echo $TotalScore;?></font>分
    </td>
 </tr><?php
 if($Order_State==1){?>
  <tr height="20" class="pprow">
    <td align="center">
      <span style="color:#FF6600;font-size:9pt;text-align:center;cursor:pointer" onclick="ShowHelp()">注： 您的订单尚未被确认，您可以继续修改该订单内的商品,<font color=#FF0000>点击这里</font>查看具体修改方法。</span>
    </td>
  </tr><?php
 }?>
 </table>
  

</td>

</tr>
</table>
<br>
<table width="99%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
<td background="<?php echo WEB_ROOT;?>images/topbg.gif">
    <b><img src="<?php echo WEB_ROOT;?>images/pic5.gif" width="28" height="22" align="absmiddle">订单附加信息</b>
</td>
</tr>  
<tr>
   <td>
     <form method="post" name="deliverinfo" style="margin:0px" action="?mode=deliverinfo&ordername=<?php echo $ordername;?>">
            <table width="99%" height="100%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <tr height="20" bgcolor="#F7F7F7">
      <td WIDTH="100" height="25" align="right" background="<?php echo WEB_ROOT;?>images/topbg.gif"><strong>收 货 人：</strong></td>
      <td> &nbsp; <input type="text" name="receipt" maxlength="16" value="<?php echo $Order_Receipt;?>" style="width:95%" <?php if($Order_State>1) echo 'disabled';?>></td>
      <td width="40%" rowspan="4" valign="top">
                    <table width="100%" height="100%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
              <tr>
                    <td align="center" height="20" background="<?php echo WEB_ROOT;?>images/topbg.gif"><strong>订单附言</strong>（256字以内）</td>
              </tr>
              <tr>
                    <td width="100%" height="100" bgcolor="#F7F7F7"  valign="top"> <textarea name="userremark" maxlength="255" wrap="VIRTUAL" style="WORD-BREAK: break-all;width:100%;height:100%;font-size: 9pt; border: 1 solid #808080"  <?php if($Order_State>1)echo 'disabled';?>><?php echo $Order_UserRemark;?></textarea>
                    </td>
              </tr>
            </table>		
      </td>
  </tr>
  <tr height="25" bgcolor="#F7F7F7"> 
      <td align="right"><strong>收货地址：</strong></td>
      <td> &nbsp; <input type="text" name="address" maxlength="100" value="<?php echo $Order_Address;?>" style="width:95%" <?php if($Order_State>1) echo 'disabled';?>></td>
      
  </tr>
  <tr height="25" bgcolor="#F7F7F7"> 
      <td align="right"><strong>联系电话：</strong></td>
      <td> &nbsp; <input type="text" name="usertel"  maxlength="50" value="<?php echo $Order_UserTel;?>" style="width:95%" <?php if($Order_State>1) echo 'disabled';?>></td>
  </tr>
  <tr height="25" bgcolor="#F7F7F7"> 
      <td align="right"><strong>配送方式：</strong></td>
      <td> &nbsp; <?php
         echo $Order_DeliveryMethod;
         if(strlen($Order_DeliveryCode)>4){
           echo '&nbsp;'.$Order_DeliveryCode.' &nbsp; &nbsp; <input type="button" value="追踪快件" style="height:20px" onclick="DeliveryTrack(\''.$Order_DeliveryMethod.'\',\''.$Order_DeliveryCode.'\')">';
         }?>
      </td>
  </tr> 

  <?php if($Order_AdminRemark) echo '<tr height="25" bgcolor="#F7F7F7"><td align="right"><strong>订单备注：</strong></td><td colspan=2 style="padding-left:12px">'.$Order_AdminRemark.'</td></tr>'; ?>

  <tr height="25" bgcolor="#F7F7F7"> 
      <td align="right"><strong><?php echo ($Order_State<4)?'下单':'发货';?>时间：</strong></td>
      <td colspan=2>
            
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td> &nbsp; <?php echo date('Y-m-d H:i:s',$Order_ActionTime);?></td>
            <td align="right" style="padding-right:4px">
              <?php if($Order_State==2) echo '<font color=#FF6600>订单已经确认,如需修改请联系客服撤回确认状态！</font>&nbsp; ';?>
              <input type="button" value="保存附加信息" onclick="if(CheckOrderInfo(this.form)){this.disabled=true;this.form.submit()}" <?php if($Order_State>1) echo 'disabled';?>><input type="button" value="删除订单" onclick="DeleteOrder()"></td>
        </tr></table>
      </td>

  </tr>    
  </table>
  </form>    
</td>
</tr>
</table>
<table width=99% border=5 align=center cellpadding=5 cellspacing=5 bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr class="pprow">
<td>
<b><img src="<?php echo WEB_ROOT;?>images/pic5.gif" width=28 height=22 align="absmiddle">异地发货声明</b>
</td>
</tr>
<tr>
<td>
     (1) 江浙沪地区一般2天内到货，其它地区3天左右到货，偏远的地区或乡镇可能会有延迟。<br>
 (2) 我们不负责快件的查询和催送问题。请您自己根据货单号码，通过该<a href="deliverytrack.asp" style="color:#FF0000" target="_blank">快递公司的官方网站</a>查询快件的行踪，或者根据快递公司网站上的客服电话进行查询。若派送延误，请尽量自行与快递公司协调解决，谢谢配合。<br>
 (3) 请收件人在快递员或物流处领取包裹时，一定注意在当场验收包裹内的物品数量、配件是否齐全；商品外表面是否有明显的因摔、撞、挤、压引起的损伤。请在确认无误后再签字签收，否则若在签收之后再提出异议，本公司概不负责。如在验收当场发现商品存在以上问题，请直接电话联系本公司或快递公司开出此类证明。若随意签收给您带来损失，本公司一律不负责！
</td>
</tr>
</table>  
<form name="MyTestForm" id="MyTestForm" method=post><input type="hidden" name="newvalue"></form>
<script language=javascript> InitMyOrder("<?php echo $ordername;?>",<?php echo $Order_State;?>); </script>	
</body>
</html>
<?php db_close();?>

