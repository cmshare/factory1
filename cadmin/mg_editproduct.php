<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
OpenDB();
$ProductID=@$_GET['id'];
if(is_numeric($ProductID) && $ProductID>0){
  $prow=$conn->query('select * from mg_product where id='.$ProductID,PDO::FETCH_ASSOC)->fetch();
  if($prow){
    $recommend=(int)$prow['recommend'];
    if($recommend==-1){
      $PageTitle='添加商品';
      $SupplierName='';
      $ProductScore=''; 
    }
    else{
      $PageTitle='编辑商品';
      $SupplierName=$prow['supplier']; 
      $ProductScore=$prow['score'];
    }
  } 
  else PageReturn('商品不存在！',0);
}
else PageReturn('参数错误！',0);

session_start();
$showcost=@$_SESSION['showcost'];
$ProductCode=GenProductCode($ProductID);
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="<?php echo WEB_ROOT;?>include/brandsel.js"></SCRIPT>
<SCRIPT language="JavaScript" src="<?php echo WEB_ROOT;?>include/categorysel.js"></SCRIPT>
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<script language="javascript" src="editproduct.js"></script>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <a href="mg_products.php">管理商品</a> -> <font color=#FF0000><?php echo $PageTitle;?></font></b></td>
  </tr>
  <tr><form name="myform" method="post" action="mg_saveproduct.php?mode=product" onsubmit="this.description.value=ueditor.getContent();return CheckSaveProductInfo(this)" target="dummyframe">
    <td bgcolor="#FFFFFF"><input type="hidden" name="id" value="<?php echo $ProductID;?>"><input type="hidden" name="recommend" value="<?php echo $recommend;?>">

        
        <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>商品名称： </strong></td>
            <td bgcolor="#FFFFFF" nowrap><input name="productname" type="text" class="input_sr" id="productname" value="<?php echo $prow['name'];?>" size="58"> &nbsp;<img src=images/memo.gif title='必填，不能为空，不能重复'> </strong> <font color=#FF0000>＊</font></td>
            <td rowspan=9 width="33%" align="center" valign="middle" bgcolor="#FFFFFF" ><a href="<?php echo GenProductLink($ProductID);?>" target="_blank"><img id="preview_img" height=250 border=0></a></td>
          </tr>
         
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>商品分类：</strong></td>
            <td bgcolor="#FFFFFF"><script language="javascript">CreateBrandSelection("brand",<?php echo $prow['brand'];?>,"---品牌分类---","");CreateCategorySelection("category",<?php echo $prow['category'];?>,"---功能分类---",""); </script> <font color=#FF0000>＊</font></td>
          </tr>            
          <tr>
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4" nowrap><strong>商品来源：</strong></td>
            <td bgcolor="#FFFFFF"><input name="supplier" type="text" class="input_sr" id="supplier" value="<?php echo $SupplierName;?>" maxlength="16" size="30" >
                <select onchange="this.form.supplier.value=this.value;this.selectedIndex=0;">
                  <option selected>请选择供货商</option><?php
                  $res=$conn->query('select suppliername from mg_supplier order by suppliercode',PDO::FETCH_ASSOC);
                  foreach($res as $row)echo '<option value="'.$row['suppliername'].'">'.$row['suppliername'].'</option>';?></select></td>
          </tr>

          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>商品规格：</strong></td>
            <td bgcolor="#FFFFFF"><input name="spec" type="text" class="input_sr" value="<?php echo $prow['spec'];?>" maxlength="16" size="30">
              <select onchange="this.form.spec.value=this.value;this.selectedIndex=0;">
	      <option selected>请选择规格</option><?php
                $res=$conn->query('select * from mg_material order by sortorder',PDO::FETCH_ASSOC);
                foreach($res as $row) echo '<option value="'.$row['name'].'">'.$row['name'].'</option>';?></select></td>
          </tr>
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>商品单位：</strong></td>
            <td bgcolor="#FFFFFF"><input name="unit" class="input_sr" type="text" value="<?php echo $prow['unit'];?>" maxlength="4" size="30">
              <select onchange="this.form.unit.value=this.value;this.selectedIndex=0;"><option selected>请选择单位</option><?php
	      $res=$conn->query('select * from mg_units order by sortorder',PDO::FETCH_ASSOC);
              foreach($res as $row) echo '<option value="'.$row['name'].'">'.$row['name'].'</option>';?></select></td>
          </tr>

          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>商品大图：</strong></td>
            <td bgcolor="#FFFFFF"><input name="pic" type="text" class="input_sr" value="/uploadfiles/ware/<?php echo GenProductCode($ProductID);?>.jpg" readOnly size="30">
              <input name="Submit11" type="button" value="浏览"  onClick="UploadPicture('<?php echo $ProductCode;?>')" title="请单击“浏览”上传图片">  &nbsp; <img src="images/memo.gif">&nbsp;建议大小：550×550，格式JPEG.
            </td>
          </tr>
          <tr>
            <td width="17%" align="right" background="images/topbg.gif" bgcolor="#f4f4f4" nowrap><strong>商品条码：</strong></td>
            <td bgcolor="#FFFFFF" width="50%"><input name="barcode" type="text" class="input_sr" id="barcode" value="<?php echo $prow['barcode'];?>" maxlength="20" size="30" onkeyup="if(isNaN(value))execCommand('undo')"> &nbsp;<img src=images/memo.gif alt='请输入实际商品的条形码，如果此商品没有条形码则填0'> </strong> <font color=#FF0000>＊</font></td>
          </tr>
          <tr>
            <td width="17%" align="right" background="images/topbg.gif" bgcolor="#f4f4f4" nowrap><strong>商品重量：</strong></td>
            <td bgcolor="#FFFFFF" width="50%"><INPUT NAME="weight" TYPE="text" style="text-align:center" class="input_sr"  VALUE="<?php echo $prow['weight'];?>" maxlength="6" SIZE="30" onkeyup="if(isNaN(value))execCommand('undo')"> 克</td>
          </tr>
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>库存总量：</strong></td>
            <td bgcolor="#FFFFFF"><span style="color:#0000FF;cursor:Pointer;text-decoration:underline;padding-left:5px;padding-right:5px" title="点击查看/编缉库存分布明细..." onclick="InitStock(<?php echo $ProductID;?>,this)"><?php echo $prow['stock0'];?></span>件 <font color=#FF0000>＊</font> &nbsp; &nbsp;<?php if($recommend==0) echo '<b>已售出</b><input type="text" name="solded" size=6 maxlength=6 value="'.$prow['solded'].'" style="text-align:center">件 <font color="#FF0000">＊</font>';else echo '<b>已售出</b> <font color="#FF0000">'.$prow['solded'].'</font> 件';?></td>
          </tr>          
          <tr bgcolor="#f7f7f7"> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>商品价格：</strong><br><span style="color:#0000FF;text-decoration: underline;cursor:pointer" onclick="AutoPriceClear(document.myform)">清除</span>&nbsp;|&nbsp;<span style="color:#0000FF;text-decoration: underline;cursor:pointer" onclick="AutoPriceFinish(document.myform)">自动完成</span>&nbsp;</td>
            <td height="50" bgcolor="#FFFFFF" colspan=2>
            	<table width="100%" border="1" cellspacing="0" cellpadding="0" bgcolor="#FFCC00" bordercolor="#D6E7FF">
              <tr align="center">
                <td width="18%">市场价：</td>
                <td width="18%">VIP价：</td>
                <td width="18%"><b>批发价</b></font>：</td>
                <td width="18%"><font color=#FF0000><b>大客户价</b></font>：</td><?php
                if($showcost) echo '<td width="18%">成本价：</td>';?>
                <td width="10%">积分:</td>
              </tr>
              <tr align="center">
                <td nowrap><img src="images/pic6.gif" width="22" height="22" align="absmiddle" /><input name="price1" type="text" class="input_sr" value="<?php echo $prow['price1'];?>" size="5">
