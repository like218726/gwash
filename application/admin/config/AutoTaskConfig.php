<?php
/**
 * 模块类型：订单(order)、WMS(wms)、财务(cash)、系统(system)、报表(report)、库存(stock)、淘宝(taobao)
 * 、京东(jingdong)
 * 如果数组中存在platform_code的key，说明定时器要以店铺为单位生成多个命令,否则不需要配置这个key
 */

$moduleType = array(
	''		=> '请选择',
	'system' => '系统',
	'stock'  => '库存',
	'order'  => '订单',
	'report' => '报表',
	'cash'   => '财务',
	'wms'    => 'WMS',
	'service' => '服务',
	'warehouse' => '仓库',
	'taobao' => '淘宝',
	'jingdong'=> '京东',
	'jdkj'    => '京东跨境',
	'youzan'=> '有赞',
	'smt'   =>  '速卖通',
	'eb'    => 'ebay',
	'ymx'   => '亚马逊',
	'yhd'   => '一号店',
	'cb'    => '跨境',
	'customer'=>'客服',
	'jumei'=>'聚美优品',
	'dd'   => '当当',
	'yg'   => '优购',
	'Test' => '测试'
	
);

$services = array();


/*********************接口定时器***********************/
//淘宝接口定时器
$serviceEach = array();
$serviceEach['code'] = 'T001';
$serviceEach['plat_code'] = 'TB';
$serviceEach['type'] = 'taobao';
$serviceEach['name'] = '淘宝订单下载';
$serviceEach['desc'] = '淘宝订单下载,下载时间段日志记录,日志目录Sdk/Cache/Opt/Tb,文件名tb_download_order_(shop_id)_data.csv';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Tb DownloadAllOrders';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'T002';
$serviceEach['plat_code'] = 'TB';
$serviceEach['type'] = 'taobao';
$serviceEach['name'] = '淘宝商品下载';
$serviceEach['desc'] = '淘宝商品下载';
$serviceEach['time'] = '120';
$serviceEach['exec'] = 'sdk.php Sdk/Tb DownloadAllGoods';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'T003';
$serviceEach['plat_code'] = 'TB';
$serviceEach['type'] = 'taobao';
$serviceEach['name'] = '淘宝退单下载';
$serviceEach['desc'] = '淘宝退单下载';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'sdk.php Sdk/Tb DownloadAllRefunds';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'T004';
$serviceEach['plat_code'] = 'TB';
$serviceEach['type'] = 'taobao';
$serviceEach['name'] = '淘宝在售商品库存同步';
$serviceEach['desc'] = '淘宝在售商品库存同步';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Tb StockSyncShops --approve_status=0 --distribution_status=0';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'T005';
$serviceEach['plat_code'] = 'TB';
$serviceEach['type'] = 'taobao';
$serviceEach['name'] = '淘宝在库商品库存同步';
$serviceEach['desc'] = '淘宝在库商品库存同步';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Tb StockSyncShops --approve_status=1 --distribution_status=0';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'T006';
$serviceEach['plat_code'] = 'TB';
$serviceEach['type'] = 'taobao';
$serviceEach['name'] = '淘分销在库商品库存同步';
$serviceEach['desc'] = '淘分销在库商品库存同步';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Tb StockSyncShops --approve_status=0 --distribution_status=1';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'T007';
$serviceEach['plat_code'] = 'TB';
$serviceEach['type'] = 'taobao';
$serviceEach['name'] = '淘分销在库商品库存同步';
$serviceEach['desc'] = '淘分销在库商品库存同步';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Tb StockSyncShops --approve_status=1 --distribution_status=1';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'T008';
$serviceEach['plat_code'] = 'TB';
$serviceEach['type'] = 'taobao';
$serviceEach['name'] = '淘宝订单下载补漏';
$serviceEach['desc'] = '淘宝订单下载补漏';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Tb FixDownTrade';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'T009';
$serviceEach['plat_code'] = 'TB';
$serviceEach['type'] = 'taobao';
$serviceEach['name'] = '淘宝退单下载补漏';
$serviceEach['desc'] = '淘宝退单下载补漏';
$serviceEach['time'] = '30';
$serviceEach['exec'] = 'sdk.php Sdk/Tb FixDownRefund';
$services[] = $serviceEach;

