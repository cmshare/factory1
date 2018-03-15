<?php require('includes/dbconn.php');
CheckLogin();
OpenDB();

$op_score=1;$op_recharge=2;$op_refund=3;$op_pre_score=4;$op_pre_recharge=5;$op_pre_refund=6;$op_audit_score=7;$op_audit_recharge=8;$op_audit_refund=9;

$mode=@$_GET['mode'];
if($mode){
  switch($mode){
    case 'audit': audit_account();break; 
    case 'new':   new_account();break;
  }
  CloseDB();
  exit(0);
}

function audit_account(){
  global $conn,$AdminUsername,$op_pre_score,$op_pre_recharge,$op_pre_refund;
  $accountID=$_POST['id'];
  if(is_numeric($accountID) && $accountID>0 && CheckPopedom('FINANCE')){
    $conn->exec('lock tables mg_users write,mg_accountlog write'); 
    $row=$conn->query('select mg_users.username,mg_users.deposit,mg_users.score,mg_accountlog.operation,mg_accountlog.amount from mg_accountlog inner join mg_users on mg_accountlog.username=mg_users.username where mg_accountlog.id='.$accountID,PDO::FETCH_ASSOC)->fetch();
    if($row){
      $Operation=$row['operation'];
      $UserName=$row['username'];
      $Adjust=$row['amount'];
      if($Operation==$op_pre_score){
        $obj_name='积分';
        $obj_unit='分';
        $surplus=$row['score']+$Adjust;
        $sql='set mg_users.score='.$surplus.',mg_accountlog.surplus='.$surplus.',mg_accountlog.operation='.($Operation-3); 
      }
      else if($Operation==$op_pre_recharge || $Operation==$op_pre_refund){
        $obj_name='预存款';
        $obj_unit='元';
        $surplus=$row['deposit']+$Adjust;
        $sql='set mg_users.deposit='.$surplus.',mg_accountlog.surplus='.$surplus.',mg_accountlog.operation='.($Operation-3);
      }
      else PageReturn('参数错误1',0);
      $Remark=FilterText(trim($_POST['remark']));
      if(strlen($Remark)>100) $Remark=substr($Remark,0,95).'...';
      $sql.=',mg_accountlog.adminuser=concat(mg_accountlog.adminuser,\''.'|'.$AdminUsername.'\'),mg_accountlog.remark=\''.$Remark.'\',mg_accountlog.actiontime=unix_timestamp()';
      $sql='update (mg_users inner join mg_accountlog on mg_users.username=mg_accountlog.username) '.$sql.' where mg_accountlog.id='.$accountID.' and mg_accountlog.operation='.$Operation;
      if($conn->exec($sql)){
        if($Adjust>0) $ret_msg='审核通过，用户['.$UserName.']的'.$obj_name.'已经成功增加了'.FormatPrice($Adjust).$obj_unit;
        else $ret_msg='审核通过，用户['.$UserName.']的'.$obj_name.'已经成功减少了'.(-$Adjust).$obj_unit;
        echo '<br><br><br><p align=center>'.$ret_msg.'</p><script>parent.closeDialog(true);</script>';
      }
    }
    $conn->exec('unlock tables');  
  }
  else PageReturn('参数错误2',0);
}

