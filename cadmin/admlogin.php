<?php require('includes/dbconn.php');
session_start();

$adminuser=FilterText(trim($_POST['adminuser']));
$adminpsw=FilterText(trim($_POST['adminpsw']));
$authcode=trim($_POST['authcode']);  

if(empty($adminuser)|| empty($adminpsw) ||empty($authcode)){
  echo '<script>alert("登录失败！请检查您的登录信息是否完整！");</script>';
}
else if(strtolower($authcode)!=strtolower($_SESSION['authcode'])) {
  echo '<script LANGUAGE="javascript">var verifyimg=parent.document.all("LoginCheckout");if(verifyimg)verifyimg.src="includes/authcode.php?handle='.time().'";alert("登录失败！验证码错误！");</script>';
}
else {
   OpenDB();
   $popedom=$conn->query("select popedom from `mg_users` where username='$adminuser' and password=md5('$adminpsw')",PDO::FETCH_NUM)->fetchColumn();
   if($popedom){
     $own_popedomFinance=CheckPopedom('FINANCE',$popedom);
     if($own_popedomFinance){
       $accountant=$conn->query('select accountant from `mg_configs`')->fetchColumn(0);
       if($accountant!=$adminuser){
          label_illegal:echo '<script LANGUAGE="javascript">alert("权限错误，登录失败，请联系管理员！");</script>';
          CloseDB();
          exit(0);
       }
     }

     if(!($cip=$_SERVER["HTTP_CLIENT_IP"]) && !($cip=$_SERVER["HTTP_X_FORWARDED_FOR"])&&!($cip=$_SERVER["REMOTE_ADDR"])) $cip='0.0.0.0';
     if($conn->exec("update `mg_admins` set loginip='$cip',logintime=unix_timestamp() where username='$adminuser'")){
       $timeout=time()+3600*24;
       $_SESSION['meray[admin]']=$adminuser;
       setcookie('meray[admin]',$adminuser,$timeout); 
       setcookie('meray[popedom]',$popedom,$timeout); 
       setcookie('meray[depot]',''); 
       setcookie('meray[home]',''); 
       if($own_popedomFinance && date('wH')<12){ //财务专员在周日12点前更新订单(未收货确认超时处理)
         $conn->exec('update mg_orders set state=6 where state=5 and actiontime<unix_timestamp()-30*24*60*60');
       }
       $conn->exec('insert into mg_logs set type=1,username=\''.$adminuser.'\',remark=\''.$cip.'\',addtime=unix_timestamp()');  
       echo '<script LANGUAGE="javascript">top.location.href="'.$_SERVER['HTTP_REFERER'].'";</script>';
     }else goto label_illegal;
   }
   else echo '<script LANGUAGE="javascript">alert("用户名或者密码错误，登录失败！");</script>';
   CloseDB();
}