//--------------------------------京东接口定时器----------------------------------
$serviceEach = array();
$serviceEach['code'] = 'JD001';
$serviceEach['plat_code'] = 'JD';
$serviceEach['type'] = 'jingdong';
$serviceEach['name'] = '京东订单下载';
$serviceEach['desc'] = '京东订单下载';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Jd DownloadAllOrders';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'JD007';
$serviceEach['plat_code'] = 'JD';
$serviceEach['type'] = 'jingdong';
$serviceEach['name'] = '京东订单下载补漏';
$serviceEach['desc'] = '京东订单下载补漏';
$serviceEach['time'] = '30';
$serviceEach['exec'] = 'sdk.php Sdk/Jd FixDownTrade';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'JD005';
$serviceEach['plat_code'] = 'JD';
$serviceEach['type'] = 'jingdong';
$serviceEach['name'] = '京东面单下载';
$serviceEach['desc'] = '京东面单下载';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Jd DownloadAllPrints';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'JD006';
$serviceEach['plat_code'] = 'JD';
$serviceEach['type'] = 'jingdong';
$serviceEach['name'] = '京东退单下载';
$serviceEach['desc'] = '京东退单下载';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Jd DownloadAllRefunds';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'JD008';
$serviceEach['plat_code'] = 'JD';
$serviceEach['type'] = 'jingdong';
$serviceEach['name'] = '京东退单下载补漏';
$serviceEach['desc'] = '京东退单下载补漏';
$serviceEach['time'] = '20';
$serviceEach['exec'] = 'sdk.php Sdk/Jd FixDownRefund';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'JD002';
$serviceEach['plat_code'] = 'JD';
$serviceEach['type'] = 'jingdong';
$serviceEach['name'] = '京东商品下载';
$serviceEach['desc'] = '京东商品下载';
$serviceEach['time'] = '120';
$serviceEach['exec'] = 'sdk.php Sdk/Jd DownloadAllGoods';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'JD003';
$serviceEach['plat_code'] = 'JD';
$serviceEach['type'] = 'jingdong';
$serviceEach['name'] = '京东在售商品库存同步';
$serviceEach['desc'] = '京东在售商品库存同步';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Jd StockSyncShops --approve_status=0 --distribution_status=0';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'JD004';
$serviceEach['plat_code'] = 'JD';
$serviceEach['type'] = 'jingdong';
$serviceEach['name'] = '京东待售商品库存同步';
$serviceEach['desc'] = '京东待售商品库存同步';
$serviceEach['time'] = '60';
$serviceEach['exec'] = 'sdk.php Sdk/Jd StockSyncShops --approve_status=1 --distribution_status=0';
$services[] = $serviceEach;



//--------------------------------京东跨境接口定时器----------------------------------
$serviceEach = array();
$serviceEach['code'] = 'JDKJ001';
$serviceEach['plat_code'] = 'JDKJ';
$serviceEach['type'] = 'jdkj';
$serviceEach['name'] = '京东跨境订单下载';
$serviceEach['desc'] = '京东跨境订单下载';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Jdkj DownloadAllOrders';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'JDKJ002';
$serviceEach['plat_code'] = 'JDKJ';
$serviceEach['type'] = 'jdkj';
$serviceEach['name'] = '京东跨境快递单号下载';
$serviceEach['desc'] = '京东跨境快递单号下载';
$serviceEach['time'] = '20';
$serviceEach['exec'] = 'sdk.php Sdk/Jdkj DownloadExpressNos';
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'JDKJ003';
$serviceEach['plat_code'] = 'JDKJ';
$serviceEach['type'] = 'jdkj';
$serviceEach['name'] = '京东跨境订单下载补漏';
$serviceEach['desc'] = '京东跨境订单下载补漏';
$serviceEach['time'] = '30';
$serviceEach['exec'] = 'sdk.php Sdk/Jdkj FixDownTrade';
$services[] = $serviceEach;


//--------------------------------速卖通接口定时器----------------------------------
$serviceEach = array();
$serviceEach['code'] = 'SMT001';
$serviceEach['plat_code'] = 'SMT';
$serviceEach['type'] = 'smt';
$serviceEach['name'] = '速卖通订单下载';
$serviceEach['desc'] = '速卖通订单下载';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Smt DownloadAllOrders';
$services[] = $serviceEach;


