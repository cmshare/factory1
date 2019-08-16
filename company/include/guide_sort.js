var CurrentBrand,CurrentCategory;       
var BrandIndex=new Array();
var CategoryIndex=new Array();
BrandIndex["227"]=new Array("226","护肤系列");
BrandIndex["228"]=new Array("226","彩妆系列");
BrandIndex["229"]=new Array("226","美发护发");
BrandIndex["226"]=new Array("224","韩伊橄榄系列","227","228","229");
BrandIndex["231"]=new Array("225","护肤系列");
BrandIndex["232"]=new Array("225","彩妆系列");
BrandIndex["233"]=new Array("225","美发护发");
BrandIndex["225"]=new Array("224","韩伊玫瑰系列","231","232","233");
BrandIndex["256"]=new Array("255","水养护颜系列");
BrandIndex["257"]=new Array("255","极致美白系列");
BrandIndex["258"]=new Array("255","紧肤抗皱系列");
BrandIndex["259"]=new Array("255","净肤平衡系列");
BrandIndex["255"]=new Array("224","韩伊水呼吸系列","256","257","258","259");
BrandIndex["284"]=new Array("224","韩伊水肌源系列");
BrandIndex["335"]=new Array("334","护肤系列");
BrandIndex["336"]=new Array("334","彩妆系列");
BrandIndex["337"]=new Array("334","美容美发");
BrandIndex["334"]=new Array("224","希伯雅橄榄$蓝莓系列","335","336","337");
BrandIndex["224"]=new Array("267","CO.E/韩国韩伊","226","225","255","284","334");
BrandIndex["435"]=new Array("267","韩国雪花秀");
BrandIndex["436"]=new Array("267","韩国后");
BrandIndex["343"]=new Array("342","护肤系列");
BrandIndex["344"]=new Array("342","彩妆系列");
BrandIndex["348"]=new Array("342","美发护发");
BrandIndex["342"]=new Array("262","蓝莓焕颜新活嫩白系列","343","344","348");
BrandIndex["392"]=new Array("391","护肤系列");
BrandIndex["393"]=new Array("391","彩妆系列");
BrandIndex["391"]=new Array("262","洋甘菊舒颜美肌修护系列","392","393");
BrandIndex["345"]=new Array("282","护肤系列");
BrandIndex["346"]=new Array("282","彩妆系列");
BrandIndex["347"]=new Array("282","美发护发");
BrandIndex["282"]=new Array("262","龙舌兰新生赋活润颜水嫩系列","345","346","347");
BrandIndex["350"]=new Array("349","护肤系列");
BrandIndex["351"]=new Array("349","彩妆系列");
BrandIndex["352"]=new Array("349","美发护发");
BrandIndex["349"]=new Array("262","五味子顶级美白悦颜系列","350","351","352");
BrandIndex["262"]=new Array("267","HANUAN/韩媛","342","391","282","349");
BrandIndex["210"]=new Array("95","逆时空还童系列");
BrandIndex["260"]=new Array("95","收毛孔细致控油系列");
BrandIndex["271"]=new Array("95","360°水动力保湿系列");
BrandIndex["283"]=new Array("95","DNA焕白系列");
BrandIndex["340"]=new Array("95","焕活青春胶原系列");
BrandIndex["389"]=new Array("95","祛痘祛斑祛黑头系列");
BrandIndex["406"]=new Array("95","极地再生冰芯水系列");
BrandIndex["390"]=new Array("95","防晒彩妆面膜系列");
BrandIndex["388"]=new Array("95","男士系列");
BrandIndex["444"]=new Array("95","美发护发");
BrandIndex["445"]=new Array("95","身体护理");
BrandIndex["95"]=new Array("267","韩国爱茉莉·尚姬泉","210","260","271","283","340","389","406","390","388","444","445");
BrandIndex["412"]=new Array("306","彩妆系列");
BrandIndex["306"]=new Array("267","韩国/SKIN79","412");
BrandIndex["413"]=new Array("168","彩妆系列");
BrandIndex["168"]=new Array("267","韩国/MISSHA","413");
BrandIndex["208"]=new Array("204","面部护理");
BrandIndex["205"]=new Array("204","彩妆系列");
BrandIndex["206"]=new Array("204","礼盒套装");
BrandIndex["207"]=new Array("204","眼部护理");
BrandIndex["209"]=new Array("204","手部护理");
BrandIndex["204"]=new Array("267","TheFaceShop/韩国","208","205","206","207","209");
BrandIndex["202"]=new Array("201","眼部护理");
BrandIndex["203"]=new Array("201","面部护理");
BrandIndex["230"]=new Array("201","彩妆系列");
BrandIndex["201"]=new Array("267","SkinFood/韩国","202","203","230");
BrandIndex["328"]=new Array("327","面部护理");
BrandIndex["265"]=new Array("327","眼部护理");
BrandIndex["330"]=new Array("327","身体护理");
BrandIndex["331"]=new Array("327","美容美发");
BrandIndex["332"]=new Array("327","彩妆系列");
BrandIndex["329"]=new Array("327","美容工具");
BrandIndex["333"]=new Array("327","礼盒套装");
BrandIndex["411"]=new Array("327","口腔护理");
BrandIndex["327"]=new Array("267","日韩泰等国当季流行产品","328","265","330","331","332","329","333","411");
BrandIndex["192"]=new Array("191","彩妆系列");
BrandIndex["193"]=new Array("191","礼盒套装");
BrandIndex["194"]=new Array("191","眼部护理");
BrandIndex["195"]=new Array("191","面部护理");
BrandIndex["191"]=new Array("267","Charmzone/韩国婵真","192","193","194","195");
BrandIndex["264"]=new Array("177","韩国产");
BrandIndex["177"]=new Array("267","Vov/韩国Vov","264");
BrandIndex["182"]=new Array("181","彩妆系列");
BrandIndex["183"]=new Array("181","礼盒套装");
BrandIndex["184"]=new Array("181","眼部护理");
BrandIndex["185"]=new Array("181","面部护理");
BrandIndex["181"]=new Array("267","Laneige/韩国兰芝","182","183","184","185");
BrandIndex["170"]=new Array("169","礼盒套装");
BrandIndex["171"]=new Array("169","美发护发");
BrandIndex["172"]=new Array("169","面部护理");
BrandIndex["169"]=new Array("267","Amore/韩国爱茉莉","170","171","172");
BrandIndex["312"]=new Array("267","其他韩国品牌");
BrandIndex["267"]=new Array("0","韩国品牌","224","435","436","262","95","306","168","204","201","327","191","177","181","169","312");
BrandIndex["363"]=new Array("356","面部护理");
BrandIndex["368"]=new Array("356","眼部护理");
BrandIndex["374"]=new Array("356","彩妆系列");
BrandIndex["378"]=new Array("356","礼盒套装");
BrandIndex["356"]=new Array("68","海洋鲜活保湿系列","363","368","374","378");
BrandIndex["398"]=new Array("68","水润柔白系列");
BrandIndex["362"]=new Array("358","面部护理");
BrandIndex["367"]=new Array("358","眼部护理");
BrandIndex["372"]=new Array("358","彩妆系列");
BrandIndex["377"]=new Array("358","礼盒套装");
BrandIndex["358"]=new Array("68","弹力提升系列","362","367","372","377");
BrandIndex["364"]=new Array("357","面部护理");
BrandIndex["369"]=new Array("357","眼部护理");
BrandIndex["373"]=new Array("357","彩妆系列");
BrandIndex["379"]=new Array("357","礼盒套装");
BrandIndex["357"]=new Array("68","晶纯皙白系列","364","369","373","379");
BrandIndex["361"]=new Array("359","面部护理");
BrandIndex["366"]=new Array("359","眼部护理");
BrandIndex["371"]=new Array("359","彩妆系列");
BrandIndex["376"]=new Array("359","礼盒套装");
BrandIndex["359"]=new Array("68","清·调·补（明星）系列","361","366","371","376");
BrandIndex["94"]=new Array("68","眼部养护系列");
BrandIndex["4"]=new Array("68","洗护系列");
BrandIndex["365"]=new Array("360","面部护理");
BrandIndex["370"]=new Array("360","眼部护理");
BrandIndex["375"]=new Array("360","彩妆系列");
BrandIndex["380"]=new Array("360","礼盒套装");
BrandIndex["360"]=new Array("68","特殊护理系列","365","370","375","380");
BrandIndex["72"]=new Array("355","面部护理");
BrandIndex["71"]=new Array("355","眼部护理");
BrandIndex["211"]=new Array("355","身体护理");
BrandIndex["69"]=new Array("355","彩妆系列");
BrandIndex["70"]=new Array("355","礼盒套装");
BrandIndex["355"]=new Array("68","老款系列","72","71","211","69","70");
BrandIndex["68"]=new Array("268","TIO/日本资生堂·凉颜","356","398","358","357","359","94","4","360","355");
BrandIndex["414"]=new Array("167","面部护理");
BrandIndex["415"]=new Array("167","身体护理");
BrandIndex["416"]=new Array("167","化妆工具");
BrandIndex["167"]=new Array("268","Mocheer/日本門前一草","414","415","416");
BrandIndex["417"]=new Array("326","面部护肤");
BrandIndex["326"]=new Array("268","日本/SANA豆乳","417");
BrandIndex["158"]=new Array("157","防晒系列");
BrandIndex["159"]=new Array("157","彩妆系列");
BrandIndex["160"]=new Array("157","礼盒套装");
BrandIndex["161"]=new Array("157","美发护发");
BrandIndex["162"]=new Array("157","男士护理");
BrandIndex["163"]=new Array("157","身体护理");
BrandIndex["164"]=new Array("157","手部护理");
BrandIndex["165"]=new Array("157","眼部护理");
BrandIndex["166"]=new Array("157","面部护理");
BrandIndex["157"]=new Array("268","Shiseido/日本资生堂","158","159","160","161","162","163","164","165","166");
BrandIndex["149"]=new Array("148","彩妆系列");
BrandIndex["150"]=new Array("148","礼盒套装");
BrandIndex["151"]=new Array("148","美发护发");
BrandIndex["152"]=new Array("148","男士护理");
BrandIndex["153"]=new Array("148","身体护理");
BrandIndex["154"]=new Array("148","手部护理");
BrandIndex["155"]=new Array("148","眼部护理");
BrandIndex["156"]=new Array("148","面部护理");
BrandIndex["148"]=new Array("268","Kose/日本高丝","149","150","151","152","153","154","155","156");
BrandIndex["140"]=new Array("139","彩妆系列");
BrandIndex["141"]=new Array("139","礼盒套装");
BrandIndex["142"]=new Array("139","美发护发");
BrandIndex["143"]=new Array("139","男士护理");
BrandIndex["144"]=new Array("139","身体护理");
BrandIndex["145"]=new Array("139","手部护理");
BrandIndex["146"]=new Array("139","眼部护理");
BrandIndex["147"]=new Array("139","面部护理");
BrandIndex["139"]=new Array("268","Kanebo/日本嘉娜宝","140","141","142","143","144","145","146","147");
BrandIndex["261"]=new Array("268","日本/DHC");
BrandIndex["217"]=new Array("216","护肤");
BrandIndex["216"]=new Array("268","OMI/近江兄弟","217");
BrandIndex["268"]=new Array("0","日本品牌","68","167","326","157","148","139","261","216");
BrandIndex["446"]=new Array("269","欧美热销产品");
BrandIndex["83"]=new Array("79","眼部护理");
BrandIndex["84"]=new Array("79","面部护理");
BrandIndex["80"]=new Array("79","身体护理");
BrandIndex["81"]=new Array("79","彩妆系列");
BrandIndex["82"]=new Array("79","礼盒套装");
BrandIndex["79"]=new Array("269","Lancome/兰蔻","83","84","80","81","82");
BrandIndex["441"]=new Array("440","身体护理");
BrandIndex["442"]=new Array("440","面部护理");
BrandIndex["443"]=new Array("440","美发沐浴");
BrandIndex["440"]=new Array("269","Mustela法国妙思乐（婴幼儿）","441","442","443");
BrandIndex["74"]=new Array("73","身体护理");
BrandIndex["75"]=new Array("73","彩妆系列");
BrandIndex["76"]=new Array("73","礼盒套装");
BrandIndex["77"]=new Array("73","眼部护理");
BrandIndex["78"]=new Array("73","面部护理");
BrandIndex["73"]=new Array("269","L’oreal/欧莱雅","74","75","76","77","78");
BrandIndex["100"]=new Array("96","面部护理");
BrandIndex["99"]=new Array("96","眼部护理");
BrandIndex["97"]=new Array("96","彩妆系列");
BrandIndex["102"]=new Array("96","香水系列");
BrandIndex["98"]=new Array("96","礼盒套装");
BrandIndex["96"]=new Array("269","EsteeLauder/雅诗兰黛","100","99","97","102","98");
BrandIndex["399"]=new Array("397","手部护理");
BrandIndex["400"]=new Array("397","身体护理");
BrandIndex["403"]=new Array("401","玫瑰嫩白系列");
BrandIndex["404"]=new Array("401","洋甘菊防敏系列");
BrandIndex["405"]=new Array("401","茶树祛痘祛印系列");
BrandIndex["408"]=new Array("401","眼部护理");
BrandIndex["401"]=new Array("397","面部护理","403","404","405","408");
BrandIndex["402"]=new Array("397","男士护理");
BrandIndex["397"]=new Array("269","瑰铂翠·柏翠丝","399","400","401","402");
BrandIndex["407"]=new Array("269","Chanel香奈儿");
BrandIndex["433"]=new Array("269","Clarins/娇韵诗");
BrandIndex["434"]=new Array("269","YSL圣罗兰");
BrandIndex["437"]=new Array("269","Givenchy/纪梵希");
BrandIndex["438"]=new Array("269","欧美流行产品");
BrandIndex["109"]=new Array("108","彩妆系列");
BrandIndex["110"]=new Array("108","礼盒套装");
BrandIndex["111"]=new Array("108","眼部护理");
BrandIndex["112"]=new Array("108","面部护理");
BrandIndex["108"]=new Array("269","Clinique/倩碧","109","110","111","112");
BrandIndex["116"]=new Array("113","面部护理");
BrandIndex["115"]=new Array("113","眼部护理");
BrandIndex["114"]=new Array("113","彩妆系列");
BrandIndex["113"]=new Array("269","CD/迪奥","116","115","114");
BrandIndex["1"]=new Array("269","法国Avene/雅漾");
BrandIndex["410"]=new Array("269","蜜丝佛陀/Max Factor");
BrandIndex["424"]=new Array("269","丝塔芙/CETAPHIL");
BrandIndex["118"]=new Array("117","眼部护理");
BrandIndex["119"]=new Array("117","面部护理(面膜)");
BrandIndex["117"]=new Array("269","Borghese/贝佳斯","118","119");
BrandIndex["104"]=new Array("101","面部护理");
BrandIndex["103"]=new Array("101","身体护理");
BrandIndex["39"]=new Array("101","香水系列");
BrandIndex["101"]=new Array("269","Elizabeth Arden/雅顿","104","103","39");
BrandIndex["106"]=new Array("105","美发护发");
BrandIndex["107"]=new Array("105","身体保养");
BrandIndex["105"]=new Array("269","DOVE/多芬","106","107");
BrandIndex["67"]=new Array("66","彩妆");
BrandIndex["66"]=new Array("269","BOBBI BROWN/波比布朗","67");
BrandIndex["421"]=new Array("420","彩妆系列");
BrandIndex["420"]=new Array("269","Benefit/贝玲妃","421");
BrandIndex["304"]=new Array("269","美国Thayers/金缕梅");
BrandIndex["353"]=new Array("269","美国Coppertone/水宝宝");
BrandIndex["325"]=new Array("269","Adidas/阿迪达斯");
BrandIndex["219"]=new Array("218","彩妆");
BrandIndex["220"]=new Array("218","护肤");
BrandIndex["218"]=new Array("269","Kiehl`s/契尔氏","219","220");
BrandIndex["423"]=new Array("422","彩妆系列");
BrandIndex["422"]=new Array("269","贝德玛/BIODERMA","423");
BrandIndex["222"]=new Array("221","护肤");
BrandIndex["223"]=new Array("221","眼部护理");
BrandIndex["221"]=new Array("269","TheBodyShop/美体小铺","222","223");
BrandIndex["431"]=new Array("20","试管香水");
BrandIndex["45"]=new Array("20","Dior /迪奥");
BrandIndex["50"]=new Array("20","Burberrys/巴宝莉");
BrandIndex["59"]=new Array("20","Givenchy/纪梵希");
BrandIndex["48"]=new Array("20","Calotine/歌宝婷");
BrandIndex["60"]=new Array("20","Davidoff/大卫杜夫");
BrandIndex["36"]=new Array("20","JLO/珍妮佛洛佩兹");
BrandIndex["31"]=new Array("20","Lancome/兰蔻");
BrandIndex["33"]=new Array("20","Kenzo/高田贤三");
BrandIndex["34"]=new Array("20","Versace/范思哲");
BrandIndex["42"]=new Array("20","Dunhill/登喜路");
BrandIndex["38"]=new Array("20","Guerlain娇兰");
BrandIndex["37"]=new Array("20","Gucci/古琦");
BrandIndex["44"]=new Array("20","CK/凯文克莱");
BrandIndex["46"]=new Array("20","Chanel/香奈儿");
BrandIndex["254"]=new Array("20","Ferragamo/佛莱格默");
BrandIndex["51"]=new Array("20","Boss/Boss");
BrandIndex["341"]=new Array("20","Harajuku Lovers/原宿");
BrandIndex["32"]=new Array("20","Lacoste/鳄鱼");
BrandIndex["30"]=new Array("20","S.T.Dupont/都彭");
BrandIndex["298"]=new Array("20","Bvlgari/宝格丽");
BrandIndex["299"]=new Array("20","Moschino/奧莉佛-梦仙奴");
BrandIndex["300"]=new Array("20","LANVIN/浪凡光韵");
BrandIndex["301"]=new Array("20","Armani/阿玛尼");
BrandIndex["302"]=new Array("20","Anna Sui安娜苏");
BrandIndex["303"]=new Array("20","Elizabeth Arden/雅顿");
BrandIndex["324"]=new Array("20","Adidas/阿迪达斯");
BrandIndex["41"]=new Array("20","其它香水");
BrandIndex["20"]=new Array("269","香水品牌","431","45","50","59","48","60","36","31","33","34","42","38","37","44","46","254","51","341","32","30","298","299","300","301","302","303","324","41");
BrandIndex["269"]=new Array("0","欧美品牌","446","79","440","73","96","397","407","433","434","437","438","108","113","1","410","424","117","101","105","66","420","304","353","325","218","422","221","20");
BrandIndex["26"]=new Array("270","贝罗/台湾");
BrandIndex["13"]=new Array("12","彩妆");
BrandIndex["14"]=new Array("12","护肤");
BrandIndex["12"]=new Array("270","我的美丽日记（面膜）","13","14");
BrandIndex["270"]=new Array("0","台湾品牌","26","12");
BrandIndex["432"]=new Array("281","国产香水");
BrandIndex["395"]=new Array("394","果荟椰纤面膜系列");
BrandIndex["396"]=new Array("394","花荟隐形面膜系列");
BrandIndex["409"]=new Array("394","蚕丝面膜");
BrandIndex["394"]=new Array("281","荟宝面膜--我的花果荟","395","396","409");
BrandIndex["419"]=new Array("418","面部护理");
BrandIndex["418"]=new Array("281","UNES/优理氏","419");
BrandIndex["320"]=new Array("319","面部护理");
BrandIndex["321"]=new Array("319","眼部护理");
BrandIndex["322"]=new Array("319","彩妆系列");
BrandIndex["323"]=new Array("319","礼盒套装");
BrandIndex["338"]=new Array("319","美发护发");
BrandIndex["339"]=new Array("319","身体护理");
BrandIndex["319"]=new Array("281","Sibelle/四季美人","320","321","322","323","338","339");
BrandIndex["354"]=new Array("281","丹希露～草本立纯B.B");
BrandIndex["240"]=new Array("281","千纤草");
BrandIndex["308"]=new Array("307","护肤系列");
BrandIndex["309"]=new Array("307","美发护发");
BrandIndex["307"]=new Array("281","吉烈绅士护理","308","309");
BrandIndex["425"]=new Array("313","眼部彩妆");
BrandIndex["426"]=new Array("313","面部彩妆");
BrandIndex["427"]=new Array("313","唇部彩妆");
BrandIndex["313"]=new Array("281","BOB彩妆","425","426","427");
BrandIndex["315"]=new Array("314","玫瑰系列～红润保湿");
BrandIndex["316"]=new Array("314","石榴系列～柔润美肌");
BrandIndex["317"]=new Array("314","橄榄系列～补水润白");
BrandIndex["314"]=new Array("281","AromaTherapy/采媚香薰","315","316","317");
BrandIndex["310"]=new Array("281","其他国货");
BrandIndex["281"]=new Array("0","国货精品","432","394","418","319","354","240","307","313","314","310");
BrandIndex["430"]=new Array("429","洗肤系列");
BrandIndex["429"]=new Array("428","熊霸天下","430");
BrandIndex["428"]=new Array("0","儿童婴儿产品","429");
BrandIndex["318"]=new Array("296","护肤");
BrandIndex["387"]=new Array("296","彩妆");
BrandIndex["296"]=new Array("0","牛耳/大Ｓ等明星推荐","318","387");
BrandIndex["385"]=new Array("0","美容美体工具");
BrandIndex["386"]=new Array("0","英皇植物精油");
BrandIndex["384"]=new Array("0","季节性产品");
BrandIndex["382"]=new Array("381","限时秒杀");
BrandIndex["383"]=new Array("381","清仓特卖");
BrandIndex["381"]=new Array("0","特卖产品","382","383");
BrandIndex["93"]=new Array("0","其它产品");
BrandIndex["439"]=new Array("0","澳纽等进口保健品");
BrandIndex["0"]=new Array("","","267","268","269","270","281","428","296","385","386","384","381","93","439");
BrandIndex["hot"]=new Array("","热销品牌","313","429","353","1","410","327","432","381","262","224","95","306","68","167");
CategoryIndex["99"]=new Array("0","限时促销");
CategoryIndex["94"]=new Array("48","彩护套装");
CategoryIndex["49"]=new Array("48","洗护套装");
CategoryIndex["96"]=new Array("48","香水套装");
CategoryIndex["51"]=new Array("48","其它套装");
CategoryIndex["48"]=new Array("0","礼盒套装","94","49","96","51");
CategoryIndex["56"]=new Array("44","祛角质");
CategoryIndex["57"]=new Array("44","清洁霜");
CategoryIndex["58"]=new Array("44","洗面奶");
CategoryIndex["59"]=new Array("44","按摩霜");
CategoryIndex["60"]=new Array("44","皂类");
CategoryIndex["44"]=new Array("4","洁面","56","57","58","59","60");
CategoryIndex["61"]=new Array("53","水");
CategoryIndex["62"]=new Array("53","乳");
CategoryIndex["63"]=new Array("53","霜");
CategoryIndex["98"]=new Array("53","胶(露)");
CategoryIndex["53"]=new Array("4","护肤","61","62","63","98");
CategoryIndex["54"]=new Array("4","精华");
CategoryIndex["55"]=new Array("4","面膜");
CategoryIndex["64"]=new Array("4","其他");
CategoryIndex["4"]=new Array("0","面部护理","44","53","54","55","64");
CategoryIndex["6"]=new Array("0","唇部护理");
CategoryIndex["5"]=new Array("0","眼部护理");
CategoryIndex["7"]=new Array("0","鼻部护理");
CategoryIndex["8"]=new Array("0","手部护理");
CategoryIndex["9"]=new Array("0","足部护理");
CategoryIndex["10"]=new Array("0","颈部护理");
CategoryIndex["11"]=new Array("0","口腔护理");
CategoryIndex["12"]=new Array("0","身体护理");
CategoryIndex["13"]=new Array("0","沐浴产品");
CategoryIndex["14"]=new Array("0","美发护发");
CategoryIndex["17"]=new Array("18","卸妆产品");
CategoryIndex["29"]=new Array("18","粉底/隔离/妆前乳/BB霜/CC霜");
CategoryIndex["30"]=new Array("18","粉饼/蜜粉/散粉");
CategoryIndex["38"]=new Array("18","眉笔/眉粉/眉饼");
CategoryIndex["37"]=new Array("18","眼线笔/眼线液");
CategoryIndex["36"]=new Array("18","眼影");
CategoryIndex["34"]=new Array("18","睫毛膏");
CategoryIndex["35"]=new Array("18","睫毛增长液");
CategoryIndex["42"]=new Array("18","腮红/胭脂");
CategoryIndex["43"]=new Array("18","唇彩/唇蜜/唇膏/口红");
CategoryIndex["40"]=new Array("18","唇笔/唇线笔");
CategoryIndex["47"]=new Array("18","修颜/高亮/阴影粉");
CategoryIndex["41"]=new Array("18","遮瑕笔/遮瑕膏/粉条");
CategoryIndex["45"]=new Array("18","指甲油/美甲产品");
CategoryIndex["46"]=new Array("18","身体彩绘");
CategoryIndex["39"]=new Array("18","双眼皮胶");
CategoryIndex["18"]=new Array("0","彩妆系列","17","29","30","38","37","36","34","35","42","43","40","47","41","45","46","39");
CategoryIndex["104"]=new Array("0","男士护理");
CategoryIndex["128"]=new Array("19","国产香水");
CategoryIndex["127"]=new Array("19","试管香水");
CategoryIndex["95"]=new Array("19","香体露/走珠/香体喷雾");
CategoryIndex["69"]=new Array("19","小样香水");
CategoryIndex["70"]=new Array("19","JAGUAR/积架");
CategoryIndex["90"]=new Array("19","EsteeLauder/雅诗兰黛");
CategoryIndex["71"]=new Array("19","Burberrys/巴宝莉");
CategoryIndex["93"]=new Array("19","CD/迪奥");
CategoryIndex["92"]=new Array("19","Clinique倩碧");
CategoryIndex["91"]=new Array("19","Lancome/兰蔻");
CategoryIndex["89"]=new Array("19","ElizabethArden/雅顿");
CategoryIndex["88"]=new Array("19","annasui/安娜苏");
CategoryIndex["72"]=new Array("19","Givenchy/纪梵希");
CategoryIndex["73"]=new Array("19","Calotine/歌宝婷");
CategoryIndex["74"]=new Array("19","Davidoff/大卫杜夫");
CategoryIndex["75"]=new Array("19","JLO/珍妮佛洛佩兹");
CategoryIndex["76"]=new Array("19","Kenzo/高田贤三");
CategoryIndex["77"]=new Array("19","Versace/范思哲");
CategoryIndex["79"]=new Array("19","Guerlain娇兰");
CategoryIndex["78"]=new Array("19","Dunhill/登喜路");
CategoryIndex["101"]=new Array("19","Moschino/奧莉佛 梦仙奴");
CategoryIndex["100"]=new Array("19","Bvlgari/宝格丽");
CategoryIndex["103"]=new Array("19","LANVIN/浪凡光韵");
CategoryIndex["102"]=new Array("19","Armani/阿玛尼");
CategoryIndex["80"]=new Array("19","Gucci/古琦");
CategoryIndex["81"]=new Array("19","CK/凯文克莱");
CategoryIndex["82"]=new Array("19","Chanel/香奈儿");
CategoryIndex["83"]=new Array("19","Ferragamo/佛莱格默");
CategoryIndex["84"]=new Array("19","Boss/Boss");
CategoryIndex["85"]=new Array("19","Lacoste/鳄鱼");
CategoryIndex["86"]=new Array("19","S.T.Dupont/都彭");
CategoryIndex["125"]=new Array("19","Adidas/阿迪达斯");
CategoryIndex["87"]=new Array("19","其它香水");
CategoryIndex["19"]=new Array("0","香水系列","128","127","95","69","70","90","71","93","92","91","89","88","72","73","74","75","76","77","79","78","101","100","103","102","80","81","82","83","84","85","86","125","87");
CategoryIndex["67"]=new Array("20","单方精油");
CategoryIndex["68"]=new Array("20","复方精油");
CategoryIndex["20"]=new Array("0","植物精油","67","68");
CategoryIndex["15"]=new Array("31","防晒修护");
CategoryIndex["16"]=new Array("31","防冻防裂");
CategoryIndex["52"]=new Array("31","祛斑祛痘祛黑头");
CategoryIndex["21"]=new Array("31","瘦身纤体");
CategoryIndex["22"]=new Array("31","美乳丰胸");
CategoryIndex["65"]=new Array("31","黑发产品");
CategoryIndex["23"]=new Array("31","增高产品");
CategoryIndex["126"]=new Array("31","脱毛除毛");
CategoryIndex["97"]=new Array("31","其它功效");
CategoryIndex["31"]=new Array("0","功效产品","15","16","52","21","22","65","23","126","97");
CategoryIndex["24"]=new Array("0","美容工具");
CategoryIndex["106"]=new Array("105","T恤/打底衫");
CategoryIndex["107"]=new Array("105","衬衫");
CategoryIndex["119"]=new Array("105","雪纺衫");
CategoryIndex["108"]=new Array("105","卫衣");
CategoryIndex["109"]=new Array("105","连衣裙/裙衣");
CategoryIndex["110"]=new Array("105","时尚马甲");
CategoryIndex["111"]=new Array("105","西装");
CategoryIndex["112"]=new Array("105","小外套/小夹克");
CategoryIndex["113"]=new Array("105","小吊带/背心");
CategoryIndex["114"]=new Array("105","针织上衣/毛衣");
CategoryIndex["115"]=new Array("105","中/长风衣");
CategoryIndex["116"]=new Array("105","皮衣");
CategoryIndex["118"]=new Array("105","牛仔");
CategoryIndex["117"]=new Array("105","棉衣");
CategoryIndex["105"]=new Array("25","上衣","106","107","119","108","109","110","111","112","113","114","115","116","118","117");
CategoryIndex["121"]=new Array("120","时尚小短裤");
CategoryIndex["122"]=new Array("120","百搭半身裙");
CategoryIndex["123"]=new Array("120","中裤/长裤");
CategoryIndex["120"]=new Array("25","下装","121","122","123");
CategoryIndex["124"]=new Array("25","围巾/饰品类");
CategoryIndex["25"]=new Array("0","精品服饰","105","120","124");
CategoryIndex["26"]=new Array("0","生活居家");
CategoryIndex["27"]=new Array("0","办公文具");
CategoryIndex["28"]=new Array("0","其它产品");
CategoryIndex["129"]=new Array("0","进口保健品食品");
CategoryIndex["0"]=new Array("","","99","48","4","6","5","7","8","9","10","11","12","13","14","18","104","19","20","31","24","25","26","27","28","129");


