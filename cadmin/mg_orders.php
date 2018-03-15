<?php require('includes/dbconn.php');
CheckLogin('ORDER');
OpenDB();
$own_popedomFinance=true;  //暂时谁都可以修改订单 
if(@$_POST['mode']=='remark'){
   $OrderID=$_POST['orderid'];
   if(is_numeric($OrderID) && $OrderID>0){
     $AdminRemark=FilterText(trim($_POST['remark']));
     if(strlen($AdminRemark)>255) $AdminRemark=substr($AdminRemark,0,250).'...';
     $sql="update mg_orders set adminremark='$AdminRemark' where id=$OrderID";
     if(!$own_popedomFinance)$sql.=" and operator='$AdminUsername'";
     if($conn->exec($sql)) echo '订单备注修改成功！<OK>';
     else echo '订单备注没有修改！<ERR>';
   }
   CloseDB();
   exit(0);
}
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Refresh" content="300;URL=<?php echo $_SERVER['PHP_SELF'];?>">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<style type="text/css">
.titlerow td{BACKGROUND-IMAGE:url(images/topbg.gif);font-weight:bold;}
.memocell {BACKGROUND-POSITION: right 30%; BACKGROUND-IMAGE:url(images/memo.gif); BACKGROUND-REPEAT: no-repeat;Cursor:pointer;}
</style>
<title>订单管理</title>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr>
     <td background="images/topbg.gif">
     	  <table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
     	  <tr>
 	    <td>
     	      <b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color="#FF0000">客户订单管理</font></b>
     	    </td>
     	    <td align="right" nowrap><?php
     	      $OrderState=@$_GET['state'];
              if(is_numeric($OrderState)) $OrderState=(int)$OrderState; else $OrderState=0;?>
              <select name="select" onChange="window.location=this.value"> 
		<option value="?state=0" <?php if($OrderState==0) echo 'selected';?>>全部订单状态</option>
                <option value="?state=1" <?php if($OrderState==1) echo 'selected';?>>未作任何处理</option>
                <option value="?state=2" <?php if($OrderState==2) echo 'selected';?>>正在进行处理</option>
                <option value="?state=3" <?php if($OrderState==3) echo 'selected';?>>已配货待发货</option>
                <option value="?state=4" <?php if($OrderState==4) echo 'selected';?>>已发货待收款</option>
                <option value="?state=5" <?php if($OrderState==5) echo 'selected';?>>已发货待确认</option>
                <option value="?state=6" <?php if($OrderState==6) echo 'selected';?>>订单交易完成</option>
                <option value="?state=-1" <?php if($OrderState==-1) echo 'selected';?>>过期失效订单</option>
              </select>
     	    </td>
     	  </tr>
     	  </table>
     </td>
  </tr>
  <tr> 
    <td valign="top" align="center" bgcolor="#FFFFFF"><?php
$SplitterFlag=0;
$DepotNameArray=array();
$SearchTitles=array('username'=>'用户名','receipt'=>'收货人','ordername'=>'订单号','deliverycode'=>'物流单号','remark'=>'订单备注');

$keyvalue=FilterText(trim(@$_GET['kv']));

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

function GetOrderCreateDate($ordername){
 return substr($ordername,0,2).'-'.substr($ordername,2,2).'-'.substr($ordername,4,2);
}

if($keyvalue){
   $where='where state>0';
   $orderby='order by sign(state-4) asc,actiontime desc';
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
   echo '<b>根据<font color="#FF6600">'.$SearchTitles[$keyname].'</font>'.$CN_BlurSearch.'搜索关健字：</b><font color="#FF0000">'.$keyvalue.'</font> &nbsp; <a href="?" title="Cancel"><img src="images/delete.gif" align="absmiddle"></a>'; 
   $SplitterFlag=2;
}
else{
  $keyname='';
  $blursearch=1;
  if($OrderState==0){//所有有效订单
    $where='where state>0 and state<6';
    $orderby='order by sign(state-4) asc,actiontime desc';
    $SplitterFlag=2;
  }
  else if($OrderState==-1){//过期订单
    $where='where state>0 and state<4 and actiontime < unix_timestamp()-30*24*60*60';
    $orderby='order by actiontime asc';
  }
  else if($OrderState<6){ //指定状态订单
    $where='where  state='.$OrderState;
    $orderby='order by actiontime desc';
  }
  else{   //交易完成订单
    $where='where  state>=6';
    $orderby='order by actiontime desc';    
  }
}
$res=page_query('select *','from mg_orders',$where,$orderby,25);
echo   '<table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr height="25" bgcolor="#F7F7F7" align="center" class="titlerow">
          <td width="20%">订单号</td><td width="12%">下单用户</td><td width="12%">收货人</td><td width="12%">订单金额</td><td width="11%">配送方式</td><td width="11%">仓库</td><td width="11%">客服</td><td width="11%">订单状态</td>
        </tr>';
