<?php
exit('disabled');
require('../includes/dbconn.php');

define('TBL_CATEGORY_DST','wxhzp.cm_store_category');
define('TBL_CATEGORY_SRC','meray_db.mg_category');

db_open();

$maxid_src=$conn->query('select max(id) from '.TBL_CATEGORY_SRC)->fetchColumn(0);
$maxid_dst=$conn->query('select max(id) from '.TBL_CATEGORY_DST)->fetchColumn(0);

while($maxid_dst<$maxid_src){
  $maxid_dst++;
  $conn->exec('insert into '.TBL_CATEGORY_DST.' set id='.$maxid_dst.',pid=-1,cate_name=\'\',sort=0,pic=\'\',is_show=0,add_time=unix_timestamp()');
}
$conn->exec('update ('.TBL_CATEGORY_DST.' as a inner join '.TBL_CATEGORY_SRC.' as b on a.id=b.id) set a.pid=b.pid,a.cate_name=b.title,a.sort=b.sequence,a.is_show=(b.recommend>0)');
db_close();

echo '<p>All category updated!</p>';
?>