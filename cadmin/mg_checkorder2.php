<?php require('includes/dbconn.php');
CheckLogin();
db_open();

$mode=@$_GET['mode'];
if($mode){
  include('checkorder.php');
  HandleAction($mode);
  db_close();
  exit(0);
}

$OrderName=FilterText(@$_GET['ordername']);
if(empty($OrderName))PageReturn('参数无效！',-1); 
	 
$row=$conn->query('select mg_orders.* from mg_orders where ordername=\''.$OrderName.'\' and state<>0',PDO::FETCH_ASSOC)->fetch();
if($row){
  $Order_Username=$row['username'];
  $OriginOrderTotalPrice=$row['totalprice'];
  $OriginOrderTotalScore=$row['totalscore'];
  $DeliveryFee=$row['deliveryfee'];
  $Order_Weight_KG=round($row['weight']/1000,3);
  $Order_State=$row['state'];
  $Order_Exporter=$row['exporter'];
  $Order_Importer=$row['importer'];
  $Order_UserTel=$row['usertel'];
  $Order_DeliveryMethod=$row['deliverymethod'];
  $Order_PayMethod=$row['paymethod'];
  $Order_Adjust=$row['adjust'];
  $Order_ActionTime=date('Y-m-d H:i',$row['actiontime']);
  $Order_DeliveryCode=$row['deliverycode'];
  $Order_Receipt=$row['receipt'];
  $Order_Address=$row['address'];
  $Order_UserRemark=$row['userremark'];
  $Order_AdminRemark=$row['adminremark'];
  $Order_IDNumber=$row['support'];
  $Order_Operator=$row['operator'];
}
else{
  PageReturn('<br><br><p align=center>该订单不存在或无效！</p>',0);
}

$DepotArray=array('其它单位');
$res=$conn->query('select id,depotname from mg_depot where enabled',PDO::FETCH_NUM);
foreach($res as $row)$DepotArray[$row[0]]=$row[1];
 
$own_popedomFinance=CheckPopedom('FINANCE');

if($Order_State>0){
  $IsOrderManager=($Order_Operator==$AdminUsername || $own_popedomFinance || $Order_Exporter==GetAdminDepot());
  if($Order_State<3 and $IsOrderManager) $BaseInputStyle='style="width:98%"';	
  else $BaseInputStyle='style="width:98%;border:0px;background-color:transparent" readOnly';	
  $CostOrScore='score';

  $row=$conn->query('select mg_users.grade,mg_users.deposit,mg_users.score,mg_usrgrade.title from mg_users inner join mg_usrgrade on mg_users.grade=mg_usrgrade.id where mg_users.username=\''.$Order_Username.'\'',PDO::FETCH_ASSOC)->fetch(); 
  if($row){
      $UserGradeTitle=$row['title'];
      $UserDeposit=$row['deposit'];
      $UserScore=$row['score'];
      $Order_UserPrice='price'.$row['grade'];
  }
  else PageReturn('订单用户不存在！');
  $UserFund=$conn->query('select sum(amount) from mg_accountlog where username=\''.$Order_Username.'\' and (operation=5 or operation=6)')->fetchColumn(0);
  if(!is_numeric($UserFund))$UserFund=0;
  $sql_ext_field=',mg_product.'.$Order_UserPrice;
}
else{
  session_start();
  $own_popedomManage=CheckPopedom('MANAGE');
  $CostOrScore=(@$_SESSION['showcost'] && $own_popedomFinance && $Order_State>-3)?'cost':'score';
  $sql_ext_field=($CostOrScore=='cost')?',mg_product.cost':'';
  $IsOrderManager=($Order_Username==$AdminUsername || $Order_Operator=$AdminUsername || $own_popedomManage); 
}

