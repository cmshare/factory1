<?php require('include/conn.php');
require('user/m_reviews.php');

OpenDB();

#遍历子级品牌分类 
function brand_sort($selec){
 global $conn,$CatList;
 $res=$conn->query('select id from `mg_category` where parent = '.$selec.' order by sortorder',PDO::FETCH_NUM);
 foreach($res as $row){
    $CatList.=', '.$row[0];
    brand_sort($row[0]);
 }
}

if(@$_POST['action']=='get'){
  ShowProduct();
  CloseDB();
  exit(0);
}

function ShowProduct(){    
  global $conn,$CatList,$LoginUserID,$LoginUserGrade;
  $id=@$_GET['id'];
  if(is_numeric($id) && $id>0){
    $rowware=$conn->query('select * from `mg_product` where id='.$id.' and recommend>=0',PDO::FETCH_ASSOC)->fetch();  
  }
  if(empty($rowware)){
    echo '<br><br><p align=center>此产品不存在或已经下架！<br><br><a href="'.WEB_ROOT.'">点击这里返回主页</a></p>';
    CloseDB();
    exit(0);
  }
   
  CheckLogin(0);

  #遍历父级品牌分类 
  $LinkSortGuider=''; 
  $brand = $rowware['brand'];
  $PID = $brand; 
  while($PID){
    $row=$conn->query('select id,title,parent,isbrand from `mg_category` where id='.$PID,PDO::FETCH_ASSOC)->fetch();
    if($row){
      if($row['isbrand'] && empty($ProductBrand)) $ProductBrand=$row['title']; 
    }
    else{
      echo '<script LANGUAGE="javascript">alert("您输入的参数非法，请正确操作！");history.go(-1);</script>';
      CloseDB();
      exit(0);
    }
    $LinkSortGuider = '&nbsp;&gt;&gt;&nbsp;<a href="brandlist.htm?cid='.$row['id'].'">'.$row['title'].'</a>'.$LinkSortGuider;
    if(empty($ParentBrand)) $ParentBrand=$row['parent'];
    $PID = $row['parent'];
  }

  if(empty($ProductBrand)) $ProductBrand='其它品牌';  


  $CatList =$brand;
  brand_sort($brand);

  $StarRecommend=$rowware['recommend'];
  if($StarRecommend<3)$StarRecommend=3;
  else if($StarRecommend>9)$StarRecommend=5;
  else $StarRecommend=4;?>
        
  <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center" border="0" bgcolor="#FFFFFF">
  <TR>
     <TD width="100%" height="28" valign="middle"  background="images/pdbg01.gif" style="padding-left:25px"><img src="images/arrow2.gif" width="6" height="7">&nbsp;當前位置：&nbsp;<a href=".">首页</a> &gt;&gt; <a href="brandlist.htm">产品中心</a><?php echo $LinkSortGuider;?></TD>
     </TR>
     <TR>
        <TD valign="top"  style="padding-top:10px;padding-bottom:20px;">
          <!-------商品基本信息---------->  
          <table width="800" bgcolor="#FFFFFF" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
             <td width="310" style="padding-left:10px"><a href="viewpic.htm?id=<?php echo $id;?>" target="_blank"><IMG  width=300 height=300 border=0  src="<?php echo product_pic($rowware['id'],2);?>"></a></td>
             <td width="490" valign="top">
               <table width="100%"  border="0" cellpadding="4" cellspacing="0" bgcolor="#f2f2f2" id="TProduct">
               <tr>
               	 <tr><td height=50 colspan=4 class="producttitle"><?php
                 echo $rowware['name'];
                 if(($rowware['onsale']&0xf)>0) echo '<img src="images/onsale.gif" height=16 width=16 alt="特价产品">';?></td>
               </tr>
               <tr>
                 <td width="18%" height="30" bgcolor="#f7f7f7">【商品编号】</td>
                 <td width="32%" bgcolor="#FFFFFF"><?php echo substr('0000'.$id,-5);?></td>
                 <td width="18%" bgcolor="#f7f7f7">【商品品牌】</td>
                 <td width="32%" bgcolor="#FFFFFF"><font color="#FF0000"><?php echo $ProductBrand;?></font></td>
               </tr>
               <tr>
                 <td height="30" bgcolor="#f7f7f7">【推荐指数】</td>
                 <td bgcolor="#FFFFFF"><img src="images/<?php echo $StarRecommend;?>star.gif" width=64 height=12></td>
                 <td height="30" bgcolor="#f7f7f7">【<?php echo ($rowware['recommend']>0)?'上':'下';?>架时间】</td>
                 <td bgcolor="#FFFFFF"><?php echo date('Y-m-d',$rowware['addtime']);?></td>
               </tr>  
               <tr>
                 <td height="30" bgcolor="#f7f7f7">【商品规格】</td>
                 <td bgcolor="#FFFFFF"><?php echo $rowware['spec'];?>&nbsp;<?php echo $rowware['unit'];?></td>
                 <td bgcolor="#f7f7f7">【商品库存】</td>
                 <td bgcolor="#FFFFFF"><?php echo ($rowware['stock0']>0)?'<font color="#00BB00">有现货</font>':'<font color="#FF0000">无现货</font>';?></td>
               </tr>
               <tr><td colspan="4" height="10" bgcolor="#FFFFFF"></td>
               </tr>
               <?php if(OWN_ICP){?>             
               <tr>
	          <td height="30" bgcolor="#f7f7f7">【零 售 价】</td>
                  <td bgcolor="#FFFFFF" style="font-weight:bold;text-decoration:line-through">￥<?php echo round($rowware['price1'],2);?>元</td>
                  <td bgcolor="#f7f7f7">【<font color=#FF5500>ＶＩＰ价</font>】</td>
                  <td bgcolor="#FFFFFF" style="font-weight:bold;color:#FF6600">￥<?php echo round($rowware['price2'],2);?>元</td>
               </tr>
               <tr>
                  <td height="30" bgcolor="#f7f7f7">【加盟代理】</td>
                  <td bgcolor="#FFFFFF"><?php
                  if($LoginUserID>0 && $LoginUserGrade==4)echo '<b><font color=#ff0000>￥'.round($rowware['price4'],2).'元</font></b>';
                  else echo '<font color=#888888>非等级查看</font>';?></td>
                  <td bgcolor="#f7f7f7">【<font color=#FF5500>批 发 价</font>】</td>
                  <td bgcolor="#FFFFFF" style="font-weight:bold;color:#FF0000"><?php
                  if($LoginUserID>0 && ($LoginUserGrade==3 || $LoginUserGrade==4))echo '<b><font color=#ff0000>￥'.round($rowware['price3'],2).'元</font></b>';
                  else echo '<font color=#888888>非等级查看</font>';?></td>
               <tr>
               </table>
               <table width="490" border="0" cellpadding="0" cellspacing="0">
               	<?php if(($rowware['onsale']&0xf)>0 && time()<$rowware['onsale'] && ($LoginUserGrade==3 || $LoginUserGrade==4)) {?>
               <tr><td colspan="4" valign="bottom">
               	  <table width="100%" height="30" border="0" cellpadding="0" cellspacing="0" id="flashsale">
               	  <tr><td width="19%"></td><td width="31%" style="font-weight:bold;font-size:12pt;color:#FFFFFF">￥<?php echo round($rowware['price0'],2);?>元</td><td width="19%"></td><td width="31%"><font id="life<?php echo $id;?>" deadline="<?php echo $rowware['onsale'];?>">正在载入中...</font></td></tr></table>
               	  </td></tr><?php
                }?>
               <tr valign="bottom">
               	<td width="50%" height="55" ><div class="bdsharebuttonbox"><div id="qrcode"></div><a class="bds_more" href="#" data-cmd="more">分享到：</a><a class="bds_qzone" title="分享到QQ空间" href="#" data-cmd="qzone"></a><a class="bds_tsina" title="分享到新浪微博" href="#" data-cmd="tsina"></a><a class="bds_tqq" title="分享到腾讯微博" href="#" data-cmd="tqq"></a><a class="bds_renren" title="分享到人人网" href="#" data-cmd="renren"></a><a class="bds_weixin" title="分享到微信" href="#" data-cmd="weixin"></a></div></td>
               	<td width="50%"><a href="javascript:AddToCart(<?php echo $id;?>)"><img src="images/add2cart.gif" width="182" height="41" border="0"></a><a href="javascript:AddToFavor(<?php echo $id;?>)"><img src="images/add2fav.gif" width="55" height="22" border="0"></a></td>
               </tr><?php
               }?>
               </table>
       
             </td>
           </tr>  
           </table>

  <TABLE cellSpacing=0 cellPadding=0 width="92%" align="center" border="0" style="color:#8f8f8f">
  	
  <TR><td><b>1. 价格等级</b>：①本站所标识的零售价是指我司直营门店及加盟店的零售价；②VIP价为持我司专属VIP卡或其他合作联盟卡（如<font color="#FF0000">大众书局易购卡</font>等）的会员价；③代理/批发价是指我司加盟店或产品代理经销商的供货价格，具体招商及报价政策请咨询我司工作人员。</td>
  </TR>
  <!--TR><td><b>2. 团购说明</b>：我司已开通在线团购业务，团购尊受VIP价。团购流程为 [注册会员 -> 在线下单 -> 在线付款 -> 门店提货]。付款前，请联系客服确认订单。如需快递，运费自理。</td>
  </TR-->
  <TR><td><b>2. 销售地址</b>：零售客户请直接至各大直营/加盟店；代理加盟及购物卡兑购请至－南京市中山路81号华夏大厦16层（南京铭悦日化用品有限公司，电话025-83222007/84730490）。</td>
  </TR>		
  </TABLE>                  

  <table width="800" bgcolor="#FFFFFF" border="0" align="center" cellpadding="0" cellspacing="0" style="BORDER-COLLAPSE: collapse; BORDER-right:#CCCCCC 1px solid;border-left:1px solid #CCCCCC;border-bottom:1px solid #CCCCCC;">
  <tr>   
     <td height="56" style="BACKGROUND-IMAGE:url(images/wareresume.gif); BACKGROUND-REPEAT: no-repeat;BACKGROUND-POSITION: center center%"></td>
  </tr>  
  <tr>
     <td style="padding-left:12px;padding-top:6px;line-height: normal" valign="top"><div style="width:755px;OVERFLOW:hidden;" oncontextmenu="event.cancelBubble=true;return false;"><?php echo $rowware['description'];?></div></td>
  </tr>
  </table>

  <!-------商品评价---------->
  <table width="800" bgcolor="#FFFFFF" border="0" align="center" cellpadding="0" cellspacing="0" style="BORDER-COLLAPSE: collapse; BORDER-right:#CCCCCC 1px solid;border-left:1px solid #CCCCCC;border-bottom:1px solid #CCCCCC;">
  <tr><td height="55" style="BACKGROUND-IMAGE:url(images/wareremark.gif); BACKGROUND-REPEAT: no-repeat;BACKGROUND-POSITION: center center"></td>
  </tr>  
  <tr>
     <td width="100%"><div id="productreviews"><?php show_product_reviews($LoginUserID,$id);?></div><form name="reviews" method="post" action="/user/review.php" target="dummyframe" style="margin:0px">
     <table style="border:1px solid #dfdfdf;" width="99%" align="center" cellSpacing="0" cellPadding="0">
     <tr>
       <td width="100%" colspan="2"><input type="hidden" name="mode" value="add"><input type="hidden" name="productid" value="<?php echo $id;?>">
         <textarea name="remark" rows="3" wrap="VIRTUAL" style="width: 100%; font-size: 9pt; border: 1 solid #DFDFDF;" cols="20"><?php if($LoginUserID==0)echo '登录本站后，您才能发表评论！！！';?></textarea>
       </td>
     </tr>
     <tr>
       <td width="80%"><select name="vote" size="1"><option value="0">　　打 分</option><option value="1">☆</option><option value="2">☆☆</option><option value="3">☆☆☆</option><option value="4">☆☆☆☆</option><option value="5">☆☆☆☆☆</option></select>
       </td>
       <td width="20%" align="right" nowrap>
         (256个字以内)&nbsp;<input type="submit" name="send_review" <?php if($LoginUserID==0) echo 'disabled';?> value="发表评论" onclick="if(this.form.remark.value==''){alert('评论不能为空！');return(false);}">
       </td>
     </tr>
     </table></form>       
     </td>
  </tr>
  <?php if(OWN_ICP) echo '<tr><td height="80" style="BACKGROUND-IMAGE:url(images/buysteps.gif); BACKGROUND-REPEAT: no-repeat;BACKGROUND-POSITION: center bottom"></td></tr>';?>
  </table>
 <!-------同类商品滚动栏 开始---------->
 <table width="800" bgcolor="#FFFFFF" border="0" align="center" cellpadding="0" cellspacing="0" style="BORDER-COLLAPSE: collapse; BORDER-right:#CCCCCC 1px solid;border-left:1px solid #CCCCCC;border-bottom:1px solid #CCCCCC;">
 <tr>
   <td height="56" colspan="2" style="BACKGROUND-IMAGE:url(images/wareTongLeiHot.gif); BACKGROUND-REPEAT: no-repeat;BACKGROUND-POSITION: center center"></td>
 </tr
 <TR>
   <TD align="center" style="BACKGROUND-POSITION: left bottom; BACKGROUND-IMAGE:url(images/clientbot.jpg); BACKGROUND-REPEAT: repeat-x"><DIV id="MarqueeDemoA" style="OVERFLOW: hidden; WIDTH: 760px; COLOR: #ffffff;">
      <TABLE cellSpacing=0 cellPadding=0 width="100%" align="center" border="0">
      <tr>
         <TD id="MarqueeDemoB">
            <TABLE cellSpacing=0 cellPadding=0 border="0" class="WareShow">
               <TR><?php
$res=$conn->query('select id,name,price1,price2 from mg_product where brand in ('.$CatList.') and recommend>0 and id<>'.$id.' order by recommend desc,addtime desc limit 12',PDO::FETCH_ASSOC);
foreach($res as $row){                 
  echo '<td><div class="pimg"><a href="product.htm?id='.$row['id'].'"><img width="160" height="160" alt="'.$row['name'].'" border="0" onmouseover="ProductTip(this)" src="'.product_pic($row['id'],0).'"></a></div><div class="pbox"><a href="product.htm?id='.$row['id'].'" class="plink">'.$row['name'].'</a><!--span class="price3">￥'.round($row['price2'],2).'元</span><span class="price1">￥'.round($row['price1'],2).'元</span><img class="pbuy" src="images/gobuy.gif" width="22" height="12" alt="将该商品放入购物车" onClick="AddToCart('.$row['id'].')"--></div></td>';
}?>
                  </tr>
                  </table>
                </td>
                <td id="MarqueeDemoC"></td>
              </tr>
              </TABLE>
              </div>
            </TD>
          </TR>
          </TABLE>
<!-------同类商品滚动栏 结束---------->      
                   
        </TD>
      </TR>
    </TABLE>
<!-------商品信息结束---------->
        </td>
     </tr>
     </TABLE><?php
  return $id;
}

