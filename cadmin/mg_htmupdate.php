<?php require('includes/dbconn.php');
CheckLogin();
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.input_text{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #000000;
	text-decoration: none;
	font-size: 12px;
	width:100%;
	text-align:center;
	border: 0px solid #CCCCCC;
	background-color:transparent
}
-->
</style>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td background="images/topbg.gif" height=22><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>前台页面更新管理</font></b></td>
</tr>
<tr> 
  <td valign="top" bgcolor="#FFFFFF">
<p align="center"><a href="mg_htmgen.php?mode=base" style="color:#FF0000">更新--前台基础页面</a></p> 
<p align="center"><a href="mg_htmgen.php?mode=main">更新--前台单独首页</a></p> 
<p align="center"><a href="mg_htmcategory.php">更新--商品分类列表</a></p>  	 
<p align="center"><a href="mg_htmnews.php">更新--更新新闻文档</a></p>   
<p align="center"><a href="mg_htmhelp.php">更新--更新帮助文档</a></p>   
<p align="center"><a href="mg_htmproduct.php">更新--商品详情介绍</a></p>   	
<!--p align="center"><a href="htm_proshow.asp">更新--SEO商品介绍</a></p-->   	
  </td>
</tr>
</table>
</body>
</html>
