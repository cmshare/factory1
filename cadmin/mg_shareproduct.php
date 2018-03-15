建设中...<?php exit(0);?><!--#include file="conn.asp"-->
<%CheckAdmin(popedomShare)

dim selectm,selectkey,selectid,CatList,newValue

selectkey=FilterText(trim(request("selectkey")))
selectm=FilterText(trim(request("selectm")))

sub ReturnWarnning(info)
	conn.close
	set conn=nothing
	response.write "<script>alert('"&info&"');window.location.href='"&Request.ServerVariables("HTTP_REFERER")&"';</Script>"
  response.end
end sub

mode=request("mode")
if mode<>"" then
	selectid=request.form("selectid")
	if selectid="" or (not OwnPopedom(popedomProduct)) then
    conn.close
    set conn = nothing
    response.Write "<script language='javascript'>alert('参数无效！');history.go(-1);</script>"
    response.End
  end if
  
  if mode="withdraw" then
  	conn.execute("update [product] set recommend=0,addtime=now() where id in ("&selectid&")")
  	ReturnWarnning "商品下架成功！"
  elseif mode="forward" then
  	conn.execute("update [product] set recommend=1,addtime=now() where id in ("&selectid&") and recommend<1")
  	ReturnWarnning "商品上架成功！"
  elseif mode="onsale" then
  	onsale=request("onsale")
    if isNumeric(onsale) then onsale=CInt(onsale) else onsale=0
  	conn.execute("update [product] set onsale = "&onsale&" where id in ("&selectid&")")
  	ReturnWarnning "商品特价指数修改成功！"
  elseif mode="recommend" then
  	newValue=request.form("newvalue")
  	if newValue<>"" and isNumeric(newValue) then
     	conn.execute("update [product] set recommend ="&newValue&" where id="&selectid )
      ReturnWarnning "商品推荐指数修改成功！"
    else
    	ReturnWarnning "参数无效"
    end if
  elseif mode="score" then
  	newValue=request.form("newvalue")
  	if newValue<>"" and isNumeric(newValue) then
     	conn.execute("update [product] set score ="&newValue&" where id="&selectid )
     	ReturnWarnning "商品积分修改成功！"
  	else
    	ReturnWarnning "参数无效"
    end if
  elseif mode="batchcategory" then 
    response.clear
    newcategory=request.form("category")
    if isNumeric(newcategory) then newcategory=CLng(newcategory) else newcategory=0
    if newcategory>0 then
      conn.execute("update [product] set category="&newcategory&" where id in ("&selectid&")")
      response.write "修改成功！"
    else
      response.write "参数错误！"
    end if  
    conn.close
    set conn=nothing
    response.end 
  end if  
  
  
end if

set rs=server.CreateObject("adodb.recordset")

sort_name=request.cookies("sort_name")	  
if sort_name<>"addtime" and sort_name<>"id" then
	if  sort_name<>"stock0" and sort_name<>"score"  and  sort_name<>"recommend" then
		 if sort_name<>"name" and sort_name<>"price3" and sort_name<>"price4" and sort_name<>"cost"  then sort_name="addtime"
	end if
end if

sort_order=request.cookies("sort_order")
if sort_order<>"asc" then sort_order="desc"
sql_sort_code=" order by "&sort_name&" "&sort_order&" "

CID=Request.QueryString("CID")
if isNumeric(CID) then CID=CInt(CID) else CID=0

If CID<>0 Then
  sub sorts(selec)
	   Set Rs1=Conn.Execute("select id from [category] where Parent = "&selec&" order by sortorder")
     do while not rs1.eof
		   IntCat =  rs1("id")
	 	   'If InStr( CatList, IntCat ) <= 0 Then
      		CatList = CatList&", "&IntCat &""
   	   'End If
	     sorts rs1("id")
	     rs1.movenext
     loop
     rs1.close
     Set Rs1 = Nothing
  end sub

  CatList =CID
  sorts(CID)
  strCat = " and category in ("& CatList&") "
Else
  strCat = " "
End if


function FormatPrice(value)
  if value<1 and value>-1 and value<>0 then
  	FormatPrice=FormatNumber(value,2,true)
  else
  	FormatPrice=Round(value,2)
  end if
end function