//--------------------------------ebay接口定时器----------------------------------
$serviceEach = array();
$serviceEach['code'] = 'EB001';
$serviceEach['plat_code'] = 'EB';
$serviceEach['type'] = 'eb';
$serviceEach['name'] = 'EBAY订单下载';
$serviceEach['desc'] = 'EBAY订单下载';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Eb DownloadAllOrders';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'EB002';
$serviceEach['plat_code'] = 'EB';
$serviceEach['type'] = 'eb';
$serviceEach['name'] = 'EBAY商品下载';
$serviceEach['desc'] = 'EBAY商品下载';
$serviceEach['time'] = '120';
$serviceEach['exec'] = 'sdk.php Sdk/Eb DownloadAllGoods';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'EB003';
$serviceEach['plat_code'] = 'EB';
$serviceEach['type'] = 'eb';
$serviceEach['name'] = 'EBAY争议下载';
$serviceEach['desc'] = 'EBAY争议下载';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Eb DownloadAllDisputes';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'EB004';
$serviceEach['plat_code'] = 'EB';
$serviceEach['type'] = 'eb';
$serviceEach['name'] = 'EBAY发起未付款争议';
$serviceEach['desc'] = 'EBAY发起未付款争议';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Eb AddDispute';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'EB005';
$serviceEach['plat_code'] = 'EB';
$serviceEach['type'] = 'eb';
$serviceEach['name'] = 'EBAY关闭超时争议';
$serviceEach['desc'] = 'EBAY关闭超时争议';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Eb CloseDispute';
$services[] = $serviceEach;
//--------------------------------有赞接口定时器----------------------------------
$serviceEach = array();
$serviceEach['code'] = 'YZ001';
$serviceEach['plat_code'] = 'YZ';
$serviceEach['type'] = 'youzan';
$serviceEach['name'] = '有赞订单下载';
$serviceEach['desc'] = '有赞订单下载';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Yz DownloadAllOrders';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'YZ002';
$serviceEach['plat_code'] = 'YZ';
$serviceEach['type'] = 'youzan';
$serviceEach['name'] = '有赞商品下载';
$serviceEach['desc'] = '有赞商品下载';
$serviceEach['time'] = '120';
$serviceEach['exec'] = 'sdk.php Sdk/Yz DownloadAllGoods';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'YZ003';
$serviceEach['plat_code'] = 'YZ';
$serviceEach['type'] = 'youzan';
$serviceEach['name'] = '有赞在售商品库存同步';
$serviceEach['desc'] = '有赞在售商品库存同步';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Yz StockSyncShops --approve_status=0 --distribution_status=0';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'YZ004';
$serviceEach['plat_code'] = 'YZ';
$serviceEach['type'] = 'youzan';
$serviceEach['name'] = '有赞在库商品库存同步';
$serviceEach['desc'] = '有赞在库商品库存同步';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Yz StockSyncShops --approve_status=1 --distribution_status=0';
$services[] = $serviceEach;

//--------------------------------优购接口定时器----------------------------------
$serviceEach = array();
$serviceEach['code'] = 'YG001';
$serviceEach['plat_code'] = 'YG';
$serviceEach['type'] = 'yg';
$serviceEach['name'] = '优购订单下载';
$serviceEach['desc'] = '优购订单下载';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Yg DownloadAllOrders';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'YG002';
$serviceEach['plat_code'] = 'YG';
$serviceEach['type'] = 'yg';
$serviceEach['name'] = '优购商品下载';
$serviceEach['desc'] = '优购商品下载';
$serviceEach['time'] = '120';
$serviceEach['exec'] = 'sdk.php Sdk/Yg DownloadAllGoods';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'YG003';
$serviceEach['plat_code'] = 'YG';
$serviceEach['type'] = 'yg';
$serviceEach['name'] = '优购在售商品库存同步';
$serviceEach['desc'] = '优购在售商品库存同步';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Yg StockSyncShops --approve_status=0 --distribution_status=0';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'YG004';
$serviceEach['plat_code'] = 'YG';
$serviceEach['type'] = 'yg';
$serviceEach['name'] = '优购在库商品库存同步';
$serviceEach['desc'] = '优购在库商品库存同步';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Yg StockSyncShops --approve_status=1 --distribution_status=0';
$services[] = $serviceEach;

//--------------------------------亚马逊接口定时器----------------------------------
$serviceEach = array();
$serviceEach['code'] = 'YMX001';
$serviceEach['plat_code'] = 'YMX';
$serviceEach['type'] = 'ymx';
$serviceEach['name'] = '亚马逊订单下载';
$serviceEach['desc'] = '亚马逊订单下载';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Ymx DownloadAllOrders';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'YMX002';
$serviceEach['plat_code'] = 'YMX';
$serviceEach['type'] = 'ymx';
$serviceEach['name'] = '亚马逊订单下载补漏';
$serviceEach['desc'] = '亚马逊订单下载补漏';
$serviceEach['time'] = '30';
$serviceEach['exec'] = 'sdk.php Sdk/Ymx FixDownTrade';
$services[] = $serviceEach;

