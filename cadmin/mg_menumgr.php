<?php require("includes/dbconn.php");
 CheckLogin();
 OpenDB();
 CheckMenu('导航菜单管理');
 if((@$mode=$_GET['mode'])){
   if($mode=='del'){
     $meuid=$_POST['menuid'];
     if(is_numeric($meuid) && $meuid>0){
       $sub_menus=$conn->query('select count(*) from mg_popedom where parent='.$meuid)->fetchColumn(0);
       if($sub_menus) PageReturn("菜单项删除失败，存在非空子菜单！");
       $conn->exec("update `mg_popedom` set parent=-1 where id=$meuid or parent=$meuid");
       PageReturn("菜单项删除成功！");
     }
   }  
   else if($mode=='update'){
     $meuID=$_POST['menuid'];
     $meuSort=$_GET['sort'];
     if(is_numeric($meuID) && is_numeric($meuSort)){
       $menu_title=FilterText(trim($_GET['title']));
       $menu_path=FilterText(trim($_POST['path']));
       $menu_remark=FilterText(trim($_POST['remark']));
       $conn->exec("update `mg_popedom` set title='$menu_title',sort=$meuSort,path='$menu_path',remark='$menu_remark' where id=$meuID");
       PageReturn("保存成功！");
     }
   }
   else if($mode=='add'){
     $parent=$_POST['menuid'];
     $menu_title=FilterText(trim($_GET['title']));
     if(is_numeric($parent) && $menu_title){
       $max_sort=$conn->query("select max(sort) from `mg_popedom` where parent=$parent")->fetchColumn(0);
       $max_sort=($max_sort)?(int)$max_sort+1:1;
       $freeid=$conn->query("select id from `mg_popedom` where parent=-1 limit 1")->fetchColumn(0); $sql="`mg_popedom` set parent=$parent,title='$menu_title',sort=$max_sort,path='',remark=''";
       $conn->exec(($freeid)?"update $sql where id=$freeid":"insert into $sql");
       PageReturn('菜单栏目添加成功！');
     }
   }      
   else if($mode=='changeparent'){
     $menuid=$_POST['menuid'];
     $parent=$_GET['parent'];
     if(is_numeric($menuid) && is_numeric($parent)){
       $conn->exec("update `mg_popedom` set parent=$parent where id=$menuid");
       PageReturn('菜单栏目移动操作成功！');
     }
   }
   PageReturn('参数错误！');	 
 }  	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>导航菜单管理</title>
</head>
<body topmargin="0" leftmargin="0">
<script>
function DeleteMenu(myForm){
  if(confirm("确定要删除该菜单及其子项？")){
    myForm.action="?mode=del";
    myForm.submit();
  }
}
function AddSubMenu(myForm){
  var newValue=window.prompt("请输入新增类目的标题：\n\n", "");
  if(newValue && newValue.length>0){
    myForm.action="?mode=add&title="+newValue;
    myForm.submit();
  }
}

function UpdateMenu(myForm){
  var meuTitle=myForm.title.value;
  var splitOffset=meuTitle.indexOf(".");
  var meuSort=0;
  if(splitOffset>=0){
    meuSort=meuTitle.substring(0,splitOffset);
    meuTitle=meuTitle.substring(splitOffset+1,meuTitle.length);
  }
  myForm.action="?mode=update&sort="+meuSort+"&title="+meuTitle;
  myForm.submit();
}

function MoveMenu(myForm){
  var onSelectParent=function(parentmenu){
      if(parentmenu){
        myForm.action="?mode=changeparent&parent="+parentmenu;
        myForm.submit();
      }else alert("请选择父级菜单！");
  }
  var html='<form style="margin:0px"><table border=0 width="100%" height="100%"><tr><td width="width:100%"><select size="6" name="parentlist" style="width:100%;height:100%"><?php
  $res=$conn->query('select id,sort,title from mg_popedom where parent=0 order by sort asc',PDO::FETCH_NUM);
  foreach($res as $row)echo '<option value="'.$row[0].'">'.$row[1].'. '.$row[2].'</option>';?></select></td></tr><tr><td height="25" align="center"><input type="button" value="确定" onclick="self.closeDialog(this.form.parentlist.value);"> &nbsp <input type="button" value="取消" onclick="self.closeDialog();"></td></tr></table></form>';
  AsyncDialog("选择父级菜单",html,150,200,onSelectParent);
}