%>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="admscript.js" type="text/javascript"></SCRIPT>
<script language="javascript" src="editproduct.js"></script>
<SCRIPT language="JavaScript" src="<%=WebRoot%>include/category.js" type="text/javascript"></SCRIPT>
<title>管理商品</title>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr>
    <td height="20" align="right" background="images/topbg.gif" bgcolor="#F2F2F2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="55%"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.asp">管理首页</a> -&gt; <a href="?"><font color=#FF0000>产品管理</font></a></b></td>
        <td width="45%"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="right">分类过滤
           	<script language="javascript">
            	CreateCategorySelection("category",<%=CID%>,"--请选择商品分类--","self.location.href='share_editproduct.asp?cid='+this.value;");
            </script>
            </td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <form method="post" action="?">
      <td height="100" align="center" bgcolor="#FFFFFF">
       <% if selectkey<>"" then 
            select case selectm
       			case "name"
       			      sql="select id,name,price3,price4,cost,recommend,onsale,stock0,score,addtime from product where"
       			      if inStr(selectkey," ")>0 then
                   	 key_list=Split(selectkey, " ")
                      for i=0 to ubound(key_list,1)
                        subkey=trim(key_list(i))
                        if subkey<>"" then 
                        	 if i>0 then  sql=sql&" and"
                       	 sql=sql&" name like '%"&subkey&"%' "
                        end if	 
                      next
                   else
                 	  sql=sql&" name like '%"&selectkey&"%' "
                   end if
       			      sql=sql&" "&sql_sort_code
       			      rs.open sql,conn,1,1
       			case "barcode"
       			      rs.open "select id,name,price3,price4,cost,recommend,onsale,stock0,score,addtime from product where barcode= '"&selectkey&"' "&sql_sort_code,conn,1,1
       			case "productid"
       			      if selectkey=""  or not isnumeric(selectkey) then  selectkey=0
       			      rs.open "select id,name,price3,price4,cost,recommend,onsale,stock0,score,addtime from product where id="&selectkey&" "&sql_sort_code,conn,1,1
       		  case "supplier"
       			      rs.open "select id,name,price3,price4,cost,recommend,onsale,stock0,score,addtime from product where supplier= '"&selectkey&"' "&sql_sort_code,conn,1,1
       		  case else
       			      response.write "<center>请返回选择您要查询的方式！<br><br><a href=javascript:history.go(-1)>点击返回上一页</a></center>"
       			      response.End
       		  end select
          else
          	
          	'rs.open "select sum(stock0*cost) from product where recommend>0 and stock0<5000" & strCat ,conn,1,1
            'response.write rs(0)
          	'rs.close
          		rs.open "select id,name,price3,price4,cost,recommend,onsale,stock0,score,addtime from product where recommend>0 " & strCat & sql_sort_code,conn,1,1
          end if

  				if rs.eof then
       				Response.Write "<p align=""center"">找不到相关记录！<br><br><a href=""javascript:history.go(-1)"" style=""color:#FF0000;text-decoration:underline"">点击返回上一页</a></p>"
   				else
	  				  dim i,CurrentPage,TotalPages,TotalRecords
              Const MaxPerPage=15  '###每页显示条数
              currentPage=request("page")
  	          if isNumeric(currentPage) then currentPage=CInt(currentPage) else currentPage=0
  	          if currentPage<1 then currentPage=1
              TotalRecords=rs.recordcount
              rs.pagesize=MaxPerPage '得到每页数
              TotalPages=rs.pagecount     '得到总页数
              if CurrentPage>TotalPages then CurrentPage=TotalPages
  	          i=0
              rs.move (currentPage-1)*MaxPerPage%>
         <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr bgcolor="#f7f7f7" align="center" height="20">
          	<td width="4%"  background="images/topbg.gif" height="25" ><input type="checkbox" name="checkbox2" value="Check All" onclick="Checkbox_SelectAll('selectid',this.checked)" /></td>
            <td width="8%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('id')" style='cursor:hand'><strong>编号</strong><%if sort_name="id" then response.write "<img src='images/sort_"&sort_order&".gif'>"%></td>
            <td width="45%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('name')" style='cursor:hand'><strong>商品名称</strong><%if sort_name="name" then response.write "<img src='images/sort_"&sort_order&".gif'>"%></td>
            <td width="6%" background="images/topbg.gif" title="点击排序！" onclick="ProductResort('stock0')" style='cursor:hand'><strong>库存</strong><%if sort_name="stock0" then response.write "<img src='images/sort_"&sort_order&".gif'>"%></td>
            <td width="5%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('recommend')" style='cursor:hand'><strong>推荐</strong><%if sort_name="recommend" then response.write "<img src='images/sort_"&sort_order&".gif'>"%></td>
            <td width="5%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('score')" style='cursor:hand'><strong>积分</strong><%if sort_name="score" then response.write "<img src='images/sort_"&sort_order&".gif'>"%></td>
            <td width="6%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('price3')" style='cursor:hand'><strong>批发</strong><%if sort_name="price3" then response.write "<img src='images/sort_"&sort_order&".gif'>"%></td>
            <td width="6%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('price4')" style='cursor:hand'><strong>大客户</strong><%if sort_name="price4" then response.write "<img src='images/sort_"&sort_order&".gif'>"%></td>
            <td width="6%" background="images/topbg.gif" title="点击排序" onclick="ProductResort('addtime')" style='cursor:hand' nowrap><strong>上架时间</strong><%if sort_name="addtime" then response.write "<img src='images/sort_"&sort_order&".gif'>"%></td>
            <td width="5%" background="images/topbg.gif"><strong>编辑</strong></td>
          </tr>
          <%
		  do while not rs.eof
		  	  if rs("recommend")>0 then FontColor="#000000" else FontColor="#BFBFBF"%>
          <tr height="25"  align="center" bgcolor="#F7F7F7" onMouseOut=mOut(this,"#F7F7F7"); onMouseOver="mOvr(this,MENU_HOTTRACK_COLOR)">
            <td><input name="selectid" type="checkbox" id="selectid" value="<%=rs("id")%>" /></td>
            <td><a href="stocklog.asp?productid=<%=rs("id")%>"><font color="<%=FontColor%>"><%=right("0000"&rs("id"),5)%></font></a></td>
            <td align="left">
            	<a href="<%=WebRoot%>products/<%=rs("id")%>.htm" target="_blank"><font color="<%=FontColor%>"><%=rs("name")%></font></a>
            	<%if rs("onsale")>0 then response.write "<img src='images/onsale.gif' width=16 height=16 alt='特价指数为"&rs("onsale")&"'>"%>
            </td>
            <td><%=rs("stock0")%></td>
            <td><%if rs("recommend")>0 then response.write rs("recommend") else response.write"<font color=#BFBFBF>已下架</font>"%></td>
            <td><%=rs("score")%></td>
            <td><%=FormatPrice(rs("price3"))%></td>
            <td><%=FormatPrice(rs("price4"))%></td>
            <td nowrap><%=FormatDateTime(rs("addtime"),vbShortDate)%></td>
            <td><a href="share_editproducts.asp?id=<%=rs("id")%>"><img src="images/pic9.gif" width="18" height="15" align="absmiddle" border=0></a></td>
          </tr>
          <%i=i+1
			if i>=MaxPerPage then Exit Do
			rs.movenext
		  loop
		  %>
          <tr bgcolor="#FFFFFF">
          	<td colspan="11" width="100%" align="right">
                <input type="button"  onclick="AddNewProduct();" value="添加新产品..." />
            </td>
          </tr>
        </table>

     <script language="javascript">
        GeneratePageGuider("selectm=<%=selectm%>&selectkey=<%=selectkey%>&cid=<%=CID%>",<%=TotalRecords%>,<%=CurrentPage%>,<%=TotalPages%>);
     </script>

