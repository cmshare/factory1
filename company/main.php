<?php require('include/conn.php');
$PageTitle='南京铭悦日化用品有限公司';
#始终用这个标题,千万不要改
#理由1:本站只体现铭悦日化公司,不使用涵若铭妆以及铭悦商城字样，因可能会作为涵若铭妆主站维护期间的镜像站点来用,同时也作为铭悦商城的镜像站点，因此需要中性.
#理由2:本站不需要seo优化，只需要中性及好看的标题；
db_open();
require('include/page_head.php');?> 
<base target="_top" />
<TABLE border=0 cellSpacing=0 cellPadding=0 width="1000" height="300" align="center">
<tr valign="top">
   <td width="750">
		
    <!--幻燈片1開始-->
    <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
    <TR>
       <TD colspan="3" height=10 background=images/flash_b_up.gif></TD>
    </TR>
    <TR>
       <TD background="images/flash_b_left.gif" width=6></TD>
       <TD height="225" align="center" valign="middle"><div id="imgslider1" style="width:725px;height:225px;" class="imgslider">
      <a target="_blank"><img src="/images/loading3.gif" width="725" height="225" border=0></a>
      <ul>
    	  <li><a href="catlist.htm?catid=262"><img src="images/advs/advs_1_1.jpg"></a></li>
        <li><a href="catlist.htm?catid=68"><img src="images/advs/advs_1_2.jpg"></a></li>
    	  <li><a href="catlist.htm?catid=167"><img src="images/advs/advs_1_3.jpg"></a></li>
      </ul><div style="position:absolute;left:165px;top:80px;z-index:1001"><img src="images/advflash.gif"></div>
    </div></TD>
       <TD background="images/flash_b_right.gif" width=19></TD>
    </TR>
    <TR>
       <TD colspan="3" height=10 background="images/flash_b_down.gif"></TD>
    </TR>
    </TABLE>
		<!--幻燈片1結束-->
		<TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">
    <TR>
       <td valign="top"> 
       	 <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%" >
         <TR>
            <TD height=31><IMG src="images/ls_14.gif" width=340 height=31></TD>
         </TR>
         <TR>
            <TD  height=210 vAlign="top" background="images/m_16.gif" style="color:#dddddd; PADDING-LEFT: 15px; PADDING-RIGHT: 15px; PADDING-TOP: 15px;LINE-HEIGHT: 180%;FONT-SIZE: 9pt">
            	<font color="#cc9900"><STRONG>开店支持：</STRONG></font>加盟即送装修方案、背柜、台柜、面柜收银系统、POS机、春夏秋冬正规工作服、广告宣传江苏卫视、城市频道、娱乐频道、影视频道等即将热播……<BR><font color="#cc9900"><STRONG>开店促销：</STRONG></font>精美饰品、特价產品、特色產品、宣传物品、购物袋、雨伞、折扇等……促销手段丰富多彩。<BR><font color="#cc9900"><STRONG>人员支持：</STRONG></font>金牌店长前期驻店辅助销售、培训、全程策划、让您轻松经营。<BR><font color="#cc9900"><STRONG>货物退换：</STRONG></font>加盟货物任选，如不满意，可随时调换畅销產品，无需压货，让您无后顾之忧。</TD>
         </TR>
         <TR>
            <TD><IMG src="images/m_25.gif" width=340 height=9></TD>
         </TR>
         </TABLE>
       </td>
       <td valign="top" align="right">
             
         <TABLE border=0 cellSpacing=0 cellPadding=0 width="410">
         <TR>
           <TD width="400" height="250" background="images/news_pic_bg.gif" align="center" valign="middle"><div id="imgslider2" class="imgslider" style="width:390px;height:240px;">
    <a target="_blank"><img src="/images/loading3.gif" width="390" height="240" border=0></a>
    <ul>
    	<li><a><img src="images/advs/advs_2_1.jpg"></a></li>
    	<li><a><img src="images/advs/advs_2_2.jpg"></a></li>
    	<li><a><img src="images/advs/advs_2_3.jpg"></a></li>
    	<li><a><img src="images/advs/advs_2_4.jpg"></a></li>
    	<li><a><img src="images/advs/advs_2_5.jpg"></a></li>
    	<li><a><img src="images/advs/advs_2_6.jpg"></a></li>
    </ul>
    </div></TD>
           <TD width="10" bgcolor="#F6F9FB"></TD>
         </TR>
         </TABLE>
     
      </td>
    </TR>
    </TABLE>	
    
    <TABLE border=0 cellSpacing=0 cellPadding=0 width="750">
    <tr>
    	<td background="images/m_36.jpg" height="31" align="right" valign="bottom" style="padding-right:9px;padding-bottom:1px"><A href="warelist.htm"><IMG border=0 src="images/index_more.gif" width=52 height=18></A></td>
    	<td width="10" bgcolor="#F6F9FB" rowspan="3"></td>
    </tr>
    <tr>
    	 <td background="images/m_17.jpg" height="185" align="center">
       <DIV id="MarqueeDemoA" style="padding-top:8px;OVERFLOW: hidden; WIDTH: 720px; height:230px;COLOR: #ffffff;">
           <TABLE cellSpacing=0 cellPadding=0 width="100%" align="center" border="0">
           <tr>
           	  <TD id=MarqueeDemoB>
             	  <TABLE cellSpacing=0 cellPadding=0 width="720" align="center" border="0" class="wareshow">
             	 	<TR><?php
