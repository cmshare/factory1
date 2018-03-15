<?php require('includes/dbconn.php');
CheckLogin();
OpenDB();
CheckMenu('财务出纳日志');

$MaxSelection=8;
$Operations=array('score'=>1,'recharge'=>2,'refund'=>3,'pre_score'=>4,'pre_recharge'=>5,'pre_refund'=>6,'audit_score'=>7,'audit_recharge'=>8,'audit_refund'=>9);
$mode=@$_GET['mode'];

if(is_numeric($mode)){
  if($mode<0 || $mode>$MaxSelection)$mode=0;
}
else if($mode){
  if($mode=='sum')MakeSum();
  else if($mode=='audit') BatchAudit();
  else if($mode=='batchdelete')BatchDelete();
  else if($mode=='delete')DeleteLog();
  CloseDB();
  exit(0);
}
else $mode=0;

function MakeSum(){
  global $conn; 
  $idlist=FilterText($_POST['selectid']);
  if($idlist){
    $row=$conn->query('select sum(amount),count(id) from mg_accountlog where id in ('.$idlist.')',PDO::FETCH_NUM)->fetch();
    if($row)echo '所选'.$row[1].'项总额为'.round($row[0],1).'元';
  }
  else echo '没有选择操作对象！';
}

function BatchDelete(){
  global $conn; 
  if(CheckPopedom('FINANCE')){
    $selectid=$_POST['selectid'];
    if(is_array($selectid)){
      if($conn->exec('update mg_accountlog set operation=0 where id in ('.implode(',',$selectid).')')){
        PageReturn('积分预存款日志删除成功！');
      } 
    }
    else PageReturn('没有选择操作对象！',-1);
  }
  else PageReturn('非法操作！',-1);
}

function DeleteLog(){
  global $conn,$AdminUsername; 
  $selectid=$_POST['selectid'];
  if(is_numeric($selectid) && $selectid>0){
    $sql='update mg_accountlog set operation=0 where id='.$selectid;
    if(!CheckPopedom('FINANCE'))$sql.=' and adminuser=\''.$AdminUsername.'\'';
    if($conn->exec($sql)) echo '删除成功<OK>';
    else echo '删除失败！';
  }
  else echo '参数错误！';
}


function BatchAudit(){
  global $conn,$Operations,$AdminUsername;
  $selectid=$_POST['selectid'];
  if(!is_array($selectid)) PageReturn('没有选择操作对象！',-1);
  else if(CheckPopedom('FINANCE')) try{
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $conn->beginTransaction();//事务开始
    $conn->exec('lock tables mg_users write,mg_accountlog write'); 
    $pre_condition="(mg_accountlog.operation={$Operations['pre_score']} or mg_accountlog.operation={$Operations['pre_recharge']} or mg_accountlog.operation={$Operations['pre_refund']})";
    $res=$conn->query('select mg_accountlog.id,mg_accountlog.username,mg_accountlog.operation,mg_accountlog.amount,mg_accountlog.actiontime,mg_users.deposit,mg_users.score from mg_accountlog inner join mg_users on mg_accountlog.username=mg_users.username where mg_accountlog.id in ('.implode(',',$selectid).') and '.$pre_condition.'  order by mg_accountlog.actiontime asc',PDO::FETCH_ASSOC);
    $count=0;
    foreach($res as $row){
      $target=($row['operation']==$Operations['pre_score'])?'score':'deposit';
      if(!$conn->exec('update mg_accountlog inner join mg_users on mg_accountlog.username=mg_users.username set mg_users.'.$target.'=mg_users.'.$target.'+mg_accountlog.amount,mg_accountlog.surplus=mg_users.'.$target.'+mg_accountlog.amount,mg_accountlog.operation='.($row['operation']-3).',mg_accountlog.adminuser=concat(mg_accountlog.adminuser,\''.'|'.$AdminUsername.'\'),mg_accountlog.actiontime=unix_timestamp() where mg_accountlog.id='.$row['id']))PageReturn('err');
      $count++; 
       
    }
    $conn->exec('unlock tables'); 
    $conn->commit();
    PageReturn('共计完成 '.$count.' 条财务日志审核！');
  }
  catch(PDOException $ex){ 
    $conn->rollBack();  //事务回滚 
    PageReturn($ex->getMessage());
  } 
}

