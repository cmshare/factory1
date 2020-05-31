<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
db_open();

$mode=@$_GET['mode'];
if($mode=='sethot'){
   $brandid=$_POST['brandid'];
   $recommend=$_POST['recommend'];
   if(is_numeric($brandid) && $brandid>0 && is_numeric($recommend) && $recommend>=2){
     if(!is_numeric($recommend)||$recommend<2)$recommend=2;
     if($conn->exec('update mg_category set recommend='.$recommend.' where recommend<2 and id='.$brandid)) PageReturn('设置成功！');	  
   }
   PageReturn('操作失败！');
}
else if($mode=='cancelhot'){
  $brandid=$_POST['brandid'];
  if(is_numeric($brandid) && $brandid>0){
     if($conn->exec('update mg_category set recommend=1 where recommend>=2 and id='.$brandid)) PageReturn('操作成功！');
  }
  PageReturn('操作失败！');
}
else if($mode=='changerank'){
  $brandid=$_POST['brandid'];
  if(is_numeric($brandid) && $brandid>0){
     $state=0;
     $prev_id=0;$rear_id=0;
     $rear_rank=0;$prev_rank=0;$cur_rank=0;
     $res=$conn->query('select id,recommend from mg_category where recommend>1 order by recommend asc',PDO::FETCH_ASSOC);
     foreach($res as $row){
        if($state==0){
 	   if($row['id']==$brandid){
             $state=1;
             $cur_rank=$row['recommend'];
           }
           else{
             $prev_id=$row['id'];
             $prev_rank=$row['recommend'];
           } 
        }
        else if($rear_id==0){
           $rear_id=$row['id'];
           $rear_rank=$row['recommend']; 
        }
     }
     if(@$_GET['direction']=='1'){ #recommand  ↑
        if($rear_id){
          $conn->exec('update mg_category set recommend='.$cur_rank.' where id='.$rear_id);
          $conn->exec('update mg_category set recommend='.$rear_rank.' where id='.$brandid);
        } 
     }
     else{
        if($prev_id){
          $conn->exec('update mg_category set recommend='.$cur_rank.' where id='.$prev_id);
          $conn->exec('update mg_category set recommend='.$prev_rank.' where id='.$brandid);
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
function SetHotBrand(myForm)
{ if(myForm.brandid.value!="0")
	{ myForm.submit();
	}
	else alert("请选择品牌！");
}
function BrandChangeRank(myForm,upordown)
{ myForm.action="?mode=changerank&direction="+upordown;
	myForm.submit();
}

function CancelHotBrand(myForm)
{ if(confirm("确定要执行该操作？"))
  { myForm.action="?mode=cancelhot";
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
    	  <tr><td width="50%"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="mg_category.php">品牌分类管理</a> -&gt; <font color=#FF0000>热销品牌分类管理</font></b></td>
    	      <td align="right"></td>
    	  </tr>
    	  </table>
    </td>
  </tr>
<tr> 
  <td valign="top" bgcolor="#FFFFFF">
  	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr height="20" bgcolor="#F7F7F7">
       <td WIDTH="15%" height="25" align="center" background="images/topbg.gif"><strong>推荐值(热销排行)</strong></td>
       <td WIDTH="50%" height="25" align="center" background="images/topbg.gif"><strong>品牌名称</strong></td>
       <td WIDTH="35%" height="25" align="center" background="images/topbg.gif"><strong>操作</strong></td>
</tr><?php
$sortindex=0;
$maxrecommend=0;
$res=$conn->query('select * from mg_category where recommend>1 order by recommend desc',PDO::FETCH_ASSOC);
foreach($res as $row){
  $sortindex++;
  if($maxrecommend==0)$maxrecommend=$row['recommend'];?>
  <form method=post>
  <tr align="center" bgcolor="#FFFFFF" height="20" onMouseOut=mOut(this,"#FFFFFF") onMouseOver=mOvr(this,MENU_HOTTRACK_COLOR)> 
       <td height="25"><?php echo $row['recommend'];?><input type="hidden" name="brandid" value="<?php echo $row['id'];?>"></td>
       <td height="25"><?php echo $row['title'];?></td>
       <td height="25"><input type="button" value="↑" onclick="BrandChangeRank(this.form,1)"><input type="button" value="↓" onclick="BrandChangeRank(this.form,0)"> &nbsp; <input type="button" value="取消热销" onclick="CancelHotBrand(this.form)"></td>
     </tr></form><?php
}?>
</table>
<br>

<form method=post action="?mode=sethot">
<table width="500" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
     <tr align="center" bgcolor="#FFFFFF" height="20" onMouseOut=mOut(this,"#FFFFFF") onMouseOver=mOvr(this,MENU_HOTTRACK_COLOR)> 
        <td height="25" background="images/topbg.gif">
        	<script language="javascript">
            	CreateBrandSelection("brandid",0,"--选择品牌分类--",null);
            </script>
        	
        	</td>
        <td height="25" background="images/topbg.gif"><input type="button" value="添加热销品牌分类" onclick="SetHotBrand(this.form)"><input type="hidden" name="recommend" value="<?php echo $maxrecommend+1;?>"></td>
      </tr>		 
     </table></form>

  </td>
</tr>
</table>
</body>
</html><?php
db_close();?>