元 <font color=#FF0000>＊</font></td>
                <td nowrap><img src="images/pic6.gif" width="22" height="22" align="absmiddle" /><input name="price2" type="text" class="input_sr" value="<?php echo $prow['price2'];?>" size="5">
元</td>
                <td nowrap><img src="images/pic6.gif" width="22" height="22" align="absmiddle" /><input name="price3" type="text" class="input_sr" value="<?php echo $prow['price3'];?>" size="5">
元 <font color=#FF0000>＊</font></td>
                <td nowrap><img src="images/pic6.gif" width="22" height="22" align="absmiddle" /><input name="price4" type="text" class="input_sr" value="<?php echo $prow['price4'];?>" size="5">
元 <font color=#FF0000>＊</font></td><?php
                if($showcost) echo '<td nowrap><img src="images/pic6.gif" width="22" height="22" align="absmiddle"/><input name="cost" type="text" class="input_sr" value="'.$prow['cost'].'" size="5">元</td>';?>
                <td nowrap><INPUT NAME="score" TYPE="text" style="text-align:center" class="input_sr"  VALUE="<?php echo $ProductScore;?>" SIZE="4" onkeyup="if(isNaN(value))execCommand('undo')">分</td>
             
              </tr>
              
            </table></td>
          </tr>          
          
          <tr> 
            <td align="right" valign="top" bgcolor="#f4f4f4"><strong> 详细简介：<br></strong></td>
            <td bgcolor="#FFFFFF" colspan=2><input type="hidden" name="description">
     <script id="description" type="text/plain"><?php echo $prow['description'];?></script>
     <script type="text/javascript" src="ueditor/ueditor.config.js"></script>
     <script type="text/javascript" src="ueditor/ueditor.all.js"></script>
  	    </td></tr>
          <tr>
          	<td height="25" align="right" bgcolor="#f4f4f4"><strong>上架时间：</strong></td>
          	<td height="25" bgcolor="#FFFFFF" colspan="2"><table width="100%" border=0><tr><td width="50%"><input type="text" name="addtime" value="<?php echo date('Y-m-d H:i',$prow['addtime']);?>" style="width:150px;border: 1px solid #CCCCCC;color:#FF0000;background-color:transparent"></td><td width="50%" align="right"><input name="ConfirmButton" type="submit" value="确认以上修改" style="margin-right:20px"></td></tr></table></td>
          </tr>
        </table>
        <br></td>
  </form>
  </tr>
</table>
<iframe name="dummyframe" style="height:5px;display:none" scrolling="no" Frameborder="no" marginwidth=0 marginheight=0></iframe>   
<SCRIPT type="text/javascript">
ShowImagePreview("<?php echo $ProductCode;?>"); 
var ueditor = UE.getEditor('description',{toolbars:[['fullscreen', 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'removeformat', 'formatmatch','|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', 'indent', '|', 'insertorderedlist', 'insertunorderedlist','|','link', 'unlink', 'anchor', '|','insertimage', 'spechars', 'horizontal']],initialFrameWidth:null,autoHeightEnabled: true,autoFloatEnabled: true});
</script>
</body>
</html><?php CloseDB();?>