function gmSwitch(m)
{ m=m.parentNode;
        if(m.className=="gMenuOpen")m.className="gMenuClose";
        else if(m.className=="gMenuClose")m.className="gMenuOpen";
        else
        { m=m.getElementsByTagName("A");
          if(m && m.length)self.location.href=m[0].href;
        }
}
function gmEnter(m){m.style.backgroundColor="#FFE3D2";}
function gmLeave(m){m.style.backgroundColor="";}

function GenSingleBrand(brandid)
{ var menu_class,categorycode="";
  var brandid2=(brandid=="hot")?"0":brandid;
        var brand_file="brandlist.htm?cid="+brandid2;
  if(BrandIndex[brandid].length>2)
  { if(brandid2==CurrentBrand)menu_class="gMenuOpen";
    else menu_class="gMenuClose";
  }
  else
  { menu_class="gMenuEmpty";
  }
  categorycode+='<TR><TD class="'+menu_class+'"><span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="'+brand_file+'">'+BrandIndex[brandid][1]+'</a></span></span><div>';
  subsort=BrandIndex[brandid];
  subcount=subsort.length;
   
  for(j=2;j<subcount;j++)
  { categorycode+='<a href="brandlist.htm?cid='+subsort[j]+'">'+BrandIndex[subsort[j]][1]+'</a>'; 
  } 
  categorycode+='</div></TD></TR>';
  return categorycode
}

