<?php require('../includes/dbconn.php');
exit(0);
db_open();

function GenerateProductMap(){
  $ret="var ProductMap=[\r\n";
  $sql="select a.id,a.name,a.brand,a.cids,b.catid from mg_product as a left join mg_sort as b on a.category=b.id";
  $query=$GLOBALS['conn']->query($sql,PDO::FETCH_ASSOC);
  foreach($query as $rs){
    $cid=$rs['catid'];
    if($cid)$cid=0;
    $ret.="{id:{$rs['id']},brand:{$rs['brand']},cid:$cid,cids:\"{$rs['cids']}\",title:\"{$rs['name']}\"},\r\n";
  }
  $ret.="{id:0}];\r\n";
  return $ret; 
}

function category_contain($cid1,$cid2){
  global $conn;
  if($cid2<=0 || $cid1==$cid2)return true;
  else if($cid1<=0)return false;
  while($cid2>0){
     $cid2=$conn->query('select pid from mg_category where id='.$cid2)->fetchColumn(0);
     if($cid2>0 && $cid2==$cid1) return true;
  }
  return false;
}

function get_cids($cid){
  global $conn;
  $cids="";
  while($cid>1){
    if($cids) $cids=$cid.",".$cids;
    else $cids=$cid;
    $cid=$conn->query('select pid from mg_category where id='.$cid)->fetchColumn(0);
  }
 return $cids;
}

$jishu=0;
$sql="select a.id,a.name,a.brand,a.cids,b.catid,b.title as sortname from mg_product as a left join mg_sort as b on a.category=b.id";
$query=$GLOBALS['conn']->query($sql,PDO::FETCH_ASSOC);
foreach($query as $rs){
  $cid1=$rs['brand'];
  $cid2=$rs['catid'];
  
  if($cid1==$cid2){
    $cid2=0;
  }
  else if($cid2 && !$cid1){
    $cid1=$cid2;
    $cid2=0;
  }
  if($cid1>0 && $cid2>0){
    if(category_contain($cid1,$cid2)){
      $cid2=0;
    }
    else if(category_contain($cid2,$cid1)){
      $cid1=$cid2;
      $cid2=0;
    }
  }
  if($cid1>0 && category_contain(1,$cid1))$brand=$cid1;
  else if($cid2>0 && category_contain(1,$cid2))$brand=$cid2;
  else $brand=7;

  $cids=get_cids($cid1);
  $cids2=get_cids($cid2);
  if($cids2){
    if(!$cids)$cids=$cids2;
    else $cids=$cids.",".$cids2;
  }  
  if($cids)$conn->exec('update mg_product set brand='.$brand.',cids=",'.$cids.'," where id='.$rs['id']);
  else $conn->exec('update mg_product set brand='.$brand.',cids=null where id='.$rs['id']);
  $jishu++;
}
?>
</body>
</html> 

<?php
db_close();
echo $jishu.'<br>';
echo time();
?>
