<?php require('includes/dbconn.php');

CheckLogin();
 
session_start();
$showcost=@$_SESSION['showcost'];

$sharefellow=false;
if(!CheckPopedom('PRODUCT')){
  if(CheckPopedom('SHARE')) $sharefellow=true;
  else PageReturn('非法访问！',0);
}

function PageHalt($errmsg){
 echo '<script>alert("'.$errmsg.'");parent.document.forms["myform"].ConfirmButton.disabled=false;</script>';
 db_close();
 exit(0);
}

function IsShareBrand($PID){
  global $conn;
  while($PID){
    $row=$conn->query('select pid,shared From mg_category Where id='.$PID,PDO::FETCH_ASSOC)->fetch();
     if($row){
       if($row['shared'])return true; 
       else $PID=$row['pid'];		   
     }
     else return false;
  }
}

function CheckNameRepetition($checkname,$id){
  $recommend=$GLOBALS['conn']->query('select recommend from mg_product where name=\''.$checkname.'\' and id<>'.$id,PDO::FETCH_NUM)->fetchColumn(0);
  if($recommend!==false){
     if($recommend>0)PageHalt('出错了，该商品名称已经存在于上架的商品列表中，请检查！');
     else PageHalt('出错了，该商品名称已经存在于下架的商品列表中，请检查！');
  }
}

function CheckBarcodeRepetition($checkcode,$id){ 
  if($checkcode){
    $recommend=$GLOBALS['conn']->query('select recommend from mg_product where barcode=\''.$checkcode.'\' and id<>'.$id,PDO::FETCH_NUM)->fetchColumn(0);
    if($recommend!==false){
      if($recommend>0)PageHalt('出错了，该条码号已经存在于上架的商品列表中，请检查！');
      else PageHalt('出错了，该条码号已经存在于下架的商品列表中，请检查！');
    }
  }
}

$productid=$_POST['id'];
$origin_recommend=$_POST['recommend'];	

if(!is_numeric($productid) || $productid<=0 || !is_numeric($origin_recommend) || $origin_recommend<-1) PageHalt('参数错误！');
	
$productname=FilterText(trim($_POST['productname']));
if(!$productname) PageHalt('商品名称不能为空！');

$brand=$_POST['brand'];
if(!is_numeric($brand) || ($brand<=0)) PageHalt('没有选择品牌分类！');
if($sharefellow && !IsShareBrand($brand)) PageHalt('操作权限错误！');

$cids=$_POST['cids'];
	
$sql="update mg_product set name='$productname',brand=$brand,cids='$cids'"; 

$barcode=FilterText(trim($_POST['barcode']));
if(empty($barcode))$sql.=',barcode=null';
else if(is_numeric($barcode))$sql.=",barcode='$barcode'";
else PageHalt('无效的商品条码！');

$weight=trim($_POST['weight']);
if(empty($weight))$sql.=',weight=null';
else if(is_numeric($weight) && $weight>0)$sql.=',weight='.$weight; 
else PageHalt('无效的商品重量！');

if(!$sharefellow){
  $price1=trim($_POST['price1']);
  $price2=trim($_POST['price2']);
  $price3=trim($_POST['price3']);
  $price4=trim($_POST['price4']);
  if(!is_numeric($price1) || !is_numeric($price2) || !is_numeric($price3) || !is_numeric($price4))PageHalt('请填写价格参数！');

  //先检查批发价和零售价	
  if($price3==0)PageHalt('请正确填写批发价！');
  else if($price1<$price2 || $price2<$price3 || $price3<$price4) PageHalt('价格分等不合理，请核实！');

  $score=trim($_POST['score']);
  if(!is_numeric($score) || $score<0)PageHalt('请填写积分！');

  if($showcost){
    $cost=trim($_POST['cost']);
    if($cost!='' && is_numeric($cost))$sql.=',cost='.round($cost,2);
    else PageHalt('请填写成本价');
  }
  $sql.=",price1=round($price1,2),price2=round($price2,2),price3=round($price3,2),price4=round($price4,2),score=$score";
}

if($origin_recommend==0){
  $solded=$_POST['solded'];
  if(is_wholenumber($solded)) $sql.=',solded='.$solded;
  else PageHalt('参数错误！');
}
else if($origin_recommend==-1){
  $sql.=',solded=0,recommend=1';
}

$description=$_POST['description'];
if(empty($description))PageHalt('商品描述不能为空！');
else $description=trim($description);

$addtime=trim($_POST['addtime']);
if(!$addtime || !($addtime=strtotime($addtime)))PageHalt('上架时间无效！'); 

$supplier=FilterText(trim($_POST['supplier']));
$unit=FilterText(trim($_POST['unit']));
$spec=FilterText(trim($_POST['spec']));

$sql.=",supplier='$supplier',unit='$unit',spec='$spec',description='$description',addtime=$addtime,updatetime=unix_timestamp() where id=$productid";

db_open();
CheckNameRepetition($productname,$productid);
CheckBarcodeRepetition($barcode,$productid); 
if($conn->exec($sql)){
  echo '<script>parent.UpdateProductHTML(',$productid,');</script>';
  PageHalt('商品保存成功！');
}
//response.write "<script>alert(""商品保存成功！"");parent.location.reload();</script>"
//server.execute("htmgen.asp")
db_close();
?>