/*
$serviceEach = array();
$serviceEach['code'] = 'YMX004';
$serviceEach['plat_code'] = 'YMX';
$serviceEach['type'] = 'ymx';
$serviceEach['name'] = '亚马逊退单下载';
$serviceEach['desc'] = '亚马逊退单下载';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Ymx DownloadAllRefunds';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'YMX005';
$serviceEach['plat_code'] = 'YMX';
$serviceEach['type'] = 'ymx';
$serviceEach['name'] = '亚马逊退单下载补漏';
$serviceEach['desc'] = '亚马逊退单下载补漏';
$serviceEach['time'] = '20';
$serviceEach['exec'] = 'sdk.php Sdk/Ymx FixDownRefund';
$services[] = $serviceEach;
*/
$serviceEach = array();
$serviceEach['code'] = 'YMX006';
$serviceEach['plat_code'] = 'YMX';
$serviceEach['type'] = 'ymx';
$serviceEach['name'] = '亚马逊商品下载';
$serviceEach['desc'] = '亚马逊商品下载';
$serviceEach['time'] = '120';
$serviceEach['exec'] = 'sdk.php Sdk/Ymx DownloadAllGoods';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'YMX007';
$serviceEach['plat_code'] = 'YMX';
$serviceEach['type'] = 'ymx';
$serviceEach['name'] = '亚马逊商品库存同步';
$serviceEach['desc'] = '亚马逊商品库存同步';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Ymx StockSyncShops --approve_status=0 --distribution_status=0';
$services[] = $serviceEach;



//--------------------------------一号店接口定时器----------------------------------
$serviceEach = array();
$serviceEach['code'] = 'YHD001';
$serviceEach['plat_code'] = 'YHD';
$serviceEach['type'] = 'yhd';
$serviceEach['name'] = '一号店订单下载';
$serviceEach['desc'] = '一号店订单下载';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Yhd DownloadAllOrders';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'YHD007';
$serviceEach['plat_code'] = 'YHD';
$serviceEach['type'] = 'yhd';
$serviceEach['name'] = '一号店订单下载补漏';
$serviceEach['desc'] = '一号店订单下载补漏';
$serviceEach['time'] = '30';
$serviceEach['exec'] = 'sdk.php Sdk/Yhd FixDownTrade';
$services[] = $serviceEach;

/*
$serviceEach = array();
$serviceEach['code'] = 'YHD006';
$serviceEach['plat_code'] = 'YHD';
$serviceEach['type'] = 'yhd';
$serviceEach['name'] = '一号店退单下载';
$serviceEach['desc'] = '一号店退单下载';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Yhd DownloadAllRefunds';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'YHD008';
$serviceEach['plat_code'] = 'YHD';
$serviceEach['type'] = 'yhd';
$serviceEach['name'] = '一号店退单下载补漏';
$serviceEach['desc'] = '一号店退单下载补漏';
$serviceEach['time'] = '20';
$serviceEach['exec'] = 'sdk.php Sdk/Yhd FixDownRefund';
$services[] = $serviceEach;
*/

$serviceEach = array();
$serviceEach['code'] = 'YHD002';
$serviceEach['plat_code'] = 'YHD';
$serviceEach['type'] = 'yhd';
$serviceEach['name'] = '一号店商品下载';
$serviceEach['desc'] = '一号店商品下载';
$serviceEach['time'] = '120';
$serviceEach['exec'] = 'sdk.php Sdk/Yhd DownloadAllGoods';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'YHD003';
$serviceEach['plat_code'] = 'YHD';
$serviceEach['type'] = 'yhd';
$serviceEach['name'] = '一号店在售商品库存同步';
$serviceEach['desc'] = '一号店在售商品库存同步';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Yhd StockSyncShops --approve_status=0 --distribution_status=0';
$services[] = $serviceEach;

//--------------------------------当当接口定时器----------------------------------
$serviceEach = array();
$serviceEach['code'] = 'DD001';
$serviceEach['plat_code'] = 'DD';
$serviceEach['type'] = 'dd';
$serviceEach['name'] = '当当订单下载';
$serviceEach['desc'] = '当当订单下载';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Dd DownloadAllOrders';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'DD007';
$serviceEach['plat_code'] = 'DD';
$serviceEach['type'] = 'dd';
$serviceEach['name'] = '当当订单下载补漏';
$serviceEach['desc'] = '当当订单下载补漏';
$serviceEach['time'] = '30';
$serviceEach['exec'] = 'sdk.php Sdk/Dd FixDownTrade';
$services[] = $serviceEach;

