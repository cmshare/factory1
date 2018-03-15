var OnlineUserID=0,MirrorSite=0,gotvcode=false,IsQrVisible=null;
var WebRoot="/";

function SetMainMenuItem(tabIndex)
{ var Pagination=(isNaN(tabIndex) || tabIndex<0)?0:tabIndex;
	var items=document.getElementById("mainmenu");	
	if(items)
	{ items=items.childNodes;
		for(var i=0;i<items.length;i++)
	  { items[i].onmouseover=function(){this.className="hoverlink";};
		  items[i].onmouseout=function(){if(this!=items[Pagination])this.className="";};
		  items[i].onclick=function(){this.getElementsByTagName('A')[0].click();};
		  if(i==Pagination)items[i].className="hoverlink";
	  }
  }
}

function ModalDialogHeightExt()
{ var ua = navigator.userAgent;   
  if(ua.indexOf("MSIE 7.0")>0)return 0; 
  else if(ua.indexOf("Firefox")>0)return 5; 
  else return 30; 
}

function ShowDialog(wndtitle,url,w,h,callback)
{ var dlg=document.createElement("DIV");
	dlg.style.position="absolute";
  dlg.style.zIndex="9999"; 
  dlg.style.width=w+"px";
  dlg.style.height=h+"px";
  dlg.style.left=Math.round((document.body.scrollWidth-w)/2)+"px";
  dlg.style.top =(120+document.body.scrollTop)+"px";
  document.body.appendChild(dlg);
  dlg.innerHTML='<table width="'+w+'" height="'+h+'" style="border-radius:5px 5px 5px 5px;background-color:#0D9EFA;" align="center"><tr><td height="20" style="color:#ffffff;font-size:9pt;font-weight:bold;">⊙ '+wndtitle+'</td><td align="right"><img style="cursor:pointer" onclick="self.CloseDialog()" src="'+WebRoot+'images/closebtn1.gif" border=0></td></tr><tr><td colspan="2" style="background-color:#dfdfdf;background-image:url('+WebRoot+'images/loading3.gif);BACKGROUND-POSITION: center center; BACKGROUND-REPEAT: no-repeat;"><iframe src="'+url+'" style="width:100%;height:100%;" marginwidth=0 marginheight=0 scrolling="no" Frameborder="no"></iframe></td></tr></table>';
  if(self.CloseDialog)self.CloseDialog();
  self.CloseDialog=function(ret){if(ret==null || !callback || callback(ret)){document.body.removeChild(dlg);self.CloseDialog=null;}}//闭包
}

function AsynPrompt(wndtitle,wndtext,callback,defvalue,maxlength)
{	var dlgwidth=200,dlgheight=100,input_filter;
	var dlg=document.createElement("DIV");
  dlg.style.position="absolute";
  dlg.style.zIndex="9999"; 
  dlg.style.width=dlgwidth+"px";
  dlg.style.height=dlgheight+"px";
  dlg.style.left=Math.round((document.body.scrollWidth-dlgwidth)/2)+"px";
  dlg.style.top=(120+document.body.scrollTop)+"px";  
  document.body.appendChild(dlg);
  if(typeof(defvalue)=="number")
  { if(!maxlength)maxlength=8;
  	input_filter="onkeyup=\"if(isNaN(value))execCommand(\'undo\');\" maxlength=8";
  }
  else
  { if(defvalue==null)defvalue=""; 
  	if(!maxlength)maxlength=16;
  	input_filter="maxlength="+maxlength;
  }
  dlg.innerHTML='<table width="'+dlgwidth+'" height="'+dlgheight+'" style="border-radius: 5px 5px 5px 5px;background-color:#0D9EFA;" align="center"><form onsubmit="closedlg(this.inputs.value);return false;"><tr><td height="20" style="color:#FFFFFF;font-size:9pt;font-weight:bold;">⊙ '+wndtitle+'</td><td align="right"><input type="image" onclick="closedlg();return false;" src="'+WebRoot+'images/closebtn1.gif" border=0></td></tr><tr><td colspan="2" align="center" bgcolor="#dfdfdf" ><table border=0 width="100%" height="100%"><tr><td style="font-size:9pt">'+wndtext+'</td></tr><tr><td><input name="inputs" type="text" '+input_filter+' value="'+defvalue+'" style="width:100%;text-align:center"></td></tr><tr><td align="center"><input type="submit" value="确定"> <input type="button" value="取消" onclick="closedlg()"></td></tr></table></td></tr></form></table>';
  if(self.asynprompt)self.asynprompt.closedlg();
  self.asynprompt=dlg.getElementsByTagName("form")[0]; 
  if(self.asynprompt)
  { self.asynprompt.inputs.select();
    self.asynprompt.closedlg=function(ret){if(ret==null || !callback || callback(ret)){self.asynprompt=null;document.body.removeChild(dlg);}}//闭包
  }
}

