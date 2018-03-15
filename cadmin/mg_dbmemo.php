<?php require('includes/dbconn.php');
require("includes/mg_comm.php");

$str_params=@$_GET["params"];
if(empty($str_params) || !($params=simpleDecode($str_params))) PageReturn("参数无效！！",0);
parse_str($params,$params);
if(!is_numeric($id=$params['id']) || empty($tb=$params["tb"]) || empty($field=$params["field"]))PageReturn("参数无效！",0);
CheckLogin();
OpenDB();

if(@$_POST['mode']=='save'){
  $sql="update `$tb` set $field=:content";
  if($id>0)$sql.=" where id=$id";
  $res=$conn->prepare($sql);
  $res->bindValue(":content",$_POST["content"]);
  if($res->execute()){ //执行sql并返回受影响的列数
    echo '<script language="javascript">parent.closeDialog("<OK>");</script>';
  }
  CloseDB();
  exit(0);
}

$sql="select $field from `$tb`";
if($id>0)$sql.=" where id=$id";
$content=$conn->query($sql)->fetchColumn(0);
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Memo Editor</title>
</head>
<body topmargin="0" leftmargin="0">
<form method="post" action="?params=<?php echo $str_params;?>" onsubmit="this.content.value=ueditor.getContent();" style="margin:0px">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr><td width="100%" height="99%" align="center" valign="top">
     <input type="hidden" name="mode" value="save"><input type="hidden" name="content">
     <script id="container" type="text/plain"><?php echo $content;?></script>
     <script type="text/javascript" src="ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="ueditor/ueditor.all.js"></script>
     <script type="text/javascript">
       var ueditor = UE.getEditor('container',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled:true,autoFloatEnabled:true});
    </script>
  </td>
</tr>
<tr>
  <td align="center" height="30"><input type="submit" value="递交修改"></td>
</tr>
</table></form>
 
</body>
</html>
<?php CloseDB();?>