$Allow_AllAccount=true;
$Own_popedomFinance=CheckPopedom('FINANCE');
$Selections=array(0=>'所有日志',1=>'当日统计',2=>'当日入帐',3=>'当日出帐',4=>'所有入帐',5=>'所有出帐',6=>'存款变动',7=>'积分变动',8=>'未审核项'); 
//Const op_score=1,op_recharge=2,op_refund=3,op_pre_score=4,op_pre_recharge=5,op_pre_refund=6,op_audit_score=7,op_audit_recharge=8,op_audit_refund=9
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>财务出纳日志</title>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr>
  <td background="images/topbg.gif">
    <form method="post" style="margin:0px">
    <table width="100%">
    <tr>
      <td><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="<?php echo $_SERVER['PHP_SELF'];?>"><font color=#FF0000>财务出纳日志</font></a> -&gt; <?php echo $Selections[$mode];?></b></td>
      <td align="right"><input type="button" value=" 页面刷新 " onclick="self.location.reload()">&nbsp; <input type="button"  onclick="CalculateSelectSum(this.form)" value=" 计算求和 ">	
<?php if($Own_popedomFinance){?>
  <script language="javascript">
  function BatchDeleteLogs(myform){
    var selcount=Checkbox_SelectedCount("selectid[]");
    if(selcount==0) alert("没有选择操作对象！");
    else if(confirm("确定要删除所选的"+selcount+"条日志吗？")){
      myform.action = "?mode=batchdelete";
      myform.submit();
    }
  }
  function BatchAuditLogs(myform){
    var selcount=Checkbox_SelectedCount("selectid[]");
    if(selcount==0) alert("没有选择操作对象！");
    else if(confirm("确定要通过审核所选的"+selcount+"条日志吗？")){
      myform.action = "?mode=audit";
      myform.submit();
    }
  }

  function AuditLog(operation,logid){
  var OnDialogReturn=function(ret){
    if(ret){
     alert('操作成功！');
     self.location.reload();
    }
    return true;  
  }
  AsyncDialog("财务审核","mg_recharge.php?operation="+(operation+3)+"&id="+logid,500,420,OnDialogReturn);
}

  </script><input type="button"  onclick="BatchDeleteLogs(this.form)" value=" 删除所选日志 ">
  <input type="button"  onclick="BatchAuditLogs(this.form)" value=" 审核所选日志 "><?php
  }?></td>
    </tr>
    </table>
  </td>
</tr>
	
<tr>
  <td height="167" align="center" align="top" bgcolor="#FFFFFF"><?php

switch($mode){ 
  case 0: $where='where operation>0';break;
  case 1: $where='where (operation=2 or operation=3 or operation=5 or operation=6) and actiontime>unix_timestamp(curdate())';break;
  case 2: $where='where (operation=2 or operation=3 or operation=5 or operation=6) and actiontime>unix_timestamp(curdate()) and amount>0';break;
  case 3: $where='where (operation=2 or operation=3 or operation=5 or operation=6 ) and actiontime>unix_timestamp(curdate()) and amount<0';break;
  case 4: $where='where (operation=2 or operation=3 or operation=5 or operation=6 ) and amount>0';break;
  case 5: $where='where (operation=2 or operation=3 or operation=5 or operation=6 ) and amount<0';break;
  case 6: $where='where (operation=2 or operation=3 or operation=5 or operation=6 )';break;
  case 7: $where='where (operation=1 or operation=4)';break;
  case 8: $where='where (operation=4 or operation=5 or operation=6)';break;
  default:PageReturn('参数错误!',0);
}