$res=$conn->query('select mg_ordergoods.id,mg_ordergoods.productid,mg_ordergoods.price,mg_ordergoods.amount,mg_ordergoods.remark,mg_ordergoods.audit,mg_ordergoods.productname,mg_ordergoods.score,mg_product.stock'.$Order_Exporter.' as stock'.$sql_ext_field.' from (mg_ordergoods inner join mg_product on mg_ordergoods.productid=mg_product.id) where mg_ordergoods.ordername=\''.$OrderName.'\' order by mg_product.brand,mg_ordergoods.productname',PDO::FETCH_ASSOC);
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript" src="checkorder.js" type="text/javascript"></SCRIPT>
<style type="text/css">
.ProName{text-align:left;padding-left:5px;}
</style>
</head>
<body topmargin="0" leftmargin="0" onload="UpdatePagePosition(1)" onunload="UpdatePagePosition(0)"> 
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif">
    	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr> 
    	<td nowrap>
    	 <b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <?php echo ($Order_State>0)?'<a href="mg_orders.php">客户订单管理</a>':'<a href="mg_privateorders.php">内部订单管理</a>';?> -> <font color=#FF0000>订单明细</font></b>
      </td>
      <td align="right"><?php
        if($IsOrderManager && ($Order_State==-1 || $Order_State==1 ||$Order_State==2)) echo '<input type="button" value="添加商品" onclick="AddNewProductToOrder()"> &nbsp;';  
        echo '<input type="button" value="复制商品" onclick="CopyProducts()"> &nbsp; ';
        if($IsOrderManager && ($Order_State==-1 || $Order_State==1) )echo '<input type="button" value="转移商品" onclick="MigrateProducts()"> &nbsp;<input type="button" value="移除商品" onclick="RemoveProducts()"> &nbsp; <input type="button" value="删除订单" onclick="DeleteMyOrder()"> &nbsp;';?>
        <input type="button" value="下载清单" onclick="window.open('mg_downorder.php?ordername=<?php echo $OrderName;?>&handle='+Math.random())"></td>
    </tr></table>
    </td>
  </tr>
  <tr> 
    <td height="200" valign="top" bgcolor="#FFFFFF"><?php

 if($Order_State>0){ ?>   
    	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr>
          <td background="images/topbg.gif" nowrap>
             <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    	     <tr>
    	        <td width="70%" nowrap><img src="images/pic17.gif" width="17" height="15" align="absmiddle" />订单号 <a href="?ordername=<?php echo $OrderName;?>" title="点击刷新订单"><b><font color="#FF0000"><?php echo $OrderName;?></font></b></a> &nbsp;<img src="images/pic18.gif" width="17" height="15" align="absmiddle" /><?php echo $UserGradeTitle;?> <a href="mg_usrinfo.php?user=<?php echo rawurlencode($Order_Username);?>" style="color:005588;font-weight:bold"><?php echo $Order_Username;?></a>，<a href="mg_accountlog.php?username=<?php echo $Order_Username;?>&mode=6">预存款<font color="#FF0000"><?php echo FormatPrice($UserDeposit);?></font>元<?php if($UserFund) echo '，<b>待审款<font color=#00AA00>'.FormatPrice($UserFund).'</font>元</b>';?></a>，积分<font color="#FF6600"><?php echo $UserScore;?></font>分</td></td><td nowrap id="OrderStatePanel" align="right"></td><td width="1%" nowrap style="padding-left:20px"> <img src="images/pic19.gif" width="18" height="15" align="absmiddle" />客服:<font color="#FF6600"><?php echo $Order_Operator.'#'.$Order_IDNumber;?></font> </td>

    	     </tr>
    	     </table>
        </tr>
        </table><?php
}
else{?>
        <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr>
           <td background="images/topbg.gif" nowrap>
             <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    	     <tr>
    	        <td width="70%" nowrap><img src="images/pic17.gif" width="17" height="15" align="absmiddle" /><b>订单号</b> <a href="?ordername=<?php echo $OrderName;?>" title="点击刷新订单"><font color="#FF0000"><?php echo $OrderName;?></font></a> &nbsp; <img src="images/pic18.gif" width="17" height="15" align="absmiddle" /><b>下单用户</b> <a href="mg_usrinfo.php?user=<?php echo rawurlencode($Order_Username);?>"><?php echo $Order_Username;?></a></td>
                <td width="30%" nowrap align="right"><span id="OrderStatePanel"></span></td>
    	     </tr>
    	     </table>
           </td>
         </tr>
         </table><?php
}?>    
      
      <table id="MyTableID" width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="20" bgcolor="#F7F7F7"> 
          <td WIDTH="4%" height="25" align="center" background="images/topbg.gif"><input type="checkbox" onclick="Checkbox_SelectAll('selectid',this.checked)"></td>
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong><strong>编号</strong></strong></td>
          <td WIDTH="56%" height="25" align="center" background="images/topbg.gif"><strong><strong>名称</strong></strong></td>
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>数量</strong></td>
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>单价</strong></td>
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>单件<?php echo ($CostOrScore=='cost')?'成本':'积分';?></strong></td>
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>备 注</strong></td>
      </tr><?php

