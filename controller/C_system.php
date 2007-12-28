<?php
/**
 * ϵͳ���ÿ�����
 *
 * @author     ice_berg16(Ѱ�εĵ�����)
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
		//ȡ�����������Ϣ
		if(@ini_get( "file_uploads" ) )
		{
			$fileUpload = "���� | �ļ�:".ini_get( "upload_max_filesize" )." | ����".ini_get( "post_max_size" );
		}
		else
		{
			$fileUpload = "<span class=\"red-font\">��ֹ</span>";
		}

		//����Ƿ�֧��GD��
		if(extension_loaded("gd"))
		{
		    $gi = gd_info();
		    $gdInfo = "<img src='images/enable.gif' align='absmiddle'/> �汾: " . $gi["GD Version"];
		}
		else
		{
		    $gdInfo = "<img src='images/disable.gif' align='absmiddle'/>";
		}
		$pdo	  = extension_loaded("PDO") ? "enable" : "disable";
		$mbstring = extension_loaded("mbstring") ? "enable" : "disable";
		$safeMode = ini_get("safe_mode") ? "����" : "�ر�";
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

	//�޸�����
	public function setPwd()
	{
		if($this->isPost())
		{
			$admin = Plite::modelFactory("admin");
			$rbac  = $admin->RBACInfo();
			$res = $admin->setPwd($rbac['user_id'], $_POST['pwd']);
			$this->setView('flash_success');
			$this->set('msg', '�����޸ĳɹ�');
		}
	}

	//���ݱ���
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
				$this->set('msg', '�������.' . $script);
			}
			else
			{
				exit(); //��ֹ��������ִ��
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

	//�ָ�����
	public function restore()
	{			
		if(!$this->isPost()) throw new Exception("����Ĳ�������");

		//ȡ��ʱ�䳬ʱ
		@set_time_limit( 0 );
		Plite::load("Plite.Db.DataSource");
		$DB   = DataSource::getInstance(Config::get("dsn"));
		$md   = Plite::libFactory("MysqlDump", array($DB));
		$fu   = Plite::libFactory("FileUploader");
		//����ļ���Դ 
		$fname = $_POST['restoreType'] == "server" 
				 ? XLITE_DB . DS . $_POST["fname"]
				 : $fu->getFile("dbfile")->move(XLITE_DB, RANDOM_NAME);
		
		$msg = $md->restore($fname) ? "�ָ����" : "ָ�������ݿ��ļ� $fname ������";
		//ɾ���ϴ����ļ�
		@unlink($fname);
		$this->setView('flash_success');
		$script = '<script type="text/javascript">
						parent.Utility.hidden("tip-box");
					</script>';
		$this->set('msg', $msg . $script);
	}
	
	//�������ݿ��ļ�
	public function download()
	{
		if(!$this->isPost()) throw new Exception("����Ĳ�������");
		$util = Plite::libFactory("Utility");
		$util->sendFile(XLITE_DB . DS . $_POST['fname']);
	}

	//ɾ���ļ�
	public function delete()
	{
		if(!$this->isPost()) throw new Exception("����Ĳ�������");
		$fname = XLITE_DB . DS . $_POST["fname"];
		@unlink($fname);
		$this->forward('system', 'backup', null, true);
	}
}
?>