$PageTitle='产品详情－南京铭悦日化用品有限公司';
require('include/page_head.php');?>	

<TABLE align="center" width="1000"  border="0" cellSpacing=0 cellPadding=0 background="images/client_bg_mid.gif">
<TR><TD colspan=2 height="1"></TD></TR>	
<TR valign="top">
  <TD background="images/client_bg_left.jpg" width=190" height="100%">
    <TABLE cellSpacing="0" cellPadding="0" width="190" height="100%" border="0">
    <tr><td height="1%"><SCRIPT language="JavaScript" src="include/guide_sort.js" type="text/javascript"></SCRIPT></td></tr>
    <tr><td height="99%" background="images/left_bg.gif"></td></tr>
    </table> 
  </TD>
  <TD width="810"  style="BORDER-right:#FF67A0 1px solid;" id="contentbox"></TD>
</TR>
</TABLE>
<script>
 AsyncPost("action=get","product.php"+window.location.search,OnLoadProduct); 
 function OnLoadProduct(info){
   var obj=document.getElementById("contentbox");
   if(info && obj)
   { var lifeid="life"+htmRequest("id"); 
     obj.innerHTML=info;
     MarqueeInit();
     window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];
     obj=document.getElementById(lifeid);
     if(obj)clock_lifetime2(lifeid,obj.getAttribute("deadline"));
   }
   else obj.innerHTML="<p align=center>服務器請求失敗，可能是您的網速太慢，請刷新重試!</p>";
 }
</SCRIPT><?php
require('include/page_bottom.php');
CloseDB();?>
</body>
</html> 
