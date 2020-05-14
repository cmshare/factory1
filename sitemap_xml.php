<?php require('include/conn.php');
OpenDB();
$lastmodify='2012-06-21T01:29:31+00:00';
?>
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="sitemap.xsl"?>
<!-- Free Sitemap Generator http://www.sitemapx.com -->
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<url>
<loc>http://www.gdhzp.com/</loc>
<priority>1.0</priority>
<lastmod><?php echo $lastmodify;?></lastmod>
<changefreq>Always</changefreq>
</url><?php

$urlarray=array('wares.htm','hotsell.htm','bargain.htm','present.htm','usrmgr.htm','search.htm','newarrival.php','news.php','orders.php','viewpic.php','help/','news/','book/','mg_articles/','products/','category/','company/');
$urlcount=count($urlarray);
for($jishu=0;$jishu<$urlcount;$jishu++){ ?>
<url>
<loc>http://www.gdhzp.com/<?php echo $urlarray[$jishu];?></loc>
<priority>0.8</priority>
<lastmod><?php echo $lastmodify;?></lastmod>
<changefreq>Always</changefreq>
</url><?php
}

$res=$conn->query('select id from `mg_product` where recommend>=0 order by addtime desc',PDO::FETCH_NUM);
foreach($res as $row){?>
<url>
<loc>http://www.gdhzp.com/products/<?php echo $row[0];?>.htm</loc>
<priority>0.8</priority>
<lastmod><?php echo $lastmodify;?></lastmod>
<changefreq>Always</changefreq>
</url><?php      
}

$res=$conn->query('select id from `mg_article` where property=1 or property=2 order by addtime desc',PDO::FETCH_NUM);	
foreach($res as $row){?>
<url>
<loc>http://www.gdhzp.com/news/news<?php echo $row[0];?>.htm</loc>
<priority>0.6</priority>
<lastmod><?php echo $lastmodify;?></lastmod>
<changefreq>Always</changefreq>
</url><?php
}

$res=$conn->query('select id from `mg_help` where property>0 order by sortorder',PDO::FETCH_NUM);
foreach($res as $row){?>
<url>
<loc>http://www.gdhzp.com/help/help<?php echo $row[0];?>.htm</loc>
<priority>0.6</priority>
<lastmod><?php echo $lastmodify;?></lastmod>
<changefreq>Always</changefreq>
</url><?php
}


$res=$conn->query('select id from `mg_category` where recommend>=0 and parent>=0',PDO::FETCH_NUM);
foreach($res as $row){?>
<url>
<loc>http://www.gdhzp.com/category/cat<?php echo $row['id'];?>.htm</loc>
<priority>0.6</priority>
<lastmod><?php echo $lastmodify;?></lastmod>
<changefreq>Always</changefreq>
</url><?php
}


$res=$conn->query('select id from `mg_sort` where parent>=0',PDO::FETCH_NUM);
foreach($res as $row){?>
<url>
<loc>http://www.gdhzp.com/category/sort<?php echo $row[0];?>.htm</loc>
<priority>0.6</priority>
<lastmod><?php echo $lastmodify;?></lastmod>
<changefreq>Always</changefreq>
</url><?php
}

CloseDB();
?>
</urlset>
