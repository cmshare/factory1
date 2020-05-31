<?php require('includes/dbconn.php');
CheckLogin('PRODUCT');
db_open();

$sort_name=@$_COOKIE['sort_name'];	  
if($sort_name){
  $sort_keys=array('addtime','id','stock0','score','recommend','name','price3','price4','cost','weight','onsale','barcode');
  $keycount=count($sort_keys);
  for($i=0;$i<$keycount;$i++){
   if($sort_name==$sort_keys[$i])break;
  }
  if($i==$keycount) goto label_defaut_sort;
}
else{
  label_defaut_sort:
  $sort_name='addtime';
}


$sort_order=@$_COOKIE['sort_order'];
if($sort_order!='asc' && $sort_order!='dec') $sort_order='desc';
$sql_sort_code='order by '.$sort_name.' '.$sort_order;

function sorts($selec){
   global $conn,$CatList;
   $res=$conn->query('select id from mg_category where parent = '.$selec.' order by sequence',PDO::FETCH_NUM);
   foreach($res as $row){
      $brandid = $row[0];
      $CatList = $CatList.','.$brandid;
      sorts($brandid);
   }
}

$cid=@$_GET['cid'];
if(is_numeric($cid) && $cid>0){
  $CatList=(string)$cid;
  sorts($cid);
  $strCat = 'and brand in ('.$CatList.') ';
}
else{
  $cid=0;
  $strCat = '';
}