function new_account(){
  global $conn,$AdminUsername,$op_score,$op_recharge,$op_refund,$op_pre_score,$op_pre_recharge,$op_pre_refund;
  $operation=$_POST['operation'];
  if($operation==$op_score || $operation==$op_recharge || $operation==$op_refund){
    if(!CheckPopedom('FINANCE')) $operation+=3;
  } 
  else if($operation!=$op_pre_score && $operation!=$op_pre_recharge && $operation!=$op_pre_refund){
    PageReturn('参数错误1',0);
  }
  $ret_bool='false';
  $UserID=$_POST['userid'];
  $Adjust=$_POST['adjust'];
  if(!is_numeric($UserID) || $UserID<=0 || !is_numeric($Adjust) && $Adjust==0) PageReturn('参数错误2',0);
  $Remark=FilterText(trim($_POST['remark']));
  if(strlen($Remark)>100) $Remark=substr($Remark,0,95).'...';
  try{
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $conn->beginTransaction();//事务开始
    $row=$conn->query('select username,deposit,score from mg_users where id='.$UserID.' for update',PDO::FETCH_ASSOC)->fetch();
    if(empty($row))PageReturn('参数错误3',0);
    $UserName=$row['username'];
    $Deposit=round($row['deposit'],2);
    $Score=$row['score'];

    if($operation==$op_score){
      $surplus=$Score+$Adjust;
      if($conn->exec('update mg_users set score='.$surplus.' where id='.$UserID)){
        if($Adjust>0) $ret_msg='用户['.$UserName.']的积分已经成功增加了'.$Adjust.'分！';
        else $ret_msg='用户['.$UserName.']的积分已经成功减少了'.(-$Adjust).'分！';
      }
    }
    else if($operation==$op_recharge || $operation==$op_refund){
      $surplus=$Deposit+$Adjust;
      if($conn->exec('update mg_users set deposit='.$surplus.' where id='.$UserID)){
	if($Adjust<0) $ret_msg='用户['.$UserName.']的预存款已成功缴扣'.FormatPrice(0-$Adjust).'元！';
	else if($operation==$op_recharge) $ret_msg='用户['.$UserName.']的预存款已成功充值入账'.FormatPrice($Adjust).'元！';
	else $ret_msg='用户['.$UserName.']的预存款已成功返款入账'.FormatPrice($Adjust).'元！';
      } 
    }
    else if($operation==$op_pre_score){
      $surplus=$Score+$Adjust;
      if($Adjust>0) $ret_msg='用户['.$UserName.']的积分增加'.FormatPrice($Adjust).'分，申请提交成功，审核后生效！';
      else $ret_msg='用户['.$UserName.']的积分减少'.FormatPrice(0-$Adjust).'分，申请提交成功，审核后生效！';
    }
    else if($operation==$op_pre_recharge || $operation==$op_pre_refund){
      $surplus=$Deposit+$Adjust;
      if($Adjust<0) $ret_msg='用户['.$UserName.']的预存款账户减少'.FormatPrice(0-$Adjust).'元，申请提交成功，审核后生效！';
      else if($operation==$op_pre_recharge) $ret_msg='用户['.$UserName.']的预存款账户充值'.FormatPrice($Adjust).'元，申请提交成功，审核后生效！';
      else $ret_msg='用户['.$UserName.']的预存款账户返款'.FormatPrice($Adjust).'元，申请提交成功，审核后生效！';
    }
    $sql="mg_accountlog set username='$UserName',amount=$Adjust,operation=$operation,adminuser='$AdminUsername',surplus=$surplus,remark='$Remark',actiontime=unix_timestamp()";
    if($conn->exec('update '.$sql.' where operation=0 limit 1') || $conn->exec('insert into '.$sql)){
      $conn->commit();//事务完成
      $ret_bool='true';
    }
  }
  catch(PDOException $ex){ 
    $conn->rollBack();  //事务回滚 
    $ret_msg=$ex->getMessage();
  } 
  echo '<br><br><br><p align=center>'.$ret_msg.'</p><script>parent.closeDialog('.$ret_bool.');</script>';
}

$operation=@$_GET['operation'];
if(is_numeric($operation))$operation=(int)$operation;
else $operation=0;

if($operation==$op_audit_score || $operation==$op_audit_recharge || $operation==$op_audit_refund){
  if(CheckPopedom('FINANCE')) $audit_mode=true;
  else PageReturn('权限错误！',0);
  $accountID=@$_GET['id'];
  if(is_numeric($accountID) && $accountID>0){
    $row=$conn->query('select * from mg_accountlog where id='.$accountID.' and operation='.($operation-3),PDO::FETCH_ASSOC)->fetch();
    if($row){
      $UserName=$row['username'];
      $Adjust=$row['amount'];
      $Remark=$row['remark'];  
    }
    else PageReturn('参数错误1',0);
  }
  else PageReturn('参数错误2',0);
  $row=$conn->query('select id,realname,deposit,score from mg_users where username=\''.$UserName.'\'',PDO::FETCH_ASSOC)->fetch();
  if($row)$UserID=$row['id'];
  else PageReturn('参数错误3',0);
}
else{
  if($operation==$op_score || $operation==$op_recharge || $operation==$op_refund){
    if(!CheckPopedom('FINANCE')) $operation+=3;
  } 
  else if($operation!=$op_pre_score && $operation!=$op_pre_recharge && $operation!=$op_pre_refund){
    PageReturn('参数错误4',0);
  }
  $audit_mode=false;
  $UserID=@$_GET['userid'];
  if(is_numeric($UserID) && $UserID>0){
    $row=$conn->query('select username,realname,deposit,score from mg_users where id='.$UserID,PDO::FETCH_ASSOC)->fetch();
    if($row) $UserName=$row['username'];
    else PageReturn('参数错误5',0);
  }
  else PageReturn('参数错误6',0);
}
 
