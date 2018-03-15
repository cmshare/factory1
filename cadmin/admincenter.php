<?php
function refer_to_home(){
  $referer=@$_SERVER['HTTP_REFERER'];
  if($referer){
    $referer=strrchr($referer,'/');
    if($referer!='/' && strpos($referer,'index.php')===false && strpos($referer,'/?')===false)return true;
  }
  return false;
}
if(!refer_to_home()){
   $homepath=@$_COOKIE['meray']['home'];
   if($homepath && $homepath!=$_SERVER['REQUEST_URI']){
     header('Location:'.$homepath);
     exit(0);//header之后会继续往下执行,这里要手动终止.
   }
}

include('includes/dbconn.php');
CheckLogin();
OpenDB();

$BeginDate=@$_GET['begindate'];
$EndDate=@$_GET['enddate'];
if(empty($BeginDate) || !($BeginDate=strtotime($BeginDate)))$BeginDate=strtotime(date('Y-m').'-1');
if(empty($EndDate) || !($EndDate=strtotime($EndDate)))$EndDate=time();

$Own_popedomManage=CheckPopedom('MANAGE');
if($Own_popedomManage && ($Operator=trim(@$_GET['operator']))) $Operator=FilterText($Operator);
else $Operator=$AdminUsername;	 

$where="where actiontime>$BeginDate and actiontime<$EndDate";
if($Operator!='所有人员') $where.=" and operator='$Operator'";
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<meta http-equiv="Refresh" content="300;URL=<?php echo $_SERVER['SCRIPT_NAME'];?>">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>管理中心首页</title>
<script type="text/JavaScript">
function UpdateTimeDisplay()
{ var timeDisp=new Date().toLocaleString();
  if(timeDisp.indexOf("星期")<0)
  { timeDisp=timeDisp+' 星期'+'日一二三四五六'.charAt(new Date().getDay()); 
  }
  webjx.innerHTML=timeDisp;
}

function GetNewDate(baseDate,dayoffset){
  var newdate=new Date(baseDate); 
  newdate.setDate(baseDate.getDate()+dayoffset); 
  return newdate.getFullYear()+"-"+(newdate.getMonth()+1)+"-"+newdate.getDate();
}

