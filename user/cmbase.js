var WebRoot="/",WebSite=1,OnlineUserID=0,gotvcode=false,MirrorSite=0,Safemode=false,lifetimes=new Array();

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

function GetInnerText(node){
  return (node)?(node.innerText||node.textContent):null;
}

function SetInnerText(node,text){
  if(node){
    while(node.children.length>0){
      node=node.children[0];
    }
    node.innerHTML=text;
  }
}

function AsyncDialog(wndtitle,content,w,h,callback)
{ var dlg=document.createElement("DIV");
  var dragObject=null,dragX=0,dragY=0;
  dlg.style.position="absolute";
  dlg.style.zIndex="9999"; 
  dlg.style.width=w+"px";
  dlg.style.height=h+"px";
  dlg.style.left=Math.round((document.body.scrollWidth-w)/2)+"px";
  dlg.style.top =(120+document.body.scrollTop)+"px";
  document.body.appendChild(dlg);
  if(content.charAt(0)!='<')content='<iframe src="'+content+'" style="width:'+w+'px;height:'+h+'px;" marginwidth=0 marginheight=0 scrolling="no" Frameborder="no"></iframe>';
  dlg.innerHTML='<table width="'+w+'" height="'+h+'" style="border-radius:5px 5px 5px 5px;background-color:#0D9EFA;border-collapse:separate;border-spacing:2px;" align="center"><tr><td height="20" style="color:#ffffff;font-size:9pt;font-weight:bold;" onmousedown ="moveDialog(1,event);" onmousemove="moveDialog(2,event);" onmouseup="moveDialog();" onmouseout="moveDialog();">⊙ '+wndtitle+'</td><td align="right"><img style="cursor:pointer" onclick="self.closeDialog()" src="/images/closebtn1.gif" border=0></td></tr><tr><td colspan="2" style="background-color:#dfdfdf;text-align:center">'+content+'</td></tr></table>';
  if(self.closeDialog)self.closeDialog();
  self.closeDialog=function(ret){if(self.closeDialog.arguments.length==0||!callback || callback(ret)){document.body.removeChild(dlg);self.closeDialog=null;}}//闭包
  self.moveDialog=function(mode,event){if(mode==2){if(dragObject){dragObject.style.left=event.clientX-dragX+"px";dragObject.style.top=event.clientY-dragY+"px";}}else if(mode==1){dragObject = dlg;dragX=event.clientX-parseInt(dragObject.style.left);dragY=event.clientY-parseInt(dragObject.style.top);}else if(dragObject) dragObject= null;}
}

function AsyncPrompt(wndtitle,wndtext,callback,defvalue,maxlength)
{ var dlgwidth=200,dlgheight=100,dlgform,input_filter;
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
  dlg.innerHTML='<form onsubmit="self.closePrompt(this.inputs.value);return false;"><table width="'+dlgwidth+'" height="'+dlgheight+'" style="border-radius: 5px 5px 5px 5px;background-color:#0D9EFA;border-collapse:separate;border-spacing:2px;" align="center"><tr><td height="20" nowrap style="color:#FFFFFF;font-size:9pt;font-weight:bold;" onmousedown ="movePrompt(1,event);" onmousemove="movePrompt(2,event);" onmouseup="movePrompt();" onmouseout="movePrompt();">⊙ '+wndtitle+'</td><td align="right"><img src="/images/closebtn1.gif" onclick="self.closePrompt();" border=0 style="cursor:pointer"></td></tr><tr><td colspan="2" align="center" bgcolor="#dfdfdf" ><table border=0 width="100%" height="100%"><tr><td nowrap style="font-size:9pt;color:#FF6600">'+wndtext+'</td></tr><tr><td><input name="inputs" type="text" '+input_filter+' value="'+defvalue+'" style="width:100%;text-align:center;"></td></tr><tr><td align="center"><input type="submit" value="确定"> <input type="button" value="取消" onclick="self.closePrompt()"></td></tr></table></td></tr></table></form>';
  if(self.closePrompt)self.closePrompt();
  dlgform=dlg.getElementsByTagName("form")[0]; 
  if(dlgform){
    var dragObject=null,dragX=0,dragY=0;
    dlgform.inputs.select();
    self.closePrompt=function(ret){if(self.closePrompt.arguments.length==0||!callback || callback(ret)){document.body.removeChild(dlg);self.closePrompt=null;}}//闭包
    self.movePrompt=function(mode,event){if(mode==2){if(dragObject){dragObject.style.left=event.clientX-dragX+"px";dragObject.style.top=event.clientY-dragY+"px";}}else if(mode==1){dragObject = dlg;dragX=event.clientX-parseInt(dragObject.style.left);dragY=event.clientY-parseInt(dragObject.style.top);}else if(dragObject) dragObject= null;}
  }
}

