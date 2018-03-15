<?php require('include/conn.php');
OpenDB();
$PageKeywords='化妆品,化妆品批发,韩国化妆品批发,进口化妆品批发,南京化妆品批发,上海化妆品批发,化妆品批发网,化妆品批发市场';
$PageDescription='涵若铭妆化妆品公司主要提供韩国化妆品批发,进口化妆品批发,品牌化妆品批发及零售业务,通过南京化妆品批发网及上海化妆品批发市场组建完善的网络销售平台。';
$PageTitle='【最新到货/缺货清单】-韩国化妆品批发-进口化妆品批发-涵若铭妆化妆品公司';
include('include/page_head.php');?>

<TABLE cellSpacing="0" cellPadding="0" width="1000" align="center"  border="0" style="background:url(/images/bg_mid.gif) repeat-x;">
<tr>
   <td width="200" valign="TOP">
	 	
   <TABLE cellSpacing="0" cellPadding="0" width="185" height="100%" border="0" style="background:url(/images/bg_left.gif) repeat-y;margin-top:30px;">
   <tr>
      <td height="1%"><?php
      include('include/guide_brand.htm');
      include('include/guide_category.htm'); 
      echo '</td></tr><tr><td height="99%">';
      include('include/guide_blank.php');?>  
      </td></tr>
    </table>    
    
  </td>
 
  <td valign="top" width="800" height="100%">
  
 <!-----最新到货内容开始--------> 
  
  <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%" align="center"  border="0">
 <TR>
   <TD width="800" height="30" valign="bottom">
   	   &nbsp;&nbsp;<img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; 最新到货/缺货清单
    </TD>
  </TR>
 <TR>
    <TD valign="top">
      
      <br><p align=center><b>即时更新</b>---最近七天到货/缺货日志(<a href="#newarrival">表一</a>、<a href="#newlack">表二</a>)
      <br> <b>声明</b>：本站商品的库存数据已经比较准确，每一产品的介绍都有标注是否有现货。</p>
      <TABLE cellSpacing=0 cellPadding=0 width="96%" align="center"  border="1" bordercolor="#dfdfdf" class="cmBorder">
      <tr><td colspan=4 bgcolor="#dfdfdf">&nbsp;<a name="newarrival"><b><font color=#FF6600>表一 &nbsp;最新到货</font></b></a></td></tr>
      <tr align="center"><td><b>产品编号</b></td><td><b>产品名称</b></td><td><b>批发价格</b></td><td><b>到货日期</b></td></tr><?php

$LimitDays=6; 
#当前库房到货情况MAIN_DEPOT
#$sql='select `mg_product`.id,`mg_product`.name,`mg_product`.price3,`mg_brand`.sortindex,mg_orders.actiontime,unix_timestamp()-mg_orders.actiontime as sorttime from (((`mg_ordergoods` inner join `mg_orders` on `mg_ordergoods`.ordername=`mg_orders`.ordername) inner join `mg_product` on `mg_ordergoods`.productid=`mg_product`.id)left join `mg_brand` on `mg_product`.brand=`mg_brand`.id) where (mg_orders.state<-3 and mg_orders.importer='.MAIN_DEPOT.') and `mg_orders`.actiontime>unix_timestamp()-'.$LimitDays.'*24*60*60 union all select `mg_product`.id,`mg_product`.name,`mg_product`.price3,`mg_brand`.sortindex,`mg_stocklog`.actiontime,unix_timestamp()-`mg_stocklog`.actiontime as sorttime from ((`mg_stocklog` inner join `mg_product` on `mg_stocklog`.productid=`mg_product`.id) left join `mg_brand` on `mg_product`.brand=`mg_brand`.id) where `mg_stocklog`.actiontime >unix_timestamp()-'.$LimitDays.'*24*60*60 and `mg_stocklog`.amount>0 and `mg_stocklog`.depot='.MAIN_DEPOT.' order by sorttime,sortindex,name';

