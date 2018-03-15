<?php require('includes/dbconn.php');
CheckLogin('SYSTEM');
OpenDB();

if(@$_GET['mode']=='sysupdate'){
  $icp=FilterText(trim($_POST['icp']));
  $webname=FilterText(trim($_POST['webname']));
  $webemail=FilterText(trim($_POST['webemail']));
  $address=FilterText(trim($_POST['address']));
  $postcode=FilterText(trim($_POST['postcode']));
  $businesshours=FilterText(trim($_POST['businesshours']));
  $tel=FilterText(trim($_POST['tel']));
  $fax=FilterText(trim($_POST['fax']));
  $manager=FilterText(trim($_POST['manager']));
  $copyright=FilterText(trim($_POST['copyright']));
  $weblogo=FilterText(trim($_POST['weblogo']));
  $weburl=FilterText(trim($_POST['weburl']));
  $webstatenabled=($_POST['webstatenabled']=='1')?1:0;
  $sql="update mg_configs set icp='$icp',webname='$webname',webemail='$webemail',address='$address',postcode='$postcode',businesshours='$businesshours',tel='$tel',fax='$fax',manager='$manager',copyright='$copyright',weblogo='$weblogo',weburl='$weburl',webstatenabled=$webstatenabled";
  if($conn->exec($sql)){    
     echo file_get_contents(get_location_base().'mg_htmgen.php?mode=sysupdate');
     PageReturn('修改成功!');
  } 
  else{
     PageReturn('没有修改!');
  }
}

function get_location_base(){
  $url=$_SERVER['REQUEST_URI'];
  $url=substr($url,0,1+strrpos($url,'/'));
  $port=$_SERVER["SERVER_PORT"];
  $url='http://'.$_SERVER['SERVER_NAME'].(($port=='80')?'':':'.$port).$url;
  return $url;
}

$row=$conn->query('select icp,webname,webemail,address,postcode,tel,fax,manager,copyright,weblogo,weburl,businesshours,webstatenabled from mg_configs',PDO::FETCH_ASSOC)->fetch();?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="22" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>商城基本设置</font></b></td>
  </tr>
  <tr> 
    <td  valign="top" bgcolor="#FFFFFF"> <br>
      <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <form name="form1" id="form1" method="post" action="?mode=sysupdate">
           <tr> 
            <td width="20%" align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>网站网址：</strong></td>
            <td width="80%" bgcolor="#FFFFFF"><INPUT NAME="weburl" TYPE="text" class="input_sr" ID="weburl" VALUE="<?php echo $row['weburl'];?>" size="50"></td>
          </tr>
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>网站名称：</strong></td>
            <td bgcolor="#FFFFFF"> <input name="webname" type="text" class="input_sr" id="webname" value="<?php echo $row['webname'];?>" size="50"></td>
          </tr>
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>公司地址：</strong></td>
            <td bgcolor="#FFFFFF"> <input name="address" type="text" class="input_sr" id="address" value="<?php echo $row['address'];?>" size="50"></td>
          </tr>
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>公司邮编：</strong></td>
            <td bgcolor="#FFFFFF"> <input name="postcode" type="text" class="input_sr" id="postcode" onkeyup="if(isNaN(value))execCommand('undo')" size="50" value="<?php echo $row['postcode'];?>"> </td>
          </tr>
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>公司电话：</strong></td>
            <td bgcolor="#FFFFFF"> <input name="tel" type="text" class="input_sr" id="tel" value="<?php echo $row['tel'];?>" size="50"></td>
          </tr>
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>传真号码：</strong></td>
            <td bgcolor="#FFFFFF"> <input name="fax" type="text" class="input_sr" id="fax" value="<?php echo $row['fax'];?>" size="50"></td>
          </tr>
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>客服信箱：</strong></td>
            <td bgcolor="#FFFFFF"> <input name="webemail" type="text" class="input_sr" id="webemail" value="<?php echo $row['webemail'];?>" size="50"></td>
          </tr>
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>客服ＱＱ：</strong></td>
            <td bgcolor="#FFFFFF"> <input name="manager" type="text" class="input_sr" id="manager" value="<?php echo $row['manager'];?>" size="50"></td>
          </tr>
 
          <tr>
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>营业时间：</strong></td>
            <td bgcolor="#FFFFFF"><input name="businesshours" type="text" class="input_sr" id="businesshours" value="<?php echo $row['businesshours'];?>" size="50" /></td>
          </tr>          
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>公司徽标：</strong></td>
            <td bgcolor="#FFFFFF"><input name="weblogo" type="text" class="input_sr" id="weblogo" value="<?php echo $row['weblogo'];?>" size="50">
          </tr>
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>网站版权：</strong></td>
            <td bgcolor="#FFFFFF"> <input name="copyright" type="text" class="input_sr" value="<?php echo $row['copyright'];?>" size="50"></td>
          </tr>
          <tr>
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>网站备案：</strong></td>
            <td bgcolor="#FFFFFF"><input name="icp" type="text" class="input_sr" value="<?php echo $row['icp'];?>" size="50" /></td>
          </tr>
          <tr>
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>流量统计：</strong></td>
            <td bgcolor="#FFFFFF">是否打开网站流量统计<input type="checkbox" name="webstatenabled" value="1" <?php if($row['webstatenabled']) echo 'checked';?> ></td>
          </tr>
          <tr> 
            <td height="22" colspan="2" align="center" bgcolor="#F4F4F4">
                <input name="Submit" type="submit" class="input_bot" value="提交">
                <input name="mode" type="hidden" value="sysupdate">
                &nbsp;&nbsp; 
                <input name="Submit2" type="reset" class="input_bot" value="恢复"></td>
          </tr>
        </form>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
<?php CloseDB();?>
