<?php require('include/conn.php');
$PageKeywords="化妆品,化妆品批发,最低价,南京化妆品批发,韩国化妆品批发,进口化妆品批发,欧美化妆品批发,上海化妆品批发,广东美容化妆品网,化妆品批发市场,品牌化妆品批发,香水批发";
$PageDescription="涵若铭妆(www.gdhzp.com)化妆品批发网,提供各种品牌化妆品批发,包括护肤品批发,彩妆批发,洗发水批发,沐浴露批发,精油香水批发,一手货源好,最低价格低,现货供应,质量保证！原装进口韩国化妆品批发,欧美化妆品批发,进口化妆品批发,品牌化妆品批发,广东美容化妆品网是全国最大最专业的化妆品批发进货渠道综合服务平台。";
$PageTitle="【涵若铭妆】－韩国化妆品批发－进口化妆品批发－南京化妆品批发网";
$Pagination="-1";
require("include/page_head.php");
OpenDB();

$row=$conn->query('select notify,bulletinenable,bulletintitle,bulletincontent,webstatenabled,advs_mid_show,advs_mid_url from `mg_configs`',PDO::FETCH_NUM)->fetch();
if($row[1]){?>
  <TABLE ID="SysBulletin" cellSpacing=0 cellPadding=0 width=530 align="center"  border="0" style="POSITION:absolute;left:expression((body.clientWidth-530)/2);TOP:180px;color:#FFFFFF;z-index:1000">
  <TR onmousedown="DragStart()" onmousemove="DragMove()" onmouseup="DragStop()" onmouseout="DragStop()" style="cursor:pointer">
     <TD width="10" background="images/wnd/wndcornerlt.jpg"></TD>
     <TD width="80" height="29" background="images/wnd/wndtitleleft.jpg"></TD>
     <TD width="350" background="images/wnd/wndtitlemiddle.jpg" align="center" style="font-weight:bold;font-size:10pt;color:YELLOW;"><?php echo $row[2];?></TD>
     <TD width="80" background="images/wnd/wndtitleright.jpg" align="right"><img src="images/wnd/wndclose.gif" style="cursor:pointer" alt="关闭" onclick="CloseBulletin();"></TD>
     <TD width="10" background="images/wnd/wndcornerrt.jpg"></TD>
  </TR> 
  <TR>
     <TD width="10" background="images/wnd/wndborderleft.jpg"></TD>
     <TD width="510" colspan="3" background="images/wnd/wndclient.jpg" style="OVERFLOW: hidden; WIDTH: 510px; COLOR: #000000;"><?php echo $row[3];?></TD>
     <TD width="10" background="images/wnd/wndborderright.jpg"></TD>
  </TR> 
  <TR>
   	<TD width="10" height="10" background="images/wnd/wndcornerlb.jpg"></TD>
     <TD width="50" colspan="3" background="images/wnd/wndborderbottom.jpg"></TD>
     <TD width="10" height="10" background="images/wnd/wndcornerrb.jpg"></TD>
  </TR> 
  </TABLE>
   <SCRIPT LANGUAGE="JavaScript">
    var currentMoveObj=null,ObjDragLeft,ObjDragTop;
    function DragStart()
    {	currentMoveObj = document.getElementById("SysBulletin");
    	currentMoveObj.style.position = "absolute";
    	ObjDragLeft = event.x - currentMoveObj.style.pixelLeft;
    	ObjDragTop = event.y - currentMoveObj.style.pixelTop;
    }
    function DragStop()
    { currentMoveObj = null;
    }
    function CloseBulletin()
    { SysBulletin.style.display="none";
    }
    function DragMove()
    { if(currentMoveObj)
    	{ currentMoveObj.style.pixelLeft=event.x-ObjDragLeft;
    		currentMoveObj.style.pixelTop=event.y-ObjDragTop;
    	}
    }
    currentMoveObj = document.getElementById("SysBulletin");
    if(currentMoveObj)
    { currentMoveObj.style.pixelLeft=(document.body.clientWidth-530)/2;
    	 currentMoveObj=null;
    }  
    if(Safemode)CloseBulletin();           
  </SCRIPT><?php
}
$webstatenabled=$row[5];
if($webstatenabled) $advs_mid_url=$row[6];
$notify=$row[0]; ?>

