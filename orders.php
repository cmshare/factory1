<?php require('include/conn.php');

function showorders(){
  $page_size=50;###每页显示条数

  echo '<table border="0" cellpadding="0" cellspacing="0" align="center" width="96%"><tr><td height="40"colspan="2" background="/images/kubars/kubar_myorders.gif""></td></tr><tr><td align="center">'; 
  $sql_count='from `mg_orders` where state>0';
  $sql_query='select ordername,totalprice,actiontime,deliverymethod,paymethod,state '.$sql_count.' order by actiontime desc';
  $total_records=$GLOBALS['conn']->query('select count(*) '.$sql_count)->fetchColumn(0);
  if(empty($total_records))return false;
  $total_pages=(int)(($total_records+$page_size-1)/$page_size);
  $page=$_GET['page'];
  if(is_numeric($page)){
    if($page<1)$page=1;
    else if($page>$total_pages)$page=$total_pages;
  }else $page=1;
  $res=$GLOBALS['conn']->query($sql_query." limit ".($page_size*($page-1)).",$page_size",PDO::FETCH_ASSOC); 
  $row=$res->fetch();
  if(empty($row)) echo '<br><br><p align=center>没有订单！</p><br><br>';
  else{
    $i=0;
    echo '<div id="ordersview">  
          <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#f2f2f2" >
            <tr align="center" bgcolor="#F7F7F7"> 
              <td height="25"><strong>订单号</strong></td>
              <td height="25"><strong>合计金额</strong></td>
              <td height="25"><strong>付款方式</strong></td>
              <td height="25"><strong>配送方式</strong></td>
              <td height="25"><strong>下单/发货时间</strong></td>
              <td height="25"><strong>订单状态</strong></td>
            </tr>';
    do{
      echo '<tr bgcolor=#FFFFFF align="center"  style="cursor:hand" onmouseover="this.style.backgroundColor=\'#f2f2f2\'; this.style.color=\'#ff0000\' " onmouseout="this.style.backgroundColor=\'\';this.style.color=\'\'">'; 
      echo '<td><a href="/orders.php?id='.$row['ordername'].'">'.$row['ordername'].'</font></td>';
      echo '<td><font color=#FF6600>'.$row['totalprice'].'元</font></td>';
      echo '<td>'.$row['paymethod'].'</td>';
      echo '<td>'.$row['deliverymethod'].'</td>';
      echo '<td>'.date('Y-m-d H:i:s',$row['actiontime']).'</td>';
      echo '<td>';
      switch($row['state']){
        case '1': echo '<font color=#000000>己提交§等待审核</font>';break;
        case '2': echo '<font color=#FF0000>己审核§正在配货</font>';break;
        case '3': echo '<font color=#8800FF>己配货§正在发货</font>';break;
        case '4': echo '<font color=#0000aa>已发货§正在结算</font>';break;
        case '5': echo '<font color=#00aaaa>已发货§等待确认</font>';break;
        case '6': echo '<font color=#00aa00>已签收§交易完成</font>';break;
        case '8': echo '<font color=#00aa00>已签收§交易完成</font>';break;
      }
      echo '</td></tr>';
      $row=$res->fetch();
   }while($row); 
   echo '<tr><td align="center" height="20" colspan=6><form style="margin:0px">';
   echo '共<b><font color=#FF0000>'.$total_records.'</font></b>个订单&nbsp;&nbsp;';
   if($page==1) echo '首页&nbsp;&nbsp;上一页';
   else echo '<a href="?page=1">首页</a>&nbsp;&nbsp;<a href="?page='.($page-1).'">上一页</a>';
   echo '&nbsp;&nbsp;';
   if($page==$total_pages) echo '下一页&nbsp;&nbsp;尾页';
   echo '<a href="?page='.($page+1).'">下一页</a>&nbsp;&nbsp;<a href="?page='.$total_pages.'">尾页</a>';
   echo '&nbsp;&nbsp;页次：<strong><font color="red">'.$page.'</font>/'.$total_pages.'</strong>页&nbsp;&nbsp;';
   echo '</td></form></tr>';
  }
  echo '</table>';
  echo '<table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC"><tr>';

  $page_size=16;
  $res=$GLOBALS['conn']->query('select id,name from `mg_product` order by id asc limit '.($page_size*($page-1)).','.$page_size,PDO::FETCH_ASSOC); 
  foreach($res as $row)echo '<td bgcolor="#FFFFFF"><a href="/products/'.$row['id'].'.htm">'.$row['name'].'</a></td>';
  echo '</tr></table>
  </div>  
 <table width="95%"  border="0" cellspacing="0" cellpadding="0">
 <tr>
 <td>
 <hr><b>异地发货声明：</b><br>
 (1) 江浙沪地区一般2天内到货，其它地区3天左右到货，偏远的地区或乡镇可能会有延迟。<br>
 (2) 我们不负责快件的查询和催送问题。请您自己根据货单号码，通过该<a href="/user/deliverytrack.php" style="color:#FF0000" target="_blank">快递公司的官方网站</a>查询快件的行踪，或者根据快递公司网站上的客服电话进行查询。若派送延误，请尽量自行与快递公司协调解决，谢谢配合。<br>
 (3) 请收件人在快递员或物流处领取包裹时，一定注意在当场验收包裹内的物品数量、配件是否齐全；商品外表面是否有明显的因摔、撞、挤、压引起的损伤。请在确认无误后再签字签收，否则若在签收之后再提出异议，本公司概不负责。如在验收当场发现商品存在以上问题，请直接电话联系本公司或快递公司开出此类证明。若随意签收给您带来损失，本公司一律不负责！	
              </td>
            </tr>
          </table>';
 
echo '</td>
</tr> </table>';
}
  
