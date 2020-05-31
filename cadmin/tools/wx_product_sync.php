<?php
exit(0);

require('../includes/dbconn.php');

define('TBL_PRODUCT_DST','wxhzp.cm_store_product');
define('TBL_PRODUCT_SRC','meray_db.mg_product');

db_open();

//exit($conn->exec('update '.TBL_PRODUCT_DST.' set temp_id=1,is_show=0'));

/*数据字典 wxhzp.cm_store_product
  store_name 商品名称
  keyword 关键词
  ot_price  市场价
  price 商品价格
  vip_price 会员价格
  cost  成本价
  postage 运费
  unit_name 单位名称
  sort 排序
  stock 库存
  is_show 是否上架
  is_hot 是否热卖产品
  is_new 是否首发新品
  is_benefit 是否促销单品(是否de优惠)
  is_best 是否精品推荐
  is_good 是否优品推荐
  is_del 是否已删除（放入回收站）
  give_integral 产品积分
  sales 销量
  ficti 虚拟销量
  browse 浏览量
  activity 活动优先级(秒杀、砍价、拼团)
  spec_type 规格类型(0:单规格 1:多规格)
  temp_id 运费模板ID
  is_sub 是否单独设置佣金
  is_postage 是否包邮
  is_seckill 是否开启秒杀
  is_bargain 是否开启砍价
  code_path 产品二维码地址
  video_link 主图视频地址
  soure_link 商品抓取源地址(淘宝京东1688类型)   
  store_info 商品简介 (产品详细描述见单独表cm_store_product_description) 
*/

function gen_sql_insert($id){
  $product_code=substr('0000'.$id,-5);
  $product_image='http://www.gdhzp.com/uploadfiles/ware/'.$product_code.'.jpg';
  $sql='insert into '.TBL_PRODUCT_DST.' set id='.$id.',image=\''.$product_image.'\',slider_image=\'["'.$product_image.'"]\',store_name=\'\',cate_id=\'\',unit_name=\'\'';
  $sql.=',ot_price=0,price=0,vip_price=0,cost=0,postage=0,sort=0,stock=0,is_show=0,is_hot=0,is_new=0,is_benefit=0,is_best=0,is_good=0,is_del=0,give_integral=0,browse=0,sales=0,ficti=0,spec_type=0,temp_id=0,is_sub=0,is_postage=0,is_seckill=0,is_bargain=0';
  $sql.=',activity=\'1,2,3\',code_path=\'\',video_link=\'\',soure_link=\'\',store_info=\'\'';
  return $sql;
}  

function gen_sql_update($rs){
  $id=$rs['id'];
  $product_code=substr('0000'.$id,-5);
  $product_image='http://www.gdhzp.com/uploadfiles/ware/'.$product_code.'.jpg';
  $cids=$rs['cids'];
  $cids_len=strlen($cids);
  if($cids_len>2 && $cids[0]==',' && $cids[$cids_len-1]==','){
     $cids=substr($cids,1,$cids_len-2);
  }
  else $cids='';
  $sql='update '.TBL_PRODUCT_DST.' set store_name="'.$rs['name'].'"';
  $sql.=',bar_code=\''.$rs['barcode'].'\'';
  $sql.=',image=\''.$product_image.'\'';
  $sql.=',slider_image=\'["'.$product_image.'"]\'';
  $sql.=',cate_id=\''.$cids.'\'';
  $sql.=',is_del='.($rs['recommend']<0?'1':'0');//是否已删除（放入回收站）
  $sql.=',is_show='.($rs['recommend']>0?'1':'0');//是否上架
  $sql.=',give_integral='.$rs['score'];//产品积分
  /* 这里设置无效，有效数据在单独在另一张表中cm_store_product_attr_value
  $sql.=',cost='.$rs['cost'];
  $sql.=',ot_price='.$rs['price1'];
  $sql.=',price='.$rs['price3'];//会员价(涵若铭妆批发价);//商品价格
  $sql.=',vip_price='.$rs['price2'];//会员价格
  $sql.=',stock='.$rs['stock0'];//库存 
  */
  $sql.=',ficti='.($rs['solded']+rand(0,100));//虚拟销量
  $sql.=',unit_name=\''.str_replace('每','',$rs['unit']).'\'';//单位名称
  return $sql.' where id='.$id;
}

//填充空白产品
$maxid_src=$conn->query('select max(id) from '.TBL_PRODUCT_SRC)->fetchColumn(0);
$maxid_dst=$conn->query('select max(id) from '.TBL_PRODUCT_DST)->fetchColumn(0);
while($maxid_dst<$maxid_src){
  $maxid_dst++;
  $conn->exec(gen_sql_insert($maxid_dst));
}

//同步产品参数（除价格/库存/详情等参数）
$query=$conn->query('select * from '.TBL_PRODUCT_SRC,PDO::FETCH_ASSOC);
foreach($query as $rs){
  //exit(gen_sql_update($rs));
  $conn->exec(gen_sql_update($rs));   
}

db_close();

echo '<p>All products updated!</p>';
?>