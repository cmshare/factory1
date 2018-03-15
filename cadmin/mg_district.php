<?php require('includes/dbconn.php');
CheckLogin('SYSTEM');
OpenDB();
$mode=@$_GET['mode'];
if($mode){
 switch($mode){
   case 'save': do_save();break;
   case 'add': do_add();break;
   case 'delete':do_del();break;
   case 'generatescript':do_generatescript();break;
 }
}

function do_save(){
  global $conn;
  $provinceid=$_POST['provinceid'];
  $sortorder=$_POST['sortorder'];
  $provincename=$_POST['provincename'];
  if(is_numeric($provinceid) && is_numeric($sortorder) && $provincename){
    $parentdistrict=$conn->query('select parent from mg_district where id='.$provinceid)->fetchColumn(0);
    if(empty($parentdistrict)) PageReturn('参数错误！');
    $existid=$conn->query('select id from mg_district where parent='.$parentdistrict.' and name=\''.$provincename.'\' and id<>'.$provinceid)->fetchColumn(0);
    if($existid)PageReturn('同级的地区名称已经存在！');
    $conn->exec("update mg_district set name='$provincename',sortorder=$sortorder where id=$provinceid");
    PageReturn('保存成功！');
  }
  else PageReturn('参数错误！');
}

function do_add(){
  global $conn;
  $parentdistrict=$_GET['parent'];
  $sortorder=$_POST['sortorder'];
  $provincename=$_POST['provincename'];
  if(is_numeric($parentdistrict) && is_numeric($sortorder) && $provincename){
     $existid=$conn->query('select * from mg_district where parent='.$parentdistrict.' and name=\''.$provincename.'\'')->fetchColumn(0);
     if($existid)PageReturn('同级的地区名称已经存在了！');
     $sql="mg_district set parent=$parentdistrict,name='$provincename',sortorder='$sortorder'";
     if($conn->exec("update $sql where parent=-1 limit 1") || $conn->exec("insert into $sql"))PageReturn('添加成功！');
  }
}

function nest_ids($selec){
  $provinceid=$selec;
  $res=$GLOBALS['conn']->query('select id from mg_district where parent = '.$selec,PDO::FETCH_ASSOC);
  foreach($res as $row){
    $provinceid.=','.nest_ids($row['id']);
  }
  return $provinceid;
}

function do_del(){
   $provinceid=$_POST['provinceid'];
   if(is_numeric($provinceid) && $provinceid>0){
     $ids=nest_ids($provinceid);
     $GLOBALS['conn']->exec('update mg_district set parent=-1 where id in('.$provinceid.')');
     PageReturn('删除成功！');
   }
}

function do_generatescript(){
  global $conn;
  $provincearray="var provincearray=new Array(new Option('请选择省份……','0')";
  $script_path=$_SERVER['DOCUMENT_ROOT'].WEB_ROOT.'user/district.js';
  $myscript="var cityarray=[];\ncityarray['0']=new Array(new Option('请选择城市……','0'));\n";
  $res=$conn->query('select * from mg_district where parent=0 order by sortorder',PDO::FETCH_ASSOC);
  foreach($res as $row){
    $provincearray.=",new Option('{$row['name']}','{$row['id']}')";
    $myscript.="cityarray['{$row['id']}']=new Array(\n";
      $subquery=$conn->query('select * from mg_district where parent='.$row['id'].' order by sortorder',PDO::FETCH_ASSOC);
      $subcount=0;
      foreach($subquery as $subrs){
        if($subcount)$myscript.=",\n";
        $myscript.="new Option('{$subrs['name']}','{$subrs['id']}')";
        $subcount++;
      }
      if($subcount)$myscript.=");\n";
      else $myscript.="new Option('','0'));\n";
  }
  $outtext=$provincearray.");\n".$myscript."\n".'function OnProvinceSelChange(){var myform=this.form;var ProvinceCode=(myform)?myform.provincelist.value:0;if(ProvinceCode){myform.district.options.length=0;for(var i=0;i<cityarray[ProvinceCode].length;i++){ myform.district.options.add(cityarray[ProvinceCode][i]);}}}function InitDistrictSelection(myform){var i,city,k,province=0,ProvinceCode,districtcode=myform.district.options[0].value;myform.provincelist.options.length=0;for(i=0;i<provincearray.length;i++){ myform.provincelist.options.add(provincearray[i]);if(!province){ ProvinceCode=provincearray[i].value;for(city=0;city<cityarray[ProvinceCode].length;city++){if(districtcode==cityarray[ProvinceCode][city].value){ myform.district.options.length=0;for(k=0;k<cityarray[ProvinceCode].length;k++){ myform.district.options.add(cityarray[ProvinceCode][k]);}province=i;getselected=true;break;}}}}if(province){myform.district.options[city].selected=true;myform.provincelist.options[province].selected=true;}myform.provincelist.onchange=OnProvinceSelChange;}'."\n";
  if(file_put_contents($script_path,$outtext))PageReturn('静态输出成功！');
  else PageReturn('操作失败');
}
  