$TotalPrice=0;  //价格总计
$TotalScore=0;
$TotalCost=0;
$TotalRecord=0;//rs.recordcount  '商品总项目
$TotalProduct=0;   //商品总件数

foreach($res as $row){
  $TotalRecord++;
  $Amount=$row['amount'];
  $Price=$row['price'];
  $Score=$row['score'];
  if($Amount>0){
    $TotalProduct+=$Amount;
    $TotalScore+=$Amount*$Score;
    $TotalPrice+=$Amount*$Price;
    if($CostOrScore=='cost')$TotalCost+=$Amount*$row['cost'];
  }
  $Remark=$row['remark'];					            
  $ProductID=$row['productid'];
  if($Remark) $Remark='<MARQUEE onmouseover="this.stop()" onmouseout="this.start()" style="cursor:pointer" width=100% scrollAmount=2 scrollDelay=100>'.$Remark.'</MARQUEE>';
  else $Remark='&nbsp;';
  echo '<tr align="center"'.(($Order_State>0 && $Order_State<5)?' audit="'.$row['audit'].'" price="'.$row[$Order_UserPrice].'" ':'').' stock="'.$row['stock'].'" bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
  <td><input name="selectid" type="checkbox" value="'.$row['id'].'" onclick="mChk(this)"></td>
  <td><a href="mg_stocklog.php?id='.$ProductID.'">'.GenProductCode($ProductID).'</a></td>
  <td class="ProName"><a href="'.GenProductLink($ProductID).'" target="_blank">'.$row['productname'].'</a></td> 
  <td>'.$Amount.'</td>
  <td>'.FormatPrice($Price).'</td>		
  <td>'.round($row[$CostOrScore],2).'</td>
  <td>'.$Remark.'</td>
  </TR>';
}

if($TotalRecord==0) echo '<tr><td colspan=7 height=50 align=center bgcolor="#FFFFFF"><font color=#FF0000>此订单中还没有商品！</font></td></tr>';
?> 
  <tr height="25" align="center" id="OrderStatRow"> 
    <td colspan="3" background="images/topbg.gif"><b>合计</b></td>
    <td background="images/topbg.gif"><font color="#FF0000"><?php echo $TotalProduct;?></font>/<?php echo $TotalRecord;?></td>
    <td background="images/topbg.gif"><b><?php echo FormatPrice($TotalPrice);?></b></td>
    <td background="images/topbg.gif"><?php echo ($CostOrScore=='cost')?$TotalCost:$TotalScore;?></td>
    <td background="images/topbg.gif">&nbsp;</td>
  </tr>
  </table>
