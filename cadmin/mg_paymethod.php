<?php require('includes/dbconn.php');
CheckLogin();
db_open();

$action=@$_GET['action'];
if($action){
    if($action=='zhifudel'){
      $id=$_POST['id'];
      if(is_numeric($id) && $id>0){
        if($conn->exec('delete from mg_delivery where id='.$id)) PageReturn('删除成功！');
      }
    }
    else if($action=='zhifusave'){
      $id=$_POST['id'];
      if(is_numeric($id) && $id>0){
        $subject=FilterText(trim($_POST['subject']));
        $sequence=$_POST['sequence'];
        $memo=FilterText(trim($_POST['memo']));
        $conn->exec("update mg_delivery set subject='$subject',sequence=$sequence,memo='$memo',method=1 where id=$id");
        PageReturn('保存成功！');
      }
    }
    else if($action=='zhifuadd'){
      $subject=FilterText(trim($_POST['subject']));
      $sequence=$_POST['sequence'];
      $memo=FilterText(trim($_POST['memo']));
      if($conn->exec("insert into mg_delivery set subject='$subject',sequence=$sequence,memo='$memo',method=1"))PageReturn('添加成功！'); 
    }
    else if($action=='save1'){ #保存支付宝信息的地方
      $alipayenabled=($_POST['alipayenabled']=='支付宝有效')?'1':'0';
      $alipaymail=FilterText(trim($_POST['alipaymail']));
      $alipaypassword=FilterText(trim($_POST['alipaypassword']));
      $alipaypartnerid=FilterText(trim($_POST['alipaypartnerid']));
      if($conn->exec("update mg_configs set alipayenabled='$alipayenabled',alipaymail='$alipaymail',alipaypassword='$alipaypassword',alipaypartnerid='$alipaypartnerid'"))PageReturn('操作成功！');
      else PageReturn('没有改变');
    }
    else if($action=='save2'){ #网银支付宝信息的地方
      $chinabankenabled=($_POST['chinabankenabled']=='网银有效')?'1':'0';
      $chinabankuid=FilterText(trim($_POST['chinabankuid']));
      $chinabankkey=FilterText(trim($_POST['chinabankkey']));
      if($conn->exec("update mg_configs set chinabankenabled='$chinabankenabled',chinabankuid='$chinabankuid',chinabankkey='$chinabankkey'"))PageReturn('操作成功！');
      else PageReturn('没有改变');
    }
    else if($action=='save3'){ #网银支付宝信息的地方
      $nnbill_0=($_POST['nnbill_0']=='快钱有效')?'1':'0';
      $nnbill_1=FilterText(trim($_POST['nnbill_1']));
      $nnbill_2=FilterText(trim($_POST['nnbill_2']));
      $nnbill_3=FilterText(trim($_POST['nnbill_3']));
      if($conn->exec("update mg_configs set nnbill_0='$nnbill_0',nnbill_1='$nnbill_1',nnbill_2='$nnbill_2',nnbill_3='$nnbill_3'"))PageReturn('操作成功！');
      else PageReturn('没有改变');
    }
    PageReturn('参数错误~');
} 