function ShowBrandGuider(){
  var i,j,RootBrand,sortcount,subcount,subsort,parent,brandcode="";
  if(!CurrentBrand)CurrentBrand="0";
  if(CurrentBrand!="0"){
    if(BrandIndex[CurrentBrand].length<3 && BrandIndex[CurrentBrand][0]!="0"){
      CurrentBrand=BrandIndex[CurrentBrand][0];
    }
  }
  RootBrand=(BrandIndex[CurrentBrand][0]=="0")?"0":CurrentBrand;
  
  brandcode+='<TABLE cellSpacing=0 cellPadding=0 width="188" align="center" border="0">';
  brandcode+='<TR>';
  brandcode+='   <TD background="images/guide_brand.gif" width="188" height="31"></TD>';
  brandcode+='</TR>';
  brandcode+='<TR>';
  brandcode+=' <TD vAlign="top" align="center" width="100%" style="background-image:url(images/toolbd_mid.gif);padding-left:10px;padding-right:3px">';
  brandcode+='      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">';
         
  if(RootBrand!="0")
  { brandcode+='<TR style="CURSOR: pointer" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)" onclick="gmSwitch(this)" height=24 valign="middle"><TD> <IMG width=20 height=20 border=0 src="images/guidefold2.gif" align="absMiddle"> <a href="brandlist.htm?cid='+BrandIndex[RootBrand][0]+'"><font color=#FF0000>返回上级分类</font></a></TD></TR>';
        brandcode+=GenSingleBrand(RootBrand);
  }
  else
  { brandcode+=GenSingleBrand("hot");
    subsort=BrandIndex[RootBrand];
        sortcount=subsort.length;
    for(i=2;i<sortcount;i++)
    { brandcode+=GenSingleBrand(subsort[i]);
    }   
  }
  brandcode+='      </table>';
  brandcode+='   </TD>';
  brandcode+='</TR><TR><TD background="images/toolbd_bot.gif" width="188" height="6"></TD></TR>';
  brandcode+='</TABLE><img src="images/index_4.gif" width="190" height="12">';
  document.write(brandcode);
}

