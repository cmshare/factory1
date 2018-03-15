<!--#include file="../include/conn.asp"-->
<!--#include file="../cadmin/config_usergrade.asp"--><%
 dim UserGrade,UserID,gradeTitles(4),index,permitgrade 
 
 set rs=server.CreateObject("adodb.recordset") 

 UserID=GetUserID()
 
 if request("mode")="save" then
 	 UserGrade=request.form("grade")
 	 if IsNumeric(UserGrade) then UserGrade=CLng(UserGrade) else UserGrade=0
   if UserGrade=UserPermitGrade(rs,UserID) then
 	    if ChangeUserGrade(rs,UserID,UserGrade) then ret_msg="success" else ret_msg="err"
 	 else 
 	 	  ret_msg="err"   
 	 end if
 	 set rs=nothing
 	 conn.close
 	 set conn=nothing
 	 response.clear
 	 response.write ret_msg	
 	 response.end
 end if  
 
  
 UserGrade=0 
 if UserID>0 then
   rs.open "select UserName,deposit,Grade from `users` where id="&UserID,conn,1,1
   if not rs.eof then
   	 UserName=rs("username")
     UserGrade=rs("grade")
     UserDeposit=rs("deposit") 
   end if
   rs.close	
 end if   
 
 if UserGrade=0 then
 	 response.write "<p align=center>参数错误</p>"
 	 conn.close
 	 response.end
 end if
 
 rs.open "select sum(TotalPrice) from `OrderIndex` where username='"&UserName&"' and state>=5",conn,1,1
 if not rs.eof and not isNull(rs(0)) then 
   successedsum=round(rs(0))
 else
   successedsum=0
 end if
 rs.close	 
 
 rs.Open "Select title  From `usergrade` order by grade asc",conn,1,1
 for index = 1 To 4
   if not rs.eof then 
			gradeTitles(index)=rs(0)
			rs.movenext
	 end if		          
 next 
 rs.Close
 
 permitgrade=UserPermitGrade(rs,UserID)%>
<table width="500" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#B7CEF4">
<tr>
   <td width="100%" align="center" height="26" colspan="2" bgcolor="#E9EFFC"> 
       <font color="#FF0000"><b>会员升级</b></font>
   </td>
</tr>	
<tr>
   <td width="50%" height="28" bgcolor="#F6F6F6">会员名称：&nbsp;<font color="#FF6600"><%=UserName%></font></td>
   <td width="50%" bgcolor="#F6F6F6">目前等级：&nbsp;<font color="#FF6600"><%=gradeTitles(UserGrade)%></font></td>
</tr>
<tr>
   <td width="50%" height="28" bgcolor="#F6F6F6">预存款额：&nbsp;<font color="#FF6600"><%=FormatNumber(UserDeposit,1,true)%>元</font></td>
   <td width="50%" bgcolor="#F6F6F6">累计消费：&nbsp;<font color="#FF6600"><%=FormatNumber(successedsum,1,true)%>元</font></td>
</tr>  
<tr> 
   <td height="40" colspan="2"  align="right" valign="middle" bgcolor="#EAF9D2"> 
     <table border=0 width="100%" cellpadding="5" cellspacing="1" bgcolor="#FFFFFF">
     <tr align="center">
     	 <td width="16%" bgcolor="#EAF9D2"><b>等级名称</b></td>
     	 <%for index = 2 To 4
     	     response.write " <td width=""28%"" bgcolor=""#EAF9D2""><b>"&gradeTitles(index)&"</b></td>"
         next
       %>   
     </tr>
     <tr>
     	 <td align="center" bgcolor="#EAF9D2"><b>升级条件</b></td>
     	 <%for index = 2 To 4
     	     response.write "<td bgcolor=""#EAF9D2"">预存款余额<font color=#FF0000>"&UpgradeDepositDemand(index)&"</font>元以上或累计消费<font color=#FF0000>"&UpgradeConsumeDemand(index)&"</font>元以上.</td>"
         next
       %>            	 
     </tr>
     <tr align="center">
     	 <td align="center" colspan="4" bgcolor="#EAF9D2"><input type="button" onclick="var ret=SyncPost('grade=<%=permitgrade%>','<%=WebRoot%>user/userupgrade.asp?mode=save');if(ret=='success'){alert('升级成功');self.location.reload();}else alert('有错误发生！');" <%if permitgrade<=UserGrade then response.write "disabled"%> value="自动升级"></td>
     </tr>
     </table>	 
   </td>
</tr>
<tr>
   <td width="100%" height="26" colspan="2" bgcolor="#E9EFFC"> 
       <font color="#FF0000">注：累计消费低于<%=UpgradeConsumeDemand(4)%>元的【<%=gradeTitles(4)%>】采用浮动制，当预存款余额低于<%=UpgradeDepositDemand(4)%>元时,将自动降级为【<%=gradeTitles(3)%>】。</font>
   </td>
</tr>
</table><% 
set rs=nothing
conn.close
set conn=nothing%>
