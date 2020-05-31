<!--#include file="conn.asp"-->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="<%=WebRoot%>include/category.js"></SCRIPT>
<SCRIPT language="JavaScript" src="<%=WebRoot%>include/property.js"></SCRIPT>
<script language="javascript" src="mouse_on_title.js"></script>
<SCRIPT language="JavaScript" src="admscript.js" type="text/javascript"></SCRIPT>
<script language="javascript" src="checkproduct.js"></script>
<%CheckAdmin(popedomShare)

set rs=server.CreateObject("adodb.recordset")
ProductID=request("id")
if isNumeric(ProductID) then ProductID=CLng(ProductID) else ProductID=0
imageFileName=right("0000"&ProductID,5)	

function FormatPrice(value)
  if value<1 and value>-1 and value<>0 then
  	FormatPrice=FormatNumber(value,2,true)
  else
  	FormatPrice=Round(value,2)
  end if 	 
end function

function IsShareBrand(catid)
  dim PID,brands,Rs1
  IsShareBrand=false
  set Rs1=server.CreateObject("adodb.recordset")
  PID=catid
  do while PID <> 0
		 Rs1.open "Select parent,shared From category Where id="&PID,conn,1,1
  	 if not Rs1.eof then
  	   if Rs1("shared") then
  	   	 IsShareBrand=true
  	   	 PID=0
  	   else
  	     PID=Rs1("parent")		   
       end if
     else
       PID=0  
		 end if
		 Rs1.close 
	loop
	set Rs1=nothing
end function


rs.open "select * from product where id="&ProductID,conn,1,1
if not rs.eof then 
  if rs("recommend")=-1 then
	  PageTitle="添加商品"
	  recommend=1
    SupplierName=""
    SharedCategory=true
  else
	  PageTitle="编辑商品"
	  recommend=rs("recommend")	
	  SupplierName=rs("Supplier") 
	  SharedCategory=IsShareBrand(rs("category"))
  end if
else
	rs.close
  PageReturn "商品不存在",0
end if
%>
</head>
<body leftmargin="0" topmargin="0">