<?php if($Order_State>0){?>     
  <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <tr><td align="center" background="images/topbg.gif"> 从<font color="#FF0000"><?php echo $DepotArray[$Order_Exporter];?></font>出货&nbsp; &nbsp;<input id="checkweightbtn" type="button" onclick="window.open('mg_weighorder.php?ordername=<?php echo $OrderName;?>')" style="border:0px;cursor:pointer;TEXT-DECORATION: underline;BACKGROUND-COLOR:transparent;color:#0000FF;" value="...订单称重...">&nbsp; &nbsp;<?php 
  $Order_Adjust_signed=FormatPrice($Order_Adjust);
  if($Order_Adjust>0) $Order_Adjust_signed='+'.$Order_Adjust_signed;
  if(($IsOrderManager && $Order_State<3) || ($Order_State<5 && $own_popedomFinance)) echo '配送费<span style="cursor:pointer;color:#FF0000" title="点击修改" onclick="ChangeDeliveryFee(this)"><u>+'.FormatPrice($DeliveryFee).'</u></span>元&nbsp;&nbsp; 折扣调整<span style="cursor:pointer;color:#FF0000" title="点击修改" onclick="ChangeAdjustFee(this)"><u>'.$Order_Adjust_signed.'</u></span>元&nbsp;&nbsp;';
  else echo '配送费<font color="#FF0000">'.FormatPrice($DeliveryFee).'</font>元&nbsp; &nbsp; 折扣调整<font color="#FF0000">'.$Order_Adjust_signed.'</font>元&nbsp; &nbsp;';
  $TotalPrice=FormatPrice($TotalPrice+$DeliveryFee+$Order_Adjust);?><span style='font-weight:bold;font-size:16pt'>→</span> &nbsp;  订单总额：<span id="TotalPriceCounter">￥<B><FONT color="#FF0000"><?php echo $TotalPrice;?></font></B>元</span></td>
      </tr>
      </table><?php
}

if($TotalPrice!=$OriginOrderTotalPrice || $TotalScore!=$OriginOrderTotalScore){ 
  if($Order_State>-3 && $Order_State<5)$conn->exec("update mg_orders set totalprice=$TotalPrice,totalscore=$TotalScore where ordername='$OrderName' and state=$Order_State");
}?>

    </td>
</tr>
</table>
<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
   <td background="images/topbg.gif" colspan="2"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />订单附加信息</b></td>
</tr>  
<tr>
   <td></td><td></td>
</tr>
<tr>
   <td colspan="2">