<%end if
rs.close%>
			</td>
    </form>
  </tr>
</table>
<br>
<table width="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr>
    <td background="images/topbg.gif"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.asp">管理首页</a> -&gt; <font color=#FF0000>架上商品搜索</font></b></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <form name="form2" method="post" action="share_editproduct.asp" onsubmit="return CheckSearch(this)">
      <td>
	    <br>
	    <table width="80%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
          <tr bgcolor="#F7F7F7" align="center">
            <td width="35%" height="25">按<select name="selectm">
                <OPTION VALUE="name">商品名称</OPTION>
                <OPTION VALUE="productid">商品编号</OPTION>
                <OPTION VALUE="barcode">商品条码</OPTION>
                <OPTION VALUE="supplier">供 货 商</OPTION>
            </select>
            </td>
            <td width="45%" height="25">查找 <input name="selectkey" type="text" class="input_sr" id="selectkey" style="color:#0000FF"></td>
            <td width="20%" height="25"><input type="submit" class="input_bot" value="查 询"></td>
          </tr>
      </table>
      <br></td>
    </form>
  </tr>
</table>
<form name="MyTestForm" id="MyTestForm" method="post"><input type="hidden" name="selectid"><input type="hidden" name="newValue"></form>
<script language=javascript>
function CheckSearch(myform)
{	 setCookie("selectm",myform.selectm.value);
	 return true;
}

function SearchModeAutoSelect()
{ var serchoptions=document.form2.selectm;
  var optionlength=serchoptions.options.length;
  var i,selectm=getCookie("selectm");
  if(selectm)
  {	for(i=0;i<optionlength;i++)
    { if(selectm==serchoptions.options[i].value)
  	  { serchoptions.options[i].selected=true;
        break;
      }
    }
  }
}    
function AddNewProduct()
{ self.location.href="b2b_addnewproduct.asp";
}

SearchModeAutoSelect();

 

 
</script>
</body>
</html>
<%
set rs=nothing
conn.close
set conn=nothing
%>