</script>	
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif" height="20">
    <table width="99%" border=0 cellpadding=0 cellspacing=0>
    <tr>
    	<td width="50%">
  	    <b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>导航菜单管理</font></b>
      </td><form method=post>
      <td width="50%" align="right">
        <input type="hidden" name="menuid" value="0">
        <input type="button" value="添加栏目" onclick="AddSubMenu(this.form)">
      </td></form>	
    </tr>
    </table>  
    </td>
  </tr>
  <tr> 
    <td  valign="top" bgcolor="#FFFFFF">
  	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
    <tr>
    	<td background="images/topbg.gif" align="center"><strong>标题</strong></td>
    	<td background="images/topbg.gif" align="center"><strong>备注</strong></td>
    	<td background="images/topbg.gif" align="center"><strong>页面</strong></td>
    	<td background="images/topbg.gif" align="center"><strong>操作</strong></td>
    	<td background="images/topbg.gif" align="center"><strong>ID</strong></td>
    </tr><?php
    $res=$conn->query("select * from `mg_popedom` where parent=0 order by sort",PDO::FETCH_ASSOC);
    foreach($res as $menu_root)
    {?>
    <form method=post>	
    <tr bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
      <td width="20%">&nbsp;&nbsp;<input type="text" name="title" value="<?php echo $menu_root["sort"].". ".$menu_root["title"];?>" style="font-weight:bold;" class="input_text" >
      <td width="18%" align="center"><input type="text" name="remark" class="input_text" value="<?php echo $menu_root["remark"];?>"></td> 
      <td width="30%" align="center"><input type="text" name="path" class="input_text" value="<?php echo $menu_root["path"];?>"></td>
      <td width="30%" align="center"><input type="hidden" name="menuid" value="<?php echo $menu_root["id"];?>"><input type="button" value="保存" onclick="UpdateMenu(this.form)"><input type="button" value="删除" onclick="DeleteMenu(this.form)">  <input type="button" value="添加菜单" onclick="AddSubMenu(this.form)"></td>	
      <td width="2%" align="center"><?php echo $menu_root["id"];?></td>
    </tr></form><?php
    $res_sub=$conn->query("select * from `mg_popedom` where parent={$menu_root['id']} order by sort",PDO::FETCH_ASSOC);
    foreach($res_sub as $menu_sub)
    {?>
    <form method=post>
    <tr bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
      <td width="20%">&nbsp;&nbsp;<input type="text" name="title" value="<?php echo $menu_sub["sort"].". ".$menu_sub["title"];?>" style="padding-left:20px;" class="input_text"></td> 
      <td width="18%" align="center"><input type="text" name="remark" class="input_text" value="<?php echo $menu_sub["remark"];?>"></td> 
      <td width="30%" align="center"><input type="text" name="path" class="input_text" value="<?php if($menu_sub["path"]) echo $menu_sub["path"];?>"></td>
      <td width="30%" align="center"><input type="hidden" name="menuid" value="<?php echo $menu_sub["id"];?>"><input type="button" value="保存" onclick="UpdateMenu(this.form)"><input type="button" value="删除" onclick="DeleteMenu(this.form)">  <input type="button" value="移动菜单" onclick="MoveMenu(this.form)"></td>	
      <td width="2%" align="center"><?php echo $menu_sub["id"];?></td>
    </tr></form><?php
    } 
  }
  CloseDB();?>
    </table>       
    </td>
</tr>
</table> 
</body>
</html>
