<?php require('includes/dbconn.php');
CheckLogin(); 
db_open();

$mode=@$_GET['mode'];
if($mode=='delete'){
  $Own_popedomSystem=CheckPopedom('SYSTEM');
  if($Own_popedomSystem){
    $selectid=@$_POST['selectid'];
    if(is_array($selectid)){
      $idlist=implode(',',$selectid);
      if($conn->exec('update mg_stocklog set productid=0 where id in ('.$idlist.')')) PageReturn('操作成功！');
    }
  }
}

function OrderLinkURL($OrderName,$ProductID){
   return 'mg_vieworder.php?ordername='.$OrderName.'&sel='.$ProductID;
}
  
$ProductID=@$_GET['id'];
if(!is_numeric($ProductID) || $ProductID<0) $ProductID=0;

if($mode=='his'){
  if($ProductID==0) PageRetrn('参数错误',-1);
  else $db_prefix=DB_HISTORY.'.';
}else $db_prefix='';



$LocalDepot=@$_GET['depot'];
if(!is_wholenumber($LocalDepot) || $LocalDepot<=0){
  if(CheckPopedom('MANAGE'))$LocalDepot=0;
  else $LocalDepot=GetAdminDepot();
}
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>商品销存日志</title>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td background="images/topbg.gif">
     <table width="100%" border=0> 
      <tr><td width="50%"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="?"><font color=#FF0000>商品销存日志</font></a></b></td><?php
  if($ProductID>0){
    $link_url='mg_stocklog.php?id='.$ProductID.'&depot='.$LocalDepot;
    if($mode=='his'){
      $link_txt='<font color="#FF0000">查看该商品近斯历史日志</font>';
    }
    else{
      $link_txt='查看该商品历史存档日志';
      $link_url.='&mode=his';
    }
    echo '<td align="center"><a href="'.$link_url.'" style="color:#0000FF">'.$link_txt.'</a>&nbsp; </td>';
  }?>
  <td nowrap align="right"><select onchange="self.location.href='?id=<?php echo $ProductID;?>&depot='+this.value+'&mode=<?php echo $mode;?>'"><option value="0">所有仓库</option><?php
$DepotArray=array();
$res=$conn->query('select id,depotname from mg_depot where enabled',PDO::FETCH_NUM);
foreach($res as $row){
  $DepotArray[$row[0]]=$row[1];
  $selectcode=($row[0]==$LocalDepot)?' selected':'';
  echo '<option value="'.$row[0].'"'.$selectcode.'>'.$row[1].'</option>';
}?></select></td>
      </tr>
      </table>
    </td>
</tr>
<tr> 
  <td height="167" valign="top" bgcolor="#FFFFFF"><form name="form1" method="post" action="?mode=delete"><?php
if($ProductID>0){
  $colspan=6;
  echo '&nbsp; &nbsp;您查找的商品编号是<font color="#FF6600">'.GenProductCode($ProductID).'</font>， 商品名称：'; 
  $row=$conn->query('select name,stock'.$LocalDepot.',stock0 from mg_product where id='.$ProductID,PDO::FETCH_NUM)->fetch();
  if($row) echo '<a href="'.GenProductLink($ProductID).'" target="_blank"><font color="#FF6600">'.$row[0].'</font></a>，当地库存<font color=#FF0000><b>'.$row[1].'</b></font>件，所有库存统计<font color=#FF0000 onclick="CheckStock()" style="cursor:pointer"><u><b>'.$row[2].'</b></u></font>件';
}
else $colspan=8;?>
<table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
<tr bgcolor="#F7F7F7" align="center"> 
    <td width="5%" background="images/topbg.gif" height="25" ><input type="checkbox" onclick="Checkbox_SelectAll('selectid[]',this.checked)" /></td><?php
   if($ProductID==0){?>
    <td width="5%" background="images/topbg.gif" bgcolor="#F7F7F7"  nowrap><strong>商品编号</strong></td>
    <td width="30%" background="images/topbg.gif" bgcolor="#F7F7F7"  nowrap><strong>商品名称</strong></td><?php
   }
   else{?>
    <script>function CheckStock(){AsyncDialog("商品库存明细", "checkstock.php?id=<?php echo $ProductID;?>&handle="+Math.random(),600,130,null);}</script><?php
   }?>	
    <td width="5%" background="images/topbg.gif" nowrap><strong>操作类型</strong></td>
    <td width="5%" background="images/topbg.gif" nowrap><strong>变动数量</strong></td>
    <td width="10%" background="images/topbg.gif" nowrap><strong>仓库</strong></td>
    <td width="20%" background="images/topbg.gif"><strong>备注</strong></td>
    <td width="10%" background="images/topbg.gif"><strong>用户</strong></td>
    <td width="10%" background="images/topbg.gif"><strong>操作时间</strong></td>
