<?php require('includes/dbconn.php');
CheckLogin('MANAGE');
db_open();
$OrderName='MerayRecommend';
$mode=@$_GET['mode'];
if($mode=='addnew'){
  $NewProductID=$_POST['productid'];
  if(is_numeric($NewProductID) && $NewProductID>0){
    $row=$conn->query('select id,name,score,price1 from mg_product where id='.$NewProductID,PDO::FETCH_ASSOC)->fetch();
    if($row){
      $product_id=$row['id'];
      $product_name=$row['name'];
      $product_score=$row['score'];
      $product_price=$row['price1'];
    }
    else PageReturn('该商品编号不存在！',0); 
    $sql="productname='$product_name',productid=$product_id,score=$product_score,price=$product_price,amount=0,remark=null";
    if(!$conn->exec("update mg_ordergoods set $sql where ordername='$OrderName' and productid=$product_id")){
      $sql.=",ordername='$OrderName'";  
      if(!$conn->exec("update mg_ordergoods set $sql where ordername is null limit 1") && !$conn->exec("insert into mg_ordergoods set $sql")) PageReturn('添加失败！',0);
    } 
    setcookie('meray_new_product',$product_id); 
    PageReturn('产品【'.$product_name.'】添加成功！<OK>',0); 
  }
}
else if($mode=='modify'){
  $productid=$_POST['productid'];
  if(is_numeric($productid) && $productid>0){ 
    $productname=FilterText(trim($_POST['productname']));
    if($conn->exec("update mg_ordergoods set productname='$productname' where ordername='$OrderName' and productid=$productid")) PageReturn('产品信息修改成功！'); 
  }
}
else if($mode=='delete'){
  $productid=$_POST['productid'];
  if(is_numeric($productid) && $productid>0){ 
    if($conn->exec("update mg_ordergoods set ordername=null where ordername='$OrderName' and productid=$productid")) PageReturn('产品删除成功！'); 
  }
}
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>订单详细资料</title>
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<style type="text/css">
<!--
A{TEXT-DECORATION: none;}
A:link    {COLOR: #000000; TEXT-DECORATION: none}
A:visited {COLOR: #000000; TEXT-DECORATION: none}
A:hover   {COLOR: #FF0000; TEXT-DECORATION: underline}
TD   {FONT-FAMILY:宋体;FONT-SIZE: 9pt;line-height: 150%;}
-->
</style>
</head>
<body topmargin="0" leftmargin="0">
        
<table width="99%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
   <td background="images/topbg.gif" height="30">
    	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr> 
    	<td nowrap>
         <b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <font color=#FF0000>公司推荐产品</font></b>
      </td>
      <td align="right"><input type="button" value="添加产品..." onclick="AddNewProduct()">&nbsp;</td>
      </tr>
      </table>
   </td>
</tr>
<tr> 
   <td valign="top" bgcolor="#FFFFFF">
      
      <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="20" bgcolor="#F7F7F7" align="center"> 
          <td WIDTH="15%" background="images/topbg.gif"><strong><strong>编号</strong></strong></td>
          <td WIDTH="50%" background="images/topbg.gif"><strong><strong>名称</strong></strong></td>
          <td WIDTH="20%" background="images/topbg.gif"><strong><strong>图片</strong></strong></td>
          <td WIDTH="15%" background="images/topbg.gif"><strong><strong>操作</strong></strong></td>
      </tr><?php

$NewAddProduct=@$_COOKIE['meray_new_product'];
$res=$conn->query('select mg_ordergoods.productid,mg_ordergoods.amount,mg_ordergoods.productname from mg_ordergoods inner join mg_product on mg_ordergoods.productid=mg_product.id where mg_ordergoods.ordername=\''.$OrderName.'\' order by mg_ordergoods.productname',PDO::FETCH_ASSOC);
foreach($res as $row){
  $productid=$row['productid'];?>
                   <form method="post">
                   <tr align="center" height="20" bgcolor="#FFFFFF" align="center"  onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
                     <TD<?php if($NewAddProduct==$productid)echo ' bgcolor="#FF6600"';?>><?php echo GenProductCode($productid);?></td>
                     <TD align="left"><input type="text" name="productname" value="<?php echo $row['productname'];?>" size="8" style="width:100%"></TD>
                     <TD><a href="<?php echo GenProductLink($row['productid']);?>" target="_blank"><img src="<?php echo product_pic($productid,0);?>" height="80" width="80" border="0"></a></td>
                     <TD><input type="hidden" name="productid" value="<?php echo $productid;?>"><input type="button" value="修改" onclick="ModifyProduct(this.form)"> <input type="button" value="删除" onclick="DeleteProduct(this.form)"></td>
                   </TR></form><?php
}?>
      </table>

    </td>
 
  </tr>
</table>


<script>
function DeleteProduct(myform)
{ if(confirm("确定要删除该产品？"))
	{ myform.action="?mode=delete";
  	myform.submit();	
	}
}

function ModifyProduct(myform){
  var productname=myform.productname.value.trim();
  if(productname==""){
     alert("产品名称为空！");
  }  
  else{
    myform.action="?mode=modify";
    myform.submit();	
  }
}
function AddNewProduct(){
  var newValue=window.prompt("请填写新添加产品的编号:\n\n", "");
  if(newValue && !isNaN(newValue)){
    var OnPostReturn=function(ret){
      if(ret){
        alert(ret);
        if(ret.indexOf('<OK')>=0)self.location.reload();
      }
    }
    AsyncPost('productid='+newValue,'?mode=addnew',OnPostReturn);
  }  
}
</script>
</body>
</html><?php db_close();?>