$parentdistrict=@$_GET['parent'];
if(is_numeric($parentdistrict) && $parentdistrict>0){
  $PID=$parentdistrict;
  $districtPath='';
  while($PID){
    $row=$conn->query('select id,name,parent from mg_district where id='.$PID,PDO::FETCH_ASSOC)->fetch();
    if($row){ 
      $districtPath = '&nbsp;&gt;&gt;&nbsp;<a href="?parent='.$row['id'].'">'.$row['name'].'</a>'.$districtPath;
      $PID=$row['parent'];
    }
    else{
      PageReturn('您输入的参数非法，请正确操作！',-1);
    }
  }
}
else{
  $parentdistrict=0;
  $districtPath='';
}?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<style type="text/css">
<!--
.input_text{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #000000;
	text-decoration: none;
	font-size: 12px;
	width:100%;
	text-align:center;
	border: 0px solid #CCCCCC;
	background-color:transparent
}
-->
</style> 
<script>
function IsDigit()
{
  return ((event.keyCode >= 48) && (event.keyCode <= 57));
}
function EditDistrict(clickbutton){
  var myform=clickbutton.form;
  var buttonname=clickbutton.value;
  if(buttonname=="保存"){
    if(myform.provincename.value.trim()==""){
     	myform.provincename.focus();
    	return false;
    }
    myform.action="?mode=save"
    myform.submit();
  }
  else if(buttonname=="删除"){
    if(!confirm("确定要删除该地区及其下属地区吗?"))return false;
    myform.action="?mode=delete"
    myform.submit();
  }
  else if(buttonname=="添加"){
    var sortorder=myform.sortorder.value.trim();
    if(sortorder=="" || isNaN(sortorder)){
      alert("请填写有效的序号！");
      myform.sortorder.focus();
      return false;
    } 		
    if(myform.provincename.value.trim()==""){
      alert("请填写名称！");
      myform.provincename.focus();
      return false;
    } 
    myform.action="?parent=<?php echo $parentdistrict;?>&mode=add"
    myform.submit();
  }
  else{
    self.location.href="?parent="+myform.provinceid.value;
    return false;
  }
  return true;
}
</script>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF" class="tableBorder">
<tr> 
  <td height="20" colspan="5" background="images/topbg.gif">
  	<table border=0 width="100%" height="100%"><tr><form method="post" action="?mode=generatescript">
  		<td><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="?parent=0"><font color=#FF0000>省市地区信息库</font></a><?php echo $districtPath;?></b></td>
  		<td align="right"><?php if($parentdistrict==0)echo '<input type="submit" title="将地区信息更新到静态的网页及脚本" value="应用更新输出">';?></td>
  	</tr></form></table>
  </td>
</tr>
<form method="post" action="shengset.php?action=update">
<tr align=center bgcolor="#f2f2f2"> 
<td width="10%" background="images/topbg.gif"><strong>序号</strong></td>
<td width="40%" background="images/topbg.gif"><strong>名称</strong></td>
<td width="50%" background="images/topbg.gif"><strong>操作</strong></td>
</tr><?php
$res=$conn->query('SELECT  * From mg_district where parent='.$parentdistrict.' order by sortorder',PDO::FETCH_ASSOC);
$row=$res->fetch();
if(empty($row)) echo '<tr align=center bgcolor="#ffffff"><td colspan="5" align="center">还没有添加下属地区</td></tr>';
else do{?>
<tr align=center bgcolor="#ffffff" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"><form method="post">
  <td align=center><input class=input_text type="text" name="sortorder" size="10" value="<?php echo $row['sortorder'];?>" ONKEYPRESS="event.returnValue=IsDigit();" onMouseOver="this.focus()"  onFocus="this.select()"></td>
  <td align=center><input class=input_text type="text" name="provincename" size="10" value="<?php echo $row['name'];?>"  onMouseOver="this.focus()"  onFocus="this.select()"></td>
  <td align=center><input type="hidden" value="<?php echo $row['id'];?>" name="provinceid"><input type="button" value="保存" onclick="EditDistrict(this)"> &nbsp;  <input type="button" value="删除" onclick="EditDistrict(this)"> &nbsp; <input type="button" value="管理下属地区..." onclick="EditDistrict(this)"></td>
</tr></form><?php
}while(($row=$res->fetch()));?>
</form>
</table>
<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF" class="tableBorder">
<tr> 
  <td height="31" colspan="5" background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>添加同级地区</font></b></td>
</tr>
<form method="post">
<tr align=center bgcolor="#FFFFFF"> 
<td width=33% align=center> 
序号：<input class=input_str type="text" name="sortorder" size="10" ONKEYPRESS="event.returnValue=IsDigit();"  onMouseOver="this.focus()"  onFocus="this.select()">
</td>
<td width=33% align=center> 
名称：<input class=input_str type="text" name="provincename" size="10"  onMouseOver="this.focus()"  onFocus="this.select()">
</td>
<td width=33% align=center> 
<input  type="button" class="input_bot" style="font-family: 宋体; font-size: 9pt" value="添加" onclick="EditDistrict(this)">
</td>
</tr>
</form>
</table>
</body>
</html><?php
CloseDB();?>