function MyStat(mode){
  var myform=document.forms['MyStatForm'];
  var DayOffset,NextDate,TodayDate = new Date();
  if(mode==1 || mode==2){ /*本周统计 or 上周统计*/
    if(mode==2) TodayDate.setDate(TodayDate.getDate()-7);
    DayOffset=TodayDate.getDay();
    myform.begindate.value=GetNewDate(TodayDate,-DayOffset);
    myform.enddate.value=GetNewDate(TodayDate,7-DayOffset);	 
  }
  else if(mode==3 ||mode==4){/*本月统计 or 上月统计*/
    TodayDate.setDate(1);
    NextDate=new Date(TodayDate);
    if(mode==3)NextDate.setMonth(NextDate.getMonth()+1); 
    else TodayDate.setMonth(NextDate.getMonth()-1); 
    myform.begindate.value=GetNewDate(TodayDate,0);
    myform.enddate.value=GetNewDate(NextDate,0);	
  }		
  myform.submit();
}   
if(self==top)self.location.href=".";
</script>	
</head>
<body>
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="20" background="images/topbg.gif">
      <table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
     	  <tr>
     	  	<td nowrap><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" /><a href="admincenter.php"><font color=#000000>管理中心首页</font></a></b></td>
     	  	<td align="center"><font color=#0000>☆★在公共场合请注意密码安全，使用完毕后请一定安全退出！！！★☆</font></td>
     	    <td align="right" nowrap><div id="webjx"></div></td>
     	    <script>setInterval("UpdateTimeDisplay();",1000);</script>
     	  </tr>
     	  </table>	
    </td>
  </tr>
  <tr> 
    <td bgcolor="#FFFFFF" valign="top"><?php
 $MyTask='';

 $value=$conn->query('select count(*) from mg_accountlog where operation>=4 and operation<=6')->fetchColumn(0);
 if($value===false)$value=0;
 $MyTask.='<a href="mg_accountlog.php?mode=8">待审款<font color=#FF0000><b>'.$value.'</b></font>项</a> | ';


 $value=$conn->query('select count(*) from mg_users where deposit<-10 and username<>\'junhang\'')->fetchColumn(0);
 if($value===false)$value=0;
 $MyTask.='<a href="mg_usrdebt.php">欠款<font color=#FF0000><b>'.$value.'</b></font>笔</a> | ';
		        
 if(CheckPopedom('INFOMATION')){
   $value=$conn->query('select count(*) from mg_review where audit=0 and productid>0')->fetchColumn(0);
   if($value===false)$value=0;
   $MyTask.='<a href="mg_remarks.php">未审评论<font color=#FF0000><b>'.$value.'</b></font>条</a> | ';
 }
			
 //$value=$conn->query('select count(*) from mg_book where re is null and addtime>unix_timestamp()-7*24*60*60')->fetchColumn(0);
 //if($value!==false) $MyTask.='<a href="mg_guestbook.php">未复留言<font color=#FF0000><b>'.$value.'</b></font>条</a> | ';
	 
 $msg_filter=@$_COOKIE[$AdminUsername]['msgread'].@$_COOKIE[$AdminUsername]['msgdelete'];
 if($msg_filter) $sql='(sendto=\'all\' and id not in ('.FilterText($msg_filter).'))';
 else $sql='sendto=\'all\'';
 if($Own_popedomManage)$sql='select count(*) from mg_message where property=1 and (sendto=\''.$AdminUsername.'\' or sendto=\'adm\' or '.$sql.')';
 else $sql='select count(*) from mg_message where property=1 and (sendto=\''.$AdminUsername.'\' or '.$sql.')';
 $value=$conn->query($sql)->fetchColumn(0);
 if($value===false)$value=0;
 $MyTask.='<a href="mg_usrmsg.php">未处理信息<font color=#FF0000><b>'.$value.'</b></font>条</a>';
 if($value>0)$MyTask.='<img src="images/mail.gif" align="absMiddle" width="13" height="13">';

 if($MyTask){?>
   <table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
   <tr height="25">
      <td nowrap width="99%" background="images/topbg.gif">&nbsp;<font color=#0000FF><b>您好</b></font><font color="#FF6600"><?php echo $AdminUsername;?></font>，今天尚有<?php echo $MyTask;?></td>
      <td nowrap background="images/topbg.gif" align="right" style="padding-left:3px;padding-right:3px;"><?php
         $signin=$conn->query('select min(addtime) from mg_logs where addtime>unix_timestamp(curdate()) and username=\''.$AdminUsername.'\'')->fetchColumn(0);
         echo '<a href="mg_logs.php">今日签到时间<font color=#FF0000><b>'.date('H:i:s',$signin).'</b></font></a>';?>
      </td>
    </tr>
    </table><?php
  }?>

<table style="margin-top:5px" width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
<tr>
  <td background="images/topbg.gif" colspan="4">
    <form method="get" name="MyStatForm" style="margin:0px">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" height="20">
    <tr> 	
      <td>
     	 <table border="0" cellpadding="0" cellspacing="0" height=22>
    	 <tr>
           <td>&nbsp;<input  name="begindate" type="text" value="<?php echo date('Y-m-d',$BeginDate);?>" style="width:70px;height:16px;font-size:10pt;color:#FF0000;text-align:center;border: 0px solid #CCCCCC;background-color:transparent">至<input name="enddate" type="text" value="<?php echo date('Y-m-d',$EndDate);?>" style="width:70px;height:16px;font-size:10pt;color:#FF0000;text-align:center;border: 0px solid #CCCCCC;background-color:transparent">期间，</td>
           <td><?php
    	     if($Own_popedomManage){?> 
    		<script>
     		function ShowOperator(onoff){
                  if(onoff){
                    DummyOperator.style.display='none';
    		    MyStatForm.operator.style.display='block';
    	            MyStatForm.operator.focus();
    	          }
    	          else{
                    MyStatForm.operator.style.display='none';
    		    DummyOperator.style.display='block';
    		  }
    		}
    	        </script><span id="DummyOperator" onclick="ShowOperator(true)" style="color:#FF0000;padding-left:5px;padding-right:5px"><?php echo $Operator;?></span></td>
      	      <td><select name="operator" onblur="ShowOperator(false)" onchange="DummyOperator.innerText=this.value;ShowOperator(false);" style="display:none;"><option value="所有人员">所有人员</option><?php

      	      $res=$conn->query('select username from mg_admins where idverified',PDO::FETCH_NUM);
      	      foreach($res as $row){
      	        if($row[0]==$Operator) echo '<option value="'.$row[0].'" selected>'.$row[0].'</option>';
      	        else echo '<option value="'.$row[0].'">'.$row[0].'</option>';
              }
              echo '</select>';
            }
      	    else echo '您';?></td><td>的销售状况:</td>
      	  </tr>
          </table>
       </td>
       <td align="right"><input type="button" value="重新统计" onclick="MyStat(0)"><input type="button" value="本周统计" onclick="MyStat(1)"><input type="button" value="上周统计" onclick="MyStat(2)"><input type="button" value="本月统计" onclick="MyStat(3)"><input type="button" value="上月统计" onclick="MyStat(4)"></td>
     </tr>
     </table></form>
  </td>