$res=$conn->query('select id,name from mg_product where recommend>0 order by addtime desc limit 25',PDO::FETCH_ASSOC);
foreach($res as $row){
  echo '<td><div class="pimg"><a href="product.htm?id='.$row['id'].'"><img width="160" height="160" alt="'.$row['name'].'>" border="0" src="'.product_pic($row['id'],0).'"></a></div><div class="pbox"><a href="product.htm?id='.$row['id'].'" class="plink">'.$row['name'].'</a></div></td>';
}?>             </TR>
                </TABLE>
              </td>
              <td id="MarqueeDemoC"></td>
           </tr>
           </TABLE>
       </div>
  	 
       </td>
    </tr>
    <tr>
    	 <td background="images/m_44.jpg" height="9"></td>
    </tr>	
    </table>	
		
  </td>
	<td width="250">	
		
		 <table border=0 cellSpacing=0 cellPadding=0 width="250" height="100%">
		 <tr>
		    <td background="images/index_blank.gif" height="34" style="color:#FFFFFF;font-size:14px;padding-left:18px;padding-top:10px">会员登录</td>
		 </tr>
		 <tr bgcolor="#F0F0F0">
		    <td background="images/index_bg.gif" height="85" align="center" id="loginfo">
		    	<form onsubmit="return userlogin(this);" style="margin:0px"><table border="0" cellSpacing=0 cellPadding=0 width="230" height="75" align="center"><tr>
        	<td><table><tr><td height="25"><STRONG>账　号</STRONG> <INPUT type="text" maxLength=16 size=12 name="username" style="width:100px;height:21px;"></td></tr><tr><td height="25"><STRONG>密　码</STRONG> <INPUT maxLength=16 size=12 type="password" name="password" style="width:100px;height:21px;"></td></tr><tr><td height="25"><STRONG>验证码</STRONG> <INPUT type="text" maxLength=4 size=12 name="verifycode" style="width:100px;height:21px;"></td></tr></table></td>
	        <td><table><tr><td><INPUT border=0 src="images/logon.gif" width=50 height=25 type="image"></td></tr><tr><td><a href="reg.php" target="_top"><img src="images/regist.gif" width=50 height=25 style="cursor:pointer" border="0"></a></td></tr><tr><td><IMG id="LoginCheckout" src="user/authcode.php" align="absMiddle" onclick="refresh_vcode()"></td></tr></table></td>
	        </tr></table></form>	
		    </td>
     </tr>
     <tr>
     	 <td background="images/index_bg.gif" bgcolor="#F0F0F0" height="50" valign="top"><hr> 
         <TABLE class="searchpanel" cellSpacing=0 cellPadding=0 width="244" height="27" border=0 style="margin-left:1px">
         <TR align="center" valign="middle"><form  name="topsearch" onsubmit="return check_search(this)">  
         	<TD width="172" valign="middle"><INPUT type="text" name="searchkey" class="searchkey"></TD>
         	<TD width="72" height="25"><input type="submit" type="button" value="搜产品" style="border:0px;HEIGHT: 25px;WIDTH:70px;COLOR:#FFFFFF;font-size:12px;font-weight:bold; CURSOR: pointer" class="m1" onmouseup="this.className='m1'" onmouseout="this.className='m1'" onmouseover="this.className='m2'" onmousedown="this.className='m3'"><input type="hidden" name="category" value="0"><input type="hidden" name="searchmode" value="0"></TD>
         </TR></form>
         </TABLE>	
		   </td>
		 </tr>
		 <tr>
		    <td background="images/index_bulletin3.gif" height="32">&nbsp;</td>
		 </tr>
		 <tr bgcolor="#000000">
		    <td background="images/index_bg.gif" style="padding:5px" valign="top"><font color="#F0F0F0">▲尚姬泉、韩媛、凉颜、门前一草等产品</font> <font color="#FF8833"><b>诚招空白地区代理</b>！</font><br><a href="warelist.htm"><font color="#FFFFFF">▲使用大众书局·易购卡，尊享VIP价。</font></a></td>
		 </tr>
		 <tr>
		    <td background="images/index_news2.gif" height="32">&nbsp;</td>
		 </tr>
		 <tr bgcolor="#000000">
		    <td background="images/index_bg.gif" valign="top" style="padding-top:6px" height="200">
 
	             <TABLE cellSpacing=0 cellPadding=0 width="95%" align="center" border="0" class="newsbox"><?php
               $res=$conn->query('select id,title,addtime from `mg_article` where property=1 order by addtime desc limit 8',PDO::FETCH_ASSOC);
               foreach($res as $row){?>
                 <TR>
                   <TD vAlign=center width="100%" background=images/brokenline.gif height=22><IMG src="images/star.gif" width=10 height=10 align=absMiddle>
                    <A title="發布時間 <?php echo date('Y-m-d',$row['addtime']);?>" href="news.htm?id=<?php echo $row['id'];?>"><?php echo $row['title'];?> </A></FONT></TD>
                 </TR><?php
               }?>
               <TR>
                 <TD vAlign="center" align="right" width="100%" height="15"><a href="news.htm" title="更多..."><img src="images/more.gif" height="5" border="0" align="absMiddle"></a></TD>
               </TR>                    		                    		                    		                    		 
               </TABLE>
                        	  	
		    </td>
		 </tr>
		 <tr>
		    <td background="images/index_aboutus.gif" height="27">&nbsp;</td>
		 </tr>
		 <tr bgcolor="#F0F0F0">
		    <td background="images/index_bg.gif" style="padding:10px;OVERFLOW: hidden;" valign="top" height="210">
		    南京铭悦日化用品有限公司，总部位于繁华时尚之都香港，在国内多个城市都有加盟合作伙伴，是一家集化妆品研发、生产、销售、培训、连锁加盟为一体的国际性集团企业，有着深厚的技术背景，产品开发和新技术应用居当前日化行业先进水平，公司以产品为核心，与世界各知名品牌长期合作，凭借著标准的国际化连锁经营模式，致力于化妆品精品店的推广，公司有著准确的市场定位与全新的经营思路，产品结构完整……
		    </td>
		 </tr>
		 <tr>
		    <td background="images/index_bottom.gif" height="8"></td>
		 </tr>
		 </table>   

	</td>
</tr>
</table><?php
require('include/page_bottom.htm');
db_close();?>
<script type="text/javascript" src="include/qqservice.js"></script><SCRIPT language="JavaScript">
flashImage("imgslider1",5000,false);flashImage("imgslider2",8000,true);MarqueeInit();QQFloating();
</SCRIPT><div style="display:none"><script type="text/javascript" src="http://js.users.51.la/2969614.js"></script></div>
</BODY>
</HTML> 
