<?php require('includes/dbconn.php');
CheckLogin();
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
    $website=FilterText(trim($_POST['website']));
    $matchrule=$_POST['matchrule'];
    $memo=FilterText(trim($_POST['memo']));
    $sequence=$_POST['sequence'];
    $sql="update mg_delivery set subject='$subject',website='$website',matchrule='$matchrule',memo='$memo',sequence=$sequence where id=$id";
    $GLOBALS['conn']->exec($sql);
    PageReturn('成功修改了送货方式！');
  }    
}

function deliveryadd(){
  $subject=FilterText(trim($_POST['subject']));
  $sequence=$_POST['sequence'];
  if($subject && is_numeric($sequence)){
    $memo=FilterText(trim($_POST['memo']));
    $matchrule=FilterText(trim($_POST['matchrule']));
    $website=FilterText(trim($_POST['website']));
    $sql="insert into mg_delivery set subject='$subject',website='$website',matchrule='$matchrule',memo='$memo',sequence=$sequence,method=2";
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
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF"> 
<tr> 
  <td height="20" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>配送方式管理</font></b></td>
</tr> 
<tr>  
<td bgcolor="#FFFFFF"><br>
  <table width="96%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#f2f2f2">
  <tr align="center" bgcolor="#F7F7F7">
    <td width="15%" height="25" background="images/topbg.gif"><strong>配送方式</strong></td>
    <td width="20%" height="25" background="images/topbg.gif"><strong>货单号码正则匹配表达式</strong></td>
    <td width="15%" height="25" background="images/topbg.gif"><strong>官方网站</strong></td>
    <td width="30%" height="25" background="images/topbg.gif"><strong>相关说明</strong></td>
    <td width="5%" height="25" background="images/topbg.gif"><strong>排序</strong></td>
    <td width="15%" height="25" background="images/topbg.gif"><strong>操作</strong></td>
  </tr><?php
$jishu=0;
$res=$conn->query('select * from mg_delivery where method=2 order by sequence',PDO::FETCH_ASSOC);
foreach($res as $row){?>
  <form method="post">
  <tr align="center" bgcolor="#FFFFFF">
    <td height="25"><input name="subject" type="text" class="input_sr" value=<?php echo $row['subject'];?> maxlength="10" size="14" style="text-align:center"></td>
    <td height="25"><input name="matchrule" type="text" class="input_sr" value="<?php echo $row['matchrule'];?>" maxlength="20" size="25"></td>
    <td height="25"><input name="website" type="text" class="input_sr" value="<?php echo $row['website'];?>" size="25"></td>
    <td height="25"><textarea name="memo" cols="30" rows="2"><?php echo $row['memo'];?></textarea></td>
    <td height="25"><input name="sequence" type="text" style="text-align:center" onkeyup="if(isNaN(value))execCommand('undo')" value=<?php echo $row['sequence'];?> size="2"></td>
    <td height="25">
    	 <input type="hidden" name="id" value="<?php echo $row['id'];?>">
    	 <input type="button" value="修改" onclick="modify_delivery(this.form);">
    	 <input type="button" value="删除" onclick="delete_delivery(this.form);">
  </tr>
  </form><?php
$jishu++;
}?>
</table> 
  <br>
  <br>
  <table width="96%"  border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#f2f2f2">
  <tr align="center" bgcolor="#F7F7F7">
      <td width="15%" height="25" background="images/topbg.gif"><strong>配送方式</strong></td>
      <td width="20%" height="25" background="images/topbg.gif"><strong>货单号码正则匹配表达式</strong></td>
      <td width="15%" height="25" background="images/topbg.gif"><strong>官方网站</strong></td>      
      <td width="30%" height="25" background="images/topbg.gif"><strong>相关说明</strong></td>
      <td width="5%" height="25" background="images/topbg.gif"><strong>排序</strong></td>
      <td width="15%" height="25" background="images/topbg.gif"><strong>操作</strong></td>
  </tr>
  <form method="post" action="?action=deliveryadd">
  <tr align="center" bgcolor="#FFFFFF">
      <td height="25"><input name="subject" type="text" class="input_sr" size="14"></td>
      <td height="25"><input name="RegExp" type="text" class="input_sr"size="25" ></td>
      <td height="25"><input name="website" type="text" class="input_sr"size="25"></td>
      <td height="25"><textarea name="memo" cols="30" rows=2"></textarea></td>
      <td height="25"><input name="sequence" type="text" class="input_sr" id="sequence" onkeyup="if(isNaN(value))execCommand('undo')" value=<?php echo $jishu+1;?> size="2"></td>
      <td height="25"><input type="submit" class="input_bot" value="添加"></td>
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
