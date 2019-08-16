/**QQ客服列表,不要手动修改(由后台自动维护)**************************************/
/**/ var OurQQs=new Array('8030','707166861','8031','1109727945','8050','503681900','8051','838402550','8060','1057924635','8061','271165451');
/*********************************************************************************/
function LoadQQService()
{ var QQNick,qqNumber,OurQQLength,i,ServiceCode,randomoffet,obj;
	obj=document.getElementById("MyPageBottom");
	if(!obj)return;
	OurQQLength=OurQQs.length;
	ServiceCode="";//<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100%><tr><td width=15 height=49 background='/images/qqface/onlinesupport.gif' style='BACKGROUND-REPEAT: no-repeat;' >&nbsp;</td><td>";
	randomoffet=Math.floor((OurQQLength>>1)*Math.random())<<1;
    	
  for(i=0;i<OurQQLength;i+=2)
  { if(randomoffet>=OurQQLength)randomoffet=0;
  	QQNick="涵妆"+OurQQs[randomoffet++];
		qqNumber=OurQQs[randomoffet++];	 
		ServiceCode+="<A title='QQ在线客服:"+qqNumber+"' href='http://wpa.qq.com/msgrd?V=1&uin="+qqNumber+"&Site=涵若铭妆&Menu=yes' target='_blank'><IMG src='http://wpa.qq.com/pa?p=1:"+qqNumber+":4' height=16 border=0>"+QQNick+"</A> ";
	}
  ServiceCode+="<A title='业务合作洽谈,若不在请留言,QQ号码787720462' href='http://wpa.qq.com/msgrd?V=1&uin=787720462&Site=涵若铭妆&Menu=yes' target=blank><IMG src='http://wpa.qq.com/pa?p=1:787720462:4' border=0>业务合作</A>";
	//ServiceCode+="&nbsp;<A title='在线处理投诉问题与建议,若不在请留言,QQ号码879391457' href='http://wpa.qq.com/msgrd?V=1&uin=879391457&Site=涵若铭妆&Menu=yes' target=blank><IMG src='http://wpa.qq.com/pa?p=1:879391457:4' border=0>投诉建议</A>";
 	
	obj=obj.getElementsByTagName("DIV");
	if(obj && obj.length)obj[0].innerHTML=ServiceCode;
}	

LoadQQService();

function QQ_FloatTopDiv()
{ var jqq_ftlObj=document.getElementById("DivQQbox");
  var jqq_startY = 150;//(document.body.clientHeight-jqq_ftlObj.offsetHeight)/2;
  jqq_ftlObj.y = jqq_startY;
  
  window.stayTopLeft=function()
  { var movestep=Math.round((document.body.scrollTop + jqq_startY - jqq_ftlObj.y)/8);
  	if(movestep!=0)
  	{	jqq_ftlObj.y += movestep;
      jqq_ftlObj.style.top=jqq_ftlObj.y;
      setTimeout("stayTopLeft()", 10);
    }
    else
    { setTimeout("stayTopLeft()", 1000);
    }	
  }
  stayTopLeft();
}

function OnlineOver()
{ var obj=document.getElementById("divMenu");
  if(obj)obj.style.display = "none";
	var obj=document.getElementById("divQQtable");
  if(obj)obj.style.display = "block";
}

function hideMsgBox(theEvent)  //theEvent用来传入事件，Firefox的方式
{ if (theEvent)
  { var browser=navigator.userAgent;   //取得浏览器属性
    if (browser.indexOf("Firefox")>0) //如果是Firefox
    { if (document.getElementById("divQQtable").contains(theEvent.relatedTarget))  //如果是子元素
      { return;   //结束函式
      } 
    } 
    if (browser.indexOf("MSIE")>0) //如果是IE
    { if (document.getElementById("divQQtable").contains(event.toElement))  //如果是子元素
      { return;  //结束函式
      }
    }
  }
 	document.getElementById("divMenu").style.display = "block";
	document.getElementById("divQQtable").style.display = "none";
}

