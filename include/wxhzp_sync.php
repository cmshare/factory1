<?php
define('TBL_CATEGORY_DST','wxhzp.cm_store_category');
define('TBL_PRODUCT_DST' ,'wxhzp.cm_store_product');
define('TBL_CATEGORY_SRC','meray_db.mg_category');
define('TBL_PRODUCT_SRC' ,'meray_db.mg_product');

require("conn.php");

$conn=db_open();

$mode=$_GET["mode"];
if($mode==="1"){
  $maxid_src=$conn->query('select max(id) from '.TBL_CATEGORY_SRC)->fetchColumn(0);
  $maxid_dst=$conn->query('select max(id) from '.TBL_CATEGORY_DST)->fetchColumn(0);
  
  while($maxid_dst<$maxid_src){
    $maxid_dst++;
    $conn->exec('insert into '.TBL_CATEGORY_DST.' set id='.$maxid_dst.',pid=-1,cate_name=\'\',sort=0,pic=\'\',is_show=0,add_time=unix_timestamp()');
  }
  $conn->exec('update ('.TBL_CATEGORY_DST.' as a inner join '.TBL_CATEGORY_SRC.' as b on a.id=b.id) set a.pid=b.pid,a.cate_name=b.title,a.sort=b.sequence,a.is_show=(b.recommend>0)');
  
  function GetCategoryByPID($pid){
     return  $GLOBALS['conn']->query('select id,cate_name,pic from '.TBL_CATEGORY_DST.' where pid='.$pid.' and is_show order by sort desc,id desc')->fetchAll();
  }
  
  $rootCategory = GetCategoryByPID(0);
  foreach ($rootCategory as $k => $cat2) {
     $subcatId=$cat2['id'];
     $subCategory=GetCategoryByPID($subcatId);
     if($subcatId==1) foreach ($subCategory as $j => $cat3) {
        $subCategory[$j]['children'] =GetCategoryByPID($cat3['id']);
     }
     $rootCategory[$k]['children'] =$subCategory; 
  } 
  
  echo '{"status":200,"msg":"ok","data":',json_encode($rootCategory,JSON_UNESCAPED_UNICODE),'}';
}/* end of $mode==1 */


else if($mode==="2"){//同步产品（只能同步产品的部分信息）
  function gen_sql_insert($id){
    $product_code=substr('0000'.$id,-5);
    $product_image='http://www.gdhzp.com/uploadfiles/ware/'.$product_code.'.jpg';
    $sql='insert into '.TBL_PRODUCT_DST.' set id='.$id.',image=\''.$product_image.'\',slider_image=\'["'.$product_image.'"]\',store_name=\'\',cate_id=\'\',unit_name=\'\'';
    $sql.=',ot_price=0,price=0,vip_price=0,cost=0,postage=0,sort=0,stock=0,is_show=0,is_del=1,is_hot=0,is_new=0,is_benefit=0,is_best=0,is_good=0,give_integral=0,browse=0,sales=0,spec_type=0,temp_id=0,is_sub=0,is_postage=0,is_seckill=0,is_bargain=0,ficti='.rand(0,100);
    $sql.=',activity=\'1,2,3\',code_path=\'\',video_link=\'\',soure_link=\'\',store_info=\'\'';
    return $sql;
  }
  function gen_sql_update($rs,$origin_name){
    $product_image='http://www.gdhzp.com/uploadfiles/ware/'.substr('0000'.$rs['id'],-5).'.jpg';
    $cids=$rs['cids'];
    $cids_len=strlen($cids);
    if($cids_len>2 && $cids[0]==',' && $cids[$cids_len-1]==','){
      $cids=substr($cids,1,$cids_len-2);
    }
    else $cids='';
    $sql='update '.TBL_PRODUCT_DST.' set store_name="'.$rs['name'].'"';
    $sql.=',bar_code=\''.$rs['barcode'].'\'';
    $sql.=',image=\''.$product_image.'\'';
    $sql.=',cate_id=\''.$cids.'\'';
    $sql.=',is_del='.($rs['recommend']<0?'1':'0');//是否已删除（放入回收站）
    //只允许对目标商品的下架状态进行同步，不允许上架状态同步，否则会乱。
    //并且目标商品改变名字后也必须下架
    if($rs['recommend']<=0 || ($origin_name!==false && $rs['name']!=$origin_name)) $sql.=',is_show=0'; 
    $sql.=',give_integral='.$rs['score'];//产品积分
    $sql.=',unit_name=\''.str_replace('每','',$rs['unit']).'\'';//单位名称
    return $sql.' where id='.$rs['id'];
  }
  $id=@$_GET['id'];
  if(is_numeric($id) &&$id>0){
    if(($origin_name=$conn->query('select store_name from '.TBL_PRODUCT_DST.' where id='.$id)->fetchColumn(0))!==false || $conn->exec(gen_sql_insert($id))){
      $rs=$conn->query('select * from '.TBL_PRODUCT_SRC.' where id='.$id,PDO::FETCH_ASSOC)->fetch();
      if($rs && $conn->exec(gen_sql_update($rs,$origin_name))) echo 'OK';
    }
  }
}/* end of $mode==2 */
db_close();?>