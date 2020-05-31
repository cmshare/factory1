<?php require('includes/dbconn.php');
CheckLogin();
db_open();
$where='where deposit<-10 and username<>\'junhang\'';
$totalDebt=$conn->query('select sum(deposit) from mg_users '.$where)->fetchColumn(0);
if($totalDebt===false)$totalDebt=0;
else $totalDebt=round($totalDebt);
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
</head>
<body topmargin="0" leftmargin="0">
<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif" height="25"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -> <a href="?"><font color=#FF0000>负债客户排行</font></a></b> &nbsp; (注：总欠款<font color="#FF0000"><?php echo $totalDebt;?></font>元，低于10元的欠款未在此列出，<!--u>请相关负责客服加紧催缴欠款</u-->。)
    </td>
  </tr>
  <tr> 
    <td valign="top" bgcolor="#FFFFFF"><form  method="post">
	  <table width="99%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
          <tr height="25" bgcolor="#F7F7F7" align="center" > 
            <td WIDTH="8%" background="images/topbg.gif"><strong>排行</strong></td>
            <td WIDTH="15%" background="images/topbg.gif"><strong>会员名</strong></td>
            <td WIDTH="15%" background="images/topbg.gif"><strong>真实姓名</strong></td>
            <td WIDTH="12%" background="images/topbg.gif"><strong>预存款(负债)</strong></td>
            <td WIDTH="25%" background="images/topbg.gif"><strong>注册时间</strong></td>
            <td WIDTH="25%" background="images/topbg.gif"><strong>最后登录</strong></td>
          </tr><?php
      $page_size=25;
      $res=page_query('select username,id,realname,deposit,lastlogin,addtime','from mg_users',$where,'order by deposit asc',$page_size);
      if($total_records==0) echo '<tr bgcolor="#FFFFFF"><td colspan="6" align="center"> 对不起，找不到相关记录！</td></tr>';
      else{
        $rank=($page-1)*$page_size;
        foreach($res as $row){
          $rank++;
          echo '<tr height="25" align="center" bgcolor="#FFFFFF" onMouseOut="mOut(this)" onMouseOver="mOvr(this)"> 
            <td>第'.$rank.'名</td>
            <td><a href="mg_usrinfo.php?id='.$row['id'].'">'.$row['username'].'</a></td>
            <td>'.$row['realname'].'</td>
            <td><font color=#FF0000>'.round($row['deposit']).'</font></td>
            <td height="25">'.date('Y-m-d',$row['addtime']).'</td>
            <td height="25">'.date('Y-m-d',$row['lastlogin']).'</td></tr>';
         }
	echo '<tr bgcolor="#FFFFFF"> <td colspan="7" align="center"><script language="javascript">GeneratePageGuider("",'.$total_records.','.$page.','.$total_pages.');</script></td></tr>';
      }?>
      </table>
      </td>
    </form>
  </tr>
</table>
</body>
</html><?php db_close();?>
