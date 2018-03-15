<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
OpenDB();?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
<!--
function ValidateTextboxAdd(box, button)
{
 var buttonCtrl = document.getElementById( button );
 if ( buttonCtrl != null )
 {
 if (box.value == "" || box.value == box.helptext)
 {
 buttonCtrl.disabled = true;
 }
 else
 {
 buttonCtrl.disabled = false;
 }
 }
}
//-->
</script> 
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing=5 bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr bgcolor="#F7F7F7"> 
  <td height="20" colspan="2" background="images/topbg.gif" bgcolor="#F7F7F7">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">    	
    <tr>
      <td width="50%"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>功能分类管理</font></b></td>
      <td align="right"><input type="button" value="应用更新输出" onclick="self.location.href='mg_htmgen.php?mode=guide_category'"></td>
    </tr>
    </table>
  </td>
</tr><?php

$SortIndex=0;

do_sort(0,0);

function do_sort($selec,$index){
  global $conn;
  $res=$conn->query('select * from mg_category where parent = '.$selec.' order by sortorder',PDO::FETCH_ASSOC);
  foreach($res as $row){
    if($selec==0){?>
<tr bgcolor="#f7f7f7" height=25>
<td background="images/topbg.gif" bgcolor="#f7f7f7">&nbsp;&nbsp;<a href="mg_addcat.php?id=<?php echo $row['id'];?>&action=edit"><b><?php
  echo $row['sortorder'].'.&nbsp;'.$row['title'];?></b></a></td>
<td width="300" align="right" background="images/topbg.gif">
  <strong><a href=mg_addcat.php?id=<?php echo $row['id'];?>&action=add>添加二级分类</a> | <a href=mg_addcat.php?id=<?php echo $row['id'];?>&action=edit>编辑分类</a> | 
  <a href="mg_addcat.php?id=<?php echo $row['id'];?>&action=delok" onClick="return confirm('您确定进行删除操作吗？')">删除分类</a></strong></td>
</tr><?php
    }
    else{?>
<tr class=a3 height=25 bgcolor="#FFFFFF" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
<td>　<?php echo str_repeat('　',$index*2);?><a href="mg_addcat.php?id=<?php echo $row['id'];?>&action=edit"><?php echo $row['sortorder'].'.&nbsp;'.$row['title'];?></a></td>
<td width="300" align="right">
<a href=mg_addcat.php?id=<?php echo $row['id'];?>&action=add><img src="images/pic10.gif" width="20" height="15" border="0" align="absmiddle" />添加
<?php echo $index+2;?>
级分类</a> <img src="images/pic9.gif" width="18" height="15" align="absmiddle" /><a href=mg_addcat.php?id=<?php echo $row['id'];?>&action=edit>编辑分类</a> <img src="images/pic12.gif" width="20" height="15" align="absmiddle" /><a href="mg_addcat.php?id=<?php echo $row['id'];?>&action=delok" onClick="return confirm('您确定进行删除操作吗？')">删除分类</a></td>
</tr><?php
    }
    do_sort($row['id'],$index+1);
  }
}?>
</table>

<form method="post" action="mg_addcat.php?action=addroot">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing=5 bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr>
  <td colspan="2" align="right" bgcolor="#FFCC00"><?php
$max_root_index=$conn->query('select max(sortorder) from mg_category where parent=0')->fetchColumn(0);?>
添加一级分类&nbsp;
<input type=hidden name=classid value=0>
<input type=hidden name=hide value=0>
分类名称：
<input name="title" class="input_sr"> 分类排序：<input name="sortorder" type="text" class="input_sr" value="<?php echo $max_root_index+1;?>" size="3">
<input type="submit" class="input_bot" value="添 加">
</td></tr>
</table>
</form>

</body>
</html><?php
CloseDB();?>
