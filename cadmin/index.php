<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>管理后台－铭悦日化用品有限公司</title>
<?php
if(@$_COOKIE['meray']['admin'] && @$_COOKIE['meray']['popedom'])
{?>
</head>
  <frameset rows="*" cols="160,*" framespacing="0" frameborder="no" border="0">
  <frame src="mg_leftnav.php" name="leftFrame" scrolling="no" noresize="noresize">
  <frame src="admincenter.php" name="mainFrame" scrolling="yes">
  </frameset>
  <noframes><body></body></noframes>
</html><?php
}
else{date_default_timezone_set("PRC");?>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #016aa9;
}
body,td,th {
	font-family: 宋体;
	font-size: 12px;
}
.login_button {
	BACKGROUND: url(images/button_bg1.gif) no-repeat;
	WIDTH: 72px;
	CURSOR: pointer;
	COLOR: #395366;
	BORDER-TOP-STYLE: none;
	PADDING-TOP: 2px;
	BORDER-RIGHT-STYLE: none;
	BORDER-LEFT-STYLE: none;
	HEIGHT: 20px;
	TEXT-ALIGN: center;
	BORDER-BOTTOM-STYLE: none;
	font-family: "宋体";
	font-size: 12px;
}
.input {
	BORDER-RIGHT: #95a1b6 1px solid;
	BORDER-TOP: #95a1b6 1px solid;
	BORDER-LEFT: #95a1b6 1px solid;
	WIDTH: 140px;
	BORDER-BOTTOM: #95a1b6 1px solid;
	font-family: "宋体";
	font-size: 12px;
	height: 20px;
}
-->
</style></head>
<body onLoad="document.all.adminuser.focus();">
<table width="917" border="0" bordercolor=red align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td  align="left" valign="top" scope="col"><table width="917" border="0" cellpadding="0" cellspacing="0" background="images/login_top.png">
      <tr>
        <td height="147">&nbsp;<iframe name="dummyframe" style="display:none;height:1px"></iframe></td>
      </tr>
    </table>
      <table width="917" border="0" cellpadding="0" cellspacing="0" background="images/2011_05.jpg" style="background-repeat:no-repeat; background-position:center top" height="171">
      <tr>
        <td height="163" align="left" valign="middle" scope="col" style="padding-left:385px;">
          <form name="admininfo" method="post" action="admlogin.php" target="dummyframe" style="margin:0px">
          <table width="350" border="0" align="left" cellpadding="0" cellspacing="0" >
              <tr>
                <td width="19%" height="25" align="right" scope="col">管理员:&nbsp;</td>
                <td width="81%" scope="col"><input  name="adminuser" AUTOCOMPLETE="off" type="text" class="input" maxlength="16"/></td>
              </tr>
              <tr>
                <td height="25" align="right" scope="col">密 &nbsp;码:&nbsp;</td>
                <td height="25" scope="col"><input  name="adminpsw" type="password" class="input" maxlength="16"/></td>
              </tr>
               <tr>
                <td height="25" align="right" scope="col">验证码:&nbsp;</td>
                <td height="25" scope="col"><input  name="authcode" type="text" class="input"  style="width:85px" maxlength="4"/>&nbsp;<img src="includes/authcode.php" height="20" width="48" align="absbottom" id="LoginCheckout" onclick="this.src='includes/authcode.php?handle='+Math.random();" title="点击刷新"></td>
              </tr>
              <tr>
                <td height="25" scope="col">&nbsp;</td>
                <td height="30" valign="bottom" scope="col"><input type="submit" class="login_button" value="登录" /></td>
              </tr>
          </table></form></td>
        </tr>
    </table></td>
  </tr>
</table>
<table width="917" border="0" align="center" cellpadding="0" cellspacing="0" background="images/2011_06.jpg" height="181" style="background-repeat:no-repeat; background-position:top">
  <tr>
    <td height="48" align="left" valign="top" scope="col"><div style="line-height:32px;padding-left:490px;">版权所有：南京铭悦日化用品有限公司</div></td>
  </tr>
  <tr>
  	<td align="center" valign="top" style="color:#888888;font-size:18px">Copyright @<?php $thisYear=date("Y");$nextYear=(int)$thisYear+1;echo "$thisYear~$nextYear";?> MERAY DAILY CHEMICALS CO., LTD.</td>
  </tr>
</table>
</body>
</html><?php 
}?>
