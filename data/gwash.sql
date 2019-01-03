/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50611
Source Host           : localhost:3306
Source Database       : gwash

Target Server Type    : MYSQL
Target Server Version : 50611
File Encoding         : 65001

Date: 2019-01-03 11:13:36
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `gwash_system_admin_user`
-- ----------------------------
DROP TABLE IF EXISTS `gwash_system_admin_user`;
CREATE TABLE `gwash_system_admin_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码',
  `nickname` varchar(64) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '用户密码',
  `regTime` int(10) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `regIp` varchar(32) NOT NULL DEFAULT '' COMMENT '注册IP',
  `updateTime` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '账号状态:0.禁用,1.启用',
  `last_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `last_login` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_city` varchar(50) NOT NULL DEFAULT '' COMMENT '最后登录城市',
  `login_count` int(10) NOT NULL DEFAULT '0' COMMENT '登录次数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员认证信息';

-- ----------------------------
-- Records of gwash_system_admin_user
-- ----------------------------
INSERT INTO `gwash_system_admin_user` VALUES ('1', 'admin', '1', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '1492004246', '3682992231', '1492236545', '1', '', '0', '', '0');

-- ----------------------------
-- Table structure for `gwash_system_admin_user_action`
-- ----------------------------
DROP TABLE IF EXISTS `gwash_system_admin_user_action`;
CREATE TABLE `gwash_system_admin_user_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `actionName` varchar(50) NOT NULL DEFAULT '' COMMENT '行为名称',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '操作用户ID',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `addTime` int(11) NOT NULL DEFAULT '0' COMMENT '操作时间',
  `data` text COMMENT '用户提交的数据',
  `url` varchar(200) NOT NULL DEFAULT '' COMMENT '操作URL',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户操作日志';

-- ----------------------------
-- Records of gwash_system_admin_user_action
-- ----------------------------

-- ----------------------------
-- Table structure for `gwash_system_admin_user_data`
-- ----------------------------
DROP TABLE IF EXISTS `gwash_system_admin_user_data`;
CREATE TABLE `gwash_system_admin_user_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `loginTimes` int(11) NOT NULL COMMENT '账号登录次数',
  `lastLoginIp` varchar(11) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `lastLoginTime` int(11) NOT NULL COMMENT '最后登录时间',
  `uid` varchar(11) NOT NULL DEFAULT '' COMMENT '用户ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员数据表';

-- ----------------------------
-- Records of gwash_system_admin_user_data
-- ----------------------------

-- ----------------------------
-- Table structure for `gwash_system_auth_group`
-- ----------------------------
DROP TABLE IF EXISTS `gwash_system_auth_group`;
CREATE TABLE `gwash_system_auth_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '组名称',
  `description` varchar(50) NOT NULL COMMENT '组描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '组状态:1.正常,0.禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限组';

-- ----------------------------
-- Records of gwash_system_auth_group
-- ----------------------------

-- ----------------------------
-- Table structure for `gwash_system_auth_group_access`
-- ----------------------------
DROP TABLE IF EXISTS `gwash_system_auth_group_access`;
CREATE TABLE `gwash_system_auth_group_access` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `groupId` int(11) NOT NULL DEFAULT '0' COMMENT '权限模块ID',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户和组的对应关系';

-- ----------------------------
-- Records of gwash_system_auth_group_access
-- ----------------------------

