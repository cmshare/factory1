<?php require('includes/dbconn.php');
CheckLogin('SYSTEM');
db_open();
	
$action=@$_GET['action'];

if($action){
  switch($action){
    case 'deliverysave':deliverysave();break;
    case 'deliveryadd':deliveryadd();break;
    case 'deliverydel':deliverydel();break;
  }
}

function deliverysave(){
  $id=$_POST['id'];
  if(is_numeric($id) && $id>0){
    $subject=FilterText(trim($_POST['subject']));
    $memo=FilterText(trim($_POST['memo']));
    $sequence=$_POST['sequence'];
    $fee=$_POST['fee'];
    $insurance=$_POST['insurance'];
    $sql="update mg_delivery set subject='$subject',fee=$fee,insurance=$insurance,memo='$memo',sequence=$sequence where id=$id";
    $GLOBALS['conn']->exec($sql);
    PageReturn('成功修改了送货方式！');
  }    
}

function deliveryadd(){
  $subject=FilterText(trim($_POST['subject']));
  $fee=$_POST['fee'];
  $sequence=$_POST['sequence'];
  if($subject && is_numeric($fee) && is_numeric($sequence)){
    $memo=FilterText(trim($_POST['memo']));
    $insurance=$_POST['insurance'];
    $sql="insert into mg_delivery set subject='$subject',fee=$fee,insurance=$insurance,memo='$memo',sequence=$sequence,method=0";
    if($GLOBALS['conn']->exec($sql)) PageReturn('成功添加了新的送货方式！');
  }
  else PageReturn('信息不完整！');
} 

function deliverydel(){
  $id=$_POST['id'];
  if(is_numeric($id) && $id>0){
    if($GLOBALS['conn']->exec('delete from mg_delivery where id='.$id)) PageReturn('删除成功！');
  }
}
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF"> 
<tr> 
  <td height="22" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>配送方式分类</font></b></td>
</tr> 
<tr>  
<td bgcolor="#FFFFFF" valign="top">
  <table width="96%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#f2f2f2">
  <tr align="center" bgcolor="#F7F7F7">
    <td width="16%" height="25" background="images/topbg.gif"><strong>配送方式</strong></td>
    <td width="14%" height="25" background="images/topbg.gif"><strong>配送费用</strong></td>
    <td width="15%" height="25" background="images/topbg.gif"><strong>保价费用</strong></td>
    <td width="38%" height="25" background="images/topbg.gif"><strong>相关说明</strong></td>
    <td width="6%" height="25" background="images/topbg.gif"><strong>排序</strong></td>
    <td width="11%" height="25" background="images/topbg.gif"><strong>操作</strong></td>
  </tr><?php
$jishu=0;
$res=$conn->query('select * from mg_delivery where method=0 order by sequence',PDO::FETCH_ASSOC);
foreach($res as $row){?>
  <form method="post" >
  <tr align="center" bgcolor="#FFFFFF">
    <td height="25"><input name="subject" type="text" class="input_sr" value=<?php echo $row['subject'];?> size="14"><input type="hidden" name="id" value="<?php echo $row['id'];?>"></td>
    <td height="25"><input name="fee" type="text" class="input_sr" value=<?php echo $row['fee'];?> size="6">
      元</td>
    <td height="25"><input name="insurance" type="text" class="input_sr" value=<?php echo $row['insurance'];?> size="6">
      %</td>
    <td height="25"><textarea name="memo" cols="46" rows="4"><?php echo $row['memo'];?></textarea></td>
    <td height="25"><input name="sequence" type="text" class="input_sr" value=<?php echo $row['sequence'];?> size="2"></td>
    <td height="25"><input type="button" class="input_bot" value="修改" onclick="modify_delivery(this.form)"><input  type="button" class="input_bot" value="删除" onclick="delete_delivery(this.form)"><input type="hidden" value="<?php echo $row['id'];?>"></td>
  </tr>
  </form><?php
  $jishu++;
}?>
</table> 
  <br>
  <br>
  <table width="96%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#f2f2f2">
  <tr align="center" bgcolor="#F7F7F7">
      <td width="16%" height="25" background="images/topbg.gif"><strong>配送方式</strong></td>
      <td width="14%" height="25" background="images/topbg.gif"><strong>配送费用</strong></td>
      <td width="15%" height="25" background="images/topbg.gif"><strong>保价费用</strong></td>
      <td width="38%" height="25" background="images/topbg.gif"><strong>相关说明</strong></td>
      <td width="6%" height="25" background="images/topbg.gif"><strong>排序</strong></td>
      <td width="11%" height="25" background="images/topbg.gif"><strong>操作</strong></td>
    </tr>
    <form  method="post" action="?action=deliveryadd">
    <tr align="center" bgcolor="#FFFFFF">
      <td height="25"><input name="subject" type="text" class="input_sr" id="subject" size="14"></td>
      <td height="25"><input name="fee" type="text" class="input_sr" id="fee" size="6" >
      元</td>
      <td height="25"><input name="insurance" type="text" class="input_sr" id="insurance" size="6"> %</td>
      <td height="25"><textarea name="memo" cols="46" rows="4" id="memo"></textarea></td>
      <td height="25"><input name="sequence" type="text" class="input_sr" id="sequence" value="<?php echo $jishu+1;?>" size="2"></td>
      <td height="25"><input name="Submit" type="submit" class="input_bot" value="添加"></td>
    </tr>
    </form>
  </table>
  <br></td>
</tr>
</table>
<script>
function modify_delivery(myform){
  myform.action="?action=deliverysave";
  myform.submit();
}

function delete_delivery(myform){
  if(confirm('您确定进行删除操作吗？')){
    myform.action="?action=deliverydel";
    myform.submit();
  }
}
</script>
</body>
</html><?php
db_close();?>