$row=$conn->query('select alipayenabled,alipaymail,alipaypassword,alipaypartnerid,chinabankenabled,chinabankuid,chinabankkey,nnbill_0,nnbill_1,nnbill_2,nnbill_3 from mg_configs',PDO::FETCH_ASSOC)->fetch();
if($row){
   $alipayenabled=$row['alipayenabled'];
   $alipaymail=$row['alipaymail'];
   $alipaypassword=$row['alipaypassword'];
   $alipaypartnerid=$row['alipaypartnerid'];
   $chinabankenabled=$row['chinabankenabled'];
   $chinabankuid=$row['chinabankuid'];
   $chinabankkey=$row['chinabankkey'];
   $nnbill_0=$row['nnbill_0'];
   $nnbill_1=$row['nnbill_1'];
   $nnbill_2=$row['nnbill_2'];
   $nnbill_3=$row['nnbill_3'];
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<script language="javascript">
function ChkModifyAlipay(myForm){
  if (myForm.alipaymail.value==""){
    alert("支付宝账户没有填写!");
    return false;
  }
  if (myForm.alipaypassword.value==""){
    alert("安全校验码没有填写!");
    return false;
  }
  if (myForm.alipaypartnerid.value==""){
    alert("合作者身份（partnerID）没有填写!");
    return false;
  }
  myForm.submit();
}
function ChkModifyChinabank(myForm){
	if (myForm.chinabankuid.value=="")
	{ alert("网银商户编号没有填写!");
		return false;
	}
	if (myForm.chinabankkey.value=="")
	{	alert("网银安全密钥没有填写!");
		return false;
	}
	myForm.submit();
}
function ChkModifyNNPay(myForm){
  if (myForm.nnbill_1.value=="" || myForm.nnbill_2.value=="" || myForm.nnbill_3.value=="")
  { alert("没有填写完整!");
    return false;
  }
  myForm.submit();
}
function modify_object(myform){
 var subject=myform.subject.value.trim();
 var sequence=myform.sequence.value.trim();
 if(!subject){
   alert("支付方式不能为空！");
   myform.subject.focus();
   return false;
 }
 else if(sequence=='' || isNaN(sequence)){
   alert("序号无效！");
   myform.sequence.focus();
   return false;
 }
 else{
   myform.action="?action=zhifusave";
   myform.submit();
 } 
}

function delete_object(myform){
  if(confirm("确定删除该项目？")){
    myform.action="?action=zhifudel";
    myform.submit();
  }
}

</script>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF"> 
<tr> 
  <td height="20" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>支付方式管理</font></b></td>
</tr> 
<tr>  
<td bgcolor="#FFFFFF"><br>

  <table width="96%"  border="1" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <tr bgcolor="#FFCC00"><form action="?action=save1" method="post" name="mForm">
    <td width="17%" align="center"><img src="/onlinepay/alipay/images/alipaylogo2.gif"></td>
    <td width="28%">
      <strong>
      <input name="alipayenabled" type="checkbox" value="支付宝有效" <?php if($alipayenabled) echo 'checked';?>>
      请勾选此处开启支付宝在线支付功能，此外您还需要填写右侧信息 → </strong></td>
    <td width="45%"><table width="96%"  border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FF0000">
      <tr>
        <td align="center" bgcolor="#FFFF00">
        	<font color=#FF0000>支付宝账户：</font> <input name="alipaymail" type="text" class="input_sr" value="<?php echo $alipaymail;?>" size="40"> *<br>
          <font color=#FF0000>安全校验码：</font> <input name="alipaypassword" type="text" class="input_sr" value="<?php echo $alipaypassword;?>" size="40"> *<br>
          <font color=#FF0000>合作者身份：</font> <input name="alipaypartnerid" type="text" class="input_sr" value="<?php echo $alipaypartnerid;?>" size="40"> *<br>
        </td>
      </tr>
    </table></td>
    <td colspan="3" align="center"><input name="Submit2" type="button" value=" 保 存 " onclick="ChkModifyAlipay(this.form)"></td>
</tr></form>
</table> 



  <table width="96%"  border="1" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <tr bgcolor="#FFCC00"><form action="?action=save2" method="post" name="mForm">
    <td width="17%" align="center"><img src="/onlinepay/chinabank/images/chinabank2.gif"></td>
    <td width="28%">
      <strong><input name="chinabankenabled" type="checkbox" value="网银有效" <?php if($chinabankenabled)echo 'checked';?>/>
      请勾选此处开启网银在线支付功能，此外您还需要填写右侧信息 → </strong></td>
    <td width="45%">
    	<table width="96%"  border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FF0000">
      <tr>
        <td align="center" bgcolor="#FFFF00">
        	<font color=#FF0000>网银商户编号：</font> <input name="chinabankuid" type="text" class="input_sr" value="<?php echo $chinabankuid;?>" size="40"> *<br>
          <font color=#FF0000>网银安全密钥：</font> <input name="chinabankkey" type="text" class="input_sr" value="<?php echo $chinabankkey;?>" size="40"> *<br>
         </td>
      </tr>
      </table>
    </td>
    <td width="10%" align="center"><input name="Submit2" type="button" value=" 保 存 " onclick="ChkModifyChinabank(this.form)">
    </td>	
  </tr>
</form>
</table> 

 <table width="96%"  border="1" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <tr bgcolor="#FFCC00"><form action="?action=save3" method="post" name="mForm">
    <td width="17%" align="center"><img src="/onlinepay/99bill/images/99bill.gif"></td>
    <td width="28%">
      <strong>
      <input name="nnbill_0" type="checkbox"  value="快钱有效" <?php if($nnbill_0)echo 'checked';?>/>
      请勾选此处开启快钱在线支付功能，此外您还需要填写右侧信息 → </strong></td>
    <td width="45%"><table width="96%"  border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#FF0000">
      <tr>
        <td align="center" bgcolor="#FFFF00">
        	<font color=#FF0000>账号(MerchantAcctID)：</font> <input name="nnbill_1" type="text" class="input_sr" value="<?php echo $nnbill_1;?>" size="40"> *<br>
          <font color=#FF0000>人民币网关密钥：</font> <input name="nnbill_2" type="text" class="input_sr" value="<?php echo $nnbill_2;?>" size="40"> *<br>
          <font color=#FF0000>网关订单查询接口密钥：</font> <input name="nnbill_3" type="text" class="input_sr" value="<?php echo $nnbill_3;?>" size="40"> *<br>
        </td>
      </tr>
    </table></td>
    <td colspan="3" align="center"><input name="Submit2" type="button" value=" 保 存 " onclick="ChkModifyNNPay(this.form)"></td>
</tr></form>
</table> 

<br><br>

  <table width="96%"  border="1" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
    <tr>
      <td width="36%" height="25" align="center" background="images/topbg.gif"><strong>支付方式</strong></td>
      <td width="40%" align="center" background="images/topbg.gif"><strong>相关说明</strong></td>
      <td width="9%" height="25" align="center" background="images/topbg.gif"><strong>排序        </strong></td>
      <td width="15%" height="25" align="center" background="images/topbg.gif"><strong>操作</strong></td>
    </tr><?php
   $res=$conn->query('select * from mg_delivery where method=1 order by sequence',PDO::FETCH_ASSOC);
   foreach($res as $row){
     $sequence=$row['sequence'];?>
    <form method="post">
    <tr bgcolor="#FFFFFF">
      <td height="25" align="center" bgcolor="#F7F7F7"><input name="subject" type="text" class="input_sr" value=<?php echo $row['subject'];?> maxlength="10" size="40"><input type=hidden name="id" value="<?php echo $row['id'];?>"></td>
      <td height="25" align="center"><textarea name="memo" cols="38" rows="4"><?php echo $row['memo'];?></textarea></td>
      <td height="25" align="center"><input name="sequence" type="text" class="input_sr" value=<?php echo $sequence;?> size="4"></td>
      <td height="25" align="center"><input  type="button" class="input_bot" value="修改" onclick="modify_object(this.form)">&nbsp; <input  type="button" class="input_bot" value="删除" onclick="delete_object(this.form)"></td>
    </tr>
    </form><?php
   }?>
  </table>  
  <br>
  <table width="96%"  border="1" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
    <tr>
      <td width="36%" height="25" align="center" background="images/topbg.gif"><strong>支付方式</strong></td>
      <td width="40%" height="25" align="center" background="images/topbg.gif">&nbsp;</td>
      <td width="9%" height="25" align="center" background="images/topbg.gif"><strong>排序  </strong></td>
      <td width="15%" height="25" align="center" background="images/topbg.gif"><strong>操作</strong></td>
    </tr>
    <form method="post" action="?action=zhifuadd">
    <tr bgcolor="#FFFFFF">
      <td height="25" align="center" bgcolor="#F7F7F7"><input name="subject" type="text" class="input_sr" size="40"></td>
      <td align="center"><textarea name="memo" cols="38" rows="4"></textarea></td>
      <td height="25" align="center"><input name="sequence" type="text" class="input_sr" value=<?php echo $sequence+1;?> size="4"></td>
      <td height="25" align="center"><input  type="submit" class="input_bot" value="添加"></td>
    </tr>
    </form>
  </table>
  <p><br>
  </p></td>
</tr> </table>

</body>
</html><?php
db_close();?>
