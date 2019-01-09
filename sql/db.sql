DROP TABLE IF EXISTS `weixin_admin`;
CREATE TABLE `weixin_admin` (
  `user` text NOT NULL,
  `pwd` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `weixin_aliyunoss`;
CREATE TABLE `weixin_aliyunoss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `OSS_ACCESS_ID` varchar(255) DEFAULT NULL COMMENT 'ACCESS_ID',
  `OSS_ACCESS_KEY` varchar(255) DEFAULT NULL COMMENT 'ACCESS_KEY',
  `ALI_LOG` tinyint(1) DEFAULT '1' COMMENT '1不记录日志2记录日志',
  `ALI_LOG_PATH` varchar(255) DEFAULT NULL COMMENT '日志存放路径',
  `ALI_DISPLAY_LOG` tinyint(1) DEFAULT '1' COMMENT '是否显示日志输出1不显示2显示',
  `BUCKET_NAME` varchar(255) DEFAULT NULL COMMENT 'bucket名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='阿里云oss配置表';

DROP TABLE IF EXISTS `weixin_attachments`;
CREATE TABLE `weixin_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filepath` varchar(255) DEFAULT NULL COMMENT '文件路径',
  `extension` varchar(10) DEFAULT NULL COMMENT '扩展名',
  `type` tinyint(1) DEFAULT NULL COMMENT '1本地文件2阿里云3新浪云',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='附件表';

		
DROP TABLE IF EXISTS `weixin_award`;
CREATE TABLE `weixin_award` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '奖品id',
  `awardname` varchar(100) DEFAULT NULL COMMENT '奖品名称',
  `imagepath` int(11) DEFAULT NULL COMMENT '奖品图片路径id',
  `created_at` int(11) DEFAULT NULL COMMENT '奖品添加时间',
  `isdel` tinyint(1) DEFAULT '1' COMMENT '1表示正常2表示被删除了',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='奖品表';

INSERT INTO `weixin_award`(`id`, `awardname`, `imagepath`, `created_at`, `isdel`) VALUES (1, '一等奖', 0, 0, 1);
INSERT INTO `weixin_award`(`id`, `awardname`, `imagepath`, `created_at`, `isdel`) VALUES (2, '二等奖', 0, 1511097439, 1);
INSERT INTO `weixin_award`(`id`, `awardname`, `imagepath`, `created_at`, `isdel`) VALUES (3, '三等奖', 0, 1511147268, 1);

DROP TABLE IF EXISTS `weixin_bimu_config`;
CREATE TABLE `weixin_bimu_config` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `imagepath` int(11) DEFAULT NULL COMMENT '闭幕墙鸣谢图id',
  `fullscreen` tinyint(1) DEFAULT 1 COMMENT '1表示居中2表示全屏',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='闭幕墙配置';

INSERT INTO `weixin_bimu_config` VALUES (1,'',1);

DROP TABLE IF EXISTS `weixin_cookie`;
CREATE TABLE `weixin_cookie` (
  `cookie` text NOT NULL,
  `cookies` text NOT NULL,
  `token` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `weixin_danmu_config`;
CREATE TABLE `weixin_danmu_config` (
  `id` int(11) NOT NULL COMMENT 'id',
  `danmuswitch` tinyint(1) DEFAULT '1' COMMENT '1表示关2表示开',
  `textcolor` varchar(7) CHARACTER SET utf8 DEFAULT NULL COMMENT '16进制颜色值',
  `looptime` int(3) DEFAULT NULL COMMENT '消息显示的时间间隔，单位是秒',
  `isloop` tinyint(1) DEFAULT NULL COMMENT '1表示不循环2表示循环',
  `historynum` int(3) DEFAULT NULL COMMENT '循环时使用的历史记录条数',
  `positionmode` tinyint(1) DEFAULT NULL COMMENT '1表示上三分之一2表示中间三分之一3表示下三分之一4表示全屏随机',
  `showname` tinyint(1) DEFAULT NULL COMMENT '1不显示昵称2显示昵称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `weixin_danmu_config`(`id`, `danmuswitch`, `textcolor`, `looptime`, `isloop`, `historynum`, `positionmode`, `showname`) VALUES (1, 1, '#b7e692', 3, 2, 30, 4, 2);

DROP TABLE IF EXISTS `weixin_flag`;
CREATE TABLE `weixin_flag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) NOT NULL COMMENT 'openid',
  `flag` int(11) DEFAULT NULL COMMENT '1表示未签到2表示签到成功',
  `vote` varchar(255) DEFAULT NULL COMMENT '投票的选项id序列，如：1,2,3',
  `nickname` varchar(255) DEFAULT NULL COMMENT '微信昵称',
  `avatar` text COMMENT '微信头像',
  `content` text,
  `fakeid` varchar(255) DEFAULT NULL,
  `sex` varchar(255) DEFAULT NULL COMMENT '性别',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常2禁用（禁用状态不能使用任何功能）',
  `othid` int(11) NOT NULL DEFAULT '0',
  `datetime` int(10) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL COMMENT '电话',
  `fromtype` varchar(25) DEFAULT NULL COMMENT '签到来源weixin',
  `rentopenid` varchar(28) DEFAULT NULL COMMENT '借用来openid',
  `signname` varchar(32) NOT NULL DEFAULT '' COMMENT '签到记录的姓名',
  `signorder` int(11) DEFAULT NULL COMMENT '签到顺序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid` (`openid`),
  KEY `openid_2` (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `weixin_kaimu_config`;
CREATE TABLE `weixin_kaimu_config` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `imagepath` int(11) DEFAULT NULL COMMENT '开幕墙图片地址id',
  `fullscreen` tinyint(1) DEFAULT 1 COMMENT '1表示居中2表示全屏',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='开幕墙配置';

INSERT INTO `weixin_kaimu_config` VALUES (1,'',1);

DROP TABLE IF EXISTS `weixin_log`;
CREATE TABLE `weixin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `echostr` mediumtext,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `weixin_plugs`;
CREATE TABLE `weixin_plugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '模块名',
  `title` varchar(255) DEFAULT NULL COMMENT '模块中文名',
  `switch` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0' COMMENT '1表示开2表示关',
  `url` varchar(255) DEFAULT NULL COMMENT 'url',
  `img` varchar(255) DEFAULT NULL COMMENT '图标',
  `ordernum` tinyint(3) unsigned zerofill DEFAULT '000' COMMENT '排序号',
  `choujiang` tinyint(1) unsigned zerofill DEFAULT '0' COMMENT '0表示不是抽奖项目1表示不能重复中奖，2表示可以重复中奖',
  `hotkey` varchar(10) DEFAULT NULL COMMENT '快捷键',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
    
INSERT INTO `weixin_plugs` VALUES (1,'cj','抽奖',1,'/wall/lottory.php','themes/meepo/assets/images/icon/ico001-.png',006,1,'c'),(2,'qdq','签到墙',1,'/wall/index.php','themes/meepo/assets/images/icon/ico005.png',001,0,'q'),(3,'ddp','对对碰',1,'/wall/ddp.php','themes/meepo/assets/images/icon/ico006.png',010,0,'d'),(4,'vote','投票',1,'/wall/vote.php','themes/meepo/assets/images/icon/ico004.png',005,0,'t'),(5,'shake','摇一摇',1,'/wall/shake.php','themes/meepo/assets/images/icon/ico002-.png',004,0,'y'),(6,'xysjh','幸运手机号',1,'/wall/xysjh.php','themes/meepo/assets/images/icon/ico019.png',008,0,'s'),(7,'xyh','幸运号码',1,'/wall/xyh.php','themes/meepo/assets/images/icon/ico016.png',007,0,'h'),(8,'zjd','砸金蛋',1,'/wall/zjd.php','themes/meepo/assets/images/icon/ico018.png',009,1,'z'),(9,'threedimensionalsign','3D签到',1,'/wall/3dsign.php','themes/meepo/assets/images/icon/ico013.png',002,0,'3'),(10,'wall','微信上墙',1,'/wall/wall.php','themes/meepo/assets/images/icon/ico009.png',003,0,'w'),(11,'cjx','抽奖箱',1,'/wall/cjx.php','themes/meepo/assets/images/icon/ico017.png',011,1,'x'),(12,'xiangce','相册',1,'/wall/xiangce.php','themes/meepo/assets/images/icon/ico003.png',012,0,'p'),(13,'danmu','弹幕文字',2,'/wall/danmu.php','themes/meepo/assets/images/icon/ico009.png',013,0,'m'),(14,'kaimu','开幕墙',1,'/wall/kaimu.php','themes/meepo/assets/images/icon/ico007.png',014,0,'k'),(15,'bimu','闭幕墙',1,'/wall/bimu.php','themes/meepo/assets/images/icon/ico014.png',015,0,'b'),(16,'redpacket','红包雨',2,'/wall/redpacket.php','themes/meepo/assets/images/icon/redpack3.png',016,0,'r'),(17, 'threedimensionallottery', '3D抽奖', 1, '/wall/3dlottery.php', 'themes/meepo/assets/images/icon/3dlottery.png', 017, 1, 'l');

DROP TABLE IF EXISTS `weixin_redpacket_config`;
CREATE TABLE `weixin_redpacket_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule` text COMMENT '抢红包规则',
  `tips` text COMMENT '提示语',
  `sendname` varchar(32) DEFAULT NULL COMMENT '红包发送者名称',
   `wishing` varchar(128) DEFAULT NULL COMMENT '祝福语',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='红包配置信息';

INSERT INTO `weixin_redpacket_config` VALUES (1,'1.用户打开微信扫描大屏幕上的二维码进入等待抢红包页面\n2.主持人说开始后，大屏幕和手机页面同时落下红包雨\n3.用户随机选择落下的红包，并拆开红包。\n4.如果倒计时还在继续，那么无论用户是否抢到了，都可以继续抢 直到倒计时完成。','大屏幕倒计时开始，\n红包将从大屏幕降落到手机，此时\n手指戳红包即可参与\n抢红包游戏','','');

DROP TABLE IF EXISTS `weixin_redpacket_order_return`;
CREATE TABLE `weixin_redpacket_order_return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_code` varchar(16) DEFAULT NULL COMMENT '返回状态吗',
  `return_msg` varchar(128) DEFAULT NULL COMMENT '返回信息表',
  `sign` varchar(32) DEFAULT NULL COMMENT '签名信息',
  `result_code` varchar(16) DEFAULT NULL COMMENT '业务结果',
  `err_code` varchar(32) DEFAULT NULL COMMENT '错误代码',
  `err_code_des` varchar(128) DEFAULT NULL COMMENT '错误代码描述',
  `mch_billno` varchar(28) DEFAULT NULL COMMENT '商户订单号',
  `mch_id` varchar(32) DEFAULT NULL COMMENT '商户号',
  `wxappid` varchar(32) DEFAULT NULL COMMENT '公众号appid',
  `re_openid` varchar(32) DEFAULT NULL COMMENT '收红包用户的openid',
  `total_amount` int(11) DEFAULT NULL COMMENT '付款金额',
  `send_listid` varchar(32) DEFAULT NULL COMMENT '微信单号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='发红包返回信息表';

DROP TABLE IF EXISTS `weixin_redpacket_orders`;
CREATE TABLE `weixin_redpacket_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `mch_billno` varchar(28) DEFAULT NULL COMMENT '商户订单号',
  `mch_id` varchar(32) DEFAULT NULL COMMENT '商户号',
  `wxappid` varchar(32) DEFAULT NULL COMMENT '公众号appid',
  `send_name` varchar(32) DEFAULT NULL COMMENT '红包发送者名称',
  `re_openid` varchar(32) DEFAULT NULL COMMENT '接受红包的openid',
  `total_num` int(11) DEFAULT '1',
  `wishing` varchar(128) DEFAULT NULL COMMENT '祝福语',
  `client_ip` varchar(15) DEFAULT NULL COMMENT '调用接口机器的ip',
  `act_name` varchar(32) DEFAULT NULL COMMENT '活动名称',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注信息',
  `scene_id` varchar(32) DEFAULT NULL COMMENT '场景id',
  `risk_info` varchar(128) DEFAULT NULL COMMENT '活动信息',
  `consume_mch_id` varchar(32) DEFAULT NULL COMMENT '资金授权商户号',
  `nonce_str` varchar(32) DEFAULT NULL COMMENT '随机字符串',
  `sign` varchar(32) DEFAULT NULL COMMENT '数据签名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='红包订单表';

DROP TABLE IF EXISTS `weixin_redpacket_round`;
CREATE TABLE `weixin_redpacket_round` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '1未开始2进行中3结束',
  `type` tinyint(1) unsigned DEFAULT '1' COMMENT '1普通红包2随机红包',
  `amount` int(8) unsigned DEFAULT '0' COMMENT '红包金额 单位是分',
  `num` int(4) unsigned DEFAULT '1' COMMENT '红包个数大于1',
  `numperperson` tinyint(3) unsigned DEFAULT '1' COMMENT '每个用户此轮可抢的红包数量，默认为1个',
  `chance` int(4) unsigned DEFAULT '0' COMMENT '红包获得概率，单位是千分之1',
  `lefttime` int(11) unsigned DEFAULT '30' COMMENT '活动持续时间，单位是秒',
  `minamount` int(8) unsigned DEFAULT '0' COMMENT '随机红包最小金额大于100，单位是分',
  `maxamount` int(8) unsigned DEFAULT '0' COMMENT '随机红包的最大金额',
  `started_at` int(11) DEFAULT NULL COMMENT '轮次开始时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='红包轮次配置';

DROP TABLE IF EXISTS `weixin_redpacket_users`;
CREATE TABLE `weixin_redpacket_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL COMMENT '用户id',
  `roundid` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='红包雨中奖用户数据';

DROP TABLE IF EXISTS `weixin_sessions`;
CREATE TABLE `weixin_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '0',
  `user_agent` varchar(200) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `weixin_shake_config`;
CREATE TABLE `weixin_shake_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `maxplayers` int(4) DEFAULT NULL COMMENT '最大参与人数',
  `duration` int(4) DEFAULT NULL COMMENT '摇一摇持续时间',
  `maxdisplayplayers` int(4) DEFAULT NULL COMMENT '最大大屏幕显示人数',
  `currentroundno` int(3) DEFAULT NULL COMMENT '当前轮次0开始0表示第1轮',
  `currentroundstatus` tinyint(1) DEFAULT NULL COMMENT '1表示未开始2表示开始3表示人满4表示结束',
  `maxround` int(2) DEFAULT NULL COMMENT '最大轮次',
  `showstyle` tinyint(1) unsigned DEFAULT '1' COMMENT '1表示汽车2表示气球',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='摇一摇配置表';
    
INSERT INTO `weixin_shake_config` VALUES (1,150,30,10,0,1,30,1);

DROP TABLE IF EXISTS `weixin_shake_toshake`;
CREATE TABLE `weixin_shake_toshake` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(255) NOT NULL COMMENT '微信昵称',
  `openid` varchar(255) NOT NULL COMMENT 'openid',
  `point` int(11) NOT NULL COMMENT '摇晃的次数',
  `avatar` text NOT NULL COMMENT '头像',
  `roundno` int(3) DEFAULT NULL COMMENT '轮次起始数字是0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

DROP TABLE IF EXISTS `weixin_system_config`;
CREATE TABLE `weixin_system_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `configkey` varchar(255) DEFAULT NULL COMMENT '配置名称',
  `configvalue` varchar(255) DEFAULT NULL COMMENT '配置值',
  `configname` varchar(255) DEFAULT NULL COMMENT '配置中文名称',
  `configcomment` text COMMENT '配置备注说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统配置表';

INSERT INTO `weixin_system_config`(`id`, `configkey`, `configvalue`, `configname`, `configcomment`) VALUES (1, 'SAVEFILEMODE', 'file', '文件保存模式', 'file表示文件保存，aliyunoss表示阿里云oss保存图片');
INSERT INTO `weixin_system_config`(`id`, `configkey`, `configvalue`, `configname`, `configcomment`) VALUES (2, 'mobileqiandaobg', '0', '手机端签到页面背景', '手机签到页面的背景图，默认0是现在的星空背景');
INSERT INTO `weixin_system_config`(`id`, `configkey`, `configvalue`, `configname`, `configcomment`) VALUES (3, 'cjshowtype', '1', '抽奖结果显示', '1昵称2姓名3手机号');
INSERT INTO `weixin_system_config`(`id`, `configkey`, `configvalue`, `configname`, `configcomment`) VALUES (4, 'threedimensionallotteryshowtype', '1', '3d抽奖结果显示', '1昵称2姓名3手机号');
INSERT INTO `weixin_system_config`(`id`, `configkey`, `configvalue`, `configname`, `configcomment`) VALUES (5, 'cjxshowtype', '1', '抽奖箱结果显示', '1昵称2姓名3手机号');
INSERT INTO `weixin_system_config`(`id`, `configkey`, `configvalue`, `configname`, `configcomment`) VALUES (6, 'zjdshowtype', '1', '砸金蛋结果显示', '1昵称2姓名3手机号');
INSERT INTO `weixin_system_config`(`id`, `configkey`, `configvalue`, `configname`, `configcomment`) VALUES (7, 'wallnameshowstyle', '1', '上墙消息显示', '1昵称2姓名3手机号');
INSERT INTO `weixin_system_config`(`id`, `configkey`, `configvalue`, `configname`, `configcomment`) VALUES (8, 'signnameshowstyle', '1', '签到显示', '1昵称2姓名3手机号');


DROP TABLE IF EXISTS `weixin_threedimensional`;
CREATE TABLE `weixin_threedimensional` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `avatarnum` tinyint(3) unsigned zerofill DEFAULT '000',
  `datastr` text,
  `avatarsize` tinyint(3) DEFAULT NULL,
  `avatargap` tinyint(3) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='3d签到动画设置';
		
INSERT INTO `weixin_threedimensional` VALUES (1,030,'新年快乐|#sphere|新年快乐|#torus|#helix',7,15);
		


DROP TABLE IF EXISTS `weixin_vote`;
CREATE TABLE `weixin_vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `res` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `res` (`res`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
		
DROP TABLE IF EXISTS `weixin_wall`;
CREATE TABLE `weixin_wall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `messageid` varchar(255) DEFAULT NULL,
  `fakeid` varchar(255) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  `content` text,
  `nickname` text,
  `avatar` text,
  `ret` tinyint(1) DEFAULT NULL COMMENT '0待审核1审核通过2审核不通过',
  `fromtype` varchar(255) DEFAULT NULL,
  `image` int(11) DEFAULT NULL COMMENT '图片路径id',
  `datetime` int(10) DEFAULT NULL,
  `openid` varchar(32) DEFAULT NULL COMMENT '发送人的openid',
  `shenhetime` int(11) DEFAULT '0' COMMENT '按照审核的时间顺序来',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
		
DROP TABLE IF EXISTS `weixin_wall_config`;
CREATE TABLE `weixin_wall_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `success` text NOT NULL COMMENT '消息发送成功但是没有审核时的提醒信息，自由手动审核才用这句',
  `acttitle` text NOT NULL COMMENT '摇一摇标题',
  `shenghe` int(11) NOT NULL COMMENT '0自动审核1手动审核',
  `cjreplay` tinyint(4) NOT NULL DEFAULT '0' COMMENT '中奖是否需要回复',
  `timeinterval` int(3) NOT NULL DEFAULT '0' COMMENT '观众发送消息的频率，单位秒',
  `shakeopen` tinyint(4) NOT NULL DEFAULT '1' COMMENT '摇一摇开关',
  `voteopen` tinyint(4) NOT NULL DEFAULT '1' COMMENT '投票开关1打开2关闭',
  `votetitle` text NOT NULL COMMENT '投票标题',
  `votefresht` tinyint(4) NOT NULL COMMENT '投票结果刷新时间',
  `circulation` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否循环播放1循环0不循环',
  `refreshtime` tinyint(2) NOT NULL DEFAULT '0' COMMENT '前台刷新时间，单位秒',
  `voteshowway` tinyint(1) DEFAULT '1' COMMENT '投票结果显示方式',
  `votecannum` varchar(255) DEFAULT '1' COMMENT '每个人可以投几票',
  `black_word` text COMMENT '屏蔽关键字',
  `screenpaw` varchar(255) NOT NULL COMMENT '开场密码',
  `rentweixin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0不借用其他微信号获取用户信息1借用其他微信服务号获取用户信息',
  `name_switch` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1记录姓名2不记录',
  `phone_switch` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1记录手机号2不记录',
  `bgimg` int(11) DEFAULT NULL COMMENT '背景图id',
  `logoimg` int(11) DEFAULT NULL COMMENT '左上角logo图id',
  `copyright` varchar(32) DEFAULT NULL COMMENT '版权',
  `copyrightlink` varchar(500) DEFAULT NULL COMMENT '版权连接',
  `welcometext1` varchar(255) DEFAULT NULL COMMENT '左上角logo右侧文字',
  `welcometext2` varchar(255) DEFAULT NULL COMMENT '左下角logo右侧文字',
  `bottom_logoimg` int(11) DEFAULT NULL COMMENT '左下角logoid',
  `msg_showstyle` tinyint(1) DEFAULT '0' COMMENT '消息显示方式 0滚动1反转',
  `msg_historynum` int(3) DEFAULT '30' COMMENT '循环播放时，循环显示的历史消息数量',
  `msg_showbig` tinyint(1) DEFAULT '0' COMMENT '图片消息是否放大显示0关闭1开启',
  `msg_showbigtime` tinyint(3) DEFAULT '5' COMMENT '开启显示放大图片消息时，显示放大图片的时间，单位是秒',
  `verifycode` varchar(255) DEFAULT NULL COMMENT '活动签到连接校验码',
  `maxplayers` int(11) unsigned DEFAULT '0' COMMENT '0表示不限，大于0表示限制n人数',
  `msg_color` varchar(7) DEFAULT '#4B9E09' COMMENT '16进制颜色值',
  `nickname_color` varchar(7) DEFAULT '#4B9E09' COMMENT '昵称颜色',
  `qrcodetoptext` varchar(255) DEFAULT '扫描下面的二维码参与签到' COMMENT '大二维码顶部文字',
  `msg_num` tinyint(1) DEFAULT '3' COMMENT '微信上墙消息数量 3，4，5，6可选',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
		
INSERT INTO `weixin_wall_config` VALUES (1,'你已经成功发送，等待审核通过即可上墙了','摇一摇',0,0,3,1,1,'你最喜欢微信墙的哪个功能？',3,1,3,1,'1','操,sb,傻逼,艹,日你妈,干你妹,老子,bitch,婊子','admin',2,1,1,'','','微赢科技','http://www.veiying.com','请先扫码关注我们的公众号','扫码关注我们的公众号','',0,30,0,5,'',0,'#4B9E09','#4B9E09','扫描下面的二维码参与签到',3);
		
DROP TABLE IF EXISTS `weixin_wall_num`;
CREATE TABLE `weixin_wall_num` (
  `num` int(11) NOT NULL,
  `lastmessageid` varchar(255) NOT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
		
INSERT INTO `weixin_wall_num` VALUES (1,'0');
		
DROP TABLE IF EXISTS `weixin_weixin_config`;
CREATE TABLE `weixin_weixin_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(255) NOT NULL COMMENT '微信名称',
  `erweima` int(11) NOT NULL DEFAULT '0' COMMENT '二维码id',
  `appid` varchar(64) DEFAULT NULL COMMENT '微信appid',
  `appsecret` varchar(128) DEFAULT NULL COMMENT '微信appsecret',
  `mch_id` varchar(255) DEFAULT NULL,
  `mchsecret` varchar(255) DEFAULT NULL,
  `apiclient_cert` text,
  `apiclient_key` text,
  `rootca` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

INSERT INTO `weixin_weixin_config` VALUES (1,'微信',0,'','',NULL,NULL,NULL,NULL,NULL);

DROP TABLE IF EXISTS `weixin_xiangce`;
CREATE TABLE `weixin_xiangce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imagepath` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='相册';

DROP TABLE IF EXISTS `weixin_xingyunhaoma`;
CREATE TABLE `weixin_xingyunhaoma` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `lucknum` int(11) DEFAULT NULL COMMENT '幸运号码',
  `designated` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1表示普通2表示必中3标识不会中',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `ordernum` int(11) NOT NULL COMMENT '第几个抽执行，如果是必中，那就是第几个会出现这个数字，如果是不会中，那就是第几个数字不会出现这个数字',
  `status` tinyint(1) DEFAULT NULL COMMENT '1未中2已中',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='幸运号码记录表';

DROP TABLE IF EXISTS `weixin_xingyunhaoma_config`;
CREATE TABLE `weixin_xingyunhaoma_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `minnum` int(11) NOT NULL DEFAULT '1' COMMENT '幸运号码最小值',
  `maxnum` int(11) NOT NULL DEFAULT '2000' COMMENT '幸运号码最大值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='幸运号码配置表';
INSERT INTO `weixin_xingyunhaoma_config` VALUES (1, 1, 1000);

DROP TABLE IF EXISTS `weixin_zjlist`;
CREATE TABLE `weixin_zjlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromplug` varchar(32) DEFAULT NULL COMMENT '来自哪个插件的中奖信息',
  `openid` varchar(64) DEFAULT NULL COMMENT '微信openid',
  `awardid` int(11) DEFAULT NULL COMMENT '奖品id',
  `status` tinyint(1) DEFAULT NULL COMMENT '中奖状态1表示未中2表示中奖3表示已发奖',
  `designated` tinyint(1) DEFAULT NULL COMMENT '内定状态1是普通状态2表示内定3表示不会中这个奖',
  `zjdatetime` int(11) DEFAULT NULL COMMENT '中奖时间',
  `fjdatetime` int(11) DEFAULT NULL COMMENT '发奖时间',
  `verifycode` varchar(16) DEFAULT NULL COMMENT '兑换码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='中奖者信息表';

DROP TABLE IF EXISTS `weixin_xingyunshoujihao`;
CREATE TABLE `weixin_xingyunshoujihao`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `openid` varbinary(255) DEFAULT NULL COMMENT 'openid',
  `designated` tinyint(1) DEFAULT NULL COMMENT '1表示普通2表示必中3标识不会中',
  `ordernum` int(11) DEFAULT NULL COMMENT '第几个抽执行，如果是必中，那就是第几个会出现这个数字，如果是不会中，那就是第几个数字不会出现这个数字',
  `status` tinyint(1) DEFAULT NULL COMMENT '1未中2已中',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '幸运手机号数据及内定信息表' ROW_FORMAT = Compact;

DROP VIEW IF EXISTS `weixin_view_cjperson`;
CREATE VIEW `weixin_view_cjperson` AS
  select `weixin_flag`.`openid` AS `openid`,`weixin_flag`.`flag` AS `flag`,`weixin_flag`.`nickname` AS `nickname`,`weixin_flag`.`avatar` AS `avatar`,`weixin_flag`.`content` AS `content`,`weixin_flag`.`status` AS `status`,`weixin_flag`.`datetime` AS `datetime`,`weixin_flag`.`phone` AS `phone`,`weixin_flag`.`signname` AS `signname`,`weixin_zjlist`.`awardid` AS `awardid`,`weixin_zjlist`.`designated` AS `designated` from (`weixin_flag` left join `weixin_zjlist` on((`weixin_zjlist`.`openid` = `weixin_flag`.`openid`))) where ((`weixin_flag`.`status` = 1) and (isnull(`weixin_zjlist`.`status`) or (`weixin_zjlist`.`status` = 1)) and (isnull(`weixin_zjlist`.`designated`) or (`weixin_zjlist`.`designated` = 1) or (`weixin_zjlist`.`designated` = 2))) order by `weixin_zjlist`.`designated` desc;
    
DROP VIEW IF EXISTS `weixin_view_zjlist`;
CREATE VIEW `weixin_view_zjlist` AS
  select `zjlist`.`id` AS `id`,`zjlist`.`fromplug` AS `fromplug`,`zjlist`.`status` AS `status`,`zjlist`.`designated` AS `designated`,`zjlist`.`zjdatetime` AS `zjdatetime`,`zjlist`.`fjdatetime` AS `fjdatetime`,`zjlist`.`verifycode` AS `verifycode`,`flag`.`openid` AS `openid`,`flag`.`nickname` AS `nickname`,`flag`.`avatar` AS `avatar`,`flag`.`signname` AS `signname`,`flag`.`phone` AS `phone`,`zjlist`.`awardid` AS `awardid`,`award`.`awardname` AS `awardname` from ((`weixin_zjlist` `zjlist` left join `weixin_award` `award` on((`zjlist`.`awardid` = `award`.`id`))) left join `weixin_flag` `flag` on((`flag`.`openid` = `zjlist`.`openid`)));