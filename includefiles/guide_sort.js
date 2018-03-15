var CurrentCategory,CurrentProperty;	
var CategoryIndex=new Array();
var PropertyIndex=new Array();
CategoryIndex["210"]=new Array("95","逆时空还童系列");
CategoryIndex["260"]=new Array("95","收毛孔细致控油系列");
CategoryIndex["271"]=new Array("95","360°水动力保湿系列");
CategoryIndex["283"]=new Array("95","DNA焕白系列");
CategoryIndex["340"]=new Array("95","焕活青春胶原系列");
CategoryIndex["406"]=new Array("95","极地再生冰芯水系列");
CategoryIndex["389"]=new Array("95","祛痘祛斑祛黑头系列");
CategoryIndex["390"]=new Array("95","防晒彩妆系列");
CategoryIndex["388"]=new Array("95","男士系列");
CategoryIndex["95"]=new Array("267","韩国爱茉莉·尚姬泉","210","260","271","283","340","406","389","390","388");
CategoryIndex["392"]=new Array("391","护肤系列");
CategoryIndex["393"]=new Array("391","彩妆系列");
CategoryIndex["391"]=new Array("262","洋甘菊舒颜美肌修护系列","392","393");
CategoryIndex["343"]=new Array("342","护肤系列");
CategoryIndex["344"]=new Array("342","彩妆系列");
CategoryIndex["348"]=new Array("342","美发护发");
CategoryIndex["342"]=new Array("262","蓝莓焕颜新活嫩白系列","343","344","348");
CategoryIndex["345"]=new Array("282","护肤系列");
CategoryIndex["346"]=new Array("282","彩妆系列");
CategoryIndex["347"]=new Array("282","美发护发");
CategoryIndex["282"]=new Array("262","龙舌兰新生赋活润颜水嫩系列","345","346","347");
CategoryIndex["350"]=new Array("349","护肤系列");
CategoryIndex["351"]=new Array("349","彩妆系列");
CategoryIndex["352"]=new Array("349","美发护发");
CategoryIndex["349"]=new Array("262","五味子顶级美白悦颜系列","350","351","352");
CategoryIndex["262"]=new Array("267","HANUAN/韩媛","391","342","282","349");
CategoryIndex["227"]=new Array("226","护肤系列");
CategoryIndex["228"]=new Array("226","彩妆系列");
CategoryIndex["229"]=new Array("226","美发护发");
CategoryIndex["226"]=new Array("224","韩伊橄榄系列","227","228","229");
CategoryIndex["231"]=new Array("225","护肤系列");
CategoryIndex["232"]=new Array("225","彩妆系列");
CategoryIndex["233"]=new Array("225","美发护发");
CategoryIndex["225"]=new Array("224","韩伊玫瑰系列","231","232","233");
CategoryIndex["256"]=new Array("255","水养护颜系列");
CategoryIndex["259"]=new Array("255","净肤平衡系列");
CategoryIndex["258"]=new Array("255","紧肤抗皱系列");
CategoryIndex["257"]=new Array("255","极致美白系列");
CategoryIndex["255"]=new Array("224","韩伊水呼吸系列","256","259","258","257");
CategoryIndex["284"]=new Array("224","韩伊水肌源系列");
CategoryIndex["335"]=new Array("334","护肤系列");
CategoryIndex["336"]=new Array("334","彩妆系列");
CategoryIndex["337"]=new Array("334","美容美发");
CategoryIndex["334"]=new Array("224","希伯雅橄榄$蓝莓系列","335","336","337");
CategoryIndex["224"]=new Array("267","CO.E/韩国韩伊","226","225","255","284","334");
CategoryIndex["306"]=new Array("267","韩国/SKIN79");
CategoryIndex["168"]=new Array("267","韩国/MISSHA");
CategoryIndex["208"]=new Array("204","面部护理");
CategoryIndex["209"]=new Array("204","手部护理");
CategoryIndex["207"]=new Array("204","眼部护理");
CategoryIndex["206"]=new Array("204","礼盒套装");
CategoryIndex["205"]=new Array("204","彩妆系列");
CategoryIndex["204"]=new Array("267","TheFaceShop/韩国","208","209","207","206","205");
CategoryIndex["203"]=new Array("201","面部护理");
CategoryIndex["202"]=new Array("201","眼部护理");
CategoryIndex["230"]=new Array("201","彩妆系列");
CategoryIndex["201"]=new Array("267","SkinFood/韩国","203","202","230");
CategoryIndex["328"]=new Array("327","面部护理");
CategoryIndex["329"]=new Array("327","眼部护理");
CategoryIndex["330"]=new Array("327","身体护理");
CategoryIndex["331"]=new Array("327","美容美发");
CategoryIndex["332"]=new Array("327","彩妆系列");
CategoryIndex["333"]=new Array("327","礼盒套装");
CategoryIndex["327"]=new Array("267","韩国baviphat/芭比娃娃","328","329","330","331","332","333");
CategoryIndex["195"]=new Array("191","面部护理");
CategoryIndex["194"]=new Array("191","眼部护理");
CategoryIndex["193"]=new Array("191","礼盒套装");
CategoryIndex["192"]=new Array("191","彩妆系列");
CategoryIndex["191"]=new Array("267","Charmzone/韩国婵真","195","194","193","192");
CategoryIndex["200"]=new Array("196","面部护理");
CategoryIndex["199"]=new Array("196","眼部护理");
CategoryIndex["198"]=new Array("196","礼盒套装");
CategoryIndex["197"]=new Array("196","彩妆系列");
CategoryIndex["196"]=new Array("267","Deoproce/韩国三星","200","199","198","197");
CategoryIndex["265"]=new Array("177","中国产");
CategoryIndex["264"]=new Array("177","韩国产");
CategoryIndex["177"]=new Array("267","Vov/韩国Vov","265","264");
CategoryIndex["175"]=new Array("173","面部护理(防晒)");
CategoryIndex["174"]=new Array("173","彩 妆(散粉)");
CategoryIndex["173"]=new Array("267","DoDo/韩国嘟嘟","175","174");
CategoryIndex["185"]=new Array("181","面部护理");
CategoryIndex["184"]=new Array("181","眼部护理");
CategoryIndex["183"]=new Array("181","礼盒套装");
CategoryIndex["182"]=new Array("181","彩妆系列");
CategoryIndex["181"]=new Array("267","Laneige/韩国兰芝","185","184","183","182");
CategoryIndex["172"]=new Array("169","面部护理");
CategoryIndex["171"]=new Array("169","美发护发");
CategoryIndex["170"]=new Array("169","礼盒套装");
CategoryIndex["169"]=new Array("267","Amore/韩国爱茉莉","172","171","170");
CategoryIndex["311"]=new Array("267","Love/韩国永爱");
CategoryIndex["312"]=new Array("267","其他韩国品牌");
CategoryIndex["267"]=new Array("0","韩国品牌","95","262","224","306","168","204","201","327","191","196","177","173","181","169","311","312");
CategoryIndex["398"]=new Array("68","水润柔白系列");
CategoryIndex["363"]=new Array("356","面部护理");
CategoryIndex["368"]=new Array("356","眼部护理");
CategoryIndex["374"]=new Array("356","彩妆系列");
CategoryIndex["378"]=new Array("356","礼盒套装");
CategoryIndex["356"]=new Array("68","海洋鲜活保湿系列","363","368","374","378");
CategoryIndex["362"]=new Array("358","面部护理");
CategoryIndex["367"]=new Array("358","眼部护理");
CategoryIndex["372"]=new Array("358","彩妆系列");
CategoryIndex["377"]=new Array("358","礼盒套装");
CategoryIndex["358"]=new Array("68","弹力提升系列","362","367","372","377");
CategoryIndex["364"]=new Array("357","面部护理");
CategoryIndex["369"]=new Array("357","眼部护理");
CategoryIndex["373"]=new Array("357","彩妆系列");
CategoryIndex["379"]=new Array("357","礼盒套装");
CategoryIndex["357"]=new Array("68","晶纯皙白系列","364","369","373","379");
CategoryIndex["361"]=new Array("359","面部护理");
CategoryIndex["366"]=new Array("359","眼部护理");
CategoryIndex["371"]=new Array("359","彩妆系列");
CategoryIndex["376"]=new Array("359","礼盒套装");
CategoryIndex["359"]=new Array("68","清·调·补（明星）系列","361","366","371","376");
CategoryIndex["94"]=new Array("68","眼部养护系列");
CategoryIndex["365"]=new Array("360","面部护理");
CategoryIndex["370"]=new Array("360","眼部护理");
CategoryIndex["375"]=new Array("360","彩妆系列");
CategoryIndex["380"]=new Array("360","礼盒套装");
CategoryIndex["360"]=new Array("68","特殊护理系列","365","370","375","380");
CategoryIndex["4"]=new Array("68","洗护系列");
CategoryIndex["72"]=new Array("355","面部护理");
CategoryIndex["71"]=new Array("355","眼部护理");
CategoryIndex["211"]=new Array("355","身体护理");
CategoryIndex["69"]=new Array("355","彩妆系列");
CategoryIndex["70"]=new Array("355","礼盒套装");
CategoryIndex["355"]=new Array("68","老款系列","72","71","211","69","70");
CategoryIndex["68"]=new Array("268","TIO/日本资生堂·凉颜","398","356","358","357","359","94","360","4","355");
CategoryIndex["167"]=new Array("268","Mocheer/日本門前一草");
CategoryIndex["326"]=new Array("268","日本/SANA豆乳");
CategoryIndex["166"]=new Array("157","面部护理");
CategoryIndex["165"]=new Array("157","眼部护理");
CategoryIndex["164"]=new Array("157","手部护理");
CategoryIndex["163"]=new Array("157","身体护理");
CategoryIndex["162"]=new Array("157","男士护理");
CategoryIndex["161"]=new Array("157","美发护发");
CategoryIndex["160"]=new Array("157","礼盒套装");
CategoryIndex["159"]=new Array("157","彩妆系列");
CategoryIndex["158"]=new Array("157","防晒系列");
CategoryIndex["157"]=new Array("268","Shiseido/日本资生堂","166","165","164","163","162","161","160","159","158");
CategoryIndex["156"]=new Array("148","面部护理");
CategoryIndex["155"]=new Array("148","眼部护理");
CategoryIndex["154"]=new Array("148","手部护理");
CategoryIndex["153"]=new Array("148","身体护理");
CategoryIndex["152"]=new Array("148","男士护理");
CategoryIndex["151"]=new Array("148","美发护发");
CategoryIndex["150"]=new Array("148","礼盒套装");
CategoryIndex["149"]=new Array("148","彩妆系列");
CategoryIndex["148"]=new Array("268","Kose/日本高丝","156","155","154","153","152","151","150","149");
CategoryIndex["147"]=new Array("139","面部护理");
CategoryIndex["146"]=new Array("139","眼部护理");
CategoryIndex["145"]=new Array("139","手部护理");
CategoryIndex["144"]=new Array("139","身体护理");
CategoryIndex["143"]=new Array("139","男士护理");
CategoryIndex["142"]=new Array("139","美发护发");
CategoryIndex["141"]=new Array("139","礼盒套装");
CategoryIndex["140"]=new Array("139","彩妆系列");
CategoryIndex["139"]=new Array("268","Kanebo/日本嘉娜宝","147","146","145","144","143","142","141","140");
CategoryIndex["266"]=new Array("268","POLA(pdc)/日本第一药妆");
CategoryIndex["261"]=new Array("268","日本/DHC");
CategoryIndex["65"]=new Array("61","面部护理");
CategoryIndex["64"]=new Array("61","眼部护理");
CategoryIndex["63"]=new Array("61","礼盒套装");
CategoryIndex["62"]=new Array("61","彩妆系列");
CategoryIndex["61"]=new Array("268","Za/姿芮","65","64","63","62");
CategoryIndex["129"]=new Array("126","护 肤");
CategoryIndex["128"]=new Array("126","彩 妆");
CategoryIndex["127"]=new Array("126","香 水");
CategoryIndex["126"]=new Array("268","Anna sui/安娜苏","129","128","127");
CategoryIndex["217"]=new Array("216","护肤");
CategoryIndex["216"]=new Array("268","OMI/近江兄弟","217");
CategoryIndex["268"]=new Array("0","日本品牌","68","167","326","157","148","139","266","261","61","126","216");
CategoryIndex["399"]=new Array("397","手部护理");
CategoryIndex["403"]=new Array("401","玫瑰嫩白系列");
CategoryIndex["404"]=new Array("401","洋甘菊防敏系列");
CategoryIndex["405"]=new Array("401","茶树祛痘祛印系列");
CategoryIndex["401"]=new Array("397","面部护理","403","404","405");
CategoryIndex["400"]=new Array("397","身体护理");
CategoryIndex["402"]=new Array("397","男士护理");
CategoryIndex["397"]=new Array("269","瑰铂翠·柏翠丝","399","401","400","402");
CategoryIndex["1"]=new Array("269","法国Avene/雅漾");
CategoryIndex["353"]=new Array("269","美国Coppertone/水宝宝");
CategoryIndex["407"]=new Array("269","Chanel香奈儿");
CategoryIndex["125"]=new Array("120","面部护理");
CategoryIndex["124"]=new Array("120","眼部护理");
CategoryIndex["123"]=new Array("120","男士护理");
CategoryIndex["122"]=new Array("120","礼盒套装");
CategoryIndex["121"]=new Array("120","彩妆系列");
CategoryIndex["120"]=new Array("269","Biotherm/碧欧泉","125","124","123","122","121");
CategoryIndex["119"]=new Array("117","面部护理(面膜)");
CategoryIndex["118"]=new Array("117","眼部护理");
CategoryIndex["117"]=new Array("269","Borghese/贝佳斯","119","118");
CategoryIndex["116"]=new Array("113","面部护理");
CategoryIndex["115"]=new Array("113","眼部护理");
CategoryIndex["114"]=new Array("113","彩妆系列");
CategoryIndex["113"]=new Array("269","CD/迪奥","116","115","114");
CategoryIndex["112"]=new Array("108","面部护理");
CategoryIndex["111"]=new Array("108","眼部护理");
CategoryIndex["110"]=new Array("108","礼盒套装");
CategoryIndex["109"]=new Array("108","彩妆系列");
CategoryIndex["108"]=new Array("269","Clinique/倩碧","112","111","110","109");
CategoryIndex["107"]=new Array("105","身体保养");
CategoryIndex["106"]=new Array("105","美发护发");
CategoryIndex["105"]=new Array("269","DOVE/多芬","107","106");
CategoryIndex["104"]=new Array("101","面部护理");
CategoryIndex["103"]=new Array("101","身体护理");
CategoryIndex["39"]=new Array("101","香水系列");
CategoryIndex["101"]=new Array("269","Elizabeth Arden/雅顿","104","103","39");
CategoryIndex["100"]=new Array("96","面部护理");
CategoryIndex["99"]=new Array("96","眼部护理");
CategoryIndex["97"]=new Array("96","彩妆系列");
CategoryIndex["102"]=new Array("96","香水系列");
CategoryIndex["98"]=new Array("96","礼盒套装");
CategoryIndex["96"]=new Array("269","EsteeLauder/雅诗兰黛","100","99","97","102","98");
CategoryIndex["92"]=new Array("89","面部护理(喷雾)");
CategoryIndex["91"]=new Array("89","眼部护理");
CategoryIndex["89"]=new Array("269","Evian/依云","92","91");
CategoryIndex["83"]=new Array("79","眼部护理");
CategoryIndex["84"]=new Array("79","面部护理");
CategoryIndex["80"]=new Array("79","身体护理");
CategoryIndex["81"]=new Array("79","彩妆系列");
CategoryIndex["82"]=new Array("79","礼盒套装");
CategoryIndex["79"]=new Array("269","Lancome/兰蔻","83","84","80","81","82");
CategoryIndex["78"]=new Array("73","面部护理");
CategoryIndex["77"]=new Array("73","眼部护理");
CategoryIndex["76"]=new Array("73","礼盒套装");
CategoryIndex["75"]=new Array("73","彩妆系列");
CategoryIndex["74"]=new Array("73","身体护理");
CategoryIndex["73"]=new Array("269","L’oreal/欧莱雅","78","77","76","75","74");
CategoryIndex["67"]=new Array("66","彩妆");
CategoryIndex["66"]=new Array("269","BOBBI BROWN/波比布朗","67");
CategoryIndex["304"]=new Array("269","美国Thayers/金缕梅");
CategoryIndex["325"]=new Array("269","Adidas/阿迪达斯");
CategoryIndex["220"]=new Array("218","护肤");
CategoryIndex["219"]=new Array("218","彩妆");
CategoryIndex["218"]=new Array("269","Kiehl's/契尔氏","220","219");
CategoryIndex["222"]=new Array("221","护肤");
CategoryIndex["223"]=new Array("221","眼部护理");
CategoryIndex["221"]=new Array("269","TheBodyShop/美体小铺","222","223");
CategoryIndex["45"]=new Array("20","JAGUAR/积架");
CategoryIndex["50"]=new Array("20","Burberrys/巴宝莉");
CategoryIndex["59"]=new Array("20","Givenchy/纪梵希");
CategoryIndex["48"]=new Array("20","Calotine/歌宝婷");
CategoryIndex["60"]=new Array("20","Davidoff/大卫杜夫");
CategoryIndex["36"]=new Array("20","JLO/珍妮佛洛佩兹");
CategoryIndex["31"]=new Array("20","Lancome/兰蔻");
CategoryIndex["33"]=new Array("20","Kenzo/高田贤三");
CategoryIndex["34"]=new Array("20","Versace/范思哲");
CategoryIndex["42"]=new Array("20","Dunhill/登喜路");
CategoryIndex["38"]=new Array("20","Guerlain娇兰");
CategoryIndex["37"]=new Array("20","Gucci/古琦");
CategoryIndex["44"]=new Array("20","CK/凯文克莱");
CategoryIndex["46"]=new Array("20","Chanel/香奈儿");
CategoryIndex["254"]=new Array("20","Ferragamo/佛莱格默");
CategoryIndex["51"]=new Array("20","Boss/Boss");
CategoryIndex["341"]=new Array("20","Harajuku Lovers/原宿");
CategoryIndex["32"]=new Array("20","Lacoste/鳄鱼");
CategoryIndex["30"]=new Array("20","S.T.Dupont/都彭");
CategoryIndex["298"]=new Array("20","Bvlgari/宝格丽");
CategoryIndex["299"]=new Array("20","Moschino/奧莉佛-梦仙奴");
CategoryIndex["300"]=new Array("20","LANVIN/浪凡光韵");
CategoryIndex["301"]=new Array("20","Armani/阿玛尼");
CategoryIndex["302"]=new Array("20","Anna Sui安娜苏");
CategoryIndex["303"]=new Array("20","Elizabeth Arden/雅顿");
CategoryIndex["324"]=new Array("20","Adidas/阿迪达斯");
CategoryIndex["41"]=new Array("20","其它香水");
CategoryIndex["20"]=new Array("269","香水品牌","45","50","59","48","60","36","31","33","34","42","38","37","44","46","254","51","341","32","30","298","299","300","301","302","303","324","41");
CategoryIndex["269"]=new Array("0","欧美品牌","397","1","353","407","120","117","113","108","105","101","96","89","79","73","66","304","325","218","221","20");
CategoryIndex["26"]=new Array("270","贝罗/台湾");
CategoryIndex["280"]=new Array("270","SHILLS/台湾");
CategoryIndex["14"]=new Array("12","护肤");
CategoryIndex["13"]=new Array("12","彩妆");
CategoryIndex["12"]=new Array("270","我的美丽日记（面膜）","14","13");
CategoryIndex["138"]=new Array("133","面部护理");
CategoryIndex["137"]=new Array("133","眼部护理");
CategoryIndex["136"]=new Array("133","身体护理");
CategoryIndex["135"]=new Array("133","礼盒套装");
CategoryIndex["134"]=new Array("133","彩妆系列");
CategoryIndex["133"]=new Array("270","Polynia/台湾","138","137","136","135","134");
CategoryIndex["270"]=new Array("0","台湾品牌","26","280","12","133");
CategoryIndex["395"]=new Array("394","果荟椰纤面膜系列");
CategoryIndex["396"]=new Array("394","花荟隐形面膜系列");
CategoryIndex["394"]=new Array("281","荟宝面膜--我的花果荟","395","396");
CategoryIndex["315"]=new Array("314","玫瑰系列～红润保湿");
CategoryIndex["316"]=new Array("314","石榴系列～柔润美肌");
CategoryIndex["317"]=new Array("314","橄榄系列～补水润白");
CategoryIndex["314"]=new Array("281","AromaTherapy/采媚香薰","315","316","317");
CategoryIndex["354"]=new Array("281","丹希露～草本立纯B.B");
CategoryIndex["320"]=new Array("319","面部护理");
CategoryIndex["321"]=new Array("319","眼部护理");
CategoryIndex["322"]=new Array("319","彩妆系列");
CategoryIndex["338"]=new Array("319","美发护发");
CategoryIndex["323"]=new Array("319","礼盒套装");
CategoryIndex["339"]=new Array("319","身体护理");
CategoryIndex["319"]=new Array("281","Sibelle/四季美人","320","321","322","338","323","339");
CategoryIndex["240"]=new Array("281","千纤草");
CategoryIndex["313"]=new Array("281","BOB彩妆");
CategoryIndex["308"]=new Array("307","护肤系列");
CategoryIndex["309"]=new Array("307","美发护发");
CategoryIndex["307"]=new Array("281","吉烈绅士护理","308","309");
CategoryIndex["236"]=new Array("281","蒙巴拉");
CategoryIndex["310"]=new Array("281","其他国货");
CategoryIndex["281"]=new Array("0","国货精品","394","314","354","319","240","313","307","236","310");
CategoryIndex["318"]=new Array("296","护肤");
CategoryIndex["387"]=new Array("296","彩妆");
CategoryIndex["296"]=new Array("0","牛耳/大Ｓ等明星推荐","318","387");
CategoryIndex["385"]=new Array("0","美容美体工具");
CategoryIndex["386"]=new Array("0","英皇植物精油");
CategoryIndex["384"]=new Array("0","季节性产品");
CategoryIndex["382"]=new Array("381","限时秒杀");
CategoryIndex["383"]=new Array("381","清仓特卖");
CategoryIndex["381"]=new Array("0","特卖产品","382","383");
CategoryIndex["93"]=new Array("0","其它产品");
CategoryIndex["0"]=new Array("","","267","268","269","270","281","296","385","386","384","381","93");
CategoryIndex["hot"]=new Array("","热销品牌","394","381","262","224","95","204","201","191","73","306","68","314","167","307");
PropertyIndex["99"]=new Array("0","限时促销");
PropertyIndex["94"]=new Array("48","彩护套装");
PropertyIndex["49"]=new Array("48","洗护套装");
PropertyIndex["96"]=new Array("48","香水套装");
PropertyIndex["51"]=new Array("48","其它套装");
PropertyIndex["48"]=new Array("0","礼盒套装","94","49","96","51");
PropertyIndex["56"]=new Array("44","祛角质");
PropertyIndex["57"]=new Array("44","清洁霜");
PropertyIndex["58"]=new Array("44","洗面奶");
PropertyIndex["59"]=new Array("44","按摩霜");
PropertyIndex["60"]=new Array("44","皂类");
PropertyIndex["44"]=new Array("4","洁面","56","57","58","59","60");
PropertyIndex["61"]=new Array("53","水");
PropertyIndex["62"]=new Array("53","乳");
PropertyIndex["63"]=new Array("53","霜");
PropertyIndex["98"]=new Array("53","胶(露)");
PropertyIndex["53"]=new Array("4","护肤","61","62","63","98");
PropertyIndex["54"]=new Array("4","精华");
PropertyIndex["55"]=new Array("4","面膜");
PropertyIndex["64"]=new Array("4","其他");
PropertyIndex["4"]=new Array("0","面部护理","44","53","54","55","64");
PropertyIndex["5"]=new Array("0","眼部护理");
PropertyIndex["6"]=new Array("0","唇部护理");
PropertyIndex["7"]=new Array("0","鼻部护理");
PropertyIndex["8"]=new Array("0","手部护理");
PropertyIndex["9"]=new Array("0","足部护理");
PropertyIndex["10"]=new Array("0","颈部护理");
PropertyIndex["11"]=new Array("0","口腔护理");
PropertyIndex["12"]=new Array("0","身体护理");
PropertyIndex["13"]=new Array("0","沐浴产品");
PropertyIndex["14"]=new Array("0","美发护发");
PropertyIndex["17"]=new Array("18","卸妆产品");
PropertyIndex["29"]=new Array("18","粉底/隔离/妆前乳/BB霜/CC霜");
PropertyIndex["30"]=new Array("18","粉饼/蜜粉/散粉");
PropertyIndex["38"]=new Array("18","眉笔/眉粉/眉饼");
PropertyIndex["37"]=new Array("18","眼线笔/眼线液");
PropertyIndex["36"]=new Array("18","眼影");
PropertyIndex["34"]=new Array("18","睫毛膏");
PropertyIndex["35"]=new Array("18","睫毛增长液");
PropertyIndex["42"]=new Array("18","腮红/胭脂");
PropertyIndex["43"]=new Array("18","唇彩/唇蜜/唇膏/口红");
PropertyIndex["40"]=new Array("18","唇笔/唇线笔");
PropertyIndex["47"]=new Array("18","修颜/高亮/阴影粉");
PropertyIndex["41"]=new Array("18","遮瑕笔/遮瑕膏/粉条");
PropertyIndex["45"]=new Array("18","指甲油/美甲产品");
PropertyIndex["46"]=new Array("18","身体彩绘");
PropertyIndex["39"]=new Array("18","双眼皮胶");
PropertyIndex["18"]=new Array("0","彩妆系列","17","29","30","38","37","36","34","35","42","43","40","47","41","45","46","39");
PropertyIndex["104"]=new Array("0","男士护理");
PropertyIndex["95"]=new Array("19","香体露/走珠/香体喷雾");
PropertyIndex["69"]=new Array("19","小样香水");
PropertyIndex["70"]=new Array("19","JAGUAR/积架");
PropertyIndex["71"]=new Array("19","Burberrys/巴宝莉");
PropertyIndex["88"]=new Array("19","annasui/安娜苏");
PropertyIndex["89"]=new Array("19","ElizabethArden/雅顿");
PropertyIndex["90"]=new Array("19","EsteeLauder/雅诗兰黛");
PropertyIndex["91"]=new Array("19","Lancome/兰蔻");
PropertyIndex["93"]=new Array("19","CD/迪奥");
PropertyIndex["92"]=new Array("19","Clinique倩碧");
PropertyIndex["72"]=new Array("19","Givenchy/纪梵希");
PropertyIndex["73"]=new Array("19","Calotine/歌宝婷");
PropertyIndex["74"]=new Array("19","Davidoff/大卫杜夫");
PropertyIndex["75"]=new Array("19","JLO/珍妮佛洛佩兹");
PropertyIndex["76"]=new Array("19","Kenzo/高田贤三");
PropertyIndex["77"]=new Array("19","Versace/范思哲");
PropertyIndex["79"]=new Array("19","Guerlain娇兰");
PropertyIndex["103"]=new Array("19","LANVIN/浪凡光韵");
PropertyIndex["100"]=new Array("19","Bvlgari/宝格丽");
PropertyIndex["101"]=new Array("19","Moschino/奧莉佛 梦仙奴");
PropertyIndex["78"]=new Array("19","Dunhill/登喜路");
PropertyIndex["102"]=new Array("19","Armani/阿玛尼");
PropertyIndex["80"]=new Array("19","Gucci/古琦");
PropertyIndex["81"]=new Array("19","CK/凯文克莱");
PropertyIndex["82"]=new Array("19","Chanel/香奈儿");
PropertyIndex["83"]=new Array("19","Ferragamo/佛莱格默");
PropertyIndex["84"]=new Array("19","Boss/Boss");
PropertyIndex["85"]=new Array("19","Lacoste/鳄鱼");
PropertyIndex["86"]=new Array("19","S.T.Dupont/都彭");
PropertyIndex["125"]=new Array("19","Adidas/阿迪达斯");
PropertyIndex["87"]=new Array("19","其它香水");
PropertyIndex["19"]=new Array("0","香水系列","95","69","70","71","88","89","90","91","93","92","72","73","74","75","76","77","79","103","100","101","78","102","80","81","82","83","84","85","86","125","87");
PropertyIndex["67"]=new Array("20","单方精油");
PropertyIndex["68"]=new Array("20","复方精油");
PropertyIndex["20"]=new Array("0","植物精油","67","68");
PropertyIndex["15"]=new Array("31","防晒修护");
PropertyIndex["16"]=new Array("31","防冻防裂");
PropertyIndex["52"]=new Array("31","祛斑祛痘祛黑头");
PropertyIndex["21"]=new Array("31","瘦身纤体");
PropertyIndex["22"]=new Array("31","美乳丰胸");
PropertyIndex["65"]=new Array("31","黑发产品");
PropertyIndex["23"]=new Array("31","增高产品");
PropertyIndex["126"]=new Array("31","脱毛除毛");
PropertyIndex["97"]=new Array("31","其它功效");
PropertyIndex["31"]=new Array("0","功效产品","15","16","52","21","22","65","23","126","97");
PropertyIndex["24"]=new Array("0","美容工具");
PropertyIndex["106"]=new Array("105","T恤/打底衫");
PropertyIndex["107"]=new Array("105","衬衫");
PropertyIndex["119"]=new Array("105","雪纺衫");
PropertyIndex["108"]=new Array("105","卫衣");
PropertyIndex["109"]=new Array("105","连衣裙/裙衣");
PropertyIndex["110"]=new Array("105","时尚马甲");
PropertyIndex["111"]=new Array("105","西装");
PropertyIndex["112"]=new Array("105","小外套/小夹克");
PropertyIndex["113"]=new Array("105","小吊带/背心");
PropertyIndex["114"]=new Array("105","针织上衣/毛衣");
PropertyIndex["115"]=new Array("105","中/长风衣");
PropertyIndex["116"]=new Array("105","皮衣");
PropertyIndex["118"]=new Array("105","牛仔");
PropertyIndex["117"]=new Array("105","棉衣");
PropertyIndex["105"]=new Array("25","上衣","106","107","119","108","109","110","111","112","113","114","115","116","118","117");
PropertyIndex["121"]=new Array("120","时尚小短裤");
PropertyIndex["122"]=new Array("120","百搭半身裙");
PropertyIndex["123"]=new Array("120","中裤/长裤");
PropertyIndex["120"]=new Array("25","下装","121","122","123");
PropertyIndex["124"]=new Array("25","围巾/饰品类");
PropertyIndex["25"]=new Array("0","精品服饰","105","120","124");
PropertyIndex["26"]=new Array("0","生活居家");
PropertyIndex["27"]=new Array("0","办公文具");
PropertyIndex["28"]=new Array("0","其它产品");
PropertyIndex["0"]=new Array("","","99","48","4","5","6","7","8","9","10","11","12","13","14","18","104","19","20","31","24","25","26","27","28");



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

