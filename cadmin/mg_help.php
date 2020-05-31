<?php require('includes/dbconn.php');
CheckLogin();
db_open();?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<SCRIPT LANGUAGE="JavaScript">
function ValidateTextboxAdd(box, button){
  var buttonCtrl = document.getElementById( button );
  if ( buttonCtrl != null ){
    if (box.value == "" || box.value == box.helptext){
      buttonCtrl.disabled = true;
    }
    else{
      buttonCtrl.disabled = false;
    }
  }
}
</script> 
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing=5 bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr bgcolor="#F7F7F7"> 
   <td height="20" colspan="2" background="images/topbg.gif" bgcolor="#F7F7F7"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>帮助中心</font></b></td>
</tr><?php
function do_sort($selec,$index){
  global $maxorder;
  $res=$GLOBALS['conn']->query("select * from mg_help where parent=$selec and property=1 order by sequence");
  foreach($res as $row){
    if($selec==0){
      if($row['sequence']>$maxorder) $maxorder=$row['sequence'];?>
    <tr bgcolor="#f7f7f7" height=25>
    <td background="images/topbg.gif" bgcolor="#f7f7f7">&nbsp;&nbsp;<a href="mg_addhelp.php?id=<?php echo $row['id'];?>&action=edit"><b><?php echo $row['sequence'].'.&nbsp;'.$row['title'];?></b></a></td>
    <td width="300" align="right" background="images/topbg.gif"><strong><a href="mg_addhelp.php?id=<?php echo $row['id'];?>&action=add">添加二级栏目</a> | <a href="mg_addhelp.php?id=<?php echo $row['id'];?>&action=edit">编辑栏目</a> | <a href="mg_addhelp.php?id=<?php echo $row['id'];?>&action=delok" onClick="return confirm('您确定进行删除操作吗？')">删除栏目</a></strong></td>
</tr><?php
    }
    else{?>
    <tr bgcolor="#FFFFFF" class=a3 height=25>
    <td><?php echo str_repeat('　',$index*2);?><a href="mg_addhelp.php?id=<?php echo $row['id'];?>&action=edit"><?php echo $row['sequence'].'.&nbsp;'.$row['title'];?></a></td>
    <td width="300" align="right"><a href="mg_addhelp.php?id=<?php echo $row['id'];?>&action=add"><img src="images/pic10.gif" width="20" height="15" border="0" align="absmiddle" />添加<?php
    echo $index+2;?>级栏目</a> <img src="images/pic9.gif" width="18" height="15" align="absmiddle" /><a href="mg_addhelp.php?id=<?php echo $row['id'];?>&action=edit">编辑栏目</a> <img src="images/pic12.gif" width="20" height="15" align="absmiddle" /><a href="mg_addhelp.php?id=<?php echo $row['id'];?>&action=delok" onclick="return confirm('您确定进行删除操作吗？')">删除栏目</a></td>
    </tr><?php
    }
    do_sort($row['id'],$index+1);
  }
}
$maxorder=0;
do_sort(0,0);
?> 
</table>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing=5 bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr>
<td colspan="2" align="right" bgcolor="#FFCC00"><form name="form" method="post" action="mg_addhelp.php?action=addcat" style="margin:0px">添加一级栏目&nbsp;<input type=hidden name=classid value=0> <input type=hidden name=hide value=0>
栏目名称：<input name="title" class="input_sr" onkeyup="ValidateTextboxAdd(this, 'title1')" onpropertychange="ValidateTextboxAdd(this, 'title1')">
栏目排序：<input name="sequence" type="text" class="input_sr" value="<?php echo $maxorder+1;?>" size="3">
<input type="submit" disabled class="input_bot" id='title1' value="添 加"></form></td> 
  </tr>
</table>
</body>
</html><?php
db_close();?>
