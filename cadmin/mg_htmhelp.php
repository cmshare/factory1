﻿<?php require('includes/dbconn.php');
CheckLogin();
db_open();
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript" src="includes/mg_htmupdate.js" type="text/javascript"></SCRIPT>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing=5 bordercolor="#CCCCCC" bgcolor="#FFFFFF" id="mytable">
<tr bgcolor="#F7F7F7"> 
  <td height="20" colspan="3" width="100%" background="images/topbg.gif" bgcolor="#F7F7F7">
    <table border=0 width="100%">
    <tr>
      <td width="65%"><img src="images/pic5.gif" width="28" height="22" align="absmiddle" /><b>您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_htmupdate.php">前台页面更新管理</a> -&gt; <font color=#FF0000>更新帮助文档</font></b></td>
      <td width="35%" align="center"><a href="#" onclick="ControlUpdate(true)"><b>开始自动更新</b></a>&nbsp;|&nbsp;<a href="#" onclick="ControlUpdate(false)"><b>停止自动更新</b></a></td>
    </tr>
    </table>
  </td>
</tr><?php
$SortIndex=0;

do_sort(0,0);

function do_sort($selec,$index){
  global $conn,$SortIndex;
  $res=$conn->query('select id,title from mg_help where parent='.$selec.' and property>0 order by sequence',PDO::FETCH_ASSOC);
  foreach($res as $row){
    $SortIndex++;
    echo '<tr bgcolor="#FFFFFF" height=25 onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
    <td width="65%">'.str_repeat('　',$index*2).$SortIndex.'.&nbsp;<a href="'.WEB_ROOT.'help/help'.$row['id'].'.htm" target="_blank">'.$row['title'].'</a></td>
    <td width="25%" align="center">&nbsp</td><td width="10%" align="center"><input type="button" value="更新" onclick="UpdateItem(this,\'id='.$row['id'].'\')"></td></tr>';
    do_sort($row['id'],$index+1);
  }
}?>
</table>
<script>InitHtmlUpdate(8,"mg_htmgen.php?mode=help","mytable");
</script>
</body>
</html><?php db_close();?>
