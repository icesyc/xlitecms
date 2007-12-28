<?php
/**
 * DefaultConfig
 *
 * Ӧ�ó���������ļ�
 *
 * @package    Config
 * @author     ice_berg16(Ѱ�εĵ�����)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */

//Ĭ��Ӧ�ó�����ļ���·������
define("APP_ROOT", dirname(__FILE__));

define("APP_NAME", "Xlite");

define("XLITE_VERSION", '1.2.0');

//xlite�İ�װ·��
define("XLITE_ROOT", "");

//һЩ����·������
define("ARTICLE_PATH", APP_ROOT . "/html");
define("XLITE_DB", APP_ROOT . "/db");
define("XLITE_SYS_TPL", APP_ROOT . "/tpl/system");
define("XLITE_APP_TPL", APP_ROOT . "/tpl/default");
define("MAX_EXEC_TIME", 5); //���ҳ��ִ��ʱ��
define("FTP_MAX_TIME", 10); //FTP����ʱ����ִ��ʱ��
define("PAGE_SIZE", 20);	//ÿҳ��ʾ�ļ�¼��

//�ɼ���������Դ�ı���Ŀ¼
define("SCRATCHER_RESOURCE_PATH", APP_ROOT . "/UserFiles");
//��Դ���������·�� ����վ��Ŀ¼��ʼ����
define("RESOURCE_VIRTUAL_PATH", XLITE_ROOT . "/UserFiles");
//�ɼ������ص��б���λ��
define("SCRATCHER_URL_PATH", APP_ROOT . "/scratcher");

//���µ�����·��
define("ARTICLE_VIRTUAL_PATH", XLITE_ROOT . "/html");
//ͼƬ����·��
define("GALLERY_PATH", APP_ROOT . "/gallery");
define("GALLERY_VIRTUAL_PATH", XLITE_ROOT . "/gallery");

//���ɵ�����ͼ�Ĵ�С
define("THUMBNAIL_WIDTH", 120);
define("THUMBNAIL_HEIGHT", 100);

return array(

	//һЩ������·������
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
	
	//ʹ�õĵ�����
	'dispatcher'		=> 'Plite.Dispatcher.RBACDispatcher',

	//���ݿ�����DSN����
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