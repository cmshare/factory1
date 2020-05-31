<?php require('includes/dbconn.php');
CheckLogin();
db_open();
	
$action=@$_GET['action'];
if($action){
  if($action=='newsave'){ #新添保存
    $sequence= $_POST['sequence'];
    $materialname=FilterText(trim($_POST['materialname']));
    if(is_numeric($sequence) && $materialname){
      $conn->exec("insert into mg_material(name,sequence) values('$materialname',$sequence)");
      PageReturn('保存成功！');
    }
  }
  else if($action=='modsave'){ #保存修改
    $id=$_POST['id'];
    echo $id;
    if(is_numeric($id)&& $id>0){
      $materialname=FilterText(trim($_POST['materialname']));
      $sequence= $_POST['sequence'];
      if(is_numeric($sequence) && $materialname){
        $conn->exec("update mg_material set name='$materialname',sequence=$sequence where id=$id");
        PageReturn('修改成功！');
      }
    }
  }
  else if($action=='del'){ #删除操作
    $id=$_POST['id'];
    if(is_numeric($id)&& $id>0){
      if($conn->exec('delete from mg_material where id='.$id)) PageReturn('删除成功！');
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
 var materialname=myform.materialname.value.trim();
 var sequence=myform.sequence.value.trim();
 if(!materialname){
   alert("名称不能为空！");
   myform.materialname.focus();
   return false;
 }
 else if(!sequence || isNaN(sequence)){
   alert("序号无效！");
   myform.sequence.focus();
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
    <td background="images/topbg.gif" height=22><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>规格商品</font></b></td>
  </tr>
  <tr> 
    <td valign="top" bgcolor="#FFFFFF"> 
	
        <br>
        <table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#f2f2f2">
          
          <tr bgcolor="#FFFFFF"> 
            <td width="51%" align="right" valign="top"><table width="99%"  border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
                <tr align="center" bgcolor="#F7F7F7">
                  <td height="25" colspan="3" background="images/topbg.gif"><strong>现有规格信息管理</strong></td>
                </tr>
              <tr align="center" bgcolor="#F7F7F7">
                <td width="42%" height="25" background="images/topbg.gif"><strong>名称</strong></td>
                <td width="28%" background="images/topbg.gif"><strong>序号</strong></td>
                <td width="30%" background="images/topbg.gif"><strong>操作</strong></td>
              </tr><?php
	$res=$conn->query('select * from mg_material order by sequence',PDO::FETCH_ASSOC);
        foreach($res as $row){?>
	     <form method="post">
              <tr align="center" bgcolor="#FFFFFF">
                <td height="25"><input name="materialname" type="text" class="input_sr" value="<?php echo $row['name'];?>"><input type="hidden" name="id" value="<?php echo $row['id'];?>"/></td>
                <td><input name="sequence" type="text" class="input_sr" value="<?php echo $row['sequence'];?>" size="8"></td>
                <td><input type="button" class="input_bot" value="修改" onclick="modify_object(this.form)"> &nbsp;<input type="button" class="input_bot" value="删除" onclick="delete_object(this.form)"></td>
              </tr>
	      </form><?php
        }?>
            </table>
            </td>
            <td width="49%" align="center" valign="top" bgcolor="#FFFFFF"><table width="99%"  border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
	<form method="post" action="?action=newsave">
              <tr align="center" bgcolor="#F7F7F7">
                <td height="25" colspan="3" background="images/topbg.gif"><strong>添加规格信息</strong></td>
              </tr>
              <tr align="center" bgcolor="#F7F7F7">
                <td width="42%" height="25" background="images/topbg.gif"><strong>名称</strong></td>
                <td width="28%" background="images/topbg.gif"><strong>序号</strong></td>
                <td width="30%" background="images/topbg.gif"><strong>操作</strong></td>
              </tr>
              <tr align="center" bgcolor="#FFFFFF">
                <td height="25"><input name="materialname" type="text" class="input_sr"></td>
                <td><input name="sequence" type="text" class="input_sr" size="8"></td>
                <td><input type="submit" class="input_bot" value="添加"></td>
              </tr></form>
            </table>
			
            </td>
          </tr>
        </table>
    <br>	</td>
  </tr>
</table>
</body>
</html><?php
db_close();?>
