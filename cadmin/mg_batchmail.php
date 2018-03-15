建设中...<?php 
exit(0);?>
<!--#include file="conn.asp"-->
<%set rs=server.CreateObject("adodb.recordset") 
 
  MailSendTO=request("sendto")
 
  if MailSendTO<>"" then%>
  <!--#INCLUDE file="../include/b2b_mail.asp"--><%
    MailTitle=request("mailtitle")
    MailContent=request("mailcontent")
    rs.Open "select SMTP,MailServerUserName,MailServerPassword,SendFromMail,SendFromName,WebName from SystemData",conn,1,1
    SMTP=rs("SMTP")
    MailServerUserName=rs("MailServerUserName")
    MailServerPassword=rs("MailServerPassword")
    SendFromMail=rs("SendFromMail")
    SendFromName=rs("SendFromName")
    rs.close
    if SendJmail(SendFromMail,MailSendTO,SMTP,MailServerUserName,MailServerPassword,MailTitle,MailContent,SendFromName) then
   	  response.write "<font color=#FF0000>发送成功!</font>"
    end if
    conn.close
    response.end
  elseif request("mode")="save" then
    rs.Open "select MailTitle,MailContent from SystemData",conn,1,3
    rs("mailtitle")=trim(request.form("mailtitle"))
    rs("mailcontent")=request.form("mailcontent")
    rs.update
    rs.close
    PageReturn "邮件内容保存成功！",1
  end if 
  
  CheckAdmin(0)%>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="admincss.css" rel="stylesheet" type="text/css">
<script>

<%
 dim maillist

 maillist="""aufame@hotmail.com"""
 
 '用户邮件列表
 set rs=server.CreateObject("adodb.recordset") 
 rs.open "select usermail from `users` where grade>0 union all select usermail from [;database="&Data_Source2&"].`users`",conn,1,1
 while not rs.eof
   if rs(0)<>"" then
      maillist= maillist&","""&Server.HtmlEncode(rs(0))&""""
   end if
   rs.movenext
 wend
 rs.close
 rs.Open "select MailTitle,MailContent from SystemData",conn,1,1
 MailTitle=rs("MailTitle") 
 MailContent=rs("MailContent")
 if MailContent<>"" then MailContent=Server.HtmlEncode(MailContent)
 rs.close
%>
var MailArray=new Array(<%=maillist%>);
var MailCount=MailArray.length;


var mail_title,mail_content,OpInterval=6,SecondCounter,enable_running=0,ProductID=0,Generating=false;

String.prototype.trim = function() 
{ return this.replace(/(^\s*)|(\s*$)/g, ""); 
} 

function UpdateProductShow()
{if(enable_running)
	{ var statusLabel=document.getElementById("process_status");
 	 	if(SecondCounter>0)
 	  { if(Generating)
 	  	{	var statustext=statusLabel.innerHTML;
 	  		if(statustext.indexOf("成功")>=0)
 	  		{ Generating=false;
 	  			ProductID++; 
 	        document.forms[0]["ProductID"].value=ProductID; 
 	        document.getElementById("process_scale").innerHTML=Math.round(parseInt(ProductID)*100/MailCount)+"%";
 	  		}
 	  	}
 	  	if(!Generating)
 	  	{ statusLabel.innerHTML="<font color=#FF0000>"+SecondCounter+"秒</font>";
 	  		SecondCounter--;
 	  	}
 	  }
 	  else
 	  { SecondCounter=OpInterval;
 	  	Generating=true;
 	    SyncPost("sendto="+MailArray[ProductID-1]+"&mailtitle="+mail_title+"&mailcontent="+mail_content,"?","process_status");
  	}
 	  setTimeout("UpdateProductShow()",1000); 
 	}  
}
function ControlProcess(myform)
{ if(enable_running)
	{ enable_running=false;
		document.getElementById("process_status").innerHTML="stoped";
		myform.ControlButton.value="开始";
	
	}
	else
	{	ProductID=myform.ProductID.value.trim();
	  ProductID=( !ProductID || isNaN(ProductID) )?0:parseInt(ProductID);
	  if(ProductID<1) 
	  { alert("无效的ID");
	  }
	  else
	  { mail_title=myform.mailtitle.value.trim();
	  	if(!mail_title)
	  	{ alert("请填写邮件标题!");
	  		return false;
	  	}
	  		
	  	window.frames["eWebEditor1"].AttachSubmit();
	  	mail_content=myform.mailcontent.value;
	  	
	  	if(!mail_content)
	  	{ alert("请填写邮件正文!");
	  		return false;
	  	}else mail_content="<html><title>"+mail_title+"</title><body>"+mail_content+"<br><br><P align=right><a href='http://www.gdhzp.com'><font color=red>涵若铭妆 www.gdhzp.com</font></a></p></body></html>";
      
       mail_title=escape(mail_title);	  	
       mail_content=escape(mail_content);	  	
	  	
	  	
	  	SecondCounter=OpInterval;
		  setTimeout("UpdateProductShow()",1000);
		  enable_running=true;
		  Generating=false;
		  myform.ControlButton.value="停止";
	  }
	}
}
</script>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing=5 bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr bgcolor="#F7F7F7"> 
  <td height="20" colspan="3" width="100%" background="images/topbg.gif" bgcolor="#F7F7F7">
   <img src="images/pic5.gif" width="28" height="22" align="absmiddle" /><b>您现在所在的位置是： <a href="admincenter.asp">管理首页</a> -&gt; <font color=#FF0000>邮件群发</font></b>
   <script>document.write("&nbsp; (Total "+MailCount+" mails)");</script>
  </td>
</tr>
<tr>
	<td align="center" height="100%">

      <table border=0 width="100%">
      <tr>
      	<td  width="33%" align="right" id="process_status" style="padding-right:10px;">
      	</td><form method="post" action="?mode=save">
      	<td width="33%" align="left">
      		<input type="text" value="1" name="ProductID" id="ProductID" style="width:60px;text-align:center" maxlength=4 onkeyup="if(isNaN(value))execCommand('undo')">&nbsp;<input type="button" name="ControlButton" id="ControlButton" value="开始群发" onclick="ControlProcess(this.form)">
      	</td>
        <td width="33%" align="center" id="process_scale" style="color:#FF0000">&nbsp;</td>	
      </tr>
      </table>
      <table border=0 width="100%">
      <tr>
      	<td width="10%" align="right" nowrap><b>邮件标题:</b></td><td width="90%"><input type="text" name="mailtitle" value="<%=MailTitle%>" style="width:100%"></td>
      </tr>
      <tr>
      	<td  width="10%" align="right" valign="top" nowrap><b>邮件正文:</b></td>
        <td width="90%"><table width="100%" border="1"><tr><td width="100%"><INPUT type="hidden" name="mailcontent" value="<%=MailContent%>"><iframe ID="eWebEditor1" src="/webedit/ewebeditor.htm?id=mailcontent&style=simple" frameborder="0" scrolling="no" width="100%" HEIGHT="350"></iframe></td></tr></table>
      	</td>
      </tr>
      <tr>
      	<td width="10%" align="right" colspan="2"><input type="submit" value="保存"></td>
      </tr>
      </form>
      </table>

  </td>
</tr>
</table>
</body>
</html>
<%set rs=nothing
  conn.close
  set conn=nothing%>



