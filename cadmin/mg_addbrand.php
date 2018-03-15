<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
OpenDB();

define('PAGE_RETURN_URL','mg_brand.php');

$action=$_GET['action'];

switch($action){
 case 'edit': edit_brand();break;
 case 'add': add_brand();break;
 case 'addroot':add_save(0);break; 
 case 'addok':add_save($_POST['parent']);break; 
 case 'editok':edit_save();break;  
 case 'delok':del_brand();break;
}

function do_sort($parentid,$selec,$index){
  $res=$GLOBALS['conn']->query('select * from mg_brand where parent = '.$selec.' order by sortorder',PDO::FETCH_ASSOC);
  foreach($res as $row){
    $selected=($row['id']==$parentid)?' selected':''; 
    if($selec==0) echo '<option value="'.$row['id'].'"'.$selected.'>'.$row['title'].'</option>';
    else echo '<option value="'.$row['id'].'"'.$selected.'>'.str_repeat('　',$index*2).$row['title'].'</option>';
    do_sort($parentid,$row['id'],$index+1);
  }
}

function edit_brand(){
  global $conn;
  $brandid=$_GET['brandid'];
  if(!is_numeric($brandid)||$brandid<=0)PageReturn('参数无效~！',PAGE_RETURN_URL);
  $row=$conn->query('select * from mg_brand where id = '.$brandid,PDO::FETCH_ASSOC)->fetch();?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
<td height="20" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_brand.php">品牌分类管理</a> -&gt; <font color=#FF0000>编辑品牌分类</font></b></td>
</tr>
<tr> 
<td bgcolor="#FFFFFF"><br>
<table width="90%"  border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
</tr>
<form  method="post" action="?action=editok">
<tr bgcolor="#FFFFFF"> 
<td align="center"><b>所属父类</b></td>
<td><select name="parent">  
<option value="0">一级分类</option><?php
do_sort($row['parent'],0,0);?></select> 
</td>
</tr>
<tr bgcolor="#FFFFFF"> 
  <td align="center"><b>分类名称</b></td>
  <td><input name="title" type="text" size="24" value="<?php echo $row['title'];?>" maxlength="20"></td>
</tr>
<tr bgcolor="#FFFFFF">
  <td align="center"><b>名称属性</b></td>
  <td><select name="isbrand"><option value="0" <?php if($row['isbrand']==0) echo 'selected';?>>分类名称</option><option value="1" <?php if($row['isbrand']) echo 'selected';?>>品牌名称</option></select></td>
</tr>
<tr bgcolor="#FFFFFF">
  <td align="center"><b>显示方式</b></td>
  <td><select name="recommend" style="color:#FF0000"><?php
  if($row['recommend']>1) echo '<option value="'.$row['recommend'].'" selected >热销分类，不允许隐藏</option>';
  else if($row['recommend']==1) echo '<option value="0">隐藏</option><option value="1" selected >显示</option>';
  else echo '<option value="0" selected >隐藏</option><option value="1">显示</option>';?></select></td>
</tr>
<tr bgcolor="#FFFFFF">
   <td align="center"><b>当前序号</b></td>
   <td><input name="sortorder" type="text" class="input_sr" id="sortorder" value="<?php echo $row['sortorder'];?>" size="3" /></td>
</tr>
<tr bgcolor="#FFFFFF">
  <td align="center"><b>分类描述</b></td>
  <td><textarea name="description"><?php if($row['description'])echo $row['description'];?></textarea></td>
 </tr> 
<tr bgcolor="#FFFFFF">
  <td>&nbsp;</td><td><input name="brandid" type="hidden" value="<?php echo $brandid;?>"><input type="submit" name="Submit3" value="提 交"></td>
</tr>
</form>
</table><br></td>
</tr>
</table>
</body>
</html><?php
}