<?php if($Order_State>0){?>
      <form method="post" name="orderinfo" action="?mode=orderinfo" onsubmit="if(CheckOrderInfo(this))this.confirmbutton.disabled=true;else return false;" style="margin:0px"><input type="hidden" name="ordername" value="<?php echo $OrderName;?>">
      <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="20" bgcolor="#F7F7F7">
        <td WIDTH="100" height="25" align="right" background="images/topbg.gif"><strong>收 货 人：</strong></td>
        <td> &nbsp; <input type="text" name="receipt" value="<?php echo $Order_Receipt;?>" maxlength=16 <?php echo $BaseInputStyle;?>></td>
        <td width="40%" rowspan="8" valign="top">
           <table width="100%" height="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
           <tr>
             <td height="20" align="center" background="images/topbg.gif"><strong>客户留言</strong></td>
           </tr>
           <tr>
             <td height="100%"  bgcolor="#F7F7F7" valign="top"><textarea  readOnly wrap="VIRTUAL" style="WORD-BREAK: break-all;width:100%;height:100%;font-size: 9pt; border: 1 solid #808080"><?php echo $Order_UserRemark;?></textarea></td>
           </tr>
           </table>		
          </td>
      </tr>
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>收货地址：</strong></td>
          <td height="25"> &nbsp; <input type="text" name="address" maxlength=100  value="<?php echo $Order_Address;?>" <?php echo $BaseInputStyle;?>></td>
      </tr>
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>联系电话：</strong></td>
          <td height="25"> &nbsp; <input type="text" name="usertel" maxlength=50 value="<?php echo $Order_UserTel;?>"  <?php echo $BaseInputStyle;?>></td>
      </tr>
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>支付方式：</strong></td>
          <td height="25"> &nbsp; <input type="text" name="paymethod" maxlength=10 value="<?php echo $Order_PayMethod;?>"  <?php echo $BaseInputStyle;?>></td>
      </tr>
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>物流配送：</strong></td>
          <td height="25"> &nbsp; <?php
if($Order_State<5){
  echo '<b>包裹重量</b><input type="text" name="orderweight" maxlength=5 value="'.$Order_Weight_KG.'" style="width:50px;text-align:center" onFocus="this.select()" onkeyup="if(isNaN(value))execCommand(\'undo\');">千克 &nbsp; &nbsp; | &nbsp; <b>配送方式</b><select name="deliverymethod" onchange="if(newmethod.indexOf(\'上门\')>=0) this.form.deliverycode.value=\'0\';" style="width:90px"><option value="选择方式" >&nbsp;&nbsp;&nbsp;&nbsp;...</option>';  
  $res=$conn->query('select * from mg_delivery where method=2 order by sequence',PDO::FETCH_ASSOC);
  foreach($res as $row){
    $selected=($Order_DeliveryMethod==$row['subject'])?' selected':'';
    echo  '<option value="'.$row['subject'].'"'.$selected.'>'.$row['subject'].'</option>';            	   	
  }
  echo '<option value="其他方式" >其他方式</option></select><input type="text" name="deliverycode" placeholder="货单号码" maxlength=16 savedvalue="'.$Order_DeliveryCode.'" value="'.$Order_DeliveryCode.'" style="width:130px;text-align:center"><input type="button" value="追踪" onclick="DeliveryTrack(this.form)">';
} else echo ($Order_Weight_KG?'包裹重量<U>'.$Order_Weight_KG.'</U>Kg &nbsp; ':'').$Order_DeliveryMethod.($Order_DeliveryCode?'<U>'.$Order_DeliveryCode.'</U> <input type="button" value="追踪" onclick="DeliveryTrack(this.form)">':'');?>
</td>
      </tr> 
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>客服备注：</strong></td>
          <td height="25"> &nbsp; <input type="text" name="adminremark" value="<?php echo $Order_AdminRemark;?>" style="width:98%"></td>
      </tr>               
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong><?php echo (($Order_State<4)?'下单':'发货');?>时间：</strong></td>
          <td height="25">
            <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
            	<td><?php echo $Order_ActionTime;?></td>
            	<td align="right"><input type="submit" name="confirmbutton" value="保存订单信息"></td>
            </tr>
            </table>
          </td>
      </tr>        
      </table></form><?php
}
else{?>
      <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="25" bgcolor="#F7F7F7">
          <td width=100 align="right" background="images/topbg.gif"><strong>订单备注：</strong></td>
          <td nowrap> &nbsp; <?php if($Order_State==-1 && $IsOrderManager) echo '<form style="margin:0px" method="post" action="?mode=userremark"><input type="hidden" name="ordername" value="'.$OrderName.'"><input type="text" name="userremark" maxlength=100 value="'.$Order_UserRemark.'" style="width:90%"><input type="submit" value="保存"></form>';
          else if($Order_UserRemark)echo $Order_UserRemark;
          else echo '无';?></td>
      </tr>

      <?php if($own_popedomManage){?>
      <tr height="25" bgcolor="#F7F7F7">  
          <td align="right" background="images/topbg.gif"><strong>附加说明：</strong></td>
          <td nowrap> &nbsp;<form style="margin:0px" method="post" action="?mode=adminremark"><input type="hidden" name="ordername" value="<?php echo $OrderName;?>"><textarea name="adminremark" rows="3" wrap="VIRTUAL" style="WORD-BREAK: break-all;width:90%;font-size: 9pt; border: 1 solid #808080"><?php echo $Order_AdminRemark;?></textarea><input type="submit" value="保存"><br> &nbsp; <font color=#FF0000>250字以内，仅管理员可见.</font></form>	
          </td>
      </tr><?php
      }?>               
      <tr height="25" bgcolor="#F7F7F7"> 
          <td align="right" background="images/topbg.gif"><strong><?php echo ($Order_State>-3)?'下单日期':'入库日期';?>：</strong></td>
          <td> &nbsp; <?php echo $Order_ActionTime;?></td>
      </tr>     
      </table><?php
}?>

    </td>
  </tr>
</table>
<script><?php
echo 'InitMyOrder("'.$OrderName.'","'.$Order_Username.'",'.$Order_State.','.($IsOrderManager?'true':'false').','.($own_popedomFinance?'true':'false').','.(($CostOrScore=='cost')?'true':'false').');'.chr(13);
$DepotOptions='';
foreach($DepotArray as $depotIndex=>$depotName){
  if($depotIndex!=0) $DepotOptions.=',';
  $DepotOptions.= 'new Option("'.$depotName.'","'.$depotIndex.'")';
}
echo "InitDepot($Order_Importer,$Order_Exporter,new Array($DepotOptions));";?>
</script>
</body>
</html><?php db_close();?>