/*
$serviceEach = array();
$serviceEach['code'] = 'DD006';
$serviceEach['plat_code'] = 'DD';
$serviceEach['type'] = 'dd';
$serviceEach['name'] = '当当退单下载';
$serviceEach['desc'] = '当当退单下载';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Dd DownloadAllRefunds';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'DD008';
$serviceEach['plat_code'] = 'DD';
$serviceEach['type'] = 'dd';
$serviceEach['name'] = '当当退单下载补漏';
$serviceEach['desc'] = '当当退单下载补漏';
$serviceEach['time'] = '20';
$serviceEach['exec'] = 'sdk.php Sdk/Dd FixDownRefund';
$services[] = $serviceEach;
*/

$serviceEach = array();
$serviceEach['code'] = 'DD002';
$serviceEach['plat_code'] = 'DD';
$serviceEach['type'] = 'dd';
$serviceEach['name'] = '当当商品下载';
$serviceEach['desc'] = '当当商品下载';
$serviceEach['time'] = '120';
$serviceEach['exec'] = 'sdk.php Sdk/Dd DownloadAllGoods';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'DD003';
$serviceEach['plat_code'] = 'DD';
$serviceEach['type'] = 'dd';
$serviceEach['name'] = '当当在售商品库存同步';
$serviceEach['desc'] = '当当在售商品库存同步';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Dd StockSyncShops --approve_status=0 --distribution_status=0';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'DD005';
$serviceEach['plat_code'] = 'DD';
$serviceEach['type'] = 'dd';
$serviceEach['name'] = '当当面单下载';
$serviceEach['desc'] = '当当面单下载';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Dd DownloadAllPrints';
$services[] = $serviceEach;


/*********************订单定时器***********************/
$serviceEach = array();
$serviceEach['code'] = 'O001';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '订单预处理';
$serviceEach['desc'] = '订单预处理';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Order/TransOrder Trans';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'O002';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '订单适配';
$serviceEach['desc'] = '订单适配';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Order/TransOrder Allocation';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'O003';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '订单审核';
$serviceEach['desc'] = '订单审核';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Order/Order OrderCheck';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'O004';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '订单解除缺货';
$serviceEach['desc'] = '订单解除缺货';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'index.php Order/Order OrderRemoveOutOfStock';
$serviceEach['process'] = '1'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'O005';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '订单合并';
$serviceEach['desc'] = '订单合并';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Order/Order OrderCombine';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'O006';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '订单拆分';
$serviceEach['desc'] = '订单拆分';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Order/Order OrderSplit';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'O007';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '订单通知配货';
$serviceEach['desc'] = '订单通知配货';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Order/Order OrderNotice';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'O008';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '更新订单商家备注';
$serviceEach['desc'] = '更新订单商家备注';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'index.php Order/Order OrderUpdateSellerMemo';
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'O009';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '订单发货回写';
$serviceEach['desc'] = '订单发货回写';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Order/Order OrderShipping';
$serviceEach['process'] = '2'; //进程数
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'O102';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '缺货订单自动转仓';
$serviceEach['desc'] = '缺货订单自动转到有库存的仓库';
$serviceEach['time'] = '20';
$serviceEach['exec'] = 'index.php Order/Order OutStockOrderChangeWarehouse';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'O103';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '平台退单导入';
$serviceEach['desc'] = '平台退单导入';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'index.php Order/TransRefund Trans';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'O104';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '未对接WMS订单自动发货';
$serviceEach['desc'] = '未对接WMS订单自动发货';
$serviceEach['time'] = '30';
$serviceEach['exec'] = 'index.php Order/Order AutoOrderDelivery';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'O105';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '订单获取快递单号';
$serviceEach['desc'] = '订单获取快递单号';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Order/Order ExpressMatch';
$serviceEach['process'] = '2'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'O106';
$serviceEach['type'] = 'order';
$serviceEach['name'] = '订单京配';
$serviceEach['desc'] = '订单京配';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Order/Order ExpressJdCheck';
$serviceEach['process'] = '2'; //进程数
$services[] = $serviceEach;