function GenSingleCategory(Categoryid)
{ var menu_class,cat_file,Categoryid2,categorycode="";
	if(Categoryid=="hot")
	{ Categoryid2="0";
	  cat_file="/category/";
	}
	else
	{ Categoryid2=Categoryid;
	  cat_file="/category/cat"+Categoryid2+".htm";
	}
	if(CategoryIndex[Categoryid].length>2)
	{ if(Categoryid2==CurrentCategory)menu_class="gMenuOpen";
		else menu_class="gMenuClose";
	}
	else
	{ menu_class="gMenuEmpty";
	}
	
	categorycode+='<TR><TD class="'+menu_class+'"><span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="'+cat_file+'">'+CategoryIndex[Categoryid][1]+'</a></span></span><div>';
  subsort=CategoryIndex[Categoryid];
  subcount=subsort.length;
   
  for(j=2;j<subcount;j++)
  { categorycode+='<a href="/category/cat'+subsort[j]+'.htm">'+CategoryIndex[subsort[j]][1]+'</a>'; 
  } 
  categorycode+='</div></TD></TR>';
  return categorycode
}


      

function ShowCategoryGuider()
{ var i,j,RootCategory,sortcount,subcount,subsort,parent,categorycode="";
  
  if(!CurrentCategory)CurrentCategory="0";
   
  if(CurrentCategory!="0")
  { if(CategoryIndex[CurrentCategory].length<3 && CategoryIndex[CurrentCategory][0]!="0")
  	{ CurrentCategory=CategoryIndex[CurrentCategory][0];
  	}
  }

  RootCategory=(CategoryIndex[CurrentCategory][0]=="0")?"0":CurrentCategory;
  
  categorycode+='<TABLE cellSpacing=0 cellPadding=0 width="190" align="center"  border="0">';
  categorycode+='<TR>';
  categorycode+='   <TD><img src="/images/guide_brand.gif" width="190" height="27"></TD>';
  categorycode+='</TR>';
  categorycode+='</TABLE>';
  categorycode+='<TABLE class="NavigationClient" align="center" cellSpacing=0 cellPadding=0 width="190">';
  categorycode+='<TR>';
  categorycode+='   <TD vAlign="top" align="center" width="100%" style="padding-left:10px;padding-top:5px;" >';
  categorycode+='      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">';
      	 
  if(RootCategory!="0")
  { categorycode+='<TR style="CURSOR: pointer" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)" onclick="gmSwitch(this)" height=24 valign="middle"><TD> <IMG width=20 height=20 border=0 src="/images/guidefold2.gif" align="absMiddle"> <a href="/category/cat'+CategoryIndex[RootCategory][0]+'.htm"><font color=#FF0000>返回上级分类</font></a></TD></TR>';
  	categorycode+=GenSingleCategory(RootCategory);
  }
  else
  { categorycode+=GenSingleCategory("hot");
    subsort=CategoryIndex[RootCategory];
  	sortcount=subsort.length;
    for(i=2;i<sortcount;i++)
    { categorycode+=GenSingleCategory(subsort[i]);
    }  	
  }
  categorycode+='      </table>';
  categorycode+='   </TD>';
  categorycode+='</TR>';
  categorycode+='</TABLE><img src="/images/index_4.gif" width="190" height="12">';
  document.write(categorycode);
}

