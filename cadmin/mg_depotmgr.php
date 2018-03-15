<?php require('includes/dbconn.php');
CheckLogin('SYSTEM');
OpenDB();
if(@$_GET['action']=='save'){
  $DepotID=$_POST['id'];
  $DepotName=FilterText(trim($_POST['depotname']));
  if(is_numeric($DepotID) && $DepotID>0 && $DepotName){
    $enabled=($_POST['depotstate']=='enabled')?1:0;
    $conn->exec("update mg_depot set depotname='$DepotName',enabled=$enabled where id=$DepotID");
    PageReturn('修改成功！');
  }
}?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<script language = "JavaScript">
function CheckDepot(myform){
  if(myform.depotname.value==""){
    myform.depotname.focus();
    alert("请仓库名不能为空！");
    return false;
  }
  return true;
}
</script>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="22" background="images/topbg.gif" bgcolor="#F7F7F7"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>仓库信息设置</font></b></td>
  </tr>
  <tr> 
    <td bgcolor="#FFFFFF" valign="top">

    <table width="80%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
    <tr align="center" bgcolor="#F7F7F7" height="25"> 
     	 <td width="10%" background="images/topbg.gif"><strong>仓库号</strong></td>
       <td width="40%" background="images/topbg.gif"><strong>仓库名</strong></td>
       <td width="20%" background="images/topbg.gif"><strong>激活状态</strong></td>
       <td width="30%" background="images/topbg.gif"><strong>操作</strong></td>
    </tr><?php
$res=$conn->query('select * from mg_depot',PDO::FETCH_ASSOC);
foreach($res as $row){?>
      <form method="post" action="?action=save" onsubmit="return CheckDepot(this)">
      <tr bgcolor="#FFFFFF" align="center" height="25"><td><input type="text" name="id" value="<?php echo $row['id'];?>" style="text-align:center;border:0px" readonly/></td>
       <td><input name="depotname" type="text" style="width:100%;<?php if(!$row['enabled']) echo 'color:#BFBFBF';?>"  value="<?php echo $row['depotname'];?>" size="20" maxlength="6"></td>
        <td><input name="depotstate" type="checkbox" <?php if($row['enabled']) echo 'checked';?> value="enabled"></td>
        <td><input type="submit" value="修 改"></td>
      </tr></form><?php
}?>
    </table>
	</td>
  </tr>
</table>
</body>
</html><?php
CloseDB();?>
