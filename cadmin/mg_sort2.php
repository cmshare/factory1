<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
db_open();?>
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
  <td height="20" colspan="3" background="images/topbg.gif" bgcolor="#F7F7F7">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">    	
    <tr>
      <td width="50%"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>功能分类映射管理</font></b></td>
      <td align="right"></td>
    </tr>
    </table>
  </td>
</tr><?php

$SortIndex=0;

do_sort(0,0);

function do_sort($selec,$index){
  global $conn;
  $res=$conn->query('select a.*,b.title as catname from mg_sort as a  left join mg_category as b on a.catid=b.id where a.pid = '.$selec.' order by a.sequence',PDO::FETCH_ASSOC);
  foreach($res as $row){
    if($selec==0){?>
<tr bgcolor="#f7f7f7" height=25>
<td background="images/topbg.gif" bgcolor="#f7f7f7">&nbsp;&nbsp;<a href="mg_addsort2.php?id=<?php echo $row['id'];?>&action=edit"><b><?php
  echo $row['sequence'].'.&nbsp;'.$row['title'];?></b></a></td>
  <td align="center">[<?php echo $row['catid'];?>]<?php echo $row['catname'];?></td>

<td width="300" align="right" background="images/topbg.gif">
  <img src="images/pic9.gif" width="18" height="15" align="absmiddle" /><a href=mg_addsort2.php?id=<?php echo $row['id'];?>&action=edit>编辑分类</a> 
</td>
</tr><?php
    }
    else{?>
<tr class=a3 height=25 bgcolor="#FFFFFF" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
<td>　<?php echo str_repeat('　',$index*2);?><a href="mg_addsort2.php?id=<?php echo $row['id'];?>&action=edit"><?php echo $row['sequence'].'.&nbsp;'.$row['title'];?></a></td>
<td align="center">[<?php echo $row['catid'];?>]<?php echo $row['catname'];?></td>

<td width="300" align="right">
 <img src="images/pic9.gif" width="18" height="15" align="absmiddle" /><a href=mg_addsort2.php?id=<?php echo $row['id'];?>&action=edit>编辑分类</a></td>
</tr><?php
    }
    do_sort($row['id'],$index+1);
  }
}?>
</table>



</body>
</html><?php
db_close();?>
