function GenPageHead()
{ var headcode='\
  <TABLE width="100%" border="0" cellSpacing=0 cellPadding=0>\
  <TR>\
     <TD bgColor=#e0e0e0 height="5"><iframe name="dummyframe" style="width:100%; height:10%;" scrolling="no"  Frameborder="no" marginwidth=0 marginheight=0></iframe></TD>\
  </TR>\
  </TABLE>\
  <TABLE cellSpacing=0 cellPadding=0 width="1000" align="center" border="0">\
  <TR>\
  	 <TD width="100%" height="80">\
    	   <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">\
         <tr>\
          <td width="285" rowspan="2" id="tpbanner1"><img src="/images/banner.png" width="285" height="80"></td>\
          <td width="715" height="50" style="BACKGROUND-IMAGE:url(/images/tp.gif); BACKGROUND-REPEAT: no-repeat;BACKGROUND-POSITION: 15% 50%;">\
         	  <TABLE cellSpacing="0" cellPadding="0" border="0" width="100%" height="100%">\
         	  <tr>\
         	  	<td align="right" valign="top" > \
         	      <a href="/usrmgr.htm?action=mycart">购物车</a> | <a href="/usrmgr.htm?action=myfav">收藏架</a> | <a href="#" onclick="window.open(\'/paybill.asp?handle=\'+Math.random())">收银台</a> | <a href="/usrmgr.htm?action=myorders"><font color=GREEN><b>我的订单</b></font></a> | <a href="/book/"><font title="有任何都可以在这里留言...">在线咨询</font></a> | <a href="/newarrival.asp"><font color="#FF0000"  title="最新到货/缺货的产品列表">最新到货/缺货</font></a>  | <a href="/quotation.asp" title="最新产品批发报价表即时下载">下载批发报价单</a>\
         	    </td>\
         	  </tr>\
         	  <tr>\
         		  <td align="right" valign="top"><img src="/images/foot.gif" width=16 height=16 align="absMiddle"> <b>您当前位置:</b> <a href="/company/" style="font-weight:bold;color:#8F8F8F" title="切换到公司官网首页">公司首页</a> &gt;&gt; 〖涵若铭妆〗<font color="#FF6600"><b>批发商城</b></font> | <a href="/usrmgr.htm?action=msg" id="msginfobox">站内信</a></td>\
         	  </tr>\
            </TABLE>\
         </td>\
       </tr>\
       <tr>\
         <TD width="715" height="30"><ul id="mainmenu"><li><a href="/#" title="商城首页">首页</a></li><li><a href="/wares.htm">新品上架</a></li><li><a href="/hotsell.htm">热销排行</a></li><li><a href="/promotion.htm">限时秒杀</a></li><li><a href="/present.htm">赠品兑购</a></li><li><a href="/article.htm">商城动态</a></li><li><a href="/help.htm">帮助导航</a></li><li><a href="/usrmgr.htm">会员中心</a></li></ul></TD>\
       </tr>\
       </table>\
    </TR>\
    <TR bgColor=#666666>\
        <TD height=3></TD>\
    </TR>\
    <TR>\
      <TD>\
      	  <TABLE width="100%" height="45" cellSpacing=0 cellPadding=0 border="0"  background="/images/navbg_0.gif">\
          <TR>\
            <TD width="50" background="/images/navbg_1.gif"> </TD>\
            <TD width="70" id="userlogo"> </TD>\
            <TD width="865" valign="top">\
              <TABLE width="100%" height="40" cellSpacing=0 cellPadding=0 border="0">\
              <TR>\
                <td id="loginfo" align="center" nowrap></div>\
                </td>\
              <form name="topsearch" onsubmit="return check_search(this)">\
              <td align="right" nowrap>\
              	  <img src="/images/searchico.gif" width="23" height="23" align="absMiddle">\
                	<select name="searchmode">\
                    <option value="0" selected>商品名称</option>\
  		              <option value="1">商品编号</option>\
                    <option value="2">商品条码</option>\
                  </select><input type="hidden" name="category" value="0">\
                  <input name="searchkey" type="text" size="15" maxlength="50"><input type="submit"  value="查询"><font size="4" color="#8f8f8f">|</font><input type="button"  value="高级搜索" onClick="check_search(null)">\
                </td></form>\
              </tr>\
              </table>\
            </TD>\
            <TD width="15" background="/images/navbg_2.gif"></TD>\
          </TR>\
          </TABLE>\
      </TD>\
    </TR>\
  <TR><TD id="tpbanner2"></TD></TR>\
  </TABLE>';
  document.write(headcode);
}
GenPageHead();page_head_init(5);
