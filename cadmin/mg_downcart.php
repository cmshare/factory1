<?php require('includes/dbconn.php');
CheckLogin();
$userid=@$_GET['userid'];
if(!is_numeric($userid) || $userid<=0) PageReturn('<p align=center>参数错误！</p>',0);?>
<html xmlns:x="urn:schemas-microsoft-com:office:excel">
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
  OpenDB(); 
  $row=$conn->query('select  mg_users.username,mg_users.grade,mg_usrgrade.title from mg_users inner join mg_usrgrade on mg_users.grade=mg_usrgrade.id where mg_users.id='.$userid,PDO::FETCH_ASSOC)->fetch();
  if($row){
    $UserName=$row['username'];
    $UserGrade=$row['grade'];
    $UserTitle=$row['title'];
    $UserPrice='price'.$UserGrade; 
  }
  else PageReturn('<p align=center>用户不存在！</p>',0);


 $res=$conn->query('select mg_favorites.productid,mg_favorites.amount,mg_favorites.remark,mg_product.name,mg_product.'.$UserPrice.',mg_product.score from (mg_favorites inner join mg_product on  mg_favorites.productid=mg_product.id) inner join mg_category on mg_category.id=mg_product.brand where mg_favorites.userid='.$userid.' and mg_favorites.amount>0 and (mg_favorites.state&0x2) order by mg_category.sortindex,mg_product.name',PDO::FETCH_ASSOC);

header('content-type:application/vnd.ms-excel'); 
//header('content-type:application/x-download'); 
header('content-disposition:attachment; filename='.$UserName.'的购物车.xls'); 
$total_records=0;
$index=2;
foreach($res as $row){
  if($total_records++==0){
    echo '购物清单（会员名：'.$UserName.'）<BR>'; 
    echo '<table width="640" border="1"  style="table-layout:fixed;word-wrap:break-word;word-break:break-all">
      <tr bgcolor="#DFDFDF">
          <td WIDTH="10%" height="25" align="center"><strong>编号</strong></td>
          <td WIDTH="60%" height="25" align="center"><strong>名称</strong></td>
          <td WIDTH="10%" height="25" align="center"><strong>数量</strong></td>
          <td WIDTH="10%" height="25" align="center"><strong>单价</strong></td>
          <td WIDTH="10%" height="25" align="center"><strong>合计</strong></td>
      </tr>';
  }
 
  $index++; 
  $ProductName=$row['name'];
  $ProScore=$row['score']; 
  $YourPrice=$row[$UserPrice];
  $Amount=$row['amount'];
  $Remark=$row['remark'];
 
  echo '<tr><td align="center">'.chr(30).GenProductCode($row['productid']).'</td><td>'.$ProductName;
  if($Remark) echo ' &nbsp; <font style="font-size:9pt;font-weight:bold;">***【备注】：<u>'.$Remark.'</u>***</font>';
  echo '</td><td align="center">'.$Amount.'</td><td align="center">'.$YourPrice.'</td><td align="center">=C'.$index.'*D'.$index.'</td></tr>';
}
if($total_records>0) echo '
      <tr bgcolor="#DFDFDF">
      	 <td colspan=4 align="center"><b>合计:</b></td>
      	 <td align="center" ><b>=SUM(E3:E'.$index.')</b></td>
      </tr>
      </table>';
else echo '<br><br><p align=center>购物车为空！</p><br><br>';


CloseDB();
?>
</body>
</html>
