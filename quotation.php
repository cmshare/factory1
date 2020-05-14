<?php require('include/conn.php');
OpenDB();
$mypagetitle=WEB_NAME.'--化妆品批发报价单'.date('Ymd');
header('Content-Type: application/vnd.ms-excel');  
header('Content-Disposition: attachment; filename='.$mypagetitle.'.xls');  
if(@$_GET['goodprice']=='yes'){
  $GoodPriceName='price4';
  $GoodPriceCaption='大客户价';
}
else{
  $GoodPriceName='price3';
  $GoodPriceCaption='批发价';
}
$sql=(@$_GET['stock']=='show')?'and `mg_product`.stock0>0':''; 
$sql='select `mg_product`.id,`mg_product`.name,`mg_product`.price0,`mg_product`.price1,`mg_product`.'.$GoodPriceName.',`mg_product`.onsale from `mg_product` inner join `mg_category` on `mg_category`.id=`mg_product`.brand where `mg_product`.recommend>0 '.$sql.' order by `mg_category`.sortindex,`mg_product`.name';
echo $sql;
$res=$conn->query($sql,PDO::FETCH_NUM);?>
<html  xmlns:x="urn:schemas-microsoft-com:office:excel">
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
<META http-equiv="Keywords" content="报价,化妆品,香水,精油,报价表,化妆品批发报价单">
<META http-equiv="Description" content="本文档即时生成涵若铭妆产品库的化妆品批发报价单,化妆品范围包括各种进口化妆品批发,韩国化妆品批发,欧美化妆品批发等">
<title>化妆品批发报价单--<?php echo WEB_NAME;?></title>
</head>
<body>
<table width="100%" border="0" align="center" >
          <tr align="center">
            <td width="10%">商品编号</td>
            <td width="60%">商品名称</td>
            <td width="10%">市场价</td> 
            <td width="10%"><?php echo $GoodPriceCaption;?></td>
          </tr><?php
foreach($res as $row){
  echo '<tr align="center"><td align="center">'.chr(30).substr("0000".$row[0],-5).'</td><td align="left">'.$row[1].'</td><td align="center">'.round($row[3],1).'</td><td align="center">';
  $price_vip=$row[4];
  $onsale=$row[5];
  if(($onsale&0xf)>0){
     $price_tejia=$row[2];
     if(time()<$onsale && $price_vip>$price_tejia) $price_vip=$price_tejia;
  }
  echo round($price_vip,2).'</td></tr>';
}
CloseDB();
?>
</table>
</body>
</html>