<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.asp">管理首页</a> -> <a href="share_editproduct.asp">产品管理</a> -> <font color=#FF0000><%=PageTitle%></font></b></td>
  </tr>
  <tr><form name="myform" method="post" <%if SharedCategory then%>action="b2b_saveaddproduct.asp?mode=product"<%end if%> onsubmit="return CheckSaveProductInfo(this)" target="dummyframe">
    <td bgcolor="#FFFFFF"><input type="hidden" name="id" value="<%=ProductID%>">
        
        <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>商品名称： </strong></td>
            <td bgcolor="#FFFFFF" nowrap><input name="productname" type="text" class="input_sr" id="productname" value="<%=rs("name")%>" size="58"> &nbsp;<img src=images/memo.gif alt='必填，不能为空，不能重复'> </strong> <font color=#FF0000>＊</font></td>
            <td rowspan=8 width="33%" align="center" valign="middle" bgcolor="#FFFFFF" ><a href="<%=WebRoot%>products/<%=ProductID%>.htm" target="_blank"><img id="preview_img" border=0 onload="DrawImage(this)"></a></td>
          </tr>
         
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>品牌分类：</strong></td>
            <td bgcolor="#FFFFFF">
            	<script language="javascript">
            	 CreateCategorySelection("categoryid",<%=rs("category")%>,"．．．","");
              </script>
            	<font color=#FF0000>＊</font></td>
          </tr> 
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>功能分类：</strong></td>
            <td bgcolor="#FFFFFF">
            	<script language="javascript">
            	CreateNavcatSelection("property",<%=rs("property")%>,"．．．","");
              </script>
            	<font color=#FF0000>＊</font></td>
          </tr>            
          <tr>
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4" nowrap><strong>供 货 商：</strong></td>
            <td bgcolor="#FFFFFF"><input name="Supplier" type="text" class="input_sr" id="Supplier" value="<%=SupplierName%>" size="30" >
                <select name="selectSupplier" id="selectSupplier" onChange="this.form.Supplier.value=this.options[this.selectedIndex].value;this.selectedIndex=0;">
                  <option selected>请选择供货商</option>
                  <%
				sql="select SupplierName from Supplier Order by SupplierCode"
				set michaelrs=conn.execute(sql)
				while not michaelrs.eof
				%>
                  <option value="<%=michaelrs("SupplierName")%>"><%=michaelrs("SupplierName")%></option>
                  <%
				michaelrs.movenext
				wend
				%>
              </select></td>
          </tr>

          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>商品规格：</strong></td>
            <td bgcolor="#FFFFFF"><input name="Specification" type="text" class="input_sr" id="Specification" value="<%=rs("Specification")%>" size="30">
              <select name="selectMaterial" onChange="this.form.Specification.value=this.options[this.selectedIndex].value;this.selectedIndex=0;">
			  <option selected>请选择规格</option>
			  <%
			  	mSql="select * from buy2buyMaterial order by IndexID"
				set mRs=conn.execute(mSql)
				if not (mRs.eof and mRs.bof) then
				while not mRs.eof
			  %>
			   <option value="<% = mRs("MaterialName") %>"><% = mRs("MaterialName")%></option>
			  <%
			  		mRs.movenext
			  	wend
				end if
				mRs.close
				set mRs=nothing			  
			  %>
            </select>              </td>
          </tr>
          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>商品单位：</strong></td>
            <td bgcolor="#FFFFFF"><input name="UnitType" class="input_sr" type="text" value="<%=rs("UnitType")%>" size="30">
              <select name="selectUnit" onChange="this.form.UnitType.value=this.options[this.selectedIndex].value;this.selectedIndex=0;">
			  <option selected>请选择单位</option>
			  <%
			  	mSql="select * from buy2buyUnits order by IndexID"
				set mRs=conn.execute(mSql)
				if not (mRs.eof and mRs.bof) then
				while not mRs.eof
			  %>
			   <option value="<% = mRs("UnitName") %>"><% = mRs("UnitName")%></option>
			  <%
			  		mRs.movenext
			  	wend
				end if
				mRs.close
				set mRs=nothing			  
			  %>
            </select>              </td>
          </tr>


          <tr> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>商品大图：            </strong></td>
            <td bgcolor="#FFFFFF"><input name="pic" type="text" class="input_sr" id="pic" value="<%=rs("pic")%>" size="30" onfocus="ShowImagePreview(this.value)" >
              <%if SharedCategory then%><input name="Submit11" type="button" value="浏览"  onClick="UploadPicture('<%=imageFileName%>','myform.pic');" alt="请单击“浏览”上传图片<br>或填写图片的网址...">  &nbsp; <img src=images/memo.gif alt='<font color=red>大图显示在什么位置？</font><br>在商品详细介绍页面可查看所有大图'>&nbsp;建议大小：550×550<%end if%>
            </td>
          </tr>
          <tr>
            <td width="17%" align="right" background="images/topbg.gif" bgcolor="#f4f4f4" nowrap><strong>商品条码：</strong></td>
            <td bgcolor="#FFFFFF"><input name="barcode" type="text" class="input_sr" id="barcode" value="<%=rs("barcode")%>" size="30" onkeyup="if(isNaN(value))execCommand('undo')"> &nbsp;<img src=images/memo.gif alt='请输入实际商品的条形码，如果此商品没有条形码则填0'> </strong> <font color=#FF0000>＊</font>
            &nbsp; &nbsp;  &nbsp;  &nbsp;  &nbsp;  <b>产品库存：</b><span style="color:#FF0000;cursor:Pointer;text-decoration:underline;padding-left:5px;padding-right:5px" title="查看库存分布明细..." onclick="ShowStock(this)"><%=rs("stock0")%></span>件
            </td>
          </tr>

       
          <tr bgcolor="#f7f7f7"> 
            <td align="right" background="images/topbg.gif" bgcolor="#f4f4f4"><strong>商品价格：</strong></td>
            <td height="50" bgcolor="#FFFFFF" colspan=2>
            	<table width="90%" border="1" cellspacing="0" cellpadding="0" bgcolor="#FFCC00" bordercolor="#D6E7FF">
              <tr align="center">
                <td width="16%">市场价</td>
                <td width="16%">VIP价</td>
                <td width="16%"><b>批发价</b></font></td>
                <td width="16%"><font color=#FF0000><b>大客户价</b></font></td>
                <%if session("showcost")=true and SharedCategory then%>
                <td width="16%">成本价</td>
                <%end if%>
                <td width="16%">积分</td>
              </tr>
              <tr align="center">
                <td nowrap><%=FormatPrice(rs("price1"))%></td>
                <td nowrap><%=FormatPrice(rs("price2"))%></td>
                <td nowrap><%=FormatPrice(rs("price3"))%></td>
                <td nowrap><%=FormatPrice(rs("price4"))%></td>
	              <%if session("showcost")=true and SharedCategory then%>             
                <td nowrap><font color="#FF0000"><%=FormatPrice(rs("cost"))%></font></td>
                <%end if%>
                <td nowrap><%=rs("score")%></td>
              </tr>
              
            </table>            </td>
          </tr>          
          
          <tr> 
            <td align="right" valign="top" bgcolor="#f4f4f4"><strong> 详细简介：<br></strong></td>
            <td bgcolor="#FFFFFF" colspan=2>
		  <INPUT type="hidden" name="ProductDescription" value="<%if rs("Description")<>"" then response.write  Server.HtmlEncode(rs("Description")) %>">	
          <iframe ID="ProductDescription" src="/webedit/ewebeditor.htm?id=ProductDescription<%if not SharedCategory then response.write "&style=simple"%>" frameborder="0" scrolling="no" width="90%" HEIGHT="800"></iframe></td>
          </tr>
          <tr>
          	<td height="25" align="right" bgcolor="#f4f4f4"><strong>上架时间：</strong></td>
          	<td height="25" bgcolor="#FFFFFF"><input type="text" name="addtime" value="<%=rs("addtime")%>" style="width:150px;border: 0px solid #CCCCCC;	color:#FF0000;background-color:transparent"></td>
          	<td height="25" align="center" bgcolor="#FFFFFF"><input type="hidden" name="onsale" value="<%=rs("onsale")%>"><input type="hidden" name="recommend" value="<%=recommend%>"><input name="ConfirmButton" type="submit" value="确认以上修改" <%if not SharedCategory then response.write "disabled"%>></td>
          </tr>
        </table>
        <br></td>
  </form>
  </tr>
</table>
<iframe name="dummyframe" style="height:5px;display:none" scrolling="no" Frameborder="no" marginwidth=0 marginheight=0></iframe>   
<SCRIPT LANGUAGE="JavaScript">
ShowImagePreview("<%=rs("spic")%>"); 
function ShowStock(obj)
{ window.showModalDialog( "checkstock.asp?pid=<%=ProductID%>&handle="+Math.random(),"","dialogWidth:600px;dialogHeight:"+(100+ModalDialogHeightExt())+"px;status:no;scroll:no");
}
</script>
</body>
</html>

<%rs.close
set rs=nothing
conn.close
set conn=nothing%>