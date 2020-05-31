<?php require('includes/dbconn.php');
CheckLogin();
db_open();
if(@$_GET['mode']=='excuse'){
  $resID=$_POST['id'];
  if(is_numeric($resID) && $resID>0){
    $value=FilterText(trim($_POST['value']));
    if(strlen($value)>255) $value=substr($value,0,250).'...';
    if($conn->exec("update mg_logs set remark='$value' where id=$resID"))echo '<OK>';
  }
  db_close();
  exit(0);
}
 
$BeginDate=@$_GET['begindate'];
$EndDate=@$_GET['enddate'];
if(empty($BeginDate) || !($BeginDate=strtotime($BeginDate)))$BeginDate=strtotime(date('Y-m').'-1');
if(empty($EndDate) || !($EndDate=strtotime($EndDate)))$EndDate=time();

$OwnPopedomManage=CheckPopedom('MANAGE');
if($OwnPopedomManage && ($Operator=trim(@$_GET['operator']))) $Operator=FilterText($Operator);
else $Operator=$AdminUsername;	 

$where="where addtime>$BeginDate and addtime<$EndDate";
if($Operator=='所有人员') $where.=' and username<>\'aufame\'';
else $where.=" and username='$Operator'";
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Refresh" content="300;URL=<?php echo $_SERVER['SCRIPT_NAME'];?>">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<title>操作日志管理</title>
<script type="text/JavaScript">
function UpdateTimeDisplay()
{ var timeDisp=new Date().toLocaleString();
  if(timeDisp.indexOf("星期")<0)
  { timeDisp=timeDisp+' 星期'+'日一二三四五六'.charAt(new Date().getDay()); 
  }
  webjx.innerHTML=timeDisp;
}

function GetNewDate(baseDate,dayoffset)
{ var newdate=new Date(baseDate); 
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

function ChangeRemark(resID,defValue){
  var OnGetValue=function(value){
    if(value!=null){
      if(value!=defValue){
        var OnPost=function(ret){
          if(ret=='<OK>'){
            alert('操作成功'); 
            self.location.reload();
          } 
          else if(ret)alert(ret);
        }
        AsyncPost('id='+resID+'&value='+encodeURIComponent(value),'?mode=excuse',OnPost); 
      }
      else alert('没有改变!');
    }
    return true;
  }
  AsyncPrompt('备注','填写说明文字:',OnGetValue,defValue,80);

}
</script>	
</head>
<style type="text/css">
.dateinput {width:70px;height:16px;font-size:10pt;color:#FF0000;text-align:center;border: 0px solid #CCCCCC;background-color:transparent;}
.memo_0{BACKGROUND-POSITION: 50% 50%; BACKGROUND-IMAGE:url(images/memo_gray.gif);BACKGROUND-REPEAT:no-repeat;Cursor:pointer;}
.memo_1{BACKGROUND-POSITION: 50% 50%; BACKGROUND-IMAGE:url(images/memo.gif);BACKGROUND-REPEAT:no-repeat;Cursor:pointer;}
 -->
</style>
<body>
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td height="20" background="images/topbg.gif">
      <table width="98%" align="center" border="0" cellpadding="0" cellspacing="0">
     	  <tr>
     	  	<td nowrap><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <font color=#FF0000>日志管理</font></b></td>
     	    <td align="right" nowrap><div id="webjx"></div></td>
     	    <script>setInterval("UpdateTimeDisplay();",1000);</script>
     	  </tr>
     	  </table>	
    </td>
  </tr>
  <tr> 
    <td bgcolor="#FFFFFF" valign="top" >
 
    <table style="margin-top:5px" width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
    <tr>
<td background="images/topbg.gif" colspan="4"><form method="get" name="MyStatForm" style="margin:0px">
         <table width="100%" border="0" cellpadding="0" cellspacing="0" height="20">
      	 <tr> 	
    	   <td>
    	      <table border="0" cellpadding="0" cellspacing="0" height=22>
    	      <tr>
    		<td>&nbsp;<input name="begindate" type="text" value="<?php echo date('Y-m-d',$BeginDate);?>" class="dateinput">至<input name="enddate" type="text" value="<?php echo date('Y-m-d',$EndDate);?>" class="dateinput">期间，</td>
    		<td><?php
    		if($OwnPopedomManage){?> 
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
      	      <td><select name="operator" onblur="ShowOperator(false)" onchange="DummyOperator.innerText=this.value; ShowOperator(false);" style="display:none;"><option value="所有人员">所有人员</option><?php
    	      $res=$conn->query('select username from mg_admins where idverified',PDO::FETCH_NUM);
      	      foreach($res as $row){
      	        if($row[0]==$Operator) echo '<option value="'.$row[0].'" selected>'.$row[0].'</option>';
      	        else echo '<option value="'.$row[0].'">'.$row[0].'</option>';
              }
              echo '</select>';
            }
      	    else echo '您';?></td><td>的日志:</td>
      	    </tr>
            </table>
          </td>	
    	  <td align="right"><input type="button" value="重新统计" onclick="MyStat(0)"><input type="button" value="本周统计" onclick="MyStat(1)"><input type="button" value="上周统计" onclick="MyStat(2)"><input type="button" value="本月统计" onclick="MyStat(3)"><input type="button" value="上月统计" onclick="MyStat(4)"></td>
        </tr>
        </table></form>
      </td>
  </tr> 
  <tr height="25" bgcolor="#F7F7F7" align="center"> 
     <td width="25%" background="images/topbg.gif"><strong>日期</strong> <img src="images/sort_desc.gif" align="absmiddle"></td>
     <td width="25%" background="images/topbg.gif"><strong>操作</strong></td>
     <td width="25%" background="images/topbg.gif"><strong>用户</strong></td>
     <td width="25%" background="images/topbg.gif"><strong>说明</strong></td>
  </tr><?php

  $res=page_query('select *','from mg_logs',$where,'order by addtime desc',20);
  if($total_records>0){
    foreach($res as $row){?>
      <tr height="25" bgcolor="#FFFFFF" align="center" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
        <td background="images/topbg.gif"> <?php echo date('Y-m-d H:i:s',$row['addtime']);?></td>
        <td><?php 
          switch($row['type']){
            case 1: echo '登录';break;
            case 2: echo '退出';break;
            default: echo '其它';break;
          }?></td>
        <td><?php echo $row['username'];?></td>
        <td <?php echo (empty($row['remark']))?'class="memo_0"':'class="memo_1" title="'.$row['remark'].'"';?> onclick="ChangeRemark(<?php echo $row['id'];?>,this.title)">&nbsp;</td>
        </tr><?php
    }
    echo '<tr><td colspan="4" height="30" bgcolor="#FFFFFF" valign="middle" align="center"><script language="javascript">  
        GeneratePageGuider("operator='.$Operator.'&begindate='.$BeginDate.'&enddate='.$EndDate.'",'.$total_records.','.$page.','.$total_pages.');</script></td></tr>';
  }
  else echo '<tr><td colspan="4" align="center"><b>没有记录！</b></td></tr>';?>
   </td>
  </tr>
</table>
</body>
</html><?php db_close();?>
