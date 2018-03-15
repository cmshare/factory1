<?php require('includes/dbconn.php');
CheckLogin();
OpenDB();
/*
-8:已存档的内部订单
-4:已完成的内部订单
-3: 完成入库审核
-2: 完成出库审核       
-1: 新建内部订单（未锁定）
0:已删除订单
1:新建订单（未锁定）
2:正在处理的订单（客服确认锁定）
3:已配货待发货
4:已发货待收款结算
5:已发货待客户确认收货
6:已完成订单（客户确认收货）
7:Reserved
8:存档订单
*/

$OrderName=FilterText(trim(@$_GET['ordername']));

PageBegin();
if($OrderName)PageShowDetail(); 
else PageListOrders();
PageClose('');

function PageBegin(){
  global $OrderName;
  $nav=($OrderName)?' -> <font color=#FF0000>订单明细</font>':'';
  echo '<html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8"><SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT><link href="includes/admincss.css" rel="stylesheet" type="text/css"><title>订单详细资料</title></head><body topmargin="0" leftmargin="0"><table width="99%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF"><tr><td background="images/topbg.gif"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"><tr><td nowrap><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <a href="mg_vieworder.php"><font color="#FF000">历史订单检索</font></a>'.$nav.'</b></td></tr></table></td></tr><tr><td height="100%" align="center" valign="top" bgcolor="#FFFFFF">'.chr(13);
}

function PageClose($info){
  if($info) echo '<p align=center>'.$info.'</p>';
  echo chr(13).'</td></tr></table></body></html>';
  CloseDB();
}