$RealName=$row['realname'];
$Deposit=round($row['deposit'],2);
$Score=$row['score'];
unset($row);
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>积分预存款管理</title>
</head>
<body leftmargin="0" topmargin="0">
<script language="javascript">
var hBeatTimer=null,DownCounter=30; /*30秒倒计时*/
function checksubmit(myform){
  var adjust=myform.adjust_display.value.trim();
  if (adjust=="" || isNaN(adjust)){
    alert("请正确填写变动数量！");
    return false;
  }
  if (myform.remark.value.trim() == ''){
    alert("请填写变动原因！");
    return false;
  }
<?php
if($operation==$op_score || $operation==$op_pre_score){?>
  if(myform.op_type[0].checked){
    if(!confirm('操作对象：<?php echo $UserName;?>\n操作类型：增加积分\n变动数量：加'+adjust+'分')) return false;
  }
  else if(myform.op_type[1].checked){
    if(!confirm('操作对象：<?php echo $UserName;?>\n操作类型：减少积分\n变动数量：减'+adjust+'分')) return false;
    adjust=-parseInt(adjust);
  }<?php
}
else if($operation==$op_recharge || $operation==$op_pre_recharge || $operation==$op_refund || $operation==$op_pre_refund){?>
  if(myform.op_type[0].checked){
    if(!confirm('操作对象：<?php echo $UserName;?>\n操作类型：预存款充值入账\n变动数量：加'+adjust+'元')) return false;
    myform.operation.value="<?php echo ($operation==$op_recharge || $operation==$op_refund)?$op_recharge:$op_pre_recharge;?>";
  }
  else if(myform.op_type[1].checked){
    if(!confirm('操作对象：<?php echo $UserName;?>\n操作类型：预存款返款入账\n变动数量：加'+adjust+'元')) return false;
    myform.operation.value="<?php echo ($operation==$op_recharge || $operation==$op_refund)?$op_refund:$op_pre_refund;?>";
  }
  else if(myform.op_type[2].checked) {
    if(!confirm('操作对象：<?php echo $UserName;?>\n操作类型：预存款缴扣\n变动数量：扣'+adjust+'元')) return false;
    adjust=-parseFloat(adjust);
    myform.operation.value="<?php echo ($operation==$op_recharge || $operation==$op_refund)?$op_recharge:$op_pre_recharge;?>";
  }<?php
}
else if($operation==$op_audit_score){?>
  if(myform.op_type[0].checked){
    if(!confirm('操作对象：<?php echo $UserName;?>\n操作类型：审核增加积分\n变动数量：加'+adjust+'分')) return false;
  }
  else if(myform.op_type[1].checked){
    if(!confirm('操作对象：<?php echo $UserName;?>\n操作类型：审核减少积分\n变动数量：减'+adjust+'分')) return false;
    adjust=-parseFloat(adjust);
  }<?php
}
else if($operation==$op_audit_recharge ||  $operation==$op_audit_refund){?>
  if(myform.op_type[0].checked){
    if(!confirm('操作对象：<?php echo $UserName;?>\n操作类型：审核预存款充值入账\n变动数量：加'+adjust+'元')) return false;
  }
  else if(myform.op_type[1].checked){
    if(!confirm('操作对象：<?php echo $UserName;?>\n操作类型：审核预存款返款入账\n变动数量：加'+adjust+'元')) return false;
  }	
  else if(myform.op_type[2].checked){
    if(!confirm('操作对象：<?php echo $UserName;?>\n操作类型：审核预存款缴扣\n变动数量：减'+adjust+'元')) return false;
    adjust=-parseFloat(adjust);
  }<?php
}?>	
  else{
    alert("请选择操作类型！");
    return false;
  }	
  myform.adjust.value=adjust;
  myform.action="?mode=<?php echo $audit_mode?'audit':'new';?>";
  myform.confirmbutton.disabled=true;
  clearInterval(hBeatTimer);
  return true;
}

