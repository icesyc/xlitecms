/*
MySQL Backup
Source Host:           localhost
Source Server Version: 5.1.19-beta-community-nt-debug
Source Database:       xlite
Date:                  2007-07-16 14:23:05
*/

SET FOREIGN_KEY_CHECKS=0;
use xlite;
#----------------------------
# Table structure for xlite_act
#----------------------------
CREATE TABLE `xlite_act` (
  `act_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `act_name` varchar(32) NOT NULL,
  `controller` varchar(128) NOT NULL,
  `action` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`act_id`),
  KEY `controller` (`controller`,`action`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=latin1;
#----------------------------
# Records for table xlite_act
#----------------------------


insert  into xlite_act values 
(1, '账号列表', 'admin', 'index'), 
(2, '账号添加', 'admin', 'save'), 
(3, '账号密码修改', 'admin', 'setPwd'), 
(4, '账号删除', 'admin', 'delete'), 
(5, '修改账号用户组', 'admin', 'chrole'), 
(6, '文章列表', 'article', 'index'), 
(7, '文章添加/修改', 'article', 'save'), 
(8, '文章删除', 'article', 'delete'), 
(9, '文章审核/锁定', 'article', 'setAudit'), 
(10, 'ftp列表', 'ftp', 'index'), 
(11, 'ftp添加/修改', 'ftp', 'save'), 
(12, 'ftp删除', 'ftp', 'delete'), 
(13, 'ftp测试连接', 'ftp', 'test'), 
(14, 'ftp发布', 'ftp', 'pub'), 
(15, '相册列表', 'gallery', 'index'), 
(16, '相册添加', 'gallery', 'save'), 
(17, '相册标题修改', 'gallery', 'editTitle'), 
(18, '相册删除', 'gallery', 'delete'), 
(19, '相册图片列表', 'image', 'index'), 
(20, '相册图片添加', 'image', 'save'), 
(21, '相册图片删除', 'image', 'delete'), 
(23, '链接列表', 'link', 'index'), 
(24, '链接添加/修改', 'link', 'save'), 
(25, '链接删除', 'link', 'delete'), 
(26, '留言板列表', 'guestbook', 'index'), 
(27, '留言内容添加', 'guestbook', 'save'), 
(28, '留言内容删除', 'guestbook', 'delete'), 
(29, '采集规则列表', 'scratcher', 'index'), 
(30, '采集规则添加/修改', 'scratcher', 'save'), 
(31, '采集规则删除', 'scratcher', 'delete'), 
(32, '采集列表', 'scratcher', 'scratchList'), 
(33, '采集页面', 'scratcher', 'scratchPage'), 
(34, '采集规则测试', 'scratcher', 'test'), 
(35, '采集缓存清理', 'scratcher', 'clearCache'), 
(36, '采集内容保存到数据库', 'scratcher', 'saveToDB'), 
(37, '采集断点调试1', 'scratcher', 'debugListURL'), 
(38, '采集断点调试2', 'scratcher', 'debugListSplit'), 
(39, '采集断点调试3', 'scratcher', 'debugArticleURL'), 
(40, '采集断点调试4', 'scratcher', 'debugArticle'), 
(41, '采集断点调试5', 'scratcher', 'debugPattern'), 
(42, '采集规则导入', 'scratcher', 'import'), 
(43, '采集规则导出', 'scratcher', 'export'), 
(44, '分类列表', 'sort', 'index'), 
(45, '分类添加/修改', 'sort', 'save'), 
(46, '分类删除', 'sort', 'delete'), 
(47, '分类更新XML文件', 'sort', 'updateXML'), 
(48, '系统信息', 'system', 'index'), 
(49, '密码修改', 'system', 'setPwd'), 
(50, '数据备份', 'system', 'backup'), 
(51, '数据恢复', 'system', 'restore'), 
(52, '备份文件下载', 'system', 'download'), 
(53, '备份文件删除', 'system', 'delete'), 
(54, '分类列表页面更新', 'update', 'updateList'), 
(55, '分类文章页面更新', 'update', 'UpdateSortArticle'), 
(56, '文章批量更新', 'update', 'updateArticleById'), 
(57, '相册页面更新', 'update', 'updateGallery'), 
(58, '首页更新', 'update', 'updateIndex'), 
(59, '用户组列表', 'role', 'index'), 
(60, '用户组添加/修改', 'role', 'save'), 
(61, '用户组删除', 'role', 'delete'), 
(62, '文章推荐/取消', 'article', 'setRecmd'), 
(63, '标签列表', 'tags', 'index'), 
(64, '标签删除', 'tags', 'delete'), 
(65, '评论列表', 'comment', 'index'), 
(66, '评论删除', 'comment', 'delete');
#----------------------------
# Table structure for xlite_admin
#----------------------------
CREATE TABLE `xlite_admin` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `role_id` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
#----------------------------
# Records for table xlite_admin
#----------------------------


insert  into xlite_admin values 
(1, 'stone', '1ddcb92ade31c8fbd370001f9b29a7d9', 1);
#----------------------------
# Table structure for xlite_article
#----------------------------
CREATE TABLE `xlite_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `summary` text,
  `post_time` int(11) NOT NULL,
  `author` varchar(20) NOT NULL,
  `come_from` varchar(30) NOT NULL,
  `content` longtext,
  `sort_id` int(11) NOT NULL,
  `is_audit` tinyint(1) NOT NULL DEFAULT '0',
  `is_recmd` tinyint(1) NOT NULL DEFAULT '0',
  `is_pic` tinyint(1) NOT NULL DEFAULT '0',
  `tags` varchar(30) NOT NULL,
  `title_color` varchar(10) NOT NULL,
  `thumbnail` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `is_recmd` (`sort_id`,`is_audit`,`is_recmd`),
  KEY `is_pic` (`is_pic`,`is_audit`)
) ENGINE=MyISAM AUTO_INCREMENT=130 DEFAULT CHARSET=latin1;
#----------------------------
# Records for table xlite_article
#----------------------------


