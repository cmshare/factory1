<?php require("includes/dbconn.php");
CheckLogin();
db_open();
$action=@$_GET["action"];
if($action=="save"){
  $pwdold=trim($_POST["pwdold"]);
  if(empty($pwdold))  PageReturn("请输入原登录密码！");
  $pwdnew=trim($_POST["pwdnew"]);
  if(empty($pwdnew))  PageReturn("请输入新登录密码！");
  if(strlen($pwdnew)>16)  PageReturn("新登录密码长度不能超过16！");
  if($pwdnew!=trim($_POST["pwdnew2"]))PageReturn("新密码两次输入不一致！");
  $existid=$conn->query("select id from `mg_users` where username='$AdminUsername' and password=md5('$pwdold')",PDO::FETCH_NUM)->fetchColumn(); 
  if($existid){
    if($conn->exec("update `mg_users` set password=md5('$pwdnew') where username='$AdminUsername'")) PageReturn("密码修改成功，请用新密码重新登录！",-2);	
  }
  else PageReturn("原密码错误");
}	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<title>修改密码</title>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="22" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>登录密码修改</font></b></td>
  </tr>
  <tr> 
    <td valign="top" bgcolor="#FFFFFF"> <br>
	<table width="250" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <form method="post" action="?action=save" onsubmit="return FormCheck(this);">
          <tr bgcolor="#FFFFFF"> 
            <td width="100" align="right" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>用户名：</strong></td>
            <td width="150" align="center"><font color=red><?php echo $AdminUsername;?></font></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>原密码：</strong></td>
            <td align="center"><input name="pwdold" type="Password"  style="width:100%;height:100%"  class="input_sr" size="12"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>新密码：</strong></td>
            <td align="center"><input name="pwdnew" type="Password" style="width:100%;height:100%"  class="input_sr" size="12"></td>
          </tr>
          <tr bgcolor="#FFFFFF"> 
            <td align="right" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>确认密码：</strong></td>
            <td align="center"><input  name="pwdnew2" type="Password" style="width:100%;height:100%" class="input_sr"  size="12"></td>
          </tr>
          <tr bgcolor="#F7F7F7"> 
            <td colspan="2" align="center"><input name="Submit" type="submit" class="input_bot" value="提交更改"></td>
          </tr>
        </form>
      </table></td>
  </tr>
</table>

<script LANGUAGE="javascript">
<!--
function checkspace(checkstr) {
  var str = '';
  for(i = 0; i < checkstr.length; i++) {
    str = str + ' ';
  }
  return (str == checkstr);
}
function FormCheck(myform)
{ if(checkspace(myform.pwdold.value))
  {	alert("原密码不能为空！");
    myform.pwdold.focus();
	  return false;
  }
  if(checkspace(myform.pwdnew.value))
  {	alert("新密码不能为空！");
    myform.pwdnew.focus();
	  return false;
  }
  if(checkspace(myform.pwdnew2.value)) 
  {	alert("确认密码不能为空！");
    myform.pwdnew2.focus();
	  return false;
  }
  if(myform.pwdnew.value != myform.pwdnew2.value) 
  {	alert("新密码和确认密码不相同，请重新输入！");
  	myform.pwdnew.focus();
	  myform.pwdnew.value = '';
	  myform.pwdnew2.value = '';
	  return false;
  }
  if( myform.pwdold.value==myform.pwdnew.value)
  { alert("密码没有改变！");
  	return false;
  }
}
//-->
</script> 
</body>
</html>