$keyvalue=FilterText(trim(@$_GET['user']));
if($keyvalue){
  if(@$_GET['blur']=='1'){
     $blursearch=1;
     $CN_BlurSearch='模糊';
     $where.=' and username like \'%'.$keyvalue.'%\'';
  }
  else{
     $blursearch=0;
     $CN_BlurSearch='精确';
     $where.=' and username = \''.$keyvalue.'\'';
  }
  echo '<b>根据<font color="#FF6600">用户名</font>'.$CN_BlurSearch.'搜索关健字：</b><font color="#FF0000">'.$keyvalue.'</font> &nbsp; <a href="?" title="Cancel"><img src="images/delete.gif" align="absmiddle"></a>'; 
}
else $blursearch=1;

$select='select id,operation,amount,surplus,remark,username,adminuser,actiontime';
$orderby='order by actiontime desc,id desc';
$pagesize=($mode==2)?10000:20;
if($Allow_AllAccount && $keyvalue) $res=page_union_query($select,'from mg_accountlog',$where,$select,'from '.DB_HISTORY.'.mg_accountlog',$where,$orderby,$pagesize);
else $res=page_query($select,'from mg_accountlog',$where,$orderby,$pagesize);
if($total_records==0) echo  '<br><br><p align="center">没有找到符合条件的记录！</p>';
else{
  echo '<table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr bgcolor="#F7F7F7" align="center"> 
          <td width="5%" background="images/topbg.gif" height="25"><input type="checkbox" onclick="Checkbox_SelectAll(\'selectid[]\',this.checked)"/></td>
          <td width="10%" background="images/topbg.gif" bgcolor="#F7F7F7" nowrap><strong>用户名称</strong></td>
          <td width="10%" background="images/topbg.gif" nowrap><strong>变动数量</strong></td>
          <td width="10%" background="images/topbg.gif" nowrap><strong>账户余额</strong></td>
          <td width="30%" background="images/topbg.gif"><strong>变动原因</strong></td>
          <td width="10%" background="images/topbg.gif"><strong>经手人</strong></td>
          <td width="20%" background="images/topbg.gif"><strong>操作时间</strong></td>
          <td width="5%" background="images/topbg.gif" nowrap><strong>状态</strong></td>
        </tr>';
  foreach($res as $row){
    $operation=$row['operation'];
    echo '<tr bgcolor="#FFFFFF" align="center" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
          <td><input type="checkbox" name="selectid[]" value="'.$row['id'].'" onclick="mChk(this)"/></td>
          <td height="25"><a href="mg_usrinfo.php?user='.$row['username'].'">'.$row['username'].'</a></td>';
    if($operation==$Operations['score']){
      if($row['amount']>0) echo '<td><font color="#FF0000">＋</font>'.round($row['amount']).'分</td>';
      else echo '<td><font color="#FF0000">－</font>'.(0-round($row['amount'])).'分</td>';
      echo '<td>'.round($row['surplus']).'分</td>';
    }
    else if($operation==$Operations['recharge'] || $operation==$Operations['refund']){
      if($row['amount']>0) echo '<td><font color="#FF0000">＋</font>'.FormatPrice($row['amount']).'元</td>';
      else echo '<td><font color="#FF0000">－</font>'.FormatPrice(0-$row['amount']).'元</td>';
      echo '<td>'.FormatPrice($row['surplus']).'元</td>';
    }
    else if($operation==$Operations['pre_score']){
      if($row['amount']>0) echo '<td bgcolor="#FFBBAA"><font color="#FF0000">＋</font>'.round($row['amount']).'分</td>';
      else echo '<td bgcolor="#FFBBAA"><font color="#FF0000">－</font>'.(0-$row['amount']).'分</td>';
      echo '<td bgcolor="#FFBBAA"><font color="#FF0000"'.(($row['username'])?'':' onclick="self.location=\'?username='.$row['username'].'&&mode=6\';" style="TEXT-DECORATION: underline;cursor:pointer;color:#0000FF"').'>???</font></td>';    
    }
    else if($operation==$Operations['pre_recharge'] || $operation==$Operations['pre_refund']){
      if($row['amount']>0) echo '<td bgcolor="#FFBBAA"><font color="#FF0000">＋</font>'.FormatPrice($row['amount']).'元</td>';
      else echo '<td bgcolor="#FFBBAA"><font color="#FF0000">－</font>'.FormatPrice(0-$row['amount']).'元</td>';
      echo '<td bgcolor="#FFBBAA"><font color="#FF0000"'.(($row['username'])?'':' onclick="self.location=\'?username='.$row['username'].'&mode=6\';" style="TEXT-DECORATION: underline;cursor:pointer;color:#0000FF"').'>???</font></td>';
    }
    else{
      echo '<td>'.$row['amount'].'</td>
            <td>'.$row['surplus'].'</td>';
    }                    
    $OrderAdmin=$row['adminuser'];
    $AllowDelete=($Own_popedomFinance||$OrderAdmin==$AdminUsername);

    echo '<td><div style="width:100%">'.$row['remark'].'</div></td>';
    echo '<td>'.($AllowDelete?'<font color="#FF6600">'.$OrderAdmin.'</font>':$OrderAdmin).'</td>';
    echo '<td>'.date('Y-m-d H:i',$row['actiontime']).'</td><td>';

    if($operation<$Operations['pre_score']) echo '<img src="images/pic21.gif" alt="已审核">';
    else{
      if($AllowDelete) echo '<img src="images/delete_1.gif" alt="未审核，点击删除..."  style="cursor:pointer" onclick="DeleteLog('.$row['id'].')">';
      if($Own_popedomFinance) echo '<img src="images/edit_1.gif" alt="未审核，点击审核..." style="cursor:pointer" onclick="AuditLog('.$operation.','.$row['id'].')">';
      else echo '<img src="images/linkspic4.gif" alt="未审核">';
    }
    echo '</td></tr>';
  } 
  echo '<tr bgcolor="#F7F7F7" align="center">
          <td height="25" colspan="8" align="center" bgcolor="#FFFFFF">
             <script language="javascript">GeneratePageGuider("mode='.$mode.'&user="+encodeURIComponent("'.$keyvalue.'")+"&blur='.$blursearch.'",'.$total_records.','.$page.','.$total_pages.');</script>
          </td>
        </tr>
        </table>';
}?></form>
  </td>