db_open();
$ordername=FilterText(trim($_GET['id']));
if($ordername){
$row=$conn->query('select * from `mg_orders` where ordername=\''.$ordername.'\'',PDO::FETCH_ASSOC)->fetch();
if($row){
  $username=$row['username'];
  $OriginOrderTotalPrice=$row['totalprice'];
  $OriginOrderTotalScore=$row['totalscore'];
  $DeliveryFee=$row['deliveryfee'];
  $Order_ID=$row['id'];
  $Order_State=$row['state'];
  $Order_Receipt=$row['receipt'];
  $Order_DeliveryMethod=$row['deliverymethod'];
  $Order_PayMethod=$row['paymethod'];
  $Order_Adjust=$row['adjust'];
  $Order_ActionTime=$row['actiontime'];
  $Order_DeliveryCode=$row['deliverycode'];
}
else{
  echo '<p align=center>订单不存在！</p>';
  db_close();
  exit(0);
}
$sql='select id,productid,price,amount,remarks,productname,score from `mg_ordergoods` where ordername=\''.$ordername.'\' order by productname';
if($Order_State<8){
  $res=$conn->query($sql,PDO::FETCH_ASSOC);
}
else{
  // OpenDB2()
  //$row=$conn2->query($sql,PDO::FETCH_ASSOC)->fetch();
}
$KeywordsArray=array('品牌','进口','正品','热销','热卖','美白','去斑','祛斑','抗皱','消痘','香水','香薰','精油','面膜','护肤品','彩妆','小工具','韩国','日本','欧美','香港','台湾','澳门','上海','北京','重庆','江苏','南京','镇江','常州','无锡','宜兴','江阴','苏州','昆山','张家港','连云港','扬州','徐州','宿迁','淮安','盱眙','盐城','泰安','泰州','泰兴','靖江','南通','宁波','杭州','山东','济南','威海','廊坊','青岛','日照','成都','广东','广州','惠州','深圳','武汉','珠海','汕头','汕尾','厦门','浙江','温州','绍兴','金华','义乌','安徽','芜湖','六安','马鞍山','蚌埠','安庆','黄山','合肥','阜阳','滁州','山西','阳泉','洛阳','天津','太原','吉林','沈阳','辽宁','长春','长沙','大连','石家庄','西安','新疆','东莞','福州','湖北','湖南','福建','黑龙江','湖州','东营','中山','临夏','临汾','临沂','临沧','丹东','丽水','丽江','乌兰察布盟','乌>海','乌鲁木齐','乐山','九江','云南','昆明','三明','三门峡','上饶','东川','七台河','万县','三亚','云浮','亳州','伊克昭盟','伊春','伊犁哈萨克','佛山','佳木斯','保定','保山','信阳','克孜勒苏柯尔克孜','克拉玛依','六盘水','兰州','兴安盟','其','内江','内蒙古','包头','北>海','十堰','南充','南宁','南宁','南平','南昌','南阳','博尔塔拉蒙古自治州','双鸭山','台州','吉安','吐鲁番','吕梁','周口','呼伦贝尔盟','呼和浩特','和田','咸宁','哈密','哈尔滨','哲里木盟','唐山','商丘','商洛','喀什','嘉兴','嘉峪关','四川','四平','固原','塔城','大兴安>岭','大同','大庆','大理','天水','娄底','孝感','宁夏','宁德','安康','安阳','安顺','定西','宜宾','宜昌','宜春','宝鸡','宣州','宿州','山南','岳阳','巢湖','巴中','巴彦淖尔盟','巴音郭楞','常德','平凉','平顶山','广元','广安','广西','庆阳','延安','延边','开封','张家界','张家口','张掖','德宏','德州','德阳','忻州','怀化','怒江','思茅','恩施','承德','抚州','抚顺','拉萨','揭阳','攀枝花','文山','新乡','新余','日喀则','昌吉','昌都','昭通','晋中','晋城','景德镇','曲靖','朔州','朝阳','本溪','松原','松花江','林芝','果洛','枣庄','柳州','株洲','桂林','梅州','梧州','楚雄','榆林','武威','毕节','永州','汉中','江西','江门','池州','沧州','河北','河南','河池','河源','泉州','泸州','济宁','海东','海北','海南','海口','海西','涪陵','淄博','淮北','淮南','清远','渭南','湘潭','湘西','湛江','滨州','漯河','漳州','潍坊','潮州','濮阳','烟台','焦作','牡丹江','玉林','玉溪','甘南','甘孜','甘肃','白城','白山','白银','百色','益阳','盘锦','石嘴山','石河子','秦皇岛','红河','绥化','绵阳','聊城','自贡','舟山','茂名','荆沙','荆门','莆田','莱芜','菏泽','萍乡','营口','衡水','衡阳','衢州','襄樊','西双版纳','西宁','西藏','许昌','贵州','贵港','贵阳','赣州','赤峰','辽源','辽阳','达川','运城','迪庆','通化','遂宁','遵义','邢台','那曲','邯郸','邵阳','郑州','郴州','鄂州','酒泉','金昌','钦州','铁岭','铜仁','铜川','铜陵','银南','银川','锡林郭勒盟','锦州','锦西','长治','阜新','防城港','阳江','阿克苏','阿勒泰','阿坝','阿拉 善盟','阿里','陇南','陕西','雅安','青海','鞍山','韶关','驻马店','鸡西','鹤壁','鹤岗','鹰潭','黄冈','黄南','黄石','黑河','黔江','黔西','齐齐哈尔','龙岩');
  $HotKeyword=$KeywordsArray[$Order_ID % count($KeywordsArray)].'化妆品批发';
  $PageTitle=substr($Order_Receipt,0,2).'的订单明细-地区代理'.$HotKeyword.'-涵若铭妆';	
}
else{
  $HotKeyword='进口化妆品批发';
  $PageTitle='客户订单-涵若铭妆-化妆品批发';	
}
$PageKeywords='客户订单,化妆品批发,'.$HotKeyword.',南京化妆品批发,上海化妆品批发,韩国化妆品批发';
$PageDescription='涵若铭妆提供各种韩国化妆品批发,常州化妆品批发,欧美化妆品批发,进口化妆品批发、'.$HotKeyword.'批发,国际名牌化妆品,网络热销化妆品、精油香水等化妆品批发零售业务, 是南京地区规模最大的进口名牌化妆品批发平台';
?><html>
<head>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<META http-equiv="Keywords" content="<?php echo $PageKeywords;?>">
<META http-equiv="Description" content="<?php echo $PageDescription;?>"><SCRIPT language="JavaScript" src="/user/cmbase.js"></SCRIPT>
<title><?php echo $PageTitle;?></title><link href="/include/mycss.css" rel="stylesheet" type="text/css">
</head>
<body topmargin="0" leftmargin="0"><SCRIPT language="JavaScript" src="/include/page_frame.js"></SCRIPT>
<table width="1000" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
<tr> 
  <td>
     <IMG src="/images/arrow2.gif" width=6 height=7>&nbsp;当前位置：&nbsp;<A href="/#">首页</a>  &gt;&gt;  <?php echo $HotKeyword;?> &gt;&gt; <a href="/orders.php">订单</a>详细信息
  </td>