</tr>
<tr bgcolor="#F7F7F7" align="center"> 
   <td height="25" width="25%" background="images/topbg.gif"><strong>订单号</strong></td>
   <td height="25" width="25%" background="images/topbg.gif"><strong>金额</strong></td>
   <td height="25" width="25%" background="images/topbg.gif"><strong>下单用户</strong></td>
   <td height="25" width="25%" background="images/topbg.gif"><strong>订单状态</strong></td>
</tr>
<?php
$SplitterFlag=2;
$res=page_query('select ordername,username,state,totalprice,deliveryfee','from mg_orders',$where.' and state>0','order by sign(state-4) asc,actiontime desc',20);
if($total_records>0){
  foreach($res as $row){
    if($SplitterFlag==2) $SplitterFlag=($row['state']<4)?1:0;
    else if($SplitterFlag==1){
      if($row['state']>=4){
        echo '<tr height="3" ><td colspan="4" bgcolor="#FFFFFF"></td></tr>';
        $SplitterFlag=0;
      } 
    }
    echo '<tr height="25" bgcolor="#FFFFFF" align="center" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
          <td background="images/topbg.gif"><a href="mg_checkorder.php?ordername='.$row['ordername'].'">'.$row['ordername'].'</a></td>
          <td>'.FormatPrice($row['totalprice']-$row['deliveryfee']).'</td>
          <td><a href="mg_usrinfo.php?user='.urlencode($row['username']).'">'.$row['username'].'</a></td>
          <td>';
            switch($row['state']){
              case '1': echo '未作任何处理';break;
              case '2': echo '<font color=#FF0000><I>正在进行处理</I></font>';break;
              case '3': echo '<font color=#8800FF>已配货待发货</font>';break;
              case '4': echo '<font color=#0000aa><b>已发货待收款</b></font>';break;
              case '5': echo '<font color=#00aaaa>已收款待确认</font>';break;
            }
     echo '</td></tr>';
   }
   $TotalSum=$conn->query('select sum(totalprice-deliveryfee) from mg_orders '.$where.' and state>=4')->fetchColumn(0);
   if($TotalSum===false)$TotalSum=0;
   echo '<tr><td colspan="4" height="30" bgcolor="#FFFFFF" valign="middle" align="center">统计范围内共<font color="#FF0000">'.$total_records.'</font>笔订单,&nbsp;交易总额<font color="#FF0000">'.FormatPrice($TotalSum).'</font>元 &nbsp; &nbsp; <script language="javascript">GeneratePageGuider("operator='.$Operator.'&beginDate='.$BeginDate.'&enddate='.$EndDate.'",'.$total_records.','.$page.','.$total_pages.');</script></td></tr>';
}
else echo '<tr><td colspan=4 align="center">统计范围内共<font color="#FF0000">0</font>笔订单</td></tr>';
?>
  
   </td>
  </tr>
</table>
</body>
</html><?php CloseDB();?>
