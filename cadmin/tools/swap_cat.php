<?php require('../includes/dbconn.php');
db_open();
exit(0);
if(@$_POST['mode']=='move'){ 
  $cid1=@$_POST['cid1'];
  $cid2=@$_POST['cid2'];
  if(is_numeric($cid1) && $cid1>0 && is_numeric($cid2) && $cid2>0 && $cid1!=$cid2){
      $sum_cid=$cid1+$cid2;
      $conn->exec("update mg_product set brand=$sum_cid-brand where brand=$cid1 or brand=$cid2");
      $conn->exec("update mg_category set pid=$sum_cid-pid where pid=$cid1 or pid=$cid2");

      $rs1=$conn->query("select * from mg_category where id=$cid1",PDO::FETCH_ASSOC)->fetch();
      $rs2=$conn->query("select * from mg_category where id=$cid2",PDO::FETCH_ASSOC)->fetch();
      $conn->exec("update mg_category set pid={$rs2['pid']},title='{$rs2['title']}',sequence={$rs2['sequence']},sortindex={$rs2['sortindex']},recommend={$rs2['recommend']},property={$rs2['property']},shared={$rs2['shared']},isbrand={$rs2['isbrand']} where id=$cid1");
      $conn->exec("update mg_category set pid={$rs1['pid']},title='{$rs1['title']}',sequence={$rs1['sequence']},sortindex={$rs1['sortindex']},recommend={$rs1['recommend']},property={$rs1['property']},shared={$rs1['shared']},isbrand={$rs1['isbrand']} where id=$cid2");

      PageReturn("success");
  }
  PageReturn("err");
}

db_close();
?>
 
<form method="post" action="?" onsubmit="if(!confirm(this.cid1.value+':'+this.cid2.value))return false;">
<input type="hidden" name="mode" value="move">
<input type="text" name="cid1"><input type="text" name="cid2"><input type="submit" value="提交">
</form>
</body>
</html> 