insert  into xlite_article values 
(110, '2007年网易体育招聘篮球通讯员_网易体育', '', 1167958565, '', '网易体育专稿', '\n<P>　　寒假即将来临，但<a href=http://sports.163.com/nba/>NBA</a>06－07赛季的激战却才刚刚开始。喜欢NBA，也希望尝试媒体工作的你又怎能错过这样一个加入网易体育的机会呢？现由于业务发展的要求，网易体育面向社会招聘以下职务：</P>\r\n<P><STRONG>1）通讯员（兼职）</STRONG><BR>a.对篮球熟悉，拥有良好的媒体写作能力；<BR>b.掌握1门或以上外语，能熟练阅读英文报刊、网页；<BR>c.拥有充足的上网时间和条件，以及善于缓解高工作压力；</P>\r\n<P><STRONG>2）Flash/漫画 制作者（兼职）<BR></STRONG>a.拥有良好的美工基础，善于并能熟悉、快速制作FLASH/漫画（网页格式）；<BR>b.富有创意，熟悉NBA，<BR>c.拥有充足的上网时间和条件，<BR><BR></P>\r\n<CENTER><IMG alt=\"\" src=\"/UserFiles/Image/1167977872.66.jpg\" border=0><BR><STRONG>虚拟对白：这么好的机会，错过可要后悔了啊！<BR></STRONG></CENTER>\r\n<P>　　以上职务招聘长期有效，<STRONG>有意者请将详细简历以及个人相关作品发至</STRONG><a href=\"mailto:wtmaths@163.com\" target=\"_blank\"><STRONG>wtmaths@163.com</STRONG></A>，来信注明应聘字样。薪金、工作方式面谈。由于来信众多，除合符要求者外，恕不一一回复。<a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977873.9335.gif\" alt=\"唐威\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '', '', ''), 
(111, '爵士-火箭前瞻：AK47状态差 麦蒂有望五连胜_网易体育', '\n<ul><li>\n　　火箭明天将迎来西北赛区领头羊，西部前四号种子球队犹他爵士队，对手虽然取得了两连胜，但核心球员基里连科近期状态不佳，只要能限制住布泽尔与奥库的得分势头，麦蒂率领球队取得五连胜的希望将很大\n</li></ul>\n', 1167973623, '英扎吉', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　火箭明天将迎来西北赛区领头羊，西部前四号种子球队犹他爵士队，对手虽然取得了两连胜，但核心球员基里连科近期状态不佳，只要能限制住布泽尔与奥库的得分势头，麦蒂率领球队取得五连胜的希望将很大\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG><FONT color=#ff0000>1月6日 09：30 火箭 VS 爵士 [<a href=\"http://chat.news.163.com/chat/new/index.html?id=767\" target=\"_blank\">点击进入直播室</A>]</FONT></STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">网易体育1月5日讯：北京时间明日（周六）早上9点30分，<a href=http://sports.163.com/special/0005222R/rockets.html target=_blank>火箭队</a>(<a href=http://blog.163.com/rocketsblog>blog</a>)继续坐镇主场丰田中心，迎战本赛季进步神速的西部劲旅犹他<a href=http://sports.163.com/special/0005222R/jazz.html>爵士队</a>。两队曾经在本赛季揭幕战中狭路相逢，当时拥有<a href=http://sports.163.com/special/0005222S/yao.html>姚明</a>（<a href=http://wiki.sports.163.com/stars/9/9d9cd68285d340efde44f7bc81183f60.html>我来编辑姚明的资料</a>）的火箭在客队拿对手无可奈何最终10分落败，如今对手同样两连胜士气正旺，布泽尔、基里连科、奥库等人又状态神勇，恐怕火箭想报一箭之仇的难度非常大。好在本场比赛是在丰田中心进行，已经变成“主场龙”的火箭相信不会放过新赛季首次五连胜的良机。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>两队近况：爵士两连胜，火箭二度四连胜</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">犹他爵士队本赛季的进步有目共睹，他们目前总战绩为23胜9负，胜率高达72％；排名高居西北赛区第一、西部第三位；而且爵士队只落后西部领头羊达拉斯<a href=http://sports.163.com/special/0005222R/mavericks.html>小牛队</a>两个胜场，如果继续连胜下去，未来一周内很有可能重返西部榜首的位置。不过与火箭情况相似的是，爵士队是西部有名的“主场龙”，他们在三角洲中心的战绩为14胜2负，而客场战绩9胜7负在西部前四名球队中并不出色位列最后，最近一个客场中（12月29日），他们以83比106惨败给<a href=http://sports.163.com/special/0005222R/spurs.html>马刺队</a>，当时基里连科只得到6分，布泽尔9分，费舍尔5分，三大核心加起来不如对方<a href=http://sports.163.com/special/0005222S/duncan.html>邓肯</a>（20分9个篮板）一人得分多，可见爵士队在客场的发挥并不稳定。</P>\r\n<P style=\"TEXT-INDENT: 2em\">不过自从输给马刺后，爵士队迅速找回了赢球感觉，他们先后在主场96比86战胜开拓者，98比87战胜76人，本周取得两连胜后排名已经稳居西北赛区第一名，遥遥领先<a href=http://sports.163.com/special/0005222S/Allen_Iverson.html>艾弗森</a>率领的西北赛区第二名丹佛<a href=http://sports.163.com/special/0005222R/nuggets.html>掘金队</a>5.5个胜场，短时间内不会跌出西部前三。但是接下来老帅斯隆将要率领球队开始背靠背客场之旅，明天打完火箭队后，他们将马不停蹄的赶往丹佛，在周日早上挑战掘金队，随后还要迎战小牛、<a href=http://sports.163.com/special/0005222Q/Miami_Heat.html target=_blank>热队</a>(<a href=http://blog.163.com/heatblog>blog</a>)等强队，可以说爵士队未来一周面对的压力要比火箭大很多，而最有希望取胜的一场比赛莫过于明天打火箭，全队同样不肯放过三连胜的良机。</P>\r\n<P style=\"TEXT-INDENT: 2em\">火箭队近期虽然取得四连胜，但不难发现其中大多数均为东西部弱旅，<a href=http://sports.163.com/special/0005222Q/NewJersey_Nets.html>网队</a>、<a href=http://sports.163.com/special/0005222Q/Atlanta_Hawks.html>鹰队</a>、灰熊与超音速的整体实力都在火箭之下，所以新赛季第二次四连胜的“含金量”并不是很高。而明天的对手爵士队将是火箭最近一周内遇到的排名最高、最难啃的一块硬骨头，对方的“三驾马车”基里连科、布泽尔、奥库的组合在全联盟都堪称数一数二，后场还拥有一名经验丰富的后卫“小鱼”费舍尔，二年级新秀德隆.威廉姆斯进步神速，客队整体实力应在火箭之上。好在火箭目前11胜3负的主场战绩十分骄人，近期在丰田中心已经三连胜，双方明日有一拼。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>核心对抗：火箭1号VS阿卡47，穆大叔+<a href=http://wiki.sports.163.com/stars/9/937aa9ef9303da7b7789f4698d22705b.html target=_blank>霍华德</a>VS奥库+布泽尔</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">麦格雷迪与俄罗斯人基里连科的交手，无疑是明天比赛的最大看点。火箭队1号最近十分勇猛，连续三场比赛得分过30。昨日面对<a href=http://sports.163.com/special/0005222R/sonic.html>超音速队</a>全明星射手雷.阿伦，<a href=http://sports.163.com/special/0005222S/tracy.html>麦蒂</a>拿下31分6个篮板7次助攻；尽管在比赛最后六分钟内没有得分，但他却摇身一变成为全队进攻的掌控者和指挥官，七次助攻的最后一次妙传<a href=http://sports.163.com/special/0005222S/mutombo.html>穆托姆博</a>篮下打三分尤为精彩。在麦蒂本赛季得分超过30分的五场比赛中，火箭队保持五战全胜。更加可怕的是，麦蒂最近3场比赛中三分球命中率高的惊人！分别为66.7％（6投4中，打鹰）、62.5％（8投5中，打灰熊）、50％（6投3中，打超音速），并出现过外线打四分的精彩表演，他是火箭能否实现赛季首次五连胜的最关键人物。</P>\r\n<P style=\"TEXT-INDENT: 2em\">基里连科本赛季的表现并不稳定，在因伤缺席了去年11月的五场比赛后，他在最近一个多月里的表现时好时坏，只有一场比赛取得过11分11个篮板的两双成绩，还出现过单场4分5次犯规的糟糕演出（昨天打76人）。目前“AK47”场均只得到9分5.4个篮板，大大低于去年同期水平（15.3分8个篮板），不过其46.3％的投篮命中比上赛季有所提升，且场均2.4次盖帽的贡献也是全队最高！爵士队现在的头号得分手为大前锋<a href=http://sports.163.com/special/000521PA/RobertoCarlos.html target=_blank>卡洛斯</a>.布泽尔（21.7分），中锋奥库16.5分紧随其后，基里连科已经沦为球队第三得分点，所以明天火箭内线能否遏制住前两人的得分将十分关键。穆大叔已经连续4场比赛抢到10个以上篮板，霍华德也连续4场比赛中投命中率超过50％，这两人临场表现的好坏同样决定着火箭能否继续高歌猛进。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>交锋历史：火箭处于下风，胜率仅为45％</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">1974年加入<a href=http://sports.163.com/nba/>NBA</a>的爵士队比火箭队小七“岁”，两队在历史上总共交手过154次，火箭70胜84负处于下风，胜率只有45％。前154次交手中，火箭队总得分15456分，失分15610分，净负对方154分。而且在上赛季双方三次交手中，火箭1胜2负同样下风，令中国球迷印象最深的一次交手便是去年4月11日姚明“受伤日”，那天姚明总共只打了8分钟便骨折离场。两队最近一次交手是06-07揭幕战（11月2日），当时姚明得到22分9个篮板2次助攻，麦蒂25分9次助攻最高；但对方布泽尔一人砍下24分19个篮板！中锋奥库17分5次犯规，威廉姆斯18分，帮助爵士在三角洲中心107比97战胜火箭。明天将是双方本赛季第二次交手，下一次碰面要等到今年4月3日。</P>\r\n<P style=\"TEXT-INDENT: 2em\">预计爵士队明日先发：德隆.威廉姆斯、费舍尔、基里连科、布泽尔、奥库。</P>\r\n<P style=\"TEXT-INDENT: 2em\">预计火箭队明日先发：<a href=http://sports.163.com/special/0005222S/alston.html>阿尔斯通</a>、麦格雷迪、<a href=http://sports.163.com/special/0005222S/battier.html>巴蒂尔</a>、霍华德、穆托姆博。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>伤兵营：爵士-（无）；火箭-<a href=http://sports.163.com/special/0005222S/bonzi.html>威尔斯</a>（腰部扭伤，休战1周），姚明（右腿胫骨骨裂，休战6-8周），<a href=http://sports.163.com/special/0005225A/Bernd%20Schneider.html>施奈德</a>(<a href=http://wiki.sports.163.com/stars/8/80ecdf63387a9cd1904398011775f9a6.html>wiki</a>)（右手骨折，2周之内复出）。</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">（英扎吉） <a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977874.3795.gif\" alt=\"悠然\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '火箭', '', ''), 
(112, '全明星第三次投票 姚明仍居首位詹姆斯领跑东部_网易体育', '\n<ul><li>\n　　NBA官方在今天公布了2007年全明星投票的第三次结果，休斯敦火箭队的姚明仍然在总票数上领先全联盟，西部的另一位领先者是洛杉矶湖人队的科比。东部联盟骑士队的詹姆斯和热队的韦德处于领跑位置\n</li></ul>\n', 1167960328, 'evan', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　NBA官方在今天公布了2007年全明星投票的第三次结果，休斯敦火箭队的姚明仍然在总票数上领先全联盟，西部的另一位领先者是洛杉矶湖人队的科比。东部联盟骑士队的詹姆斯和热队的韦德处于领跑位置\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\">网易体育1月5日消息，<a href=http://sports.163.com/nba/>NBA</a>官方在今天公布了2007年全明星投票的第三次结果反馈，休斯敦<a href=http://sports.163.com/special/0005222R/rockets.html target=_blank>火箭队</a>(<a href=http://blog.163.com/rocketsblog>blog</a>)的<a href=http://sports.163.com/special/0005222S/yao.html>姚明</a>（<a href=http://wiki.sports.163.com/stars/9/9d9cd68285d340efde44f7bc81183f60.html>我来编辑姚明的资料</a>）仍然在总票数上领先全联盟，西部的另一位领先者是洛杉矶<a href=http://sports.163.com/special/0005222R/lakers.html target=_blank>湖人队</a>(<a href=http://blog.163.com/lakersblog/>blog</a>)的<a href=http://sports.163.com/special/0005222S/kobe.html>科比</a>-布莱恩特。在东部联盟克里夫兰<a href=http://sports.163.com/special/0005222Q/Cleveland_Cavaliers.html>骑士队</a>的勒布朗-<a href=http://sports.163.com/special/0005222S/LeBron_James.html>詹姆斯</a>和迈阿密<a href=http://sports.163.com/special/0005222Q/Miami_Heat.html target=_blank>热队</a>(<a href=http://blog.163.com/heatblog>blog</a>)的<a href=http://sports.163.com/special/0005222S/Dwyane_Wade.html>韦德</a>处于领跑位置。 <STRONG>【</STRONG><a href=\"http://www.nba.com/allstar2007/asb/sch/ballot.html\" target=\"_blank\"><STRONG><FONT color=#ff0000>点此为姚明投票</FONT></STRONG></A><STRONG>】</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">根据2007年NBA全明星赛的投票程序，全世界的NBA球迷都可以投票选出参加第56届NBA全明星赛的东西部首发球员，这场比赛将于美国时间2月18日在拉斯维加斯进行。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>姚明目前得到1629832张选票排名所有球员的首位</STRONG>，尽管在与<a href=http://sports.163.com/special/0005222R/clipper.html>快船队</a>的比赛中不幸受伤，可是球迷们对于姚明的投票热情并没有任何减退，这样的票数也让他排名西部联盟中锋首位。明尼苏达<a href=http://sports.163.com/special/0005222R/timberwolves.html>森林狼队</a>的<a href=http://sports.163.com/special/0005222S/garnett.html>加内特</a>以947040票位居西部前锋位置首位，紧随其后的是<a href=http://sports.163.com/special/0005222R/spurs.html>马刺队</a>的<a href=http://sports.163.com/special/0005222S/duncan.html>邓肯</a>（852827票），火箭队的<a href=http://sports.163.com/special/0005222S/battier.html>巴蒂尔</a>以539714票排名西部前锋第五位。</P>\r\n<P style=\"TEXT-INDENT: 2em\">湖人队的科比以1386477票排名西部后卫的第一位，火箭队的<a href=http://sports.163.com/special/0005222S/tracy.html>麦蒂</a>以1205510票排名后卫第二位，不久前由费城<a href=http://sports.163.com/special/0005222Q/Philadelphia_76ers.html>76人队</a>交换到丹佛<a href=http://sports.163.com/special/0005222R/nuggets.html>掘金队</a>的<a href=http://sports.163.com/special/0005222S/Allen_Iverson.html>艾弗森</a>以1157031票排名西部后卫第三位，由于艾弗森交易时他的总票数还没有完全公布，因此他的东部联盟得票数也将记在目前的总票数之内。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>骑士队的詹姆斯以1587738票排名东部所有球员首位</STRONG>，他也是前锋位置的领头羊。热队的韦德以1191403票排名第二位，他是后卫位置的领头羊。<a href=http://sports.163.com/special/0005222Q/Toronto_Raptors.html>猛龙队</a>的<a href=http://sports.163.com/special/0005222S/Chris_Bosh.html>波什</a>（589829票）位居前锋位置第二位，<a href=http://sports.163.com/special/0005222Q/NewJersey_Nets.html>网队</a>的卡特则以867437票排名后卫位置第二位。热队的<a href=http://sports.163.com/special/0005222S/Shaquille_ONeal.html>奥尼尔</a>以1013747票在中锋位置领先，<a href=http://sports.163.com/special/0005222Q/Orlando_Magic.html>魔术队</a>的<a href=http://wiki.sports.163.com/stars/9/937aa9ef9303da7b7789f4698d22705b.html target=_blank>霍华德</a>得到789950票暂居第二位。</P>\r\n<P style=\"TEXT-INDENT: 2em\">2007年的NBA全明星投票还将持续一段时间，纸张投票将进行到1月15日，网上投票截止到1月21日。当地时间1月25日NBA官方将在TNT电视台的比赛直播时宣布东西部联盟的首发球员名单，在这份名单公布后东西部30支球队的主帅将投票选出他们心目中的全明星替补球员，这将在2月1日TNT的比赛直播前宣布。</P>\r\n<P style=\"TEXT-INDENT: 2em\">以下是东西部联盟全明星投票的第三次结果反馈票数：</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>西部联盟</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">中锋：<STRONG><FONT color=#ff0000>姚明（火箭队）1629832</FONT></STRONG>、斯塔德迈尔（<a href=http://sports.163.com/special/00051TAK/taiyangduiziliao.html target=_blank>太阳队</a>）610444、丹皮尓（<a href=http://sports.163.com/special/0005222R/mavericks.html>小牛队</a>）246918、 奥库（<a href=http://sports.163.com/special/0005222R/jazz.html>爵士队</a>）189778、坎比（掘金队）154712、米勒（<a href=http://sports.163.com/special/0005222R/king.html>国王队</a>）81424、埃尔森（马刺队）71114、<a href=http://sports.163.com/special/0005222S/chandler.html>钱德勒</a>（<a href=http://sports.163.com/special/0005222R/hornet.html>黄蜂队</a>）69851、<a href=http://sports.163.com/special/0005222S/mihm.html>米姆</a>（湖人队）69840、卡曼（快船队）67430。</P>\r\n<P style=\"TEXT-INDENT: 2em\">前锋：加内特（森林狼队）947040、邓肯（马刺队）852827、<a href=http://sports.163.com/special/0005222S/dirk.html>诺维茨基</a>（小牛队）794581、 <a href=http://sports.163.com/special/0005222S/Carmelo_Anthony.html>安东尼</a>（掘金队）769491、巴蒂尔（火箭队）539714、<a href=http://sports.163.com/special/0005222S/marion.html>马里昂</a>（太阳队）283766、<a href=http://sports.163.com/special/0005222S/odom.html>奥多姆</a>（湖人队）275327、约什-霍华德（小牛队）256409、布泽尔（爵士队）217872、<a href=http://sports.163.com/special/0005222S/pau.html>加索尔</a>（<a href=http://sports.163.com/special/0005222R/grizzlies.html>灰熊队</a>）198075。</P>\r\n<P style=\"TEXT-INDENT: 2em\">后卫：<STRONG>科比（湖人队）1386477</STRONG>、麦蒂（火箭队）1205510、艾弗森（掘金队）1157031、 <a href=http://sports.163.com/special/0005222S/nash.html>纳什</a>（太阳队）869645、<a href=http://sports.163.com/special/0005222S/chris_paul.html>保罗</a>（黄蜂队）276477、<a href=http://sports.163.com/special/000521QE/terry.html>特里</a>（<a href=http://wiki.sports.163.com/stars/0/0967fe6984174bd9b553e2a155ff2d3d.html>我来编辑特里的资料</a>）（小牛队）267698、<a href=http://sports.163.com/special/0005222S/ginobili.html>吉诺比利</a>（马刺队）256362、<a href=http://sports.163.com/special/0005222S/tony_parker.html>帕克</a>（马刺队）242929、阿伦（<a href=http://sports.163.com/special/0005222R/sonic.html>超音速队</a>）221003、斯塔克豪斯（小牛队）220385。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>东部联盟</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">中锋：奥尼尔（热队）1013747、霍华德（魔术队）789950、 华莱士（<a href=http://sports.163.com/special/0005222Q/Chicago_Bulls.html>公牛队</a>）451575、 <a href=http://sports.163.com/special/0005222S/Alonzo_Mourning.html>莫宁</a>（热队）155605、帕楚里亚（<a href=http://sports.163.com/special/0005222Q/Atlanta_Hawks.html>鹰队</a>）103301、博格特（<a href=http://sports.163.com/special/0005222Q/Milwaukee_Bucks.html>雄鹿队</a>）103321、伊尔戈斯卡斯（骑士队）88979、穆罕默德（<a href=http://sports.163.com/special/0005222Q/Detroit_Pistons.html>活塞队</a>）73139、库里（<a href=http://sports.163.com/special/0005222Q/NewYork_Knicks.html>尼克斯队</a>）68445、科斯蒂奇（网队）62550。</P>\r\n<P style=\"TEXT-INDENT: 2em\">前锋：<STRONG>詹姆斯（骑士队）1587738</STRONG>、 波什（猛龙队）589829、杰梅因-奥尼尔（<a href=http://sports.163.com/special/0005222Q/Indiana_Pacers.html>步行者队</a>） 531531、 皮尔斯（<a href=http://sports.163.com/special/0005222Q/Boston_Celtics.html>凯尔特人队</a>）382287、 希尔（魔术队）331931、 拉希德-华莱士（活塞队）299296、伊戈达拉（76人）260993、普林斯（活塞队）200065、奥卡福（<a href=http://sports.163.com/special/0005222Q/bobcat.html>山猫队</a>）186445、 韦伯（76人队）173572。</P>\r\n<P style=\"TEXT-INDENT: 2em\">后卫：<STRONG>韦德（热队）1191403</STRONG>、卡特（网队）867437、阿里纳斯（<a href=http://sports.163.com/special/0005222Q/Washington_Wizards.html>奇才队</a>）647281、基德（网队）490622、马布里（尼克斯队）347994、比卢普斯（活塞队）244236、里德（雄鹿队）192280、约翰逊（鹰队）160280、弗朗西斯（尼克斯队）155676、汉密尔顿（活塞队）136946。</P>\r\n<P style=\"TEXT-INDENT: 2em\">（evan） <a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977874.5633.gif\" alt=\"悠然\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '姚明', '', ''), 
(113, '连胜小牛占据实力榜首位 火箭上升三位排名第九_网易体育', '\n<ul><li>\n　　NBA的第10期球队实力榜出炉，本赛季第二次拿到10场以上连胜的达拉斯小牛队重返实力榜首位，菲尼克斯太阳队和犹他爵士队紧随其后。休斯敦火箭队在拿到平赛季最长的4连胜后排名上升三位，他们位居第9位。\n</li></ul>\n', 1167958206, 'evan', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　NBA的第10期球队实力榜出炉，本赛季第二次拿到10场以上连胜的达拉斯小牛队重返实力榜首位，菲尼克斯太阳队和犹他爵士队紧随其后。休斯敦火箭队在拿到平赛季最长的4连胜后排名上升三位，他们位居第9位。\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\">网易体育1月5日消息，<a href=http://sports.163.com/nba/>NBA</a>的第10期球队实力榜出炉，本赛季第二次拿到10场以上连胜的达拉斯<a href=http://sports.163.com/special/0005222R/mavericks.html>小牛队</a>重返实力榜首位，菲尼克斯<a href=http://sports.163.com/special/00051TAK/taiyangduiziliao.html target=_blank>太阳队</a>和犹他<a href=http://sports.163.com/special/0005222R/jazz.html>爵士队</a>紧随其后。休斯敦<a href=http://sports.163.com/special/0005222R/rockets.html target=_blank>火箭队</a>(<a href=http://blog.163.com/rocketsblog>blog</a>)在拿到平赛季最长的4连胜后排名上升三位，他们位居第9位。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>排名涨幅最大的球队：孟菲斯灰熊（＋4）</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>排名跌幅最大的球队：丹佛掘金（-5）</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">1．<STRONG>达拉斯小牛</STRONG>（2）括号内为上周排名，下同 25胜7负：小牛队是历史上第20支在同一赛季里拿到两次10场以上连胜的球队，今天迎战<a href=http://sports.163.com/special/0005222Q/Indiana_Pacers.html>步行者队</a>的他们力争12连胜。</P>\r\n<P style=\"TEXT-INDENT: 2em\">2．<STRONG>菲尼克斯太阳</STRONG>（1） 23胜8负：他们输给了联盟最炙手可热的球队（也是此前排名该榜单第二位的球队），这让他们的名次不得不下滑一位，即使他们已经拿到四连胜。</P>\r\n<P style=\"TEXT-INDENT: 2em\">3．<STRONG>犹他爵士</STRONG>（3） 23胜9负：爵士队本赛季在自己的主场有14胜2负的战绩，在做客迎战火箭队和<a href=http://sports.163.com/special/0005222R/nuggets.html>掘金队</a>后，他们将在主场对垒小牛队，他们争取要提高联盟最佳主场战绩。</P>\r\n<P style=\"TEXT-INDENT: 2em\">4．<STRONG>圣<a href=http://sports.163.com/special/0005222S/Carmelo_Anthony.html>安东尼</a>奥马刺</STRONG>（5） 23胜10负：自从2005年3月20日-23日的东部之旅输给<a href=http://sports.163.com/special/0005222Q/Detroit_Pistons.html>活塞队</a>、<a href=http://sports.163.com/special/0005222Q/NewYork_Knicks.html>尼克斯队</a>和步行者队后他们就再也没有遭遇过三连败，明天他们要在对垒小牛队时避免遭遇三连败。</P>\r\n<P style=\"TEXT-INDENT: 2em\">5．<STRONG>华盛顿奇才</STRONG>（7） 18胜13负：我并不在意阿里纳斯的道德问题，我只在意<a href=http://sports.163.com/special/0005222Q/Washington_Wizards.html>奇才队</a>的表现，只要阿里纳斯继续这种表现。</P>\r\n<P style=\"TEXT-INDENT: 2em\">6．<STRONG>底特律活塞</STRONG>（4） 18胜11负：比卢普斯随队前往俄克拉荷马城，不过他似乎要缺席与<a href=http://sports.163.com/special/0005222R/hornet.html>黄蜂队</a>的比赛，他也可能缺席周日在芝加哥与大本的遭遇战。</P>\r\n<P style=\"TEXT-INDENT: 2em\">7．<STRONG>芝加哥公牛</STRONG>（8 ） 19胜13负：当<a href=http://sports.163.com/special/0005222Q/Chicago_Bulls.html>公牛队</a>主场迎战活塞队时，大本的老队友们将考验他身边的新队友。</P>\r\n<P style=\"TEXT-INDENT: 2em\">8．<STRONG>洛杉矶湖人</STRONG>（6） 20胜11负：布朗的受伤将让拜纳姆重新成为首发球员，在今年出任首发的14场比赛中，他场均得到8.5分和6.3个篮板，而作为替补只有6.1分和4.2个篮板。</P>\r\n<P style=\"TEXT-INDENT: 2em\">9．<STRONG><FONT color=#ff0000>休斯敦火箭</FONT></STRONG>（12） 20胜12负：本赛季当他们主场得分超过100分时火箭队的战绩是6胜0负，过去两场对垒<a href=http://sports.163.com/special/0005222R/grizzlies.html>灰熊队</a>和<a href=http://sports.163.com/special/0005222R/sonic.html>超音速队</a>时他们都得分超过100分，这一周爵士队和<a href=http://sports.163.com/special/0005222R/lakers.html target=_blank>湖人队</a>(<a href=http://blog.163.com/lakersblog/>blog</a>)将做客丰田中心。</P>\r\n<P style=\"TEXT-INDENT: 2em\">10．<STRONG>克里夫兰骑士</STRONG>（10） 19胜12负：<a href=http://sports.163.com/special/0005222Q/Cleveland_Cavaliers.html>骑士队</a>在客场仅有5胜9负的战绩，他们场均得到91.9分，只有42.5％的投篮命中率。在这个周末的客场主场比赛后，他们将开始连续七个客场的西部之旅。</P>\r\n<P style=\"TEXT-INDENT: 2em\">排名第11位到第30位的球队依次是：奥兰多魔术、印第安纳步行者、明尼苏达森林狼、丹佛掘金、密尔沃基雄鹿、金州勇士、洛杉矶快船、萨克拉门托国王、波特兰开拓者、迈阿密热、多伦多猛龙、新泽西网、西雅图超音速、孟菲斯灰熊、纽约尼克斯、波士顿凯尔特人、新奥尔良/俄克拉荷马城黄蜂、夏洛特山猫、费城76人和<a href=http://sports.163.com/special/0005229P/Atalanta.html target=_blank>亚特兰大</a>鹰。</P>\r\n<P style=\"TEXT-INDENT: 2em\">（evan） <a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977874.7272.gif\" alt=\"悠然\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, 'NBA实力榜', '', ''), 
(114, '票王缺阵已有先例 姚明参加全明星赛希望渺茫_网易体育', '\n<ul><li>\n　　既然\"票王\"缺阵已有先例，而且姚明的广告合同中也没有必须要参加这一届全明星赛这一条，那为了姚明的健康着想，他最好也最有可能不参加这届全明星赛\n</li></ul>\n', 1167969394, '梦断金陵', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　既然\"票王\"缺阵已有先例，而且姚明的广告合同中也没有必须要参加这一届全明星赛这一条，那为了姚明的健康着想，他最好也最有可能不参加这届全明星赛\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\"><a href=http://sports.163.com/special/0005222S/yao.html>姚明</a>（<a href=http://wiki.sports.163.com/stars/9/9d9cd68285d340efde44f7bc81183f60.html>我来编辑姚明的资料</a>）的伤势正在恢复，但姚明参加全明星赛的希望却非常渺茫，因为不管对于<a href=http://sports.163.com/special/0005222R/rockets.html target=_blank>火箭队</a>(<a href=http://blog.163.com/rocketsblog>blog</a>)还是姚明本人来说，全明星赛只是走秀的舞台，相比于姚明一个完全健康的身体，参加全明星赛那只不过是无足轻重的一件事。为了确保彻底恢复，姚明极有可能不参加今年的全明星赛。</P>\r\n<P style=\"TEXT-INDENT: 2em\">姚明很有可能再次成为\"票王\"，而\"票王\"缺阵全明星赛并不是什么稀罕事。曾12次入选<a href=http://sports.163.com/nba/>NBA</a>全明星赛的巨星摩西<a href=http://sports.163.com/special/000521U3/malong.html target=_blank>马龙</a>于1983年带领<a href=http://sports.163.com/special/0005222Q/Philadelphia_76ers.html>76人队</a>击败<a href=http://sports.163.com/special/0005222R/lakers.html target=_blank>湖人队</a>(<a href=http://blog.163.com/lakersblog/>blog</a>)勇夺总冠军，他本人也夺得83年总决赛MVP。在这样的赫赫战功之下，1984年他在全明星投票中以927779票高居全联盟第一名，但是他却并没有出现在全明星赛的赛场上，而这是自1975年全明星赛引入球迷投票后的第一次。</P>\r\n<P style=\"TEXT-INDENT: 2em\">第二次\"票王\"缺席发生在1994年，打破了乔丹对全明星\"票王\"垄断的巴克利成为了那年新的\"票王\"，可惜的是巴克利因为四头肌腱撕裂而无法参加当年的全明星赛。他职业生涯好不容易得到一次\"票王\"的荣誉，没想到却不幸错过。</P>\r\n<P style=\"TEXT-INDENT: 2em\">第三次\"票王\"缺阵也就是最近的一次，是2002年的卡特，卡特在2000-2002年连续三年当选\"票王\"，但2002年他因为有伤不得不缺席当年的全明星赛，所以在那一年我们没有看到他精彩的扣篮。</P>\r\n<P style=\"TEXT-INDENT: 2em\">既然\"票王\"缺阵已有先例，而且姚明的广告合同中也没有必须要参加这一届全明星赛这一条，那为了姚明的健康着想，他最好也最有可能不参加这届全明星赛。不过，虽然没有姚明，但全明星赛依然精彩。</P>\r\n<P style=\"TEXT-INDENT: 2em\">（梦断金陵） <a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977874.8942.gif\" alt=\"悠然\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '姚明', '', ''), 
(115, '汉密尔顿势不可挡 活塞击败黄蜂结束三连败_网易体育', '\n<ul><li>\n　　尽管比卢普斯仍然缺阵，但是汉密尔顿足以带领底特律活塞队结束连败。面对缺少保罗、斯托亚科维奇和韦斯特三大主力的新奥尔良/俄克拉荷马城黄蜂队，汉密尔顿率领活塞队击败黄蜂队结束三连败，黄蜂队遭遇三连败\n</li></ul>\n', 1167968849, '小柳', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　尽管比卢普斯仍然缺阵，但是汉密尔顿足以带领底特律活塞队结束连败。面对缺少保罗、斯托亚科维奇和韦斯特三大主力的新奥尔良/俄克拉荷马城黄蜂队，汉密尔顿率领活塞队击败黄蜂队结束三连败，黄蜂队遭遇三连败\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\">网易体育1月5日消息，尽管比卢普斯仍然缺阵，但是汉密尔顿足以带领底特律<a href=http://sports.163.com/special/0005222Q/Detroit_Pistons.html>活塞队</a>结束连败。面对缺少<a href=http://sports.163.com/special/0005222S/chris_paul.html>保罗</a>、斯托亚科维奇和韦斯特三大主力的新奥尔良/俄克拉荷马城<a href=http://sports.163.com/special/0005222R/hornet.html>黄蜂队</a>，<STRONG>汉密尔顿率领活塞队以92-68击败黄蜂队结束三连败，黄蜂队遭遇三连败</STRONG>。</P>\r\n<P style=\"TEXT-INDENT: 2em\">活塞队的<STRONG>汉密尔顿得到27分、5个篮板和5次助攻</STRONG>，普林斯得到15分和10个篮板，德尔菲诺得到13分，马克斯尔得到12分，麦克代斯得到10分和6个篮板，华莱士抢到10个篮板。黄蜂队的<STRONG>帕戈得到16分、8个篮板和6次助攻</STRONG>，梅森得到15分，布朗得到14分和8个篮板，<a href=http://sports.163.com/special/0005222S/chandler.html>钱德勒</a>抢到16个篮板。</P>\r\n<P style=\"TEXT-INDENT: 2em\">活塞队已经遭遇三连败，他们在东部联盟的领先优势也丧失殆尽，今天做客面对缺兵少将的黄蜂队，他们要全力结束连败。黄蜂队已经遭遇两连败，球队四大得分手中的三人受伤，这让他们的排名不断下滑，今天面对近况不佳的活塞队，他们力争结束连败。</P>\r\n<P style=\"TEXT-INDENT: 2em\">两支球队开场后互有攻守，场上比分交替增加，比赛的领先权多次易手。马克-杰克逊跳投命中将比分迫近至12-13后，汉密尔顿连得7分，这让活塞队以20-12领先。博比-杰克逊突破上篮得分后，汉密尔顿率队又打出一轮6-3的攻击波，活塞队以26-17领先。梅森转身跳投命中后，布拉洛克跳投命中，普林斯左侧底角命中3分，活塞队在首节结束时以31-19领先。<STRONG>汉密尔顿在这一节10投7中独得17分。</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">麦克代斯和德尔菲诺在第二节开始后分别进攻得手，活塞队的领先优势扩大为16分。西蒙斯跳投命中后，活塞队又打出一轮9-2的进攻高潮，这让他们在半场前6分40秒时以44-23领先21分。帕戈和梅森合力得到3分后，汉密尔顿连得4分，他带领活塞队又是一波8-2的进攻高潮，半场前2分钟他们以51-28领先23分。帕戈两罚全中后，普林斯和杜普雷合力得到5分，活塞队在半场结束时以58-32领先。</P>\r\n<P style=\"TEXT-INDENT: 2em\">活塞队的汉密尔顿上半场独得22分，普林斯得到9分，麦克代斯得到8分；黄蜂队的帕戈上半场得到8分，梅森和杰克逊各得到7分。</P>\r\n<P style=\"TEXT-INDENT: 2em\">梅森独得4分，他带领黄蜂队以7-0的反击波开始第三节，他们将比分迫近至39-58。穆雷右翼跳投命中后，帕戈、梅森和布朗分别进攻得手，一轮6-2的反击高潮让黄蜂队将差距缩小到17分。汉密尔顿连得4分后，帕戈连续命中两个3分球，黄蜂队继续缩小分差。本节结束前布朗打3分成功，钱德勒两罚一中，这让黄蜂队在前三节过后以55-72落后。</P>\r\n<P style=\"TEXT-INDENT: 2em\">最后决战开始后两队各打成几次进攻，阿姆斯特朗和西蒙斯各得两分后，黄蜂队以63-77落后14分。德尔菲诺圈顶命中3分，普林斯和马克斯尔分别进攻得手，<STRONG>一轮7-0的攻击波让活塞队在终场前5分15秒时以84-63领先</STRONG>。黄蜂队暂停后普林斯和德尔菲诺分别进攻得手，活塞队继续扩大领先。布朗外线命中3分后，德尔菲诺跳投命中，终场前3分15秒时活塞队以90-66领先。大比分差距让剩余比赛变成垃圾时间，双方都尽遣替补出战，最终活塞队以92-68赢得胜利。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>活塞队首发阵容：穆雷、汉密尔顿、普林斯、华莱士、穆罕默德</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>黄蜂队首发阵容：帕戈、<a href=http://sports.163.com/special/000521QB/Butt.html target=_blank>巴特</a>勒、梅森、马克-杰克逊、钱德勒</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">（小柳） </P>\r\n<META http-equiv=Content-Language content=zh-cn>\r\n<TABLE id=table1 borderColor=#ffffff cellPadding=2 width=\"100%\" bgColor=#ffefce border=1>\r\n<TBODY>\r\n<TR>\r\n<TD width=0 bgColor=#ffcc99 colSpan=13 height=0><B>活塞 92</B></TD></TR>\r\n<TR>\r\n<TD width=0 bgColor=#ffcc99 height=0>球员</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>时间</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>投篮</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>三分</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>罚球</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>进攻篮板</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>篮板</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>助攻</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>失误</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>抢断</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>盖帽</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>犯规</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>得分</TD></TR>\r\n<TR>\r\n<TD align=left>普林斯</TD>\r\n<TD>39 </TD>\r\n<TD>6-15 </TD>\r\n<TD>2-4 </TD>\r\n<TD>1-2 </TD>\r\n<TD>0 </TD>\r\n<TD>10 </TD>\r\n<TD>3 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>1 </TD>\r\n<TD>15 </TD></TR>\r\n<TR>\r\n<TD align=left>华莱士</TD>\r\n<TD>27 </TD>\r\n<TD>1-4 </TD>\r\n<TD>0-1 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0 </TD>\r\n<TD>10 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD></TR>\r\n<TR>\r\n<TD align=left>莫罕默德</TD>\r\n<TD>15 </TD>\r\n<TD>1-2 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>1 </TD>\r\n<TD>3 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD></TR>\r\n<TR>\r\n<TD align=left>汉密尔顿</TD>\r\n<TD>34 </TD>\r\n<TD>10-18 </TD>\r\n<TD>1-1 </TD>\r\n<TD>6-7 </TD>\r\n<TD>0 </TD>\r\n<TD>5 </TD>\r\n<TD>5 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>5 </TD>\r\n<TD>27 </TD></TR>\r\n<TR>\r\n<TD align=left>麦克代斯</TD>\r\n<TD>23 </TD>\r\n<TD>5-7 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>2 </TD>\r\n<TD>6 </TD>\r\n<TD>1 </TD>\r\n<TD>2 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>10 </TD></TR>\r\n<TR>\r\n<TD align=left>麦克西尔</TD>\r\n<TD>21 </TD>\r\n<TD>4-5 </TD>\r\n<TD>0-0 </TD>\r\n<TD>4-6 </TD>\r\n<TD>3 </TD>\r\n<TD>4 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>1 </TD>\r\n<TD>2 </TD>\r\n<TD>12 </TD></TR>\r\n<TR>\r\n<TD align=left>德尔菲诺</TD>\r\n<TD>15 </TD>\r\n<TD>3-8 </TD>\r\n<TD>1-3 </TD>\r\n<TD>6-6 </TD>\r\n<TD>3 </TD>\r\n<TD>6 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>13 </TD></TR>\r\n<TR>\r\n<TD align=left>布雷洛克</TD>\r\n<TD>16 </TD>\r\n<TD>1-4 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>2 </TD>\r\n<TD>1 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD></TR>\r\n<TR>\r\n<TD align=left>约翰逊</TD>\r\n<TD>3 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD></TR>\r\n<TR>\r\n<TD align=left>穆雷</TD>\r\n<TD>34 </TD>\r\n<TD>3-9 </TD>\r\n<TD>0-1 </TD>\r\n<TD>1-2 </TD>\r\n<TD>0 </TD>\r\n<TD>3 </TD>\r\n<TD>5 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>7 </TD></TR>\r\n<TR>\r\n<TD align=left>德普利</TD>\r\n<TD>7 </TD>\r\n<TD>1-4 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD></TR>\r\n<TR>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD></TR>\r\n<TR>\r\n<TD>合计</TD>\r\n<TD>234 </TD>\r\n<TD>35-76 </TD>\r\n<TD>4-10 </TD>\r\n<TD>18-23 </TD>\r\n<TD>9 </TD>\r\n<TD>51 </TD>\r\n<TD>17 </TD>\r\n<TD>13 </TD>\r\n<TD>7 </TD>\r\n<TD>3 </TD>\r\n<TD>13 </TD>\r\n<TD>92 </TD></TR>\r\n<TR>\r\n<TD>平均</TD>\r\n<TD></TD>\r\n<TD>.461 </TD>\r\n<TD>.400 </TD>\r\n<TD>.783 </TD>\r\n<TD></TD>\r\n<TD colSpan=7>　</TD></TR></TBODY></TABLE>\r\n<TABLE id=table2 borderColor=#ffffff cellPadding=2 width=\"100%\" bgColor=#ffefce border=1>\r\n<TBODY>\r\n<TR>\r\n<TD width=0 bgColor=#ffcc99 colSpan=13 height=0><B>黄蜂 68</B></TD></TR>\r\n<TR>\r\n<TD width=0 bgColor=#ffcc99 height=0>球员</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>时间</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>投篮</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>三分</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>罚球</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>进攻篮板</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>篮板</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>助攻</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>失误</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>抢断</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>盖帽</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>犯规</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>得分</TD></TR>\r\n<TR>\r\n<TD align=left>梅森</TD>\r\n<TD>34 </TD>\r\n<TD>7-18 </TD>\r\n<TD>0-0 </TD>\r\n<TD>1-2 </TD>\r\n<TD>2 </TD>\r\n<TD>5 </TD>\r\n<TD>3 </TD>\r\n<TD>3 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>4 </TD>\r\n<TD>15 </TD></TR>\r\n<TR>\r\n<TD align=left>钱德勒</TD>\r\n<TD>34 </TD>\r\n<TD>1-3 </TD>\r\n<TD>0-0 </TD>\r\n<TD>1-2 </TD>\r\n<TD>5 </TD>\r\n<TD>16 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>3 </TD></TR>\r\n<TR>\r\n<TD align=left>帕戈</TD>\r\n<TD>44 </TD>\r\n<TD>6-24 </TD>\r\n<TD>2-7 </TD>\r\n<TD>2-2 </TD>\r\n<TD>2 </TD>\r\n<TD>8 </TD>\r\n<TD>6 </TD>\r\n<TD>3 </TD>\r\n<TD>3 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>16 </TD></TR>\r\n<TR>\r\n<TD align=left>巴特勒</TD>\r\n<TD>28 </TD>\r\n<TD>1-10 </TD>\r\n<TD>0-1 </TD>\r\n<TD>1-2 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>3 </TD></TR>\r\n<TR>\r\n<TD align=left>B.杰克逊</TD>\r\n<TD>9 </TD>\r\n<TD>3-5 </TD>\r\n<TD>1-2 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>7 </TD></TR>\r\n<TR>\r\n<TD align=left>阿姆斯特朗</TD>\r\n<TD>12 </TD>\r\n<TD>1-3 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>1 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>3 </TD>\r\n<TD>2 </TD></TR>\r\n<TR>\r\n<TD align=left>布朗</TD>\r\n<TD>29 </TD>\r\n<TD>6-12 </TD>\r\n<TD>1-4 </TD>\r\n<TD>1-1 </TD>\r\n<TD>4 </TD>\r\n<TD>8 </TD>\r\n<TD>1 </TD>\r\n<TD>1 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>3 </TD>\r\n<TD>14 </TD></TR>\r\n<TR>\r\n<TD align=left>巴斯</TD>\r\n<TD>9 </TD>\r\n<TD>0-4 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD></TR>\r\n<TR>\r\n<TD align=left>M.杰克逊</TD>\r\n<TD>24 </TD>\r\n<TD>2-7 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0 </TD>\r\n<TD>3 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>4 </TD></TR>\r\n<TR>\r\n<TD align=left>西蒙斯</TD>\r\n<TD>14 </TD>\r\n<TD>2-2 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>4 </TD>\r\n<TD>4 </TD></TR>\r\n<TR>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD></TR>\r\n<TR>\r\n<TD>合计</TD>\r\n<TD>237 </TD>\r\n<TD>29-88 </TD>\r\n<TD>4-14 </TD>\r\n<TD>6-9 </TD>\r\n<TD>14 </TD>\r\n<TD>45 </TD>\r\n<TD>13 </TD>\r\n<TD>13 </TD>\r\n<TD>6 </TD>\r\n<TD>3 </TD>\r\n<TD>20 </TD>\r\n<TD>68 </TD></TR>\r\n<TR>\r\n<TD>平均</TD>\r\n<TD></TD>\r\n<TD>.330 </TD>\r\n<TD>.286 </TD>\r\n<TD>.667 </TD>\r\n<TD></TD>\r\n<TD colSpan=7>　</TD></TR></TBODY></TABLE><a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977875.0695.gif\" alt=\"悠然\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '活塞', '', ''), 
(116, '火箭老后卫复出希望渺茫 范甘迪彻底放弃苏拉_网易体育', '\n<ul><li>\n　　“虽然他很努力的进行恢复训练，但说实话，我对他能复出并不抱有期望，”范甘迪对记者说\n</li></ul>\n', 1167967444, '大被', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　“虽然他很努力的进行恢复训练，但说实话，我对他能复出并不抱有期望，”范甘迪对记者说\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\">网易体育1月5日讯：上赛季火箭后卫严重缺乏，在<a href=http://sports.163.com/special/0005222S/tracy.html>麦蒂</a>缺席的情况下，球队可用的后场球员捉襟见肘。不过在本赛季，这一状况得到很好的改观，施耐德、<a href=http://sports.163.com/special/0005222S/head.html>海德</a>、卢卡斯、斯潘以及痊愈的麦蒂，火箭后场相当坚挺，这也让人们逐渐忘记了仍在养伤的苏拉。在取得四连胜后，<a href=http://sports.163.com/special/0005222S/Van_Gundy.html>范甘迪</a>接受休斯敦当地媒体采访时表示他对苏拉已经不抱希望。</P>\r\n<P style=\"TEXT-INDENT: 2em\">“虽然他很努力的进行恢复训练，但说实话，我对他能复出并不抱有期望，”范甘迪对记者说，“本赛季是他缺席的第二年，我们很遗憾的不知道他何时能复出，他的伤病影响了他的职业前途。”目前火箭后卫线的表现非常出色，海德已经成为火箭重要得分点，诺瓦克的三分绝技也是范甘迪所欣赏的地方，而小将卢卡斯和斯潘的组织能力以及突破能力，都具有发展潜力。随着复出的日期越来越模糊，苏拉已经逐渐远离<a href=http://sports.163.com/special/0005222R/rockets.html target=_blank>火箭队</a>(<a href=http://blog.163.com/rocketsblog>blog</a>)伍。</P>\r\n<P style=\"TEXT-INDENT: 2em\">苏拉在联盟已经征战了11个赛季，不过最近2年因为伤病，他一直远离赛场，苦苦的同伤病做斗争，他上次参加比赛，还是20个月前的事，就算能提前复出，他的状态以及是否能尽快适应火箭的战术体系，还是一个大问题。目前火箭同苏拉还有384万美元的合同，但在本赛季结束后，火箭很可能裁掉这位老将或者将其送走。</P>\r\n<P style=\"TEXT-INDENT: 2em\">对于自己能否回到赛场，苏拉表示一切都得听医生的建议，“现在我一步一步的在恢复，具体的复出时间还得听医生的建议，”苏拉说，当记者说范甘迪对其不抱有期望时，苏拉说：“我希望回到赛场上，其他的不是我关心的问题。”在2005年季后赛同小牛对战受伤后，苏拉就再也没有代表火箭出战过。</P>\r\n<P style=\"TEXT-INDENT: 2em\">（大被）<a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977875.2395.gif\" alt=\"悠然\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '苏拉', '', ''), 
(117, '体脂超标无缘比赛 为救主热队二将拼命减肥_网易体育', '', 1167967050, '袁汉', '《东方体育日报》', '\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\">“我们是一支偏离了轨道的冠军球队。”帕特·莱利暂时离开<a href=http://sports.163.com/special/0005222Q/Miami_Heat.html target=_blank>热队</a>(<a href=http://blog.163.com/heatblog>blog</a>)前这样说。卸下肩头的主帅担子，莱利依然是热队总裁，他的规矩依然有效，倒霉的沃克和波西就在莱利离队前夕撞到枪口上。由于体脂含量超过莱利定下的标准，沃克和波西无缘昨天热与快艇的比赛。全联盟中热对球员体形要求最严格，沃克和波西的体形其实与上赛季相差不远，但现在他们还是不得不在体能教练福伦的监督下拼命减肥，以求赶上下一场对太阳的比赛。</P>\r\n<P style=\"TEXT-INDENT: 2em\">莱利称，将沃克和波西放入休赛名单并非对他们的惩罚，只是执行队规：“球员的体形是我执教哲学的根基，对此我深信不疑。沃克和波西并非犯了什么错，他们只是没达到标准。这两名球员的缺阵无疑伤害全队，我希望他们能从中汲取教训。”</P>\r\n<P style=\"TEXT-INDENT: 2em\">莱利不失幽默地表示，沃克和波西的体形“用日常生活的眼光来看已相当棒了”，但职业球员的标准理应更高。同时莱利还预计沃克和波西都能在几天后归队，不过要是他们在1月15日还达不到体脂含量标准，也不排除对他们处以队内停赛处罚的可能。</P>\r\n<P style=\"TEXT-INDENT: 2em\">昨天的比赛开始前，沃克垂头丧气地坐在自己的衣柜前，偶尔与队友小声说上几句话。莱利为沃克定下的体脂含量标准是10％，而他最近的测试结果是11％。波西的测试结果也只比8％的标准高了一个百分点，这让他在回答问题时还显得怨气冲天。</P>\r\n<P style=\"TEXT-INDENT: 2em\">“教练的决定对我有什么影响？”波西说，“就像我说过的那样，我的自信永远不会改变，我对自己的要求也永远不会改变。每个球员都有各自的体脂含量标准，我没能达到标准，就是这么简单。我会继续刻苦训练，直到让教练满意为止。”<a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977875.4555.gif\" alt=\"悠然\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '热队', '', ''), 
(118, '不服裁判科尔曼接到罚单 三赛区混乱同受制裁_网易体育', '\n<ul><li>\n　　新疆队外援科尔曼在与山东队客场比赛中有不服裁判判罚的行为，两次技术犯规被直接罚下，结果本人被罚款1万元并停赛一场\n</li></ul>\n', 1167966612, '小林', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　新疆队外援科尔曼在与山东队客场比赛中有不服裁判判罚的行为，两次技术犯规被直接罚下，结果本人被罚款1万元并停赛一场\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\">北京时间2007年元月4日，篮管中心开出2007年的首批罚单，这也预示着CBA新的一年篮管中心正式向违规行为宣战。北京赛区惊现的87.4秒事件，造成现场的诸多混乱，因此北京赛区被严重警告的同时，北京队由于球员和教练的情绪比较激动，也被通报批评；而乌鲁木齐赛区和太原赛区由于发生了球迷投掷矿泉水瓶事件，被各罚5000元，新疆队外援<a href=http://sports.163.com/special/0005223O/Coleman.html target=_blank>科尔曼</a>在与山东队客场比赛中有不服裁判判罚的行为，两次技术犯规被直接罚下，结果本人被罚款1万元并停赛一场，停赛已经在和江苏队的客场比赛中执行了。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>北京计时器事件</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">北京和<a href=http://sports.163.com/special/00051SKJ/liaozuwiki.html>辽宁</a>队的比赛进行到离全场结束还有6.4秒时，仅落后1分的辽宁队持球外援在北京队包夹下走步违例，辽宁叫了30秒短暂停。但是明明仅剩下6.4秒却忽然变成了86秒4。辽宁队开始犯规战术，但却停表了，这引起北京队极大不满，特别是此前裁判已经连续罚下了张云松和门维两位主力。焦健再度抢到后场篮板直接冲向技术台，此时计时器显示66秒9。终于技术代表在一片混乱中宣布全场比赛结束。根据《2006—2007中国男子篮球职业联赛纪律处罚规定》中第七章第五十八条第一款的规定，中国篮协给予北京赛区严重警告；同时对北京金隅队给予通报批评的处罚。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>科尔曼违规事件</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">在12月31日新疆队客场对阵山东队的比赛中，新疆队外援科尔曼连续与两名山东队球员发生冲突，科尔曼被主裁判判罚两次违体犯规，主裁判随即取消了科尔曼当场比赛资格。根据《2006—2007中国男子篮球职业联赛纪律处罚规定》第二章第九条第二、三款的有关规定，给予新疆广汇外援科尔曼罚款10000元(个人、俱乐部各承担5000元)，停赛一场的处罚(第17轮比赛)，同时，根据《2006—2007中国男子篮球职业联赛纪律处罚规定》第二章第九条第一款的规定，给予山东队队员孙杰罚款4000元(个人、俱乐部各承担2000元)的处罚。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>球迷不冷静扔水瓶赛区被罚</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">12月29日新疆主场对八一的比赛和12月31日山西对吉林的比赛中，现场的部分观众因对裁判员的判罚表示不满，观众席矿泉水瓶投掷到场地上造成比赛中断数分钟。篮管中心根据《2006—2007中国男子篮球职业联赛纪律处罚规定》中第六章第五十五条第二款的规定，分别给予新疆乌鲁木齐赛区和山西太原赛区通报批评并分别扣发承办单位联赛经费5000元的处罚。（小林）<a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977875.7354.gif\" alt=\"悠然\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '科尔曼', '', ''), 
(119, '休斯敦配角获胜不可缺 火箭找到制敌新方法_网易体育', '', 1167965982, '潘丽娟', '《东方体育日报》', '\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\">无论在<a href=http://sports.163.com/special/0005222S/tracy.html>麦蒂</a>伤停的时候，还是在<a href=http://sports.163.com/special/0005222S/yao.html>姚明</a>（<a href=http://wiki.sports.163.com/stars/9/9d9cd68285d340efde44f7bc81183f60.html>我来编辑姚明的资料</a>）歇战的时候，火箭的表现都说明了这一点：他们从来都不是单凭领袖的一己之力就能决定战局的球队。</P>\r\n<P style=\"TEXT-INDENT: 2em\">超音速主帅鲍勃·希尔对于本队的篮板球远远低于火箭而大感恼火，毕竟对手的篮下统治者姚明缺席，而替补出场的<a href=http://sports.163.com/special/0005222S/mutombo.html>穆托姆博</a>已经40岁高龄。昨天非洲大山出场28分钟：“说到抢篮板球这事儿，我还有很多可以做的。现在，我就要尽可能地做一切去帮助球队获胜。”</P>\r\n<P style=\"TEXT-INDENT: 2em\">近期手风正顺的<a href=http://sports.163.com/special/0005222S/battier.html>巴蒂尔</a>和超音速神射手雷·阿伦比划了一下，较量的不仅是外线投篮，还有防守。“两者都很困难，我不知道一对一面对他时攻防哪一样更轻松些。”巴蒂尔赛后坦言。在全场比赛中，巴蒂尔几乎一直在贴身紧逼雷·阿伦，而他本人则拿下12分，包括第二节中的连续两个三分球。“有些人会因为疲于防守而投篮不多，有些人会只顾着进攻而忘了会防，但是对31号（巴蒂尔）来说，每一个夜晚他都会在两个篮下不知疲倦地奔跑，”火箭主帅<a href=http://sports.163.com/special/0005222S/Van_Gundy.html>范甘迪</a>毫不掩饰对这位工兵球员的赏识，“他总能在每一场比赛中用不同的方式做出自己的贡献。”</P>\r\n<P style=\"TEXT-INDENT: 2em\">然而前两场比赛命中率均低于30％的<a href=http://sports.163.com/special/0005222S/alston.html>阿尔斯通</a>仍在挣扎，昨天他的前6次出手无一命中，但此后8投6中，全场贡献了13分。“拉夫陷入了困境，”范甘迪表示了对他的信任，“我真的为他感到骄傲。他在下半场的意志非常坚定，表现相当不错。”<a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977875.8927.gif\" alt=\"悠然\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '替补', '', ''), 
(120, '火箭连胜助力姚明当选 全明星票王名副其实_网易体育', '\n<ul><li>\n　　姚明的影响力很大，人气很高，他的个人成绩优异，团队成绩又相当不错，全明星票王名副其实。\n</li></ul>\n', 1167964150, '梦断金陵', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　姚明的影响力很大，人气很高，他的个人成绩优异，团队成绩又相当不错，全明星票王名副其实。\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\">全明星的投票仍在继续，而在最新公布的选票情况中，<a href=http://sports.163.com/special/0005222S/yao.html>姚明</a>（<a href=http://wiki.sports.163.com/stars/9/9d9cd68285d340efde44f7bc81183f60.html>我来编辑姚明的资料</a>）依然排名第一，他以1629832张选票继续领先全联盟，而排在得票第二的是<a href=http://sports.163.com/special/0005222Q/Cleveland_Cavaliers.html>骑士队</a>的\"小皇帝\"<a href=http://sports.163.com/special/0005222S/LeBron_James.html>詹姆斯</a>，他得到1587738张选票。虽然两人的票数差距比之前进一步缩小，但至今为止，姚明依然排名第一，而这个荣誉对于他来说也是名副其实。</P>\r\n<P style=\"TEXT-INDENT: 2em\">全明星的投票不是单纯球技的竞争，还是形象和人气的竞争，而由于竞争对手的难度不同，所以每个位置上的球员面临的对手也不一样。比如两届MVP得主<a href=http://sports.163.com/special/0005222S/nash.html>纳什</a>，他在西区后卫的排名中只排名第四，原因在于他的竞争对手太强，分别是三大人气球星<a href=http://sports.163.com/special/0005222S/kobe.html>科比</a>，<a href=http://sports.163.com/special/0005222S/tracy.html>麦蒂</a>和<a href=http://sports.163.com/special/0005222S/Allen_Iverson.html>艾弗森</a>。此三人的球迷众多，球技又不差，纳什排名第四，只能怪运气不佳。而像\"小皇帝\"詹姆斯，他所在的东区前锋实力较弱，所以排名在他后面的<a href=http://sports.163.com/special/0005222S/Chris_Bosh.html>波什</a>票数只有他的零头并不足为奇，而我们的姚明也是同样这种情况。</P>\r\n<P style=\"TEXT-INDENT: 2em\">姚明的竞争对手主要有<a href=http://sports.163.com/special/0005222S/amare.html>小斯</a>，<a href=http://sports.163.com/special/0005222S/dampier.html>丹皮尔</a>和奥库，可是小斯才从伤病中恢复，丹皮尔只是一个苦力，而奥库在爵士只能算队中三号人物，他们的影响力都不足与国际巨星姚明相提并论，所以姚明的票数在西部中锋中遥遥领先是很正常的。</P>\r\n<P style=\"TEXT-INDENT: 2em\">但我们更应该看到，姚明的票数领先主要原因是他本赛季的确变得异常强大，他已经成为这个联盟乃至这个星球上最好的中锋。他平均每场得25.9分，已经进入联盟十大得分手行列，他每场有9.4个篮板排名，排名联盟第十六，而他场均2.2个盖帽更是排名联盟第八。另外，他的助攻数据和抢断数据都比上个赛季有了提升，他已经达到一个伟大中锋的底线。</P>\r\n<P style=\"TEXT-INDENT: 2em\">姚明的个人成绩优异，而他的团队成绩又相当不错。火箭现在取得了20胜12负排名西部第六的战绩，多半要归功于前面姚明率队打下的良好基础。在麦蒂状态不佳或者因伤缺阵的时候，是姚明独自扛起重任，击败小牛马刺等强敌，给球队增强了信心。而现在姚明受伤了，火箭面对轻松的赛程，也取得了4连胜，实力排行榜也前进3位，再度进入前十，排名第九。</P>\r\n<P style=\"TEXT-INDENT: 2em\">姚明的进步带动了球队前进，球队的不断胜利又让姚明变得更加伟大，姚明成为全明星票王名副其实。最好的\"姚时代\"已经来临，让我们为此欢呼吧！</P>\r\n<P style=\"TEXT-INDENT: 2em\">（梦断金陵）<a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977876.0517.gif\" alt=\"悠然\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '姚明', '', ''), 
(121, '4连胜火箭4大收获 卢卡斯崛起麦蒂成长为领袖_网易体育', '\n<ul><li>\n　　火箭队在没有姚明的情况下出人意料的取得了四连胜，而且一场比一场表现稳定，麦蒂、海德、穆大叔、卢卡斯等人令人眼前一亮，火箭在四连胜中收获极大。\n</li></ul>\n', 1167963578, '英扎吉', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　火箭队在没有姚明的情况下出人意料的取得了四连胜，而且一场比一场表现稳定，麦蒂、海德、穆大叔、卢卡斯等人令人眼前一亮，火箭在四连胜中收获极大。\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\">网易体育1月5日讯：在<a href=http://sports.163.com/special/0005222S/yao.html>姚明</a>（<a href=http://wiki.sports.163.com/stars/9/9d9cd68285d340efde44f7bc81183f60.html>我来编辑姚明的资料</a>）因伤缺阵12天后，<a href=http://sports.163.com/special/0005222R/rockets.html target=_blank>火箭队</a>(<a href=http://blog.163.com/rocketsblog>blog</a>)意外的在新年开始阶段迎来四连胜；原本认为姚明的受伤会让火箭队战绩急剧下滑，没想到球队在最近五场比赛中只输了一场，不但稳居西部前八，而且距离西部前四只差两个半胜场，不由得让人感到欣喜。回顾过去的四连胜，从中不难发现球队近期收获之大。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>收获1：<a href=http://sports.163.com/special/0005222S/tracy.html>麦蒂</a>找回手感，重新确立领袖地位</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">姚明受伤后，麦格雷迪曾在一周前拜访过美国当地一家非常有名的医疗机构Waco，那里的著名腰伤专家帕特森用特制的频率特效仪为麦蒂检查后表示，麦蒂的背伤完全能够康复，而且未来复发的可能性非常小；这如同给麦蒂与火箭队吃了一颗定心丸，因为帕特森曾经医好过美国著名网球选手<a href=http://sports.163.com/special/000521Q1/Andy_Roddick.html target=_blank>罗迪克</a>(<a href=http://wiki.sports.163.com/view_top_level_entry.do?entryid=112>wiki</a>)的顽固背伤，在治疗腰伤和背伤方面独树一帜；专家的乐观态度，让麦蒂在最近一周内信心大增，这也是他在场上敢于突入内线、勇于和对手在篮下拼抢的一个重要原因。</P>\r\n<P style=\"TEXT-INDENT: 2em\">在四连胜中，麦蒂有3场比赛得分超过30+，而且最近三场比赛场场得分过30。昨日面对<a href=http://sports.163.com/special/0005222R/sonic.html>超音速队</a>全明星射手雷.阿伦，麦蒂拿下31分6个篮板7次助攻；尽管在比赛最后六分钟内没有得分，但他却摇身一变成为全队进攻的掌控者和指挥官，七次助攻的最后一次妙传<a href=http://sports.163.com/special/0005222S/mutombo.html>穆托姆博</a>篮下打三分尤为精彩。在麦蒂本赛季得分超过30分的五场比赛中，火箭队保持五战全胜，这一数据比<a href=http://sports.163.com/special/0005222Q/Washington_Wizards.html>奇才队</a>阿里纳斯的40+定律还要灵验。更加可怕的是，麦蒂最近3场比赛中三分球命中率均超过50％！分别为66.7％（6投4中，打鹰）、62.5％（8投5中，打灰熊）、50％（6投3中，打超音速），甚至出现过外线打四分的精彩表演，麦蒂的复苏无疑是火箭近期最大收获。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>收获2：老霍与穆大叔挺身而出，火箭内线不吃亏</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">姚明的受伤，一度让火箭球迷对球队内线产生了悲观情绪，因为他们只剩下一个正统中锋穆托姆博，而且穆托姆博已经40岁，体能方面很难适应首发要求。没想到穆大叔在姚明缺阵后的五场比赛中一场比一场表现好，已经连续四场抢篮板在10个以上，分别为打<a href=http://sports.163.com/special/0005222Q/NewJersey_Nets.html>网队</a>10个、打<a href=http://sports.163.com/special/0005222Q/Atlanta_Hawks.html>鹰队</a>14个、打灰熊11个和打超音速12个，其中与超音速队一役最后时刻上篮打三分成功并在最后39秒抢下一个前场篮板，是火箭四连胜的功臣之一。而另外一位老将<a href=http://wiki.sports.163.com/stars/9/937aa9ef9303da7b7789f4698d22705b.html target=_blank>霍华德</a>在最近四场比赛中展现出了当年“密歇根五虎”的风采，战网队和老鹰，老霍分别得到12分11篮板17分13篮板，之后打灰熊和超音速他又分别砍下22分和18分，这四场比赛老霍的投篮命中率全部超过50％，最高曾经达到71.4％（打网队），正是他稳定的中投弥补了火箭内线得分能力不足的劣势。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>收获3：卢卡斯进步神速，<a href=http://sports.163.com/special/0005222S/Van_Gundy.html>范甘迪</a>没有看错</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">就在两个月前新赛季尚未开始时，人们还在纷纷讨论火箭到底应该怎样裁员，当时卢卡斯、哈亚兹、雅克布森、阿祖布克等人都在被清除名单之列，很多人都看好投篮能力更强的阿祖布克或雅克布森，而身高只有5尺11寸（1米79），季前赛中表现也不抢眼的控卫卢卡斯三世并不被人看好。没想到最后范甘迪与道森果断的放弃了后三人，唯独把小卢卡斯留了下来，当时引起了不少非议，认为是卢卡斯的父亲老约翰.卢卡斯在其中起了关键作用。</P>\r\n<P style=\"TEXT-INDENT: 2em\">不过小卢卡斯用自己近期优异的表现回击了那些质疑他的人们，他目前场均只出场8分24秒，在火箭队14名有出场记录的球员中仅比诺瓦克（5分30秒）多，比希腊人斯潘诺里斯还少2分钟，但却可以得到4.3分1.2个篮板和0.6次助攻，得分是希腊人与诺瓦克的总和，投篮命中率47％、三分球33％，在四连胜中场场有得分，昨天打超音速只上场11分钟，但8投4中得到8分2次助攻，是火箭替补席上效率最高、进步最快的球员。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>收获4：火箭外线集体爆发，<a href=http://sports.163.com/special/0005222S/head.html>海德</a>令人惊叹</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">火箭队之所以能够取得四连胜，很大程度上要得益于外线球员的稳定发挥。尤其是替补后卫海德与主力小前锋<a href=http://sports.163.com/special/0005222S/battier.html>巴蒂尔</a>，是火箭外线得分的重要保障。新赛季到目前为止，海德已经连续25场比赛投中三分，继续书写火箭历史纪录；48.1％的三分球命中率不但是火箭队最高，而且在全联盟中也可以排到第四位，仅次于<a href=http://sports.163.com/special/00051TAK/taiyangduiziliao.html target=_blank>太阳队</a><a href=http://sports.163.com/special/0005222S/nash.html>纳什</a>（52％）、<a href=http://sports.163.com/special/0005222Q/Miami_Heat.html target=_blank>热队</a>(<a href=http://blog.163.com/heatblog>blog</a>)卡波诺（52％）、马刺布伦特.巴里（50％）。在周一与<a href=http://sports.163.com/special/0005222R/grizzlies.html>灰熊队</a>一役中，海德的三分球命中率竟然达到83.3％（6投5中），令对方三分王麦克.米勒（10投7中）都感到惊奇。加上外线投篮同样稳定的巴蒂尔（40.3％）和偶尔发飙的<a href=http://sports.163.com/special/0005222S/alston.html>阿尔斯通</a>（35％），已让麦蒂感到不再孤单，这同样也是火箭四连胜的巨大收获。</P>\r\n<P style=\"TEXT-INDENT: 2em\">（英扎吉）<a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977876.2836.gif\" alt=\"唐威\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '', '', ''), 
(122, '体育画报评最令人吃惊球员 火箭旧将榜上有名_网易体育', '\n<ul><li>\n　　想知道哪些球员在本赛季过去两月最让人吃惊吗？《体育画报》为你揭晓\n</li></ul>\n', 1167949426, '汉水游侠', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　想知道哪些球员在本赛季过去两月最让人吃惊吗？《体育画报》为你揭晓\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\">网易体育1月5日讯 每个赛季，<a href=http://sports.163.com/nba/>NBA</a>都会出现一些表现超乎人们预料的球员，他们所展现出来的价值往往是人们在赛季前无法预想的，1月5日，《体育画报》记者麦克卡卢姆就撰文评出了新赛季过去两个月，表现令人吃惊的球员名单——</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>东部</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">泰伦·卢（老<a href=http://sports.163.com/special/0005222Q/Atlanta_Hawks.html>鹰队</a>）</P>\r\n<P style=\"TEXT-INDENT: 2em\">大家一定还记得他在2001年总决赛中防守<a href=http://sports.163.com/special/0005222S/Allen_Iverson.html>艾弗森</a>的画面吧，但从那以后，他就居无定所，先是到了华盛顿，然后“流落”到奥兰多，又被换到休斯敦<a href=http://sports.163.com/special/0005222R/rockets.html target=_blank>火箭队</a>(<a href=http://blog.163.com/rocketsblog>blog</a>)，最后到了<a href=http://sports.163.com/special/0005229P/Atalanta.html target=_blank>亚特兰大</a>。这种展转在NBA代表着你没有得到认可，但泰伦·卢在本赛季却证明了那完全是谬论，他场均拿下14.7分，成为老鹰队板凳上最有实力的后卫。</P>\r\n<P style=\"TEXT-INDENT: 2em\">莫·威廉姆斯（<a href=http://sports.163.com/special/0005222Q/Milwaukee_Bucks.html>雄鹿队</a>）</P>\r\n<P style=\"TEXT-INDENT: 2em\">每当我看到他比赛，我都会认为他是联盟中最出色的后卫之一。虽然我知道他并不是每晚都有上佳表现，可是雄鹿队50%的胜率仅凭里德一人是无法做到的，他场均拿下17.8分和5.5个篮板让里德不至于那么孤单。</P>\r\n<P style=\"TEXT-INDENT: 2em\">卡隆·<a href=http://sports.163.com/special/000521QB/Butt.html target=_blank>巴特</a>勒（<a href=http://sports.163.com/special/0005222Q/Washington_Wizards.html>奇才队</a>）</P>\r\n<P style=\"TEXT-INDENT: 2em\">每个人都知道这名拥有5年NBA经验的老将的价值，本赛季奇才成为联盟中进攻最火爆的球队之一，不单只是因为他们拥有阿里纳斯，巴特勒场均能拿下20.7分，而他的命中率更是达到49.5%，此外，他每场比赛还能贡献8.1个篮板。千万别试图把他送上罚球线，他的罚球命中率高达87.7%！</P>\r\n<P style=\"TEXT-INDENT: 2em\">罗尔·邓（<a href=http://sports.163.com/special/0005222Q/Chicago_Bulls.html>公牛队</a>）</P>\r\n<P style=\"TEXT-INDENT: 2em\">邓的突然爆发是公牛战绩回升的主要原因，本·戈登和<a href=http://sports.163.com/special/0005222S/Kirk_Hinrich.html>辛里奇</a>吸引了对手过多的防守，而邓并没有浪费他们创造的机会，这名来自杜克大学的年轻人，投篮命中率达到53.5%。</P>\r\n<P style=\"TEXT-INDENT: 2em\">阿里扎（<a href=http://sports.163.com/special/0005222Q/Orlando_Magic.html>魔术队</a>）</P>\r\n<P style=\"TEXT-INDENT: 2em\">这名名气并不大的球员已经进入魔术队主教练布莱恩·希尔的9人轮换阵容中了，他是一名典型的替补球员，在球队担当“蓝领”，但除了干苦活，他的投篮命中率达到55%！</P>\r\n<P style=\"TEXT-INDENT: 2em\">西部赛区</P>\r\n<P style=\"TEXT-INDENT: 2em\">皮特鲁斯（<a href=http://sports.163.com/special/0005222R/warrior.html>勇士队</a>）</P>\r\n<P style=\"TEXT-INDENT: 2em\">尼尔森执教，勇士当然会得到更多的分数，连皮特鲁斯也在教练的战术中占到了便宜，他场均拿下14.3分，比他前三年多了8分，但这一切都是可以理解的，看看他的法国同胞<a href=http://sports.163.com/special/00051TAK/taiyangduiziliao.html target=_blank>太阳队</a>的<a href=http://sports.163.com/special/0005222S/boris.html>迪奥</a>就知道了。</P>\r\n<P style=\"TEXT-INDENT: 2em\">马盖特（快艇队）</P>\r\n<P style=\"TEXT-INDENT: 2em\">马盖特的得分不如以前那般凶猛了，但要知道，他的出场时间比布兰德、莫布里、利文、斯顿和卡曼都要少，但他的数据（场均15.2分、5.6个篮板）看上去却很漂亮。也许你会说他在投篮机会的选择上有问题，但这肯定也和教练邓利维的战术安排有关系。</P>\r\n<P style=\"TEXT-INDENT: 2em\">巴尔博萨（太阳队）</P>\r\n<P style=\"TEXT-INDENT: 2em\">在<a href=http://sports.163.com/special/0005222S/nash.html>纳什</a>身边，你的光芒总是会被掩盖，何况现在太阳越来越器重拉加·<a href=http://sports.163.com/special/0005222S/bell.html>贝尔</a>了。但作为后卫，巴尔博萨依旧能成为一名光芒四射的球员，他每年都在进步，本赛季，他每场能送出4.3次助攻和1.1抢断，而他的得分能力是毋庸置疑的。</P>\r\n<P style=\"TEXT-INDENT: 2em\">凯文·马丁（<a href=http://sports.163.com/special/0005222R/king.html>国王队</a>）</P>\r\n<P style=\"TEXT-INDENT: 2em\">本赛季场均21.2分、50%的投篮命中率和91.1%罚球命中率，这名进入联盟已3年的小伙子甚至可以去参加全明星比赛了，本赛季他的表现经常使阿泰斯特坐在板凳上没球可打。</P>\r\n<P style=\"TEXT-INDENT: 2em\">布伦特·巴里（<a href=http://sports.163.com/special/0005222R/spurs.html>马刺队</a>）</P>\r\n<P style=\"TEXT-INDENT: 2em\">他是一名纯粹的射手，他是马刺最得力的第六人，他是<a href=http://sports.163.com/special/0005222S/duncan.html>邓肯</a>最信赖的队友，51.6%的投篮命中率和49.1%的三分球命中率是他最大的资本，但千万别告诉他我把他评为最令人吃惊的球员，因为他已经在这混了12年了。</P>\r\n<P style=\"TEXT-INDENT: 2em\">（汉水游侠）</P>\r\n<P style=\"TEXT-INDENT: 2em\"><a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977876.4633.gif\" alt=\"小云\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '火箭', '', ''), 
(123, '麦蒂领军豪取四连胜 火箭103-96主场胜超音速_网易体育', '\n<ul><li>\n　　麦蒂贡献全队最高的31分，穆大叔12个篮板，尽管阿伦一人得到32分，还是无法阻挡火箭队新年第一胜，同时四连胜\n</li></ul>\n', 1167882678, '英扎吉', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　麦蒂贡献全队最高的31分，穆大叔12个篮板，尽管阿伦一人得到32分，还是无法阻挡火箭队新年第一胜，同时四连胜\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\">网易体育1月4日讯：休斯敦<a href=http://sports.163.com/special/0005222R/rockets.html target=_blank>火箭队</a>(<a href=http://blog.163.com/rocketsblog>blog</a>)今天在主场丰田中心迎来了雷阿伦领军的西雅图<a href=http://sports.163.com/special/0005222R/sonic.html>超音速队</a>，麦格雷迪一人得到全队最高的31分，加上<a href=http://wiki.sports.163.com/stars/9/937aa9ef9303da7b7789f4698d22705b.html target=_blank>霍华德</a>贡献18分，<a href=http://sports.163.com/special/0005222S/mutombo.html>穆托姆博</a>狂砍12个篮板，并在比赛最后39秒抢到十分关键的前场篮板，确保火箭103－96战胜超音速，取得2007年第一场胜利，并喜获四连胜。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>麦格雷迪今天总共出场36分钟，21投11中（其中3个三分），罚球8罚6中，</STRONG>得到主队最高的31分并有6个篮板7次助攻；霍华德18分4个篮板，<a href=http://sports.163.com/special/0005222S/battier.html>巴蒂尔</a>12分，<a href=http://sports.163.com/special/0005222S/alston.html>阿尔斯通</a>13分，穆托姆博8分12个篮板。客队雷.阿伦32分全场最高，威尔金斯19分，威尔考克斯13分4个篮板，其余人表现平平。四节的比分分别为：30－32、18－21、25－27、23－23（火箭队在后）。</P>\r\n<P style=\"TEXT-INDENT: 2em\">第一节开场后主队队员手感极佳，巴蒂尔与霍华德两次中距离跳投全部命中！火箭队4－0迅速领先。<a href=http://sports.163.com/special/0005222S/tracy.html>麦蒂</a>第一次突破上篮打板入筐后，阿伦反击中投得分，超音速2－6终于开和！麦蒂的三分、加罚，霍华德的上篮帮助火箭17－10迅速领先七分，但对方威尔金斯马上回敬一记三分，<a href=http://sports.163.com/special/0005222S/Van_Gundy.html>范甘迪</a>叫了全场第一次长暂停。雷阿伦一次跳投得分。帮助超音速23－24将分差缩小，但麦蒂马上便带球上篮打了一次二加一，加罚得手后火箭27－23重新领先4分。虽然威尔金斯投中三分，一度将比分28－27反超，但<a href=http://sports.163.com/special/0005222S/head.html>海德</a>在第一节最后47秒外线出手还以颜色，加上麦蒂两次罚球，火箭32－30领先两分结束首节。</P>\r\n<P style=\"TEXT-INDENT: 2em\">第二节超音速开始反客为主，麦蒂暂时被换下后，客队打出了一个8比3的小高潮，38－35反超火箭；多亏巴蒂尔中路及时回敬一记三分！火箭迅速将比分追成38平。此时双方陷入拉锯战，比分交替上升至42平仍难分高下。1分钟后，巴蒂尔一记28尺外超远三分，卢卡斯一次骑马射箭上篮，帮助火箭49－44重新领先，此时麦蒂再度登场。<a href=http://sports.163.com/special/0005222S/hayes.html>海耶斯</a>一次上篮，帮助火箭53－48始终领先，阿伦最后1秒压哨跳投不中，火箭队领先五分进入中场休息。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>上半场麦格雷迪17分6个篮板3次助攻最高，老将霍华德11分表现十分抢眼，巴蒂尔也得到8分，</STRONG>阿尔斯通0分2次助攻。对方阿伦14分，威尔金斯10分，威尔考克斯10分1个篮板。</P>\r\n<P style=\"TEXT-INDENT: 2em\">第三节火箭乘胜追击，上半场一分未得的阿尔斯通突然开窍投中一记三分！麦蒂中路三分也锦上添花，火箭63－53领先10分之多！超音速主帅鲍勃.希尔赶紧请求暂停。暂停后火箭始终领先10分左右，大前锋霍华德一次强攻上篮二加一，主队在第三节还剩3分12秒时78－66领先12分之多，比赛再次进入暂停。暂停后两队狂投三分，可惜沃特森、巴蒂尔、海德的四次出手无一命中，第三节结束时火箭80－73仍领先七分。</P>\r\n<P style=\"TEXT-INDENT: 2em\">最后一节超音速队并未放弃，尽管火箭一度领先11分，但阿伦与沃特森连罚带投迅速将分差77－84缩小。第四节还有8分47秒时，阿伦中距离转身跳投得手并造成海德打手犯规！二加一后客队80－84只落后4分！暂停后多亏海德一次突破上篮，麦蒂两次中投，火箭90－83才重新夺回主动权。不料雷.阿伦关键时刻连续投中两个三分！超音速92－96只差四分；幸亏霍华德一次中投稳定住局面。</P>\r\n<P style=\"TEXT-INDENT: 2em\">第四节最后3分08秒，40岁的穆大叔在篮下接到麦蒂分球，双手准备灌篮时遭到对方皮特罗干扰，不过球依然爬进了篮筐并造成后者打手犯规！穆大叔二加一后火箭101－92领先9分之多。<STRONG>无奈好景不长，阿伦中投异常精准，打了火箭一个4比0，在第四节最后1分钟又将分差96－101缩小。比赛最后39秒，麦蒂上篮不中，又是穆大叔在乱军中抢到一个关键的前场篮板！阿尔斯通将时间故意拖延到只剩24.4秒，超音速赶紧采取犯规。</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">海德两罚两中，火箭103－96领先，客队已感到大势已去。威尔金斯三分不中，火箭抢到篮板，103－96的比分一直维持到终场笛声响起。</P>\r\n<P style=\"TEXT-INDENT: 2em\">超音速先发五虎：里德诺尔、雷.阿伦、威尔金斯、威尔考克斯、福特森。</P>\r\n<P style=\"TEXT-INDENT: 2em\">火箭队先发五虎：阿尔斯通、麦格雷迪、巴蒂尔、霍华德、穆托姆博。</P>\r\n<P style=\"TEXT-INDENT: 2em\">（英扎吉） </P>\r\n<TABLE id=table2 borderColor=#ffffff cellPadding=2 width=\"100%\" bgColor=#ffefce border=1>\r\n<TBODY>\r\n<TR>\r\n<TD width=0 bgColor=#ffcc99 colSpan=13 height=0><B>超音速 96</B></TD></TR>\r\n<TR>\r\n<TD width=0 bgColor=#ffcc99 height=0>球员</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>时间</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>投篮</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>三分</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>罚球</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>进攻篮板</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>篮板</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>助攻</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>失误</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>抢断</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>盖帽</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>犯规</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>得分</TD></TR>\r\n<TR>\r\n<TD align=left>威尔考克斯</TD>\r\n<TD>39 </TD>\r\n<TD>5-11 </TD>\r\n<TD>0-0 </TD>\r\n<TD>3-8 </TD>\r\n<TD>2 </TD>\r\n<TD>4 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>1 </TD>\r\n<TD>1 </TD>\r\n<TD>13 </TD></TR>\r\n<TR>\r\n<TD align=left>阿伦</TD>\r\n<TD>42 </TD>\r\n<TD>13-22 </TD>\r\n<TD>2-7 </TD>\r\n<TD>4-5 </TD>\r\n<TD>0 </TD>\r\n<TD>3 </TD>\r\n<TD>2 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>32 </TD></TR>\r\n<TR>\r\n<TD align=left>里德诺</TD>\r\n<TD>29 </TD>\r\n<TD>2-7 </TD>\r\n<TD>0-1 </TD>\r\n<TD>3-4 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>8 </TD>\r\n<TD>3 </TD>\r\n<TD>3 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>7 </TD></TR>\r\n<TR>\r\n<TD align=left>科利森</TD>\r\n<TD>19 </TD>\r\n<TD>3-6 </TD>\r\n<TD>0-0 </TD>\r\n<TD>1-1 </TD>\r\n<TD>1 </TD>\r\n<TD>7 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>2 </TD>\r\n<TD>7 </TD></TR>\r\n<TR>\r\n<TD align=left>威尔金斯</TD>\r\n<TD>35 </TD>\r\n<TD>6-13 </TD>\r\n<TD>5-10 </TD>\r\n<TD>2-2 </TD>\r\n<TD>1 </TD>\r\n<TD>4 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>19 </TD></TR>\r\n<TR>\r\n<TD align=left>吉勒<a href=http://sports.163.com/special/0005222S/bell.html>贝尔</a></TD>\r\n<TD>12 </TD>\r\n<TD>2-3 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>4 </TD></TR>\r\n<TR>\r\n<TD align=left>佩特罗</TD>\r\n<TD>23 </TD>\r\n<TD>4-5 </TD>\r\n<TD>0-0 </TD>\r\n<TD>3-4 </TD>\r\n<TD>0 </TD>\r\n<TD>6 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>4 </TD>\r\n<TD>11 </TD></TR>\r\n<TR>\r\n<TD align=left>福特森</TD>\r\n<TD>9 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>1 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD></TR>\r\n<TR>\r\n<TD align=left>沃特森</TD>\r\n<TD>26 </TD>\r\n<TD>1-5 </TD>\r\n<TD>0-3 </TD>\r\n<TD>1-1 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>4 </TD>\r\n<TD>1 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>3 </TD></TR>\r\n<TR>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD></TR>\r\n<TR>\r\n<TD>合计</TD>\r\n<TD>234 </TD>\r\n<TD>36-72 </TD>\r\n<TD>7-21 </TD>\r\n<TD>17-25 </TD>\r\n<TD>5 </TD>\r\n<TD>32 </TD>\r\n<TD>20 </TD>\r\n<TD>8 </TD>\r\n<TD>6 </TD>\r\n<TD>2 </TD>\r\n<TD>14 </TD>\r\n<TD>96 </TD></TR>\r\n<TR>\r\n<TD>平均</TD>\r\n<TD></TD>\r\n<TD>.500 </TD>\r\n<TD>.333 </TD>\r\n<TD>.680 </TD>\r\n<TD></TD>\r\n<TD colSpan=7>　</TD></TR></TBODY></TABLE>\r\n<TABLE id=table1 borderColor=#ffffff cellPadding=2 width=\"100%\" bgColor=#ffefce border=1>\r\n<TBODY>\r\n<TR>\r\n<TD width=0 bgColor=#ffcc99 colSpan=13 height=0><B>火箭&nbsp; 103</B></TD></TR>\r\n<TR>\r\n<TD width=0 bgColor=#ffcc99 height=0>球员</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>时间</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>投篮</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>三分</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>罚球</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>进攻篮板</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>篮板</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>助攻</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>失误</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>抢断</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>盖帽</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>犯规</TD>\r\n<TD width=0 bgColor=#ffcc99 height=0>得分</TD></TR>\r\n<TR>\r\n<TD align=left>巴蒂尔</TD>\r\n<TD>36 </TD>\r\n<TD>5-10 </TD>\r\n<TD>2-5 </TD>\r\n<TD>0-0 </TD>\r\n<TD>1 </TD>\r\n<TD>3 </TD>\r\n<TD>3 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>12 </TD></TR>\r\n<TR>\r\n<TD>麦格雷迪</TD>\r\n<TD>36 </TD>\r\n<TD>11-21 </TD>\r\n<TD>3-6 </TD>\r\n<TD>6-8 </TD>\r\n<TD>2 </TD>\r\n<TD>6 </TD>\r\n<TD>7 </TD>\r\n<TD>2 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>31 </TD></TR>\r\n<TR>\r\n<TD align=left>阿尔斯通</TD>\r\n<TD>36 </TD>\r\n<TD>6-16 </TD>\r\n<TD>1-4 </TD>\r\n<TD>0-0 </TD>\r\n<TD>2 </TD>\r\n<TD>2 </TD>\r\n<TD>3 </TD>\r\n<TD>2 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>3 </TD>\r\n<TD>13 </TD></TR>\r\n<TR>\r\n<TD align=left>诺瓦克</TD>\r\n<TD>8 </TD>\r\n<TD>0-3 </TD>\r\n<TD>0-2 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD></TR>\r\n<TR>\r\n<TD>海耶斯</TD>\r\n<TD>19 </TD>\r\n<TD>2-4 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>3 </TD>\r\n<TD>8 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>4 </TD></TR>\r\n<TR>\r\n<TD>穆托姆博 　</TD>\r\n<TD>28 </TD>\r\n<TD>3-4 </TD>\r\n<TD>0-0 </TD>\r\n<TD>2-3 </TD>\r\n<TD>5 </TD>\r\n<TD>12 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>1 </TD>\r\n<TD>1 </TD>\r\n<TD>4 </TD>\r\n<TD>8 </TD></TR>\r\n<TR>\r\n<TD>海德</TD>\r\n<TD>23 </TD>\r\n<TD>3-8 </TD>\r\n<TD>1-4 </TD>\r\n<TD>2-2 </TD>\r\n<TD>0 </TD>\r\n<TD>4 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>0 </TD>\r\n<TD>2 </TD>\r\n<TD>9 </TD></TR>\r\n<TR>\r\n<TD>霍华德</TD>\r\n<TD>39 </TD>\r\n<TD>8-14 </TD>\r\n<TD>0-0 </TD>\r\n<TD>2-2 </TD>\r\n<TD>0 </TD>\r\n<TD>4 </TD>\r\n<TD>3 </TD>\r\n<TD>3 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>18 </TD></TR>\r\n<TR>\r\n<TD>卢卡斯</TD>\r\n<TD>11 </TD>\r\n<TD>4-8 </TD>\r\n<TD>0-0 </TD>\r\n<TD>0-0 </TD>\r\n<TD>1 </TD>\r\n<TD>1 </TD>\r\n<TD>2 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>0 </TD>\r\n<TD>1 </TD>\r\n<TD>8 </TD></TR>\r\n<TR>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD>\r\n<TD>　</TD></TR>\r\n<TR>\r\n<TD>合计</TD>\r\n<TD>236 </TD>\r\n<TD>42-88 </TD>\r\n<TD>7-21 </TD>\r\n<TD>12-15 </TD>\r\n<TD>14 </TD>\r\n<TD>41 </TD>\r\n<TD>20 </TD>\r\n<TD>8 </TD>\r\n<TD>4 </TD>\r\n<TD>1 </TD>\r\n<TD>16 </TD>\r\n<TD>103 </TD></TR>\r\n<TR>\r\n<TD>平均</TD>\r\n<TD></TD>\r\n<TD>.477 </TD>\r\n<TD>.333 </TD>\r\n<TD>.800 </TD>\r\n<TD></TD>\r\n<TD colSpan=7>　</TD></TR></TBODY></TABLE><a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977876.6651.gif\" alt=\"唐威\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '', '', ''), 
(124, '科比愿用个人得分换胜利 奥多姆进行罚球练习_网易体育', '\n<ul><li>\n　　当奥多姆和布朗双双因伤缺阵的时候，科比-布莱恩特要承担起洛杉矶湖人队更多的领袖责任，不过科比并不愿意自己总要去得到很多分来帮助球队取胜。\n</li></ul>\n', 1167920917, 'evan', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　当奥多姆和布朗双双因伤缺阵的时候，科比-布莱恩特要承担起洛杉矶湖人队更多的领袖责任，不过科比并不愿意自己总要去得到很多分来帮助球队取胜。\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\">网易体育1月4日消息，当<a href=http://sports.163.com/special/0005222S/odom.html>奥多姆</a>和布朗双双因伤缺阵的时候，<a href=http://sports.163.com/special/0005222S/kobe.html>科比</a>-布莱恩特要承担起洛杉矶<a href=http://sports.163.com/special/0005222R/lakers.html target=_blank>湖人队</a>(<a href=http://blog.163.com/lakersblog/>blog</a>)更多的领袖责任，不过科比并不愿意自己总要去得到很多分来帮助球队取胜。</P>\r\n<P style=\"TEXT-INDENT: 2em\">\"我有些疲惫，\"科比说道，\"希望我们球队并不需要我这样，我真的不想去得很多分。我认为我们可以打得更好、更出色，希望每个人都能挺身而出来给球队做出贡献，那么我就不需要再去得很多分，这真是太好了。\"</P>\r\n<P style=\"TEXT-INDENT: 2em\">这是上赛季联盟得分王真正得想法吗？</P>\r\n<P style=\"TEXT-INDENT: 2em\">\"这会给我更多的乐趣，这会给我们更多乐趣。\"科比说道，\"有些夜晚你做到要想赢得比赛所要做的事情，但是如果我不必做那些事球队就能取胜，那么我会感觉更好。\"</P>\r\n<P style=\"TEXT-INDENT: 2em\">做客迎战萨克拉门托<a href=http://sports.163.com/special/0005222R/king.html>国王队</a>，湖人队还要面对主场球迷的挑衅和嘘声，当奥多姆和布朗缺阵时，他们将面临更多的困难，这时球队其他球员的表现如何就更让人关注。本赛季奥多姆和布朗联手场均贡献26.2分，在他们因伤缺阵时，科比或许不得不挑起更多进攻责任。</P>\r\n<P style=\"TEXT-INDENT: 2em\">\"只有当我必须要做时我才会去，\"科比说道，\"在一些比赛中，为了确保球队不遇到开局就大比分落后的情况，我要表现得更具攻击性一些。\"</P>\r\n<P style=\"TEXT-INDENT: 2em\">自从福克斯和克里斯蒂在一场季前赛中大打出手之后，洛杉矶湖人队和萨克拉门托国王队就结下世仇，他们成为联盟的一对宿敌。当他们在萨克拉门托遭遇时，比赛会有更多不同的意义。</P>\r\n<P style=\"TEXT-INDENT: 2em\">\"不管两支球队的近况如何，因为我们曾经发生的过去，比赛都会变得非常特别。\"科比表示。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><a href=http://sports.163.com/special/0005222S/Shaquille_ONeal.html>奥尼尔</a>曾经把国王队称为\"皇后队\"，<a href=http://sports.163.com/special/0005222S/horry.html>霍里</a>曾有3分球扼杀了国王队的冠军梦想，科比曾因为酒店客房服务的饮食遭遇食物中毒，这都导致双方有更多的矛盾。\"自从那一天过后，我只吃酪饼。\"科比谈起他做客加州首府时的饮食习惯。</P>\r\n<P style=\"TEXT-INDENT: 2em\">布朗已经肯定因伤缺阵，但杰克逊并不愿意透露自己的想法。\"在我们看来，有关布朗的问题我们要谈论四周左右的时间。\"杰克逊说道。</P>\r\n<P style=\"TEXT-INDENT: 2em\">这意味着湖人队要有12场比赛缺少布朗，拜纳姆、<a href=http://sports.163.com/special/0005222S/Ronny_Turiaf.html>图里亚夫</a>和<a href=http://sports.163.com/special/0005222S/cook.html>库克</a>三人将在中锋位置上轮番出赛。</P>\r\n<P style=\"TEXT-INDENT: 2em\">奥多姆目前已经可以进行轻微的慢跑和罚球练习，尽管他还不愿多谈复出的时间。\"因为我要保持状态，所以我必须掌控好时间。\"奥多姆说道，\"我希望以最佳状态复出，队友们表现得很好，他们仍拥有联盟最优秀的球员，现在我们正打出很好的团队篮球。\"</P>\r\n<P style=\"TEXT-INDENT: 2em\">自从12月12日右膝韧带拉伤后奥多姆就一直缺阵，湖人队当时预计他会至少缺阵四周。</P>\r\n<P style=\"TEXT-INDENT: 2em\">（evan）<a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977876.8331.gif\" alt=\"志翔\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '科比', '', ''), 
(125, '麦蒂：健康的人不懂背伤痛苦 老球也需要适应_网易体育', '', 1167919522, '', '汉网-武汉晚报', '\n<P style=\"TEXT-INDENT: 2em\">昨日，没有比赛任务的<a href=http://sports.163.com/special/0005222R/rockets.html target=_blank>火箭队</a>(<a href=http://blog.163.com/rocketsblog>blog</a>)进行了新年的首次队内训练。<a href=http://sports.163.com/special/0005222S/tracy.html>麦蒂</a>在全队训练结束后，还自己加练了半小时三分球和罚球，看着麦蒂投入地跳投，感叹超级球星就是这样练成的。麦蒂在训练结束后接受了采访。&nbsp;&nbsp;</P>\r\n<P style=\"TEXT-INDENT: 2em\">麦蒂对于加练没有什么想法，只是希望自己的手感可以更好一些。“毕竟更换了‘新球’，还需要适应一下。”麦蒂说，虽然回到了老的皮制球，但只有找回以前的感觉，才能给火箭和他带来好运气。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>目前形势好于上赛季</STRONG> </P>\r\n<P style=\"TEXT-INDENT: 2em\"><a href=http://sports.163.com/special/0005222S/yao.html>姚明</a>（<a href=http://wiki.sports.163.com/stars/9/9d9cd68285d340efde44f7bc81183f60.html>我来编辑姚明的资料</a>）还需要几周的时间恢复，MM组合只剩麦蒂，不由得让人想起上赛季火箭的惨状，对于这个问题，麦蒂表示这和上个赛季差别很大。“不同的是我们这个赛季多了更多优秀的投手，这让我们有更多的得分点确保比赛的胜利。”</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>站着说话“不腰疼”</STRONG> </P>\r\n<P style=\"TEXT-INDENT: 2em\">背伤一直困扰着麦蒂，而很多评论员和记者都拿背伤来说事，麦蒂对于这个话题也是很恼火。“外界很多人对于背伤这个问题理解不是很准确，这帮家伙从来没得过背伤，根本不知道背伤是怎么回事。像维尔斯这次就是这样，我很明白他所承受的痛苦，而且这次他受伤的地方是他上次手术过的地方，背伤复发，更是痛苦。”麦蒂深有感触地说。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>部分球员无自信 </STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">少了姚明火箭的进攻问题是明显的。“空位投篮，我要说的就是这个。”麦蒂说：“当我受伤坐在板凳上看比赛的时候，我看到很多人在空位有机会投篮，但是他们却往往把球传出去，白白浪费了一个好机会。其实只要机会好，就应该投，哪怕我就站在他身边，也不应该给我，应该自己投，要自信一点。”(鹿南风） <a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977877.0027.gif\" alt=\"沐沐\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 0, 0, '', '', ''), 
(126, '前瞻：科比面临野兽考验 湖人欲破国王魔鬼主场_网易体育', '\n<ul><li>\n　　在主场击败费城76人队后，洛杉矶湖人队将迎来严峻的一月考验，他们首先要做客加州首府萨克拉门托迎战国王队，科比领军的湖人队力争两连胜，国王队则要冲击三连胜。\n</li></ul>\n', 1167916233, 'evan', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　在主场击败费城76人队后，洛杉矶湖人队将迎来严峻的一月考验，他们首先要做客加州首府萨克拉门托迎战国王队，科比领军的湖人队力争两连胜，国王队则要冲击三连胜。\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\">网易体育1月4日消息，在主场击败费城<a href=http://sports.163.com/special/0005222Q/Philadelphia_76ers.html>76人队</a>后，洛杉矶<a href=http://sports.163.com/special/0005222R/lakers.html target=_blank>湖人队</a>(<a href=http://blog.163.com/lakersblog/>blog</a>)将迎来严峻的一月考验，他们首先要做客加州首府萨克拉门托迎战<a href=http://sports.163.com/special/0005222R/king.html>国王队</a>，<a href=http://sports.163.com/special/0005222S/kobe.html>科比</a>领军的湖人队力争两连胜，国王队则要冲击三连胜。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG><a href=\"http://chat.news.163.com/chat/new/index.html?id=763\" target=\"_blank\"><FONT color=#6600ff>本场比赛直播室入口</FONT></A></STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>两队近况分析</STRONG>：</P>\r\n<P style=\"TEXT-INDENT: 2em\">湖人队目前仍排名太平洋分区第二位，不过他们与领头羊<a href=http://sports.163.com/special/00051TAK/taiyangduiziliao.html target=_blank>太阳队</a>的差距已经拉开到三场球，因此对于他们来说，现在需要做的就是紧紧咬住太阳队。<a href=http://sports.163.com/special/0005222S/odom.html>奥多姆</a>的缺阵对湖人队的影响非常明显，过去10场比赛中他们仅有5胜5负的战绩，这让他们与太阳队的差距被拉开。科比近来的表现相当出色，可是队友们的发挥并不稳定，在做客出赛时科比身边的球员们必须要有更好的发挥才能帮助湖人队赢得胜利。</P>\r\n<P style=\"TEXT-INDENT: 2em\">布朗的受伤让湖人队的内线深度又大为削弱，不过这却给了小将拜纳姆机会，他已经肯定会首发出场，他的表现如何对于湖人队能否赢球非常重要。本赛季在对垒同分区的球队时，湖人队取得4胜0负的战绩，这让他们在与同分区球队交锋时拥有足够的信心，面对宿敌国王队时他们应该会有更好的发挥。未来八场比赛中湖人队有七个客场，本赛季在客场他们仅有6胜7负的战绩，因此他们的做客前景堪忧。</P>\r\n<P style=\"TEXT-INDENT: 2em\">国王队已经取得两连胜，这对他们信心的恢复很有帮助，虽然过去10场比赛仅有5胜5负的战绩，但球队目前的状态相当不错，因此他们对主场击败湖人队很有信心。阿泰斯特在击败纽约<a href=http://sports.163.com/special/0005222Q/NewYork_Knicks.html>尼克斯队</a>的比赛中得到生涯最高分，毕比也渐入佳境，这两位明星球员的回勇将让国王队有更大的取胜机会。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>核心对抗：科比 VS 阿泰斯特</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">科比在奥多姆缺阵后完全挑起球队领袖的责任，他也用全面的表现给予球队极大的帮助，科比能够当选12月西部最佳球员这就是对他近期表现的一种肯定。明天科比将面临近来最严峻的一次考验，他将与防守悍将阿泰斯特过招，这将是万众期待的攻守大战，这也是对科比状态的一次检验。如果科比能够在与阿泰斯特的较量中完胜对手，那么湖人队取胜的希望将大增。</P>\r\n<P style=\"TEXT-INDENT: 2em\">阿泰斯特这个赛季的表现无法令人满意，他也一直受到伤病困扰，这让他身陷转会传闻。可是自从有关他和马盖蒂将互换东家的传闻出现后，阿泰斯特的表现也有所反弹。美国时间1月1日是联盟重新启用皮制篮球的第一天，结果阿泰斯特就在与尼克斯队的比赛中得到生涯最高分，他用这种出色的表现向科比\"宣战\"，相信他明天肯定会给湖人队巨星制造更大的麻烦。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>交锋纪录：</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">在过去七次做客萨克拉门托阿科球馆的比赛中湖人队输掉六场，其中包括三连败，这对于湖人队来说将是一次严峻的考验。</P>\r\n<P style=\"TEXT-INDENT: 2em\"><STRONG>阵容分析：</STRONG></P>\r\n<P style=\"TEXT-INDENT: 2em\">预计湖人队先发：<a href=http://sports.163.com/special/0005222S/tony_parker.html>帕克</a>、科比、沃顿、<a href=http://sports.163.com/special/0005222S/cook.html>库克</a>、拜纳姆</P>\r\n<P style=\"TEXT-INDENT: 2em\">预计国王队先发：毕比、马丁、阿泰斯特、托马斯、米勒</P>\r\n<P style=\"TEXT-INDENT: 2em\">伤病营：湖人-<a href=http://sports.163.com/special/0005222S/mihm.html>米姆</a>（脚踝伤势，赛季结束）、麦基（背伤）、奥多姆（膝盖扭伤，缺阵3-6周）</P>\r\n<P style=\"TEXT-INDENT: 2em\">（evan）</P>\r\n<P style=\"TEXT-INDENT: 2em\"><a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977877.1665.gif\" alt=\"志翔\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 1, 0, '科比', '', ''), 
(127, '休斯顿球迷厌倦等待 建议道森以坎比暂替姚明_网易体育', '\n<ul><li>\n　　火箭获得了四连胜，但赢的球队都是在联盟排名靠后的球队，而接下来火箭面临的却是20天的魔鬼赛程，休斯顿当地球迷在论坛上展开了关于火箭的讨论，结论是在接下来的比赛难以乐观，关于姚明受伤的事当然也屡被提及。\n</li></ul>\n', 1167915967, '长恨水', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　火箭获得了四连胜，但赢的球队都是在联盟排名靠后的球队，而接下来火箭面临的却是20天的魔鬼赛程，休斯顿当地球迷在论坛上展开了关于火箭的讨论，结论是在接下来的比赛难以乐观，关于姚明受伤的事当然也屡被提及。\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\">网易体育专稿 谁都知道麦迪和<a href=http://sports.163.com/special/0005222S/yao.html>姚明</a>（<a href=http://wiki.sports.163.com/stars/9/9d9cd68285d340efde44f7bc81183f60.html>我来编辑姚明的资料</a>）对火箭的不可或缺，上赛季他们相继缺阵的后果就是火箭远离季后赛。而麦迪的背伤是一个定时炸弹，谁也无法预料他什么时候又会倒下。姚明虽然没有像麦迪这样的老伤隐患，但作为<a href=http://sports.163.com/nba/>NBA</a>现在的第一中锋，对手给他的防守越来越重视，土豆罗宾逊给戮伤姚明眼睛的事件仍令球迷心有余悸，而姚明自己也越来越有责任感，这次的受伤就是快速回防后盖帽造成的。</P>\r\n<P style=\"TEXT-INDENT: 2em\">虽然姚明能在二月初，但球迷们已经等不及了，毕竟一月份火箭接来的都是硬仗，所以有球迷建议道森把<a href=http://sports.163.com/special/0005222R/nuggets.html>掘金队</a>的坎比交换过来，以便对付一月的赛程，二是即使在姚明完好时也有一个强人替补，坎比的进攻时爱中投的特点甚至可能打大前锋。毕竟<a href=http://sports.163.com/special/0005222S/mutombo.html>穆托姆博</a>已经老了，状态只会下降，虽然现在内线表现不错，但进攻始终是他的弱点。而坎比却是能在内线独当一面的中锋。</P>\r\n<P style=\"TEXT-INDENT: 2em\">当然，火箭球迷更希望拥有一个健康的姚明，但对像姚明这样的大个子想要以最高水平的坚持82场比赛显然不是太现实的，要知道<a href=http://sports.163.com/special/0005222S/Van_Gundy.html>范甘迪</a>对姚明的使用经常是在40分钟以上了。越疲劳就越容易受伤，像<a href=http://sports.163.com/special/0005222S/Shaquille_ONeal.html>奥尼尔</a>那样的中锋在巅峰时期也往往打个三十五六分钟，还常常因伤缺阵。</P>\r\n<P style=\"TEXT-INDENT: 2em\">只不过那些球迷的建议有些一厢情愿，不说掘金肯不肯放坎比，至少火箭现在的薪金空间和球员资源都不足以交换，虽然球迷积极，所提出的交换方式莫衷一是，但谁也没有提到拿麦迪和<a href=http://sports.163.com/special/0005222S/battier.html>巴蒂尔</a>去交换，当然，姚明更不可能了。</P>\r\n<P style=\"TEXT-INDENT: 2em\">休斯顿球迷的心情可以理解，但这样的交换操作起来毕竟不太现实，不过这也正是他们等待姚明的急迫心情的体现。（长恨水）<a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977877.3355.gif\" alt=\"志翔\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 1, 0, '姚明', '', ''), 
(128, '一周低氧训练量等于登次珠峰 刻苦成就阿里纳斯_网易体育', '\n<ul><li>\n　　阿里纳斯60分击败他的偶像科比，阿里纳斯当选周最佳，阿里纳斯独得54分，阿里纳斯荣膺东部月最佳，阿里纳斯压哨绝杀雄鹿……这个193公分的小个子被莱利称为：“他和神只差5厘米！”\n</li></ul>\n', 1167913740, '长恨水', '网易体育专稿', '\n<div class=\"endSummary\">\n<ul><li>\n　　阿里纳斯60分击败他的偶像科比，阿里纳斯当选周最佳，阿里纳斯独得54分，阿里纳斯荣膺东部月最佳，阿里纳斯压哨绝杀雄鹿……这个193公分的小个子被莱利称为：“他和神只差5厘米！”\n</li></ul>\n</div>\n<P style=\"TEXT-INDENT: 2em\"></P>\r\n<P style=\"TEXT-INDENT: 2em\">你以为他是天才，但是，你恐怕错了。</P>\r\n<P style=\"TEXT-INDENT: 2em\">阿里纳斯11岁才开始摸篮球，但是在高一球季结束的时候，他的教练说：“你永远也不可能进入大学打篮球。”于是他转到另外一所高中。高中毕业时，很多专家又预测他在NCAA的上场比赛时间将为0。结果他父亲不得不到处向人推销自己的儿子，后来因为有人退出，他才得以进入篮球名校Arizona，为此他身着0号球衣。</P>\r\n<P style=\"TEXT-INDENT: 2em\">2001年NCAA总决赛惜败于杜克后，他参加选秀，选秀那天，他在一个旅馆看着电视上的<a href=http://sports.163.com/nba/>NBA</a>选秀，在第一轮却没有人要他，他打电话给自己的大学教练痛哭失声，教练赶紧安慰他说他第二轮第二顺位被金州<a href=http://sports.163.com/special/0005222R/warrior.html>勇士队</a>选中了。他说：“这是NBA犯下的最大的错误，我TMD现在就去训练馆！”</P>\r\n<P style=\"TEXT-INDENT: 2em\">阿里纳斯的篮球天赋在NBA众球星里确实算不了什么，但他用努力证明了自己，与他截然想反的一个例子就是卡特了。在多伦多时的卡特是个百分百消极怠工的主，转会到篮网之后，他开始好转，不过好转的只是心情。因为他是一个天生的球星，只要有好心情就能打出好比赛，但曾经号称乔丹第一接班人的他远没有发挥出他的天赋。比如，他从不练肌肉，这正是他肌肉经常拉伤扭伤的原因。</P>\r\n<P style=\"TEXT-INDENT: 2em\">以天赋而伦，卡特顶多发挥出他的七成，而阿里纳斯恰恰相反，他天赋不如卡特，但他却把自己发挥到十二成。可以说他的成绩完全是靠努力得来了，记得年被骑士淘汰的那天，他很伤心，但唯一没有忘记的竟然还是训练，那天他竟然训练直到深夜。</P>\r\n<P style=\"TEXT-INDENT: 2em\">预测阿里纳斯的那些人都错了，但阅人众多的专家一致地不看好他的前程，这只能说明一个问题，天才的开赋是很明显的，从篮球的角度来说，阿里纳斯确实不是一个天才。但专家们的错误在于，阿里纳斯以他们根本想像不到的努力成为了一个巨星。</P>\r\n<P style=\"TEXT-INDENT: 2em\">让我们看看阿里纳斯巨星之路的刻苦程度吧：高一时的阿里纳斯每天早上四点就翻墙进入附近的初中篮球场投篮；大学时，掌管大学球馆钥匙的朋友每天深夜要给他打开13000人的球馆给他训练；他加盟NBA后，经常凌晨两三点就开始起床训练；他在夏天找真正的军人而不是体能训练师来训练自己的体能，内容包括一些军训项目的负重登山跑和举重训练。他出巨资在家里修建一个低氧训练室来模拟高海拔训练，还专门订做了低氧帐篷去客场比赛。据说，他每个星期的低氧训练达到了登一次珠穆朗玛峰的量。</P>\r\n<P style=\"TEXT-INDENT: 2em\">在NBA有些人是天才，但有些人却在糟蹋自己的天才，有些人利用了自己的天才，比如<a href=http://sports.163.com/special/0005222S/kobe.html>科比</a>。而阿里纳斯的天才毫无疑问不如科比与卡特，但他却用刻苦超越了卡特，如果再这样下去，他超越科比并非不可能，因为科比的努力不足以与阿里纳斯的刻苦相比。（长恨水） <a href=\"http://sports.163.com/\"><img src=\"/UserFiles/Image/1167977877.5344.gif\" alt=\"志翔\" width=\"12\" height=\"11\" border=\"0\" class=\"icon\" /></a> \n', 1, 1, 1, 0, '阿里纳斯', '', ''), 
(129, '昔日火箭老大被传要退役 弗朗西斯表示纯属谣言_网易体育', '', 1167580800, '大被', '网易体育专稿', '<p style=\"text-indent: 2em;\">&nbsp;</p>\r\n<p style=\"text-indent: 2em;\">网易体育1月4日讯：曾经的火箭老大弗朗西斯，在离开休斯敦后转投魔术，随后又来到尼克斯。几经折腾的&ldquo;弹簧人&rdquo;的锐气逐渐被磨灭，而一直属于他的3号球衣，也被马布里夺走，加上现在脚伤未愈，弗朗西斯已经隐约成为联盟的二流球星。就在前天《纽约邮报》披露弗老大会提前退役，不过今日弗朗西斯经纪人弗雷德接受记者采访时表示，关于退役的新闻纯属谣言。 </p>\r\n<p style=\"text-indent: 2em;\">为了更好的治疗脚伤，弗朗西斯来到他熟悉的休斯敦，在这里接受前<a href=\"http://sports.163.com/nba/\">NBA</a>球星杰里-卢卡斯的康复训练。&ldquo;这是明显的谣言，&rdquo;弗朗西斯的经纪人弗雷德对《纽约每日新闻》记者说，&ldquo;我可以保证，现在史蒂夫正在积极恢复中，他从来没有想过退役，他只想尽快回到赛场，为球队做出贡献，而这也是他来到休斯敦的原因。在这里他将接受专家的指点，尽早的康复。&rdquo;</p>\r\n<p style=\"text-indent: 2em;\">卢卡斯是联盟著名的康复训练师，当初&ldquo;便士&rdquo;哈达威膝伤康复期间，也是在卢卡斯的指点下进行训练。不过在弗朗西斯离开尼克斯的那一刻起，关于他转会的谣言就没有停止过，球队主教练&ldquo;微笑刺客&rdquo;托马斯前不久就表示，将弗朗西斯交换到纽约不是他的主意，这都是前主教练布朗一手运作的。从托马斯的语气中，很容易让人觉得尼克斯已经准备送走弗朗西斯。</p>\r\n<p style=\"text-indent: 2em;\">对于自己退役的传言，弗朗西斯表示很无奈，&ldquo;没什么好说的，&rdquo;弗朗西斯说，&ldquo;我的篮球生涯还很长，现在不是说退役的时候，目前我只想养好伤，早日回到我热爱的赛场。&rdquo;如今弗朗西斯不是尼克斯的老大，球队属于马布里，昔日的火箭老大连自己最爱的3号球衣都被对方抢夺，可见纽约并不是弗老大乐意呆的地方。</p>\r\n<p style=\"text-indent: 2em;\">目前弗朗西斯和尼克斯还有4800万美元的合约，也许对于&ldquo;弹簧人&rdquo;来言，尼克斯花重金买断他的合同，是最好的选择，这样弗老大也可以转投实力更为强大的球队，而不是呆在联盟的超级弱队，度过自己职业生涯的末期。</p>\r\n<p style=\"text-indent: 2em;\">（大被）<a href=\"http://sports.163.com/\"><img width=\"12\" height=\"11\" border=\"0\" src=\"/UserFiles/Image/1167977877.9093.gif\" alt=\"志翔\" class=\"icon\" /></a>  </p>', 1, 1, 1, 1, '弗朗西斯', '#FF0000', '/UserFiles/Image/1161843475.5751.jpg');
#----------------------------
# Table structure for xlite_ftp
#----------------------------
CREATE TABLE `xlite_ftp` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `site_name` varchar(20) NOT NULL,
  `host` varchar(50) NOT NULL,
  `user` varchar(20) NOT NULL,
  `pwd` varchar(30) NOT NULL,
  `pub_dir` varchar(100) NOT NULL,
  `last_pub_date` int(11) NOT NULL,
  `sort_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
#----------------------------
# Records for table xlite_ftp
#----------------------------


insert  into xlite_ftp values 
(1, 'www.lanqiaobiz.com', 'www.lanqiaobiz.com', 'lanqiao_com', 'lanqiao_com', '/', 0, 0);
#----------------------------
# Table structure for xlite_gallery
#----------------------------
CREATE TABLE `xlite_gallery` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pic_number` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `thumbnail` varchar(200) NOT NULL,
  `sort_id` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sort_id` (`sort_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
#----------------------------
# No records for table xlite_gallery
#----------------------------

#----------------------------
# Table structure for xlite_guestbook
#----------------------------
CREATE TABLE `xlite_guestbook` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(30) NOT NULL,
  `created` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `home` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text,
  `reply` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
#----------------------------
# No records for table xlite_guestbook
#----------------------------

#----------------------------
# Table structure for xlite_image
#----------------------------
CREATE TABLE `xlite_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `image` varchar(200) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gallery_id` (`gallery_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
#----------------------------
# No records for table xlite_image
#----------------------------

#----------------------------
# Table structure for xlite_link
#----------------------------
CREATE TABLE `xlite_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `url` varchar(100) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `image` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
#----------------------------
# No records for table xlite_link
#----------------------------

#----------------------------
# Table structure for xlite_role
#----------------------------
CREATE TABLE `xlite_role` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
#----------------------------
# Records for table xlite_role
#----------------------------


insert  into xlite_role values 
(1, '超级管理员');
#----------------------------
# Table structure for xlite_role_act
#----------------------------
CREATE TABLE `xlite_role_act` (
  `role_id` int(11) DEFAULT NULL,
  `act_id` int(11) DEFAULT NULL,
  KEY `role_id` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
#----------------------------
# Records for table xlite_role_act
#----------------------------


insert  into xlite_role_act values 
(1, 1), 
(1, 2), 
(1, 3), 
(1, 4), 
(1, 5), 
(1, 6), 
(1, 7), 
(1, 8), 
(1, 9), 
(1, 10), 
(1, 11), 
(1, 12), 
(1, 13), 
(1, 14), 
(1, 15), 
(1, 16), 
(1, 17), 
(1, 18), 
(1, 19), 
(1, 20), 
(1, 21), 
(1, 22), 
(1, 23), 
(1, 24), 
(1, 25), 
(1, 26), 
(1, 27), 
(1, 28), 
(1, 29), 
(1, 30), 
(1, 31), 
(1, 32), 
(1, 33), 
(1, 34), 
(1, 35), 
(1, 36), 
(1, 37), 
(1, 38), 
(1, 39), 
(1, 40), 
(1, 41), 
(1, 42), 
(1, 43), 
(1, 44), 
(1, 45), 
(1, 46), 
(1, 47), 
(1, 48), 
(1, 49), 
(1, 50), 
(1, 51), 
(1, 52), 
(1, 53), 
(1, 54), 
(1, 55), 
(1, 56), 
(1, 57), 
(1, 58), 
(1, 59), 
(1, 60), 
(1, 61), 
(1, 62), 
(1, 63), 
(1, 64), 
(1, 65), 
(1, 66);
#----------------------------
# Table structure for xlite_scratcher
#----------------------------
CREATE TABLE `xlite_scratcher` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `list_url` varchar(200) NOT NULL,
  `list_before_string` text NOT NULL,
  `list_after_string` text NOT NULL,
  `article_url` varchar(200) NOT NULL,
  `title_pattern` text NOT NULL,
  `post_time_pattern` text NOT NULL,
  `summary_pattern` text NOT NULL,
  `author_pattern` text NOT NULL,
  `come_from_pattern` text NOT NULL,
  `tags_pattern` text NOT NULL,
  `content_pattern` text NOT NULL,
  `save_sort_id` int(11) NOT NULL,
  `get_number` int(11) NOT NULL,
  `save_resource` tinyint(1) NOT NULL DEFAULT '0',
  `total_save` int(11) NOT NULL,
  `charset` varchar(15) NOT NULL,
  `last_modified_time` int(11) NOT NULL,
  `is_rss` tinyint(1) NOT NULL DEFAULT '0',
  `cookie` text,
  `keyword` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
#----------------------------
# Records for table xlite_scratcher
#----------------------------


insert  into xlite_scratcher values 
(14, '网易电影新闻频道RSS', 'http://ent.163.com/special/00031K7Q/rss_entmovie.xml', '', '', '从RSS文件中读取', '<title>__CATCH___网易娱乐</title>', '<h3>__ANY__</h3>__SPACE__<div class=\"text\">__CATCH__　来源: <a ', '<meta name=\"description\" content=\"__CATCH__\">', '作者：__CATCH____SPACE__', '<h3>__ANY__来源: <a href=__ANY__>__CATCH__</a>__ANY__<a href=\"javascript:reply_allReply()\" target=\"_self\" class=\"cDRed\" >网友评论', '<meta name=\"keywords\" content=\"__CATCH__\">', '<div id=\"endText\">__CATCH__</div>__SPACE__<!-- 分页 -->', 6, 0, 0, 0, 'GBK', 0, 1, '', ''), 
(13, 'CSDN标签PHP', 'http://tag.csdn.net/tag/PHP/[1-5].html', '', '', 'http://tag.csdn.net/Article/__ANY__.html', '<title>\r\n			__CATCH__\r\n		</title>', '', '', '作者：</cite__ANY__>__CATCH__</a>', '来源：</cite__ANY__>__CATCH__</a>', '本文Tag：</cite><span id=\"lbltag\">__ANY__<a href=\"http://tag.csdn.net/tag__ANY__\">__CATCH__</a>', '<p><span id=\"lblsummary\">__CATCH__</span></p>', 1, 0, 0, 0, 'UTF-8', 0, 0, '', ''), 
(12, '网易体育篮球频道', 'http://sports.163.com/special/00051K7F/rss_sportslq.xml', '', '', '从RSS文件中读取', '<title>__CATCH__</title>', '<h3>__ANY__</h3>__SPACE__<div class=\"text\">__CATCH__　来源: <a ', '<div class=\"endSummary\">__CATCH__</div>', '作者：__CATCH____SPACE__', '<h3>__ANY__来源: <a href=__ANY__>__CATCH__</a>__ANY__<a href=\"javascript:reply_allReply()\" target=\"_self\" class=\"cDRed\" >网友评论', '<a href=\"http://news.so.163.com/newsSearch.jsp?f=1&q=__CATCH__\"', '<div id=\"endText\">__CATCH__</div>__SPACE__<!-- 分页 -->', 1, 0, 1, 60, 'GBK', 1167977878, 1, '', ''), 
(11, 'CSDN技术中心WEB开发', 'http://dev.csdn.net/tag/web%e5%bc%80%e5%8f%91/p[1-4].html', '', '', '/author/__ALNUM__/__ALNUM<32>__.html', 'div class=\"story_con_title_l\"><h5>__CATCH__</h5></div>', '发表日期:__CATCH__<br/>', '<div class=\"con_sample\"><p>__CATCH__</p>', '<p>源自：<a href=__ANY__>__CATCH__</a>', '/a> (<a href=\"__CATCH__\" target=\"_blank\" title=\"点击进入作者个人网站\" >个人网站</a>) 标签', '标签：<a href=__ANY__>__CATCH__</a>', '<div class=\"con_all\">__CATCH__</div><!--ArticleItem_Author', 6, 0, 1, 0, 'UTF-8', 0, 0, '', 'ajax'), 
(10, '机床网新闻', 'http://www.mw35.com/catalog/m/apply_0_[1-5].html', '', '', 'http://www.mw35.com/article/apply/__DIGITAL__.html', '<title>__CATCH__-应用论文-中国金属加工网', ' <a href=http://www.mw35.com><b style=font-family=verdana;font-size:11px;><font color=#000000>MW</font><font color=#ff0000>35.com</font></b></a> __CATCH__\n                </td>', '<meta http-equiv=\"description\" content=\"__CATCH__\">', '', ' <a href=http://__CATCH__><b style=font-family=verdana', '', '<td><div class=\"big\">&nbsp;&nbsp;&nbsp;&nbsp;__CATCH__</div></td>', 1, 0, 1, 0, 'GBK', 0, 0, '', ''), 
(9, '货架', 'http://www.hj18.cn/info_hy.php?page=[1-5]', '您现在的位置 ：<a href=\"index.php\">首 页</a> &gt;&gt; 行业信息', '<td height=\"40\"><div align=\"center\"> 总共 193 条 <a href=\"?page=1\">【首页】</a>', 'view_news_industry.php?id=__DIGITAL__', '<div align=\"center\" class=\"style3\">__CATCH__</div>', '发布时间：__CATCH__</span>', '', '', '文章出自：__CATCH__　　　　发布时间', '', '<td class=\"hui6e\"> <div align=\"left\"><span class=\"black\">__CATCH__</span>', 1, 0, 1, 0, 'GBK', 0, 0, '', ''), 
(8, '网易电视新闻频道', 'http://ent.163.com/special/00031K7Q/rss_enttv.xml', '', '', '从RSS文件中读取', '<title>__CATCH___网易娱乐</title>', '<h3>__ANY__</h3>__SPACE__<div class=\"text\">__CATCH__　来源: <a ', '<meta name=\"description\" content=\"__CATCH__\">', '作者：__CATCH____SPACE__', '<h3>__ANY__来源: <a href=__ANY__>__CATCH__</a>__ANY__<a href=\"javascript:reply_allReply()\" target=\"_self\" class=\"cDRed\" >网友评论', '<meta name=\"keywords\" content=\"__CATCH__\">', '<div id=\"endText\">__CATCH__</div>__SPACE__<!-- 分页 -->', 1, 0, 1, 0, 'GBK', 0, 1, '', '');
#----------------------------
# Table structure for xlite_sort
#----------------------------
CREATE TABLE `xlite_sort` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `root_id` int(11) unsigned NOT NULL DEFAULT '0',
  `deep` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `ordernum` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ordernum` (`ordernum`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
#----------------------------
# Records for table xlite_sort
#----------------------------


insert  into xlite_sort values 
(1, 0, 0, 0, '篮球');
#----------------------------
# Table structure for xlite_tags
#----------------------------
CREATE TABLE `xlite_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `keyword` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `keyword` (`keyword`)
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=latin1;
#----------------------------
# Records for table xlite_tags
#----------------------------


insert  into xlite_tags values 
(2, 22, '火箭'), 
(3, 24, '科比'), 
(5, 28, '阿里纳斯'), 
(6, 29, '弗朗西斯'), 
(7, 31, '火箭'), 
(9, 33, 'NBA实力榜'), 
(11, 35, '活塞'), 
(12, 36, '苏拉'), 
(14, 38, '科尔曼'), 
(17, 42, '火箭'), 
(18, 44, '科比'), 
(19, 46, '科比'), 
(21, 48, '阿里纳斯'), 
(22, 49, '弗朗西斯'), 
(23, 51, '火箭'), 
(25, 53, 'NBA实力榜'), 
(27, 55, '活塞'), 
(28, 56, '苏拉'), 
(30, 58, '科尔曼'), 
(33, 62, '火箭'), 
(34, 64, '科比'), 
(35, 66, '科比'), 
(37, 68, '阿里纳斯'), 
(38, 69, '弗朗西斯'), 
(39, 71, '火箭'), 
(41, 73, 'NBA实力榜'), 
(43, 75, '活塞'), 
(44, 76, '苏拉'), 
(46, 78, '科尔曼'), 
(49, 82, '火箭'), 
(50, 84, '科比'), 
(51, 86, '科比'), 
(53, 88, '阿里纳斯'), 
(54, 89, '弗朗西斯'), 
(55, 91, '火箭'), 
(57, 93, 'NBA实力榜'), 
(59, 95, '活塞'), 
(60, 96, '苏拉'), 
(62, 98, '科尔曼'), 
(65, 102, '火箭'), 
(66, 104, '科比'), 
(67, 106, '科比'), 
(69, 108, '阿里纳斯'), 
(70, 109, '弗朗西斯'), 
(71, 111, '火箭'), 
(73, 113, 'NBA实力榜'), 
(75, 115, '活塞'), 
(76, 116, '苏拉'), 
(78, 118, '科尔曼'), 
(81, 122, '火箭'), 
(82, 124, '科比'), 
(83, 126, '科比'), 
(85, 128, '阿里纳斯'), 
(90, 129, '弗朗西斯');