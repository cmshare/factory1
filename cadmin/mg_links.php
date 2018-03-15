<?php require('includes/dbconn.php');
CheckLogin('SYSTEM');
OpenDB();
$mode=@$_GET['mode'];
if($mode){
  switch($mode){
    case 'modify': modify_save();break;
    case 'add': add_save();break;
    case 'delete':delete_save();break;
  }
}

function modify_save(){
  $linkid=$_POST['linkid'];
  $property=$_POST['property'];
  $linkorder=$_POST['linkorder'];
  if(is_numeric($linkid) && $linkid>0 && is_numeric($property) && $property>0 && is_numeric($linkorder)){
    $linkname=FilterText(trim($_POST['linkname']));
    $linktitle=FilterText(trim($_POST['linktitle']));
    $linkpicture=FilterText(trim($_POST['linkpicture']));
    $linkurl=FilterText(trim($_POST['linkurl']));
    $sql="update mg_links set linkname='$linkname',linktitle='$linktitle',linkpicture='$linkpicture',linkurl='$linkurl',linkorder=$linkorder,property=$property where id=$linkid";
    if($GLOBALS['conn']->exec($sql)) PageReturn('修改成功！');
  }
  PageReturn('参数错误！');
}

function add_save(){
  global $conn;
  $property=$_POST['property'];
  $linkorder=$_POST['linkorder'];
  if(is_numeric($property) && $property>0 && is_numeric($linkorder)){
    $linkname=FilterText(trim($_POST['linkname']));
    $linktitle=FilterText(trim($_POST['linktitle']));
    $linkpicture=FilterText(trim($_POST['linkpicture']));
    $linkurl=FilterText(trim($_POST['linkurl']));
    $sql="mg_links set linkname='$linkname',linktitle='$linktitle',linkpicture='$linkpicture',linkurl='$linkurl',linkorder=$linkorder,property=$property";
    if($conn->exec('upate '.$sql.' where property=0 limit 1')||$conn->exec('insert into '.$sql )) PageReturn('添加成功！');
  }
  PageReturn('参数错误！');
}

function delete_save(){
  $linkid=$_POST['linkid'];
  if(is_numeric($linkid) && $linkid>0){
    if($GLOBALS['conn']->exec('update mg_links set property=0 where id='.$linkid)) PageReturn('删除成功！');
  }
  PageReturn('参数错误！');
}

$LinkTypes=array('所有链接','友情链接','首页幻灯','其他链接');
$MaxLinkTypes=count($LinkTypes);

$property=@$_GET['property'];
if(!is_numeric($property)) $property=0;?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<style type="text/css">
<!--
.input_text{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #000000;
	text-decoration: none;
	font-size: 12px;
	width:100%;
	text-align:center;
	border: 0px solid #CCCCCC;
	background-color:transparent
}
-->
</style>
<script>
function DeleteLink(myForm)
{ if(confirm("确定要删除该链接吗？"))
  { myForm.action="?mode=delete";
    myForm.submit();
  }  
}
	
function AddNewLink(myForm)
{  var linkproperty=myForm.property.value.trim();
   if(isNaN(linkproperty)) linkproperty=0;
   else linkproperty=parseInt(linkproperty);
   if(linkproperty<=0)
   { alert("链接类别设置错误！");
     return false;
   }
   else if(myForm.linkname.value.trim()=="" && myForm.linkpicture.value.trim()=="")
   { alert("链接信息不完整！");
     return false;
   }
   myForm.action="?mode=add";
   myForm.submit();
}
	
function ModifyLinkInfo(myForm)
{ var linkproperty=myForm.property.value.trim();
  if(isNaN(linkproperty)) linkproperty=0;
  else linkproperty=parseInt(linkproperty);
  if(linkproperty<=0)
  { alert("链接类别设置错误！");
    return false;
  }
  else if(myForm.linkname.value.trim()=="" &&  myForm.linkpicture.value.trim()=="")
  { alert("链接信息不完整！");
    return false;
  }
  myForm.action="?mode=modify";
  myForm.submit();
}
</script>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td background="images/topbg.gif" height=22>
  	<table width="100%" border="0" cellpadding="0" cellspacing="0">
  	<tr><td width="65%" nowrap><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>友情链接管理</font></b></td>
  		  <td width="35%" nowrap align="right"><select onchange="self.location.href='?property='+this.value;"><?php
  for($i=1;$i<$MaxLinkTypes;$i++){
    if($i==$property) echo '<option value="'.$i.'" selected >'.$LinkTypes[$i].'</option>';
    else echo '<option value="'.$i.'">'.$LinkTypes[$i].'</option>';
  }?></select></td></tr></table></td>
