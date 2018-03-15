<?php
header("content-type:text/html;charset=utf-8");
date_default_timezone_set("PRC");//设置时区，否则将时间戳转换为时间字符串时会有时差；

require_once('config.php');
require_once('pagepay/service/AlipayTradeService.php');
require_once('pagepay/buildermodel/AlipayTradePagePayContentBuilder.php');

//商户订单号，商户网站订单系统中唯一订单号，必填
$pay_tradeno = FilterText(trim($_POST['pay_tradeno']));

//订单名称，必填
$pay_subject = FilterText(trim($_POST['pay_subject']));

//付款金额，必填
$pay_amount = trim($_POST['pay_amount']);

//商品描述，可空
$pay_user = FilterText(trim($_POST['pay_remark']));

if(is_numeric($pay_amount))$pay_amount=round($pay_amount,2);
else $pay_amount=0;

if($pay_amount>0 && $pay_tradeno && $pay_subject && $pay_user ){
    OpenDB();
    
    $conn->exec('lock tables mg_onlinepay write'); 
    $bExist=$conn->query('select id from mg_onlinepay where tradeno=\''.$pay_tradeno.'\'')->fetchColumn(0);
    if(empty($bExist)){
      $returl=@$_SERVER['HTTP_REFERER'];
      $param_pos=strpos($returl,'?');
      if($param_pos>0)$returl=substr($returl,0,$param_pos);

      $sql="mg_onlinepay set tradeno='$pay_tradeno',username='$pay_user',site='$returl',amount=$pay_amount,mode=1,state=1,actiontime=unix_timestamp()";
      if($conn->exec('update '.$sql.' where state=0 order by actiontie asc limit 1')||$conn->exec('insert into '.$sql)){
	 $conn->exec('unlock tables');  

	 //构造参数
	 $payRequestBuilder = new AlipayTradePagePayContentBuilder();
	 $payRequestBuilder->setBody($pay_user);
	 $payRequestBuilder->setSubject($pay_subject);
	 $payRequestBuilder->setTotalAmount($pay_amount);
	 $payRequestBuilder->setOutTradeNo($pay_tradeno);

	 $aop = new AlipayTradeService($config);

	 /**
	  * pagePay 电脑网站支付请求
	  * @param $builder 业务参数，使用buildmodel中的对象生成。
	  * @param $return_url 同步跳转地址，公网可以访问
	  * @param $notify_url 异步通知地址，公网可以访问
	  * @return $response 支付宝返回的信息
	 */
	 $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

	 //输出表单
	 var_dump($response);
      } 
    }
    CloseDB();
}?>
