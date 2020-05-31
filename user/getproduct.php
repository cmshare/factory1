<?php require('../include/conn.php');
if($_POST['action']=='get'){
 //ob_clean();
  $ProductID=$_POST['id'];
  if(is_numeric($ProductID)){
    db_open();
    $row=$conn->query('select id,stock0,price0,price1,price2,price3,price4,onsale,brand from `mg_product` where id='.$ProductID,PDO::FETCH_NUM)->fetch();
    if($row){
       echo $row[0].'|'.$row[1].'|'.round($row[2],2).'|'.round($row[3],2).'|'.round($row[4],2).'|'.round($row[5],2).'|'.round($row[6],2).'|'.($row[7]&0xf).'|'.($row[7]&~0xf).'|';
       $brand=$row[8];
       $cidsel='select id,name,price0,price1,price3,onsale from `mg_product` where brand='.$brand.' and recommend>0 and id<>'.$ProductID.' order by recommend desc limit 12';
       $query=$conn->query($cidsel,PDO::FETCH_ASSOC);
       $total_record=0;
       foreach($query as $rs){
         if($total_record++==0) echo '<TABLE cellSpacing=0 cellPadding=0 border="0" class="WareShow">';
         $price_pf=(($rs['onsale']&0xf)>0 && $rs['onsale']>time())?$rs['price0']:$rs['price3'];
         echo '<td><div class="pimg"><a href="/products/'.$rs['id'].'.htm"><img width="160" height="160" src="'.product_pic($rs['id'],0).'" alt="'.$rs['name'].'" border="0"></a></div><div class="pbox"><a href="/products/'.$rs['id'].'.htm" class="plink">'.$rs['name'].'</a><span class="price3">￥'.round($price_pf,2).'元</span><span class="price1">￥'.round($rs['price1'],2).'元</span><img class="pbuy" src="/images/gobuy.gif" width="22" height="12" alt="将该商品放入购物车" onClick="AddToCart('.$rs['id'].')"></div></td>';
       } 
       if($total_record) echo '</tr></table>';            
    }
    db_close();
  }
}?>