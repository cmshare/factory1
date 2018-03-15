<?php
require('conn_articles.php');
OpenDB();
?>
<table border="0" width="190" cellpadding="0" cellspacing="0" align="center" style="BACKGROUND-IMAGE:url(images/leftbarpatch1.gif);margin-top:5px">
<tr><td height=60><img src="images/articles.gif" border=0 width=190 height=60></td></tr>
<tr>
   <td align=center>
   <table width="80%"  border="0" cellspacing="0" align="center" style="color:#FF6600"><?php
  $res_article=$conn->query('select id,title,link from `articles` where property=1 order by addtime desc limit 8',PDO::FETCH_ASSOC);
  foreach($res_article as $row_article){
    $newslink=$row_article['link'];
    if(empty($newslink)) $newslink='/articles/?id='.$row_article['id']; 
    echo '<TR><TD><A href="'.$newslink.'">'.$row_article['title'].'</A></TD></TR>';
  }?>
    </table></td></tr>
<tr><td><img src="images/leftbarpatch2.gif" width=190 border=0></td></tr>
</table> 
<?php CloseDB(); ?>   