</tr><?php
$first_records=0;
if($ProductID>0 && $mode!='his'){
  $CurrentPage=@$_GET['page'];
  if(!is_wholenumber($CurrentPage) || $CurrentPage<=1){
    $sql='select mg_ordergoods.amount,mg_orders.exporter,mg_orders.importer,mg_orders.username,mg_orders.ordername,mg_orders.actiontime from `mg_ordergoods` inner join `mg_orders` on mg_ordergoods.ordername=mg_orders.ordername where mg_ordergoods.productid='.$ProductID.' and (mg_orders.state>-4 and mg_orders.state<4 and mg_orders.state<>0)'.($LocalDepot?" and (mg_orders.exporter=$LocalDepot or mg_orders.importer=$LocalDepot)":'').' order by mg_orders.actiontime desc,mg_ordergoods.productname';//预订单(未出库订单）库存变动
    $res=$conn->query($sql,PDO::FETCH_ASSOC);
    foreach($res as $row){
      $first_records++;
      $Importer=$row['importer'];
      $Exporter=$row['exporter'];
      echo '<tr height="25" align="center" bgcolor="#FFFFFF" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"><td><input type="checkbox" disabled /></td><td>预订</td>';
      if($Exporter){
        if($Importer) echo '<td>'.$row['amount'].'</td><td>'.$DepotArray[$Exporter].'=>'.$DepotArray[$Importer].'</td><td>订单<a href="'.OrderLinkURL($row['ordername'],$ProductID).'" style="color:#000055">'.$row['ordername'].'</a>，未完成</td>';
        else echo '<td>0(－'.$row['amount'].')</td><td>'.$DepotArray[$Exporter].'</td><td>订单<a href="'.OrderLinkURL($row['ordername'],$ProductID).'" style="color:#000055">'.$row['ordername'].'</a>，待出库</td>';
      }
      else if($Importer) echo '<td>0(＋'.$row['amount'].')</td><td>'.$DepotArray[$Importer].'</td><td>订单<a href="'.OrderLinkURL($row['ordername'],$ProductID).'" style="color:#000055">'.$row['ordername'].'</a>，待入库</td>';
      else echo '<td>0(<font color="RED">±'.$row['amount'].'</font>)</td><td>待定</td><td>订单<a href="'.OrderLinkURL($row['ordername'],$ProductID).'" style="color:#008855">'.$row['ordername'].'</a>，处理中</td>';
      echo '<td>'.$row['username'].'</td><td nowrap>'.date('Y-m-d H:i',$row['actiontime']).'</td></tr>';       
    }
    echo '<tr height="3" ><td colspan="'.$colspan.'" bgcolor="#FFFFFF"></td></tr>';
  }
}

if($ProductID>0){
  $sql_select1='select 0 as logid,mg_ordergoods.amount,mg_orders.exporter,mg_orders.importer,mg_orders.username,mg_orders.ordername as remark,mg_orders.actiontime as actiontime';
  $sql_where1='where mg_ordergoods.productid='.$ProductID.' and (mg_orders.state>3 or mg_orders.state<-3)'.($LocalDepot?" and (mg_orders.exporter=$LocalDepot or mg_orders.importer=$LocalDepot)":'');
  $sql_select2='select id as logid,amount,surplus as exporter,depot as importer,operator as username,remark,actiontime';
  $sql_where2='where productid='.$ProductID.($LocalDepot?" and depot=$LocalDepot":'');
  $sql_orderby='order by actiontime desc';
}
else{
  $sql_select1='select 0 as logid,mg_ordergoods.productid,mg_ordergoods.productname,mg_ordergoods.amount,mg_orders.exporter,mg_orders.importer,mg_orders.username,mg_orders.ordername as remark,mg_orders.actiontime as actiontime';
  $sql_where1='where (mg_orders.state>3 or mg_orders.state<-3)'.($LocalDepot?" and (mg_orders.exporter=$LocalDepot or mg_orders.importer=$LocalDepot)":'');
  $sql_select2='select id as logid,productid,null as productname,amount,surplus as exporter,depot as importer,operator as username,remark,actiontime';
  $sql_where2='where productid>0'.($LocalDepot?" and depot=$LocalDepot":'');
  $sql_orderby='order by actiontime desc,productname';
}
$sql_from1='from '.$db_prefix.'mg_ordergoods inner join '.$db_prefix.'mg_orders on mg_ordergoods.ordername=mg_orders.ordername';
$sql_from2='from '.$db_prefix.'mg_stocklog';

