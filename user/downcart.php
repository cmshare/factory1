<?php require('../include/conn.php');
$PageTitle='购物车-清单下载-'.WEB_NAME;
?><html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <!--[if gte mso 9]><xml>
            <x:ExcelWorkbook>
                <x:ExcelWorksheets>
                    <x:ExcelWorksheet>
                        <x:Name><?php echo $PageTitle;?></x:Name>
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
<META http-equiv="Description" content="本文档即时生成<?php echo WEB_NAME;?>购物车产品下载清单,化妆品范围包括各种进口化妆品批发,韩国化妆品批发,欧美化妆品批发等">
<title><?php echo $PageTitle;?></title>             
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
if(!CheckLogin(0)){
  echo '<br><br><br><p align="center">请先登录</p><br><br><br>';
  exit(0);
}
  
OpenDB();
$row=$conn->query('select `mg_users`.username,`mg_users`.grade,`mg_usrgrade`.title from `mg_users` inner join `mg_usrgrade` on `mg_users`.grade=`mg_usrgrade`.id where `mg_users`.id='.$LoginUserID,PDO::FETCH_NUM)->fetch();
if($row){
  $UserName=$row[0];
  $UserGrade=$row[1];
  $UserTitle=$row[2];
}
else{
  CloseDB();
  echo '<p align=center>用户不存在！</p>';
  exit(0);
}

$res=$conn->query('select `mg_favorites`.productid,`mg_favorites`.amount,`mg_favorites`.remark,`mg_product`.name,`mg_product`.price0,`mg_product`.price'.$UserGrade.' as myprice,`mg_product`.onsale from ((`mg_favorites` inner join `mg_product` on `mg_favorites`.productid=`mg_product`.id) inner join `mg_brand` on `mg_brand`.id=`mg_product`.brand) where `mg_favorites`.userid='.$LoginUserID.' and `mg_favorites`.amount>0 and (`mg_favorites`.state=2 or `mg_favorites`.state=3) order by `mg_brand`.sortindex,`mg_product`.name',PDO::FETCH_ASSOC);
$row=$res->fetch();
header('Content-Type: application/vnd.ms-excel');  
header('Content-Disposition: attachment; filename='.$UserName.'的购物车.xls');  
if(empty($row)) echo '<br><br><p align=center>购物车为空！</p><br><br>';
else {
  echo '购物清单（会员名：'.$UserName.'）<BR>'; 
  echo '<table width="640" border="1"  style="table-layout:fixed;word-wrap:break-word;word-break:break-all">
      <tr bgcolor="#DFDFDF">
          <td WIDTH="10%" height="25" align="center"><strong>编号</strong></td>
          <td WIDTH="60%" height="25" align="center"><strong>名称</strong></td>
          <td WIDTH="10%" height="25" align="center"><strong>数量</strong></td>
          <td WIDTH="10%" height="25" align="center"><strong>单价</strong></td>
          <td WIDTH="10%" height="25" align="center"><strong>合计</strong></td>
      </tr>';

  $index=2;
  do{
    $index++; 
    $ProductName=$row['name'];
    $myprice = $row['myprice'];
    if(($row['onsale']&0xf)>0 && $UserGrade>2 && $row['onsale']>time() && $row['price0']<$myprice) $myprice=$row['price0'];
    $Amount = $row['amount'];
    $remark = $row['remark'];
    echo '<tr><td align="center">'.chr(30).substr('0000'.$row['productid'],-5).'</td><TD>'.$ProductName;
    if($remark)echo ' &nbsp; <font style="font-size:9pt;font-weight:bold;">***【备注】：<u>'.$remark.' </u>***</font></TD>';

    echo '<td align="center">'.$Amount.'</td><td align="center">'.$myprice.'</td><td align="center">=C'.$index.'*D'.$index.'</td></TR>';
    $row=$res->fetch();
  }while($row); 
  echo '<tr bgcolor="#DFDFDF"><td colspan=4 align="center"><b>合计:</b></td><td align="center" ><b>=SUM(E3:E'.$index.')</b></td></tr></table>';
  $row=$res->fetch();
}

CloseDB();?>
</body>
</html>
