<?php require('includes/dbconn.php');
CheckLogin();
db_open();
$UserTitles=array();
$res=$conn->query('select id,title from mg_usrgrade',PDO::FETCH_NUM);
foreach($res as $row){
  $UserTitles[$row[0]]=$row[1];
}?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="includes/admincss.css" rel="stylesheet" type="text/css">
<SCRIPT language="JavaScript" src="includes/mg_comm.js" type="text/javascript"></SCRIPT>
<title>会员消费排名</title>
</head>
<body leftmargin="0" topmargin="0">

<table width="100%" height="100%" border="5" align="center" cellpadding="5" cellspacing="5" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr> 
    <td background="images/topbg.gif" height="25"><b><img src="images/pic5.gif" width="28" height="22" align="absmiddle" />您现在所在的位置是： <a href="admincenter.php">管理首页</a> -&gt; <font color=#FF0000>会员消费排名</font></b></td>
  </tr>
  <tr> 
    <td valign="top" bgcolor="#FFFFFF"><table width="98%" border="2" align="center" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
        <tr bgcolor="#F7F7F7" align="center"> 
          <td width="5%" height="25" background="images/topbg.gif" bgcolor="#F7F7F7"><strong>排名</strong></td>
          <td width="10%" height="25" background="images/topbg.gif"><strong>用户名</strong></td>
          <td width="9%" background="images/topbg.gif"><strong>真实姓名</strong></td>
          <td width="11%" background="images/topbg.gif"><strong>会员级别</strong></td>
          <td width="9%" background="images/topbg.gif"><strong>积分</strong></td>
          <td width="9%" background="images/topbg.gif"><strong>预存款</strong></td>
          <td width="9%" background="images/topbg.gif"><strong>销费额</strong></td>
          <td width="8%" background="images/topbg.gif"><strong>登录次数</strong></td>
          <td width="15%" background="images/topbg.gif"><strong>最近登录</strong></td>
          <td width="15%" background="images/topbg.gif"><strong>注册时间</strong></td>
        </tr><?php
$MaxPageSize=20;
$res=page_query('select mg_users.*,sum(mg_orders.totalprice) AS totalconsume','FROM mg_orders inner join mg_users on mg_orders.username=mg_users.username','where mg_orders.state>3 GROUP BY mg_users.username','order by totalconsume desc',$MaxPageSize);
if(!$res) echo '<tr bgcolor="#FFFFFF"><td colspan="9" align="center"> 对不起，找不到相关记录！</td></tr>';
else{
  $index=0;
  foreach($res as $row){		
    echo '<tr height="25" align="center" bgcolor="#FFFFFF" height="20" onMouseOut="mOut(this)" onMouseOver="mOvr(this)">';
    echo '<td>'.(($page-1)*$MaxPageSize+(++$index)).'</td>';
    echo '<td><a href="mg_usrinfo.php?id='.$row['id'].'">'.$row['username'].'</a></td>';
    echo '<td>'.$row['realname'].'</td>';
    echo '<td>'.$UserTitles[$row['grade']].'</td>';
    echo '<td>'.$row['score'].'</td>';
    echo '<td>'.round($row['deposit']).'</td>';
    echo '<td>'.round($row['totalconsume']).'</td>';
    echo '<td>'.$row['logincount'].'</td>';
    echo '<td>'.date('Y-m-d',$row['lastlogin']).'</td>';
    echo '<td>'.date('Y-m-d',$row['addtime']).'</td>';
    echo '</tr>';
  }
  echo '<tr bgcolor="#FFFFFF"><td align="center" colspan="10"><script language="javascript">GeneratePageGuider("",'.$total_records.','.$page.','.$total_pages.');</script></td></tr>';
}?>		
  </table></td>
</tr>
</table>

</body>
</html>
<?php
db_close();?>
