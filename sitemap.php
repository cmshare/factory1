<?php require('include/conn.php');
OpenDB();?>
<HTML><HEAD>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<META http-equiv="Keywords" content="产品清单,化妆品,化妆品批发,进口化妆品批发,韩国化妆品批发,最低价格">
<META http-equiv="Description" content="本页面包揽了涵若铭妆所有化妆品批发清单，承诺最低价格的正品化妆品批发，涵若铭妆主营进口化妆品批发,韩国化妆品批发,最低价格最周到服务">
<link href="include/mycss.css" rel="stylesheet" type="text/css">
<title>产品清单－化妆品批发－涵若铭妆化妆品公司</title>
</HEAD>
<body oncontextmenu="return isNaIMG(event)">
<SCRIPT language="JavaScript" src="user/cmbase.js"></SCRIPT><SCRIPT language="JavaScript" src="include/page_frame.js"></SCRIPT>
<TABLE cellSpacing="0" cellPadding="0" width="1000" align="center"  border="0" bgcolor="#FFFFFF">
<tr>
  <td width="190" valign="TOP">
	 	
    <TABLE cellSpacing="0" cellPadding="0" width="100%" height="100%" align="center" border="0">
    <tr>
       <td height="1%">
 
<TABLE cellSpacing=0 cellPadding=0 width="100%" align="center"  border="0">
<TR>
   <TD><IMG height=27 src="images/guide_brand.gif" width=190></TD>
</TR>
</TABLE>
  
<TABLE  style="padding:8px;margin-bottom:5px;background:#FFFFFF;BORDER-COLLAPSE: collapse" borderColor="#cccccc" cellSpacing="0" cellPadding="0" width="100%" align="center" border="1">
<TR>
   <TD vAlign="top" align="center" width="100%" style="BACKGROUND-POSITION: 50% top; BACKGROUND-REPEAT: repeat-x;" background="images/search_bg.gif" >

      <TABLE align=center cellSpacing=0 cellPadding=0 width="100%" border="0" style="font-family: "宋体"; font-size: 9pt; color: #333333; letter-spacing: 1px; line-height: 160%"><?php  
$res=$conn->query('select * from `mg_brand` where parent=0 and recommend>0 order by sortorder',PDO::FETCH_ASSOC);
foreach($res as $row){?>
      <TR>
        <TD height=24 valign="middle"><IMG id="img<?php echo $row['id'];?>"  width="20" height="20" src="images/guidefold1.gif" align="absMiddle">
            <a href="category/cat<?php echo $row['id'];?>.htm" title="<?php echo $row['title'];?>化妆品批发"><font color="GREEN"><?php echo $row['title'];?></font></a>
        </td>
      </tr>
      <TR>
        <TD  bgColor="#FEFDF5"><?php

$res2=$conn->query('select * from `mg_brand` where parent='.$row['id'].' and recommend>0 order by sortorder',PDO::FETCH_ASSOC);
foreach($res2 as $row_subsort){?>
  <IMG height=20 src="images/bclass3.gif" width=36 align=absMiddle border=0><a href="category/cat<?php echo $row_subsort['id'];?>.htm"><font color=0066ff><?php echo $row_subsort['title'];?></font></a><br><?php
}?>
        </td>
      </tr><?php
      }?>
      </table>
   </TD>
</TR>
</TABLE>


</td></tr><tr><td height="1%" style="padding-top:5px"> 
<TABLE cellSpacing=0 cellPadding=0 width="100%" align="center"  border="0">
<TR>
   <TD><IMG height=27 src="images/guide_property.gif" width=190></TD>
</TR>
</TABLE>
<TABLE  style="padding:8px;margin-bottom:5px;background:#FFFFFF;BORDER-COLLAPSE: collapse" borderColor="#cccccc" cellSpacing="0" cellPadding="0" width="100%" align="center" border="1">
<TR>
   <TD vAlign="top" align="center" width="100%" style="BACKGROUND-POSITION: 50% top; BACKGROUND-REPEAT: repeat-x;" background="images/search_bg.gif" >
      <TABLE align=center cellSpacing=0 cellPadding=0 width="100%" border="0" style="font-family: "宋体"; font-size: 9pt; color: #333333; letter-spacing: 1px; line-height: 160%"><?php
$res=$conn->query('select * from `mg_category` where parent=0 order by sortorder',PDO::FETCH_ASSOC);
foreach($res as $row){
  $res2=$conn->query('select * from `mg_category` where parent='.$row['id'].' order by sortorder',PDO::FETCH_ASSOC);
  $row_subsort=$res2->fetch();
  if($row_subsort){?>
        <TR>
          <TD height=24 valign="middle"><IMG width="20" height="20" src="images/guidefold1.gif" align="absMiddle">
            <a href="category/sort<?php echo $row['id'];?>.htm" title="<?php echo $row['title'];?>类化妆品批发"><font color="GREEN"><?php echo $row['title'];?></font></a>
          </td>
        </tr>
        <TR>
          <TD bgColor="#FEFDF5"><?php

          foreach($res2 as $row_subsort){?>
               <IMG height=20 src="images/bclass3.gif" width=36 align=absMiddle border=0><a href="category/sort<?php echo $row_subsort['id'];?>.htm"><font color=0066ff><?php echo $row_subsort['title'];?></font></a><br><?php
          }?>
          </td>
        </tr><?php
  }
  else{?>
    	<TR>
          <TD language="javascript"  onMouseOver="bgColor='#FFE3D2';" onMouseOut="bgColor='';" height=24 valign="middle">
          <IMG width="20" height="20" src="images/guidefold.gif" align="absMiddle"><a href="category/sort<?php echo $row['id'];?>.htm" title="<?php echo $row['title'];?>类化妆品批发"><font color="GREEN"><?php echo $row['title'];?></font></a>
          </td>
        </tr><?php
  }
}?>
      </table>
   </TD>
