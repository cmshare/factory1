<?php require('include/conn.php');
$cid=@$_GET['id'];
$mode=@$_GET['mode'];
if(!is_numeric($cid))$cid=0;
if(!is_numeric($mode))$mode=0;
OpenDB();

function sorts($selec){
  global $conn,$CatList,$UnfoldCategory;
  $res=$conn->query('select id from `mg_sort` where parent = '.$selec.' order by sortorder',PDO::FETCH_NUM);
  foreach($res as $row){
    $CatList .= ', '.$row[0];
    if(empty($UnfoldCategory)) $UnfoldCategory=$selec;
    sorts($row[0]);
  }
}
 
#先遍历子类 	
$CatList=$cid;
sorts($cid);

#再遍历父类     
$PID = $cid;
$LinkSortGuider='';
while($PID>0){
   $row=$conn->query('select id,title,parent from `mg_sort` Where id='.$PID,PDO::FETCH_NUM)->fetch();
   if(empty($row)){
     echo '<script language="javascript">alert("您输入的参数非法，请正确操作！");history.go(-1);</script>';
     CloseDB();
     exit(0);
   }
   $LinkSortGuider = '&nbsp;&gt;&gt;&nbsp;<a href="/category/sort'.$row[0].'.htm">'.$row[1].'</a>'.$LinkSortGuider;
   if(empty($CatTitle)) $CatTitle = $row[1];
   if($PID==$UnfoldCategory) $ParentCategory=$row[2];
   $PID = $row[2];
   if(empty($UnfoldCategory)) $UnfoldCategory=$PID;
}		 

if(empty($LinkSortGuider)){
   $LinkSortGuider = '&nbsp;&gt;&gt;&nbsp;<a href="/category/">所有产品</a>';
   $CatTitle='功能分类';
}
  
if(@$_POST['action']=='get'){
   ShowWareList(true);
   CloseDB();
   exit(0);
}

function ShowWareList($dynamicLoad){
  global $mode,$CatList;
  if($mode==0){
    $sql_count='from `mg_product` where category in ('.$CatList.') and recommend>0';
    $sql_query='select id,name,spec,stock0,price0,price1,price3,onsale '.$sql_count.' order by addtime desc';
  }
  else if($mode==1){
      $sql_count='from  `mg_product` as a, `mg_ordergoods` as b, `mg_orders` as c where a.id=b.productid and  b.ordername=c.ordername and c.state>3 and c.actiontime>unix_timestamp()-30*24*60*60 and a.category in ('.$CatList.') and a.recommend>0';
      $sql_query='select a.id,a.name,a.spec,a.stock0,a.price0,a.price1,a.price3,a.onsale '.$sql_count.' group by a.id order by sum(b.amount) desc, a.recommend desc';
  }
  else{
      $sql_count='from `mg_product` where category in ('.$CatList.') and recommend>0';
      $sql_query='select id,name,spec,stock0,price0,price1,price3,onsale '.$sql_count.' order by price3 asc';
  }
  include('include/m_warelist.php');
  $content=GenWareList($sql_count,$sql_query,$MAX_PER_PAGE=20,@GenPageUrl,$dynamicLoad);
  if(empty($content))$content='<br><p align="center">本类商品暂无记录！</p>';
  echo $content;
}
function GenPageUrl($page){
  global $cid,$mode;
  $url='/category.php?page='.$page;
  if($cid) $url.='&id='.$cid;
  if($mode) $url.='&mode='.$mode;	
  return $url;
}