<TABLE width="1000" align="center" border=0 cellSpacing=0 cellPadding=0 style="background-color: #FFFFFF;border-left:1px solid #cccccc;border-right:1px solid #cccccc;border-bottom:1px solid #cccccc;border-right:1px solid #cccccc;">
<TR valign="top">  
  <!-----幻灯片广告开始-----> 
  <td width="750"><div id="imgslider">
    <a target="_blank"><img src="images/loading3.gif" width="750" height="330" border=0></a>
    <ul><?php
    	$res=$conn->query('select * from `mg_links` where property=2 order by linkorder',PDO::FETCH_ASSOC);
        foreach($res as $row){
           echo '<li><a href="'.$row['linkurl'].'"><img src="'.$row['linkpicture'].'" alt="'.$row['linktitle'].'"></a></li>';
        }?>
    </ul>
    </div></td>
  <!-----幻灯片广告结束----->     
  <td width="250">
  <!-----新闻栏 开始----->
 	<div class="topnews">
	  <div class="t_bulletin"><?php echo $notify;?></div>
	  <div class="tab_title"><ul><li class="selected" id="newstab_1" onmouseover="news_tab(1)"><a href="news.php?cid=1">商城动态</a></li><li><a href="news.php?cid=2" id="newstab_2" onmouseover="news_tab(2)">今日话题</a></ul></div>
    <div class="tab_content">
   	  <TABLE cellSpacing=0 cellPadding=0 width="230" align="center" border="0" id="tab_content_1">
    		   <tr><td colspan="2" ></td></tr><?php
            $res=$conn->query('select id,title,addtime from `mg_article` where property=1 order by addtime desc limit 7',PDO::FETCH_ASSOC);
            foreach($res as $row){?><TR> 
              <TD width="185" height=23 class="news_title"><div><IMG src="images/star.gif" width=10 height=10 align=absMiddle><A  href="news/news<?php echo $row['id'];?>.htm" target="_blank"><NOBR><?php echo $row['title'];?></NOBR></A></div></TD>
             	<TD align="right" style="color:#88765e">[<?php echo date('y-m',$row['addtime']);?>]</td></TR><?php
            }?>   		  
           </TABLE>
           <TABLE cellSpacing=0 cellPadding=0 width="230" align="center" border="0" id="tab_content_2" class="nonselected">
           <tr><td colspan="2"></td></tr><?php
            $res=$conn->query('select id,title,addtime from `mg_article` where property=2 order by addtime desc limit 7',PDO::FETCH_ASSOC);
            foreach($res as $row){?><TR> 
               <TD width="185" height=23 class="news_title"><div><IMG src="images/star.gif" width=10 height=10 align=absMiddle><A  href="news/news<?php echo $row['id'];?>.htm" target="_blank"><NOBR><?php echo $row['title'];?></NOBR></A></div></TD>
               <td align="right" style="color:#88765e">[<?php echo date('y-m',$row['addtime']);?>]</td>	</TR><?php
            }?>
           </TABLE>
    </div>
  </div>
  </TD>
  <!-----新闻栏 结束----->  
</TR>
<?php if(@$advs_mid_url) echo '<TR><td colspan=2><a href="promotion.htm"><img src="'.$advs_mid_url.'" width=1000 border=0></a></td></TR>';?>
<TR>
  <TD colspan="2" background="images/scrollbg1.gif">
  	<TABLE cellSpacing="0" cellPadding="0" width="100%" height="20"  border="0">
    <TR>
    	 <TD align="center" width="30"><img src="images/speaker.gif"></TD>
    	 <TD width=918><?php if($notify) echo '<MARQUEE onmouseover="this.stop()" onmouseout="this.start()" scrollAmount=2 scrollDelay=60 width="100%"><FONT color=#ff6600>'.$notify.'</font></MARQUEE>';?></TD> 
       <TD align="center" width="30"><img src="images/speaker.gif"></TD>
    </TR>
    <TR>
    	<TD colspan=3 background="images/colbar.gif" height=1></TD>
    </TR>
    </TABLE>
	</TD>
</TR>
</TABLE> 


<div style="width:100%;height:50px;background:url(images/bg_top2.gif) repeat-x;">
<TABLE cellSpacing="0" cellPadding="0" width="1000" height="50" align="center" border="0">
<TR><TD width="450"></TD><TD width="300" background="images/ware_top.gif"></TD><TD width="250" align="right" valign="bottom"><a href="wares.htm">更 多 &gt;&gt;</a></TD>
</TR>
</TABLE>
</div> 
  
<TABLE cellSpacing="0" cellPadding="0" width="1000" align="center" border="0" bgcolor="#FFFFFF">
 <tr>
	 <TD width="200" valign="TOP" height="100%" background="/images/bg_left.gif">
	 	
	 	<TABLE cellSpacing="0" cellPadding="0" width="185" height="100%" border="0">
	 	<tr>
	 		<td height="1%">