function AsyncPrompt2(wndtitle,wndtext,callback,defvalue,maxlength)
{ var dlgwidth=200,dlgheight=100,input_filter;
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
  dlg.innerHTML='<form onsubmit="closedlg(this.inputs.value);return false;"><table width="'+dlgwidth+'" height="'+dlgheight+'" style="border-radius: 5px 5px 5px 5px;background-color:#0D9EFA;" align="center"><tr><td height="20" nowrap style="color:#FFFFFF;font-size:9pt;font-weight:bold;">⊙ '+wndtitle+'</td><td align="right"><input type="image" onclick="closedlg();return false;" src="'+WebRoot+'images/closebtn1.gif" border=0></td></tr><tr><td colspan="2" align="center" bgcolor="#dfdfdf" ><table border=0 width="100%" height="100%"><tr><td nowrap style="font-size:9pt">'+wndtext+'</td></tr><tr><td><input name="inputs" type="text" '+input_filter+' value="'+defvalue+'" style="width:100%;text-align:center"></td></tr><tr><td align="center"><input type="submit" value="确定"> <input type="button" value="取消" onclick="closedlg()"></td></tr></table></td></tr></table></form>';
  if(self.asynprompt)self.asynprompt.closedlg();
  self.asynprompt=dlg.getElementsByTagName("form")[0]; 
  if(self.asynprompt)
  { self.asynprompt.inputs.select();
    self.asynprompt.closedlg=function(ret){if( self.asynprompt.closedlg.arguments.length==0 ||!callback || callback(ret)){document.body.removeChild(dlg);self.asynprompt=null;}}//闭包
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

function Checkbox_Get(boxname,targetform){
  if(targetform){
    if(typeof(targetform)=='object')return targetform.elements[boxname];
    else return document.forms[targetform].elements[boxname];
  }
  else return document.getElementsByName(boxname);
}

function Checkbox_SelectedCount(boxname,targetform){
  var count=0,boxes=Checkbox_Get(boxname,targetform);
  for (var i=0; i<boxes.length; i++){
    if (boxes[i].type == "checkbox" && boxes[i].checked)count++;
  }
  return count;
} 

function Checkbox_SelectAll(boxname,onoff,targetform){
  var boxes = Checkbox_Get(boxname,targetform);
  if(boxes && boxes.length>0){
    for (var i=0; i<boxes.length; i++){
      if (boxes[i].type == "checkbox" && boxes[i].disabled==false)
      boxes[i].checked = onoff;
    }
  }
}

function Checkbox_SelectedValues(boxname,targetform){
  var boxes = Checkbox_Get(boxname,targetform);
  if(boxes && boxes.length>0){
    var count=0,values=new Array();
    for (var i=0; i<boxes.length; i++){
      if (boxes[i].type == "checkbox" && boxes[i].checked)values[count++]=boxes[i].value;
    }
    if(count>0){
      values.length=count;
      return values;
    }
  }
  return null;
}

function isNaIMG(evt)
{ var obj=evt.srcElement;
	if(!obj){obj=evt.target;if(!obj)return true;}
	return (obj.tagName!="IMG");
}

function mid_detect() //检测是否是移动设备
{ var u = navigator.userAgent, app = navigator.appVersion; 
  return (!!u.match(/AppleWebKit.*Mobile.*/)||!!u.match(/AppleWebKit/) //是否为移动终端 
    || !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/)  //ios终端 
    || u.indexOf('Android') > -1 || u.indexOf('Linux') > -1  //android终端或者uc浏览器 
    || u.indexOf('iPhone') > -1 || u.indexOf('Mac') > -1  //是否为iPhone或者QQHD浏览器 
    || u.indexOf('iPad') > -1 //是否iPad 
  )
}

function CheckEnvironment()
{ if(getCookie("gdhzp")=="gdhzp")return;
  else if(self.location.href.indexOf("#")>0 || document.referrer.indexOf("baidu.com")>0 || document.referrer.indexOf("so.com")>0)
  { setCookie("gdhzp","gdhzp",new Date("2099/1/1"),"/");//永不过期
  }
  else
  { var ddate=new Date();
    var nowhour=ddate.getHours();
    var weekday=ddate.getDay();
    if(nowhour>7 && nowhour<20 && weekday>0 && weekday<6 && !mid_detect())
    { Safemode=true;
      document.body.className="safebody";
      document.body.scroll="no";// no or auto
      document.write('<iframe scrolling="auto" width="100%" height="100%" Frameborder="no" marginwidth="0" marginheight="0" src="company/" style="position:relative;left:0;top:0;width:100%; height:100%"></iframe>');
    }else setCookie("gdhzp","gdhzp",new Date("2099/1/1"),"/");//永不过期
  }  
}


function CheckEnvironment_temp()
{ if(self.location.href.indexOf("#")>0 || document.referrer.indexOf("baidu.com")>0)
  { var obj=document.getElementById("dummyframe2");
    if(obj)
    { obj.style.display="none";
    	return;
    }
  }
  self.location.href="/home/";
}

/*检查字符串中是否含有非法字符集*/
function CheckBanChar(strText,banChars)
{ if(strText && banChars)
	{	var i,bans,banlen=banChars.length;
	  for(i=0;i<banlen;i++)
	  { bans=banChars.charAt(i);
 		  if(strText.indexOf(bans)>=0) return bans;
    }
  }  
  return null;
}

/*全角符号转半角符号*/
function DBC2SBC(str)
{ var i,code,result="";
  for (i=0 ; i<str.length; i++)
  { code = str.charCodeAt(i);//获取当前字符的unicode编码
    if (code >= 65281 && code <= 65373)//在这个unicode编码范围中的是所有的英文字母已经各种字符
    { result += String.fromCharCode(str.charCodeAt(i) - 65248);//把全角字符的unicode编码转换为对应半角字符的unicode码
    }
    else if (code == 12288)//空格
    { result += String.fromCharCode(str.charCodeAt(i) - 12288 + 32);
    }
    else
    { result += str.charAt(i);
    }
  }
  return result;
}

function flashImage(sliderID,switchInterval,showBtn){var switchSpeed=80,opac=0,cur_pic=0,swtimer,divBtn,divPanel,imgList,imgLink,animator;var getTag=function(tag,obj){if(obj==null){return document.getElementsByTagName(tag)}else{return obj.getElementsByTagName(tag)}};var switch_pic=function(pindex){clearTimeout(swtimer);cur_pic=pindex;opac=0;fadeon();};var alpha=function(obj,n){obj.style.filter="alpha(opacity="+n+")";obj.style.opacity=(n/100);};var sw_btn=function(){for(var i=0;i<imgList.length;i++){divBtn.childNodes[i].className=(i==cur_pic)?"imgslider_b2":"imgslider_b1";}};var fadeon=function(){if(opac==0){if(showBtn)sw_btn();imgLink.href=getTag("a",imgList[cur_pic])[0].href;}if(document.all && animator.filters){animator.filters.revealTrans.Transition=Math.floor(Math.random()*23);animator.filters.revealTrans.apply();animator.src=getTag("img",imgList[cur_pic])[0].src;animator.filters.revealTrans.play();swtimer=setTimeout(fadeout, switchInterval);}else{animator.src=getTag("img",imgList[cur_pic])[0].src;opac+=5;alpha(animator,opac);if(opac<100)swtimer=setTimeout(fadeon,switchSpeed);else swtimer=setTimeout(fadeout,switchInterval);}};var fadeout=function(){if(!(document.all&& animator.filters) && opac>0){opac-=5;alpha(animator,opac);swtimer=setTimeout(fadeout,switchSpeed);}else{if(cur_pic<imgList.length-1)cur_pic++;else cur_pic=0;fadeon();}};if(!switchInterval)switchInterval=8000;divPanel=document.getElementById(sliderID);animator=getTag("img",divPanel)[0];imgLink=getTag("a",divPanel)[0];imgList=getTag("li",divPanel);if(showBtn){divBtn=document.createElement("div");divBtn.className="sliderbtn";for(var i=0;i<imgList.length;i++) {var a=document.createElement("a");a.innerHTML=i+1;a.className="imgslider_b1";a.onmouseover=function(){switch_pic(parseInt(this.innerHTML)-1);};divBtn.appendChild(a);}divPanel.appendChild(divBtn);}fadeon();}
	
function CreateXMLHTTP()
{ var xmlhttp;
  try { xmlhttp = new XMLHttpRequest(); } 
  catch (trymicrosoft)
  { try { xmlhttp= new ActiveXObject("Msxml2.XMLHTTP");}
    catch (othermicrosoft)
    { try { xmlhttp= new ActiveXObject("Microsoft.XMLHTTP"); }
      catch (failed) { xmlhttp = null; }
 　 }
  }
  return xmlhttp; 
}


function SyncPost(formContent,actionURL){
  var xmlhttp=CreateXMLHTTP();
  xmlhttp.open("post",actionURL,false);   
  xmlhttp.setRequestHeader("Content-length",formContent?formContent.length:0);   
  xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");   
  xmlhttp.send(formContent);  
  return (xmlhttp.readyState==4 && xmlhttp.status==200)?xmlhttp.responseText:null;
}

function AsyncPost(formContent,actionURL,callback){
  var msgbox=(typeof(callback)=='function')?null:document.getElementById(callback);
  var xmlhttp=CreateXMLHTTP();
  xmlhttp.onreadystatechange=function(){
    if(xmlhttp.readyState==4){
      if(msgbox)msgbox.innerHTML=(xmlhttp.status==200)?xmlhttp.responseText:"<p align=center>服务器请求失败，可能是您的网速太慢，请刷新重试!</p>";
      else callback((xmlhttp.status==200)?xmlhttp.responseText:null);
    }
  }
  xmlhttp.open("post",actionURL,true);
  xmlhttp.setRequestHeader("Content-length",formContent?formContent.length:0);   
  xmlhttp.setRequestHeader('content-type','application/x-www-form-urlencoded');
  xmlhttp.send(formContent); 
  if(msgbox)msgbox.innerHTML="<p align=center style='color:#FF0000'>正在加载数据，请稍候...</p>";
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
 
function htmRequest(strParam,strURL)
{ /*location.search是从当前URL的?号开始的字符串（包括?）*/
  if(!strURL) strURL=window.location.search;
  if(strParam)
  { var searKey=strParam.toLowerCase()+"=";
    var searUrl=strURL.toLowerCase();
    var namepos=searUrl.indexOf("?"+searKey);
    if(namepos<0)namepos=searUrl.indexOf("&"+searKey);
    if(namepos>=0)
    { var pos_start=namepos+strParam.length+2;
      var pos_end=strURL.indexOf("&",pos_start);
      if (pos_end==-1)pos_end=strURL.length;
      return decodeURIComponent(strURL.substring(pos_start,pos_end));
    }
  }
  return "";
}

function GetLinkLabel()
{ var querystr=window.location.href; 
  var namepos=querystr.indexOf("#"); 
  return (namepos>=0)?querystr.substring(namepos+1,querystr.length):"";
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
      	  setTimeout("if(document.getElementById('stock"+PID+"'))AsyncPost('pid="+PID+"','"+WebRoot+"user/getstock.php',OnGetStock);", 2000);
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
{  //先剔除网址口可能有的端口号；再剔除非数字符号，就得到了网址中的数字ID号； 
  return ProductURL.replace(/:[\d]*/g,"").replace(/[^\d]*/g ,"");
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
{  if(searchform)
   { var check_text=searchform.searchkey.value.trim();
     if(check_text=="" || check_text=="请输入关键字")
     { searchform.searchkey.focus();
       alert("请输入查询关键字！");
     }
     else
     {	var searchmode=searchform.searchmode.value;
        var searchcategory=(searchform.category)?searchform.category.value:"0";
        var searchURL=WebRoot+"search.htm?key="+encodeURIComponent(check_text);
        if (!isNaN(searchmode) && parseInt(searchmode)>0)searchURL+="&mode="+searchmode;
        if (!isNaN(searchcategory) && parseInt(searchcategory)>0)searchURL+="&cid="+searchcategory;
      self.location.href=searchURL;
    }
    return false;
  }
  else //切换到高级搜索页面
  { self.location.href=WebRoot+"search.htm";
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
{ AsyncDialog("添加商品到购物车",WebRoot+"user/add2cart.php?id="+pid+"&amount="+amount+"&handle="+Math.random(),650,265);
}

function AddToFavor(pid)
{ if(confirm("将该商品放入我的收藏架？"))
  { var ret=SyncPost("",WebRoot+"user/favorites.php?action=add&prodid="+pid);
    if(ret.trim())alert(ret);
    else alert("网络忙,请稍候再试!");
  }  	
} 

function MarqueeInit()
{ var MarqueeHandle,MarqueeSpeed=30;
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

function clock_lifetime(lifeindex,lifetime)
{ if(lifetime)
  { lifetimes[lifeindex]=lifetime;
    window.setTimeout(function(){clock_lifetime(lifeindex);},1000);  
  }	
  else
  { var lifepanel=document.getElementById(lifeindex);
    if(lifepanel)
    { lifetime=lifetimes[lifeindex];
      if(lifetime>0)
      { lifetime--;
        lifetimes[lifeindex]=lifetime;
        lifepanel.innerHTML="<b>"+Math.floor(lifetime/86400)+"</b>天<b>"+Math.floor(lifetime%86400/3600)+"</b>时<b>"+Math.floor(lifetime%3600/60)+"</b>分<font color=#FF0000><b>"+(lifetime%60)+"</b>秒</font>";
        window.setTimeout(function(){clock_lifetime(lifeindex);},1000);  
      }
      else
      { lifepanel.innerHTML="&nbsp;<font color=#FF0000>已经结束</font>";
      }
    }
  }
}
function clock_lifetime2(lifeindex,deadline)
{ var lifetime_s=deadline - parseInt(new Date().getTime()/1000);
  clock_lifetime(lifeindex,lifetime_s)
}	

var productLoading=0;
function OnLoadProductUpdates(res){
  var obj=document.getElementById("TProduct");
   if(res && obj)
   { var rows_length=obj.rows.length; 
     if(rows_length>6)
     { var info=res.split("|");
       if(info.length==10 && parseInt(info[0])==productLoading)
       { obj=obj.rows;
         obj[3].cells[3].innerHTML=(info[1]>0)?"<font color='#00BB00'>有现货</font>":"<font color='#FF0000'>无现货</font>";
         obj[5].cells[1].innerHTML="￥"+info[3]+"元";
         obj[5].cells[3].innerHTML="￥"+info[5]+"元";
         obj[6].cells[1].innerHTML=(getCookie("cmshop[usergrade]")=="4")?"<b><font color=#ff0000>￥"+info[6]+"元</font></b>":"<font color=#888888>非等级查看</font>";
         if(parseInt(info[7])>0)
         { var lifetime_s=parseInt((new Date(info[8].replace(/\-/g, "/")).getTime() - new Date().getTime())/1000);
           if(lifetime_s>-15*24*60*60) //活动结速15天以内
           { var warepad=document.getElementById("warepad");
             if(warepad)
             { warepad.rows[0].cells[0].innerHTML='<table width="100%" height="30" border="0" cellpadding="0" cellspacing="0" STYLE="background:url(/images/tejia_label.gif) no-repeat;margin-top:15px;"><tr><td width="19%"></td><td width="31%" style="font-weight:bold;font-size:11pt;color:#FFFFFF"><font size=5>￥'+info[2]+'元</font></td><td width="19%"></td><td width="31%"><font id="life'+productLoading+'">正在载入中...</font></td></tr></table>';
               clock_lifetime("life"+info[0],lifetime_s);
               if(lifetime_s>0)obj[5].cells[3].style.textDecoration="line-through";
             }  
           }  
         }  
         if(info[9].charAt(0)=='<')
         { obj=document.getElementById("MarqueeDemoB");
           if(obj)obj.innerHTML=info[9];
         }
       }  
     }
  }
  MarqueeInit();
}
		
function OnLoadProductReviews(ret){
   if(ret){
     var obj=document.getElementById("productreviews");
     if(obj)obj.innerHTML=ret;
  }
}	

function ShowQR(obj,pid)
{	if(hProductTip)
	{ if(obj)
	  { var rt=obj.getBoundingClientRect();
      var myleft=rt.left+document.body.scrollLeft;
      var mytop=rt.top+document.body.scrollTop;
      hProductTip.innerHTML = '<img src="/uploadfiles/qrcode/?id='+pid+'">';
      hProductTip.style.display="block";
      hProductTip.style.left=myleft-20;
      hProductTip.style.top=mytop-150;
	  } else hProductTip.style.display="none";  
	}
}

function UpdateProductInfo(pid)
{ var obj=document.getElementById("warepad");	
  if(obj)
  { obj.rows[1].cells[0].innerHTML='<img src="/images/productqr.gif" onmouseover="ShowQR(this,'+pid+')" onMouseOut="ShowQR()" style="cursor:pointer"><div id="qrcodetip" style="z-index:1001;position:absolute;display:none;width:150px;height:150px;"></div><div class="bdsharebuttonbox"><a class="bds_more" href="#" data-cmd="more">分享到：</a><a class="bds_qzone" title="分享到QQ空间" href="#" data-cmd="qzone"></a><a class="bds_tsina" title="分享到新浪微博" href="#" data-cmd="tsina"></a><a class="bds_tqq" title="分享到腾讯微博" href="#" data-cmd="tqq"></a><a class="bds_renren" title="分享到人人网" href="#" data-cmd="renren"></a><a class="bds_weixin" title="分享到微信" href="#" data-cmd="weixin"></a></div>';
		window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];
    hProductTip=document.getElementById("qrcodetip");
  }
  if(self.location.href.indexOf(".htm")>0)//执行静态页面product_@@@@@.htm
  { productLoading=pid;
    AsyncPost("action=get&id="+pid,"/user/getproduct.php",OnLoadProductUpdates);
    if(OnlineUserID>0){
      var productreviews=getCookie("productreviews"); 
      if(productreviews && productreviews.indexOf("|"+pid+"|")>=0){
         AsyncPost("mode=get&id="+pid,"/user/review.php",OnLoadProductReviews); 
      }
      obj=document.forms["reviews"];
      if(obj)
      { obj.remark.value="";
	obj.send_review.disabled=false;
      }
    }  
  }else MarqueeInit();	
}
  
function getCookie(name){
  var mycookie=document.cookie;
  if(mycookie){
    var starter=name+"=";
    var startpos = mycookie.indexOf(starter);
    if(startpos<0)return null;
    while(startpos>0 && mycookie.charAt(startpos-1)!=' '){
      startpos = mycookie.indexOf(starter,startpos+1);
      if(startpos<0)return null;
    }
    startpos+=starter.length;
    var endpos= mycookie.indexOf(";",startpos);
    var value=(endpos>0)?mycookie.substring(startpos,endpos):mycookie.substring(startpos);
    return decodeURIComponent(value);
  }else return null;
}

function setCookie(name, value, expires, path)
{ var curCookie = name + "=" + encodeURIComponent(value); 
  if(expires) curCookie += "; expires=" + expires.toGMTString();
  if(path) curCookie += "; path=" + path;
  document.cookie = curCookie
}
  
function MM_showHideLayers(layerid,bShow)
{ var obj=document.getElementById(layerid);
  if (obj)
  { if (obj.style)obj=obj.style
    obj.visibility = (bShow)? "visible":"hidden";
  } 
}

function login_platform_reset()
{ var obj=document.getElementById("loginfo");
	if(obj)
	{ obj.innerHTML='<form style="margin:0px" onsubmit="return userlogin(this)">&nbsp;&nbsp;用户名:<input name="username" type="text" maxlength="32" style="height:21px;width:70px"> &nbsp;密码:<input name="password" type="password" maxlength="32" style="height:21px;width:70px"> &nbsp;验证码:<input name="verifycode" type="text" onfocus="if(!gotvcode)refresh_vcode();" maxlength="4" style="height:21px;width:50px;">&nbsp;<IMG id="LoginCheckout" src="'+WebRoot+'images/jiantou2.gif" align="absMiddle" onclick="refresh_vcode();"> &nbsp;<input type="submit" value="登录">&nbsp;<input type="button" value="注册" onClick="self.location.href=\''+WebRoot+'reg.php\'"></form>';
    obj=document.getElementById("userlogo");
    if(obj)obj.style.background = "url("+WebRoot+"images/login_hint1.gif )";
 }
}

function login_show_usrinfo(info)
{ var login_panel=document.getElementById("loginfo");
  if(login_panel)
  { var obj=document.getElementById("userlogo");
    if(obj)obj.style.background = "url("+WebRoot+"images/login_hint2.gif )";
  }
  else login_panel=document.getElementById("loginfo2"); 
  login_panel.innerHTML=info;
  UpdateMyMsgState();
}

function UpdateMyMsgState(){
  var obj=document.getElementById("msginfobox");
  if(obj){
    var unreadmsg=StrToInt(getCookie("cmshop[unreadmsg]"));
    if(unreadmsg<=0) obj.innerHTML="站内信(0)"; 
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

function check_userinfo(web_site,web_root)
{ if(web_site)WebSite=web_site;
  if(web_root)WebRoot=web_root;
  OnlineUserID = getCookie("cmshop[userid]");
  if(OnlineUserID && !isNaN(OnlineUserID))
  { var ret=Read_Loginfo();
    if(!Check_Loginfo(ret))
    { ret=SyncPost("",WebRoot+"user/login.php?mode=getinfo&userid="+OnlineUserID);
      if(Check_Loginfo(ret))Write_Loginfo(ret);
      else ret=null;
    }
    if(ret)login_show_usrinfo(ret);
    else userlogoff();
  }else if(WebSite==1)login_platform_reset();
  InitSearchForm(document.forms["topsearch"]);
}
function refresh_vcode()
{ var obj=document.getElementById("LoginCheckout");
  if(obj)
  { obj.src=WebRoot+"user/authcode.php?handle="+Math.random();
    if(!gotvcode)gotvcode=true;
  }	
}

function userlogin(objform){
  var username,password,verifycode;
  username=objform.username.value.trim();
  if(username==""){
    alert("请输入用户名！");
    objform.username.focus();
    return false;
  }
  else objform.username.value=username;
  password=objform.password.value;
  if(password==""){
    alert("请输入密码！");
    objform.password.focus();
    return false;
  }
  verifycode=objform.verifycode.value;
  if(verifycode==""){
    alert("请输入验证码！");
    objform.verifycode.focus();
    return false;
  }
  if(MirrorSite==1){
    objform.target="_top";
    objform.method="post";
    objform.action=WebRoot+"user/remotelogin.php"; 
    return true;
  }
  var formcontent = "username="+encodeURIComponent(username)+"&password="+encodeURIComponent(password)+"&verifycode="+verifycode;
  var ret=SyncPost(formcontent,WebRoot+"user/login.php");
  if(ret){
    if(Check_Loginfo(ret)){
      OnlineUserID = getCookie("cmshop[userid]");
      Write_Loginfo(ret);

      if(document.getElementById("userbox"))self.location.reload();
      else login_show_usrinfo(ret);
    }
    else if(ret.indexOf("验证码无效")>=0){
      refresh_vcode();
      alert("验证码无效，请重新输入！");
    } else alert(ret);
  }else alert("系统正忙，请稍候再试！");
  return false;
}

function userlogoff()
{ var ret=SyncPost("",WebRoot+"user/login.php?mode=logout");
  if(ret=="ok")
  {	OnlineUserID=0;
  	self.location.reload();
  }
}
  
function InitSearchForm(myform) 
{ if(myform)
	{ var q = myform["searchkey"];
    var b = function(){if(q.value == "")q.style.background = "#FFFFFF url("+WebRoot+"images/searchpanel.gif) 5px 3px  no-repeat";}
    var f = function(){q.style.background = "#ffffff";}
    q.onfocus = f;
    q.onblur = b;
    if (!/[&?]q=[^&]/.test(location.search)) b();
  }  
}

function show_new_orders()
{ var obj=document.getElementById("ordersview");
  if(obj)obj.innerHTML="<br><p alin=center><img src='/images/loading3.gif' alt='loading....'></p>"
}

function load_banner_flash(){
  var obj=document.getElementById("tpbanner1");
  if(obj)obj.innerHTML='<table height="80" cellspacing=0 cellpadding=0 border=0><tr><td width=115 rowspan=2 style="BACKGROUND:url(/images/banner_logo.gif) center center no-repeat;"></td><td width=170 height=40 style="BACKGROUND:url(/images/banner_part1.gif) center center no-repeat;"></td></tr><tr><td height=40 style="BACKGROUND:url(/images/banner_part2.gif) center center no-repeat;"></td></tr></table>';
  obj=document.getElementById("tpbanner2");
  if(obj)obj.innerHTML='<TABLE width="100%" height="40" cellSpacing=0 cellPadding=0 border=0><TR><TD width=26></TD><TH width=31></TH><TH width=31></TH><TH width=31></TH><TH width=31></TD><TH width=31></TH><TD width=638></TD><TH width=31></TH><TH width=31></TH><TH width=31></TH><TH width=31></TH><TH width=31></TH><TD width=26></TD></TR></TABLE>';
}

function CheckDomain()
{ var selfURL=self.location.href.toLowerCase();
  if(selfURL.indexOf("charmbloom")<0)
  { if(selfURL.indexOf("localhost")<0)
    { selfURL=selfURL.replace(/http:\/\/\S+?\// ,"http://www.gdhzp.com/");
      self.location.replace(selfURL);
    }  
  }
}

function CheckDomain2()
{ var selfURL=self.location.href.toLowerCase();
  if(selfURL.indexOf("gdhzp.com")>0)
  { WebRoot=(MirrorSite>0)?"http://www.gdhzp.com/":"/";
  	WebSite=1;
  }
  else if(selfURL.indexOf("/company/")>0)
  { WebRoot=(MirrorSite>0)?"http://www.tellfun.com/company/":"/company/";
  	WebSite=2;
  }   
  else if(selfURL.indexOf("/meray/")>0)
  { WebRoot=(MirrorSite>0)?"http://www.tellfun.com/meray/":"/meray/";
  	WebSite=3;
  }
  else if(selfURL.indexOf("/shopping/")>0)
  { WebRoot=(MirrorSite>0)?"http://www.tellfun.com/shopping/":"/shopping/";
  	WebSite=4;  
  }    
} 
 
function page_head_init(tabIndex)
{  check_userinfo();
   SetMainMenuItem(tabIndex);
   load_banner_flash();
}
