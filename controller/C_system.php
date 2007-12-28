<?php
/**
 * 系统设置控制器
 *
 * @author     ice_berg16(寻梦的稻草人)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */

class C_system extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->set('APP_NAME', APP_NAME);
		$this->setLayout('head', 'admin_head');
	}
	
	public function index()
	{
		
		Plite::load("Plite.Db.DataSource");
		$db = DataSource::getInstance(Config::get("dsn"));
		$st = $db->query("SELECT VERSION()");
		$mysqlVersion = $st->fetchColumn();
		//取出相关配置信息
		if(@ini_get( "file_uploads" ) )
		{
			$fileUpload = "允许 | 文件:".ini_get( "upload_max_filesize" )." | 表单：".ini_get( "post_max_size" );
		}
		else
		{
			$fileUpload = "<span class=\"red-font\">禁止</span>";
		}

		//检测是否支持GD库
		if(extension_loaded("gd"))
		{
		    $gi = gd_info();
		    $gdInfo = "<img src='images/enable.gif' align='absmiddle'/> 版本: " . $gi["GD Version"];
		}
		else
		{
		    $gdInfo = "<img src='images/disable.gif' align='absmiddle'/>";
		}
		$pdo	  = extension_loaded("PDO") ? "enable" : "disable";
		$mbstring = extension_loaded("mbstring") ? "enable" : "disable";
		$safeMode = ini_get("safe_mode") ? "启用" : "关闭";
		$systemInfo = array("webServer"	=> PHP_OS . " | " .$_SERVER["SERVER_SOFTWARE"],
							"domain"	=> $_SERVER["SERVER_NAME"],
							"phpVer"	=> PHP_VERSION,
							"mysqlVer"	=> $mysqlVersion,
							"upload"	=> $fileUpload,
							"safeMode"	=> $safeMode,
							"gdInfo"	=> $gdInfo,
							"pdo"		=> $pdo,
							"mbstring"	=> $mbstring
							);
		$this->set($systemInfo);
	}

	//修改密码
	public function setPwd()
	{
		if($this->isPost())
		{
			$admin = Plite::modelFactory("admin");
			$rbac  = $admin->RBACInfo();
			$res = $admin->setPwd($rbac['user_id'], $_POST['pwd']);
			$this->setView('flash_success');
			$this->set('msg', '密码修改成功');
		}
	}

	//数据备份
	public function backup()
	{
		if($this->isPost())
		{
			Plite::load("Plite.Db.DataSource");
			$DB = DataSource::getInstance(Config::get("dsn"));
			$md = Plite::libFactory("MysqlDump", array($DB));
			$md->setDumpOption( "dbName", Config::get("dsn.database"));
			$md->setDumpOption( "dumpType", $_POST['dumpType'] );
			$md->setDumpOption( "savePath", XLITE_DB );
			$md->setDumpOption( "saveType", $_POST['saveType'] );
			$md->dumpDB();
			if( $_POST['saveType'] == 'server' )
			{
				$this->setView('flash_success');
				$script = '<script type="text/javascript">'
						. 'parent.Utility.hidden("tip-box");'
						. '</script>';
				$this->set('msg', '备份完毕.' . $script);
			}
			else
			{
				exit(); //防止后面程序的执行
			}
		}
		else
		{
			$fl = Plite::libFactory("FileSystem")->listDir(XLITE_DB);
			foreach($fl as $f)
				$fileList[]['file'] = $f;
			$this->set('fileList', $fileList);
			$this->set('maxPostFileSize', @ini_get("upload_max_filesize"));
		}
	}

	//恢复数据
	public function restore()
	{			
		if(!$this->isPost()) throw new Exception("错误的操作流程");

		//取消时间超时
		@set_time_limit( 0 );
		Plite::load("Plite.Db.DataSource");
		$DB   = DataSource::getInstance(Config::get("dsn"));
		$md   = Plite::libFactory("MysqlDump", array($DB));
		$fu   = Plite::libFactory("FileUploader");
		//检测文件来源 
		$fname = $_POST['restoreType'] == "server" 
				 ? XLITE_DB . DS . $_POST["fname"]
				 : $fu->getFile("dbfile")->move(XLITE_DB, RANDOM_NAME);
		
		$msg = $md->restore($fname) ? "恢复完毕" : "指定的数据库文件 $fname 不存在";
		//删除上传的文件
		@unlink($fname);
		$this->setView('flash_success');
		$script = '<script type="text/javascript">
						parent.Utility.hidden("tip-box");
					</script>';
		$this->set('msg', $msg . $script);
	}
	
	//下载数据库文件
	public function download()
	{
		if(!$this->isPost()) throw new Exception("错误的操作流程");
		$util = Plite::libFactory("Utility");
		$util->sendFile(XLITE_DB . DS . $_POST['fname']);
	}

	//删除文件
	public function delete()
	{
		if(!$this->isPost()) throw new Exception("错误的操作流程");
		$fname = XLITE_DB . DS . $_POST["fname"];
		@unlink($fname);
		$this->forward('system', 'backup', null, true);
	}
}
?>