/*********************系统定时器***********************/
$serviceEach = array();
$serviceEach['code'] = 'H001';
$serviceEach['type'] = 'system';
$serviceEach['name'] = '清道夫';
$serviceEach['desc'] = '库存事务转移到文件,库存事务文件路径Cache/Opt/Stock, 文件名：stock_transaction_log.csv';
$serviceEach['time'] = '1440';
$serviceEach['exec'] = 'index.php System/System Clear';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'H002';
$serviceEach['type'] = 'system';
$serviceEach['name'] = '御城河日志上传';
$serviceEach['desc'] = '御城河日志上传';
$serviceEach['time'] = '1440';
$serviceEach['exec'] = 'sdk.php Sdk/Aliyun BatchUploadAliyunLog';
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'H003';
$serviceEach['type'] = 'system';
$serviceEach['name'] = '报表缓存维护';
$serviceEach['desc'] = '报表缓存维护';
$serviceEach['time'] = '7200';
$serviceEach['exec'] = 'index.php Report/Report CheckDelData';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'H004';
$serviceEach['type'] = 'system';
$serviceEach['name'] = '数据截转';
$serviceEach['desc'] = '数据截转';
$serviceEach['time'] = '43200';
$serviceEach['exec'] = 'index.php System/System TransferData';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'H005';
$serviceEach['type'] = 'system';
$serviceEach['name'] = '系统日志归档';
$serviceEach['desc'] = '系统日志归档,把 Cache/Log下的文件定期移动到指定的归档目录';
$serviceEach['time'] = '86400';
$serviceEach['exec'] = 'index.php System/System ArchivingLog';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'H006';
$serviceEach['type'] = 'system';
$serviceEach['name'] = '错误重置';
$serviceEach['desc'] = '错误重置,把转单/wms/回写的错误重置,以便定时器重新处理';
$serviceEach['time'] = '720';
$serviceEach['exec'] = 'index.php System/System ResetErrorTask';
$services[] = $serviceEach;
/*********************库存定时器***********************/
$serviceEach = array();
$serviceEach['code'] = 'S001';
$serviceEach['type'] = 'stock';
$serviceEach['name'] = '库存事务转移到文件';
$serviceEach['desc'] = '库存事务转移到文件,库存事务文件路径Cache/Opt/Stock, 文件名：stock_transaction_log.csv';
$serviceEach['time'] = '60';
$serviceEach['exec'] = 'index.php Stock/Stock MoveTransactionLog';
$services[] = $serviceEach;


/*********************财务定时器***********************/



/*********************报表定时器***********************/


/*********************WMS定时器***********************/
$serviceEach = array();
$serviceEach['code'] = 'W001';
$serviceEach['type'] = 'wms';
$serviceEach['name'] = 'WMS上传档案';
$serviceEach['desc'] = 'WMS上传档案';
$serviceEach['time'] = '3600';
$serviceEach['exec'] = 'index.php Wms/Wms UploadArchive';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'W002';
$serviceEach['type'] = 'wms';
$serviceEach['name'] = 'WMS上传订单';
$serviceEach['desc'] = 'WMS上传订单';
$serviceEach['time'] = '300';
$serviceEach['exec'] = 'index.php Wms/Wms UploadOrder';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'W003';
$serviceEach['type'] = 'wms';
$serviceEach['name'] = 'WMS取消订单';
$serviceEach['desc'] = 'WMS取消订单';
$serviceEach['time'] = '300';
$serviceEach['exec'] = 'index.php Wms/Wms CancelOrder';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'W004';
$serviceEach['type'] = 'wms';
$serviceEach['name'] = 'WMS更新OMS库存';
$serviceEach['desc'] = 'WMS更新OMS库存';
$serviceEach['time'] = '300';
$serviceEach['exec'] = 'index.php Wms/Wms StockProcess';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'W005';
$serviceEach['type'] = 'wms';
$serviceEach['name'] = 'WMS收发货状态同步';
$serviceEach['desc'] = 'WMS收发货状态同步';
$serviceEach['time'] = '1200';
$serviceEach['exec'] = 'index.php Wms/Wms Shipping';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'W006';
$serviceEach['type'] = 'wms';
$serviceEach['name'] = 'WMS快递修改同步';
$serviceEach['desc'] = 'WMS快递修改同步';
$serviceEach['time'] = '1200';
$serviceEach['exec'] = 'index.php Wms/Wms ExpressChanges';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'W007';
$serviceEach['type'] = 'wms';
$serviceEach['name'] = 'WMS零库存更新OMS库存';
$serviceEach['desc'] = 'WMS零库存更新OMS库存';
$serviceEach['time'] = '3600';
$serviceEach['exec'] = 'index.php Wms/Wms StockZeroProcess';
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'W008';
$serviceEach['type'] = 'wms';
$serviceEach['name'] = 'WMS库存全量下载';
$serviceEach['desc'] = 'WMS库存全量下载';
$serviceEach['time'] = '43200';
$serviceEach['exec'] = 'index.php Wms/Wms GetAllSkuStockInfo --processZeroStock=1';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'W009';
$serviceEach['type'] = 'wms';
$serviceEach['name'] = 'WMS取订单发货状态';
$serviceEach['desc'] = 'WMS取订单发货状态';
$serviceEach['time'] = '36000';
$serviceEach['exec'] = 'index.php Wms/Wms GetWmsInfo';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'W010';
$serviceEach['type'] = 'wms';
$serviceEach['name'] = 'WMS缺货通知处理';
$serviceEach['desc'] = 'WMS缺货通知处理,自动在订单上打标签';
$serviceEach['time'] = '3600';
$serviceEach['exec'] = 'index.php Wms/Wms WmsLackProcess';
$services[] = $serviceEach;

