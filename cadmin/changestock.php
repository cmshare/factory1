<?php require('includes/dbconn.php');
CheckLogin('STOCK');
OpenDB();?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>库存变动管理</title><?php

$productid=@$_GET['productid'];
if(is_numeric($productid)){
   $row=$conn->query('select name,stock0 from mg_product where id='.$productid,PDO::FETCH_ASSOC)->fetch();
   if($row){
     $ProductName=$row['name'];
     $ProductStock=$row['stock0'];
   }
   else goto page_exit;
}
else{
   page_exit:echo "<p align=center>参数错误</p>";
   CloseDB();
   exit(0);
}
 
if(@$_GET['mode']=='change'){
   $amount=trim(@$_POST['amount']);
   if(is_numeric($amount)) $amount=(int)$amount;else $amount=0;
   if($amount!=0){
      $mydepot=@$_POST['depot'];
      if(is_numeric($mydepot)) $mydepot=(int)($mydepot); else $mydepot=0;
      if($mydepot<=0) $op_type='';
      else $op_type=@$_POST['op_type'];
      if($op_type=='1' || $op_type=='2'){
         $StockLocal='stock'.$mydepot;
         $SQLLog="mg_stocklog set productid=$productid,depot=$mydepot,operator='$AdminUsername',actiontime=unix_timestamp()";
         $remark=trim(FilterText(@$_POST['remark']));
         if(strlen($remark)>100) $remark=substr($remark,0,95).'...';
         $SQLLog.=',remark=\''.$remark.'\''; 
         try{//事务管理
            $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $conn->beginTransaction();//事务开始
            $row=$conn->query('select stock0,'.$StockLocal.' from mg_product where id='.$productid.' for update',PDO::FETCH_ASSOC)->fetch();
            if($row){
              if($op_type=='1'){
                $SQLLog.=',amount='.$amount.',surplus='.($row[$StockLocal]+$amount); 
                $sql="update mg_product set $StockLocal=$StockLocal+$amount,stock0=stock0+$amount where id=$productid";
                $ret_msg='商品【<font color=#FF6600>'.$ProductName.'</font>】的库存已经成功<font color=#FF0000>增加<b>'.$amount.'</b></font>件！';
              }
              else{
                $SQLLog.=',amount='.(-$amount).',surplus='.($row[$StockLocal]-$amount); 
                $sql="update mg_product set $StockLocal=$StockLocal-$amount,stock0=stock0-$amount where id=$productid";
                $ret_msg='商品【<font color=#FF6600>'.$ProductName.'</font>】的库存已经成功<font color=#FF0000>减少<b>'.$amount.'</b></font>件！';
              }
              if($conn->exec('update '.$SQLLog.' where productid=0 limit 1') || $conn->exec('insert into '.$SQLLog)){
                if($conn->exec($sql))$conn->commit();//事务完成
                else throw new PDOException('操作失败,数据库异常！');  
              }
              else $ret_msg="操作失败"; 
            }
         }
         catch(PDOException $ex){ 
	   $conn->rollBack();  //事务回滚 
	   $ret_msg=$ex->getMessage();
	 } 
      }
      else $ret_msg='<br><br><p align=center>参数错误</p>';
      echo '<br><br><p align=center>'.$ret_msg.'<br><br><br><input type="button" onclick="parent.closeDialog(true)" value=" 确定 "></p>';
  }
  CloseDB(); 
  exit(0);
}?>

</head>
<body leftmargin="0" topmargin="0">
	
<script language="javascript">

var hBeatTimer=null,DownCounter=30; /*30秒倒计时*/

function checksubmit(myform){
  var tmpvalue=myform.amount.value.trim();
  if ( tmpvalue=="" || isNaN(tmpvalue)){
    alert("请正确填写变动数量！");
    myform.amount.focus();
    return false;
  }
  else if(myform.depot.selectedIndex == 0){
     alert("请选择操仓对象！");
     myform.depot.focus();
     return false;
  }	
  else if (myform.remark.value.trim() == ''){
    alert("请填写变动原因！");
    myform.remark.focus();
    return false;
  }
  tmpvalue=myform.depot.options[myform.depot.selectedIndex].text;
  if(myform.op_type[0].checked){
    if(!confirm("商品名称：<?php echo $ProductName;?>\n操作类型：增加["+tmpvalue+"]库存量\n变动数量：加"+myform.amount.value+"件")) return false;
  }
  else if(myform.op_type[1].checked){
    if(!confirm("商品名称：<?php echo $ProductName;?>\n操作类型：减少["+tmpvalue+"]库存量\n变动数量：减"+myform.amount.value+"件")) return false;
  }
  else{
    alert("请选择操作类型（增加/减少库存？）！");
    return false;
  }
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
  }else parent.closeDialog();
}

function CheckStock(){
  parent.AsyncDialog("商品库存明细", "checkstock.php?id=<?php echo $productid;?>&handle="+Math.random(),600,130,null);
}

//hBeatTimer=setInterval("StartCloseWindow(DownCounter-1)",1000); 
</script>
<form action="?mode=change&productid=<?php echo $productid;?>" method="post" name="myform" style="margin:0px" onsubmit="return checksubmit(this);">
<table width="500" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <td height="25" width="20%" align="center" bgcolor="#F7F7F7"><strong>商品名称</strong></td>
  <td height="25" width="80%" bgcolor="#FFFFFF">&nbsp;<font color="#FF0000"><?php echo $ProductName;?></font>  
  </td>
</tr>
<tr>
  <td height="25" align="center" bgcolor="#F7F7F7"><strong>操作类型</strong></td>
  <td height="25" bgcolor="#FFFFFF">&nbsp; <select name="depot"><option value="0">选择仓库...</option><?php
	$res=$conn->query('select id,depotname from mg_depot where enabled',PDO::FETCH_NUM);
	foreach($res as $row) echo '<option value="'.$row[0].'">'.$row[1].'</option>';?></select>&nbsp;
   <input type="radio" name="op_type" value="1" />增加库存 <input type="radio" name="op_type" value="2" <?php if(!CheckPopedom('PRODUCT')) echo 'disabled';?>>减少库存</td>
</tr>
<tr>
  <td height="25" align="center" bgcolor="#F7F7F7"><strong>变动数量</strong></td>
  <td height="25" bgcolor="#FFFFFF">&nbsp; <input name="amount" type="text" style="text-align:center; width:50px" size="5" maxlength="6" onMouseOver="DownCounter=30;this.focus();"  onFocus="this.select()" onkeyup="if(isNaN(value))execCommand('undo')">
    	 &nbsp;（当前库存总量<font color="#FF0000" class="dummyLink" onclick="CheckStock()"><?php echo $ProductStock;?></font>件）</td>
</tr>
<tr>
  <td height="25" align="center" bgcolor="#F7F7F7"><strong>变动原因</strong><br>(80字以内)</td>
  <td height="25" bgcolor="#FFFFFF"><textarea name="remark" cols="50" rows="4" class="input_sr" style="width:100%;" onkeyup="DownCounter=30;"></textarea></td>
</tr>
<tr>
  <td height="25" colspan="2" align="center" bgcolor="#F7F7F7"><input name="confirmbutton" type="submit" value=" 执行操作 " /> &nbsp; <input name="CloseBtn" type="button" value=" 取消 " onclick="StartCloseWindow(0)">  </td>
</tr>
</table></form>
</body>
</html><?php
 CloseDB();?>