$res=page_union_query($sql_select1,$sql_from1,$sql_where1,$sql_select2,$sql_from2,$sql_where2,$sql_orderby,20);

if($total_records>0){
  foreach($res as $row){
    echo '<tr height="25" bgcolor="#FFFFFF" align="center" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">';
    $amount=$row['amount'];
    $Importer=$row['importer'];
    $Exporter=$row['exporter'];
    if($row['logid']==0){ 
      $remark=($ProductID>0)?$ProductID:$row['productid'];
      $remark='订单号<a href="'.OrderLinkURL($row['remark'],$remark).'" style="color:#000055">'.$row['remark'].'</a>';
      echo '<td><input type="checkbox" disabled></td>';
    } 
    else{
      $remark=$row['remark'];
      $surplus=$Exporter;
      if(is_numeric($surplus))$remark.='&nbsp;<font color="#FF0000">【'.$DepotArray[$Importer].'结余<b>'.$surplus.'</b>件】</font>';
      echo '<td><input type="checkbox" name="selectid[]" value="'.$row['logid'].'" onclick="mChk(this)"></td>';
      if($amount<0){
        $Exporter=$Importer;
        $Importer=0;
        $amount=-$amount;
      }
      else $Exporter=0;
    }
    if($ProductID==0){ 
      if($row['logid']>0) $ProductName=$conn->query('select name from mg_product where id='.$row['productid'])->fetchColumn(0);
      else $ProductName=$row['productname'];
      echo '<td><a href="?id='.$row['productid'].'">'.GenProductCode($row['productid']).'</a></td>';
      echo '<td align="left"><a href="'.GenProductLink($row['productid']).'" target="_blank">'.$ProductName.'</a></td>';
    }
    if($Importer && $Exporter) echo '<td>移库</td><td>'.$amount.'</td><td>'.$DepotArray[$Exporter].'=>'.$DepotArray[$Importer].'</td>';
    else if($Exporter) echo '<td style="color:#FF0000">出库</td><td>－'.$amount.'</td><td>'.$DepotArray[$Exporter].'</td>';
    else if($Importer) echo '<td style="color:#00AA00">入库</td><td>＋'.$amount.'</td><td>'.$DepotArray[$Importer].'</td>';
    echo '<td><div style="width:99%">'.$remark.'</div></td>
          <td>'.$row['username'].'</td>
          <td nowrap>'.date('Y-m-d H:i',$row['actiontime']).'</td>
        </tr>';
  }
  echo '<tr>';
  if(CheckPopedom('SYSTEM')){echo '<td align="center">
    <script language="javascript">
      function BatchDeleteLogs(myform){
        var selcount=Checkbox_SelectedCount("selectid[]");
	if(selcountu=0)alert("没有选择操作对象！");
        else if(confirm("确定要删除所选的"+selcount+"条日志吗？"))myform.submit();
      }</script>&nbsp; <input type="button" value=" 删除 " title="删除选定的记录" onclick="BatchDeleteLogs(this.form)"></td>';
     $colspan--;
  }
  echo '<td align="center" colspan="'.$colspan.'"><script language="javascript">GeneratePageGuider("id='.$ProductID.'&depot='.$LocalDepot.'&mode='.$mode.'",'.($total_records+$first_records).','.$page.','.$total_pages.');</script></td></tr>';

}
else if($first_records==0) echo '<tr><td align=center colspan="'.$colspan.'"> 数据库中无库存变动记录！</td></tr>';
?>

	  </table>
       </form>
     </td>
</tr></table>
<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td align="center"  bgcolor="#FFFFFF"><form method="get" style="margin:0px">
按商品编号<input name="id" type="text" size="12"> &nbsp;<input name="depot" type="hidden" value="<?php echo $LocalDepot;?>"> &nbsp; <input type="submit" value="查 询"></form></td>
</tr>
</table> 

</body>
</html><?php db_close();?>