function LoadFloatingQQs()
{ var i,qqNumber,QQNick,QQcode="";
	var OurQQLength=OurQQs.length;
	var randomoffet=Math.floor((OurQQLength>>1)*Math.random())<<1;
	QQcode+='<div id="DivQQbox" style="right:3px;position:absolute">';
  QQcode+='<table cellSpacing="0" cellPadding="0" width="110" border="0" id="divQQtable" onmouseout="hideMsgBox(event);" style="display:none;">';
  QQcode+='    <tr>';
  QQcode+='      <td title="工作时间：9:00～18:00 （周一至周六）" align="right" valign="top" width="110" height="76" background="/images/qqface/qq_top.gif">';
  QQcode+='         <table border=0 width=16 height=16 style="margin:5px;cursor:hand" onClick="document.getElementById(\'DivQQbox\').style.display=\'none\';"><tr><td></td></tr></table>';
  QQcode+='      </td>';
  QQcode+='    </tr>';
  QQcode+='    <tr>';
  QQcode+='      <td valign="middle" align="center" background="/images/qqface/qq_middle.gif">';
  QQcode+='<table border="0" width="90" cellSpacing="0" cellPadding="0">';
  QQcode+='  <tr>';
  QQcode+='    <td width="90" height="5" border="0" colspan="2">';
 
  for(i=0;i<OurQQLength;i+=2)
  { if(randomoffet>=OurQQLength)randomoffet=0;
  	QQNick="销售咨询";
  	randomoffet++;
		qqNumber=OurQQs[randomoffet++];
	  QQcode+='</td></tr><tr><td height=25 style="FONT-SIZE:12px;FONT-FAMILY:verdana">';
	  QQcode+="&nbsp;<img src='http://wpa.qq.com/pa?p=1:"+qqNumber+":17' height=17 border=0 align=middle>&nbsp;&nbsp;<A title='在线即时交谈(QQ:"+qqNumber+")' href='http://wpa.qq.com/msgrd?V=1&uin="+qqNumber+"&Site=涵若铭妆&Menu=yes' target='_blank'>"+QQNick+"</A>"; 
	}
	QQNick="业务合作"
	qqNumber="787720462";
  QQcode+='</td></tr><tr><td height=25 style="FONT-SIZE:12px;FONT-FAMILY:verdana">';
	QQcode+="&nbsp;<img src='http://wpa.qq.com/pa?p=1:"+qqNumber+":17' height=17 border=0 align=middle>&nbsp;&nbsp;<A title='在线即时交谈(QQ:"+qqNumber+")' href='http://wpa.qq.com/msgrd?V=1&uin="+qqNumber+"&Site=涵若铭妆&Menu=yes' target='_blank'>"+QQNick+"</A>"; 
	QQNick="投诉建议"
	qqNumber="879391457";
  QQcode+='</td></tr><tr><td height=25 style="FONT-SIZE:12px;FONT-FAMILY:verdana">';
	QQcode+="&nbsp;<img src='http://wpa.qq.com/pa?p=1:"+qqNumber+":17' height=17 border=0 align=middle>&nbsp;&nbsp;<A title='在线即时交谈(QQ:"+qqNumber+")' href='http://wpa.qq.com/msgrd?V=1&uin="+qqNumber+"&Site=涵若铭妆&Menu=yes' target='_blank'>"+QQNick+"</A>"; 

  QQcode+='</td></tr></table>';
  QQcode+='</td>';
  QQcode+='    </tr>';
  QQcode+='    <tr>';
  QQcode+='      <td width=110 height=12 background="/images/qqface/qq_bottom.gif"></td>';
  QQcode+='    </tr>';
  QQcode+='</table>';
  QQcode+='<div id="divMenu" onmouseover="OnlineOver();"><img src="/images/qqface/serviceqq2.gif" class="press" alt="在线服务"></div>';
  QQcode+='</div>';
  return QQcode;
}


function LoadFloating2()
{ var i,qqNumber,QQNick,QQcode="";
	var randomoffet=Math.floor((OurQQs.length>>1)*Math.random())<<1;
	if (!document.layers)QQcode+='<div id="DivQQbox" style="width:200px;height:236px;position:absolute;right:3px;background-image:url(/images/qqface/resting.gif)">';
  QQcode+='<table border=0 width=20 height=16 align="right" style="margin-top:10px;cursor:hand" onClick="document.getElementById(\'DivQQbox\').style.display=\'none\';"><tr><td></td></tr></table>';
  if (!document.layers)QQcode+='</div>';
  return QQcode;
}

function QQFloating()
{ document.write(LoadFloatingQQs()); 
  QQ_FloatTopDiv();
}