function GenSingleCategory(categoryid)
{ var guide_fold_image,guide_fold_disp_opt,catcode="";
        if(CategoryIndex[categoryid].length>2)
        { if(categoryid==CurrentCategory)menu_class="gMenuOpen";
                else menu_class="gMenuClose";
        }
        else
        { menu_class="gMenuEmpty";
        }

  catcode+='<TR><TD class="'+menu_class+'"><span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="catlist.htm?cid='+categoryid+'">'+CategoryIndex[categoryid][1]+'</a></span></span><div>';

  subsort=CategoryIndex[categoryid];
  subcount=subsort.length;
  
  for(j=2;j<subcount;j++)
  {  catcode+='<a href="catlist.htm?cid='+subsort[j]+'">'+CategoryIndex[subsort[j]][1]+'</a>';  
  } 
  catcode+="</div></td></tr>";
  
  return catcode
}

function ShowCategoryGuider()
{ var i,j,RootCategory,sortcount,subcount,subsort,parent,catcode="";
  if(!CurrentCategory)CurrentCategory="0";
  if(CurrentCategory!="0")
  { if(CategoryIndex[CurrentCategory].length<3 && CategoryIndex[CurrentCategory][0]!="0")
    { CurrentCategory=CategoryIndex[CurrentCategory][0];
    }
  }
  RootCategory=(CategoryIndex[CurrentCategory][0]=="0")?"0":CurrentCategory;
  
  
  catcode+='<TABLE cellSpacing=0 cellPadding=0 width="188" align="center" border="0">';
  catcode+='<TR>';
  catcode+='   <TD background="images/guide_property.gif" width="188" height="31"></TD>';
  catcode+='</TR>';
  catcode+='<TR>';
  catcode+=' <TD vAlign="top" align="center" width="100%" style="background-image:url(images/toolbd_mid.gif);padding-left:10px;padding-right:3px">';
  catcode+='      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">';
  
  if(RootCategory!="0")
  { catcode+='<TR style="CURSOR: pointer" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)" onclick="gmSwitch(this)" height=24 valign="middle"><TD> <IMG width=20 height=20 border=0 src="images/guidefold2.gif" align="absMiddle"> <a href="catlist.htm?cid='+CategoryIndex[RootCategory][0]+'"><font color=#FF0000>返回上级分类</font></a></TD></TR>';
        catcode+=GenSingleCategory(RootCategory);
  }
  else
  { subsort=CategoryIndex[RootCategory];
        sortcount=subsort.length;
    for(i=2;i<sortcount;i++)
    { catcode+=GenSingleCategory(subsort[i]);
    }   
  }
  catcode+='      </table>';
  catcode+='   </TD>';
  catcode+='</TR><TR><TD background="images/toolbd_bot.gif" width="188" height="6"></TD></TR>';
  catcode+='</TABLE><img src="images/index_4.gif" width="190" height="12">';
  document.write(catcode);
}

if(self.location.href.indexOf("catlist")>0)
{ CurrentCategory=htmRequest("cid")
  ShowCategoryGuider();
} 
else
{ CurrentBrand=htmRequest("cid") 
  ShowBrandGuider();
  ShowCategoryGuider();
}

