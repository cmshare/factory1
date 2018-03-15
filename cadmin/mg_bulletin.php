<?php require('includes/dbconn.php');
CheckLogin('INFOMATION');
OpenDB();

$action=@$_GET['action'];
if($action){
  switch($action){
    case 'savebulletin1':savebulletin1();break;
    case 'savebulletin2':savebulletin2();break;
    case 'saveadvs':saveadvs();break;
  }
}

function savebulletin1(){
  $notify=$_POST['notify'];
  $GLOBALS['conn']->exec("update mg_configs set notify='$notify'");
  PageReturn('修改成功!');
}

function savebulletin2(){
  $BulletinTitle=FilterText(trim($_POST['bulletintitle']));
  $BulletinEnable=(@$_POST['bulletinenable']=='OK')?1:0;
  $BulletinContent=$_POST['bulletincontent'];
  $GLOBALS['conn']->exec("update mg_configs set bulletinenable=$BulletinEnable,bulletintitle='$BulletinTitle',bulletincontent='$BulletinContent'");
  PageReturn('修改成功!');
}

function saveadvs(){
  $advs_mid_url=FilterText(trim($_POST['advs_mid_url']));
  $advs_mid_show=($_POST['advs_mid_show']=='OK')?1:0;
  $GLOBALS['conn']->exec("update mg_configs set advs_mid_show=$advs_mid_show,advs_mid_url='$advs_mid_url'");
  PageReturn('修改成功!');
}

$row=$conn->query('select notify,bulletinenable,bulletintitle,bulletincontent,advs_mid_show,advs_mid_url from mg_configs',PDO::FETCH_ASSOC)->fetch();
$strNotify=$row['notify']; 
$BulletinEnable=$row['bulletinenable']; 
$BulletinTitle=$row['bulletintitle']; 
$BulletinContent=$row['bulletincontent']; 
$advs_mid_show=$row['advs_mid_show'];
$advs_mid_url=$row['advs_mid_url'];  
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="includes/mg_comm.js"></script>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF" id="Table1">
  <tr> 
    <td height="22" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <font color=#FF0000>公告管理</font></b></td>
  </tr>
  <tr>
  	<td>
        <table width="80%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#f2f2f2" id="Table2">
        <tr><form  method="post" action="?action=savebulletin1" onsubmit="this.notify.value=ueditor.getContent();">
        	 <td align="center" colspan=2 style="font-weight:bold;font-size:20px;color:#FFFFFF;background-color:#0000FF">右侧公告栏</td></tr>
        <tr>
            <td align="center" bgcolor="#f7f7f7"><input type="hidden" name="notify">

     <script id="notify" type="text/plain"><?php echo $strNotify?></script>
     <script type="text/javascript" src="ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="ueditor/ueditor.all.js"></script>
     <script type="text/javascript">var ueditor = UE.getEditor('notify',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled: true,autoFloatEnabled: true});</script>

            </td>
          </tr>
          <tr> 
            <td height="20" align="center" bgcolor="#F7F7F7">
                <input name="Submit" type="submit" class="input_bot" id="Submit1" value="提 交">
                &nbsp;&nbsp; 
            <input name="Submit2" type="reset" class="input_bot" id="Reset1" value="恢 复">           </td>
        </tr></form>
        </table>
  	</td>
  </tr> 	
  <tr> 
    <td bgcolor="#FFFFFF" valign="top"> 
       <br><form method="post" action="?action=savebulletin2" onsubmit="this.bulletincontent.value=ueditor2.getContent();">
        <table width="550" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#f2f2f2">
          <tr><td align="center" colspan=2 style="font-weight:bold;font-size:20px;color:#FFFFFF;background-color:#0000FF">弹窗文字公告</td></tr>
          <tr><td colspan=2>&nbsp;标题：<input type="text" name="bulletintitle" value="<?php echo $BulletinTitle;?>" size=50></td></tr> 
          <tr> 	
            <td align="center" bgcolor="#f7f7f7"  colspan=2><input type="hidden" name="bulletincontent">

     <script id="bulletincontent" type="text/plain"><?php echo $BulletinContent;?></script>
     <script type="text/javascript">var ueditor2 = UE.getEditor('bulletincontent',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled: true,autoFloatEnabled: true});</script>
     

            </td>
          </tr>
          <tr> 
          	<td height="20" bgcolor="#F7F7F7" width="20%" nowrap>
          		&nbsp;显示弹窗文字公告：<input type="checkbox" name="bulletinenable" value="OK" <?php if($BulletinEnable) echo 'checked';?> >
          	</td>            	
            <td align="right" bgcolor="#F7F7F7" width="80%">
                <input name="Submit" type="submit" class="input_bot" id="Submit1" value="保 存">
                <input name="Submit2" type="reset" class="input_bot" id="Reset1" value="恢 复">
            </td>
          </tr>
        </table></form>
   </td>
  </tr>
  <tr> 
    <td bgcolor="#FFFFFF" valign="top"> 
       <br><form name="advset" method="post" action="?action=saveadvs">
        <table width="1000" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#f2f2f2">
          <tr><td align="center" colspan=2 style="font-weight:bold;font-size:20px;color:#FFFFFF;background-color:#0000FF">横幅图片公告</td></tr>
          <tr><td colspan="2" align="center"><img id="preview_img" src="<?php echo $advs_mid_url;?>"></td></tr>
          <tr> 
          	<td height="20" bgcolor="#F7F7F7" width="20%" nowrap>
          		&nbsp;显示横幅图片公告：<input type="checkbox" name="advs_mid_show" value="OK" <?php if($advs_mid_show) echo 'checked';?> >
          	</td>            	
            <td align="right" bgcolor="#F7F7F7" width="80%">
            	图片路径<font color="#FF0000">(建议图片宽度为1000像素，高度任意)</font>：<input type="text" name="advs_mid_url" value="<?php echo $advs_mid_url;?>" size="30" onfocus="ShowImagePreview(this.value)"><input type="button" value="上传图片..." onclick="UploadAdvs('advs_middle')">&nbsp;
                <input type="submit" value="保 存">
                <input type="reset" value="恢 复">
            </td>
          </tr>
        </table></form>
   </td>
  </tr>  
</table>
<script>

function UploadAdvs(imgname){
  var myform=document.forms["advset"];
  var upload_callback=function(ret) { 
    if(ret){
      myform.advs_mid_url.value=ret;
      return true;
    }
  }
  showUploadDialog("adv",imgname,upload_callback);
}

function ShowImagePreview(imagepath)
{ if(!imagepath)imagepath="/uploadfiles/ware/nopic.jpg";
  document.getElementById("preview_img").src=imagepath+"?"+Math.random();	
}	

</script>
</body>
</html><?php
CloseDB();?>