function add_brand(){
  global $conn;
  $brandid=$_GET['brandid'];
  if(!is_numeric($brandid)||$brandid<=0)PageReturn('参数无效~！',PAGE_RETURN_URL);
  $row=$conn->query('select * from mg_brand where id = '.$brandid,PDO::FETCH_ASSOC)->fetch();?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="20" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a>-&gt; <a href="mg_brand.php">品牌分类管理</a> -&gt; <font color="#FF0000">添加下级分类</font></b></td>
  </tr>
    <td bgcolor="#FFFFFF">
	<br>
	<table width="90%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
  <form method="post" action="?action=addok">
  <tr bgcolor="#FFFFFF"> 
     <td height="25" align="center" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>所属父类</strong></td>
     <td><input name="parent" type="hidden" value="<?php echo $brandid;?>"><b><?php echo $row['title'];?></b></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
     <td align="center" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>分类名称</strong></td>
     <td><input type="text" name="title" id="title" size="24" maxlength="20"> <font color=#FF0000>＊</font>
     	</td>
  </tr>   
  <tr bgcolor="#FFFFFF">
		  <td align="center" background="images/topbg.gif" bgcolor="#F7F7F7"><b>名称属性</b></td>
		  <td><select name="isbrand"><option value="">．．．</option><option value="0">分类名称</option>
            <option value="1">品牌名称</option></select> <font color=#FF0000>＊</font></td>
	</tr> 
	<tr bgcolor="#FFFFFF">
		  <td align="center" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>显示方式</strong></td>
		  <td><select name="recommend" style="color:#FF0000">
		    	  <option value="0">隐藏</option>"
		    	  <option value="1" selected >显示</option>"
           </select>
       </td>
	</tr>
		  
		  <tr bgcolor="#FFFFFF">
		  <td align="center" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>排列序号</strong></td>
		  <td><input name="sortorder" type="text" class="input_sr" id="sortorder" value="<?php echo $row['sortorder'];?>" size="3" /></td>
		  </tr>
		  
	<tr bgcolor="#FFFFFF">
		  <td align="center" valign="top" background="images/topbg.gif" bgcolor="#F7F7F7"><br><strong>分类描述</strong></td>
		  <td><textarea name="description"></textarea></td>
		  </tr>
		  	  
		          
          
          
		   <tr bgcolor="#FFFFFF">
		  <td background="images/topbg.gif" bgcolor="#F7F7F7">&nbsp;</td>
		  <td><input name="Submit3" type="submit" value=" 提 交 "></td>
		  </tr>
        </form>
      </table>
	<br></td>
  </tr>
</table>
</body>
</html><?php
}

function add_save($parentid){
  global $conn;
  $title=FilterText(trim($_POST['title']));
  $sortorder=$_POST['sortorder'];
  $description=FilterText(trim($_POST['description']));
  $isbrand=$_POST['isbrand'];
  $recommend=$_POST['recommend'];
  if(!is_numeric($isbrand))$isbrand=0;
  if(!is_numeric($recommend))$recommend=1;
  if($title && is_numeric($sortorder) && is_numeric($parentid) && $parentid>=0){
    $sql="mg_brand set title='$title',sortorder=$sortorder,parent=$parentid,recommend=$recommend,isbrand=$isbrand,shared=0,description='$description'";
    if($conn->exec('update '.$sql.' where parent=-1 limit 1') || $conn->exec('insert into '.$sql)) PageReturn('分类添加成功！',PAGE_RETURN_URL);
  }
  PageReturn('添加失败！'.$sql);
}
 	
 
function edit_save(){ 
  global $conn;
  $brandid=$_POST['brandid'];
  $title=FilterText(trim($_POST['title']));
  $isbrand=$_POST['isbrand'];
  $sortorder=$_POST['sortorder'];
  $parent=$_POST['parent'];
  $recommend=$_POST['recommend'];
  $description=FilterText(trim($_POST['description']));
  if($title && is_numeric($isbrand) && is_numeric($sortorder) && is_numeric($parent) && is_numeric($brandid) && $brandid>0 && is_numeric($recommend)){
    if($brandid==$parent) PageReturn('父类不能指向自己！');
    $sql="update mg_brand set title='$title',sortorder=$sortorder,parent=$parent,recommend=$recommend,isbrand=$isbrand,description='$description' where id=$brandid";
    if($conn->exec($sql)) PageReturn('保存成功！',PAGE_RETURN_URL);
    else PageReturn('没有修改~！',PAGE_RETURN_URL);
  }
  else PageReturn('参数错误！');
}

function del_brand(){
  global $conn;
  $brandid=$_GET['brandid'];
  if(is_numeric($brandid) && $brandid>0){
    $subcount=$conn->query('select count(*) from mg_brand where parent='.$brandid)->fetchColumn(0);
    if($subcount) PageReturn('该分类有子分类，请先删除该分类的所有子分类！');
    else if($conn->exec('update mg_brand set parent=-1,title=null where id='.$brandid)) PageReturn('分类删除成功！',PAGE_RETURN_URL);
  }
}

CloseDB();?>