function PageShowDetail(){
  global $conn,$OrderName;
  $orders=$conn->query('select * from mg_orders where ordername=\''.$OrderName.'\' and state<>0',PDO::FETCH_ASSOC)->fetch();
  if($orders)$db_prefix='';
  else{
    $db_prefix=DB_HISTORY.'.';
    $orders=$conn->query('select * from '.$db_prefix.'mg_orders where ordername=\''.$OrderName.'\'',PDO::FETCH_ASSOC)->fetch();
    if(!$orders)PageClose('订单名称不存在--'.$OrderName);
  }
  $Order_State=$orders['state'];
  $Order_UserRemark=$orders['userremark'];
  $Order_AdminRemark=$orders['adminremark'];

  $Order_Exporter=$orders['exporter'];
  if($Order_Exporter>0)$Title_Exporter=$conn->query('select depotname from mg_depot where id='.$Order_Exporter)->fetchColumn(0);
  if(empty(@$Title_Exporter)) $Title_Exporter='其他单位';
    
  if($Order_State>0) $Title_Importer='客户('.$orders['receipt'].')';
  else{
    $Order_Importer=$orders['importer'];
    if($Order_Importer>0)$Title_Importer=$conn->query('select depotname from mg_depot where id='.$Order_Importer)->fetchColumn(0);
    if(empty(@$Title_Importer)) $Title_Importer='其他单位';
  }
  $res=$conn->query('select * from '.$db_prefix.'mg_ordergoods where ordername=\''.$OrderName.'\' order by productname',PDO::FETCH_ASSOC);
?>
    <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
    <tr>
      <td background="images/topbg.gif" nowrap>
       	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
    	   <td width="50%" nowrap><img src="images/pic17.gif" width="17" height="15" align="absmiddle" /><b>订单号</b>：<font color="#FF0000"><?php echo $OrderName;?></font> &nbsp; &nbsp; <img src="images/pic18.gif" width="17" height="15" align="absmiddle" /><b>下单用户</b>：<?php echo $orders['username'];?></a> &nbsp; &nbsp; &nbsp; <img src="images/pic19.gif" width="18" height="15" align="absmiddle" style=""/>客服：<font color="#FF6600"><?php echo $orders['operator'].'#'.$orders['support'];?></font></td>
           <td width="30%" nowrap align="center"><img src="images/arrow3.gif" height="16" align="absmiddle"><b>订单流向</b>：<font color="#FF0000"><?php echo $Title_Exporter;?></font>→出货至→<font color="#FF0000"><?php echo $Title_Importer;?></font></td>
           <td width="20%" nowrap align="right">&nbsp; &nbsp;  <img src="images/pic21.gif" height="15" align="absmiddle"><b><font color="#FF6600">订单状态</font>： <?php echo OrderStateName($Order_State);?></b> &nbsp; </td>
    	</tr>
    	</table>
      </td>
    </tr>
    </table>
    <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
    <tr height="20" bgcolor="#F7F7F7"> 
       <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong><strong>编号</strong></strong></td>
       <td WIDTH="60%" height="25" align="center" background="images/topbg.gif"><strong><strong>名称</strong></strong></td>
       <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>数量</strong></td>
       <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>单价</strong></td>
       <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>积分</strong></td>
       <td WIDTH="6%" height="25" align="center" background="images/topbg.gif"><strong>备 注</strong></td>
    </tr>
    <style type="text/css">.highlink, .highlink A:link, .highlink A:visited {color:#0000FF;font-weight:bold}</style><?php
$HighLight=@$_GET['sel'];
$TotalPrice=0;  //价格总计
$TotalScore=0;
$TotalProduct=0;   //商品总件数
foreach($res as $row){ 
  $Amount=$row['amount'];
  $Price=$row['price'];
  $Score=$row['score'];
  if($Amount>0){
    $TotalProduct+=$Amount;
    $TotalScore+=$Amount*$Score;
    $TotalPrice+=$Amount*$Price;
  }
  $Remark=$row['remark'];
  if($Remark)$Remark='<MARQUEE onmouseover="this.stop()" onmouseout="this.start()" scrollAmount="2" scrollDelay="100" width="100%">'.$Remark.'</MARQUEE>';
  else $Remark=' ';
  $ProductID=$row['productid'];
  $style=($ProductID==$HighLight)?' class="highlink"':'';
  echo '<tr align="center" bgcolor="#FFFFFF" height="20"'.$style.'> 
          <td><a href="mg_stocklog.php?id='.$ProductID.'">'.GenProductCode($ProductID).'</a></td>
          <td align="left">&nbsp;<a href="'.WEB_ROOT.'products/'.$ProductID.'.htm" target="_blank">'.$row['productname'].'</a></td>
          <td>'.$Amount.'</td>
	  <td>'.FormatPrice($Price).'</td>		
	  <td>'.$Score.'</td>
	  <td>'. $Remark.'</td></tr>';
}
echo '<tr height="25" align="center"> 
    <td background="images/topbg.gif" colspan="2"><b>合计</b></td>
    <td background="images/topbg.gif">'.$TotalProduct.'</td>
    <td background="images/topbg.gif"><b>'.FormatPrice($TotalPrice).'</b></td>
    <td background="images/topbg.gif">'.$TotalScore.'</td>
    <td background="images/topbg.gif">&nbsp;</td>
  </tr>
  </table>';?>
   </td>
</tr>
</table>
<br>
<table width="99%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle">订单附加信息</b></td>
</tr>  
<tr>
  <td><?php
  if($Order_State>0){?>  		
      <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="20" bgcolor="#F7F7F7">
          <td WIDTH="100" height="25" align="right" background="images/topbg.gif"><strong>收 货 人：</strong></td>
          <td> &nbsp; <?php echo $orders['receipt'];?></td>
          <td width="40%" rowspan="6">
             <table width="99%" height="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
             <tr>
              	<td height=20 align="center" background="images/topbg.gif"><strong>客户留言</strong></td>
             </tr>
             <tr>
              	<td height="100%" bgcolor="#F7F7F7"  valign="top" style="WORD-BREAK: break-all;width: 100%; height:100%;font-size: 9pt; border: 1 solid #808080"><?php echo $Order_UserRemark;?></td>
             </tr>
             </table>		
          </td>
      </tr>
      <tr height="25" bgcolor="#F7F7F7"> 
          <td align="right" background="images/topbg.gif"><strong>收货地址：</strong></td>
          <td> &nbsp; <?php echo $orders['address'];?></td>
          
      </tr>
      <tr height="25" bgcolor="#F7F7F7"> 
          <td align="right" background="images/topbg.gif"><strong>联系电话：</strong></td>
          <td> &nbsp; <?php echo $orders['usertel'];?></td>
      </tr>
      <tr height="25" bgcolor="#F7F7F7"> 
          <td align="right" background="images/topbg.gif"><strong>物流配送：</strong></td>
          <td> &nbsp; <?php
           $Order_DeliveryMethod=$orders['deliverymethod'];
           $Order_DeliveryCode=$orders['deliverycode'];
           $Order_Weight_KG=round($orders['weight']/1000,3);
           $Order_deliveryfee=$orders['deliveryfee'];
           echo ($Order_Weight_KG?'包裹重量<U>'.$Order_Weight_KG.'</U>Kg &nbsp; ':'').$Order_DeliveryMethod.($Order_DeliveryCode?'<U>'.$Order_DeliveryCode.'</U>':'');
           if($Order_deliveryfee>0) echo ' &nbsp; 物流费用<font color=#FF6600>'.FormatPrice($Order_deliveryfee).'</font>元';?></td>
      </tr> 
      <tr height="25" bgcolor="#F7F7F7"> 
          <td align="right" background="images/topbg.gif"><strong>订单金额：</strong></td>
          <td> &nbsp; <?php
   $Order_Adjust=$orders['adjust'];
   if($Order_Adjust){
     if($Order_Adjust>0) $Order_Adjust='+'.$Order_Adjust;
     $Order_Adjust=' (折扣调整'.$Order_Adjust.'元)';
   }else $Order_Adjust=''; 
   echo '<FONT color="#FF0000">￥'.FormatPrice($orders['totalprice']).'元</font>'.$Order_Adjust.'</td></tr>';?>
     <tr height="25" bgcolor="#F7F7F7"> 
          <td align="right" background="images/topbg.gif"><strong>发货时间：</strong></td>
          <td> &nbsp; <?php echo date('Y-m-d H-m',$orders['actiontime']);?></td>
      </tr>
      <tr height="25" bgcolor="#F7F7F7"> 
          <td align="right" background="images/topbg.gif"><strong>订单备注：</strong></td>
          <td colspan=2> &nbsp; <?php echo ($Order_AdminRemark)?$Order_AdminRemark:'无';?></td>
      </tr>
      </table><?php
  }
  else{?>
      <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="25" bgcolor="#F7F7F7">
          <td width=100 align="right" background="images/topbg.gif"><strong>订单备注：</strong></td>
          <td nowrap> &nbsp; <?php echo ($Order_UserRemark)?$Order_UserRemark:'无';?></td>
      </tr>
      <?php if($Order_AdminRemark) echo '<tr height="25" bgcolor="#F7F7F7"><td align="right" background="images/topbg.gif"><strong>附加说明：</strong></td><td> &nbsp; '.$Order_AdminRemark.'</td></tr>';?> 
      <tr height="25" bgcolor="#F7F7F7"> 
          <td align="right" background="images/topbg.gif"><strong><?php echo ($Order_State>-3)?'下单日期':'入库日期';?>：</strong></td>
          <td> &nbsp; <?php echo date('Y-m-d H-m',$orders['actiontime']);?></td>
      </tr>     
      </table><?php
  }
}

function GetDepotName($index){
  global $conn,$DepotNameArray;
  $depotName=@$DepotNameArray[$index];
  if(empty($depotName)){
    $depotName=$conn->query('select depotname from mg_depot where id='.$index)->fetchColumn(0);
    if($depotName)$DepotNameArray[$index]=$depotName;
    else $depotName='其它单位';
  }
  return $depotName;
}

function OrderStateName($state){
  switch($state){ 
    case -8: 
    case -4: return '<font color=#00aa00>已完成</font>';
    case -3: 
    case -2: return '<font color=#FF6600>出入库审核中</font>';
    case -1: 
    case  1: return '未处理';;
    case  2: return '<font color="#FF0000"><I>正在进行处理</I></font>';
    case  3: return '<font color="#8800FF">已配货待发货</font>';break;
    case  4: return '<font color="#0000aa"><b>已发货待收款</b></font>';
    case  5: return '<font color="#00aaff">已发货待确认</font>';
    case  6:
    case  8: return '<font color="#00aa00">已完成</font>';
    default: return '<font color=#FF0000>系统保留</font>';
  }
}

function PageListOrders(){
  global $conn,$DepotNameArray,$total_records,$total_pages,$page;
  $DepotNameArray=array();
  $SearchTitles=array('username'=>'用户名','receipt'=>'收货人','ordername'=>'订单号','deliverycode'=>'物流单号','remark'=>'订单备注');
  $keyvalue=FilterText(trim(@$_GET['kv']));
  if($keyvalue){//按用户查询
    $where='where state<>0';
    $orderby='order by actiontime desc';
    $keyname=FilterText(trim($_GET['kn']));
    if($keyname=='remark'){
      $where.=" and (userremark like '%$keyvalue%' or adminremark like '%$keyvalue%')";
      $blursearch=1;
      $CN_BlurSearch='模糊';
    }
    else{
      if(!$keyname || !array_key_exists($keyname,$SearchTitles)) $keyname='username';
      if(@$_GET['blur']=='1'){
        $where.=' and '.$keyname.' like \'%'.$keyvalue.'%\'';
        $blursearch=1;
        $CN_BlurSearch='模糊';
      }
      else{
        $where.=' and '.$keyname.' = \''.$keyvalue.'\'';
        $blursearch=0;
        $CN_BlurSearch='精确';
      }
    }
    echo '<b>根据<font color="#FF6600">'.$SearchTitles[$keyname].'</font>'.$CN_BlurSearch.'搜索关健字：</b><font color="#FF0000">'.$keyvalue.'</font> &nbsp; <a href="?" title="Cancel"><img src="images/delete.gif" align="absmiddle"></a> 
      <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr bgcolor="#F7F7F7" align="center"> 
          <td height="25" background="images/topbg.gif"><strong>订单号</strong></td>
          <td height="25" background="images/topbg.gif"><strong>下单用户</strong></td>
          <td height="25" background="images/topbg.gif"><strong>订单金额</strong></td>
          <td height="25" background="images/topbg.gif"><strong>订单流向</strong></td>
          <td height="25" background="images/topbg.gif"><strong>订单信息</strong></td>
          <td height="25" background="images/topbg.gif"><strong>订单状态</strong></td>
        </tr>';

    $res=page_union_query('select *','from mg_orders',$where,'select *','from '.DB_HISTORY.'.mg_orders',$where,$orderby,20);
    //$res=page_query('select *','from mg_orders',$where,$orderby,20);
    if($total_records==0)echo '<tr><td colspan=8 align=center>没有查询到对应的订单！</td><tr></table>';
    else{
      $AdminDepotIndex=GetAdminDepot();
      foreach($res as $row){      
	$state=$row['state'];
	$AdminRemark=$row['adminremark'];
	echo '<tr height="25" align="center" bgcolor="#FFFFFF"  onMouseOut="mOut(this)" onMouseOver="mOvr(this)"'.'>'; 
	echo '<td background="images/topbg.gif"> <a href="?ordername='.$row['ordername'].'">'.$row['ordername'].'</a></td>';
	echo '<td><a href="mg_usrinfo.php?user='.$row['username'].'">'.$row['username'].'</a></td>';
	echo '<td>'.FormatPrice($row['totalprice']).'</td>';
        echo '<td>'.GetDepotName($row['exporter']).' → '.GetDepotName($row['importer']).'</td>';
        if($state<0)echo '<td>'.$row['userremark'].'</td>';
        else echo '<td>'.$row['receipt'].' ['.$row['deliverymethod'].']</td>';
	if($AdminRemark)echo '<td style="BACKGROUND-POSITION: right 30%; BACKGROUND-IMAGE:url(images/memo.gif); BACKGROUND-REPEAT: no-repeat;Cursor:pointer" title="'.$AdminRemark.'">';
	else echo '<td>';
        echo OrderStateName($state).'</td></tr>';
      }
      echo '<tr><td colspan="6" height="30" bgcolor="#FFFFFF" valign="middle" align="center"><script>';
      echo "GeneratePageGuider('kn=$keyname&kv=$keyvalue&blur=$blursearch',$total_records,$page,$total_pages);</script></td></tr></table>";
    }
  }
  else{
    $blursearch=1;
    $keyname='';
    echo '<p align=center style="margin:50px">没有查询结果！</p>';
  }?>

  </td></tr></table><br>

  <table width="99%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr>
    <td height="34" align="center" bgcolor="#FFFFFF"><form name="schform"  method="get" style="margin:0px">按<select name="kn"><?php
      foreach($SearchTitles as $s_key=>$s_value){
        $selectcode=($s_key==$keyname)?' selected':'';
        echo '<option value="'.$s_key.'"'.$selectcode.'>'.$s_value.'</option>';
      }?></select><input name="kv" type="text" size="20" value="<?php echo $keyvalue;?>"> <input name="blur" type="checkbox" value="1"<?php if($blursearch)echo ' checked';?>>模糊 &nbsp; <input type="submit" value="查 询"></form><?php
    //echo '</td></tr></table>'; //由PageClose结束结尾
}
?>

      

