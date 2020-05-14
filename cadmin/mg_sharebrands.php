<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
OpenDB();

$mode=@$_GET['mode'];
if($mode=='setshare'){
  $brandid=$_POST['brandid'];
  if(is_numeric($brandid) && $brandid>0){
    $conn->exec('update mg_category set shared=1 where id='.$brandid.' and recommend>=0');
    PageReturn('设置成功！');	  
  }
}
else if($mode=='cancelshare'){
  $brandid=$_POST['brandid'];
  if(is_numeric($brandid) && $brandid>0){
    $conn->exec('update mg_category set shared=0 where id='.$brandid);
    PageReturn('操作成功！');
  }
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript" src="<?php echo WEB_ROOT;?>include/brandsel.js" type="text/javascript"></SCRIPT>
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
function SetShareBrand(myForm)
{ if(myForm.brandid.value!="0")
	{ myForm.submit();
	}
	else alert("请选择品牌！");
}
function BrandChangeRank(myForm,upordown)
{ myForm.action="?mode=changerank&direction="+upordown;
	myForm.submit();
}

function cancelshareBrand(myForm)
{ if(confirm("确定要执行该操作？"))
  { myForm.action="?mode=cancelshare";
	  myForm.submit();
	}  
}
</script>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr bgcolor="#F7F7F7"> 
    <td height="20" colspan="2" background="images/topbg.gif" bgcolor="#F7F7F7">
       <b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_category.php">品牌分类管理</a> -&gt; <font color=#FF0000>共享品牌分类管理</font></b>
    </td>
  </tr>
<tr> 
  <td valign="top" bgcolor="#FFFFFF">
  	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr height="20" bgcolor="#F7F7F7">
       <td WIDTH="15%" height="25" align="center" background="images/topbg.gif"><strong>序号</strong></td>
       <td WIDTH="70%" height="25" align="center" background="images/topbg.gif"><strong>品牌名称</strong></td>
       <td WIDTH="15%" height="25" align="center" background="images/topbg.gif"><strong>操作</strong></td>
</tr><?php
    
function GetBrandPath($parentid,$title){
   global $conn;
   while($parentid){
     $row=$conn->query('select id,title,parent,isbrand from mg_category where id='.$parentid,PDO::FETCH_ASSOC)->fetch();
     if($row){
       $title = $row['title'].'>>'.$title;
       $parentid = $row['parent'];
       if($row['isbrand'])$parentid=0;
     }
     else $parentid=0;  
   }
   return $title;
}
    
$sortindex=0;
     
$res=$conn->query('select * from mg_category where recommend>=0 and shared=1 order by recommend desc',PDO::FETCH_ASSOC);
foreach($res as $row){
  $sortindex++;?>
     <form method=post>
     <tr align="center" bgcolor="#FFFFFF" height="20" onMouseOut=mOut(this,"#FFFFFF") onMouseOver=mOvr(this,MENU_HOTTRACK_COLOR)> 
       <td height="25"><?php echo $sortindex;?><input type="hidden" name="brandid" value="<?php echo $row['id'];?>"></td>
       <td height="25"><?php
       	 if($row['isbrand']) echo $row['title'];
       	 else echo GetBrandPath($row['parent'],$row['title']);?>
       </td>
       <td height="25"><input type="button" value="取消共享" onclick="cancelshareBrand(this.form)"></td>
     </tr></form><?php
}?>
</table>
<br>
<form method=post action="?mode=setshare">
<table width="500" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
<tr align="center" bgcolor="#FFFFFF" height="20" onMouseOut=mOut(this,"#FFFFFF") onMouseOver=mOvr(this,MENU_HOTTRACK_COLOR)> 
        <td height="25" background="images/topbg.gif">
        	<script language="javascript">
            	CreateBrandSelection("brandid",0,"--选择品牌分类--",null);
            </script>
        	
        	</td>
        <td height="25" background="images/topbg.gif"><input type="button" value="添加共享品牌分类" onclick="SetShareBrand(this.form)"></td>
      </tr></form>		 
     </table>

  </td>
</tr>
</table>
</body>
</html><?php
CloseDB();?>
