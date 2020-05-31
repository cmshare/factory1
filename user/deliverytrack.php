<?php 

require('../include/conn.php');
$DeliveryMethod=@$_GET['method'];
$DeliveryCode=@$_GET['code'];
?>


<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<META http-equiv="Keywords" content="快递,物流,追踪,货单号,查询,化妆品运输,化妆品批发">
<META http-equiv="Description" content="本页主要列举常用的快递物流,并介绍化妆品运输注意事项,以及包裹查询相关内容">
<title>快递物流追踪查询,化妆品运输批发业务</title>
<body>
<script>
function DoJump()
{ document.forms[0].submit()
}
function JumpToSearch()
{ document.write("<br><br><p align=center><font color=#FF0000 size=3>请稍候，正在自动接入查询页面...<span id='mycounter'> </span></font></p>");
  document.write("<br><p align=center><font size=2>如果长时间没有响应，请点击下面快递公司网站，手动查询！</font></p>");
  document.write("<table align=center width=100 border=1  cellpadding=0 cellspacing=0><tr><td nowrap><a href='?method=<?php echo $DeliveryMethod;?>' style='font-size:11pt;line-height: 130%;TEXT-DECORATION:none;color:#000000'><b>承运单位：</b><font color=#0000FF><u><?php echo $DeliveryMethod;?></u></font><br><b>货运单号：</b><font color=#FF6600><?php echo $DeliveryCode;?></font></a></td></tr></table>");
  window.onload=DoJump;
} 

var DownCounter=30; /*30秒倒计时*/
function StartCounter(counter)
{ DownCounter=counter;
  if(DownCounter>0)
  { var obj=document.getElementById("mycounter");
    if(obj)obj.innerHTML=DownCounter;
  }else DownCounter=30;
}
setInterval("StartCounter(DownCounter-1)",1000); 
</script><?php
db_open();
if(empty($DeliveryCode)){
  if($DeliveryMethod){
      $weburl=$conn->query('select website from `mg_delivery` where methord=2 and subject=\''.$DeliveryMethod.'\'')->fetchColumn(0);
      if($weburl){
        db_close();
        header('location:'.$weburl);
        exit(0);
      }
  }
  else{
    echo '<table width="10%" border=0 align="center" cellpadding=0 cellspacing=0><tr><td>';
    $res=$conn->query('select subject,website from `mg_delivery` where methord=2 and website<>\'\' order by deliveryidorder',PDO::FETCH_NUM);
    foreach($res as $row){
      echo '<tr><td nowrap height=25><a href="'.$row[1].'">'.$row[0].'</a></td></tr>';
    }
    echo '</table>';
  }
}
else if(strstr($DeliveryMethod,'申通')) {?>
  <form action="http://www.kiees.cn/sto/" method=post>
  <input name="wen" type="hidden" value="<?php echo $DeliveryCode;?>"> 
  </FORM><script>JumpToSearch()</script><?php 
}
else if(strstr($DeliveryMethod,'中通')){?>  	
  <FORM action="http://www.kiees.cn/zto/" method=post>
  <input name="wen" type="hidden" value="<?php echo $DeliveryCode;?>"> 
  </FORM><script>JumpToSearch()</script><?php
}
else if(strstr($DeliveryMethod,'全峰')){?>
 <FORM method="POST" action="http://60.191.136.251:7080/PublicInterface/showInfo.do">
  <input name="billcode" type="hidden" value="<?php echo $DeliveryCode;?>">
  </FORM><script>JumpToSearch()</script><?php
}
else if(strstr($DeliveryMethod,'韵达')){?>
  <form action="http://www.kiees.cn/yd/" method="post">
  <INPUT name="wen" id="wen" type="hidden" value="<?php echo $DeliveryCode;?>"> 
  </FORM><script>JumpToSearch()</script><?php
}
else if(strstr($DeliveryMethod,'韵_达')){?>
  <form action="http://www.kiees.cn/yd.php" method="post">
  <INPUT name="wen" id="No" type="hidden" value="<?php echo $DeliveryCode;?>"> 
  </FORM><script>JumpToSearch()</script><?php
}
else if(strstr($DeliveryMethod,'圆通')){?>
 <FORM method="POST" action="http://www.kiees.cn/yto/">
  <input name="wen" type="hidden" value="<?php echo $DeliveryCode;?>">
  </FORM><script>JumpToSearch()</script><?php
}
else if(strstr($DeliveryMethod,'国通')){?>
  <FORM method="POST" action="http://www.kiees.cn/gto365/">
  <input type="hidden" name="wen" value="<?php echo $DeliveryCode;?>">
  </FORM><script>JumpToSearch()</script><?php
}
else if(strstr($DeliveryMethod,'中诚')){?>
  <FORM method="POST" action="http://222.73.237.73/Express_WEBSite/cha.asp">
  <input name="JobNo" type="hidden" value="<?php echo $DeliveryCode;?>">
  </FORM><script>JumpToSearch()</script><?php
}
else if(strstr($DeliveryMethod,'邮政') or InStr(DeliveryMethod,'EMS')){?>  	  	
  <form action="http://my.kiees.cn/ems/" method="post">
  <INPUT name="wen" type="hidden" value="<?php echo $DeliveryCode;?>"> 
  </FORM><script>JumpToSearch()</script><?php
}  
else if(strstr($DeliveryMethod,'顺丰')){?>  
  <FORM action="http://www.kiees.cn/sf/" method=post>
  <input name="wen" type="hidden" value="<?php echo $DeliveryCode;?>"> 
  </FORM><script>JumpToSearch()</script><?php
}  
else if(strstr($DeliveryMethod,'传志')){?>  
  <form action="http://www.chuanzhi.cn/ydcx_ok.asp" method="post">
  <INPUT  type="hidden"  NAME="serverip" VALUE="http://218.81.169.174/cz/search.asp">
  <input name="id" type="hidden" value="<?php echo $DeliveryCode;?>"> 
  </FORM><script>JumpToSearch()</script><?php
} 
else if(strstr($DeliveryMethod,'汇通')){?>  
  <form action="http://www.htky365.com/track.do" method="post">
  <INPUT  type="hidden"  NAME="inputNumber" value="<?php echo $DeliveryCode;?>">
  </FORM><script>JumpToSearch()</script><?php
}  
else if(strstr($DeliveryMethod,'德邦')){?>  	    	  	
  <form method="post" action="http://www.deppon.com.cn/home.aspx">
  <input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="/market/wEPDwUKMTI2MzA0NDM3NGQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgEFCWxidFNlYXJjaE0zmtT/8tBwHyrmWz55Myy11yJM">
  <input name="txtSearch" type="hidden" value="<?php echo $DeliveryCode;?>"> 
  <input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="/market/wEWAwLs/r+wAgKgqaSmAwKZ7YzdCvdVvzEVyxch8uHwrbn+N+Nb1TJF" />
  </form><script>JumpToSearch()</script><?php
} 
else{
  echo '<p align="center">尚未给该配送方式建立自动追踪服务！</p>';
}
db_close();
?>
</body>
</html>
