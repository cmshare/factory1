<?php require('includes/dbconn.php');
CheckLogin();
OpenDB();
	
$action=@$_GET['action'];
if($action){
  if($action=='newsave'){ #新添保存
    $sortorder= $_POST['sortorder'];
    $UnitName=FilterText(trim($_POST['unitname']));
    if(is_numeric($sortorder) && $UnitName){
      $conn->exec("insert into mg_units(name,sortorder) values('$UnitName',$sortorder)");
      PageReturn('保存成功！');
    }
  }
  else if($action=='modsave'){ #保存修改
    $id=$_POST['id'];
    if(is_numeric($id)&& $id>0){
      $UnitName=FilterText(trim($_POST['unitname']));
      $sortorder= $_POST['sortorder'];
      if(is_numeric($sortorder) && $UnitName){
        $conn->exec("update mg_units set name='$UnitName',sortorder=$sortorder where id=$id");
        PageReturn('修改成功！');
      }
    }
  }
  else if($action=='del'){ #删除操作
    $id=$_POST['id'];
    if(is_numeric($id)&& $id>0){
      if($conn->exec('delete from mg_units where id='.$id)) PageReturn('删除成功！');
    }
  }
  PageReturn('参数错误');
}?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<script>
function modify_object(myform){
 var unitname=myform.unitname.value.trim();
 var sortorder=myform.sortorder.value.trim();
 if(!unitname){
   alert("单位名称不能为空！");
   myform.unitname.focus();
   return false;
 }
 else if(!sortorder || isNaN(sortorder)){
   alert("序号无效！");
   myform.sortorder.focus();
   return false;
 }
 else{
   myform.action="?action=modsave";
   myform.submit();
 } 
}

function delete_object(myform){
  if(confirm("确定删除该单位？")){
    myform.action="?action=del";
    myform.submit();
  }
}
</script>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif" height="22"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>单位管理</font></b></td>
  </tr>
  <tr> 
    <td valign="top" bgcolor="#FFFFFF"> 
	
        <br>
        <table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#f2f2f2">
          
          <tr bgcolor="#FFFFFF"> 
            <td width="51%" align="right" valign="top"><table width="99%"  border="1" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
                <tr align="center" bgcolor="#F7F7F7">
                  <td height="25" colspan="3" background="images/topbg.gif"><strong>现有单位信息管理</strong></td>
                </tr>
              <tr align="center" bgcolor="#F7F7F7">
                <td width="42%" height="25" background="images/topbg.gif"><strong>名称</strong></td>
                <td width="28%" background="images/topbg.gif"><strong>序号</strong></td>
                <td width="30%" background="images/topbg.gif"><strong>操作</strong></td>
              </tr><?php
	$res=$conn->query('select * from mg_units order by sortorder',PDO::FETCH_ASSOC);
        foreach($res as $row){?>
	      <form method="post">
              <tr align="center" bgcolor="#FFFFFF">
                <td height="25"><input name="unitname" type="text" class="input_sr" value="<?php echo $row['name'];?>"><input type="hidden" name="id" value="<?php echo $row['id'];?>"/></td>
                <td><input name="sortorder" type="text" class="input_sr" value="<?php echo $row['sortorder'];?>" size="8"></td>
                <td><input type="button" class="input_bot" value="修改" onclick="modify_object(this.form)">&nbsp; <input type="button" class="input_bot" value="删除" onclick="delete_object(this.form)"></td>
              </tr></form><?php
        }?>
            </table>
            <br></td>
            <td width="49%" align="center" valign="top" bgcolor="#FFFFFF"><table width="99%"  border="1" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
             <form  method="post" action="?action=newsave">
	      <tr align="center" bgcolor="#F7F7F7">
                <td height="25" colspan="3" background="images/topbg.gif"><strong>添加单位信息</strong></td>
              </tr>
              <tr align="center" bgcolor="#F7F7F7">
                <td width="42%" height="25" background="images/topbg.gif"><strong>名称</strong></td>
                <td width="28%" background="images/topbg.gif"><strong>序号</strong></td>
                <td width="30%" background="images/topbg.gif"><strong>操作</strong></td>
              </tr>
              <tr align="center" bgcolor="#FFFFFF">
                <td height="25"><input name="unitname" type="text" class="input_sr"></td>
                <td><input name="sortorder" type="text" class="input_sr" size="8"></td>
                <td><input name="Submit" type="submit" class="input_bot" value="添加"></td>
              </tr>
			  </form>
            </table>            
			</td>
          </tr>
        </table>
        <br>
	</td>
  </tr>
</table>
</body>
</html><?php
CloseDB();?>

