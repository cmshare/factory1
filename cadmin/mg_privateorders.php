<?php require('includes/dbconn.php');
CheckLogin();
OpenDB();

function GenOrderNO(){
  return date('ymdHis').sprintf('%04d',rand(1,9999));
}

function GetDepotName($index){
  global $conn,$DemotNameArray;
  if(!isset($DemotNameArray)){
    $DemotNameArray=array();
    $res=$conn->query('select id,depotname from mg_depot where enabled',PDO::FETCH_NUM);
    foreach($res as $row){
      $DemotNameArray[$row[0]]=$row[1];
    }
  } 
  if($index>0)return $DemotNameArray[$index];
  else return '外部单位';
}

$mode=@$_GET['mode'];  
if($mode){
  switch($mode){
    case 'neworder': GenNewOrder();break;
    case 'copyorder':CopyOrder();break;
    case 'commonorder':ChangeToCommonOrder();break; 
    case 'privateorder':ChangeToPrivateOrder();break;
  }
  CloseDB();
  exit(0);
}

function GenNewOrder(){
  global $conn,$AdminUsername;
  $AdminIDNumber=GetAdminIDNumber();
  $UserRemark=FilterText(trim($_POST['newvalue']));
  $conn->exec('lock tables mg_orders write'); 
  label_gen_ordername: $OrderName=GenOrderNO();
  $bExist=$conn->query('select id from mg_orders where ordername=\''.$OrderName.'\'')->fetchColumn(0);
  if($bExist)goto label_gen_ordername;
  $sql="mg_orders set ordername='$OrderName',state=-1,receipt='/',adjust=0,deliveryfee=0,weight=0,importer=0,exporter=0,address='',paymethod='',deliverymethod='/',deliverycode='',usertel='',userremark='$UserRemark',adminremark='',username='$AdminUsername',operator='$AdminUsername',support=$AdminIDNumber,totalprice=0,totalscore=0,actiontime=unix_timestamp()";
  if(!$conn->exec('update '.$sql.' where state=0 limit 1')&&!$conn->exec('insert into '.$sql))PageReturn('未知错误',-1);
  $conn->exec('unlock tables');  
  PageReturn('新建空白订单成功！');
}


function CopyOrder(){
  global $conn,$AdminUsername;
  $src_OrderName=FilterText(trim($_POST['newvalue']));
  if($src_OrderName){
    $row=$conn->query('select * from mg_orders where ordername=\''.$src_OrderName.'\'',PDO::FETCH_ASSOC)->fetch();
    if($row){
      $AdminIDNumber=GetAdminIDNumber();
      $conn->exec('lock tables mg_orders write'); 
      label_gen_ordername: $OrderName=GenOrderNO();
      $bExist=$conn->query('select id from mg_orders where ordername=\''.$OrderName.'\'')->fetchColumn(0);
      if($bExist)goto label_gen_ordername;
      $sql="mg_orders set ordername='$OrderName',state=-1,receipt='{$row['receipt']}',adjust={$row['adjust']},deliveryfee={$row['deliveryfee']},weight={$row['weight']},importer={$row['importer']},exporter={$row['exporter']},address='{$row['address']}',paymethod='{$row['paymethod']}',deliverymethod='{$row['deliverymethod']}',deliverycode='{$row['deliverycode']}',usertel='{$row['usertel']}',userremark='{$row['userremark']}',adminremark='{$row['adminremark']}',username='$AdminUsername',operator='$AdminUsername',support=$AdminIDNumber,totalprice={$row['totalprice']},totalscore={$row['totalscore']},actiontime=unix_timestamp()";
      if(!$conn->exec('update '.$sql.' where state=0 limit 1')&&!$conn->exec('insert into '.$sql))PageReturn('未知错误',-1);
      $conn->exec('unlock tables');  

      $res=$conn->query('select * from  mg_ordergoods where ordername=\''.$src_OrderName.'\'',PDO::FETCH_ASSOC);
      foreach($res as $row){
	$sql="mg_ordergoods set ordername='$OrderName',productid={$row['productid']},productname='{$row['productname']}',score={$row['score']},price={$row['price']},amount={$row['amount']},remark='{$row['remark']}',audit={$row['audit']}";
	if(!$conn->exec('update '.$sql.' where ordername is null limit 1') && !$conn->exec('insert into '.$sql)){
	  PageReturn('未知错误！');     
	}
      }
      PageReturn('复制订单成功！');
    }
    else PageReturn('源订单['.$src_OrderName.']不存在！');
  }
}