function GenSingleProperty(Propertyid)
{ var guide_fold_image,guide_fold_disp_opt,Propertycode="";
	if(PropertyIndex[Propertyid].length>2)
	{ if(Propertyid==CurrentProperty)menu_class="gMenuOpen";
		else menu_class="gMenuClose";
	}
	else
	{ menu_class="gMenuEmpty";
	}

  Propertycode+='<TR><TD class="'+menu_class+'"><span onclick="gmSwitch(this)" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)"><span class="gMenuBar"><a href="/category/sort'+Propertyid+'.htm">'+PropertyIndex[Propertyid][1]+'</a></span></span><div>';

  subsort=PropertyIndex[Propertyid];
  subcount=subsort.length;
  
  for(j=2;j<subcount;j++)
  {  Propertycode+='<a href="/category/sort'+subsort[j]+'.htm">'+PropertyIndex[subsort[j]][1]+'</a>';  
  } 
  Propertycode+="</div></td></tr>";
  
  return Propertycode
}

function ShowPropertyGuider()
{ var i,j,RootProperty,sortcount,subcount,subsort,parent,Propertycode="";
 
  if(!CurrentProperty)CurrentProperty="0";
   
  if(CurrentProperty!="0")
  { if(PropertyIndex[CurrentProperty].length<3 && PropertyIndex[CurrentProperty][0]!="0")
  	{ CurrentProperty=PropertyIndex[CurrentProperty][0];
  	}
  }

  RootProperty=(PropertyIndex[CurrentProperty][0]=="0")?"0":CurrentProperty;
  

  Propertycode+='<TABLE cellSpacing=0 cellPadding=0 width="190" align="center"  border="0">';
  Propertycode+='<TR>';
  Propertycode+='   <TD><img src="/images/guide_property.gif" width="190" height="27"></TD>';
  Propertycode+='</TR>';
  Propertycode+='</TABLE>';
  Propertycode+='<TABLE class="NavigationClient" align="center" cellSpacing=0 cellPadding=0 width="190">';
  Propertycode+='<TR>';
  Propertycode+='   <TD vAlign="top" align="center" width="100%" style="padding-left:10px;padding-top:5px;" >';
  Propertycode+='      <TABLE border=0 cellSpacing=0 cellPadding=0 width="100%">';
 
  if(RootProperty!="0")
  { Propertycode+='<TR style="CURSOR: pointer" onMouseOver="gmEnter(this)" onMouseOut="gmLeave(this)" onclick="gmSwitch(this)" height=24 valign="middle"><TD> <IMG width=20 height=20 border=0 src="/images/guidefold2.gif" align="absMiddle"> <a href="/category/sort'+PropertyIndex[RootProperty][0]+'.htm"><font color=#FF0000>返回上级分类</font></a></TD></TR>';
  	Propertycode+=GenSingleProperty(RootProperty);
  }
  else
  { subsort=PropertyIndex[RootProperty];
  	sortcount=subsort.length;
    for(i=2;i<sortcount;i++)
    { Propertycode+=GenSingleProperty(subsort[i]);
    }  	
  }
  Propertycode+='      </table>';
  Propertycode+='   </TD>';
  Propertycode+='</TR>';
  Propertycode+='</TABLE><img src="/images/index_4.gif" width="190" height="12">';
  document.write(Propertycode);
}

/*
CurrentProperty=htmRequest("proid")
if(CurrentProperty)
{ ShowPropertyGuider();
} 
else
{ CurrentCategory=htmRequest("catid") 
  ShowCategoryGuider();
  ShowPropertyGuider();
}*/
 ShowCategoryGuider();
 ShowPropertyGuider();