?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<script language="javascript" src="editproduct.js"></script>
<SCRIPT language="JavaScript" src="<?php echo WEB_ROOT;?>include/brandsel.js" type="text/javascript"></SCRIPT>
<title>下架商品管理</title>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr>
    <td height="20" align="right" background="images/topbg.gif" bgcolor="#F2F2F2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="55%"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <a href="?"><font color=#FF0000>下架商品管理</font></a></b></td>
        <td width="45%"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="right"><script language="javascript">CreateBrandSelection("brand",<?php echo $cid;?>,"--------商品分类过滤--------","self.location.href='?cid='+this.value;");</script></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <form method="post" action="?" style="margin:0px">
      <td height="100" align="center" bgcolor="#FFFFFF"><?php
        $keyvalue=FilterText(trim(@$_GET['kv']));
        if($keyvalue){
            $keyname=FilterText(trim(@$_GET['kn']));
            switch($keyname){
              case 'name':
       		     if(strpos($keyvalue,' ')>0){
       	               $sql='where';
                       $key_list=explode(' ',$keyvalue);
                       for($i=0;$i<count($key_list);$i++){
                         $subkey=trim($key_list[$i]);
                         if($subkey){ 
                           if($i>0) $sql.=' and';
                          $sql.=' name like \'%'.$subkey.'%\' ';
                         }
                       }
                     }
                     else{
                       $sql='where name like \'%'.$keyvalue.'%\' ';
                     }
                     $search_title='商品名称';
                     break;
       		case 'barcode':
       		     $sql='where barcode= \''.$keyvalue.'\'';
                     $search_title='商品条码';
                    break; 
       		case 'productid':
                     if(is_wholenumber($keyvalue) && $keyvalue>0){
       		       $sql='where id='.$keyvalue;
                       $keyvalue=GenProductCode($keyvalue); 
                     }
                     else{
       		       $sql='where id=0';
                     }
                     $search_title='商品编号';
                     break;
       		case 'supplier':
       		     $sql='where supplier= \''.$keyvalue.'\'';
                     $search_title='供 货 商';
                     break;
                default:   PageReturn('参数错误',0);	
             }
             $sql.=' and recommend=0';

             echo '<b>根据<font color="#FF6600">'.$search_title.'</font>搜索关健字：</b><font color="#FF0000">'.$keyvalue.'</font> &nbsp; <a href="?" title="Cancel"><img src="images/delete.gif" align="absmiddle"></a>'; 
         }
         else{
            $keyname='';
            $sql='where recommend=0 '.$strCat;
         }
         while(1){
           $res=page_query('select id,name,price3,price4,cost,recommend,onsale,stock0,solded,addtime','from mg_product',@$sql,$sql_sort_code,15);
           if(!$res && $keyname=='name' && is_numeric($keyvalue)){
             if(strlen($keyvalue)<=8){ 
               $sql='where id='.$keyvalue;
               $keyname='productid'; 
             }
             else{ 
       	       $sql='where barcode= \''.$keyvalue.'\'';
               $keyname='barcode'; 
             }
           }
           else break;
         }

  	 if(!$res){ 
           echo '<p align="center">找不到相关记录！<br><br><a href="javascript:history.go(-1)" style="color:#FF0000;text-decoration:underline">点击返回上一页</a></p>';
         }
         else{?>
          <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr bgcolor="#f7f7f7" align="center" height="20">
            <td width="4%"  background="images/topbg.gif" height="25" ><input type="checkbox" onclick="Checkbox_SelectAll('selectid[]',this.checked)" /></td>
            <td width="8%" background="images/topbg.gif" title="点击排序" style="cursor:pointer" onclick="ProductResort('id')"><strong>编号</strong><?php if($sort_name=='id') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
            <td width="45%" background="images/topbg.gif" title="点击排序" style="cursor:pointer" onclick="ProductResort('name')"><strong>商品名称</strong><?php if($sort_name=='name') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
            <td width="8%" background="images/topbg.gif" title="点击排序" style="cursor:pointer" onclick="ProductResort('stock0')"><strong>总库存</strong><?php if($sort_name=='stock0') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
            <td width="8%" background="images/topbg.gif" title="点击排序" style="cursor:pointer" onclick="ProductResort('solded')"><strong>售出</strong><?php if($sort_name=='solded') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
            <td width="7%" background="images/topbg.gif" title="点击排序" style="cursor:pointer" onclick="ProductResort('price3')"><strong>批发</strong><?php if($sort_name=='price3') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
            <td width="15%" background="images/topbg.gif" title="点击排序" style="cursor:pointer" onclick="ProductResort('price4')"><strong>下架时间</strong><?php if($sort_name=='price4') echo '<img src="images/sort_'.$sort_order.'.gif">';?></td>
           <td width="5%" background="images/topbg.gif"><strong>操作</strong></td>

            </tr><?php
        foreach($res as $row){
          $productid=$row['id'];?>
          <tr height="25"  align="center" bgcolor="#F7F7F7" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">
            <td><input name="selectid[]" type="checkbox" value="<?php echo $row['id'];?>" onclick="mChk(this)"></td>
            <td><a href="mg_stocklog.php?id=<?php echo $productid;?>"><?php echo GenProductCode($productid);?></a></td>
            <td align="left"><?php
               echo '<a href="'.GenProductLink($productid).'" target="_blank">'.$row['name'].'</a>';
               $onsale=$row['onsale']&0xf;
               if($onsale>0) echo '<img src="images/onsale'.$onsale.'.gif" width=16 height=16 alt="特价指数为'.$onsale.'">';?>
            </td>
            <td><?php echo $row['stock0'];?></td>
            <td><?php echo $row['solded'];?></td>
            <td><?php echo FormatPrice($row['price3']);?></td>
            <td><?php echo date('Y-m-d H:i',$row['addtime']);?></td>
            <td><a href="mg_editproduct.php?id=<?php echo $productid;?>"><img src="images/pic9.gif" width="18" height="15" align="absmiddle" border=0></a></td>

          </tr><?php
        }?>
          <tr bgcolor="#FFFFFF">
          <td colspan="9" width="100%">
             <table width="100%" height="35"  border=0>
             <tr><td align="center"><script language="javascript"><?php 
          $queryparam='kn='.$keyname.'&kv='.rawurlencode($keyvalue).'&cid='.$cid;
          echo "GeneratePageGuider(\"$queryparam\",$total_records,$page,$total_pages);";?>
     </script></td>
                  <td align="right" width="20%" nowrap>
 <input type="button"  onclick="BatchForwardProduct(this.form);" value="产品上架" />
              &nbsp;
              <input type="button"  onclick="BatchDeleteProduct(this.form);" value="永久删除">
 &nbsp;
                  </td>
             </tr>
             </table>
            </td>
          </tr>
        </table>
     <?php
}?>
</td>
    </form>
  </tr>
</table>
<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr bgcolor="#FFFFFF">
  <td align="center"><form name="schform" method="get" style="margin:0px">
     <b>按</b><select name="kn">
	<OPTION VALUE="name">商品名称</OPTION>
	<OPTION VALUE="productid">商品编号</OPTION>
	<OPTION VALUE="barcode">商品条码</OPTION>
	<OPTION VALUE="supplier">供 货 商</OPTION></select> 
    <input name="kv" type="text" style="color:#FF0000"> &nbsp; <input type="submit" value=" 搜 索 "></form></td>
  </tr>
</table>
<script language="javascript">
<?php if($keyname) echo 'ProSearchAutoSelect("'.$keyname.'","'.$keyvalue.'");';?>
</script>
</body>
</html><?php db_close();?>
