
<?php
 function GenWareList($sql_count,$sql_query,$page_size,$page_url,$link_guider){
  $total_records=$GLOBALS['conn']->query('select count(*) '.$sql_count,PDO::FETCH_NUM)->fetchColumn(0); 
  if(empty($total_records)){
     return false;
  }
  $total_pages=(int)(($total_records+$page_size-1)/$page_size);
  $page=$_GET['page'];
  if(is_numeric($page)){
    if($page<1)$page=1;
    else if($page>$total_pages)$page=$total_pages;
  }else $page=1;
  $res=$GLOBALS['conn']->query($sql_query." limit ".($page_size*($page-1)).",$page_size",PDO::FETCH_ASSOC); 
  if($link_guider)$link_guider='linkguider="'.$link_guider.'"';
  $content='<TABLE cellSpacing=0 cellPadding=0 width="780" align="center" border="0" class="wareshow" id="wareshow" '.$link_guider.'><tr>';
  $jishu=0;
  foreach($res as $row){
    if($jishu>0 && $jishu%4==0) $content.='</tr><tr>';
   $content.='<td><div class="pimg"><a href="product.htm?id='.$row['id'].'"><img width="160" height="160" alt="'.$row['name'].'" border="0" onmouseover="ProductTip(this)" src="'.product_pic($row['id'],0).'" spec="'.$row['spec'].'" stoc="'.$row['stock0'].'"></a></div><div class="pbox"><a href="product.htm?id='.$row['id'].'" class="plink">'.$row['name'].'</a><span class="price3">￥'.round($row['price2'],2).'元</span><span class="price1">￥'.round($row['price1'],2).'元</span><img class="pbuy" src="images/gobuy.gif" width="22" height="12" alt="将该商品放入购物车" onClick="AddToCart('.$row['id'].')"></div></td>';
    $jishu++;
  }
  $content.='</tr></TABLE><TABLE cellSpacing=0 cellPadding=0 width="100%" align="center" border="0"><TR><TD align="center"><form style="margin:0px">共 <b>'.$total_records.'</b> 件商品&nbsp;&nbsp;';
  if($page==1) $content.='首页&nbsp;上一页&nbsp;';
  else $content.='<a href="'.$page_url(1).'" onclick="return JumpLinks(this)">首页</a>&nbsp;<a href="'.$page_url($page-1).'" onclick="return JumpLinks(this)">上一页</a>&nbsp;';
  if($page==$total_pages)$content.='下一页&nbsp;尾页';
  else  $content.='<a href="'.$page_url($page+1).'" onclick="return JumpLinks(this)">下一页</a>&nbsp;<a href="'.$page_url($total_pages).'" onclick="return JumpLinks(this)">尾页</a>';
  $content.='&nbsp;页次：<strong><font color="red">'.$page.'</font>/'.$total_pages.'</strong>页&nbsp; 每页<b>'.$page_size.'</b>件商品&nbsp;&nbsp; 转到第<input type="text" name="page" value="'.$page.'" size=3 maxlength=8 style="text-align:center" onFocus="this.select()" onkeyup="if(isNaN(value))execCommand(\'undo\')"  onkeydown="if(window.event.keyCode==13){this.form.jumpbtn.click();return false;}">页 &nbsp;<input type="button" name="jumpbtn" value="跳转" onclick="JumpToPage(this.form.page.value)"></form></TD></tr></TABLE>';
  return $content;
}  
?>


