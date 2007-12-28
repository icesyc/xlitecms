<?php
/**
 * DefaultConfig
 *
 * 应用程序的配置文件
 *
 * @package    Config
 * @author     ice_berg16(寻梦的稻草人)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */

//默认应用程序各文件夹路径设置
define("APP_ROOT", dirname(__FILE__));

define("APP_NAME", "Xlite");

define("XLITE_VERSION", '1.2.0');

//xlite的安装路径
define("XLITE_ROOT", "");

//一些其它路径设置
define("ARTICLE_PATH", APP_ROOT . "/html");
define("XLITE_DB", APP_ROOT . "/db");
define("XLITE_SYS_TPL", APP_ROOT . "/tpl/system");
define("XLITE_APP_TPL", APP_ROOT . "/tpl/default");
define("MAX_EXEC_TIME", 5); //最大页面执行时间
define("FTP_MAX_TIME", 10); //FTP操作时最大的执行时间
define("PAGE_SIZE", 20);	//每页显示的记录数

//采集器下载资源的保存目录
define("SCRATCHER_RESOURCE_PATH", APP_ROOT . "/UserFiles");
//资源的虚拟访问路径 从网站根目录开始计算
define("RESOURCE_VIRTUAL_PATH", XLITE_ROOT . "/UserFiles");
//采集器下载的列表保存位置
define("SCRATCHER_URL_PATH", APP_ROOT . "/scratcher");

//文章的虚拟路径
define("ARTICLE_VIRTUAL_PATH", XLITE_ROOT . "/html");
//图片集的路径
define("GALLERY_PATH", APP_ROOT . "/gallery");
define("GALLERY_VIRTUAL_PATH", XLITE_ROOT . "/gallery");

//生成的缩略图的大小
define("THUMBNAIL_WIDTH", 120);
define("THUMBNAIL_HEIGHT", 100);

return array(

	//一些基本的路径设置
	'appRoot'			=> APP_ROOT,
	'controllerPath'	=> APP_ROOT . DS . "controller",
	"modelPath"			=> APP_ROOT . DS . "model",
	"viewPath"			=> APP_ROOT . DS . "view",
	"layoutPath"		=> APP_ROOT . DS . "view" . DS . "layout",
	"cachePath"			=> APP_ROOT . DS . "cache",
	
	'viewEngine'		=> 'STA',
	'tablePrefix'		=> 'xlite_',

	'headerCharset'		=> 'gbk',
	'sessionStart'		=> true,
	
	//使用的调度器
	'dispatcher'		=> 'Plite.Dispatcher.RBACDispatcher',

	//数据库连接DSN设置
	'dsn'				=> array(
		"driver"	=> "Mysql",
		"dbType"	=> "mysql",
		"host"		=> "localhost",
		"user"		=> "root",
		"pwd"		=> "format999",
		"database"	=> "xlite",
	),

	'RBAC'				=> array(
		'onLogin'		   => array('index', 'login'),
		'onValidateFailed' => array('index', 'accessDenied'),
		'notValidate'	   => array(
			'index' => array('index', 'main', 'login', 'logout')
		)
	)
);
?>