<?php //导航:商品分类
        include("include/guide_brand.htm");
        include("include/guide_category.htm");
      ?>      
      </td></tr><tr><td height="98%"> 
        <!-----导航:友情链接 开始------> 
      <?php //导航:友情链接
        include("include/guide_links.php"); 
      ?>
        <!-----导航:友情链接 结束------>  
      </td></tr><tr><td height="1%" id="orderinfo"> 
        <!-----导航:基本信息 开始------>
         <?php //导航:基本信息
        $res=$conn->query("select ordername,receipt from `mg_orders` where state>3 order by actiontime desc limit 6",PDO::FETCH_NUM);          
        foreach($res as $row)
        { echo substr($row[1],0,3)."的订单<a href=\"orders.php?id=".$row[0]."\">".$row[0]."</a>正在发货...<br>";
        }
      ?>
        <!-----导航:基本信息 结束------> 
      </td></tr>
    </TABLE>
    
  </TD>

  <TD valign="top" width="800" height="100%">
      <TABLE cellSpacing="0" cellPadding="0" width="100%" height="100%" align="center"  border="0">
       <tr>
       	 <td  align="center" valign="TOP" height="1%">
       	 <!--------客户区开始----------->
 
       	 
           <!--------最新上架滚动栏 开始----------->
           <TABLE cellSpacing=0 cellPadding=0 width="100%" align="center"  border="0">
             
             <TR>
                <TD align=center>
             
                 <DIV id="MarqueeDemoA" style="OVERFLOW: hidden; WIDTH: 800px; COLOR: #ffffff;">
                  <TABLE cellSpacing=0 cellPadding=0 width="100%" align="center" border="0">
                   <tr>
                   	 <TD id=MarqueeDemoB>
                   	 <TABLE cellSpacing=0 cellPadding=0 width="780" align="center" border="0" class="WareShow">
                   	 	<TR><?php
