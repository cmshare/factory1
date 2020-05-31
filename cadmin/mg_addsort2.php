<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
db_open();

define('PAGE_RETURN_URL','mg_sort2.php');

$action=$_GET['action'];
if($action){
  switch($action){
    case 'edit': edit_cat();break;
    case 'editok':edit_save();break; 
  }
}

function do_sort($parentid,$selec,$index){
  global $conn;
  $sql='select * from mg_sort where pid = '.$selec.' order by sequence';
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
<SCRIPT language="JavaScript" src="/include/category.js" type="text/javascript"></SCRIPT>
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
            <td><?php echo $row['title'];?></td>
          </tr>
		  <tr bgcolor="#FFFFFF"> 
            <td align="center"><b>所属分类</b></td>
            <td> 
            <script language="javascript">CreateCategorySelection("catid",<?php echo $row['catid'];?>,"--------商品分类映射--------");</script>
           </td>
          </tr>
		  
		  <tr bgcolor="#FFFFFF">
		  <td align="center"><b>商品数量</b></td>
		  <td style="color:#FF6600;font-weight:bold;"><?php echo $conn->query('select count(*) from mg_product where category='.$id)->fetchColumn(0);?></td>
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

function edit_save(){
  global $conn;
  $id=$_POST['id'];
  $catid=$_POST['catid'];
  if(is_numeric($id) && $id>0 && is_numeric($catid) && $catid>0){
    $sql="update mg_sort set catid=$catid where id=$id";
    if($conn->exec($sql)) PageReturn('保存成功！',PAGE_RETURN_URL);
    else PageReturn('没有修改~！',PAGE_RETURN_URL);
  }
  else PageReturn('参数错误！');
}

   


db_close();?>
