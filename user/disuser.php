<?php require('../include/conn.php');
OpenDB();
switch($_GET['action']){
  case 'customerinfo': customerinfo();break;
  case 'changepass':   changepass();break;
  case 'resetpsw':     resetpsw();break; 
  case 'receiveaddr':  receiveaddr();break;
  case 'myorders':     myorders();break; 
  case 'onlinepay':    onlinepay();break;
  case 'confirmpay':   ConfirmPay();break;
  case 'accountlog':   AccountLog();break; 
  default:             MyAccount();break;
}
CloseDB();

function check_user(){
  if(!CheckLogin(0)){
    echo '<table border=0 width="100%" height="100%"><tr valign="middle"><td width="47%" align="right"><img src="/images/nologin.gif"></td><td width="53%">请先登录网站！！！</td></tr></table>';
    CloseDB();
    exit(0);
  }
}


function OnlinepayEntry(){?>
  <form style="margin:0px" onsubmit="if(this.pay_amount.value.trim()==''){alert('请填写支付金额！'); return false;}if(isNaN(this.pay_amount.value)){alert('支付金额必须填写数字！'); return false;} show_ConfirmPay(0,this.pay_amount.value);/*if(this.paymethod[0].checked)show_ConfirmPay(1,this.pay_amount.value);else if(this.paymethod[1].checked)show_ConfirmPay(0,this.pay_amount.value);else alert('还没有选择支付方式！');*/return false;">    
  <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#B7CEF4">
  <tr>
     <td width="100%" height="26" colspan="2" bgcolor="#E9EFFC" align="center" style="font-weight:bold;font-size:10pt">预存款帐户在线充值</td>
  </tr>
  <tr>
     <td width="25%" height="28" align="center"  bgcolor="#F6F6F6">充值金额：</td>
     <td width="75%" bgcolor="#F6F6F6"> <input name="pay_amount" value="500.00" onMouseOver="this.focus()"  onFocus="this.select()" onkeyup="if(isNaN(value))execCommand('undo')" style="BORDER-BOTTOM: #0561a9 1px solid; BORDER-LEFT: #0561a9 1px solid; BORDER-RIGHT: #0561a9 1px solid; BORDER-TOP: #0561a9 1px solid; COLOR: black;text-align:center"> 元（人民币）</td>
  </tr>
  <!--tr> 
       <td width="25%" height="28" align="center"  bgcolor="#F6F6F6">支付方式：</td>
       <td width="75%"   bgcolor="#F6F6F6"><input type="radio" name="paymethod" value="1">快钱在线支付 (手续费0.5%) <br><input type="radio" name="paymethod" value="0" checked >支付宝<img src="<?php echo WEB_ROOT;?>images/recommend1.gif" alt="已开启非证书余额支付功能" style="cursor:pointer" onclick="alert(this.alt)">&nbsp; (<font color=#FF0000>免</font>手续费,无需支付宝账户)</td>
  </tr-->    
  <tr> 
     <td height="40" colspan="2"  align="center" valign="middle" bgcolor=#F8FAFE><a href="http://www.chinaunionpay.com/" target="_blank"><img src="<?php echo WEB_ROOT;?>images/unipaylogo.gif" width=152 height=40 border="0" align=absMiddle></a>
        &nbsp; &nbsp; &nbsp;<input type="submit" value="下一步" style="color:#0000FF;font-weight:bold"> &nbsp;&nbsp;</td>
  </tr>
  </table>		
  </form><?php
}

   
function MyAccount(){
 global $conn,$LoginUserID;
 check_user();
 $row=$conn->query('select `mg_users`.*,`mg_usrgrade`.title from `mg_users`,`mg_usrgrade` where `mg_users`.id='.$LoginUserID.' and `mg_users`.grade=`mg_usrgrade`.id',PDO::FETCH_ASSOC)->fetch();
 if($row){
   $LoginUserName=$row['username'];
   $ShopUserGrade=$row['grade'];
   $ShopUserTitle=$row['title'];
   $UserVIPNO=$row['vipno'];
   $LoginCount=$row['logincount'];
   $LastLogin=@$_COOKIE['cmshop']['lastlogin'];
   if(!is_numeric($LastLogin)) $LastLogin=$row['lastlogin'];
   $MyDeposit=round($row['deposit'],2);
   $MyScore=$row['score'];
 }
 else
 { 
   return false;
 }
 
 $MyFund=$conn->query('select sum(amount) from `mg_accountlog` where username=\''.$LoginUserName.'\' and operation=3')->fetchColumn(0);
 if($MyFund)$MyFund=round($MyFund,2);  
   
 $TotalProductInCart=$conn->query('select sum(amount) from `mg_favorites` where userid='.$LoginUserID.' and state>1')->fetchColumn(0);
 if($TotalProductInCart===FALSE)$TotalProductInCart=0;

 $TotalPriceInCart=$conn->query('select sum(`mg_favorites`.amount*`mg_product`.price'.$ShopUserGrade.') from (`mg_favorites` inner join `mg_product` on `mg_favorites`.productid=`mg_product`.id) where `mg_favorites`.userid='.$LoginUserID.' and `mg_favorites`.state>1')->fetchColumn(0);
 if($TotalPriceInCart===FALSE)$TotalPriceInCart=0;?>
<table align="center" width="98%" border="0" cellpadding="0" cellspacing="0">
<tr><td height="40" colspan="2" background="<?php echo WEB_ROOT;?>images/kubars/kubar_account.gif" align="right"><img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; 会员中心 &gt;&gt; 账户信息</td></tr>
<tr><td>
<table align="center" width="350" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td height="30">欢迎 <font color="#FF0000"><?php echo $LoginUserName;?></font> 进入会员中心！</td>
</tr>
<?php if($UserVIPNO) echo '<tr><td height="30">会员卡号 <font color=#FF0000>'.substr('00000'.$UserVIPNO,-6).'</font></td></tr>';?>
<tr>
  <td height=30>
    <table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td>会员等级 <font color="#FF0000"><?php echo $ShopUserTitle;?></font></td><td align="right" style="font-size:9pt;color:#0000FF;cursor:Pointer"><!--u onclick="AsyncPost('','<?php echo WEB_ROOT;?>user/userupgrade.php?handle='+Math.random(),'userbox');">我要升级</u--> &nbsp; <u onclick="userlogoff()">注销退出</u></td></tr></table>
  </td>
</tr>
<tr>
  <td height="30" nowrap>预存款余额 <font color="#FF0000"><?php echo $MyDeposit;?> </font>元，<?php if($MyFund) echo '审核中金额 <font color="#FF0000">'.$MyFund.' </font>元，';?>积分 <font color="#FF0000"><?php echo $MyScore;?></font> 分</td>
</tr>	
<TR>
  <TD><?php OnlinepayEntry();?></TD>
</TR>

<tr>
  <td height="30">您的购物车中有 <font color="#FF0000"><?php echo $TotalProductInCart;?></font> 件商品，共计 <font color="#FF0000"><?php echo $TotalPriceInCart;?></font> 元</td>
</tr>
<tr>	
  <td height="30">您是第 <font color="#FF0000"><?php echo $LoginCount;?></font> 次登录本站，上次登陆时间<font color="#FF0000"><?php echo date('Y-m-d H:i:s',$LastLogin);?></font></td>
</tr>
</table>
</td></tr></table>	
<br><?php
}

function ConfirmPay(){
 global $conn,$LoginUserID,$LoginUserName;
 check_user();
 
 $pay_tradeno=date('ymdHis').sprintf('%04d',rand(1,9999));
 $pay_amount=$_POST['pay_amount'];
 if(is_numeric($pay_amount)) $pay_amount=round($pay_amount,2);
 else{
   echo '支付金额无效！';
   return false;
 }
 $paymethod=@$_POST['paymethod'];
 
 $paymethod='支付宝(免手续费)';
 $payaction='/onlinepay/alipay/index.php';
// $payaction='http://www.gdhzp.com/onlinepay/alipay/index.php';
 $paylogo='/onlinepay/alipay/images/alipaylogo3.gif';?>
<table width="770"  border="0" align="center" cellpadding="3" cellspacing="1">	
<tr><td height="40" background="<?php echo WEB_ROOT;?>images/kubars/kubar_pay.gif" align="right"><img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; 会员中心 &gt;&gt; 确认支付</td></tr>  	
<tr><td>
 <table width="363" height="109" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#B7CEF4">
 <tr>
    <td height="26" colspan="2" bgcolor="#E9EFFC" align="center" style="font-weight:bold;font-size:10pt">在 线 支 付</td>
 </tr>
 <tr>
    <td height="26"  align="center" bgcolor=#F8FAFE>流水单号:</td> 
    <td height="26"  align="center" bgcolor=#F8FAFE><?php echo $pay_tradeno;?></td>
 </tr>
 <tr>
    <td height="26"  align="center" bgcolor=#F8FAFE>支付金额:</td>
    <td height="26"  align="center" bgcolor=#F8FAFE> <?php echo $pay_amount;?> (元)</td>
 </tr>
 <!--tr>
    <td height="26"  align="center" bgcolor=#F8FAFE>支付方式:</td>
    <td height="26"  align="center" bgcolor=#F8FAFE><?php echo $paymethod;?></td>
 </tr--> 
 <tr>
    <td height="26"  align="center" bgcolor=#F8FAFE width="135">用 户 名:</td>
    <td height="26"  align="center" bgcolor=#F8FAFE width="213"><?php echo $LoginUserName;?></td>
 </tr>
 <tr>
    <td height="49" colspan="2" bgcolor="#E9EFFC"  valign="middle" align="center">
    <form method="post" style="margin:0px" action="<?php echo $payaction;?>" onsubmit="this.subtn.disabled=true;" target="_top">
    <input name="pay_subject" type="hidden" value="用户交易账户充值">
    <input name="pay_tradeno" type="hidden" value="<?php echo $pay_tradeno;?>">
    <input name="pay_amount" type="hidden" value="<?php echo $pay_amount;?>">
    <input name="pay_remark" type="hidden" value="<?php echo $LoginUserName;?>">
    <!--input type="image" src="<?php echo  $paylogo;?>" align="absmiddle"-->
    <input name="subtn" type="submit"  value="确定支付"></form></td>
 </tr>
 </table>
 <table width="500"  border="0" align="center" cellpadding="3" cellspacing="1" ><tr><td align="right" valign="top"><b><font color="#FF6600">注意：</font></b></td><td style="color:#084B8F">
（1）为保证支付成功，请在支付前，关闭所有屏蔽浏览器弹出窗口的插件；<br>
（2）支付完成后，请不要急于关闭支付页面，系统会自动跳转回到本商城；<br>
（3）支付成功后，若资金没有自动注入预存款账户，请联系客服手动入账！<br></td></tr></table>
 </td></tr></table><?php  
}
 
function receiveaddr(){
 global $conn,$LoginUserID;
 check_user();
 $row=$conn->query('select * from `mg_users` where id='.$LoginUserID,PDO::FETCH_ASSOC)->fetch();?>
<form onsubmit="save_receiver_address(this);return false;" style="margin:0px">
<table width="96%" border=0 align=center cellpadding=4 cellspacing=1>
<tr><td height="40" colspan="4" background="<?php echo WEB_ROOT;?>images/kubars/kubar_recv.gif" align="right"><img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; 会员中心 &gt;&gt; 收货人信息</td></tr>
<tr bgcolor=#FFFFFF height=28>
<td width=80 bgcolor="#F7F7F7" >收货人姓名：</td>
<td bgcolor="#FFFFFF"><input name=receipt type=text class="input_sr" id=receipt value="<?php echo $row['receipt'];?>" size=12 maxlength="16"> 
</td>
</tr>
<tr height=28 bgcolor=#FFFFFF>
<td height=16 bgcolor=#F7F7F7 >收货人地址：</td>
<td bgcolor="#FFFFFF"><input name=address type=text class="input_sr" id=address value="<?php echo $row['address'];?>" size=40 maxlength="100"></td>
</tr>
<tr height=28 bgcolor=#FFFFFF>
<td bgcolor="#F7F7F7" >联系电话：</td>
<td bgcolor="#FFFFFF"><input name=usertel type=text class="input_sr" id=usertel value="<?php echo $row['usertel'];?>" size=40 maxlength="50"> 注：收货人联系电话，可以填多个号码，中间用空格隔开！</td>
</td>
</tr>
<tr bgcolor=#FFFFFF align="right">
<td height=32 colspan=2 ><hr><input type="submit" class="input_bot" value=提交保存></td></tr>
</table>
</form><?php
}
 
function changepass(){
 global $conn,$LoginUserID;
 check_user();
 $UserQuestion=$conn->query('select userquestion from `mg_users` where id='.$LoginUserID)->fetchColumn(0);?>
<table width="96%" border="0" align="center" cellpadding=0 cellspacing=0 bgcolor="#F6F6F6">
<tr><td height="40" colspan="2" background="<?php echo WEB_ROOT;?>images/kubars/kubar_changepsw.gif" align="right"><img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; 会员中心 &gt;&gt; 密码安全</td></tr>	
<tr><td align="center"><b>修改密码</b></td><td align="center"><b>修改提示问题</b></td></tr>
<tr>
   <td width="50%" align="center">
<form style="margin:0px" onsubmit="check_changepsw(this);return false;">
<table width="250" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#F6F6F6">
<tr>
  <td height="25" align="right">原 密 码：</td>
  <td height="25"><input name="userpassword" type="password" maxlength="16"></td>
</tr>
<tr>
  <td height="25" align="right">新 密 码：</td>
  <td height="25" nowarp><input name="userpassword1" type="password"  maxlength="16"></td>
</tr>
<tr>
  <td height="25" align="right">确认密码：</td>
  <td height="25" nowarp><input name="userpassword2" type="password"  maxlength="16"></td>
</tr>
<tr>
  <td height="25" colspan=2 align="right"><input type="submit" value="确认修改">&nbsp;&nbsp;</td>
</tr>
</table>
</form></td>
  <td>
<form style="margin:0px" onsubmit="check_changequestion(this);return false;">
<table width="250" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#F6F6F6">
<tr>
  <td height="25" align="right">原 密 码：</td>
  <td height="25"><input name="userpassword" type="password" maxlength="16"></td>
</tr>
<tr>
  <td height="25" align="right">密码提问：</td>
  <td height="25" nowarp><input name="userquestion" type="text"  value="<?php echo $UserQuestion;?>" maxlength="16"></td>
</tr>
<tr>
  <td height="25" align="right">问题答案：</td>
  <td height="25" nowarp><input name="useranswer" type="text"  maxlength="32"></td>
</tr>
<tr>
  <td height="25" colspan=2 align="right"><input type="submit" value="确认修改">&nbsp;&nbsp;</td>
</tr>
</table>
</form>  	
  </td>
</tr>
</table>
<br><?php
}

function resetpsw(){?>
<table width="96%" border=0cellpadding=0 cellspacing=0 align="center">
<tr><td height="40" background="<?php echo WEB_ROOT;?>images/kubars/kubar_resetpsw.gif" align="right"><img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; 会员中心 &gt;&gt; 找回密码</td></tr>
<tr><td><?php
  global $conn;
  $state=@$_GET['state'];
  if($state=='1'){
   $objUserName=FilterText(trim($_POST['username']));
   $row=$conn->query('select userquestion,useranswer from `mg_users` where username=\''.$objUserName.'\'',PDO::FETCH_ASSOC)->fetch();
   if(empty($row)){
     ob_clean();
     echo '此用户名不存在！';
     return false;
   }
   else{?>
    <form onsubmit="Check_PswQuestion(this.useranswer.value.trim(),'<?php echo $objUserName;?>');return false;" style="margin:0px">
    <table width="350" border=0 align="center" cellpadding=1 cellspacing=1 bgcolor="#FFFFFF">
    <tr><td width="20%" nowrap bgcolor="#ffffff" STYLE="PADDING-LEFT: 20px">您的密码提问：</td><td width="80%" height=28 bgcolor="#ffffff">&nbsp; <font color=red><?php echo $row['userquestion'];?></font></td></tr>
    <tr><td bgcolor=#ffffff STYLE="PADDING-LEFT: 20px">您的密码答案：</td><td height=28 bgcolor="#ffffff" nowrap>&nbsp; <input name="useranswer" type="text"  maxlength=16 class="input_sr"></td></tr>
    <tr bgcolor=#ffffff><td height=32 colspan=2 STYLE="PADDING-LEFT: 50px" align="right"><input class="input_bot" type="submit" value="确 定"></td></tr>
    </table></form><?php
   } 
  }
  else if($state=='2'){#/market//输入新密码
    $UserAnswer=FilterText(trim($_POST['useranswer']));
    $UserName=FilterText(trim($_POST['username']));
    $userid=$conn->query("select id from `mg_users` where username='$UserName' and useranswer=md5('$UserAnswer')")->fetchColumn();
    if(empty($userid)){
       ob_clean();
       echo '对不起，您输入的问题答案不正确！';
       return false;
    }
    else{?>
    <form onsubmit="Resetpsw_ModifyPsw(this);return false;" style="margin:0px">
    <table width="350" border=0 align="center" cellpadding=0 cellspacing=0 bgcolor="#f2f2f2">
    <tr><td width=20% bgcolor="#f2f2f2" nowrap>&nbsp;请输入新密码：</td><td width="80%" height=28 bgcolor=#ffffff>&nbsp; <input name="userpassword1" class="input_sr" type="password"><input type="hidden" name="username" value="<?php echo $UserName;?>"></td></tr>
    <tr><td bgcolor="#f2f2f2">&nbsp;输入确认密码：</td><td height=28 bgcolor="#ffffff">&nbsp; <input class="input_sr" name="userpassword2" type="password"></td></tr>
    <tr><td height=32 colspan=2 bgcolor="#ffffff" align="right"><input type="hidden" name="useranswer" value="<?php echo $UserAnswer;?>"><input type="submit" class="input_bot" value="确 定"></td></tr>
    </table></form><?php
    }
  }
  else{?>
    <form onsubmit="check_if_user_exist(this.username.value.trim());return false;" style="margin:0px">
    <table width="350" border=0 cellpadding=01 cellspacing=1 align="center">
    <tr>
      <td height=80 bgcolor="#ffffff"><div align=center>请输入您的用户名： <input name="username" type="text" size=16 class="input_sr"></div></td>
    </tr>
    <tr>
      <td height=32 bgcolor="#ffffff"  align="right"><input type="submit" class="input_bot" value="下一步"></td>
    </tr>
    </table></form><?php
  }?>
 </td></tr></table><?php
}

function myorders(){
  global $conn,$LoginUserID,$total_pages,$total_records,$page;
  check_user();?>
<table border="0" cellpadding="0" cellspacing="0" align="center" width="96%" >
<tr><td height="40" background="<?php echo WEB_ROOT;?>images/kubars/kubar_myorders.gif" align="right"><img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; 会员中心 &gt;&gt; 我的订单</td></tr>
<tr>
  <td align="center"><?php
  $res=page_query('select `mg_orders`.ordername,`mg_orders`.totalprice,`mg_orders`.actiontime,`mg_orders`.deliverymethod,`mg_orders`.paymethod,`mg_orders`.state','from `mg_orders` inner join `mg_users` on `mg_orders`.username=`mg_users`.username','where `mg_users`.id='.$LoginUserID.' and `mg_orders`.state>0','order by `mg_orders`.actiontime desc',15);
  if(empty($res)){
    echo '<br><br><p align=center>没有订单！</p><br><br>';
  }
  else{?> 
    <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#f2f2f2" >
    <tr align="center" bgcolor="#F7F7F7"> 
       <td height="25"><strong>订单号</strong></td>
       <td height="25"><strong>合计金额</strong></td>
       <td height="25"><strong>付款方式</strong></td>
       <td height="25"><strong>配送方式</strong></td>
       <td height="25"><strong>下单/发货时间</strong></td>
       <td height="25"><strong>订单状态</strong></td>
    </tr><?php
    foreach($res as $row){?>
    <tr bgcolor="#FFFFFF" align="center"  style="cursor:pointer" onmouseover="this.style.backgroundColor='#f2f2f2'; this.style.color='#ff0000' " onmouseout="this.style.backgroundColor='';this.style.color=''"> 
        <td><a href="<?php echo WEB_ROOT.'user/myorder.php?ordername='.$row['ordername'];?>"><?php echo $row['ordername'];?></a></td>
        <td><font color="#FF6600"><?php echo $row['totalprice'];?>元</font></td>
        <td><?php echo $row['paymethod'];?></td>
        <td><?php echo $row['deliverymethod'];?></td>
        <td><?php echo $row['actiontime'];?></td>
        <td><?php switch($row['state']){
            case '1': echo '<font color=#000000>己提交§等待审核</font>';break;
            case '2': echo '<font color=#FF0000>己审核§正在配货</font>';break;
            case '3': echo '<font color=#8800FF>己配货§正在发货</font>';break;
            case '4': echo '<font color=#0000aa>已发货§正在结算</font>';break;
            case '5': echo '<font color=#00aaaa>已发货§等待确认</font>';break;
            case '6': echo '<font color=#00aa00>已签收§交易完成</font>';break;
            case '8': echo '<font color=#00aa00>已签收§交易完成</font>';break;
        }?>
        </td></tr><?php
    } 
    if($total_pages>1){
      echo '<tr><td align="center" height="20" colspan=6><form style="margin:0px">共<b><font color="#FF0000">'.$total_records.'</font></b>个订单&nbsp;&nbsp;';
      if($page==1) echo '首页&nbsp;&nbsp;上一页';
      else echo '<a href="#" onclick="return show_myorders(1)" target="_self">首页</a>&nbsp;&nbsp;<a href="#" onclick="return show_myorders('.($page-1).')" target="_self">上一页</a>';
      echo '&nbsp;&nbsp;';
      if($page==$total_pages) echo '下一页&nbsp;&nbsp;尾页';
      else echo '<a href="#" onclick="return show_myorders('.($page+1).')" target="_self">下一页</a>&nbsp;&nbsp;<a href="#" onclick="return show_myorders('.$total_pages.')" target="_self">尾页</a>';
      echo '&nbsp;&nbsp;页次：<strong><font color="red">'.$page.'</font>/'.$total_pages.'</strong>页&nbsp;&nbsp; 转到第<input type="text" name="page" value="'.$page.'" size=3 maxlength=8 style="text-align:center" onFocus="this.select()" onkeyup="if(isNaN(value))execCommand(\'undo\')" onkeydown="if(window.event.keyCode==13){this.form.jumpbtn.click();return false;}">页 &nbsp;<input type="button" name="jumpbtn" value="跳转" onclick="show_myorders(this.form.page.value);"></form></td></tr>';
    }
    echo '</table>';
  }
  echo '</td></tr></table>';
}

function  AccountLog(){
  global $conn,$LoginUserID,$total_pages,$total_records,$page;
  check_user();?>
<table border="0" cellpadding="0" cellspacing="0" align="center" width="96%" >
<tr><td height="40" colspan="2" background="<?php echo WEB_ROOT;?>images/kubars/kubar_details.gif" align="right"><img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; 会员中心 &gt;&gt; 账务明细</td></tr>
<tr>
  <td align="center"><?php 
   $res=page_query('select `mg_accountlog`.*','from `mg_accountlog` inner join `mg_users` on `mg_accountlog`.username=`mg_users`.username','where `mg_users`.id='.$LoginUserID.' and `mg_accountlog`.operation=2','order by `mg_accountlog`.actiontime desc',15);
   if(empty($total_records)){
     echo '<br><br><p align=center>没找到相关记录！</p><br><br>';
   }
   else{?> 
    <table width="100%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#f2f2f2" >
    <tr align="center" bgcolor="#F7F7F7"> 
       <td height="25" width="20%"><strong>日期</strong></td>
       <td height="25" width="15%"><strong>支出/存入</strong></td>
       <td height="25" width="15%"><strong>余额</strong></td>
       <td height="25" width="50%"><strong>说明</strong></td>
    </tr><?php
    foreach($res as $row){
      echo '<tr bgcolor="#FFFFFF" align="center" onmouseover="this.style.backgroundColor=\'#f2f2f2\'; this.style.color=\'#ff0000\';" onmouseout="this.style.backgroundColor=\'\';this.style.color=\'\';" >';
      echo '<td>'.date('Y-m-d H:i',$row['actiontime']).'</td><td>';
      if($row['amount']>0)echo '<font color="#FF0000">＋</font>'.round($row['amount'],2).'元';
      else echo '<font color="#FF0000">－</font>'.round(-$row['amount'],2).'元';
      echo '<td>'.round($row['surplus'],2).'</td><td><input type="text" value="';
      if($row['remark']) echo $row['remark'];
      echo '" style="width:360px;background-color:transparent;border:0px solid #CCCCCC;text-align:center;font-size:8pt" readOnly></td></tr>';
    } 
    if($total_pages>1){
      echo '<tr><td align="center" height="20" colspan="4"><form style="margin:0px">共<b><font color=#FF0000>'.$total_records.'</font></b>条记录&nbsp;&nbsp;';
      if($page==1) echo '首页&nbsp;&nbsp;上一页';
      else echo '<a href="#" onclick="return show_accountlog(1)">首页</a>&nbsp;&nbsp;<a href="#" onclick="return show_accountlog('.($page-1).')" >上一页</a>';
      echo '&nbsp;&nbsp;';
      if($page==$total_pages) echo '下一页&nbsp;&nbsp;尾页';
      else echo '<a href="#" onclick="return show_accountlog('.($page+1).')">下一页</a>&nbsp;&nbsp;<a href="#" onclick="return show_accountlog('.$total_pages.')">尾页</a>';
      echo '&nbsp;&nbsp;页次：<strong><font color="red">'.$page.'</font>/'.$total_pages.'</strong>页&nbsp;&nbsp;';
      echo '转到第<input type="text" name="page" value="'.$page.'" size=3 maxlength=8 style="text-align:center" onFocus="this.select()" onkeyup="if(isNaN(value))execCommand(\'undo\')"  onkeydown="if(window.event.keyCode==13){this.form.jumpbtn.click();return false;}">页
  &nbsp;<input type="button" name="jumpbtn" value="跳转" onclick="show_accountlog(this.form.page.value)"></form></td></tr>';
    }
    echo '</table>';
   }
   echo '</td></tr></table><br>';
}

function onlinepay(){
  global $conn;
  $AlipayMail=$conn->query('select alipaymail from `mg_configs`')->fetchColumn(0);?>
  <table width="770"  border="0" align="center" cellpadding="3" cellspacing="1">
  <tr><td height="40" background="<?php echo WEB_ROOT;?>images/kubars/kubar_pay.gif" align="right"><img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; 会员中心 &gt;&gt; 在线支付</td></tr>  	
  <tr>
    <td  bgcolor="#FFFFFF"><ul class="style1">
      <li>欢迎使用在线支付系统，您可以在此完成您的订单支付，或者是为自己的预存款充值。</li>
      <li> 如果您此次操作在公共场合，请您保护好自己的支付安全，本公司不推荐您在诸如网吧的地点进行支付操作。</li>
      </ul>
    </td>
  </tr>
  <tr>
     <td align="center" bgcolor="#f7f7f7">
      	<table border=0 cellspacing=0 cellpadding=0 width=390><tr><td><?php OnlinepayEntry();?></td></tr>
      	<tr><td>（1）充值成功后，资金将立即注入您在本站的会员账户。您随时都可以利用该账户的资金来订购我们提供的任何产品。&nbsp;&nbsp;</td></tr>
      	<tr><td>（2）如果使用支付宝支付，请务必通过我司网站上的支付接口，<!--并选择即时到账交易，-->否则资金不能即时充值到您在我司网站注册的会员账户上！如果您不打算通过我司网站上的支付接口进行支付，请您登录支付宝，然后在支付宝的[我要付款]里进行即时到账支付，我司支付宝收款账号:  <font color="#FF0000"><?php echo $AlipayMail;?></font>，支付完毕后，请及时联系本站客服手动入账。</td></tr>
        </table>
     </td>
  </tr>
  <tr>
     <td align="center" style="font-weight:bold;font-size:11pt;color:#FF0000">▲▲如果您没有网上银行账户，请<a href="/help/help3.htm"><font color="#0000FF"><u>点击这里</u></font></a>查看其他付款方式(银行汇款)<br></td>
  </tr>
  </table><?php
}

function customerinfo(){
  global $conn,$LoginUserID;
  check_user();
  $row=$conn->query('select `mg_users`.username,`mg_users`.usersex,`mg_users`.usermail,`mg_users`.usermobile,`mg_users`.userqq,`mg_users`.userquestion,`mg_users`.realname,`mg_users`.district,`mg_usrgrade`.title from `mg_users`,`mg_usrgrade` where `mg_users`.id='.$LoginUserID.' and `mg_users`.grade=`mg_usrgrade`.id',PDO::FETCH_ASSOC)->fetch();?>
<form style="margin:0px" name="userinfo" method=post action="<?php echo WEB_ROOT;?>user/saveuserinfo.php?action=customerinfo" onsubmit="return Check_UserStuff(this);" >
<table width="96%" align=center cellpadding=0 cellspacing=0 border="0">
<tr><td height="40" colspan="2" background="<?php echo WEB_ROOT;?>images/kubars/kubar_userinfo.gif" align="right"><img src="images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#">首页</a> &gt;&gt; 会员中心 &gt;&gt; 个人资料</td></tr>
<tr >
  <td width="100%" height=25 colspan=2>&nbsp;&nbsp;用 户 名： <font color=#FF6600><?php echo $row['username'];?> &nbsp;(<b><?php echo $row['title'];?></b>)</font></td>
</tr>
<tr>
  <td>&nbsp;&nbsp;真实姓名： <input name="realname" type=text class="input_sr" value="<?php echo $row['realname'];?>" maxlength="16"></td>
  <td width="55%">&nbsp;&nbsp;用户性别：<input type="radio" value="0" name="usersex" <?php if($row['usersex']=='0') echo 'checked';?>>男<input type="radio" value="1" name="usersex"  <?php if($row['usersex']=='1') echo 'checked';?>>女</td>
</tr>
<tr>
  <td>&nbsp;&nbsp;电子邮箱：  <input name="usermail" type=text class="input_sr" value="<?php echo $row['usermail'];?>" maxlength="50"></td>
  <td height=25>&nbsp;&nbsp;所在地区：&nbsp;<select name="provincelist" size="1"><option value="0" selected>请选择省份……</option></select><select size="1" name="district"><option value='<?php echo $row['district'];?>'>请选择城市……</option></select></td>
</tr>
<tr>
  <td>&nbsp;&nbsp;腾讯QQ号：  <input name="userqq" type=text class="input_sr" value="<?php echo $row['userqq'];?>" maxlength="50"></td>
  <td height=25>&nbsp;&nbsp;联系电话：  <input name="usermobile" type=text class="input_sr" value="<?php echo $row['usermobile'];?>" maxlength="50"> *多个电话加空格!</td>
</tr>  
<tr align="right">
   <td height=25 colspan="2"><hr><input type="submit" class="input_bot" value="提交保存"></td>
</tr>
</table>
</form><?php
}

?>
