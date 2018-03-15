function ShowFlash(flashURL)
{ var obj=document.all.tags('TD')[4];if(obj){var flashCode="<OBJECT height=411 width=745 alt='a' codeBase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0'  classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'>"; flashCode+="<PARAM NAME='movie' VALUE='"+flashURL+"'><PARAM NAME='quality' VALUE='high'>";flashCode+="<embed src='"+flashURL+"' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width='745' height='411'></embed></OBJECT>";document.all.tags('MARQUEE')[0].innerHTML=obj.innerText; obj.innerHTML=flashCode;obj.bgColor=0;}
}
//ShowFlash("/company/images/proshow.swf");

var nowhour=(new Date()).getHours();
if(nowhour>18 || nowhour<7) document.write('<A class="menu"  href="/?h=#" target="_blank" title="南京化妆品批发网 - 化妆品批发｜韩国化妆品｜进口化妆品" style="TEXT-DECORATION: underline">---&gt;&gt; 进入网站 &lt;&lt;---</A>');
 
var linkguider='<p align="center"><b style="FONT-SIZE:14px">网站导航</b>：&nbsp;\
<a class="menu2" href="http://www.google.com/" target="_top">谷歌搜索</a> | \
<a class="menu2" href="http://zhidao.baidu.com/browse/147/" target="_top">百度知道美容</a> | \
<a class="menu2" href="http://www.chinadmoz.org/" target="_top">DMOZ目录</a> | \
<a class="menu2" href="http://www.coodir.com/" target="_top">酷帝网站目录</a> | \
<a class="menu2" href="http://www.dzhai.com/" target="_top">第一摘网站目录</a> | \
<a class="menu2" href="http://www.gdhzp.com/wares.htm" target="_top">化妆品批发</a>\
</p>';

document.write(linkguider);

 