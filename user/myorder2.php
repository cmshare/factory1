<?php require('../include/conn.php');
$ordername=trim(FilterText($_GET['ordername']));
if(empty($ordername))PageReturn('参数无效！');

CheckLogin();
db_open();

function ProductURL($pid){
 if(WEB_SITE>1) return WEB_ROOT.'product.htm?id='.$pid;
 else return  '/products/'.$pid.'.htm';
}

$row=$conn->query('select * from '.DB_BACKUP.'.`mg_orders` where ordername=\''.$ordername.'\' and username=\''.$LoginUserName.'\'',PDO::FETCH_ASSOC)->fetch();
if($row){
  $OriginOrderTotalPrice=$row['totalprice'];
  $OriginOrderTotalScore=$row['totalscore'];
  $DeliveryFee=$row['deliveryfee'];
  $Order_Receipt=$row['receipt'];
  $Order_Address=$row['address'];
  $Order_UserTel=$row['usertel'];
  $Order_DeliveryMethod=$row['deliverymethod'];
  $Order_Adjust=$row['adjust'];
  $Order_ActionTime=$row['actiontime'];
  $Order_DeliveryCode=$row['deliverycode'];
  $Order_UserRemark=$row['userremark'];
  $Order_AdminRemark=$row['adminremark'];
}
else{
   db_close();
   echo '<p align=center>订单不存在！</p>';
   exit(0);
}
$res=$conn->query('select * from '.DB_BACKUP.'.`mg_ordergoods` where ordername=\''.$ordername.'\' order by productname',PDO::FETCH_ASSOC);
?><html>
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<META http-equiv="Keywords" content="订单详细资料,化妆品批发">
<title>订单详细资料-<?php echo WEB_NAME;?></title>
<style type="text/css">
<!--
A{TEXT-DECORATION: none;}
A:link    {COLOR: #000000; TEXT-DECORATION: none}
A:visited {COLOR: #000000; TEXT-DECORATION: none}
A:hover   {COLOR: #FF0000; TEXT-DECORATION: underline} TD   {FONT-FAMILY:宋体;FONT-SIZE: 9pt;line-height: 150%;}
TR.pprow TD{BACKGROUND-IMAGE:url(<?php echo WEB_ROOT;?>images/topbg.gif); }
-->
</style>
</head>
<body topmargin="0" leftmargin="0">
<table width="99%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td  align="center">
       <b><font size=2>订单详细信息</font></b>
    </td>
  </tr>
  <tr> 
    <td height="200" valign="top" bgcolor="#FFFFFF">
    	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr class="pprow">
          <td nowrap>
          	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    	      <tr>
    		      <td width="70%" nowrap height="25"><img src="<?php echo WEB_ROOT;?>images/pic17.gif" width="17" height="15" align="absmiddle" />订单号：<b><font color="#FF0000"><?php echo $ordername;?></font></b> ，<img src="<?php echo WEB_ROOT;?>images/pic18.gif" width="17" height="15" align="absmiddle" />会员名：<b><?php echo $LoginUserName;?></b></td>
    		      <td width="30%" nowrap align="right"></td>
    		    </tr>
    		    </table>
          </td>
        </tr>
      </table>
      
	    <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="20" align="center" bgcolor="#F7F7F7" class="pprow"> 
          <td WIDTH="10%"><strong><strong>编号</strong></strong></td>
          <td WIDTH="60%"><strong><strong>名称</strong></strong></td>
          <td WIDTH="10%"><strong>数量</strong></td>
          <td WIDTH="10%"><strong>单价</strong></td>
          <td WIDTH="10%"><strong>备 注</strong></td>
      </tr><?php
foreach($res as $row){
  $remark=$row['remark'];
  if($remark)$remark='<MARQUEE onmouseover="this.stop()" onmouseout="this.start()" scrollAmount="2" scrollDelay="100" width="100%">'.$remark.'</MARQUEE>';
  else $remark='&nbsp;&nbsp;';
  echo '<tr align="center" bgcolor="#FFFFFF" height="20"><td>'.substr('0000'.$row['productid'],-5).'</td><td align="left">&nbsp;<a href="'.ProductURL($row['productid']).'" target="_blank">'.$row['productname'].'</a></td><td>'.$row['amount'].'</td><td>'.round($row['price'],2).'</td><td>'.$remark.'</td></tr>';
}?>
     </table>
     
     <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr  class="pprow">
      	<td align="center"><?php
          $Order_Adjust_signed=round($Order_Adjust,2);
      	  if($Order_Adjust>0) $Order_Adjust_signed='+'.$Order_Adjust_signed;
      	  echo '配送费用<font color=#FF0000>'.round($DeliveryFee,2).'</font>元';
      	  if($Order_Adjust) echo ' &nbsp;  折扣调整<font color=#FF0000>'.$Order_Adjust_signed.'</font>元';?>
      	  &nbsp; -&gt; &nbsp;  订单总额：￥<B><FONT color="#FF0000"><?php echo round($OriginOrderTotalPrice,2);?></font></B>元
          &nbsp; &nbsp; | &nbsp; &nbsp;  获得积分：<font color="#FF0000"><?php echo $OriginOrderTotalScore;?></font>分
        </td>
     </tr>
     </table>
      
    </td>
  </tr>
</table>
<br>
<table width="99%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr class="pprow"> 
    <td>
    	<b><img src="<?php echo WEB_ROOT;?>images/pic5.gif" width="28" height="22" align="absmiddle">订单附加信息</b>
    </td>
  </tr>  
  <tr>
  	<td>
      <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="20" bgcolor="#F7F7F7">
          <td WIDTH="100" align="right"><strong>收 货 人：</strong></td>
          <td> &nbsp; <?php echo $Order_Receipt;?></td>
          <td width="40%" rowspan="5" valign="top">
             		<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
             	  <tr class="pprow">
             	  	<td height="20" align="center"><strong>客户留言</strong></td>
             	  </tr>
             	  <tr>
             	  	<td width="100%" height="125" bgcolor="#F7F7F7"  valign="top"><textarea name="UserRemark" disabled rows="5" cols="20" wrap="VIRTUAL" style="WORD-BREAK: break-all;width:100%;height:100%;font-size: 9pt; border: 1 solid #808080" <?php if($Order_State>2) echo 'disabled';?>><?php echo $Order_UserRemark;?></textarea></td>
             	  </tr>
               	</table>		
          </td>
      </tr>
      <tr height="20" bgcolor="#F7F7F7"> 
          <td align="right"><strong>收货地址：</strong></td>
          <td> &nbsp; <?php echo $Order_Address;?></td>
          
      </tr>
      <tr height="20" bgcolor="#F7F7F7"> 
          <td align="right"><strong>联系电话：</strong></td>
          <td> &nbsp; <?php echo $Order_UserTel;?></td>
      </tr>
      <tr height="20" bgcolor="#F7F7F7"> 
          <td align="right"><strong>配送方式：</strong></td>
          <td> &nbsp; <?php echo $Order_DeliveryMethod;?></td>
      </tr> 
      <tr height="20" bgcolor="#F7F7F7"> 
          <td align="right"><strong>运单号码：</strong></td>
          <td> &nbsp;
          <?php echo ($Order_DeliveryCode)?$Order_DeliveryCode:'未知';?>
          </td>
      </tr> 
  <?php if($Order_AdminRemark) echo '<tr height="20" bgcolor="#F7F7F7"><td align="right"><strong>订单备注：</strong></td><td colspan=2 style="padding-left:12px">'.$Order_AdminRemark.'</td></tr>';?>
      <tr height="20" bgcolor="#F7F7F7"> 
          <td align="right" ><strong>发货时间：</strong></td>
          <td colspan=2> &nbsp; <?php date('Y-m-d H:i:s',$Order_ActionTime);?></td>

      </tr>       
      </table>
    </td>
  </tr>
</table>
</body>
</html><?php
db_close();?>