$res=$conn->query("select id,name,price0,price1,price3,onsale from `mg_product` where recommend>0 order by addtime desc,id desc limit 15",PDO::FETCH_ASSOC);
foreach($res as $row) {
   if(($row['onsale']&0xf)>0 &&  $row['onsale']>time()) $price_pf=$row['price0'];
   else $price_pf=$row['price3'];
   ?> 
<td><div class="pimg"><a href="/products/<?php echo $row['id'];?>.htm"><img width="160" height="160" alt="<?php echo $row['name'];?>" border="0" src="<?php echo product_pic($row['id'],false);?>"></a></div>
<div class="pbox"><a href="/products/<?php echo $row['id'];?>.htm" class="plink"><?php echo $row['name'];?></a><span class="price3" title="批发代理价">￥<?php echo round($price_pf,2);?>元</span><span class="price1">￥<?php echo round($row['price1'],2);?>元</span><img class="pbuy" src="/images/gobuy.gif" width="22" height="12" alt="将该商品放入购物车" onClick="AddToCart(<?php echo $row['id'];?>)"></div>
</td><?php
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
            
           
         <!--------最新上架滚动栏 结束----------->    
         </td></tr><tr><td  align="center"  height="1%">       
       	 <!--------特价商品 开始----------->
     	   <TABLE cellSpacing=0 cellPadding=0 width="100%" align="center"  border="0">
             <TR>
               <TD><IMG src="images/tejia.gif" height=45 width=800 border="0" usemap="#Map1"></TD>
                <map name="Map1">
               	<area shape="rect" coords="740,16,795,40" href="promotion.htm">
                </map>
              </TR>
              <TR>
                <TD style="BORDER-COLLAPSE: collapse; border-left:1px solid #cccccc;">
<?php
$res=$conn->query("select id,name,price0,price1,price3,onsale from `mg_product` where (onsale&0xf)>0 and recommend>0 and onsale>unix_timestamp() order by (onsale&0xf) desc,onsale asc limit 4",PDO::FETCH_ASSOC);
$jishu=0;
foreach($res as $row) {
   if($jishu>0){
     if($jishu%2==0) echo "</tr><tr>";
   }
   else
   { echo '<TABLE cellSpacing=0 cellPadding=0 width=800 align=center border=0><tr>';
   }
   $jishu=$jishu+1;?>
   <td width="400">
      <TABLE width="400" height="200" cellSpacing=0 cellPadding=0 align="center" border="0" class="promotion">
      <tr><td width="25" rowspan="5"></td><td width="133" height="60"><div class="price_tj">￥<?php echo round($row['price0'],2);?>元</div></td><td width="62" class="qiangou"><a href="javascript:AddToCart(<?php echo $row['id'];?>)">抢购</a></td><td width="180" height="180" rowspan="4" align="center"><a href="/products/<?php echo $row['id'];?>.htm"><img width="160" height="160" alt="<?php echo $row['name'];?>" border="0" src="<?php echo product_pic($row['id'],false);?>"></a></td></tr>
      <tr><td height="50" colspan="2">&nbsp; <strike>市场价：￥<?php echo round($row['price1'],2);?>元</strike><br>&nbsp; <font color=#55AA66><strike>批发价：￥<?php echo round($row['price3'],2);?>元</strike></font></td></tr>
      <tr><td height="35" colspan="2">&nbsp; 距活动结束<img src="/images/time1.png" width="16" height="16" style="margin-bottom:-4px"><font id="life<?php echo $row['id'];?>">00天00时00分00秒</font></td></tr>
      <tr><td height="35" colspan="2"></td></tr>
      <tr height="20"><td colspan="3"><div class="name_tj"><a href="/products/<?php echo $row['id'];?>.htm"><NOBR><?php echo $row['name'];?></NOBR></a></div></td></tr>
      </TABLE><script>clock_lifetime2("life<?php echo $row['id'];?>",<?php echo $row['onsale'];?>);</script>
    </td><?php
  }
  if($jishu==0) echo '<p align=center>产品更新中...</p><br>';
  else echo '</tr></TABLE>';?>  
                </TD>
             </TR>
           </TABLE>
           <!--------特价商品 结束----------->
           </td></tr><tr><td valign="TOP">
           <!--------热销榜 开始---------->
           <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center"  border="0">
             <TR>
               <TD height=45><IMG src="images/hotsale.gif" width=800 height=45 border=0 usemap="#Map3"></TD>
                <map name="Map3">
               	<area shape="rect" coords="740,16,795,40" href="hotsell.htm">
                </map>
              </TR>
             <TR>
                <TD valign="top" style="BORDER-COLLAPSE: collapse; border-left:1px solid #cccccc;border-right:1px solid #cccccc;border-bottom:1px solid #cccccc;BACKGROUND-POSITION: left bottom; BACKGROUND-IMAGE:url(images/clientbot.jpg); BACKGROUND-REPEAT: repeat-x">
                  <TABLE cellSpacing=0 cellPadding=0 width="780" align="center"  border="0" class="WareShow">
                  <tr><?php
$res=$conn->query("select id,name,price0,price1,price3,onsale from `mg_product` where recommend>0 order by recommend desc,solded desc,id limit 32",PDO::FETCH_ASSOC);
$jishu=0;
foreach($res as $row) {
   if(($row['onsale']&0xf)>0 &&  $row['onsale']>time()) $price_pf=$row['price0'];
   else $price_pf=$row['price3'];
   if($jishu>0 && $jishu%4==0) echo "</tr><tr>";
   $jishu=$jishu+1;?>
<td><div class="pimg"><a href="/products/<?php echo $row['id'];?>.htm"><img width="160" height="160" alt="<?php echo $row['name'];?>" border="0" src="<?php echo product_pic($row['id'],false);?>"></a></div>
    <div class="pbox"><a href="/products/<?php echo $row['id'];?>.htm" class="plink"><?php echo $row['name'];?></a><span class="price3" title="批发代理价">￥<?php echo round($price_pf,2);?>元</span><span class="price1">￥<?php echo round($row['price1'],2);?>元</span><img class="pbuy" src="/images/gobuy.gif" width="22" height="12" alt="将该商品放入购物车" onClick="AddToCart(<?php echo $row['id'];?>)"></div>
</td><?php
}?>
                   </tr>
                  </TABLE>
                </TD>
             </TR>
           </TABLE><!-------热销榜 结束---------->
       	  
       	</td>
        <!-------客户区结束---------->
        </tr>
       </table> 		  
  </td>
</tr>
<tr>
	<td width="1000" height="180" colspan="3" valign="bottom" style="color:GREEN;text-align:center;BACKGROUND-IMAGE:url(images/alipaybanner.jpg); BACKGROUND-REPEAT: no-repeat">&nbsp;</td>
</tr>	
</table>
<?php include('include/page_bottom.htm'); ?>
<script>
var news_tab_index=1;
function OnLoadStatInfo(info){var showarea=document.getElementById("orderinfo");if(showarea && info){showarea.innerHTML=info+LoadFloatingQQs();QQ_FloatTopDiv();}}
function news_tab(focustab){if(focustab!=news_tab_index){var tab_new=document.getElementById("newstab_"+focustab);var tab_old=document.getElementById("newstab_"+news_tab_index);var con_new=document.getElementById("tab_content_"+focustab);var con_old=document.getElementById("tab_content_"+news_tab_index);if(tab_new && tab_old && con_new && con_old){tab_new.className="selected";tab_old.className="";con_old.className="nonselected";con_new.className="";news_tab_index=focustab;}}}
if(!Safemode){MarqueeInit();flashImage("imgslider",8000,true);AsyncPost("","<?php if($webstatenabled) echo 'include/guide_orderstat.php'; else echo 'include/guide_blank.php';?>",OnLoadStatInfo);}
</script>
</body>
</html> 
<?php
CloseDB();
?>
