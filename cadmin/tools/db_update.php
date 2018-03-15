<?php
//要保存为无BOM(Byte Order Mark)格式，否则被require时会导致出现空白行。

header("content-type:text/html;charset=utf-8"); 
date_default_timezone_set("PRC");//设置时区，否则将时间戳转换为时间字符串时会有时差；

function OpenDB()
{ global $conn,$conn; 
  if(empty($conn))
  { try
    {// $conn = new PDO("mysql:host=localhost;dbname=miaowdb","miaow","miaomiao");
      define("WEB_DOMAIN","svr.mplanet.cn");
      $conn = new PDO("mysql:host=localhost;dbname=meowcloud","meow","miaomiao");
      $conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER); //指定关联索引为小写。
      $conn->exec('SET NAMES utf8'); //设置字符集编码方式
    }
    catch (Exception $e)
    { exit("Failed:".$e->getMessage());
    }
  }  
}  

function CloseDB()
{ global $conn; 
  if(isset($conn))unset($conn);
}

OpenDB();
$query=$conn->query("select *  from  mc_admins ",PDO::FETCH_ASSOC); 
foreach($query as $rs){
/*   $app_url=str_replace('http://www.mplanet.cn','',$rs['app_download_url']);
   $mainfw_url=str_replace('http://www.mplanet.cn','',$rs['fw_download_url']);
   echo $app_url."<br>";
   $conn->exec("update mc_usrgroup set app_ver_main={$rs['app_version_main']},app_ver_minor={$rs['app_version_minor']},app_url='{$app_url}' where id={$rs['id']}"); 
   $conn->exec("update mc_devgroup set mcufw_ver={$rs['mcufw_version']},mainfw_ver={$rs['mainfw_version']},mainfw_url='{$mainfw_url}' where id={$rs['id']}"); 
   */
 //  $conn->exec("update mc_devgroup set mainfw_upgradestory='{$rs['fwupgradestory']}' where id={$rs['id']}"); 
}

CloseDB();
echo WEB_DOMAIN;
?>
