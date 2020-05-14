<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
OpenDB();

define('PAGE_RETURN_URL','mg_sort.php');

$action=$_GET['action'];
if($action){
  switch($action){
    case 'edit': edit_cat();break;
    case 'add':add_cat();break;
    case 'addroot':add_save(0);break; 
    case 'addok':add_save($_POST['parent']);break;
    case 'editok':edit_save();break; 
    case 'delok':del_cat();break;
  }
}

function do_sort($parentid,$selec,$index){
  global $conn;
  $sql='select * from mg_sort where parent = '.$selec.' order by sortorder';
  $res=$conn->query($sql,PDO::FETCH_ASSOC);
  foreach($res as $row){
    $selected=($row['id']==$parentid)?' selected':'';
    if($selec==0) echo '<option value="'.$row['id'].'"'.$selected.'>'.$row['title'].'</option>';
    else echo '<option value="'.$row['id'].'"'.$selected.'>'.str_repeat('　',$index*2).$row['title'].'</option>';
    do_sort($parentid,$row['id'],$index+1);
  }
}

function edit_cat(){
  global $conn;
  $id=$_GET['id'];
  if(is_numeric($id) && $id>0) $row=$conn->query('select * from mg_sort where id = '.$id,PDO::FETCH_ASSOC)->fetch();
  if(!$row)PageReturn('参数无效！');?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="20" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>编辑功能分类</font></b></td>
  </tr>
  <tr> 
    <td bgcolor="#FFFFFF"><br>
	<table width="90%"  border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        </tr>
        <form  method="post" action="?action=editok">
	<input name="id" type="hidden" value="<?php echo $id;?>">
	<tr bgcolor="#FFFFFF"> 
            <td align="center"><b>分类名称</b></td>
            <td><input name="title" type="text" id="title" size="24" value="<?php echo $row['title'];?>"></td>
          </tr>
		  <tr bgcolor="#FFFFFF"> 
            <td align="center"><b>所属分类</b></td>
            <td><select name="parent">  
<option value="0">一级分类</option><?php do_sort($row['parent'],0,0);?></select>
</td>
          </tr>
		  
		  <tr bgcolor="#FFFFFF">
		  <td align="center"><b>当前序号</b></td>
		  <td><input name="sortorder" type="text" class="input_sr" value="<?php echo $row['sortorder'];?>" size="3" /></td>
		  </tr>
		   <tr bgcolor="#FFFFFF">
		  <td></td>
		  <td><input type="submit" value="提 交"></td>
		  </tr>
        </form>
      </table><br></td>
  </tr>
</table>
</body>
</html><?php
}


function add_cat(){
  global $conn;
  $id=$_GET['id'];
  if(is_numeric($id) && $id>0)$row=$conn->query('select * from mg_sort where id = '.$id,PDO::FETCH_ASSOC)->fetch();
  if(empty($row)) PageReturn('参数错误！');
  $subindex=$conn->query('select max(sortorder) from mg_sort where parent='.$id)->fetchColumn(0);
  $subindex=($subindex)?$subindex+1:1;?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="20" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color="#FF0000">添加下级分类</font></b></td>
  </tr>
    <td bgcolor="#FFFFFF">
	<br>
        <form method="post" action="?action=addok">
	<table width="90%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
       
		<tr bgcolor="#FFFFFF"> 
            <td align="center" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>分类名称</strong></td>
            <td><input name="title" type="text" class="input_sr" id="title" size="24"></td>
          </tr>
		  <tr bgcolor="#FFFFFF"> 
            <td height="25" align="center" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>所属分类</strong></td>
            <td><input name="parent" type="hidden" value="<?php echo $id;?>">
		<?php echo $row['title'];?></td>
          </tr>
		  <tr bgcolor="#FFFFFF">
		  <td align="center" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>排列序号</strong></td>
		  <td><input name="sortorder" type="text" class="input_sr" id="sortorder" value="<?php echo $subindex;?>" size="3" /></td>
		  </tr>
		  
		   <tr bgcolor="#FFFFFF">
		  <td background="images/topbg.gif" bgcolor="#F7F7F7"></td>
		  <td><input type="submit" class="input_bot" value="提 交"></td>
		  </tr>
      </table></form>
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
  if($title && is_numeric($sortorder) && is_numeric($parentid) && $parentid>=0){
    $sql="mg_sort set title='$title',sortorder=$sortorder,parent=".$parentid;
    if($conn->exec('update '.$sql.' where parent=-1 limit 1') || $conn->exec('insert into '.$sql) )
    PageReturn('分类添加成功！',PAGE_RETURN_URL);
  }
  PageReturn('添加失败！'.$sortorder.'- '.$parentid);
}

function edit_save(){
  global $conn;
  $id=$_POST['id'];
  $title=FilterText(trim($_POST['title']));
  $sortorder=$_POST['sortorder'];
  $parent=$_POST['parent'];
  if(is_numeric($id) && $id>0 && $title && is_numeric($sortorder) && is_numeric($parent) && $parent>=0){
    $sql="update mg_sort set title='$title',sortorder=$sortorder,parent=$parent where id=$id";
    if($conn->exec($sql)) PageReturn('保存成功！',PAGE_RETURN_URL);
    else PageReturn('没有修改~！',PAGE_RETURN_URL);
  }
  else PageReturn('参数错误！');
}

   
function del_cat(){
  global $conn;
  $id=$_GET['id'];
  if(is_numeric($id) && $id>0){
    $subcount=$conn->query('select count(*) from mg_sort where parent='.$id)->fetchColumn(0);
    if($subcount)PageReturn('该分类有子分类，请先删除该分类的所有子分类！');
    else if($conn->exec('update mg_sort  set parent=-1,title=null where id='.$id))PageReturn('分类删除成功！',PAGE_RETURN_URL);
  }
}

CloseDB();?>