function ChangeToCommonOrder(){
  global $conn,$AdminUsername;
  $OrderName=FilterText(trim($_POST['newvalue']));
  if($OrderName){
    $row=$conn->query('select id,state,exporter,importer,username,operator from mg_orders where ordername=\''.$OrderName.'\'',PDO::FETCH_ASSOC)->fetch();
    if($row){
      if($row['state']!=-1) PageReturn('该订单状态不允许修改！');
      else if($row['username']!=$AdminUsername && $row['operator']!=$AdminUsername && !CheckPopedom('MANAGE')) PageReturn('无权修改该用户订单！');
      else{
        $sql='update mg_orders set state=1';
 	if($row['importer']>0) $sql.=',importer=0';
 	if($row['exporter']<=0) $sql.=',exporter='.GetAdminDepot();	
        if($conn->exec($sql.' where id='.$row['id'])) PageReturn('修改成功！'); 
       }
    }
    else PageReturn('该订单号不存在！');
  }
}

function ChangeToPrivateOrder(){
  global $conn,$AdminUsername;
  $OrderName=FilterText(trim($_POST['newvalue']));
  if($OrderName){
    $row=$conn->query('select id,state,username,operator from mg_orders where ordername=\''.$OrderName.'\'',PDO::FETCH_ASSOC)->fetch();
    if($row){
      if($row['state']!=1) PageReturn('该订单状态不允许修改！');
      else if($row['username']!=$AdminUsername && $row['operator']!=$AdminUsername && !CheckPopedom('MANAGE')) PageReturn('无权修改该用户订单！');
      else if($conn->exec('update mg_orders set state=-1 where id='.$row['id'])) PageReturn('修改成功！'); 
    }
    else PageReturn('该订单号不存在！');
  }
}
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<title>内部订单管理</title>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%"  border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr>
   <td height=30 background="images/topbg.gif">
     <form  style="margin:0px" method="post"><input type="hidden" name="newvalue">
     <table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
     <tr>
    	<td><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <a href="?"><font color=#FF0000>内部订单管理</font></a></b>
     	    </td>
     	  <td align="right" nowrap><input type="button" value="内部单转客户单" onclick="ChangeToCommonOrder(this.form)">&nbsp; <input type="button" value="客户单转内部单" onclick="ChangeToPrivateOrder(this.form)">&nbsp; <input type="button" value="新建空白订单" onclick="GenNewOrder(this.form)">&nbsp; <input type="button" value="复制订单..." onclick="GenCopyOrder(this.form)"></form></td>
     	  </tr>
     	  </table></form>
     </td>
  </tr>
  <tr> 
    <td valign="top" align="center" bgcolor="#FFFFFF"><?php  
$where='where state<0';
$keyvalue=FilterText(trim(@$_GET['kv']));
if($keyvalue){
   $keyname=trim(@$_GET['kn']);
   if($keyname=='remark'){
      $where.=" and (userremark like '%$keyvalue%' or adminremark like '%$keyvalue%')";
      $blursearch=1;
      $CN_BlurSearch='模糊';
      $CN_Keyname='订单备注';
   }
   else{
     if($keyname=='ordername'){
       $CN_Keyname='订单号';
     }
     else{
       $keyname='username';
       $CN_Keyname='用户名';
     }
     if(@$_GET['blur']=='1'){
       $where.=' and '.$keyname.' like \'%'.$keyvalue.'%\'';
       $CN_BlurSearch='模糊';
       $blursearch=1;
     }
     else{
       $where.=' and '.$keyname.'=\''.$keyvalue.'\'';
       $CN_BlurSearch='精确';
       $blursearch=0;
     }
   }
   echo '<b>根据<font color="#FF6600">'.$CN_Keyname.'</font>'.$CN_BlurSearch.'搜索</b>，查询关健字：<font color="#FF0000">'.$keyvalue.'</font> &nbsp; <a href="?" title="Cancel"><img src="images/delete.gif" align="absmiddle"></a>'; 
}
else{
   $keyname='';
   $blursearch=1;
}
echo '<table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <tr bgcolor="#F7F7F7" align="center" height="25"> 
     <td width="15%" background="images/topbg.gif"><strong>订单号</strong></td>
     <td width="10%" background="images/topbg.gif"><strong>下单用户</strong></td>
     <td width="10%" background="images/topbg.gif"><strong>订单金额</strong></td>
     <td width="15%" background="images/topbg.gif"><strong>订单流向</strong></td>
     <td width="40%" background="images/topbg.gif"><strong>订单备注</strong></td>
     <td width="10%" background="images/topbg.gif"><strong>订单状态</strong></td>
   </tr>';
