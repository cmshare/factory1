<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>在线支付结果</title>
</head>
<body><?php
/* *
 * 功能：支付宝页面跳转同步通知页面
 * 版本：2.0
 * 修改日期：2017-05-01
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 */
require_once("config.php");
require_once 'pagepay/service/AlipayTradeService.php';


$arr=$_GET;
$alipaySevice = new AlipayTradeService($config); 
$result = $alipaySevice->check($arr);

/* 实际验证过程建议商户添加以下校验。
1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
4、验证app_id是否为该商户本身。
*/
if($result) {//验证成功
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//请在这里加上商户的业务逻辑程序代码
    db_open();
    echo '<p align=center><font color="#FF0000">在线支付已经支付成功！</font></p>'; 
    $out_trade_no = htmlspecialchars(trim($_GET['out_trade_no'])); //商户订单号
    $returl=$conn->query('select site from mg_onlinepay where tradeno=\''.$out_trade_no.'\' and mode=1')->fetchColumn(0);
    if($returl) echo '<p align="center"><a href="'.$returl.'">返回商户网站</a>...</p>';
    //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
    db_close();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else{
  echo "验证失败"; //验证失败
}?>
</body>
</html>