String.prototype.trim=function() 
{ return this.replace(/(^\s*)|(\s*$)/g,"");
}

function StrToInt(numstr)
{ return (numstr && !isNaN(numstr) )?parseInt(numstr) : 0;
}

function HTML2Text(strHTML) 
{ return strHTML.replace(/<.*?>/g, ""); 
}

function isNaIMG(evt)
{ var obj=evt.srcElement;
	if(!obj){obj=evt.target;if(!obj)return true;}
	return (obj.tagName!="IMG");
}

function CreateXMLHTTP(){
  var xmlhttp;
  try{
    xmlhttp = new XMLHttpRequest();
  } 
  catch (trymicrosoft){
    try{
      xmlhttp= new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (othermicrosoft){
      try{
        xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch (failed){
        xmlhttp = null;
      }
    }
  }
  return xmlhttp; 
}

function PostForm()  //formContent,actionURL,textBlockID,textInitial
{	var paramcount=PostForm.arguments.length;
	var xmlhttp=CreateXMLHTTP();
  if(paramcount==2)
	{ xmlhttp.open("post",PostForm.arguments[1],false);   
    xmlhttp.setRequestHeader("Content-length",PostForm.arguments[0].length);   
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");   
    xmlhttp.send(PostForm.arguments[0]);  
    if (xmlhttp.readyState==4)
    { return (xmlhttp.status==200)?xmlhttp.responseText:null;
    }   
	}
	else if(paramcount>2)
	{ var RedirectTextBlock=document.getElementById(PostForm.arguments[2]);
	  RedirectTextBlock.innerHTML=(paramcount==4)?arguments[3]:"<p align=center style='color:#FF0000'>正在加载数据，请稍候...</p>";
	  xmlhttp.onreadystatechange=function()
    { if (xmlhttp.readyState==4)
      { if (xmlhttp.status==200)
        {	RedirectTextBlock.innerHTML=xmlhttp.responseText;
       	}
	      else
	      { RedirectTextBlock.innerHTML="<p align=center>服务器请求失败，可能是您的网速太慢，请刷新重试!</p>";
	      }
	    }
    }
	  xmlhttp.open("post",PostForm.arguments[1],true);
	  xmlhttp.setRequestHeader("Content-length",PostForm.arguments[0].length);   
    xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
    xmlhttp.send(PostForm.arguments[0]); 
  }
}

function AsynPostForm(formContent,actionURL,callbackfunc)
{	var xmlhttp=CreateXMLHTTP();
  xmlhttp.onreadystatechange=function()
  { if (xmlhttp.readyState==4)
    {	callbackfunc((xmlhttp.status==200)?xmlhttp.responseText:null);
	  }
  }
	xmlhttp.open("post",actionURL,true);
	xmlhttp.setRequestHeader("Content-length",formContent.length);   
  xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
  xmlhttp.send(formContent); 
}

var OldTempScript=null;
function loadScript(url)
{ var newscript=document.createElement("script");
  newscript.type="text/javascript";
  newscript.src=url;
  document.getElementsByTagName("head")[0].appendChild(newscript);
  if(OldTempScript)lastScript.parentNode.removeChild(OldTempScript);
  OldTempScript=newscript;
}
 
function GetUrlParam(strURL,strParam)
{ if(strURL && strParam)
	{ var searKey=strParam.toLowerCase()+"=";
	  var searUrl=strURL.toLowerCase();
	  var namepos=searUrl.indexOf("?"+searKey);
	  if(namepos<0)namepos=searUrl.indexOf("&"+searKey);
    if(namepos>=0)
    {	var pos_start=namepos+strParam.length+2;
      var pos_end=strURL.indexOf("&",pos_start);
      if (pos_end==-1)pos_end=strURL.length;
      return strURL.substring(pos_start,pos_end);
    }
  }
  return "";
}

function htmRequest(strName)
{ /*location.search是从当前URL的?号开始的字符串*/
	var strURL=window.location.search+"&";
  return GetUrlParam(strURL,strName);
}

function GetUrlQuery(url)//返回url串中?后的string
{ var urlStartIndex = url.indexOf("?");
  if(urlStartIndex>=0)
  { return url.substring(urlStartIndex+1,url.length);
  } else return "";
}
  
var hProductTip=null;
function ProductTip(obj)
{ if(!hProductTip)
	{ /* 动态方法的这段代码不稳定。
 		hProductTip = document.createElement("div"); 
 		document.body.appendChild(hProductTip);
 		hProductTip.style.display="none";  */
		hProductTip=document.getElementById("ProductTipLayer");
		if(hProductTip)
		{	hProductTip.style.zIndex=1000;
			hProductTip.style.position="absolute";
	  }	
	}
  if(hProductTip)
  { if(obj)
  	{	var pName=obj.alt;
  		var PID=GetProductIDFromURL(obj.parentNode.href);
      var mytop=obj.offsetTop;
      var myleft=obj.offsetLeft;
      var tipContent='<table border=0 cellSpacing=2 style="filter:glow(color=#948c7b,strength=8);" ><tr><td bgcolor="#C7DD9F" onmouseout="ProductTip()">' + pName;
      pName=PID;
      while(pName.length<5) pName="0"+pName;
      tipContent+='<br>【商品编号】 '+pName;
      pName=obj.getAttribute("spec")
      tipContent+='<br>【商品规格】 '+((pName)?pName:"/");
      pName=obj.getAttribute("stoc")
      if(MirrorSite==0)
      { if(pName!=null)
        { tipContent+='<br>【库存状态】 '+((pName>0)?"<b>有现货</b>":"<font color=#FF0000>无现货</font>");
        }
        else
        { tipContent+='<br>【库存状态】 <span id="stock'+PID+'"><img src="'+WebRoot+'images/loading1.gif"  height=9></span>';
      	  obj.setAttribute("id","ware"+PID); 
      	  setTimeout("if(document.getElementById('stock"+PID+"'))AsynPostForm('pid="+PID+"','"+WebRoot+"user/getstock.asp',OnGetStock);", 2000);
        }
      }  
      tipContent+='</td><tr></table>';
      
      /*该方法对不同浏览器有兼容性问题
      mytop=obj.offsetHeight;
      while(obj=obj.offsetParent)
      { myleft+=obj.offsetLeft;
        mytop+=obj.offsetTop;
      }*/
      var rt=obj.getBoundingClientRect();
      myleft=rt.left+document.body.scrollLeft;
      mytop=rt.top+document.body.scrollTop+obj.offsetHeight;
      
      hProductTip.innerHTML = tipContent
      hProductTip.style.display="block";
      hProductTip.style.left=myleft-8;
      hProductTip.style.top=mytop+8;
    }else hProductTip.style.display="none";  
  }
}
	
function GetProductIDFromURL(ProductURL)
{ return ProductURL.replace(/[^\d]*/g ,"");
}

function OnGetStock(responsetext)
{ if(responsetext)
	{ var dd=responsetext.split("|");
		if(dd.length==2)
		{ var obj=document.getElementById("ware"+dd[0]);
	    if(obj)
	    { obj.setAttribute("stoc",dd[1]); 
	    	obj=document.getElementById("stock"+dd[0]);
	    	if(obj)obj.innerHTML=(dd[1]>0)?"有现货":"<font color=#FF0000>无现货</font>";
      }	
		}
	}
}
 
function check_search(searchform)
{ if(searchform)
	{ var check_text=searchform.searchkey.value.trim();
    if(check_text=="" || check_text=="请输入关键字")
    { searchform.searchkey.focus();
	    alert("请输入查询关键字！");
    }
    else
    {	var searchmode=searchform.searchmode.value;
  	  var searchcategory=(searchform.category)?searchform.category.value:"0";
  	  var searchURL=WebRoot+"search.htm?k="+escape(check_text);
  	  if (!isNaN(searchmode) && parseInt(searchmode)>0)searchURL+="&m="+searchmode;
  	  if (!isNaN(searchcategory) && parseInt(searchcategory)>0)searchURL+="&c="+searchcategory;
      self.location.href=searchURL;
    }
    return false;
  }
  else //切换到高级搜索页面
  { self.location.href=WebRoot+"search.htm";
  }  
}
function InitSearchForm(myform) 
{ if(myform)
	{ var q = myform["searchkey"];
    var b = function(){if(q.value == "") q.style.background = "#FFFFFF url("+WebRoot+"images/searchpanel.gif) 5px 1px  no-repeat";}
    var f = function(){q.style.background = "#ffffff";}
    q.onfocus = f;
    q.onblur = b;
    if (!/[&?]q=[^&]/.test(location.search)) b();
  }  
}

function gmSwitch(m)
{ m=m.parentNode;
	if(m.className=="gMenuOpen")m.className="gMenuClose";
	else if(m.className=="gMenuClose")m.className="gMenuOpen";
	else
	{ m=m.getElementsByTagName("A");
	  if(m && m.length)self.location.href=m[0].href;
	}
}

function gmEnter(m){m.style.backgroundColor="#FFE3D2";}
function gmLeave(m){m.style.backgroundColor="";}
 
function AddToCart(pid,amount)
{ ShowDialog("添加商品到购物车",WebRoot+"user/add2cart.asp?id="+pid+"&amount="+amount+"&handle="+Math.random(),650,265);
}

function AddToFavor(pid)
{ if(confirm("将该商品放入我的收藏架？"))
	{	var ret=PostForm("",WebRoot+"Favorites.asp?action=add&ProdId="+pid);
    if(ret.trim())alert(ret);
    else alert("网络忙,请稍候再试!");
  }  	
} 

var MarqueeHandle;
function MarqueeInit()
{ var MarqueeSpeed=30;
	if(document.getElementById("MarqueeDemoA"))
  { MarqueeDemoC.innerHTML=MarqueeDemoB.innerHTML;
    MarqueeHandle=setInterval(MarqueeProc,MarqueeSpeed)
    MarqueeDemoA.onmouseover=function() {clearInterval(MarqueeHandle)}
    MarqueeDemoA.onmouseout=function() {MarqueeHandle=setInterval(MarqueeProc,MarqueeSpeed)}
  }
}
function MarqueeProc()
{ if(MarqueeDemoC.offsetWidth-MarqueeDemoA.scrollLeft>0)MarqueeDemoA.scrollLeft++;
  else MarqueeDemoA.scrollLeft-=MarqueeDemoB.offsetWidth;
}


var productLoading=0;
function OnLoadProductUpdates(res)
{	var obj=document.getElementById("TProduct");
	if(res && obj && obj.rows.length>7)
  {	var info=res.split("|");
   	if(info.length==10 && parseInt(info[0])==productLoading)
 	  {	obj=obj.rows;
 	  	obj[4].cells[1].innerHTML=(info[1]>0)?"<font color='#00BB00'>有现货</font>":"<font color='#FF0000'>无现货</font>";
      obj[6].cells[1].innerHTML="￥"+info[3]+"元";
      obj[6].cells[3].innerHTML="￥"+info[4]+"元";
      obj[7].cells[1].innerHTML=(getCookie("cmshop","usergrade")=="4")?"<b><font color=#ff0000>￥"+info[6]+"元</font></b>":"<font color=#888888>非等级查看</font>";
      //obj[7].cells[2].innerHTML="【<font color=#FF5500>批 发 价</font>】";
      obj[7].cells[3].innerHTML="￥"+info[5]+"元";
      if(info[9].charAt(0)=='<')
      { obj=document.getElementById("MarqueeDemoB");
        if(obj)obj.innerHTML=info[9];
      }
  	}
  }
  MarqueeInit();
}	

function OnLoadProductReviews(ret)
{ if(ret)
	{ var obj=document.getElementById("productreviews");
	  if(obj)obj.innerHTML=ret;
	  else//临时方案，兼容之前未更新的页面
	  { obj=document.getElementById("ProductAppendix");
	  	if(obj)
	  	{ obj=obj.children.tags("table")[0];
	  		if(obj)
	  		{ obj=obj.rows(1).cells[0];
	  			if(obj)obj.children[0].innerHTML=ret;
	  	  }	
	  	}
	  }
  }
}	

function ShowQR(obj,pid)
{	if(hProductTip)
	{ if(obj)
	  { var rt=obj.getBoundingClientRect();
      var myleft=rt.left+document.body.scrollLeft;
      var mytop=rt.top+document.body.scrollTop;
		  hProductTip.innerHTML = "<img src='http://www.charmbloom.com/qrcode/qr/"+pid+".png'>";
      hProductTip.style.display="block";
      hProductTip.style.left=myleft-20;
      hProductTip.style.top=mytop-150;
	  } else hProductTip.style.display="none";  
	}
}

	
function UpdateProductInfo(pid)
{ var obj=document.getElementById("qrcode");	
	if(obj)
	{ obj.innerHTML='<img src="/images/productqr.gif" onmouseover="ShowQR(this,'+pid+')" onMouseOut="ShowQR()" style="cursor:pointer"><div id="qrcodetip" style="z-index:1001;position:absolute;display:none;width:150px;height:150px;"></div><div class="bdsharebuttonbox"><a class="bds_more" href="#" data-cmd="more">分享到：</a><a class="bds_qzone" title="分享到QQ空间" href="#" data-cmd="qzone"></a><a class="bds_tsina" title="分享到新浪微博" href="#" data-cmd="tsina"></a><a class="bds_tqq" title="分享到腾讯微博" href="#" data-cmd="tqq"></a><a class="bds_renren" title="分享到人人网" href="#" data-cmd="renren"></a><a class="bds_weixin" title="分享到微信" href="#" data-cmd="weixin"></a></div>';
		window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];
		hProductTip=document.getElementById("qrcodetip");
  }
	if(self.location.href.indexOf(".htm")>0)//执行静态页面product_@@@@@.htm
	{	productLoading=pid;
		AsynPostForm("action=get&id="+pid,"/user/getproduct.asp",OnLoadProductUpdates);
		if(OnlineUserID>0)
		{ var productreviews=getCookie("productreviews"); 
			if(productreviews.indexOf("|"+pid+"|")>=0)AsynPostForm("mode=get&id="+pid,"/user/review.asp",OnLoadProductReviews);
			obj=document.forms["reviews"];
			if(!obj)obj=document.forms[1]; //临时方案，兼容之前未更新的页面
			if(obj)
			{ obj.remark.value="";
				obj.send_review.disabled=false;
			}
		}  
  }else MarqueeInit();	
}

function getCookie(c_name,c_key) 
{ var mycookie=document.cookie;
	if(mycookie)
	{	var re = new RegExp(";\\s*"+c_name+"=([^;]*)","i"); 
		if(re.exec(";"+mycookie))
		{	mycookie=RegExp.$1;
			if(c_key)
	 	  {	re = new RegExp("\\b"+c_key+"=([^&]*)","i"); 
	 	  	if(re.exec(mycookie))
	 	  	{ mycookie=RegExp.$1;
	 	  		return unescape(mycookie);
	 	  	}
	 	  }else return unescape(mycookie);
		}
	}
	return null;
}	

function setCookie(c_name,c_value)
{ document.cookie = c_name + "=" + escape(c_value);
}

/*cookie字段名必须为字母且不能有下划线都特殊符号，否则会造服务器端与客户端的操作不匹配。*/
function setCookie2(c_name,c_key,c_value,c_path,c_expires)
{ var mycookie;
	c_value=escape(c_value);
	if(c_key)
	{	mycookie=document.cookie;//"test=a=1a&b=1b&c=1c;ttest=a=2a&b=2b&c=2c;ttest=a=3a&b=3b&c=3c;tttesttt=a=4a&b=4b&c=4c;test_t";
	  if(mycookie)
	  { var re = new RegExp(";\\s*"+c_name+"=([^;]*)","i"); 
		  if(re.exec(";"+mycookie))
		  { mycookie=RegExp.$1;
		  	re = new RegExp("\\b"+c_key+"=[^&]*","i"); 
	 	  	if(re.test(mycookie))c_value=mycookie.replace(re,c_key+"="+c_value);
	 	  	else c_value=mycookie+"&"+c_key+"="+c_value;
		  }else c_value=c_key+"="+c_value;
	  }else c_value=c_key+"="+c_value;
	}
	mycookie=c_name + "=" + c_value;
	if(c_path)mycookie+=";path="+c_path;
	if(c_expires)mycookie+=";expires="+c_expires.toGMTString();
  document.cookie = mycookie;
}
  
function MM_showHideLayers(layerid,bShow)
{ var obj=document.getElementById(layerid);
  if (obj)
  { if (obj.style)obj=obj.style
    obj.visibility = (bShow)? "visible":"hidden";
  } 
}

function refresh_vcode()
{ var obj=document.getElementById("LoginCheckout");
	if(obj)
	{ obj.src=WebRoot+"user/authcode.php?handle="+Math.random();
		if(!gotvcode)gotvcode=true;
  }	
}
    
function login_platform_reset()
{ var outtext="<form style='margin:0px' onsubmit='return userlogin(this);'>&nbsp;&nbsp;用户:<input name='username' type='text' maxlength='32' style='height:21px;width:70px'>";
  outtext+="&nbsp;密码:<input name='password' type='password' maxlength='32' style='height:21px;width:70px'>"
  outtext+="&nbsp;验证:<input name='verifycode' type='text'  maxlength='4' style='height:21px;width:50px;' onfocus='if(!gotvcode)refresh_vcode();'>"
  outtext+="&nbsp;<IMG id='LoginCheckout' src='"+WebRoot+"images/code.gif' align='absMiddle' onclick='if(!gotvcode)refresh_vcode();'>"
  outtext+="&nbsp;<input type='submit' value='登录'>";
  outtext+="&nbsp;<input type='button' value='注册' onClick=\"self.location.href='"+WebRoot+"reg.php'\"></form>";
  document.getElementById("loginfo").innerHTML=outtext;	
  document.getElementById("userlogo").style.background = "url("+WebRoot+"images/navbg_login1.gif )";
  UpdateMyMsgState();
}  

function UpdateMyMsgState()
{ var obj=document.getElementById("msginfobox");
	if(obj)
	{ var unreadmsg=StrToInt(getCookie("cmshop","unreadmsg"));
		if(unreadmsg<=0) obj.innerHTML='站内信(0)'; 
		else obj.innerHTML='站内信<img src="'+WebRoot+'images/mail.gif" border="0" width="13" height="13" align="absMiddle"><b>('+unreadmsg+')</b>'; 
  }
}

function Check_Loginfo(loginfo)
{ return (loginfo && loginfo.indexOf("欢迎")>=0);
}
function Write_Loginfo(loginfo)
{ if(OnlineUserID)setCookie("cmshop"+OnlineUserID,loginfo);
}
function Read_Loginfo()
{ return (OnlineUserID)?getCookie("cmshop"+OnlineUserID):null;
} 

function login_show_uerinfo(info)
{ var obj1=document.getElementById("loginfo");
	var obj2=document.getElementById("userlogo");
	if(obj1)obj1.innerHTML=info;
  if(obj2)obj2.style.background = "url("+WebRoot+"images/navbg_login2.gif )";
  UpdateMyMsgState();
}

function check_userinfo()
{ OnlineUserID = getCookie("cmshop","userid");
  if(OnlineUserID && !isNaN(OnlineUserID))
  { var ret=Read_Loginfo();
  	if(!Check_Loginfo(ret))
  	{ ret=PostForm("",WebRoot+"user/login.php?mode=getinfo&userid="+OnlineUserID);
  		if(Check_Loginfo(ret))Write_Loginfo(ret);
  		else ret=null;
  	}
  	if(ret)login_show_uerinfo(ret);
    else userlogoff();
  }else login_platform_reset();
  InitSearchForm(document.forms["topsearch"]);
}	

function userlogin(objform)
{	var username,password,verifycode;
  username=objform.username.value.trim();
	if(username=="")
	{ alert("请输入用户名！");
		objform.username.focus();
		return false;
	}
	else
	{ objform.username.value=username;
	}
	password=objform.password.value;
	if(password=="")
	{ alert("请输入密码！");
		objform.password.focus();
		return false;
	}
	
	verifycode=objform.verifycode.value;
	if(verifycode=="")
	{ alert("请输入验证码！");
		objform.verifycode.focus();
		return false;
	}
	
	if(MirrorSite==1)
	{ objform.target="_top";
    objform.method="post";
    objform.action=WebRoot+"remotelogin.asp"; 
    return true;
	}
	
	var formcontent = "username="+escape(username)+"&password="+escape(password)+"&verifycode="+verifycode;

  var ret=PostForm(formcontent,WebRoot+"user/login.php");
  if(ret)
  { if(Check_Loginfo(ret))
  	{ OnlineUserID = getCookie("cmshop","userid");
      Write_Loginfo(ret);
  		if(document.getElementById("userbox"))self.location.reload();
      else login_show_uerinfo(ret);
  	}
  	else if(ret.indexOf("验证码无效")>=0)
    { refresh_vcode();
      alert("验证码无效，请重新输入！");
    } else alert(ret);
  }else alert("系统正忙，请稍候再试！");
  return false;
}

function userlogoff()
{ var ret=PostForm("",WebRoot+"login.php?mode=logout");
  if(ret=="ok")
  { alert("您已正常退出商城！");
    if(document.getElementById("userbox"))self.location.reload();
    else login_platform_reset();
  }
}
   
function show_new_orders()
{ var obj=document.getElementById("ordersview");
	if(obj)obj.innerHTML="<br><p alin=center><img src='/images/loading3.gif' alt='loading....'></p>"
}

function load_banner_flash()
{ var obj=document.getElementById("tpbanner1");
  if(obj)obj.innerHTML='<table height="80" cellspacing=0 cellpadding=0 border=0><tr><td width=115 rowspan=2 style="BACKGROUND:url(/images/banner_logo.gif) center center no-repeat;"></td><td width=170 height=40 style="BACKGROUND:url(/images/banner_part1.gif) center center no-repeat;"></td></tr><tr><td height=40 style="BACKGROUND:url(/images/banner_part2.gif) center center no-repeat;"></td></tr></table>';
  obj=document.getElementById("tpbanner2");
  if(obj)obj.innerHTML='<TABLE width="100%" height="40" cellSpacing=0 cellPadding=0 border=0><TR><TD width=26></TD><TH width=31></TH><TH width=31></TH><TH width=31></TH><TH width=31></TD><TH width=31></TH><TD width=638><!--object width="420" height="40"  classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0"><param name="wmode" value="transparent"><param name="bgcolor" value="#ffffff"><param name="quality" value="high"><param name="allowScriptAccess" value="sameDomain"><param name="movie" value="/images/announce.swf"><embed src="/images/announce.swf" quality="high" wmode="transparent" bgcolor="#ffffff" width="420" height="40" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></object--></TD><TH width=31></TH><TH width=31></TH><TH width=31></TH><TH width=31></TH><TH width=31></TH><TD width=26></TD></TR></TABLE>';
}
 
function page_head_init(tabIndex)
{  check_userinfo();
	 SetMainMenuItem(tabIndex);
	 load_banner_flash();
}