</tr>
  <tr> 
    <td height="200" valign="top" bgcolor="#FFFFFF"><?php
      if(empty($ordername)){ 
    	 showorders();?><script>show_new_orders();</script><?php
      }
      else{?>
    	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr>
          <td background="images/topbg.gif" nowrap>
          	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    	      <tr>
    		      <td width="70%" nowrap><img src="images/pic17.gif" width="17" height="15" align="absmiddle" />订单号：<b><font color="#FF0000"><?php echo $OrderName;?></font></b> ，<img src="images/pic18.gif" width="17" height="15" align="absmiddle" />会员名：<b><?php echo substr($username,0,2).'********';?></b></td>
    		      <td width="30%" nowrap align="right"></td>
    		    </tr>
    		    </table>
          </td>
        </tr>
      </table>
 	    <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="20" bgcolor="#F7F7F7"> 
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong><strong>编号</strong></strong></td>
          <td WIDTH="60%" height="25" align="center" background="images/topbg.gif"><strong><strong>名称</strong></strong></td>
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>数量</strong></td>
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>单价</strong></td>
          <td WIDTH="8%" height="25" align="center" background="images/topbg.gif"><strong>合计</strong></td>
          <td WIDTH="6%" height="25" align="center" background="images/topbg.gif"><strong>备 注</strong></td>
      </tr><?php
        $TotalPrice=0;  #价格总计
        $TotalCount=0;  #商品总项目
        $TotalProduct=0;   #商品总件数
        foreach($res as $row){
           $TotalCount++;
           $Amount = $row['amount'];
           $Remarks= $row['Remarks'];
	   $price =  $row['price'];
           $TotalProduct=$TotalProduct+$Amount;
           $TotalPrice =$TotalPrice+$Amount*$price;?> 
                   <tr align="center" bgcolor="#FFFFFF" height="20"> 
                     <TD align="center" style="color:#000000"><?php echo substr('0000'.$row['productid'],-5);?></td>
                     <TD align="left">&nbsp;<a href="/products/<?php echo $row['productid'];?>.htm"><?php echo $row['productname'];?></a></TD>
                 		 <TD align="center" style="color:#000000"><?php echo $amount;?></td>
			               <TD align="center" style="color:#000000"><?php echo round(price,1);?></td>		
			               <TD align="center" style="color:#000000"><?php echo round($Amount*$price,1);?></td>
			               <TD align="center" style="color:#000000" ><?php 
                        if($Remarks)echo '<MARQUEE onmouseover="this.stop()" onmouseout="this.start()" scrollAmount="2" scrollDelay="100" width="100%">'.$Remarks.'</MARQUEE>';
                       else echo '&nbsp;&nbsp;';?></TD></TR><?php
        }?>
       <tr height="20"> 
          <td height="25" align="center" colspan="2" background="images/topbg.gif"><b>合计</b></td>
          <td height="25" align="center" background="images/topbg.gif"><font color="#FF0000"><?php echo $TotalProduct;?></font>/<?php echo $TotalCount;?></td>
          <td height="25" align="center" background="images/topbg.gif">&nbsp;</td>
          <td height="25" align="center" background="images/topbg.gif"><?php echo round($TotalPrice,1);?></td>
          <td height="25" align="center" background="images/topbg.gif">&nbsp;</td>
      </tr>
      </table>
      
    	<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr>
      	<td align="center" background="images/topbg.gif"><?php

          $Order_Adjust_signed=round($Order_Adjust);
      	  if($Order_Adjust>0) $Order_Adjust_signed='+'.$Order_Adjust_signed;
          echo '配送费用<font color=#FF0000>'.round($DeliveryFee).'</font>元';
      	  if($Order_Adjust) echo ' &nbsp;  折扣调整<font color=#FF0000>'.$Order_Adjust_signed.'</font>元';?>
      	   &nbsp; <span style='font-family:Wingdings;'>à</span> &nbsp;  订单总额：￥<B><FONT color="#FF0000"><?php echo round($OriginOrderTotalPrice);?></font></B>元
            &nbsp; &nbsp; | &nbsp; &nbsp;  获得积分：<font color="#FF0000"><?php echo $OriginOrderTotalScore;?></font>分 &nbsp; &nbsp; | &nbsp; &nbsp; <span><?php echo $HotKeyword;?></span>
        </td>
     </tr>
     </table>
      

    </td>
 
  </tr>
</table>

<table width="1000" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif">
    	<b><img src="images/pic5.gif" width="28" height="22" align="absmiddle">订单附加信息</b>
    </td>
  </tr>  
  <tr>
  	<td>
  		<table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
      <tr height="20" bgcolor="#F7F7F7">
          <td WIDTH="100" height="25" align="right" background="images/topbg.gif"><strong>收 货 人：</strong></td>
          <td> &nbsp; <?php echo substr($Order_Receipt,0,2).'********';?></td>
         
          </td>
      </tr>
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>付款方式：</strong></td>
          <td height="25"> &nbsp; <?php echo $Order_PayMethod;?></td>
      </tr> 
      <!--tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>配送方式：</strong></td>
          <td height="25"> &nbsp; <?php echo $Order_DeliveryMethod;?></td>
      </tr--> 
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>运单号码：</strong></td>
          <td height="25"> &nbsp;
          <?php if(strlen($Order_DeliveryCode)>2) echo substr($Order_DeliveryCode,strlen($Order_DeliveryCode)-2).'**'; else echo '**';?>
          </td>
      </tr> 
      <tr height="20" bgcolor="#F7F7F7"> 
          <td height="25" align="right" background="images/topbg.gif"><strong>发货时间：</strong></td>
          <td height="25" colspan=2> &nbsp; <?php echo $Order_ActionTime;?></td>

      </tr>       
      </table><?php
      }?>
    </td>
  </tr>
</table><?php
include('include/page_bottom.htm');
db_close();?>
</body>
</html> 