-- ----------------------------
-- Table structure for `gwash_system_auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `gwash_system_auth_rule`;
CREATE TABLE `gwash_system_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `url` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `groupId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限所属组的ID',
  `auth` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '权限数值',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限细节';

-- ----------------------------
-- Records of gwash_system_auth_rule
-- ----------------------------

-- ----------------------------
-- Table structure for `gwash_system_auto_task`
-- ----------------------------
DROP TABLE IF EXISTS `gwash_system_auto_task`;
CREATE TABLE `gwash_system_auto_task` (
  `task_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '表的主键,自增',
  `task_code` varchar(100) NOT NULL COMMENT '任务代码，唯一键',
  `is_copy` tinyint(4) NOT NULL DEFAULT '0' COMMENT '复制次数',
  `task_name` varchar(100) NOT NULL DEFAULT '' COMMENT '任务名称',
  `type` varchar(20) NOT NULL COMMENT '模块类型',
  `cmd` varchar(255) NOT NULL COMMENT '命令执行相对路径',
  `run_days` varchar(20) NOT NULL DEFAULT '' COMMENT '指定哪天运行: 0.星期天,1.星期一,2.星期二,3.星期三, 4.星期四,5.星期五,6.星期六,7.每天',
  `run_times` varchar(200) NOT NULL DEFAULT '' COMMENT '指定运行的时间点',
  `lx_time` int(11) NOT NULL COMMENT '轮询时间 分钟',
  `process_num` tinyint(4) NOT NULL DEFAULT '0' COMMENT '进程数',
  `is_on` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启动自动服务 1:启动,0:不启动',
  `last_exec_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次执行，启动时间点',
  `allow_ips` varchar(200) NOT NULL COMMENT '服务器运行ip',
  `platform_code` varchar(20) NOT NULL DEFAULT '' COMMENT '平台代码',
  `shop_ids` varchar(300) NOT NULL DEFAULT '' COMMENT '店铺ids',
  `description` varchar(300) NOT NULL DEFAULT '' COMMENT '描述',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`task_id`),
  UNIQUE KEY `task_code` (`task_code`,`is_copy`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=438 DEFAULT CHARSET=utf8 COMMENT='系统自动服务管理';

-- ----------------------------
-- Records of gwash_system_auto_task
-- ----------------------------
INSERT INTO `gwash_system_auto_task` VALUES ('1', 'T001', '0', '淘宝订单下载', 'taobao', 'sdk.php Sdk/Tb DownloadAllOrders', '', '', '15', '0', '0', '0', '', 'TB', '', '淘宝订单下载,下载时间段日志记录,日志目录Sdk/Cache/Opt/Tb,文件名tb_download_order_(shop_id)_data.csv', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('2', 'T002', '0', '淘宝商品下载', 'taobao', 'sdk.php Sdk/Tb DownloadAllGoods', '', '', '120', '0', '0', '0', '', 'TB', '', '淘宝商品下载', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('3', 'T003', '0', '淘宝退单下载', 'taobao', 'sdk.php Sdk/Tb DownloadAllRefunds', '', '', '6', '0', '0', '0', '', 'TB', '', '淘宝退单下载', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('4', 'T004', '0', '淘宝在售商品库存同步', 'taobao', 'sdk.php Sdk/Tb StockSyncShops --approve_status=0 --distribution_status=0', '', '', '10', '0', '0', '0', '', 'TB', '', '淘宝在售商品库存同步', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('5', 'T005', '0', '淘宝在库商品库存同步', 'taobao', 'sdk.php Sdk/Tb StockSyncShops --approve_status=1 --distribution_status=0', '', '', '10', '0', '0', '0', '', 'TB', '', '淘宝在库商品库存同步', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('6', 'T006', '0', '淘分销在库商品库存同步', 'taobao', 'sdk.php Sdk/Tb StockSyncShops --approve_status=0 --distribution_status=1', '', '', '10', '0', '0', '0', '', 'TB', '', '淘分销在库商品库存同步', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('7', 'T007', '0', '淘分销在库商品库存同步', 'taobao', 'sdk.php Sdk/Tb StockSyncShops --approve_status=1 --distribution_status=1', '', '', '10', '0', '0', '0', '', 'TB', '', '淘分销在库商品库存同步', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('8', 'JD001', '0', '京东订单下载', 'jingdong', 'sdk.php Sdk/Jd DownloadAllOrders', '', '', '15', '0', '0', '0', '', 'JD', '', '京东订单下载', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('9', 'JD005', '0', '京东面单下载', 'jingdong', 'sdk.php Sdk/Jd DownloadAllPrints', '', '', '10', '0', '0', '0', '', 'JD', '', '京东面单下载', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('10', 'JD006', '0', '京东退单下载', 'jingdong', 'sdk.php Sdk/Jd DownloadAllRefunds', '', '', '10', '0', '0', '0', '', 'JD', '', '京东退单下载', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('11', 'JD002', '0', '京东商品下载', 'jingdong', 'sdk.php Sdk/Jd DownloadAllGoods', '', '', '120', '0', '0', '0', '', 'JD', '', '京东商品下载', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('12', 'JD003', '0', '京东在售商品库存同步', 'jingdong', 'sdk.php Sdk/Jd StockSyncShops --approve_status=0 --distribution_status=0', '', '', '15', '0', '0', '0', '', 'JD', '', '京东在售商品库存同步', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('13', 'JD004', '0', '京东待售商品库存同步', 'jingdong', 'sdk.php Sdk/Jd StockSyncShops --approve_status=1 --distribution_status=0', '', '', '60', '0', '0', '0', '', 'JD', '', '京东待售商品库存同步', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('14', 'SMT001', '0', '速卖通订单下载', 'smt', 'sdk.php Sdk/Smt DownloadAllOrders', '', '', '15', '0', '0', '0', '', 'SMT', '', '速卖通订单下载', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('15', 'EB001', '0', 'EBAY订单下载', 'eb', 'sdk.php Sdk/Eb DownloadAllOrders', '', '', '15', '0', '0', '0', '', 'EB', '', 'EBAY订单下载', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('16', 'EB002', '0', 'EBAY商品下载', 'eb', 'sdk.php Sdk/Eb DownloadAllGoods', '', '', '120', '0', '0', '0', '', 'EB', '', 'EBAY商品下载', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('17', 'YZ001', '0', '有赞订单下载', 'youzan', 'sdk.php Sdk/Yz DownloadAllOrders', '', '', '15', '0', '0', '0', '', 'YZ', '', '有赞订单下载', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('18', 'YZ002', '0', '有赞商品下载', 'youzan', 'sdk.php Sdk/Yz DownloadAllGoods', '', '', '120', '0', '0', '0', '', 'YZ', '', '有赞商品下载', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('19', 'YZ003', '0', '有赞在售商品库存同步', 'youzan', 'sdk.php Sdk/Yz StockSyncShops --approve_status=0 --distribution_status=0', '', '', '10', '0', '0', '0', '', 'YZ', '', '有赞在售商品库存同步', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('20', 'YZ004', '0', '有赞在库商品库存同步', 'youzan', 'sdk.php Sdk/Yz StockSyncShops --approve_status=1 --distribution_status=0', '', '', '10', '0', '0', '0', '', 'YZ', '', '有赞在库商品库存同步', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('21', 'O001', '0', '订单预处理', 'order', 'index.php Order/TransOrder Trans', '', '', '6', '3', '0', '0', '', '', '', '订单预处理', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('22', 'O002', '0', '订单适配', 'order', 'index.php Order/TransOrder Allocation', '', '', '6', '3', '0', '0', '', '', '', '订单适配', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('23', 'O003', '0', '订单审核', 'order', 'index.php Order/Order OrderCheck', '', '', '6', '3', '0', '0', '', '', '', '订单审核', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('24', 'O004', '0', '订单解除缺货', 'order', 'index.php Order/Order OrderRemoveOutOfStock', '', '', '10', '1', '0', '0', '', '', '', '订单解除缺货', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('25', 'O005', '0', '订单合并', 'order', 'index.php Order/Order OrderCombine', '', '', '6', '0', '0', '0', '', '', '', '订单合并', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('26', 'O006', '0', '订单拆分', 'order', 'index.php Order/Order OrderSplit', '', '', '6', '0', '0', '0', '', '', '', '订单拆分', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('27', 'O007', '0', '订单通知配货', 'order', 'index.php Order/Order OrderNotice', '', '', '6', '3', '0', '0', '', '', '', '订单通知配货', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('28', 'O008', '0', '更新订单商家备注', 'order', 'index.php Order/Order OrderUpdateSellerMemo', '', '', '10', '0', '0', '0', '', '', '', '更新订单商家备注', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('29', 'O009', '0', '订单发货回写', 'order', 'index.php Order/Order OrderShipping', '', '', '6', '2', '0', '0', '', '', '', '订单发货回写', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('30', 'O102', '0', '缺货订单自动转仓', 'order', 'index.php Order/Order OutStockOrderChangeWarehouse', '', '', '20', '0', '0', '0', '', '', '', '缺货订单自动转到有库存的仓库', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('31', 'O103', '0', '平台退单导入', 'order', 'index.php Order/TransRefund Trans', '', '', '10', '3', '0', '0', '', '', '', '平台退单导入', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('32', 'H001', '0', '清道夫', 'system', 'index.php System/System Clear', '', '', '1440', '0', '0', '0', '', '', '', '库存事务转移到文件,库存事务文件路径Cache/Opt/Stock, 文件名：stock_transaction_log.csv', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('33', 'H002', '0', '御城河日志上传', 'system', 'sdk.php Sdk/Aliyun BatchUploadAliyunLog', '', '', '1440', '0', '0', '0', '', '', '', '御城河日志上传', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('34', 'S001', '0', '库存事务转移到文件', 'stock', 'index.php Stock/Stock MoveTransactionLog', '', '', '60', '0', '0', '0', '', '', '', '库存事务转移到文件,库存事务文件路径Cache/Opt/Stock, 文件名：stock_transaction_log.csv', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('35', 'W001', '0', 'WMS上传档案', 'wms', 'index.php Wms/Wms UploadArchive', '', '', '3600', '3', '0', '0', '', '', '', 'WMS上传档案', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('36', 'W002', '0', 'WMS上传订单', 'wms', 'index.php Wms/Wms UploadOrder', '', '', '300', '3', '0', '0', '', '', '', 'WMS上传订单', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('37', 'W003', '0', 'WMS取消订单', 'wms', 'index.php Wms/Wms CancelOrder', '', '', '300', '3', '0', '0', '', '', '', 'WMS取消订单', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('38', 'W004', '0', 'WMS更新OMS库存', 'wms', 'index.php Wms/Wms StockProcess', '', '', '300', '0', '0', '0', '', '', '', 'WMS更新OMS库存', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('39', 'W005', '0', 'WMS收发货状态同步', 'wms', 'index.php Wms/Wms Shipping', '', '', '1200', '3', '0', '0', '', '', '', 'WMS收发货状态同步', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('40', 'W006', '0', 'WMS快递修改同步', 'wms', 'index.php Wms/Wms ExpressChanges', '', '', '1200', '0', '0', '0', '', '', '', 'WMS快递修改同步', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('41', 'W007', '0', 'WMS零库存更新OMS库存', 'wms', 'index.php Wms/Wms StockZeroProcess', '', '', '3600', '0', '0', '0', '', '', '', 'WMS零库存更新OMS库存', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('42', 'FS001', '0', '短信发送数据获取', 'service', 'index.php Service/SendMessage GetTimerSendOrder', '', '', '60', '0', '0', '0', '', '', '', '短信发送数据获取', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('43', 'FS002', '0', '短信发送', 'service', 'index.php Service/SendMessage TimerSendMessages', '', '', '60', '0', '0', '0', '', '', '', '短信发送', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('44', 'ZJ001', '0', '有主件定时审核', 'warehouse', 'index.php Warehouse/TheMainThing TimeAudit', '', '', '60', '0', '0', '0', '', '', '', '有主件定时审核', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('45', 'ZJ002', '0', '无主件定时审核', 'warehouse', 'index.php Warehouse/TheMainThing TimeNoAudit', '', '', '60', '0', '0', '0', '', '', '', '无主件定时审核', '2016-04-07 11:14:18');
INSERT INTO `gwash_system_auto_task` VALUES ('46', 'T008', '0', '淘宝订单下载补漏', 'taobao', 'sdk.php Sdk/Tb FixDownTrade', '', '', '10', '0', '0', '0', '', 'TB', '', '淘宝订单下载补漏', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('47', 'T009', '0', '淘宝退单下载补漏', 'taobao', 'sdk.php Sdk/Tb FixDownRefund', '', '', '30', '0', '0', '0', '', 'TB', '', '淘宝退单下载补漏', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('48', 'JD007', '0', '京东订单下载补漏', 'jingdong', 'sdk.php Sdk/Jd FixDownTrade', '', '', '30', '0', '0', '0', '', 'JD', '', '京东订单下载补漏', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('49', 'JD008', '0', '京东退单下载补漏', 'jingdong', 'sdk.php Sdk/Jd FixDownRefund', '', '', '20', '0', '0', '0', '', 'JD', '', '京东退单下载补漏', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('50', 'JDKJ001', '0', '京东跨境订单下载', 'jdkj', 'sdk.php Sdk/Jdkj DownloadAllOrders', '', '', '15', '0', '0', '0', '', 'JDKJ', '', '京东跨境订单下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('51', 'JDKJ002', '0', '京东跨境快递单号下载', 'jdkj', 'sdk.php Sdk/Jdkj DownloadExpressNos', '', '', '20', '0', '0', '0', '', 'JDKJ', '', '京东跨境快递单号下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('52', 'JDKJ003', '0', '京东跨境订单下载补漏', 'jdkj', 'sdk.php Sdk/Jdkj FixDownTrade', '', '', '30', '0', '0', '0', '', 'JDKJ', '', '京东跨境订单下载补漏', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('53', 'EB003', '0', 'EBAY争议下载', 'eb', 'sdk.php Sdk/Eb DownloadAllDisputes', '', '', '15', '0', '0', '0', '', 'EB', '', 'EBAY争议下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('54', 'EB004', '0', 'EBAY发起未付款争议', 'eb', 'sdk.php Sdk/Eb AddDispute', '', '', '15', '0', '0', '0', '', 'EB', '', 'EBAY发起未付款争议', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('55', 'EB005', '0', 'EBAY关闭超时争议', 'eb', 'sdk.php Sdk/Eb CloseDispute', '', '', '15', '0', '0', '0', '', 'EB', '', 'EBAY关闭超时争议', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('56', 'YG001', '0', '优购订单下载', 'yg', 'sdk.php Sdk/Yg DownloadAllOrders', '', '', '15', '0', '0', '0', '', 'YG', '', '优购订单下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('57', 'YG002', '0', '优购商品下载', 'yg', 'sdk.php Sdk/Yg DownloadAllGoods', '', '', '120', '0', '0', '0', '', 'YG', '', '优购商品下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('58', 'YG003', '0', '优购在售商品库存同步', 'yg', 'sdk.php Sdk/Yg StockSyncShops --approve_status=0 --distribution_status=0', '', '', '10', '0', '0', '0', '', 'YG', '', '优购在售商品库存同步', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('59', 'YG004', '0', '优购在库商品库存同步', 'yg', 'sdk.php Sdk/Yg StockSyncShops --approve_status=1 --distribution_status=0', '', '', '10', '0', '0', '0', '', 'YG', '', '优购在库商品库存同步', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('60', 'YMX001', '0', '亚马逊订单下载', 'ymx', 'sdk.php Sdk/Ymx DownloadAllOrders', '', '', '15', '0', '0', '0', '', 'YMX', '', '亚马逊订单下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('61', 'YMX002', '0', '亚马逊订单下载补漏', 'ymx', 'sdk.php Sdk/Ymx FixDownTrade', '', '', '30', '0', '0', '0', '', 'YMX', '', '亚马逊订单下载补漏', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('62', 'YMX006', '0', '亚马逊商品下载', 'ymx', 'sdk.php Sdk/Ymx DownloadAllGoods', '', '', '120', '0', '0', '0', '', 'YMX', '', '亚马逊商品下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('63', 'YMX007', '0', '亚马逊商品库存同步', 'ymx', 'sdk.php Sdk/Ymx StockSyncShops --approve_status=0 --distribution_status=0', '', '', '15', '0', '0', '0', '', 'YMX', '', '亚马逊商品库存同步', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('64', 'YHD001', '0', '一号店订单下载', 'yhd', 'sdk.php Sdk/Yhd DownloadAllOrders', '', '', '15', '0', '0', '0', '', 'YHD', '', '一号店订单下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('65', 'YHD007', '0', '一号店订单下载补漏', 'yhd', 'sdk.php Sdk/Yhd FixDownTrade', '', '', '30', '0', '0', '0', '', 'YHD', '', '一号店订单下载补漏', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('66', 'YHD002', '0', '一号店商品下载', 'yhd', 'sdk.php Sdk/Yhd DownloadAllGoods', '', '', '120', '0', '0', '0', '', 'YHD', '', '一号店商品下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('67', 'YHD003', '0', '一号店在售商品库存同步', 'yhd', 'sdk.php Sdk/Yhd StockSyncShops --approve_status=0 --distribution_status=0', '', '', '15', '0', '0', '0', '', 'YHD', '', '一号店在售商品库存同步', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('68', 'DD001', '0', '当当订单下载', 'dd', 'sdk.php Sdk/Dd DownloadAllOrders', '', '', '15', '0', '0', '0', '', 'DD', '', '当当订单下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('69', 'DD007', '0', '当当订单下载补漏', 'dd', 'sdk.php Sdk/Dd FixDownTrade', '', '', '30', '0', '0', '0', '', 'DD', '', '当当订单下载补漏', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('70', 'DD002', '0', '当当商品下载', 'dd', 'sdk.php Sdk/Dd DownloadAllGoods', '', '', '120', '0', '0', '0', '', 'DD', '', '当当商品下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('71', 'DD003', '0', '当当在售商品库存同步', 'dd', 'sdk.php Sdk/Dd StockSyncShops --approve_status=0 --distribution_status=0', '', '', '15', '0', '0', '0', '', 'DD', '', '当当在售商品库存同步', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('72', 'DD005', '0', '当当面单下载', 'dd', 'sdk.php Sdk/Dd DownloadAllPrints', '', '', '10', '0', '0', '0', '', 'DD', '', '当当面单下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('73', 'O104', '0', '未对接WMS订单自动发货', 'order', 'index.php Order/Order AutoOrderDelivery', '', '', '30', '0', '0', '0', '', '', '', '未对接WMS订单自动发货', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('74', 'O105', '0', '订单获取快递单号', 'order', 'index.php Order/Order ExpressMatch', '', '', '6', '2', '0', '0', '', '', '', '订单获取快递单号', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('75', 'O106', '0', '订单京配', 'order', 'index.php Order/Order ExpressJdCheck', '', '', '6', '2', '0', '0', '', '', '', '订单京配', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('76', 'H003', '0', '报表缓存维护', 'system', 'index.php Report/Report CheckDelData', '', '', '7200', '0', '0', '0', '', '', '', '报表缓存维护', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('77', 'H004', '0', '数据截转', 'system', 'index.php System/System TransferData', '', '', '43200', '0', '0', '0', '', '', '', '数据截转', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('78', 'H005', '0', '系统日志归档', 'system', 'index.php System/System ArchivingLog', '', '', '86400', '0', '0', '0', '', '', '', '系统日志归档,把 Cache/Log下的文件定期移动到指定的归档目录', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('79', 'H006', '0', '错误重置', 'system', 'index.php System/System ResetErrorTask', '', '', '720', '0', '0', '0', '', '', '', '错误重置,把转单/wms/回写的错误重置,以便定时器重新处理', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('80', 'W008', '0', 'WMS库存全量下载', 'wms', 'index.php Wms/Wms GetAllSkuStockInfo --processZeroStock=1', '', '', '43200', '0', '0', '0', '', '', '', 'WMS库存全量下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('81', 'W009', '0', 'WMS取订单发货状态', 'wms', 'index.php Wms/Wms GetWmsInfo', '', '', '36000', '0', '0', '0', '', '', '', 'WMS取订单发货状态', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('82', 'W010', '0', 'WMS缺货通知处理', 'wms', 'index.php Wms/Wms WmsLackProcess', '', '', '3600', '0', '0', '0', '', '', '', 'WMS缺货通知处理,自动在订单上打标签', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('83', 'CB002', '0', '跨境订单报关', 'cb', 'index.php Cb/Cb UploadOrder', '', '', '6', '3', '0', '0', '', '', '', '跨境订单报关', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('84', 'CB003', '0', '报关结果查询', 'cb', 'index.php Cb/Cb GetOrder', '', '', '6', '3', '0', '0', '', '', '', '报关结果查询', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('85', 'CB004', '0', '报关完成数据处理', 'cb', 'index.php Cb/Cb OrderProcess', '', '', '6', '3', '0', '0', '', '', '', '报关完成数据处理和平台反馈', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('86', 'CB009', '0', '跨境运单报关', 'cb', 'index.php Cb/Cb UploadExpressOrder', '', '', '6', '3', '0', '0', '', '', '', '跨境运单报关', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('87', 'CB010', '0', '深圳海关回执文件下载和处理', 'cb', 'index.php Cb/Cb SzResponse', '', '', '6', '0', '0', '0', '', '', '', '深圳海关回执文件下载和处理', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('88', 'CB011', '0', '三单报关反馈', 'cb', 'index.php Cb/Cb OrderUploadProcess', '', '', '6', '3', '0', '0', '', '', '', '三单报关反馈', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('89', 'CB012', '0', '跨境支付单报关', 'cb', 'index.php Cb/Cb UploadOrderPay', '', '', '6', '3', '0', '0', '', '', '', '跨境支付单报关', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('90', 'CB013', '0', '跨境支付单报关反馈', 'cb', 'index.php Cb/Cb OrderPayProcess', '', '', '6', '3', '0', '0', '', '', '', '跨境支付单报关反馈', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('91', 'CB014', '0', '跨境清单报关', 'cb', 'index.php Cb/Cb UploadOrderDetail', '', '', '6', '3', '0', '0', '', '', '', '跨境清单报关', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('92', 'CUS001', '0', '工单解锁业务执行', 'customer', 'index.php Customer/WorkOrder WKBusinessAll', '', '', '60', '0', '0', '0', '', '', '', '工单解锁业务执行', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('93', 'CUS002', '0', '异常工单业务执行', 'customer', 'index.php Customer/WorkOrder WKABusinessAll', '', '', '60', '0', '0', '0', '', '', '', '异常工单业务执行', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('94', 'J001', '0', '聚美订单下载', 'jumei', 'sdk.php Sdk/Jumei DownloadAllOrders', '', '', '15', '0', '0', '0', '', 'JUMEI', '', '聚美订单下载,下载时间段日志记录,日志目录Sdk/Cache/Opt/Jumei,文件名jumei_download_order_(shop_id)_data.csv', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('95', 'J002', '0', '聚美商品下载', 'jumei', 'sdk.php Sdk/Jumei DownloadAllGoods', '', '', '120', '0', '0', '0', '', 'JUMEI', '', '聚美商品下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('96', 'J003', '0', '聚美退单下载', 'jumei', 'sdk.php Sdk/Jumei DownloadAllRefunds', '', '', '6', '0', '0', '0', '', 'JUMEI', '', '聚美退单下载', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('97', 'J004', '0', '聚美商品库存同步', 'jumei', 'sdk.php Sdk/Jumei StockSyncShops', '', '', '10', '0', '0', '0', '', 'JUMEI', '', '聚美商品库存同步', '2018-11-02 18:25:42');
INSERT INTO `gwash_system_auto_task` VALUES ('240', 'Test001', '0', '测试接口', 'Test', 'index.php System/Test Test', '', '', '1', '0', '1', '1546049641', '', '', '', '测试接口', '2018-12-29 16:42:52');
INSERT INTO `gwash_system_auto_task` VALUES ('437', 'Test002', '0', '测试接口', 'Test', 'index.php System/Test Test', '', '', '60', '0', '0', '0', '', '', '', '测试接口', '2018-11-05 15:17:59');

-- ----------------------------
-- Table structure for `gwash_system_config`
-- ----------------------------
DROP TABLE IF EXISTS `gwash_system_config`;
CREATE TABLE `gwash_system_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_url` varchar(100) NOT NULL DEFAULT '' COMMENT '网站地址',
  `record_no` varchar(100) NOT NULL DEFAULT '' COMMENT '备案号',
  `site_name` varchar(100) NOT NULL DEFAULT '' COMMENT '网站名称',
  `site_logo` varchar(200) NOT NULL DEFAULT '' COMMENT '网站logo',
  `site_title` varchar(1024) NOT NULL DEFAULT '' COMMENT '标题',
  `site_desc` varchar(1024) NOT NULL DEFAULT '' COMMENT '描述',
  `site_key` varchar(1024) NOT NULL DEFAULT '' COMMENT '关键字',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='网站设置';

-- ----------------------------
-- Records of gwash_system_config
-- ----------------------------
INSERT INTO `gwash_system_config` VALUES ('2', 'http://admin.gwash.com', '粤00-123456号', 'thinkphp管理系统', 'Uploads/20181210/c60a798cc64f9f675b79402c9db09817.jpg', '华安华安华安111', 'thinkphp管理系统', 'thinkphp管理系统');

-- ----------------------------
-- Table structure for `gwash_system_config_dict`
-- ----------------------------
DROP TABLE IF EXISTS `gwash_system_config_dict`;
CREATE TABLE `gwash_system_config_dict` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '参数字典ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '参数字典名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '参数字典类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '参数字典标题',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '参数字典分组',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '参数字典值',
  `remark` varchar(100) NOT NULL COMMENT '参数字典说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '使用状态:0,禁用,1:启用',
  `value` text NOT NULL COMMENT '使用状态',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`) USING BTREE,
  KEY `type` (`type`) USING BTREE,
  KEY `group` (`group`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='参数字典表';

-- ----------------------------
-- Records of gwash_system_config_dict
-- ----------------------------

-- ----------------------------
-- Table structure for `gwash_system_menu`
-- ----------------------------
DROP TABLE IF EXISTS `gwash_system_menu`;
CREATE TABLE `gwash_system_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单名',
  `fid` int(11) NOT NULL COMMENT '父级菜单ID',
  `url` varchar(50) NOT NULL DEFAULT '' COMMENT '链接',
  `auth` tinyint(2) NOT NULL DEFAULT '0' COMMENT '访客权限',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态: 0.显示,1.隐藏',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '等级: 0.顶级菜单,1.一级菜单,2.菜单权限',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COMMENT='菜单管理';

-- ----------------------------
-- Records of gwash_system_menu
-- ----------------------------
INSERT INTO `gwash_system_menu` VALUES ('1', '欢迎页', '0', 'Index/welcome', '0', '9999', '0', '', '0');
INSERT INTO `gwash_system_menu` VALUES ('2', '首页', '0', 'Index/index', '0', '0', '1', '', '0');
INSERT INTO `gwash_system_menu` VALUES ('3', '系统配置', '0', '', '0', '999', '0', '', '0');
INSERT INTO `gwash_system_menu` VALUES ('4', '网站设置', '3', 'System/index', '0', '6', '0', '', '1');
INSERT INTO `gwash_system_menu` VALUES ('5', '保存设置', '4', 'System/save', '0', '1', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('6', '用户管理', '3', 'AdminUser/index', '0', '5', '0', '', '1');
INSERT INTO `gwash_system_menu` VALUES ('7', 'Ajax查询列表', '6', 'AdminUser/ajaxGetIndex', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('8', '添加', '6', 'AdminUser/add', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('9', '启用', '6', 'AdminUser/open', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('10', '禁用', '6', 'AdminUser/close', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('11', '删除', '6', 'AdminUser/del', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('12', '授权', '6', 'permission/group', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('13', '权限管理', '3', 'Permission/index', '0', '4', '0', '', '1');
INSERT INTO `gwash_system_menu` VALUES ('14', 'Ajax查询列表', '13', 'Permission/ajaxGetIndex', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('15', '添加', '13', 'Permission/add', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('16', '禁用', '13', 'Permission/close', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('17', '启用', '13', 'Permission/open', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('18', '编辑', '13', 'Permission/edit', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('19', '访问授权', '13', 'Permission/rule', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('20', '成员授权', '13', 'Permission/member', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('21', '删除成员', '13', 'Permission/delmember', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('22', '参数字典', '3', 'ConfigDict/index', '0', '2', '0', '', '1');
INSERT INTO `gwash_system_menu` VALUES ('23', '新增', '22', 'ConfigDict/add', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('24', '编辑', '22', 'ConfigDict/edit', '0', '2', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('25', '删除', '22', 'ConfigDict/del', '0', '3', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('26', '启用', '22', 'ConfigDict/open', '0', '4', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('27', '禁用', '22', 'ConfigDict/close', '0', '5', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('28', 'Ajax查询列表', '22', 'ConfigDict/ajaxGetIndex', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('29', '菜单管理', '3', 'Menu/index', '0', '3', '0', '', '1');
INSERT INTO `gwash_system_menu` VALUES ('30', '新增', '29', 'Menu/add', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('31', '编辑', '29', 'Menu/edit', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('32', '隐藏', '29', 'Menu/close', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('33', '显示', '29', 'Menu/open', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('34', '删除', '29', 'Menu/del', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('35', 'Ajax查询列表', '29', 'Menu/ajaxGetIndex', '0', '0', '0', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('36', '操作日志', '3', 'Log/index', '0', '1', '0', '', '1');
INSERT INTO `gwash_system_menu` VALUES ('37', 'Ajax查询Log列表', '36', 'Log/ajaxGetIndex', '0', '0', '11', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('38', '删除', '36', 'Log/del', '0', '0', '1', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('39', '详情', '36', 'Log/showDetail', '0', '0', '1', '', '2');
INSERT INTO `gwash_system_menu` VALUES ('40', '授权信息', '3', 'Index/ShowAuth', '0', '0', '0', '', '0');
INSERT INTO `gwash_system_menu` VALUES ('41', '版本信息', '3', 'Index/ShowAuth', '0', '0', '0', '', '0');
INSERT INTO `gwash_system_menu` VALUES ('42', '系统任务', '3', 'Task/index', '0', '3', '0', '', '0');
INSERT INTO `gwash_system_menu` VALUES ('43', 'Ajax查询列表', '42', 'Task/ajaxGetIndex', '0', '0', '0', '', '0');
