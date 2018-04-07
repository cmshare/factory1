var ROW_EFFECT_COLOR=new Array('#FFFFFF','#FFFF00'/*'#D0F0FF'*/,'#33B8EE');
function mOvr(src){var hicolor=src.getAttribute('hicolor');if(!hicolor)src.bgColor=ROW_EFFECT_COLOR[1];} 
function mOut(src){var hicolor=src.getAttribute('hicolor');if(!hicolor)src.bgColor=ROW_EFFECT_COLOR[0];} 
function mChk(src,ismouseout){var checked=src.checked;src=src.parentNode.parentNode;src.setAttribute('hicolor',(checked)?ROW_EFFECT_COLOR[2]:'');src.bgColor=ROW_EFFECT_COLOR[(checked)?2:(ismouseout?0:1)];} 
function mClk(src){if(event.srcElement.tagName=='TD') src.children.tags('A')[0].click();} 

String.prototype.trim = function(){
  return this.replace(/(^\s*)|(\s*$)/g, ""); 
} 

function GetRandInt(min,max){
 return Math.round(Math.random()*(max-min))+min;
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

function UpdatePagePosition(mode)
{ if(mode==0)  /*save position*/
    setCookie("PagePosition",document.body.scrollTop);
  else if(mode==1) /*load and restore last position*/
    document.body.scrollTop=getCookie("PagePosition");
}

function RadioboxSelected(radioboxes)
{ if(radioboxes)
  { var count=radioboxes.length;
    if(!count)
    { if(radioboxes.checked) return radioboxes.value;
    }
    else
    { for(var i=0;i<count;i++)
      { if(radioboxes[i].checked) return radioboxes[i].value;
      }
    }   
  }
  return null;
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
      if (boxes[i].type == "checkbox" && boxes[i].disabled==false){
        boxes[i].checked = onoff;
        mChk(boxes[i],true);
      }
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

function setCookie(name, value, expires, path)
{ var curCookie = name + "=" + encodeURIComponent(value);
  if(expires) curCookie += "; expires=" + expires.toGMTString();
  if(path) curCookie += "; path=" + path;
  document.cookie = curCookie;
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

function CreateXMLHTTP()
{ var xmlhttp;
  try 
  { xmlhttp = new XMLHttpRequest();
  } 
  catch (trymicrosoft)
  { try
    { xmlhttp= new ActiveXObject("Msxml2.XMLHTTP");
  　}
    catch (othermicrosoft)
    { try
      { xmlhttp= new ActiveXObject("Microsoft.XMLHTTP");
  　　}
      catch (failed)
      { xmlhttp = null;
  　　}
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

function FormSetSelect(form_name,select_name,select_value){
  var targetform=document.forms[form_name];
  if(targetform){
    var serchoptions=targetform.elements[select_name];
    if(serchoptions){
      serchoptions=serchoptions.options;
      for(var i=0;i<serchoptions.length;i++){
        if(select_value==serchoptions[i].value){
          serchoptions[i].selected=true;
          break;
        }
      }
    }
  }
}

function ChangeQueryString(QueryName,NewValue)
{ if(QueryName)
  {  var querystr=window.location.search.toLowerCase();   //location.search是从当前URL的?号开始的字符串
     var namepos=querystr.indexOf(QueryName.toLowerCase()+"=");
  	 NewValue=NewValue.toString();
     if(querystr)
     { if(namepos!=-1)
       { var OriginValue,pos_start,pos_end;
     	   pos_start=namepos+QueryName.length+1;
         pos_end=querystr.indexOf("&",pos_start);
         if (pos_end==-1)pos_end=querystr.length;
         OriginValue=querystr.substring(pos_start,pos_end);
         if(NewValue!=OriginValue)
         { querystr=querystr.substring(0, pos_start)+NewValue+querystr.substring(pos_end,querystr.length);
         }else return;
       }else querystr+=("&"+QueryName+"="+NewValue);
     }else querystr+=("?"+QueryName+"="+NewValue);
     self.location.href=querystr;
  }   
}

function UrlChangePage(OriginQuerystring,NewPage)
{ return (OriginQuerystring)?"?"+OriginQuerystring+"&page="+NewPage:"?page="+NewPage;
}
function GeneratePageGuider(OriginURL,TotalRecords,CurPage,TotalPage)
{ var pagecode='共 <b>'+TotalRecords+'</b> 条记录&nbsp;&nbsp;<font color="#888888">';
  if(CurPage<=1)pagecode+='首页&nbsp;上一页';
  else pagecode+='<a href="'+UrlChangePage(OriginURL,1)+'" target="_self">首页</a>&nbsp;<a href="'+UrlChangePage(OriginURL,CurPage-1)+'" target="_self">上一页</a>';
  pagecode+='&nbsp;';
  if(CurPage>=TotalPage) pagecode+='下一页&nbsp;尾页';
  else pagecode+='<a href="'+UrlChangePage(OriginURL,CurPage+1)+'" target="_self">下一页</a>&nbsp;<a href="'+UrlChangePage(OriginURL,TotalPage)+'" target="_self">尾页</a>';
  pagecode+='</font>&nbsp;页次：<strong><font color="#FF0000">'+CurPage+'</font>/'+TotalPage+'</strong>页&nbsp;&nbsp;'
  pagecode+='转到第<input type="text" id="page_input_box" value="'+CurPage+'" size=3 maxlength=8 style="text-align:center" onFocus="this.select()" onkeyup=\'if(isNaN(value))execCommand("undo")\'  onkeydown=\'if(window.event.keyCode==13){document.getElementById("page_click_btn").click();return false;}\'>页';
  pagecode+='&nbsp;<input type="button" id="page_click_btn" value="跳转" onclick=\'self.location.href=UrlChangePage("'+OriginURL+'",document.getElementById("page_input_box").value)\'>';
  document.write(pagecode);
} 

function PageJump(OriginURL)
{ var re = /([\?\&]page=)[^&]*/i;
	self.location.href=OriginURL.replace(re,"$1"+window.event.srcElement.form.page.value);
}

function StrToInt(numstr)
{ return (numstr && !isNaN(numstr) )?parseInt(numstr) : 0;
}

//检查身份证号码
function CheckIdentity(pId)
{ var arrVerifyCode = [1,0,"X",9,8,7,6,5,4,3,2];
  var Wi = [7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2];
  var Checker = [1,9,8,7,6,5,4,3,2,1,1];
  if(pId.length != 15 && pId.length != 18)    return "身份证号共有 15 码或18位";
  var Ai=pId.length==18 ?  pId.substring(0,17)   :   pId.slice(0,6)+"19"+pId.slice(6,16);
  if (!/^\d+$/.test(Ai))  return "身份证除最后一位外，必须为数字！";
  var yyyy=Ai.slice(6,10) ,  mm=Ai.slice(10,12)-1  ,  dd=Ai.slice(12,14);
  var d=new Date(yyyy,mm,dd) ,  now=new Date();
  var year=d.getFullYear() ,  mon=d.getMonth() , day=d.getDate();
  if (year!=yyyy || mon!=mm || day!=dd || d>now || year<1940) return "身份证输入错误！";
  for(var i=0,ret=0;i<17;i++)  ret+=Ai.charAt(i)*Wi[i];    
  Ai+=arrVerifyCode[ret %=11];     
  return (pId.length ==18 && pId != Ai)?"身份证输入错误！":Ai;        
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

// 参数说明
// s_Type : 文件类型，可用值为"image","flash","media","file"
// s_Link : 文件上传后，用于接收上传文件路径文件名的表单名
// s_named : 文件上传后，指定文件命名。
function showUploadDialog(s_Type,s_named, s_callback)
{  AsyncDialog('文件上传','includes/upload.php?type='+s_Type+'&filenamed='+s_named+'&handle='+Math.random(), 500,30,s_callback);
}	
