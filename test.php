<html>
<body>
<?php require('include/conn.php');
$url="http://www.gdhzp.com/market/test.php?id=adsf&mode=test";
$pos=strpos($url,'?');
if($pos>0)$url=substr($url,0,$pos);
echo $url;?>

</body>
</html>