/*********************跨境定时器***********************/
/*
$serviceEach = array();
$serviceEach['code'] = 'CB001';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '跨境商品档案上传';
$serviceEach['desc'] = '跨境商品档案上传';
$serviceEach['time'] = '3600';
$serviceEach['exec'] = 'index.php Cb/Cb UploadArchive';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;
*/

$serviceEach = array();
$serviceEach['code'] = 'CB002';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '跨境订单报关';
$serviceEach['desc'] = '跨境订单报关';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb UploadOrder';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'CB003';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '报关结果查询';
$serviceEach['desc'] = '报关结果查询';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb GetOrder';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'CB004';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '报关完成数据处理';
$serviceEach['desc'] = '报关完成数据处理和平台反馈';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb OrderProcess';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;

/*
$serviceEach = array();
$serviceEach['code'] = 'CB005';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '销售订单推送到跨境中间表';
$serviceEach['desc'] = '销售订单推送到跨境中间表';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/CbMiddlEware CbMiddlEware';
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'CB006';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '顺丰快递单号匹配';
$serviceEach['desc'] = '顺丰快递单号匹配';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb ExpressMatch --express_code=SF';
$serviceEach['process'] = '2'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'CB007';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '京东快递单号匹配';
$serviceEach['desc'] = '京东快递单号匹配';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb ExpressMatch --express_code=JD';
$serviceEach['process'] = '2'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'CB008';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '天天快递单号匹配';
$serviceEach['desc'] = '天天快递单号匹配';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb ExpressMatch --express_code=TTKDEX';
$serviceEach['process'] = '2'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'CB0081';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '中通快递单号匹配';
$serviceEach['desc'] = '中通快递单号匹配';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb ExpressMatch --express_code=ZTO';
$serviceEach['process'] = '2'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'CB0082';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '韵达快递单号匹配';
$serviceEach['desc'] = '韵达快递单号匹配';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb ExpressMatch --express_code=YUNDA';
$serviceEach['process'] = '2'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'CB0083';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = 'EMS快递单号匹配';
$serviceEach['desc'] = 'EMS快递单号匹配';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb ExpressMatch --express_code=EMS';
$serviceEach['process'] = '2'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'CB0084';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '圆通快递单号匹配';
$serviceEach['desc'] = '圆通快递单号匹配';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb ExpressMatch --express_code=YTO';
$serviceEach['process'] = '2'; //进程数
$services[] = $serviceEach;
*/

$serviceEach = array();
$serviceEach['code'] = 'CB009';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '跨境运单报关';
$serviceEach['desc'] = '跨境运单报关';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb UploadExpressOrder';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'CB010';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '深圳海关回执文件下载和处理';
$serviceEach['desc'] = '深圳海关回执文件下载和处理';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb SzResponse';
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'CB011';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '三单报关反馈';
$serviceEach['desc'] = '三单报关反馈';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb OrderUploadProcess';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'CB012';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '跨境支付单报关';
$serviceEach['desc'] = '跨境支付单报关';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb UploadOrderPay';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'CB013';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '跨境支付单报关反馈';
$serviceEach['desc'] = '跨境支付单报关反馈';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb OrderPayProcess';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'CB014';
$serviceEach['type'] = 'cb';
$serviceEach['name'] = '跨境清单报关';
$serviceEach['desc'] = '跨境清单报关';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'index.php Cb/Cb UploadOrderDetail';
$serviceEach['process'] = '3'; //进程数
$services[] = $serviceEach;