if($total_records==0)echo '<tr><td colspan=8 align=center>没有查询到对应的订单！</td><tr>';
else{
  $ret=@$_SERVER['QUERY_STRING'];
  if($ret) $ret='&ret='.rawurlencode($ret);
  $OrderTimeout=time()-30*24*60*60;
  $AdminDepotIndex=GetAdminDepot();
  foreach($res as $row){      
    $OrderPriceStyle='';
    $state=$row['state'];
    //判断显示状态分割条
    if($SplitterFlag>0){
      if($SplitterFlag==2){
        $SplitterFlag=($state<4)?1:0;
      }
      else if($SplitterFlag==1){
         if($state>=4){ 
           echo '<tr height="3"><td colspan="8" bgcolor="#FFFFFF"></td></tr>';
           $SplitterFlag=0;
         }
      } 
      if($SplitterFlag>0){
        if($state<4){
           $deposit=$conn->query('select deposit from mg_users where username=\''.$row['username'].'\'')->fetchColumn(0);
           if($deposit>=$row['totalprice']) $OrderPriceStyle='style="color:GREEN;font-weight:bold"';
        }
        else if($state==4) $OrderPriceStyle='style="color:GREEN;font-weight:bold"';
      }
    }
    $AdminRemark=$row['adminremark'];
    $FilterTimeout=($state<4 && $row['actiontime']< $OrderTimeout)?' style="text-decoration:line-through"':'';
    echo '<tr height="25" align="center"  bgcolor="#FFFFFF"  onMouseOut="mOut(this)" onMouseOver="mOvr(this)"'.$FilterTimeout.'> 
          <td><a href="mg_checkorder.php?ordername='.$row['ordername'].$ret.'">[20'.GetOrderCreateDate($row['ordername']).'] <font color="#000066">'.$row['ordername'].'</font></a></td>
          <td><a href="mg_usrinfo.php?user='.rawurlencode($row['username']).'">'.$row['username'].'</a></td>
          <td>'.$row['receipt'].'</td>
          <td '.$OrderPriceStyle.'>'.FormatPrice($row['totalprice']).'</td>
          <td>'.$row['deliverymethod'].'</td>';
    echo ($row['exporter']==$AdminDepotIndex)?'<td>':'<td style="color:#FF0000;font-weight:bold">';
    echo GetDepotName($row['exporter']).'</td><td';
    if($row['operator']==$AdminUsername) echo ' style="color:#00AA00"';
    echo (($row['operator'])?('>'.$row['operator'].'</td>'):'>NONE</td>');
    if($AdminRemark)echo '<td class="memocell" title="'.$AdminRemark.'" onclick="ChangeRemark('.$row['id'].',this)">';
    else echo '<td>';
    switch($state){ 
      case 1: echo '未作任何处理';break;
      case 2: echo '<font color="#FF0000"><I>正在进行处理</I></font>';break;
      case 3: echo '<font color="#8800FF">已配货待发货</font>';break;
      case 4: echo '<font color="#0000aa"><b>已发货待收款</b></font>';break;
      case 5: echo '<font color="#00aaff">已发货待确认</font>';break;
      case 6: echo '<font color="#00aa00">订单交易完成</font>';break;
      case 8: echo '<font color="#00aa00">订单交易完成</font>';break;//已存档订单
    }
    echo '</td></tr>';
  }

  echo '<tr><td colspan="8" height="30" bgcolor="#FFFFFF" valign="middle" align="center"><script>';
  echo "GeneratePageGuider('state=$OrderState&kn=$keyname&kv=$keyvalue&blur=$blursearch',$total_records,$page,$total_pages);</script></td></tr>";
}?>
   </table></td>
</tr></table>
<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr>
  <td height="34" align="center" bgcolor="#FFFFFF"><form name="schform" method="get" style="margin:0px">按<select name="kn"><?php
foreach($SearchTitles as $s_key=>$s_value){
  $selectcode=($s_key==$keyname)?' selected':'';
  echo '<option value="'.$s_key.'"'.$selectcode.'>'.$s_value.'</option>';
}?></select><input name="kv" type="text" size="20" value="<?php echo $keyvalue;?>"> <input name="blur" type="checkbox" value="1"<?php if($blursearch)echo ' checked';?>>模糊 &nbsp; <input type="submit" value="查 询"></form>
    </td>
  </tr>
</table>
<SCRIPT LANGUAGE="JavaScript">
function ChangeRemark(OrderID,tableCell){
  var tds=tableCell.parentNode.getElementsByTagName('td');
  var OrderName=tds[0].children[0].innerHTML;
  var UserName=tds[1].children[0].innerHTML;
  var defValue=tableCell.title.trim();
  var getresult = function(newValue){
    if(newValue==defValue){
      alert("没有变化！");
      return true;
    }
    else if(newValue!=null){
       var ret=SyncPost("mode=remark&orderid="+OrderID+"&remark="+encodeURIComponent(newValue),"");
       if(ret && ret.indexOf("<OK>")>=0){
         newValue=newValue.replace(/</g,"&lt;").replace(/>/g,"&gt;");
         tableCell.title=newValue;
         alert('订单备注修改成功！');
         return true;
       }
    }  
  }
  AsyncPrompt("设定订单备注",OrderName+"@"+UserName,getresult,defValue,255); 
}
</script>
</body>
</html><?php CloseDB();?>