</TR>
</TABLE>


</td></tr><tr><td height="1%" style="padding-top:5px"> 
<TABLE cellSpacing=0 cellPadding=0 width="100%" align="center"  border="0">
<TR>
   <TD><IMG height=27 src="images/default_bulletin.jpg" width=190></TD>
</TR>
</TABLE>
<TABLE  style="padding:8px;margin-bottom:5px;background:#FFFFFF;BORDER-COLLAPSE: collapse" borderColor="#cccccc" cellSpacing="0" cellPadding="0" width="100%" align="center" border="1">
<TR>
   <TD vAlign="top" align="center" width="100%" style="BACKGROUND-POSITION: 50% top; BACKGROUND-REPEAT: repeat-x;" background="images/search_bg.gif" >
     
       <TABLE align=center cellSpacing=5 cellPadding=0 width="96%" height="100%" border="0" style="BACKGROUND-IMAGE:url(images/bk1.gif);font-family: "宋体"; font-size: 9pt; color: #333333; letter-spacing: 1px; line-height: 160%"><?php
$res=$conn->query('select id,title from `mg_article` where property=1 or property=2 order by addtime desc',PDO::FETCH_NUM);
foreach($res as $row){?>
  	  <TR>
         <td><a href="news/news<?php echo $row[0];?>.htm"><?php echo $row[1];?></a></TD>  
      </TR><?php
}

$res=$conn->query('select id,title from `mg_help` where property>0 order by sortorder',PDO::FETCH_NUM);
foreach($res as $row){?>
       <TR>
         <td><a href="help/help<?php echo $row[0];?>.htm"><?php echo $row[1];?></a></TD>  
      </TR><?php
}?>
     <TR><td><a href="wares.htm">新货上架</a></td></tr>
     <TR><td><a href="wares.htm">新货上架</a></td></tr>
     <TR><td><a href="hotsell.htm">热销排行</a></td></tr>
     <TR><td><a href="bargain.htm">特价商品</a></td></tr>
     <TR><td><a href="present.htm">赠品兑换</a></td></tr>
     <TR><td><a href="news/">商城动态</a></td></tr>
     <TR><td><a href="help/">帮助中心</a></td></tr>
     <TR><td><a href="usrmgr.htm">会员中心</a></td></tr>
     <TR><td><a href="newarrival.php">最新到货</a></td></tr>
     <TR><td><a href="book/">在线咨询</a></td></tr>
     <tr>
      	<td height="98%"></td>
     </tr>
     </table>
   </TD>
</TR>
</TABLE>
 
</td></tr><tr><td height="1%" style="padding-top:5px">  
		
</td></tr><tr><td height="97%" style="padding-top:5px"><?php
   include('include/guide_blank.php');?> 
      </td></tr>
    </table>     
               
  </td>
  
  <td width="10"></td>
  
  <td valign="top" width="800" height="100%">
  	<TABLE cellSpacing=0 cellPadding=0 width="800" height="100% align="center"  border="0">
    <TR>
        <TD width="780" height="25" valign="bottom" style="BACKGROUND-IMAGE:url(images/ppbar3.gif); BACKGROUND-REPEAT: no-repeat;">
          &nbsp;&nbsp;<img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="#">涵若铭妆</a>-化妆品批发-产品清单
        </TD>
      </TR>
      <TR>
        <TD valign="top" style="padding-top:10px;padding-left:50px;BORDER-COLLAPSE: collapse; border-left:1px solid #cccccc;border-right:1px solid #cccccc;border-bottom:1px solid #cccccc"><?php
$res=$conn->query('select id,name from `mg_product` where recommend>=0 order by addtime desc',PDO::FETCH_NUM);
foreach($res as $row){
    $productname=$row[0];//substr("0000".$row[0],-5);
  echo '<li><a href="products/'.$productname.'.htm">'.$row[1].'</a></li>';
}?></TD>
    </TR>
    </TABLE>
  </td>
</tr>
<tr>
	<td height="5"></td>
</tr>	
</table>
  南京涵若铭妆成立以来一直从事国际知名品牌化妆品、品牌服饰、进口食品等贸易活动。作为一家贸易公司，本公司拥有严密的管理，保证产品质量及价格达到客户要求。本公司长期供货，保证品质，价格低廉，注重诚信；支持小量批发。为您提供最轻松、快捷的服务！ 
  我们主营化妆品贸易，主要提供进口化妆品批发业务，包括韩国化妆品批发，日本化妆口批发，欧美化妆品批发等国际品牌化妆品批发业务，我们主要提供以下化妆品品牌及产品的批发：韩国的TheFaceShop/韩国,SkinFood/韩国,Deoproce/韩国三星,Charmzone/韩国婵真, ETUDE /韩国爱丽,Laneige/韩国兰芝, Vov/韩国Vov,DoDo/韩国嘟嘟,Amore/韩国爱茉莉,Love/韩国永爱,CO.E/韩国韩伊,Shiseido/日本资生堂,Kose/日本高丝,Kanebo/日本嘉娜宝, SK-Ⅱ日本,SUKI/日本,Anna sui/安娜苏,Biotherm/碧欧泉,Borghese/贝佳斯,CD/迪奥,Clinique/倩碧,DOVE/多芬,Elizabeth Arden/雅顿,EsteeLauder/雅诗兰黛,Evian/依云,H2O/水之澳,Lancome/兰蔻,L’oreal/欧莱雅,Za/姿芮,Neutrogena/露得清,台湾牛耳/大Ｓ推荐,国际美容院品牌-----凉颜等品牌。
</body>
</html><?php
CloseDB();?>
