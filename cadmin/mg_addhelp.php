<?php require('includes/dbconn.php');
CheckLogin();
OpenDB();

switch($_GET['action']){
  case 'edit': show_editor();break;
  case 'add':  show_add();break;
  case 'addcat':addcat_save();break;
  case 'addsave': addsave();break; 
  case 'editsave':editsave();break; 
  case 'delok': delok_save();break;
}


function show_editor(){
  $id=$_GET['id'];
  if(is_numeric($id) && $id>0) $row=$GLOBALS['conn']->query('select * from mg_help where id = '.$id,PDO::FETCH_ASSOC)->fetch();
  else PageReturn('参数错误');

  function do_sort($selec,$parent){
    $sql="select id,title from mg_help where parent = '$selec' order by sortorder";
    $res=$GLOBALS['conn']->query($sql,PDO::FETCH_NUM);
    foreach($res as $row){
      echo '<option value="'.$row[0].'" ';   
      if($row[0]==$parent) echo 'selected'; 
      echo '>';
      if($selec)echo str_repeat('　',$ii*2);
      echo $row[1].'</option>';
      $ii++;
      do_sort($row[0],$parent);
      $ii--;
    }
  }?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td height="20" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_help.php">帮助中心</a> -&gt; <font color=#FF0000>栏目编辑</font></b></td>
</tr>
<tr> 
  <td bgcolor="#FFFFFF"><br>
     <form method="post" action="?action=editsave" style="margin:0px" onsubmit="this.content.value=ueditor.getContent();"><input name="id" type="hidden" value="<?php echo $id;?>">
     <table width="90%"  border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr bgcolor="#FFFFFF"> 
        <td align="center"><b>标题名称</b></td><td><input name="title" type="text" size="28" value="<?php echo $row['title'];?>" style="width:250px"></td>
     </tr>
     <tr bgcolor="#FFFFFF"> 
        <td align="center"><b>所属栏目</b></td><td><select name="parent" style="width:250px"><option value="0">一级栏目</option><?php
do_sort(0,$row['parent']);
$ii=0;?></select></td>
     </tr>
     <tr bgcolor="#FFFFFF">
       <td align="center"><b>当前序号</b></td>
       <td><input name="sortorder" type="text" class="input_sr" id="sortorder" value="<?php echo $row['sortorder'];?>" size="3" /></td>
     </tr>
     <tr bgcolor="#FFFFFF">
  	<td align="center" valign="top"><b>正文内容</b></td>
        <td><input type="hidden" name="content">

     <link rel="stylesheet" href="ueditor/themes/default/css/ueditor.css">
     <script id="content" type="text/plain"><?php echo $row['content'];?></script>
     <script type="text/javascript" src="ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="ueditor/ueditor.all.js"></script>
     <script type="text/javascript">var ueditor = UE.getEditor('content',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled: true,autoFloatEnabled: true});</script>
     
        </td>
     </tr>
     <tr bgcolor="#FFFFFF">
       <td colspan="2" align="right"><input type="submit" name="Submit3" value="提 交">&nbsp; &nbsp;</td>
     </tr>
     </table></form><br>
   </td>
</tr>
</table></body></html><?php
}


