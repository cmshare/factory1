<?php
function get_star($jishu){
  if(empty($jishu))return '未评分';
  else{
    $stars='';
    for($i=0;$i<$jishu;$i++) $stars.='☆';
    return $stars;
  }
}
 
function show_product_reviews($user_id,$product_id){
  $sql='select `mg_review`.*,`mg_users`.id as userid from `mg_review` left join `mg_users` on `mg_review`.username=`mg_users`.username where `mg_review`.productid='.$product_id.' and (`mg_review`.audit=1 or `mg_users`.id='.$user_id.') order by `mg_review`.actiontime desc limit 20';
  $res=$GLOBALS['conn']->query($sql,PDO::FETCH_ASSOC);
  $row=$res->fetch();
  if(empty($row)) echo '<p align=center>暂没有相关评论！欢迎您积极发言。请文明用词，谢谢！</p>';
  else do{?>
<table width="99%" border=0 align="center" cellSpacing="0" cellPadding="0" style="background-color: #f8f9f9; border: 1px solid #dfdfdf;margin-bottom:5px">
<tr>
  <td width="25%" style="FILTER: glow(Color=yellow,Strength=3);" >&nbsp;<img border="0" src="/images/foot.gif" width="16" height="16" align="absMiddle">&nbsp;<font id="author_nick_name" color="#003399"><?php echo $row['username'];?></font></td>
  <td width="16%"><?php echo get_star($row['vote']);?></td>
  <td width="21%"><img src="/images/posttime.gif" width="11" height="11" align="absMiddle"> <?php echo date('Y-m-d H:i:s',$row['actiontime']);?></td>
  <td width="18%">IP: <?php echo $row['ip'];?></td>
  <td width="10%" align="center"><?php echo (empty($row['audit']))?'未审核':'已审核';?></td>
  <td width="10%" align="right"><?php if($user_id==$row['userid'] && empty($row['audit'])) echo '<a href="#" onclick=" if(confirm(\'确定要删除这条评论吗？\')) { dummyframe.location.href=\'/user/review.php?mode=del&reviewid='.$row['id'].'\'; } return false;"><img border=0 src="/images/del.gif"></a>'; else echo '<img border=0 src="/images/del1.gif">';?></td>
</tr>
<tr>
  <td width="100%" colspan="6" style="padding-left:25px;border-top: 1px solid #dfdfdf;"><?php
  	 if($row['remark']) echo $row['remark'];
  	 if($row['reply']){
  	   echo '<br><img border=0 src="/images/dot.gif" WIDTH=10 HEIGHT=10>&nbsp;<font color="#FF8000"><u><b>管理员回复</b></u>：</font>';
           echo $row['reply'];
         }?></td></tr></table><?php
   $row=$res->fetch();
  }while($row);  
}?>
