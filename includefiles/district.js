var provincearray=new Array(new Option('请选择省份……','0'),new Option('北京','2'),new Option('安徽','1'),new Option('山东省','3'),new Option('江苏省','4'),new Option('上海市','5'),new Option('广东省','6'),new Option('浙江省','7'),new Option('福建省','8'),new Option('重庆市','9'),new Option('甘肃省','10'),new Option('广西省','11'),new Option('贵州省','12'),new Option('海南省','13'),new Option('河北省','14'),new Option('河南省','15'),new Option('黑龙江省','16'),new Option('湖北省','17'),new Option('湖南省','18'),new Option('江西省','19'),new Option('吉林省','20'),new Option('辽宁省','21'),new Option('内蒙古','22'),new Option('宁夏','23'),new Option('青海省','24'),new Option('山西省','25'),new Option('陕西省','26'),new Option('四川省','27'),new Option('天津市','28'),new Option('新疆','29'),new Option('西藏','30'),new Option('云南省','31'),new Option('香港','32'),new Option('澳门','33'),new Option('台湾','34'),new Option('国外与其它','35'));
var cityarray=[];
cityarray['0']=new Array(new Option('请选择城市……','0'));
cityarray['2']=new Array(
new Option('北京市','38'));
cityarray['1']=new Array(
new Option('合肥市','36'),
new Option('芜湖市','37'),
new Option('安庆市','39'),
new Option('巢湖市','40'),
new Option('滁州市','41'),
new Option('淮南市','42'),
new Option('马鞍山市','43'),
new Option('宿州市','44'),
new Option('宣州市','45'),
new Option('蚌埠市','46'),
new Option('池州地区','47'),
new Option('阜阳市','48'),
new Option('淮北市','49'),
new Option('黄山市','50'),
new Option('六安市','51'),
new Option('铜陵市','52'),
new Option('亳州市','53'));
cityarray['3']=new Array(
new Option('济南市','54'),
new Option('东营市','55'),
new Option('滨州地区','56'),
new Option('淄博市','57'),
new Option('德州市','58'),
new Option('济宁市','59'),
new Option('聊城地区','60'),
new Option('临沂市','61'),
new Option('莱芜市','62'),
new Option('青岛市','63'),
new Option('日照市','64'),
new Option('威海市','65'),
new Option('泰安市','66'),
new Option('潍坊市','67'),
new Option('烟台市','68'),
new Option('菏泽地区','69'),
new Option('枣庄市','70'));
cityarray['4']=new Array(
new Option('南京市','71'),
new Option('镇江市','81'),
new Option('常州市','73'),
new Option('无锡市','78'),
new Option('江阴市','383'),
new Option('宜兴市','384'),
new Option('苏州市','77'),
new Option('南通市','75'),
new Option('泰州市','82'),
new Option('淮安市','72'),
new Option('盐城市','79'),
new Option('扬州市','80'),
new Option('徐州市','76'),
new Option('连云港市','74'),
new Option('宿迁市','83'),
new Option('其它区','385'));
cityarray['5']=new Array(
new Option('上海市','84'));
cityarray['6']=new Array(
new Option('广州市','85'),
new Option('深圳市','97'),
new Option('湛江市','104'),
new Option('珠海市','103'),
new Option('汕头市','95'),
new Option('汕尾市','96'),
new Option('东莞市','88'),
new Option('梅州市','93'),
new Option('清远市','94'),
new Option('潮州市','91'),
new Option('茂名市','92'),
new Option('佛山市','86'),
new Option('韶关市','98'),
new Option('阳江市','99'),
new Option('河源市','100'),
new Option('云浮市','101'),
new Option('中山市','102'),
new Option('惠州市','87'),
new Option('江门市','89'),
new Option('揭阳市','90'));
cityarray['7']=new Array(
new Option('杭州市','107'),
new Option('嘉兴市','108'),
new Option('金华市','109'),
new Option('义乌市','381'),
new Option('衢州市','110'),
new Option('丽水地区','111'),
new Option('宁波市','112'),
new Option('绍兴市','113'),
new Option('台州市','114'),
new Option('温州市','115'),
new Option('舟山市','116'),
new Option('湖州市','117'),
new Option('其地区','382'));
cityarray['8']=new Array(
new Option('福州市','118'),
new Option('龙岩地区','119'),
new Option('南平市','120'),
new Option('宁德地区','121'),
new Option('莆田市','122'),
new Option('泉州市','123'),
new Option('三明市','124'),
new Option('厦门市','125'),
new Option('漳州市','126'));
cityarray['9']=new Array(
new Option('重庆市','127'),
new Option('涪陵市','128'),
new Option('黔江地区','129'),
new Option('白银市','134'),
new Option('万县市','130'));
cityarray['10']=new Array(
new Option('兰州市','131'),
new Option('甘南藏族自治州','132'),
new Option('定西地区','133'),
new Option('嘉峪关市','135'),
new Option('金昌市','136'),
new Option('酒泉地区','137'),
new Option('临夏回族自治州','138'),
new Option('陇南地区','139'),
new Option('平凉地区','140'),
new Option('庆阳地区','141'),
new Option('天水市','142'),
new Option('武威地区','143'),
new Option('张掖地区','144'));
cityarray['11']=new Array(
new Option('南宁市','145'),
new Option('防城港市','146'),
new Option('北海市','147'),
new Option('百色地区','148'),
new Option('桂林地区','149'),
new Option('桂林市','150'),
new Option('柳州地区','151'),
new Option('柳州市','152'),
new Option('南宁地区','153'),
new Option('钦州市','154'),
new Option('梧州地区','155'),
new Option('梧州市','156'),
new Option('河池地区','157'),
new Option('玉林地区','158'),
new Option('贵港市','159'));
cityarray['12']=new Array(
new Option('贵阳市','160'),
new Option('毕节地区','161'),
new Option('遵义地区','162'),
new Option('安顺地区','163'),
new Option('六盘水市','164'),
new Option('黔东南苗族侗族自治州','165'),
new Option('黔南布依族苗族自治州','166'),
new Option('黔西南布依族苗族自治州','167'),
new Option('铜仁地区','168'));
cityarray['13']=new Array(
new Option('三亚市','169'),
new Option('海口市','170'));
cityarray['14']=new Array(
new Option('石家庄市','171'),
new Option('邯郸市','172'),
new Option('邢台市','173'),
new Option('保定市','174'),
new Option('张家口市','175'),
new Option('沧州市','176'),
new Option('承德市','177'),
new Option('廊坊市','178'),
new Option('秦皇岛市','179'),
new Option('唐山市','180'),
new Option('衡水地区','181'));
cityarray['15']=new Array(
new Option('郑州市','182'),
new Option('开封市','183'),
new Option('驻马店地区','184'),
new Option('安阳市','185'),
new Option('焦作市','186'),
new Option('洛阳市','187'),
new Option('濮阳市','188'),
new Option('漯河市','189'),
new Option('南阳市','190'),
new Option('平顶山市','191'),
new Option('新乡市','192'),
new Option('信阳地区','193'),
new Option('许昌市','194'),
new Option('商丘地区','195'),
new Option('三门峡市','196'),
new Option('鹤壁市','197'),
new Option('周口地区','198'));
cityarray['16']=new Array(
new Option('哈尔滨市','199'),
new Option('大庆市','200'),
new Option('大兴安岭地区','201'),
new Option('鸡西市','202'),
new Option('佳木斯市','203'),
new Option('牡丹江市','204'),
new Option('齐齐哈尔市','205'),
new Option('七台河市','206'),
new Option('双鸭山市','207'),
new Option('绥化地区','208'),
new Option('松花江地区','209'),
new Option('鹤岗市','210'),
new Option('黑河市','211'),
new Option('伊春市','212'));
cityarray['17']=new Array(
new Option('武汉市','213'),
new Option('黄冈市','214'),
new Option('黄石市','215'),
new Option('恩施土家族苗族自治州','216'),
new Option('鄂州市','217'),
new Option('荆门市','218'),
new Option('荆沙市','219'),
new Option('孝感市','220'),
new Option('十堰市','221'),
new Option('襄樊市','222'),
new Option('咸宁地区','223'),
new Option('宜昌市','224'));
cityarray['18']=new Array(
new Option('长沙市','225'),
new Option('怀化地区','226'),
new Option('郴州市','227'),
new Option('常德市','228'),
new Option('娄底地区','229'),
new Option('邵阳市','230'),
new Option('湘潭市','231'),
new Option('湘西土家族苗族自治州','232'),
new Option('衡阳市','233'),
new Option('永州市','234'),
new Option('益阳市','235'),
new Option('岳阳市','236'),
new Option('株洲市','237'),
new Option('张家界市','238'));
cityarray['19']=new Array(
new Option('南昌市','239'),
new Option('抚州地区','240'),
new Option('赣州地区','241'),
new Option('吉安地区','242'),
new Option('景德镇市','243'),
new Option('九江市','244'),
new Option('萍乡市','245'),
new Option('新余市','246'),
new Option('上饶地区','247'),
new Option('鹰潭市','248'),
new Option('宜春地区','249'));
cityarray['20']=new Array(
new Option('长春市','250'),
new Option('白城市','251'),
new Option('白山市','252'),
new Option('吉林市','253'),
new Option('辽源市','254'),
new Option('四平市','255'),
new Option('松原市','256'),
new Option('通化市','257'),
new Option('延边朝鲜族自治州','258'));
cityarray['21']=new Array(
new Option('沈阳市','259'),
new Option('大连市','260'),
new Option('阜新市','261'),
new Option('抚顺市','262'),
new Option('本溪市','263'),
new Option('鞍山市','264'),
new Option('丹东市','265'),
new Option('锦州市','266'),
new Option('朝阳市','267'),
new Option('辽阳市','268'),
new Option('盘锦市','269'),
new Option('铁岭市','270'),
new Option('营口市','271'),
new Option('锦西市','272'));
cityarray['22']=new Array(
new Option('呼和浩特市','273'),
new Option('阿拉善盟','274'),
new Option('巴彦淖尔盟','275'),
new Option('包头市','276'),
new Option('赤峰市','277'),
new Option('兴安盟','278'),
new Option('乌兰察布盟','279'),
new Option('乌海市','280'),
new Option('锡林郭勒盟','281'),
new Option('呼伦贝尔盟','282'),
new Option('伊克昭盟','283'),
new Option('哲里木盟','284'));
cityarray['23']=new Array(
new Option('银川市','285'),
new Option('固原地区','286'),
new Option('石嘴山市','287'),
new Option('银南地区','288'));
cityarray['24']=new Array(
new Option('西宁市','289'),
new Option('海东地区','290'),
new Option('海南藏族自治州','291'),
new Option('海北藏族自治州','292'),
new Option('黄南藏族自治州','293'),
new Option('果洛藏族自治州','294'),
new Option('海西蒙古族藏族自治州','295'));
cityarray['25']=new Array(
new Option('朔州市','304'),
new Option('太原市','296'),
new Option('大同市','297'),
new Option('晋城市','298'),
new Option('晋中地区','299'),
new Option('长治市','300'),
new Option('临汾地区','301'),
new Option('吕梁地区','302'),
new Option('忻州地区','303'),
new Option('阳泉市','305'),
new Option('运城地区','306'));
cityarray['26']=new Array(
new Option('西安市','307'),
new Option('宝鸡市','308'),
new Option('安康地区','309'),
new Option('商洛地区','310'),
new Option('铜川市','311'),
new Option('渭南地区','312'),
new Option('渭南地区','313'),
new Option('延安地区','314'),
new Option('汉中地区','315'),
new Option('榆林地区','316'));
cityarray['27']=new Array(
new Option('成都市','317'),
new Option('达川地区','318'),
new Option('甘孜藏族自治州','319'),
new Option('自贡市','320'),
new Option('阿坝藏族羌族自治州','321'),
new Option('巴中地区','322'),
new Option('德阳市','323'),
new Option('广安地区','324'),
new Option('广元市','325'),
new Option('凉山彝族自治州','326'),
new Option('乐山市','327'),
new Option('攀枝花市','328'),
new Option('南充市','329'),
new Option('内江市','330'),
new Option('泸州市','331'),
new Option('绵阳市','332'),
new Option('遂宁市','333'),
new Option('雅安地区','334'),
new Option('宜宾地区','335'));
cityarray['28']=new Array(
new Option('天津市','336'));
cityarray['29']=new Array(
new Option('乌鲁木齐市','337'),
new Option('喀什地区','338'),
new Option('克孜勒苏柯尔克孜自治州','339'),
new Option('克拉玛依市','340'),
new Option('阿克苏地区','341'),
new Option('阿勒泰地区','342'),
new Option('巴音郭楞蒙古自治州','343'),
new Option('哈密地区','344'),
new Option('博尔塔拉蒙古自治州','345'),
new Option('昌吉回族自治州','346'),
new Option('塔城地区','347'),
new Option('吐鲁番地区','348'),
new Option('和田地区','349'),
new Option('伊犁地区','350'),
new Option('伊犁哈萨克自治州','351'),
new Option('石河子市','352'));
cityarray['30']=new Array(
new Option('拉萨市','353'),
new Option('阿里地区','354'),
new Option('昌都地区','355'),
new Option('林芝地区','356'),
new Option('那曲地区','357'),
new Option('山南地区','358'),
new Option('日喀则地区','359'));
cityarray['31']=new Array(
new Option('昆明市','360'),
new Option('大理白族自治州','361'),
new Option('东川市','362'),
new Option('保山地区','363'),
new Option('德宏傣族景颇族自治州','364'),
new Option('迪庆藏族自治州','365'),
new Option('楚雄彝族自治州','366'),
new Option('临沧地区','367'),
new Option('怒江傈僳族自治州','369'),
new Option('丽江地区','368'),
new Option('曲靖地区','370'),
new Option('思茅地区','371'),
new Option('西双版纳傣族自治州','372'),
new Option('文山壮族苗族自治州','373'),
new Option('红河哈尼族彝族自治州','374'),
new Option('玉溪地区','375'),
new Option('昭通地区','376'));
cityarray['32']=new Array(
new Option('香港','377'));
cityarray['33']=new Array(
new Option('澳门','378'));
cityarray['34']=new Array(
new Option('台湾','379'));
cityarray['35']=new Array(
new Option('国外与其它','380'));

function OnProvinceSelChange(){var myform=this.form;var ProvinceCode=(myform)?myform.ProvinceList.value:0;if(ProvinceCode){myform.District.options.length=0;for(var i=0;i<cityarray[ProvinceCode].length;i++){ myform.District.options.add(cityarray[ProvinceCode][i]);}}}
function InitDistrictSelection(myform){var i,j,k,getselected=false,ProvinceCode,districtcode=myform.District.options[0].value;myform.ProvinceList.options.length=0;for(i=0;i<provincearray.length;i++){ myform.ProvinceList.options.add(provincearray[i]);if(!getselected){ ProvinceCode=provincearray[i].value;for(j=0;j<cityarray[ProvinceCode].length;j++){	if(districtcode==cityarray[ProvinceCode][j].value){ myform.District.options.length=0;for(k=0;k<cityarray[ProvinceCode].length;k++){ myform.District.options.add(cityarray[ProvinceCode][k]);}myform.District.options[j].selected=true;myform.ProvinceList.options[i].selected=true;getselected=true;break;}}}}myform.ProvinceList.onchange=OnProvinceSelChange;}
