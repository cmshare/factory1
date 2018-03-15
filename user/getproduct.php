<?php require('../include/conn.php');
if($_POST['action']=='get'){
 //ob_clean();
  $ProductID=$_POST['id'];
  if(is_numeric($ProductID)){
    OpenDB();
    $row=$conn->query('select id,stock0,price0,price1,price2,price3,price4,onsale,brand,category from `mg_product` where id='.$ProductID,PDO::FETCH_NUM)->fetch();
    if($row){
       echo $row[0].'|'.$row[1].'|'.round($row[2],2).'|'.round($row[3],2).'|'.round($row[4],2).'|'.round($row[5],2).'|'.round($row[6],2).'|'.($row[7]&0xf).'|'.($row[7]&~0xf).'|';
       $brand_id=$row[8];
       $category_id=$row[9];
       $res=$conn->query('select id,name,price0,price1,price3,onsale from `mg_product` where brand='.$brand_id.' or category='.$category_id.' order by recommend desc limit 12',PDO::FETCH_ASSOC);
       $total_record=0;
       foreach($res as $row){
         if($total_record++==0) echo '<TABLE cellSpacing=0 cellPadding=0 border="0" class="WareShow">';
         $price_pf=(($row['onsale']&0xf)>0 && $row['onsale']>time())?$row['price0']:$row['price3'];
         echo '<td><div class="pimg"><a href="/products/'.$row['id'].'.htm"><img width="160" height="160" src="'.product_pic($row['id'],0).'" alt="'.$row['name'].'" border="0"></a></div><div class="pbox"><a href="/products/'.$row['id'].'.htm" class="plink">'.$row['name'].'</a><span class="price3">￥'.round($price_pf,2).'元</span><span class="price1">￥'.round($row['price1'],2).'元</span><img class="pbuy" src="/images/gobuy.gif" width="22" height="12" alt="将该商品放入购物车" onClick="AddToCart('.$row['id'].')"></div></td>';
       } 
       if($total_record) echo '</tr></table>';
    }
    CloseDB();
  }
}
?>