function show_add(){
  $id=$_GET['id'];
  if(is_numeric($id) && $id>0) $row=$GLOBALS['conn']->query('select * from mg_help where id = '.$id,PDO::FETCH_ASSOC)->fetch();
  else PageReturn('参数错误');?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td height="20" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle">您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_help.php">帮助中心</a> -&gt; <font color="#FF0000">添加下级栏目</font></b></td>
</tr>
  <td bgcolor="#FFFFFF"><br>
     <form method="post" action="?action=addsave" onsubmit="this.content.value=ueditor.getContent();">
     <table width="90%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr bgcolor="#FFFFFF"> 
        <td align="center" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>标题名称</strong></td>
        <td><input name="title" type="text" class="input_sr" id="title" size="28" style="width:250px"></td>
     </tr>
     <tr bgcolor="#FFFFFF"> 
        <td height="25" align="center" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>所属栏目</strong></td>
        <td><input name="parent" type="hidden" value="<?php echo $id;?>"><?php echo $row['title'];?></td>
     </tr>
     <tr bgcolor="#FFFFFF">
        <td align="center" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>排列序号</strong></td>
        <td><input name="sortorder" type="text" class="input_sr" id="sortorder" value="<?php echo $row['sortorder'];?>" size="3" /></td>
     </tr>
     <tr bgcolor="#FFFFFF">
 	<td align="center" valign="top"><b>正文内容</b></td>
        <td><input type="hidden" name="content">

     <link rel="stylesheet" href="ueditor/themes/default/css/ueditor.css">
     <script id="content" type="text/plain"></script>
     <script type="text/javascript" src="ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="ueditor/ueditor.all.js"></script>
     <script type="text/javascript">var ueditor = UE.getEditor('content',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled: true,autoFloatEnabled: true});</script>
    
        </td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td colspan="2" align="right"><input name="Submit3" type="submit" class="input_bot" value="提 交">&nbsp; &nbsp;</td>
      </tr>
      </table>
      </form><br>
    </td>
  </tr>
</table>
</body></html><?php
}

function addcat_save(){
  $title=FilterText(trim($_POST['title']));
  $sortorder=$_POST['sortorder'];
  if($title && is_numeric($sortorder)){
    $sql="insert into mg_help set title='$title',sortorder=$sortorder,parent=0,property=1";
    if($GLOBALS['conn']->exec($sql)) PageReturn('添加成功！');
  }
  PageReturn('参数错误');
}

function addsave(){
  $helpTitle=FilterText(trim($_POST['title']));
  if(empty($helpTitle))PageReturn('标题为空！',-1);
  $existid=$GLOBALS['conn']->query('select id from mg_help where title=\''.$helpTitle.'\'')->fetchColumn(0);
  if($existid) PageReturn('标题名重复,请更改！',-1);
  $sortorder=$_POST['sortorder'];
  $parent=$_POST['parent'];
  if(is_numeric($sortorder) && is_numeric($parent)){
    $content=trim($_POST['content']);
    $sql="insert into mg_help set title='$helpTitle',content='$content',sortorder=$sortorder,parent=$parent,property=1";
    if($GLOBALS['conn']->exec($sql))PageReturn('添加成功！','mg_help.php');
  }
  PageReturn('参数错误！',-1);
}


function editsave(){
  $id=$_POST['id'];
  if(!is_numeric($id) || $id<=0) PageReturn('参数错误！');
  $helpTitle=FilterText(trim($_POST['title']));
  if(empty($helpTitle))PageReturn('标题为空！',-1);
  $existid=$GLOBALS['conn']->query('select id from mg_help where title=\''.$helpTitle.'\' and id<>'.$id.' and property=1')->fetchColumn(0);
  if($existid) PageReturn('标题名重复,请更改！',-1);
  $sortorder=$_POST['sortorder'];
  $parent=$_POST['parent'];
  if(is_numeric($sortorder) && is_numeric($parent)){
    $content=trim($_POST['content']);
    $sql="update mg_help set title='$helpTitle',content='$content',sortorder=$sortorder,parent=$parent where id=$id";
    if($GLOBALS['conn']->exec($sql))PageReturn('保存成功！','mg_help.php');
  }
  PageReturn('参数错误！',-1);
}

function delok_save(){
  $id=$_GET['id'];
  if(is_numeric($id) && $id>0){
    $existid=$GLOBALS['conn']->query('select id from mg_help where parent='.$id.' and property=1')->fetchColumn(0);
    if($existid) PageReturn('该栏目有子栏目，请先删除该其所有子栏目！');
    else if($GLOBALS['conn']->exec('update mg_help set property=0,parent=-1,title=null where id='.$id)) PageReturn('删除成功！');
  }
  PageReturn('参数错误！',-1);
}

CloseDB();
?>