/*********************短信定时器***********************/
$serviceEach = array();
$serviceEach['code'] = 'FS001';
$serviceEach['type'] = 'service';
$serviceEach['name'] = '短信发送数据获取';
$serviceEach['desc'] = '短信发送数据获取';
$serviceEach['time'] = '60';
$serviceEach['exec'] = 'index.php Service/SendMessage GetTimerSendOrder';
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'FS002';
$serviceEach['type'] = 'service';
$serviceEach['name'] = '短信发送';
$serviceEach['desc'] = '短信发送';
$serviceEach['time'] = '60';
$serviceEach['exec'] = 'index.php Service/SendMessage TimerSendMessages';
$services[] = $serviceEach;

/*********************有主件与无主件定时器***********************/
$serviceEach = array();
$serviceEach['code'] = 'ZJ001';
$serviceEach['type'] = 'warehouse';
$serviceEach['name'] = '有主件定时审核';
$serviceEach['desc'] = '有主件定时审核';
$serviceEach['time'] = '60';
$serviceEach['exec'] = 'index.php Warehouse/TheMainThing TimeAudit';
$services[] = $serviceEach;


$serviceEach = array();
$serviceEach['code'] = 'ZJ002';
$serviceEach['type'] = 'warehouse';
$serviceEach['name'] = '无主件定时审核';
$serviceEach['desc'] = '无主件定时审核';
$serviceEach['time'] = '60';
$serviceEach['exec'] = 'index.php Warehouse/TheMainThing TimeNoAudit';
$services[] = $serviceEach;

/*客服工单*/
$serviceEach = array();
$serviceEach['code'] = 'CUS001';
$serviceEach['type'] = 'customer';
$serviceEach['name'] = '工单解锁业务执行';
$serviceEach['desc'] = '工单解锁业务执行';
$serviceEach['time'] = '60';
$serviceEach['exec'] = 'index.php Customer/WorkOrder WKBusinessAll';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'CUS002';
$serviceEach['type'] = 'customer';
$serviceEach['name'] = '异常工单业务执行';
$serviceEach['desc'] = '异常工单业务执行';
$serviceEach['time'] = '60';
$serviceEach['exec'] = 'index.php Customer/WorkOrder WKABusinessAll';
$services[] = $serviceEach;

//聚美接口定时器
$serviceEach = array();
$serviceEach['code'] = 'J001';
$serviceEach['plat_code'] = 'JUMEI';
$serviceEach['type'] = 'jumei';
$serviceEach['name'] = '聚美订单下载';
$serviceEach['desc'] = '聚美订单下载,下载时间段日志记录,日志目录Sdk/Cache/Opt/Jumei,文件名jumei_download_order_(shop_id)_data.csv';
$serviceEach['time'] = '15';
$serviceEach['exec'] = 'sdk.php Sdk/Jumei DownloadAllOrders';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'J002';
$serviceEach['plat_code'] = 'JUMEI';
$serviceEach['type'] = 'jumei';
$serviceEach['name'] = '聚美商品下载';
$serviceEach['desc'] = '聚美商品下载';
$serviceEach['time'] = '120';
$serviceEach['exec'] = 'sdk.php Sdk/Jumei DownloadAllGoods';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'J003';
$serviceEach['plat_code'] = 'JUMEI';
$serviceEach['type'] = 'jumei';
$serviceEach['name'] = '聚美退单下载';
$serviceEach['desc'] = '聚美退单下载';
$serviceEach['time'] = '6';
$serviceEach['exec'] = 'sdk.php Sdk/Jumei DownloadAllRefunds';
$services[] = $serviceEach;

$serviceEach = array();
$serviceEach['code'] = 'J004';
$serviceEach['plat_code'] = 'JUMEI';
$serviceEach['type'] = 'jumei';
$serviceEach['name'] = '聚美商品库存同步';
$serviceEach['desc'] = '聚美商品库存同步';
$serviceEach['time'] = '10';
$serviceEach['exec'] = 'sdk.php Sdk/Jumei StockSyncShops';
$services[] = $serviceEach;

//测试
$serviceEach = array();
$serviceEach['code'] = 'Test001';
$serviceEach['type'] = 'Test';
$serviceEach['name'] = '测试接口';
$serviceEach['desc'] = '测试接口';
$serviceEach['time'] = '60';
$serviceEach['exec'] = 'index.php admin/Test/Test';
$services[] = $serviceEach;
$serviceEach = array();
$serviceEach['code'] = 'Test002';
$serviceEach['type'] = 'Test';
$serviceEach['name'] = '测试接口';
$serviceEach['desc'] = '测试接口';
$serviceEach['time'] = '60';
$serviceEach['exec'] = 'index.php System/Test Test';
$services[] = $serviceEach;