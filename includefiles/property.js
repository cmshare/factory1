var CurSortSelection2=0;
function GenOption2(cID,cTitle,cIndent)
{ var i,StrIndent="";
	for(i=0;i<cIndent;i++)StrIndent+="　　";
	document.write("<option value=\""+cID+"\" "+((cID==CurSortSelection2)?"selected":"")+">"+StrIndent+cTitle+"</option>");
}
function CreatePropertySelection(SelectName,SelectValue,DefaultOptionTitle,OnchageProcess)
{	CurSortSelection2=SelectValue;
	document.write("<select name=\""+SelectName+"\" onchange=\""+OnchageProcess+"\">");
	if(DefaultOptionTitle)document.write("<option value=\"0\">"+DefaultOptionTitle+"</option>");
GenOption2(99,"限时促销",0);
GenOption2(48,"礼盒套装",0);
GenOption2(94,"彩护套装",1);
GenOption2(49,"洗护套装",1);
GenOption2(96,"香水套装",1);
GenOption2(51,"其它套装",1);
GenOption2(4,"面部护理",0);
GenOption2(44,"洁面",1);
GenOption2(56,"祛角质",2);
GenOption2(57,"清洁霜",2);
GenOption2(58,"洗面奶",2);
GenOption2(59,"按摩霜",2);
GenOption2(60,"皂类",2);
GenOption2(53,"护肤",1);
GenOption2(61,"水",2);
GenOption2(62,"乳",2);
GenOption2(63,"霜",2);
GenOption2(98,"胶(露)",2);
GenOption2(54,"精华",1);
GenOption2(55,"面膜",1);
GenOption2(64,"其他",1);
GenOption2(5,"眼部护理",0);
GenOption2(6,"唇部护理",0);
GenOption2(7,"鼻部护理",0);
GenOption2(8,"手部护理",0);
GenOption2(9,"足部护理",0);
GenOption2(10,"颈部护理",0);
GenOption2(11,"口腔护理",0);
GenOption2(12,"身体护理",0);
GenOption2(13,"沐浴产品",0);
GenOption2(14,"美发护发",0);
GenOption2(18,"彩妆系列",0);
GenOption2(17,"卸妆产品",1);
GenOption2(29,"粉底/隔离/妆前乳/BB霜/CC霜",1);
GenOption2(30,"粉饼/蜜粉/散粉",1);
GenOption2(38,"眉笔/眉粉/眉饼",1);
GenOption2(37,"眼线笔/眼线液",1);
GenOption2(36,"眼影",1);
GenOption2(34,"睫毛膏",1);
GenOption2(35,"睫毛增长液",1);
GenOption2(42,"腮红/胭脂",1);
GenOption2(43,"唇彩/唇蜜/唇膏/口红",1);
GenOption2(40,"唇笔/唇线笔",1);
GenOption2(47,"修颜/高亮/阴影粉",1);
GenOption2(41,"遮瑕笔/遮瑕膏/粉条",1);
GenOption2(45,"指甲油/美甲产品",1);
GenOption2(46,"身体彩绘",1);
GenOption2(39,"双眼皮胶",1);
GenOption2(104,"男士护理",0);
GenOption2(19,"香水系列",0);
GenOption2(95,"香体露/走珠/香体喷雾",1);
GenOption2(69,"小样香水",1);
GenOption2(70,"JAGUAR/积架",1);
GenOption2(71,"Burberrys/巴宝莉",1);
GenOption2(88,"annasui/安娜苏",1);
GenOption2(89,"ElizabethArden/雅顿",1);
GenOption2(90,"EsteeLauder/雅诗兰黛",1);
GenOption2(91,"Lancome/兰蔻",1);
GenOption2(93,"CD/迪奥",1);
GenOption2(92,"Clinique倩碧",1);
GenOption2(72,"Givenchy/纪梵希",1);
GenOption2(73,"Calotine/歌宝婷",1);
GenOption2(74,"Davidoff/大卫杜夫",1);
GenOption2(75,"JLO/珍妮佛洛佩兹",1);
GenOption2(76,"Kenzo/高田贤三",1);
GenOption2(77,"Versace/范思哲",1);
GenOption2(79,"Guerlain娇兰",1);
GenOption2(103,"LANVIN/浪凡光韵",1);
GenOption2(100,"Bvlgari/宝格丽",1);
GenOption2(101,"Moschino/奧莉佛 梦仙奴",1);
GenOption2(78,"Dunhill/登喜路",1);
GenOption2(102,"Armani/阿玛尼",1);
GenOption2(80,"Gucci/古琦",1);
GenOption2(81,"CK/凯文克莱",1);
GenOption2(82,"Chanel/香奈儿",1);
GenOption2(83,"Ferragamo/佛莱格默",1);
GenOption2(84,"Boss/Boss",1);
GenOption2(85,"Lacoste/鳄鱼",1);
GenOption2(86,"S.T.Dupont/都彭",1);
GenOption2(125,"Adidas/阿迪达斯",1);
GenOption2(87,"其它香水",1);
GenOption2(20,"植物精油",0);
GenOption2(67,"单方精油",1);
GenOption2(68,"复方精油",1);
GenOption2(31,"功效产品",0);
GenOption2(15,"防晒修护",1);
GenOption2(16,"防冻防裂",1);
GenOption2(52,"祛斑祛痘祛黑头",1);
GenOption2(21,"瘦身纤体",1);
GenOption2(22,"美乳丰胸",1);
GenOption2(65,"黑发产品",1);
GenOption2(23,"增高产品",1);
GenOption2(126,"脱毛除毛",1);
GenOption2(97,"其它功效",1);
GenOption2(24,"美容工具",0);
GenOption2(25,"精品服饰",0);
GenOption2(105,"上衣",1);
GenOption2(106,"T恤/打底衫",2);
GenOption2(107,"衬衫",2);
GenOption2(119,"雪纺衫",2);
GenOption2(108,"卫衣",2);
GenOption2(109,"连衣裙/裙衣",2);
GenOption2(110,"时尚马甲",2);
GenOption2(111,"西装",2);
GenOption2(112,"小外套/小夹克",2);
GenOption2(113,"小吊带/背心",2);
GenOption2(114,"针织上衣/毛衣",2);
GenOption2(115,"中/长风衣",2);
GenOption2(116,"皮衣",2);
GenOption2(118,"牛仔",2);
GenOption2(117,"棉衣",2);
GenOption2(120,"下装",1);
GenOption2(121,"时尚小短裤",2);
GenOption2(122,"百搭半身裙",2);
GenOption2(123,"中裤/长裤",2);
GenOption2(124,"围巾/饰品类",1);
GenOption2(26,"生活居家",0);
GenOption2(27,"办公文具",0);
GenOption2(28,"其它产品",0);
document.write("</select>");
}