$HotKeywords=array('品牌','原装','进口','美白','去斑','祛斑','抗皱','消痘','网络热销','正品','热卖','香水','香薰','精油','面膜','护肤品','彩妆','韩国','日本','欧美','香港','台湾','澳门','上海','北京','重庆','江苏','南京','镇江','常州','无锡','宜兴','江阴','苏州','昆山','张家港','连云港','扬州','徐州','宿迁','淮安','盱眙','盐城','泰安','泰州','泰兴','靖江','南通','宁波','杭州','山东','济南','威海','廊坊','青岛','日照','成都','广东','广州','惠州','深圳','武汉','珠海','汕头','汕尾','厦门','浙江','温州','绍兴','金华','义乌','安徽','芜湖','六安','马鞍山','蚌埠','安庆','黄山','合肥','阜阳','滁州','山西','阳泉','洛阳','天津','太原','吉林','沈阳','辽宁','长春','长沙','大连','石家庄','西安','新疆','东莞','福州','湖北','湖南','福建','黑龙江','湖州','东营','中山','临夏','临汾','临沂','临沧','丹东','丽水','丽江','乌兰察布盟','乌海','乌鲁木齐','乐山','九江','云南','昆明','三明','三门峡','上饶','东川','七台河','万县','三亚','云浮','亳州','伊克昭盟','伊春','伊犁哈萨克','佛山','佳木斯','保定','保山','信阳','克孜勒苏柯尔克孜','克拉玛依','六盘水','兰州','兴安盟','其','内江','内蒙古','包头','北海','十堰','南充','南宁','南宁','南平','南昌','南阳','博尔塔拉蒙古自治州','双鸭山','台州','吉安','吐鲁番','吕梁','周口','呼伦贝尔盟','呼和浩特','和田','咸宁','哈密','哈尔滨','哲里木盟','唐山','商丘','商洛','喀什','嘉兴','嘉峪关','四川','四平','固原','塔城','大兴安岭','大同','大庆','大理','天水','娄底','孝感','宁夏','宁德','安康','安阳','安顺','定西','宜宾','宜昌','宜春','宝鸡','宣州','宿州','山南','岳阳','巢湖','巴中','巴彦淖尔盟','巴音郭楞','常德','平凉','平顶山','广元','广安','广西','庆阳','延安','延边','开封','张家界','张家口','张掖','德宏','德州','德阳','忻州','怀化','怒江','思茅','恩施','承德','抚州','抚顺','拉萨','揭阳','攀枝花','文山','新乡','新余','日喀则','昌吉','昌都','昭通','晋中','晋城','景德镇','曲靖','朔州','朝阳','本溪','松原','松花江','林芝','果洛','枣庄','柳州','株洲','桂林','梅州','梧州','楚雄','榆林','武威','毕节','永州','汉中','江西','江门','池州','沧州','河北','河南','河池','河源','泉州','泸州','济宁','海东','海北','海南','海口','海西','涪陵','淄博','淮北','淮南','清远','渭南','湘潭','湘西','湛江','滨州','漯河','漳州','潍坊','潮州','濮阳','烟台','焦作','牡丹江','玉林','玉溪','甘南','甘孜','甘肃','白城','白山','白银','百色','益阳','盘锦','石嘴山','石河子','秦皇岛','红河','绥化','绵阳','聊城','自贡','舟山','茂名','荆沙','荆门','莆田','莱芜','菏泽','萍乡','营口','衡水','衡阳','衢州','襄樊','西双版纳','西宁','西藏','许昌','贵州','贵港','贵阳','赣州','赤峰','辽源','辽阳','达川','运城','迪庆','通化','遂宁','遵义','邢台','那曲','邯郸','邵阳','郑州','郴州','>鄂州','酒泉','金昌','钦州','铁岭','铜仁','铜川','铜陵','银南','银川','锡林郭勒盟','锦州','锦西','长治','阜新','防城港','阳江','阿克苏','阿勒泰','阿坝','阿拉善盟','阿里','陇南','陕西','雅安','青海','鞍山','韶关','驻马店','鸡西','鹤壁','鹤岗','鹰潭','黄冈','黄南','黄石','黑河','黔江','黔西','齐齐哈尔','龙岩');
$MyKeyword=$HotKeywords[$cid % count($HotKeywords)].'化妆品批发';
$PageTitle=$CatTitle.'－地区代理'.$MyKeyword.'－最低价格－【涵若铭妆】';
$PageKeywords=$CatTitle.',化妆品批发,'.$MyKeyword.',最低价';
$PageDescription='产品类别:'.$CatTitle.',本公司提供各种品牌化妆品批发代理加盟连锁业务,包括进口化妆品批发,韩国化妆品批发业务,地区代理'.$MyKeyword.',打造国内化妆品批发最低价格的批发进货渠道/批发市场。';
include('include/page_head.php');?>
<TABLE align="center" cellSpacing="0" cellPadding="0" border="0" width="1000" style="background:url(/images/bg_mid.gif) repeat-x;">
<tr>
   <td width="200" valign="TOP">
      <TABLE width="100%" height="100%" cellSpacing=0 cellPadding=0 border=0 style="background:url(/images/bg_left.gif) repeat-y;margin-top:30px;">
      <tr>
    	 <td height="1%" align="center">
	 <!----导航:商品分类 开始---->
         <?php include('include/guide_category.php');?>  
         <!----导航:商品分类 结束---->   
         </td></tr>
      <tr><td height="99%" style="background:url(/images/advs/advs_blank.gif) repeat-y;"></td></tr>
      </table>       
    </td>
    <td valign="top" width="800" height="100%">
    <!-------分类显示 开始---------->
       <TABLE cellSpacing=0 cellPadding=0 width="100%" height="100%"  border="0">
       <TR>
          <TD width="800" height="40" valign="middle">
            <TABLE cellSpacing=0 cellPadding=0 width="100%" height="20" border="0">
            <TR valign="bottom">
              <TD>&nbsp;&nbsp;<img src="/images/arrow2.gif" width="6" height="7">&nbsp;当前位置：&nbsp;<a href="<?php echo WEB_ROOT;?>#" title="回到化妆品批发商城首页">首页</a><?php echo $LinkSortGuider;?></TD>
              <TD align="right"><font color=GREEN>排序方式：</font><select name="sortsel" id="sortsel" onchange="ChangeSort(this.value);">
                 <option value="0">最新上架</option>
                 <option value="1" <?php if($mode==1) echo 'selected';?>>最近热销</option>
                 <option value="2" <?php if($mode==2) echo 'selected';?>>商品价格</option> 
             	</select></TD><td width="18"></td>
            </TR>
            </TABLE>
          </TD>
       </TR>
       <TR>
          <TD id="contentbox" valign="top"><?php ShowWareList(false);?></TD>
        </TR>
        </TABLE>
        <!-------分类显示 结束---------->
  </td>
</tr>
<tr>
  <td height="5"></td>
</tr>	
</table>
<div id="ProductTipLayer" style="display:none;"></div>
<script>
var sortmode=<?php echo $mode;?>;
  function JumpToPage(page){
     var params="?&id=<?php echo $cid;?>&mode="+sortmode+"&page="+page;
     AsyncPost("action=get","/category.php"+params,"contentbox");	
     document.body.scrollTop=0;
  }
  function JumpLinks(alink){
     AsyncPost("action=get",alink.href,"contentbox");	 
     document.body.scrollTop=0;
     return false;
  }
  function ChangeSort(sortindex){
    sortmoder=sortindex;
    JumpToPage(1); 
  }
</script><?php
include('include/page_bottom.htm');
CloseDB();?>
</body>
</html>
