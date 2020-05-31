<?php require('includes/dbconn.php');
CheckLogin('SYSTEM');

db_open();
if(@$_GET['action']=='save'){ #所有无权限
  $license=$_POST['content'];
  if($conn->exec("update mg_configs set license='$license'")) PageReturn('修改成功！');
  else PageReturn('没有修改!'); 
}
$license=$conn->query('select license from mg_configs')->fetchColumn(0);
// $license=htmlspecialchars_decode(htmlspecialchars_decode(htmlspecialchars_decode($license)));

db_close();?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<title>服务条款设置</title>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF" id="Table1">
  <tr> 
    <td height="22" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <font color=#FF0000>服务条款设置</font></b></td>
  </tr>
  <tr> 
    <td bgcolor="#FFFFFF" valign="top"><form  method="post" action="?action=save" onsubmit="this.content.value=ueditor.getContent();">
       <br>
        <table width="735" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#f2f2f2" id="Table2">
           <tr> 	
            <td align="center" bgcolor="#f7f7f7"><input type="hidden" name="content">

     <link rel="stylesheet" href="ueditor/themes/default/css/ueditor.css">
     <script id="content" type="text/plain"><?php echo $license;?></script>
     <script type="text/javascript" src="ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="ueditor/ueditor.all.js"></script>
     <script type="text/javascript">var ueditor = UE.getEditor('content',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled: true,autoFloatEnabled: true});</script>
     
            </td>
          </tr>
          <tr> 
            <td align="center" bgcolor="#F7F7F7" width="80%">
                <input name="Submit" type="submit" class="input_bot" id="Submit1" value="提 交">
                <input name="Submit2" type="reset" class="input_bot" id="Reset1" value="恢 复">
            </td>
          </tr>
        </table>
        <br>
    </form></td>
  </tr>
</table>
</body>
</html>