function StartCloseWindow(counter){
  DownCounter=counter;
  if(DownCounter>0){
    var obj=document.forms[0];
    if(obj){
      obj=obj.CloseBtn;
      if(obj)obj.value="关闭["+DownCounter+"]";
    }
  }
  else parent.closeDialog();
}
hBeatTimer=setInterval("StartCloseWindow(DownCounter-1)",1000); 
</script>

<form method="post" onsubmit="return checksubmit(this);" style="margin:0px">
<table width="500" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
<tr bgcolor="#F7F7F7" align="center"> 
   <td height="25" colspan="2" background="images/topbg.gif"><input type="hidden" name="userid" value="<?php echo $UserID;?>"><input type="hidden" name="operation" value="<?php echo $operation;?>"><input type="hidden" name="id" value="<?php echo $accountID;?>"><strong><?php
    if($operation==$op_score || $operation==$op_pre_score) echo '客户积分管理';
    else if($operation==$op_recharge || $operation==$op_pre_recharge || $operation==$op_refund || $operation==$op_pre_refund) echo '客户预存款管理';
    else if($operation==$op_audit_score)echo '客户积分变动审核';
    else if($operation==$op_audit_recharge || $operation==$op_audit_refund) echo '客户预存款变动审核';?></strong></td>
</tr>
<tr>
   <td height="25" width="20%" align="center" bgcolor="#F7F7F7"><strong>操作对象</strong></td>
   <td height="25" width="80%" bgcolor="#FFFFFF">&nbsp;<font color="#FF0000"><?php echo $UserName;?> &nbsp; (<?php echo $RealName;?>)</font> </td>
</tr>
<tr>
   <td height="25" align="center" bgcolor="#F7F7F7"><strong>操作类型</strong></td>
   <td height="25" bgcolor="#FFFFFF"><?php

if($operation==$op_score || $operation==$op_pre_score){
   $operation_unit='分';
   echo '<input type="radio" name="op_type">增加积分 <input type="radio" name="op_type">减少积分';
}
else if($operation==$op_recharge || $operation==$op_pre_recharge || $operation==$op_refund || $operation==$op_pre_refund){
   $operation_unit='元';
   echo '<input name="op_type" type="radio">充值入账 <input name="op_type" type="radio">返款入账 <input name="op_type" type="radio">扣款出账';
}
else if($operation==$op_audit_score){
  $operation_unit='分';
  echo '<input type="radio" name="op_type" '.(($Adjust>0)?'checked':'disabled').' type="radio">增加积分'; 
  echo '<input type="radio" name="op_type" '.(($Adjust<0)?'checked':'disabled').' type="radio">减少积分'; 
}
else if($operation==$op_audit_recharge || $operation==$op_audit_refund){
  $operation_unit='元';
  echo '<input name="op_type" '.(($operation==$op_audit_recharge && $Adjust>0)?'checked':'disabled').' type="radio">充值';
  echo '<input name="op_type" '.(($operation==$op_audit_refund && $Adjust>0)?'checked':'disabled').' type="radio">返款';
  echo '<input name="op_type" '.(($Adjust<0)?'checked':'disabled').' type="radio">扣款';
}?></td>
</tr>
<tr>
  <td height="25" align="center" bgcolor="#F7F7F7"><strong>变动数量</strong><input name="adjust" type="hidden"></td>
  <td height="25" bgcolor="#FFFFFF"><input name="adjust_display" type="text" <?php if($audit_mode) echo 'value="'.abs($Adjust).'" disabled';?> style="text-align:center; width:50px"  size="5" maxlength="6" onMouseOver="DownCounter=30;this.focus();"  onFocus="this.select()" onkeyup="if(isNaN(value))execCommand('undo')"><?php echo $operation_unit;?> &nbsp;（当前账户余额<font color="#FF0000"><?php echo FormatPrice($Deposit);?></font>元，可用积分<font color="#FF0000"><?php echo $Score;?></font>分）</td>
