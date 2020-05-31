<?php require('includes/dbconn.php');
CheckLogin();
db_open();?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<script language="javascript">
function CopyText(obj) {
  ie = (document.all)? true:false
  if (ie){
    var rng = document.body.createTextRange();
    rng.moveToElementText(obj);
    rng.scrollIntoView();
    rng.select();
    rng.execCommand("Copy");
    rng.collapse(false);
  }
}
</script>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif" height="22"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>会员邮件列表</font></b></td>
  </tr>
  <tr> 
    <td valign="top" bgcolor="#FFFFFF"> 
	<form name="form1" method="post" action="">
        <br>
        <table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#f2f2f2">
          
          <tr bgcolor="#FFFFFF"> 
            <td align="center" valign="top" bgcolor="#FFFFFF">本列表主要用于收集站内所有注册会员EMAIL地址，直接COPY即可使用。<br>
			<textarea name="tbURL" cols="80" rows="10" id="tbURL"><?php
                        $tempStr='';
                        $res=$conn->query('select usermail from mg_users',PDO::FETCH_ASSOC);
                        foreach($res as $row){
			  if($row['usermail']) $tempStr.=$row['usermail'].',';
                        }
                        echo $tempStr;?></textarea>            
            <br>
              <table width="76%"  border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="30" align="right"><input name="Submit" type="button" class="input_bot" onClick="CopyText(document.all.tbURL)" value="复制以上所有邮件地址到剪贴板" ></td>
                </tr>
              </table></td>
          </tr>
        </table>
        <br>
	</form></td>
  </tr>
</table>
</body>
</html><?php
db_close();?>
