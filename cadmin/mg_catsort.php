<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
db_open();

$mode=@$_GET['mode'];
if($mode=='sethot'){
   $catid=$_POST['catid'];
   $property=$_POST['property'];
   if(is_numeric($catid) && $catid>0 && is_numeric($property) && $property>0){
     if($conn->exec('update mg_category set property='.$property.' where property<1 and recommend>0 and id='.$catid)) PageReturn('设置成功！');	  
   }
   PageReturn('操作失败！');
}
else if($mode=='cancelnav'){
  $catid=$_POST['catid'];
  if(is_numeric($catid) && $catid>0){
     if($conn->exec('update mg_category set property=0 where id='.$catid)) PageReturn('操作成功！');
  }
  PageReturn('操作失败！');
}
else if($mode=='changerank'){
  $catid=$_POST['catid'];
  if(is_numeric($catid) && $catid>0){
     $state=0;
     $prev_id=0;$rear_id=0;
     $rear_rank=0;$prev_rank=0;$cur_rank=0;
     $res=$conn->query('select id,property from mg_category where property>0 and recommend>0 order by property asc',PDO::FETCH_ASSOC);
     foreach($res as $row){
       if($state==0){
 	       if($row['id']==$catid){
           $state=1;
           $cur_rank=$row['property'];
         }
         else{
           $prev_id=$row['id'];
           $prev_rank=$row['property'];
         } 
       }
       else if($rear_id==0){
           $rear_id=$row['id'];
           $rear_rank=$row['property']; 
        }
     }
     if(@$_GET['direction']=='1'){ #property  ↑
        if($rear_id){
          $conn->exec('update mg_category set property='.$cur_rank.' where id='.$rear_id);
          $conn->exec('update mg_category set property='.$rear_rank.' where id='.$catid);
        } 
     }
     else{
        if($prev_id){
          $conn->exec('update mg_category set property='.$cur_rank.' where id='.$prev_id);
          $conn->exec('update mg_category set property='.$prev_rank.' where id='.$catid);
        } 
     }
     PageReturn('');
  }
}?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript" src="<?php echo WEB_ROOT;?>include/category.js" type="text/javascript"></SCRIPT>
<style type="text/css">
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
</style>
<script>
function SetNavCat(myForm)
{ if(myForm.catid.value!="0")
	{ myForm.submit();
	}
	else alert("请选择分类！");
}
function BrandChangeRank(myForm,upordown)
{ myForm.action="?mode=changerank&direction="+upordown;
	myForm.submit();
}

function CancelNavCat(myForm)
{ if(confirm("确定要执行该操作？"))
  { myForm.action="?mode=cancelnav";
	  myForm.submit();
	}  
}
</script>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr bgcolor="#F7F7F7"> 
    <td height="20" colspan="2" background="images/topbg.gif" bgcolor="#F7F7F7">
       <table width="100%" border="0" cellpadding="0" cellspacing="0">    	
    	  <tr><td width="50%"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_category.php">商品分类管理</a> -&gt; <font color=#FF0000>导航分类设置</font></b></td>
            <td align="right"></td>
    	  </tr>
    	  </table>
    </td>
  </tr>
<tr> 
  <td valign="top" bgcolor="#FFFFFF">
  	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr height="20" bgcolor="#F7F7F7">
       <td WIDTH="15%" height="25" align="center" background="images/topbg.gif"><strong>序号</strong></td>
       <td WIDTH="50%" height="25" align="center" background="images/topbg.gif"><strong>分类名称</strong></td>
       <td WIDTH="35%" height="25" align="center" background="images/topbg.gif"><strong>操作</strong></td>
</tr><?php

$max_property=0;
$sequence=0;
$res=$conn->query('select * from mg_category where property>0 and recommend>0 order by property desc',PDO::FETCH_ASSOC);
foreach($res as $row){
  if($max_property==0)$max_property=$row['property'];?>
  <form method=post>
  <tr align="center" bgcolor="#FFFFFF" height="20" onMouseOut=mOut(this,"#FFFFFF") onMouseOver=mOvr(this,MENU_HOTTRACK_COLOR)> 
       <td height="25"><?php echo ++$sequence;?><input type="hidden" name="catid" value="<?php echo $row['id'];?>"></td>
       <td height="25"><?php echo $row['title'];?></td>
       <td height="25"><input type="button" value="↑" onclick="BrandChangeRank(this.form,1)"><input type="button" value="↓" onclick="BrandChangeRank(this.form,0)"> &nbsp; <input type="button" value="取消导航" onclick="CancelNavCat(this.form)"></td>
     </tr></form><?php
}?>
</table>
<br>

<form method=post action="?mode=sethot">
<table width="500" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr align="center" bgcolor="#FFFFFF" height="20" onMouseOut=mOut(this,"#FFFFFF") onMouseOver=mOvr(this,MENU_HOTTRACK_COLOR)> 
        <td height="25" background="images/topbg.gif">
        	<script language="javascript">
             CreateCategorySelection("catid",0,"--选择商品分类--",null);
          </script>
        	
        	</td>
        <td height="25" background="images/topbg.gif"><input type="button" value="添加导航分类" onclick="SetNavCat(this.form)"><input type="hidden" name="property" value="<?php echo $max_property+1;?>"></td>
      </tr>		 
     </table></form>

  </td>
</tr>
</table>
</body>
</html><?php
db_close();?>
