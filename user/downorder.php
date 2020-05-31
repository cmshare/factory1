<?php require('../include/conn.php');

if(!CheckLogin(0)){
  echo '<br><br><br><p align="center">请先登录</p><br><br><br>';
  exit(0);
}

$ordername=trim(FilterText($_GET['ordername']));
if(empty($ordername)){
   echo '<p align=center>参数错误！</p>';
   exit(0);
}

db_open();
$row=$conn->query('select `mg_orders`.support,`mg_orders`.username,`mg_orders`.receipt,`mg_orders`.address,`mg_orders`.usertel,`mg_orders`.userremark from `mg_orders` inner join `mg_users` on `mg_orders`.username=`mg_users`.username where `mg_orders`.ordername=\''.$ordername.'\' and `mg_users`.id='.$LoginUserID,PDO::FETCH_ASSOC)->fetch();
if($row){
  $Receipt=$row['receipt'];
  $username=$row['username'];
  $Address=$row['address'];
  $UserTel=$row['usertel'];
  $UserRemark=$row['userremark'];
  $Operator=$row['support'];
}
else{
  echo '<p align=center>订单不存在！</p>';
  goto LABEL_EXIT;
}

$res=$conn->query('select `mg_ordergoods`.id,`mg_ordergoods`.productid,`mg_ordergoods`.price,`mg_ordergoods`.amount,`mg_ordergoods`.remark,`mg_ordergoods`.productname,`mg_ordergoods`.score from (`mg_ordergoods` inner join `mg_product` on `mg_ordergoods`.productid=`mg_product`.id) inner join `mg_category` on `mg_category`.id=`mg_product`.brand where `mg_ordergoods`.ordername=\''.$ordername.'\' and `mg_ordergoods`.amount>0 order by `mg_category`.sortindex,`mg_ordergoods`.productname',PDO::FETCH_ASSOC);
header('Content-Type: application/vnd.ms-excel');  
header('Content-Disposition: attachment; filename='.$username.'的订单('.$ordername.').xls');  ?>
<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<META http-equiv="Keywords" content="报价,批发,订单,化妆品,精油,韩国化妆品批发,进口化妆品批发,香水批发">
<META http-equiv="Description" content="这里是订单下载,我们主要提供韩国化妆品批发,进口化妆品批发,香水批发,欧美化妆品批发等">
<!--[if gte mso 9]><xml>
  <x:ExcelWorkbook>
  <x:ExcelWorksheets>
  <x:ExcelWorksheet>
  <x:Name>工作表标题</x:Name>
  <x:WorksheetOptions>
  <x:Print>
  <x:ValidPrinterInfo />
  </x:Print>
  </x:WorksheetOptions>
  </x:ExcelWorksheet>
  </x:ExcelWorksheets>
  </x:ExcelWorkbook>
  </xml>
<![endif]-->
<title>订单下载 -<?php echo WEB_NAME;?></title>        
<style>
<!--
@page
	{margin:0in .75in 0in .75in;
	mso-header-margin:0in;
	mso-footer-margin:0in;}
	TD   {FONT-FAMILY:宋体;FONT-SIZE: 10pt;line-height: 100%;}
	BODY {FONT-FAMILY:ARIAL;FONT-SIZE:10pt;}
-->
</style>
</head>
<body>
<p align="center"><strong><font size=4><?php echo WEB_NAME;?>出库清单</font></strong></p>
<b>订单号</b>：<?php echo $ordername;?>（<?php echo $username;?>）&nbsp; &nbsp; &nbsp; <b>客服</b>：<u>&nbsp; &nbsp; <?php echo $Operator;?>&nbsp; &nbsp; </u>&nbsp; &nbsp; <b>配货</b>：<u>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </u>&nbsp; &nbsp; <b>审核</b>：<u>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </u>
    <table width="640" border="1" style="table-layout:fixed;word-wrap:break-word;word-break:break-all">
      <tr bgcolor="#DFDFDF"> 
          <td WIDTH="10%" align="center"><strong>编号</strong></td>
          <td WIDTH="60%" align="center"><strong>名称</strong></td>
          <td WIDTH="10%" align="center"><strong>数量</strong></td>
          <td WIDTH="10%" align="center"><strong>价格</strong></td>
          <td WIDTH="10%" align="center"><strong>合计</strong></td>
      </tr><?php
      $index=4;
                     
      foreach($res as $row){
        $index++;
        $price = $row['price'];
        $amount = $row['amount'];
        $remark = trim($row['remark']);?>
       <tr> 
          <td align="center"><%=chr(30)&right("0000"&$row['ProductID"),5)%></td>
          <td><?php
           echo $row['productname'];
           if($remark) echo ' &nbsp; <font style="font-size:9pt;font-weight:bold;">***【备注】：<u>'.$remark.' </u>***</font>';?></td>
          <td align="center"><?php echo $amount;?></td>
          <td align="center"><?php echo round($price,2);?></td>
          <td align="center">=C<?php echo $index;?>*D<?php echo $index;?></td>
        </tr><?php
      }?>
      <tr bgcolor="#DFDFDF">
      	 <td colspan=4 align="center"><b>合计:</b></td>
      	 <td align="center" ><b>=SUM(E3:E<?php echo $index;?>)</b></td>
      </tr>
      <tr>
      	 <td colspan=5 align="center" height="30" valign="bottom">
     <b>货款：</b><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>&nbsp;
     <b>运费：</b><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>&nbsp;
 <b>合计应收：</b><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>&nbsp;
     <b>实收：</b><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>	
      	 </td>
      </tr>
      </table>
<b>收货人</b>：<?php echo $Receipt;?> &nbsp;<b>电话</b>：<?php echo $UserTel;?><br>
<b>地址</b>：<?php echo $Address;?> &nbsp;
<?php if($UserRemark) echo '<br><b>客户留言</b>：<u>'.$UserRemark.'</u>';

LABEL_EXIT: db_close();?>
</body>
</html>
