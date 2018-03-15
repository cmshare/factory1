<?php require('includes/dbconn.php');
CheckLogin('SYSTEM');
OpenDB();

if(@$_GET['mode']=='save'){
  $SMTP=$_POST['SMTP'];
  $MailServerUserName=$_POST['MailServerUserName'];
  $MailServerPassword=$_POST['MailServerPassword'];
  $SendFromMail=$_POST['SendFromMail'];
  $SendFromName=$_POST['SendFromName']; 
  $sql="update mg_configs set smtp='$SMTP',mailserverusername='$MailServerUserName',mailserverpassword='$MailServerPassword',sendfrommail='$SendFromMail',sendfromname='$SendFromName'";
  if($conn->exec($sql))PageReturn('保存成功！');
  else PageReturn('没有修改');
}

$row=$conn->query('select smtp,mailserverusername,mailserverpassword,sendfrommail,sendfromname from mg_configs',PDO::FETCH_ASSOC)->fetch();
if($row){
    $SMTP=$row['smtp'];
    $MailServerUserName=$row['mailserverusername'];
    $MailServerPassword=$row['mailserverpassword'];
    $SendFromMail=$row['sendfrommail'];
    $SendFromName=$row['sendfromname'];
}?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif" height="22"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>MAIL发信设置</font></b></td>
  </tr>
  <tr> 
    <td  valign="top" bgcolor="#FFFFFF"> 
	<form name="form1" method="post" action="?mode=save">
         
        <table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#f2f2f2">
          
          <tr> 
            <td align="right" bgcolor="#FFFFFF" width="30%">邮件发送服务器:&nbsp;</td>
            <td><input type="text" name="SMTP" width="70" value="<?php echo $SMTP;?>">  &nbsp;<img src=images/memo.gif alt='smtp服务器地址（企业邮局地址）'></td>
          </tr>
          <tr> 
            <td align="right" bgcolor="#FFFFFF" width="30%">服务器验证用户:&nbsp;</td>
            <td><input type="text" name="MailServerUserName" width="70" value="<?php echo $MailServerUserName;?>">  &nbsp;<img src=images/memo.gif alt='邮局中任何一个用户的Email地址'></td>
          </tr>
          <tr> 
            <td align="right" bgcolor="#FFFFFF" width="30%">服务器验证密码:&nbsp;</td>
            <td><input type="password" name="MailServerPassword" width="70" value="<?php echo $MailServerPassword;?>">  &nbsp;<img src=images/memo.gif alt='用户Email帐号对应的密码'></td>
          </tr> 
          <tr> 
            <td align="right" bgcolor="#FFFFFF" width="30%">发件人对外邮箱:&nbsp;</td>
            <td><input type="text" name="SendFromMail" width="70" value="<?php echo $SendFromMail;?>">  &nbsp;<img src=images/memo.gif alt='收件人所见的发件人信箱'></td>
          </tr> 
          <tr> 
            <td align="right" bgcolor="#FFFFFF" width="30%">发件人对外名称:&nbsp;</td>
            <td><input type="text" name="SendFromName" width="70" value="<?php echo $SendFromName;?>">  &nbsp;<img src=images/memo.gif alt='收件人所见的发件人称呼'></td>
          </tr>  
          <tr>
          	<td colspan="2" align="center" height=35 valign="middle">
          		 <input type="submit" name="submit" value=" 保存 ">
            </td>
          </tr>                            
        </table>
        <br>
	</form></td>
  </tr>
</table>
</body>
</html><?php
CloseDB();?>