</tr>
<tr>
  <td height="25" align="center" bgcolor="#F7F7F7"><strong>变动原因</strong><br><br><select onChange="this.form.remark.value=this.options[this.selectedIndex].text;this.selectedIndex=0;"><option>...</option><option>上门支付</option><option>工商银行</option><option>建设银行</option><option>农业银行</option><option>招商银行</option><option>中国邮政</option><option>支付宝()</option><option>微信支付()</option></select></td>
  <td height="25" bgcolor="#FFFFFF"><textarea name="remark" cols="50" rows="4" class="input_sr" style="WORD-BREAK: break-all;width:100%;" onkeyup="DownCounter=30;"><?php if($audit_mode) echo $Remark;?></textarea></td>
</tr>
<tr>
  <td height="25" colspan="2" align="center" bgcolor="#F7F7F7"><input name="confirmbutton" type="submit"  value=" 执行操作 " /> &nbsp; <input name="CloseBtn" type="button" value=" 取消 " onclick="StartCloseWindow(0)"> </td>
</tr>
</table>
</form>
<table width="500" border="1" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC" style="margin-top:5px">
<tr>
  <td colspan=7 align="center" background="images/topbg.gif"><b><font color=#FF0000><?php echo $UserName;?></font>当日帐务情况</b></td>
</tr><?php
if($operation==$op_score || $operation==$op_pre_score || $operation==$op_audit_score) $sql='(operation='.$op_score.' or operation='.$op_pre_score.')';
else $sql='(operation='.$op_recharge.' or operation='.$op_pre_recharge.' or operation='.$op_refund.' or operation='.$op_pre_refund.')';
$sql='select * from mg_accountlog where username=\''.$UserName.'\' and actiontime>unix_timestamp(curdate()) and '.$sql.' order by actiontime desc,id desc';
$total_records=0;
$res=$conn->query($sql,PDO::FETCH_ASSOC); 
foreach($res as $row){
  if($total_records++==0) echo '<tr  bgcolor=#FFFFFF align=center><td width="10%"><b>时间</b></td><td width="13%"><b>类型</b></td><td width="13%"><b>数额</b></td><td width="14%"><b>余额</b></td><td width="10%"><b>经手人</b></td><td width="30%"><b>变动原因</b></td><td width="10%"><b>状态</b></td></tr>';
  $operation=$row['operation'];
  $auditState='已审核';
  echo '<tr  bgcolor=#FFFFFF align=center><td height="25">'.date('H:i',$row['actiontime']).'</td>';
  if($operation==$op_score || $operation==$op_pre_score){
    if($row['amount']>0) echo '<td>增加积分</td><td><font color=#FF0000>＋</font>'.round($row['amount']).'分</td>';
    else echo '<td>减少积分</td><td><font color=#FF0000>－</font>'.(0-round($row['amount'])).'分</td>';
    if($operation==$op_score) echo '<td>'.round($row['surplus']).'分</td>';
    else{
      $auditState='<font color=#FF0000>未审核</font>';
      echo '<td><font color=#FF0000>???</font></td>';
    }
  }
  else{
    if($row['amount']>0){
       if($operation==$op_recharge || $operation==$op_pre_recharge) echo '<td>充值入账</td>';
       else if($operation==$op_refund || $operation==$op_pre_refund) echo '<td>返款入账</td>';
       echo '<td><font color=#FF0000>＋</font>'.FormatPrice($row['amount']).'元</td>';
    } 
    else echo '<td>扣款出账</td><td><font color=#FF0000>－</font>'.FormatPrice(0-$row['amount']).'元</td>';
    if($operation==$op_recharge || $operation==$op_refund) echo '<td>'.FormatPrice($row['surplus']).'元</td>';
    else{
      $auditState='<font color=#FF0000>未审核</font>';
      echo '<td><font color=#FF0000>???</font></td>';
    }
  }
  echo '<td>'.$row['adminuser'].'</td><td><MARQUEE onmouseover="this.stop()" onmouseout="this.start()" style="cursor:pointer" scrollAmount="2" scrollDelay="100" width="100%">'.$row['remark'].'</MARQUEE></td><td>'.$auditState.'</td></tr>';

}
if($total_records==0) echo  '<tr  bgcolor=#FFFFFF align=center><td colspan=6>注：该用户今天没有其它帐务记录！</td></tr>';?>
</table>
</body>
</html><?php CloseDB();?>