$res=page_query('select ordername,username,exporter,importer,totalprice,userremark,state','from mg_orders',$where,'order by sign(state+4) desc,actiontime desc',25);
if($total_records==0){
  echo '<tr><td colspan=5 align=center>对不起，您选择的状态目前还没有订单！</td><tr>';
}
else{
  $i=0;
  $SplitterFlag=2;
  $ret=@$_SERVER['QUERY_STRING'];
  if($ret) $ret='&ret='.rawurlencode($ret);
 
  foreach($res as $row){
    //判断显示状态分割条
    if($SplitterFlag>0){
      if($SplitterFlag==2){ 
	$SplitterFlag=($row['state']>-4)?1:0;
      }
      else if($SplitterFlag==1){
	if($row['state']<=-4){
	  echo '<tr height="3" ><td colspan="6" bgcolor="#FFFFFF"></td></tr>';
	  $SplitterFlag=0;
	}
      }
    }?>

    <tr height="25" bgcolor="#FFFFFF" align="center" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
      <td><a href="mg_checkorder.php?ordername=<?php echo $row['ordername'].$ret;?>"><?php echo $row['ordername'];?></a></td>
      <td><?php echo $row['username'];?></td>
      <td><?php echo $row['totalprice'];?></td>
      <td><?php
$Exporter=$row['exporter'];
$Importer=$row['importer'];
if($Exporter && $Importer)echo GetDepotName($Exporter).' → '.GetDepotName($Importer);
else if($Importer) echo GetDepotName($Importer).' <font color="#00AA00">[入库]</font>';
else if($Exporter) echo GetDepotName($Exporter).' <font color="#FF0000">[出库]</font>';?></td>
      <td><?php echo $row['userremark'];?></td>
      <td><?php
     switch($row['state']){
       case -1: echo '未处理';break;
       case -2: echo '<font color=#FF6600>审核中</font>';break;
       case -3: echo '<font color=#00aa00>审核中</font>';break;
       case -4: echo '<font color=#00aa00>已完成</font>';break;
       case -8: echo '<font color=#00aa00>已完成</font>';break;//已完成并存档
       default: echo '<font color=#FF0000>系统保留</font>';
     }?></td>
        </tr><?php
   }?>
   <tr>
     <td colspan="7" height="30" bgcolor="#FFFFFF" valign="middle" align="center"><script language="javascript"><?php  
   echo 'GeneratePageGuider("kn='.$keyname.'&kv='.$keyvalue.'&blur='.$blursearch.'",'.$total_records.','.$page.','.$total_pages.');';?></script></td>
   </tr><?php
}?>
</table>
		
</td>
</tr>
</table>

<br>
<form method="get">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr>
  <td height="34" align="center" bgcolor="#FFFFFF">按<select name="kn">
    <option value="username"<?php if($keyname=='username')echo ' selected';?>>用 户 名</option>
    <option value="ordername"<?php if($keyname=='ordername')echo ' selected';?>>订 单 号</option>
    <option value="remark"<?php if($keyname=='remark')echo ' selected';?>>订单备注</option></select>
    <input name="kv" type="text" value="<?php echo $keyvalue;?>">
    <input type="checkbox"  value="1"<?php if($blursearch)echo ' checked';?>>模糊 &nbsp; <input type="submit" value="查 询">
  </td>
</tr>
</table></form>

<script>
 function GenNewOrder(myform){
   var getprompt=function(ret){
     if(ret){
       myform.action="?mode=neworder";
       myform.newvalue.value=ret;
       myform.submit();
     }
   }
   AsyncPrompt("新建订单","请设定订单备注:",getprompt);
 }

 function ChangeToCommonOrder(myform){
   var getprompt=function(ret){
     if(ret){
       myform.action="?mode=commonorder";
       myform.newvalue.value=ret;
       myform.submit();
     } 
   }
   AsyncPrompt("内部订单转换成客户订单","请输入内部订单号:",getprompt,"",16);
 }

 function GenCopyOrder(myform){
   var getprompt=function(ret){
     if(ret){
       myform.action="?mode=copyorder";
       myform.newvalue.value=ret;
       myform.submit();
     } 
   }
   AsyncPrompt("新建订单","请输入待复制的订单号:",getprompt,"",16);
 }
 
 function ChangeToPrivateOrder(myform){
   var getprompt=function(ret){
     if(ret){
       myform.action="?mode=privateorder";
       myform.newvalue.value=ret;
       myform.submit();
     } 
   }
   AsyncPrompt("客户订单转换成内部订单","请输入客户订单号:",getprompt,"",16);
 }
</script>
</body>
</html><?php CloseDB();?>