</tr>
</table>

<?php if($Allow_AllAccount){?>
<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td align=center bgcolor="#FFFFFF"><form method="get" style="margin:0px">按用户名<input name="user" type="text" size="12" value="<?php echo $keyvalue;?>">
    <input name="blur" type="checkbox"  value="1"<?php if($blursearch)echo ' checked';?>>模糊查询 &nbsp;  <select name="mode"><?php
foreach($Selections as $key=>$value){
   if($key==$mode) echo '<option value="'.$key.'" selected>'.$value.'</option>';
   else echo '<option value="'.$key.'">'.$value.'</option>';
}?></select><input type="submit" value="查 询"></form></td>
</tr>
</table><?php
}?>

<script>
function DeleteLog(logID){
  if(confirm("确定删除该条记录？")){
    var OnGetSum=function(ret){if(ret){alert(ret);self.location.reload();}}
    AsyncPost("selectid="+logID,"?mode=delete",OnGetSum);
    return true;	
  }
} 
function CalculateSelectSum(myform){
  var selarray=Checkbox_SelectedValues("selectid[]",myform);
  if(!selarray) alert("没有选择操作对象！");
  else{
    var OnGetSum=function(ret){alert(ret);}
    AsyncPost("selectid="+selarray.join(','),"?mode=sum",OnGetSum);
    return true;	
  }
}
// if Own_popedomFinance then response.write "<img src=""images/edit_1.gif"" alt=""未审核，点击审核..."" style=""cursor:pointer"" onclick=""window.open('b2b_jfgl.asp?id="&rs("id")&"&operation="&CSTR(operation+3)&"','deposit_score')"">"
</script>  
</body>
</html><?php CloseDB();?>
