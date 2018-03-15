<?php require('includes/dbconn.php');
CheckLogin();
OpenDB();
if(@$_GET['action']=='edit'){
  $gradetitle=trim(@$_POST['gradetitle']);
  $gradeid=@$_POST['id'];
  if(empty($gradetitle))PageReturn('请填写等级名称！');
  $conn->exec("update mg_usrgrade set title='$gradetitle' where id=$gradeid");
  PageReturn('修改成功！');
}?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<script language = "JavaScript">
function CheckAdd(myform){
  if(checkspace(myform.UserGradeTitle.value)){
    myform.UserGradeTitle.focus();
    alert("请输入会员等级名称！");
    return false;
  }
}

function checkspace(checkstr) {
  var str = '';
  for(i = 0; i < checkstr.length; i++) {
    str = str + ' ';
  }
  return (str == checkstr);
}

function regInput(obj, reg, inputStr){
  var docSel	= document.selection.createRange()
  if (docSel.parentNode.tagName != "INPUT")	return false
  oSel = docSel.duplicate()
  oSel.text = ""
  var srcRange	= obj.createTextRange()
  oSel.setEndPoint("StartToStart", srcRange)
  var str = oSel.text + inputStr + srcRange.text.substr(oSel.text.length)
  return reg.test(str)
}
</script>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="22" background="images/topbg.gif" bgcolor="#F7F7F7"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>客户等级管理</font></b></td>
  </tr>
  <tr> 
    <td bgcolor="#FFFFFF" valign="top">
 
        <table width="80%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr align="center" bgcolor="#F7F7F7" height="25"> 
          <td width="50%" height="25" background="images/topbg.gif"><strong>等级名称</strong></td>
           <td width="50%" height="25" background="images/topbg.gif"><strong>操作</strong></td>
        </tr><?php
      $res=$conn->query('select * from mg_usrgrade order by id',PDO::FETCH_ASSOC);
      foreach($res as $row){?>
        <form method="post" action="?action=edit">
        <tr bgcolor="#FFFFFF" align="center">
          <td height="25"><input type=hidden name="id" value="<?php echo $row['id'];?>"><input name="gradetitle" type="text" class="input_sr" value="<?php echo $row['title'];?>" size="20"></td>
          <td height="25"><input name="Submit" type="submit" class="input_bot" onClick="return CheckAdd(this.form);" value="修 改"></td>
        </tr>
        </form><?php
      }?>
    </table>
  <br></td>
  </tr>
</table>
</body>
</html><?php
CloseDB();?>