#所有库房到货情况
$sql='select `mg_product`.id,`mg_product`.name,`mg_product`.price3,`mg_brand`.sortindex,mg_orders.actiontime,unix_timestamp()-mg_orders.actiontime as sorttime from (((`mg_ordergoods` inner join `mg_orders` on `mg_ordergoods`.ordername=`mg_orders`.ordername) inner join `mg_product` on `mg_ordergoods`.productid=`mg_product`.id)left join `mg_brand` on `mg_product`.brand=`mg_brand`.id) where (mg_orders.state<-3 and mg_orders.exporter<=0 and mg_orders.importer>0) and `mg_orders`.actiontime>unix_timestamp()-'.$LimitDays.'*24*60*60 union all select `mg_product`.id,`mg_product`.name,`mg_product`.price3,`mg_brand`.sortindex,`mg_stocklog`.actiontime,unix_timestamp()-`mg_stocklog`.actiontime as sorttime from ((`mg_stocklog` inner join `mg_product` on `mg_stocklog`.productid=`mg_product`.id) left join `mg_brand` on `mg_product`.brand=`mg_brand`.id) where `mg_stocklog`.actiontime >unix_timestamp()-'.$LimitDays.'*24*60*60 and `mg_stocklog`.amount>0 order by sorttime,sortindex,name';
$res=$conn->query($sql,PDO::FETCH_ASSOC);
foreach($res as $row){?>
   <tr align="center">
     <td><?php echo substr('0000'.$row['id'],-5);?></td>
     <td align="left"><a href="/products/<?php echo $row['id'];?>.htm"><?php echo $row['name'];?></a></td>
     <td>￥<?php echo round($row['price3'],2);?>元</td>
     <td><?php echo date('Y-m-d H:i:s',$row['actiontime']);?></td>
    </tr><?php
}?>
    </TABLE><br>
    <TABLE cellSpacing=0 cellPadding=0 width="96%" align="center"  border="1" bordercolor="#EEAA66" class="cmBorder">
    <tr><td colspan=4 bgcolor="#dfdfdf">&nbsp;<a name="newlack"><b><font color=#FF6600>表二 &nbsp;最新缺货</font></b></a></td></tr>
      <tr align="center"><td><b>产品编号</b></td><td><b>产品名称</b></td><td><b>批发价格</b></td><td><b>缺货日期</b></td></tr><?php
#当前库房缺货情况
#$sql='select c.id,c.name,c.price3,max(b.actiontime) as actiontime from `mg_ordergoods` as a,`mg_orders` as b,`mg_product` as c  where a.ordername=b.ordername and b.state>3 and b.exporter='.MAIN_DEPOT.' and a.amount>0 and a.productid=c.id and c.stock'.MAIN_DEPOT.'<=0 and b.actiontime >= unix_timestamp()-'.$LimitDays.'*24*60*60 group by c.id order by max(b.actiontime) desc';

#所有库房缺货情况
$sql='select c.id,c.name,c.price3,max(b.actiontime) as actiontime from `mg_ordergoods` as a,`mg_orders` as b,`mg_product` as c  where a.ordername=b.ordername and b.state>3 and a.amount>0 and a.productid=c.id and c.stock0<=0 and b.actiontime >= unix_timestamp()-'.$LimitDays.'*24*60*60 group by c.id order by max(b.actiontime) desc';

$res=$conn->query($sql,PDO::FETCH_ASSOC);
foreach($res as $row){?>
   <tr align="center">
     <td><?php echo substr('0000'.$row['id'],-5);?></td>
     <td align="left"><a href="/products/<?php echo $row['id'];?>.htm"><?php echo $row['name'];?></a></td>
     <td>￥<?php echo round($row['price3'],2);?>元</td>
     <td><?php echo date('Y-m-d H:i:s',$row['actiontime']);?></td>
    </tr><?php
}?>
  </TABLE><br></TD>
</TR>
</TABLE>
<!-----最新到货内容结束-------->   
  </td>
</tr>
<tr>
   <td height="5"></td>
</tr>	
</table><?php
include('include/page_bottom.htm');
CloseDB();
?>
</body>
</html>
