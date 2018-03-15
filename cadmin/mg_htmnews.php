<?php require('includes/dbconn.php');
CheckLogin();
OpenDB();
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
      <td width="65%"><img src="images/pic5.gif" width="28" height="22" align="absmiddle" /><b>您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_htmupdate.php">前台页面更新管理</a> -&gt; <font color=#FF0000>更新新闻文档</font></b></td>
      <td width="35%" align="center"><a href="#" onclick="ControlUpdate(true)"><b>开始自动更新</b></a>&nbsp;|&nbsp;<a href="#" onclick="ControlUpdate(false)"><b>停止自动更新</b></a></td>
    </tr>
    </table>
  </td>
</tr><?php
$SortIndex=0;
$res=$conn->query('select id,title,property from mg_article where property=1 or property=2 Order by addtime desc',PDO::FETCH_ASSOC);
foreach($res as $row){	
  $SortIndex++;
  echo '<tr bgcolor="#FFFFFF" height=25 onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
<td width="65%"> &nbsp; '.$SortIndex.'. &nbsp; <a href="'.WEB_ROOT.'news/news'.$row['id'].'.htm" target="_blank">'.$row['title'].'</a></td>
<td width="25%" align="center">&nbsp;</td><td width="10%" align="center"> <input type="button" value="更新" onclick="UpdateItem(this,\'id='.$row['id'].'&property='.$row['property'].'\')"></td>
</tr>';
}
echo '</table>';?>
<script>InitHtmlUpdate(8,'mg_htmgen.php?mode=news','mytable');</script>
</body>
</html><?php CloseDB();?>
