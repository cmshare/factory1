<?php require('includes/dbconn.php');
CheckLogin();
OpenDB();
function GenHtmURL($id){return WEB_ROOT.'category/cat'.$id.'.htm';}
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
      <td width="65%" nowrap><img src="images/pic5.gif" width="28" height="22" align="absmiddle" /><b>您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_htmupdate.php">前台页面更新管理</a> -&gt; <font color=#FF0000>商品品牌分类静态页面更新</font></b></td><td width="35%" nowrap align="right"><a href="#" onclick="ControlUpdate(true);"><b>开始自动更新</b></a>&nbsp;|&nbsp;<a href="#" onclick="ControlUpdate(false);"><b>停止自动更新</b></a></td>
    </tr>
    </table>
  </td>
</tr>
<tr bgcolor="#f7f7f7" height=25>
<td width="65%" background="images/topbg.gif" bgcolor="#f7f7f7">&nbsp;&nbsp;<b><a href="<?php echo GenHtmURL(0);?>" target="_blank">0.&nbsp;热销品牌</a></b></td>
<td width="25%" id="state_0" align="center">&nbsp;</td>
<td width="10%" align="center" background="images/topbg.gif"><input type="button" value="更新" onclick="UpdateItem(this,'id=0')"></td>
</tr><?php
$SortIndex=0;
do_sort(0,0);

function do_sort($selec,$index){
  global $conn,$SortIndex;
  $res=$conn->query('select * from mg_category where parent = '.$selec.' and recommend>0 order by sortorder',PDO::FETCH_ASSOC);
  foreach($res as $row){
    $SortIndex++;
    if($row['sortindex']!=$SortIndex)$conn->exec('update mg_category set sortindex='.$SortIndex.' where id='.$row['id']);
    if($selec==0) echo '<tr bgcolor="#f7f7f7" height=25><td width="65%" background="images/topbg.gif" bgcolor="#f7f7f7">&nbsp;&nbsp;<b><a href="'.GenHtmURL($row['id']).'" target="_blank">'.$SortIndex.'.&nbsp;'.$row['title'].'</a></b></td><td align="center">&nbsp;</td><td width="10%" align="center" background="images/topbg.gif"><input type="button" value="更新" onclick="UpdateItem(this,\'id='.$row['id'].'\')"></td></tr>';
    else echo '<tr bgcolor="#FFFFFF" height=25 onMouseOut="mOut(this)" onMouseOver="mOvr(this)"><td><a href="'.GenHtmURL($row['id']).'" target="_blank">'.str_repeat('　',$index*2).$SortIndex.'.&nbsp;'.$row['title'].'</a></td><td align="center">&nbsp;</td><td align="center"> <input type="button" value="更新" onclick="UpdateItem(this,\'id='.$row['id'].'\')"></td></tr>';
    
    do_sort($row['id'],$index+1);
  }
}?>
</table>
<script>InitHtmlUpdate(1,"mg_htmgen.php?mode=brand","mytable");</script>
</body>
</html><?php CloseDB();?>