</tr>
<tr> 
  <td valign="top" bgcolor="#FFFFFF">
  	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr height="20" bgcolor="#F7F7F7">
       <td WIDTH="10%" height="25" align="center" background="images/topbg.gif"><strong>序号</strong></td>
       <td WIDTH="15%" height="25" align="center" background="images/topbg.gif"><strong>链接名称</strong></td>
       <td WIDTH="15%" height="25" align="center" background="images/topbg.gif"><strong>链接标题</strong></td>
       <td WIDTH="15%" height="25" align="center" background="images/topbg.gif"><strong>链接图片</strong></td>
       <td WIDTH="15%" height="25" align="center" background="images/topbg.gif"><strong>链接地址</strong></td>
       <td WIDTH="10%" height="25" align="center" background="images/topbg.gif"><strong>类别</strong></td>
       <td WIDTH="20%" height="25" align="center" background="images/topbg.gif"><strong>操作</strong></td>
     </tr><?php
      $maxorder=0;
      if($property>0)$sql="select * from mg_links where property=$property order by linkorder";
      else $sql="select * from mg_links where property>0 order by linkorder";
      $res=$conn->query($sql,PDO::FETCH_ASSOC); 
      foreach($res as $row){?>
        <form method=post>
     <tr align="center" bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
       <td height="25"><input name="linkorder" value="<?php echo $row['linkorder'];?>" maxlength="3" onMouseOver="this.focus()"  onFocus="this.select()" onkeyup="if(isNaN(value))execCommand('undo')" class="input_text"></td>
       <td height="25"><input name="linkname" type="text" value="<?php echo $row['linkname'];?>" class="input_text"></td>
       <td height="25"><input name="linktitle" type="text" value="<?php echo $row['linktitle'];?>" class="input_text"></td>
       <td height="25"><input name="linkpicture" type="text" value="<?php echo $row['linkpicture'];?>" class="input_text"></td>
       <td height="25"><input name="linkurl" type="text"  value="<?php echo $row['linkurl'];?>" class="input_text"></td>
       <td height="25"><select name="property"><?php
         for($i=1;$i<$MaxLinkTypes;$i++){
           if($i==$row['property']) echo '<option value="'.$i.'" selected >'.$LinkTypes[$i].'</option>';
           else echo '<option value="'.$i.'">'.$LinkTypes[$i].'</option>';
         }?></select></td>  
       <td height="25"><input type="button" value="修改" onclick="ModifyLinkInfo(this.form)"> &nbsp; <input type="button" value="删除" onclick="DeleteLink(this.form)"><input name="linkid" type="hidden" value="<?php echo $row['id'];?>"></td>
     </tr></form><?php
       if($row['linkorder']>$maxorder)$maxorder=$row['linkorder'];
      }?>
     <form method=post>
     <tr align="center" bgcolor="#FFFFFF" height="20" onMouseOut=mOut(this,"#FFFFFF") onMouseOver=mOvr(this,MENU_HOTTRACK_COLOR)> 
        <td height="25"><input name="linkorder" value="<?php echo $maxorder?>" maxlength="3" onMouseOver="this.focus()"  onFocus="this.select()" onkeyup="if(isNaN(value))execCommand('undo')" class="input_text"></td>
        <td height="25"><input name="linkname"  onMouseOver="this.focus()"  onFocus="this.select()"  type="text" class="input_text"></td>
        <td height="25"><input name="linktitle"  onMouseOver="this.focus()"  onFocus="this.select()"  type="text" class="input_text"></td>
        <td height="25"><input name="linkpicture"  onMouseOver="this.focus()"  onFocus="this.select()"  type="text" class="input_text"></td>
        <td height="25"><input name="linkurl"  type="text" onMouseOver="this.focus()"  onFocus="this.select()"  class="input_text" ></td>
        <td height="25"><input name="property"  type="text" onMouseOver="this.focus()"  onFocus="this.select()"  class="input_text" onkeyup="if(isNaN(value))execCommand('undo');" value="<?php if($property>0) echo $property;echo '1';?>"></td>
        <td height="25"><input type="button" value="添加新链接" onclick="AddNewLink(this.form)"></td>
      </tr></form>		 
     </table>

  </td>
</tr>
</table>
</body>
</html><?php
CloseDB();?>
