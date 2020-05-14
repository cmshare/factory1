<?php require('includes/dbconn.php');
CheckLogin();
$OrderName=FilterText(trim(@$_GET['ordername']));
if(!$OrderName)PageReturn('<p align=center>参数错误！</p>',0);

OpenDB();

$row=$conn->query('select state,support,username,receipt,address,usertel,userremark from mg_orders where ordername=\''.$OrderName.'\'',PDO::FETCH_ASSOC)->fetch();
if($row){
  $OrderState=$row['state'];
  $Receipt=$row['receipt'];
  $UserName=$row['username'];
  $Address=$row['address'];
  $UserTel=$row['usertel'];
  $UserRemark=$row['userremark'];
  $Operator=$row['support'];
  $OrderDate=date('Y-m-d',$row['actiontime']);
}
else PageReturn('<p align=center>订单不存在！</p>',0);
?><html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
<body><?php

header('content-type:application/vnd.ms-excel'); 
//header('content-type:application/x-download'); 
header('content-disposition:attachment; filename='.$UserName.'的订单('.$OrderName.').xls'); 

$res=$conn->query('select mg_ordergoods.id,mg_ordergoods.productid,mg_ordergoods.price,mg_ordergoods.amount,mg_ordergoods.remark,mg_ordergoods.productname,mg_ordergoods.score from (mg_ordergoods inner join mg_product on mg_ordergoods.productid=mg_product.id) inner join mg_category on mg_category.id=mg_product.brand where mg_ordergoods.ordername=\''.$OrderName.'\' and mg_ordergoods.amount>0 order by mg_category.sortindex,mg_ordergoods.productname',PDO::FETCH_ASSOC);

if($OrderState>0){
  echo '<p align="center"><font size=4><strong>南京铭悦日化用品有限公司（出货单）</strong></font></p>
      <b>订单号</b>：'.$OrderName.'（'.$UserName.'）&nbsp; &nbsp; &nbsp; <b>客服</b>：<u>&nbsp; &nbsp; '.$Operator.'&nbsp; &nbsp; </u>&nbsp; &nbsp; <b>配货</b>：<u>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </u>&nbsp; &nbsp; <b>审核</b>：<u>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </u>
	    <table width="640" border="1" style="table-layout:fixed;word-wrap:break-word;word-break:break-all">
      <tr bgcolor="#DFDFDF"> 
          <td WIDTH="10%" align="center"><strong>编号</strong></td>
          <td WIDTH="60%" align="center"><strong>名称</strong></td>
          <td WIDTH="10%" align="center"><strong>数量</strong></td>
          <td WIDTH="10%" align="center"><strong>价格</strong></td>
          <td WIDTH="10%" align="center"><strong>合计</strong></td>
      </tr>';

  $index=4;
  foreach($res as $row){
    $index++;
    $Price =$row['price'];
    $Amount = $row['amount'];
    $Remark = $row['remark'];
    if($Remark)$Remark=' &nbsp; <font style="font-size:9pt;font-weight:bold;">***【备注】：<u>'.$Remark.' </u>***</font>';
    echo '<tr><td align="center">'.chr(30).GenProductCode($row['productid']).'</td>
          <td>'.$row['productname'].$Remark.'</td>
          <td align="center">'.$Amount.'</td>
	  <td align="center">'.$Price.'</td>
	  <td align="center">=C'.$index.'*D'.$index.'</td></tr>'; 
  }
  echo '<tr bgcolor="#DFDFDF"><td colspan=4 align="center"><b>合计:</b></td><td align="center" ><b>=SUM(E3:E'.$index.')</b></td></tr>
        <tr><td colspan=5 align="center" height="30" valign="bottom">
     <b>货款：</b><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>&nbsp;
     <b>运费：</b><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>&nbsp;
     <b>合计应收：</b><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>&nbsp;
     <b>实收：</b></td></tr> </table>
      <b>收货人</b>：'.$Receipt.' &nbsp;<b>电话</b>：'.$UserTel.'<br>
      <b>地址</b>：'.$Address.' &nbsp;';

  if($UserRemark) echo '<br><b>客户留言</b>：<u>'.$UserRemark.'</u>';
}
else{
  echo '<p align="center"><strong><font size=4>涵若铭妆内部订单</font></strong></p>
 <b>订单号</b>：'.$OrderName.'&nbsp; &nbsp; &nbsp;<b>订单备注</b>：&nbsp; &nbsp; '.$UserRemark.'
	    <table width="640" border="1" style="table-layout:fixed;word-wrap:break-word;word-break:break-all">
      <tr bgcolor="#DFDFDF"> 
          <td WIDTH="10%" align="center"><strong>序号</strong></td>
          <td WIDTH="70%" align="center"><strong>名称</strong></td>
          <td WIDTH="20%" align="center"><strong>数量</strong></td>
      </tr>';
  $index=0;
  foreach($res as $row){ 
    $index++;
    $Price = $row['price'];
    $Amount = $row['amount'];
    $Remark = $row['remark'];  
    if($Remark) $Remark=' &nbsp; <font style="font-size:9pt;font-weight:bold;">***【备注】：<u>'.$Remark.' </u>***</font>';
    echo '<tr> 
            <td align="center">'.chr(30).$index.'</td>
            <td>'.$row['productname'].$Remark.'</td>
            <td align="center">'.$Amount.'</td>
          </tr>';
  }
  echo '</table>';
}


CloseDB();?>
</body>
</html>
