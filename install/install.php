<?php
//====================================================
//		FileName:	install.php
//		Summary:	��װ����controller�ļ�
//		Author:		ice_berg16(Ѱ�εĵ�����)
//		version:	$Id: install.php 84 2006-12-31 07:30:21Z icesyc $
//		copyright (c)2006 ice_berg16@163.com
//====================================================
class install
{
	public function __construct()
	{
	}
	
	//Ĭ�ϳ���
	public function index()
	{
		if(PHP_VERSION < 5)
			throw new Exception("����PHP�汾С��5��Xliteֻ����PHP5����ߵİ汾�����С�");
		$filemode = $this->sysCheck();
		$this->output('install_index.htm', array('filemode' => $filemode));
	}

	//ϵͳ���
	public function sysCheck()
	{
		$files = array('../xml', '../gallery', '../cache', '../db', '../scratcher', '../userFiles', '../config.php');
		$filemode = array();
		foreach ($files as $f)
		{
			if (is_writable($f))
				$filemode[] = "<option style='color:#080'>".$f." ��д</option>";
			else
			{
				if( @chmod( $v, 0755 ) )
					$filemode[] = "<option style='color:#0A0'>".$f." ������Ϊ��д</option>";
				else
					$filemode[] = "<option style='color:#F00'>".$f." ��������ʧ��</option>";
			}
		}
		return join("", $filemode);
	}

	//��װ���ݿ�
	public function installDB()
	{
		$DSN = array(
					'driver'	=> $_POST['driver'],
					'host'		=> $_POST['dbhost'],
					'user'		=> $_POST['dbuser'],
					'pwd'		=> $_POST['dbpwd'],
		);
		Plite::load("Plite.Db.DataSource");
		$DB	= DataSource::getInstance($DSN);

		//д�������ļ�
		$file = "../config.php";
		$this->writeConfig($file);

		//�Ƿ�Ҫ�������ݿ�
		if(isset($_POST['createDB']))
		{
			$sql = "CREATE DATABASE ". $_POST['dbname'];
			$DB->exec($sql);
		}
		$DB->exec("USE ". $_POST['dbname']);

		//ִ��SQL���
		$file = "../db/xlite_install.sql";
		$override = isset($_POST['override']);
		$this->execSql($DB, $file, $_POST['tablePrefix'], $override);
		
		//��ӳ�������Ա
		$sql = sprintf("INSERT INTO %s VALUES(1, '%s', '%s', 1)", 
				$_POST['tablePrefix'].'admin',
				$_POST['adminUser'],
				md5($_POST['adminPwd']));
		$DB->exec($sql);
		$root	  = dirname(dirname($_SERVER['PHP_SELF']));
		if(strlen($root) == 1) $root = "";
		$loginURL = $_SERVER['SERVER_NAME'].$root."/login";
		$this->output('install_halt.htm', 'xlite��װ�ɹ�!<p>��ʹ�� <a href="../login" style="font-size:14px;text-decoration:underline">http://'.$loginURL.'</a> ���е�¼');
		//ɾ��install�ļ���
		//Plite::libFactory("FileSystem")->removeDir(dirname(__FILE__));
	}

	//�������
	public function output($file, $var=null)
	{
		if(is_null($var))
			exit(file_get_contents($file));
		if(is_array($var)) extract($var);
		else $msg = $var;
		$f = file_get_contents($file);
		echo preg_replace("#\{(.+)\}#Ue", '$\\1', $f);
	}

	//ִ��SQL�ļ�
	private function execSql($DB, $dbFile, $tblPrefix, $override)
	{
		if( !file_exists($dbFile))
			throw new Exception("ָ�������ݿ��ļ�������. ->".$dbFile);
		
		$sqlfile = file_get_contents($dbFile);		
		$sqlArray = preg_split("/;[\r\n]+/", $sqlfile);
		unset($sqlfile);
		
		foreach($sqlArray as $sql)
		{
			$sql = trim(preg_replace("/^\s*#.+$/m", "", $sql));
			if(!empty($sql))
			{
				if($override && preg_match('/CREATE TABLE `(.*)`/iU',$sql,$tblarr))
				{
					//���ݱ��������ɾ��ԭ����
					$tblName = str_replace('@@__prefix__@@', $tblPrefix, $tblarr[1]);
					$DB->exec("DROP TABLE IF EXISTS $tblName");	
				}
				//ǰ׺�滻
				$sql = str_replace('@@__prefix__@@', $tblPrefix, $sql);
				//echo "<p>".$sql."</p>";
				$DB->exec($sql);
			}
		}
		return true;
	}

	//д�������ļ�
	private function writeConfig($f)
	{
		if( !file_exists($f))
			throw new Exception("ָ�������ݿ��ļ�������. ->".$f);
		$config = file_get_contents($f);
		$dsn	= '\'dsn\'				=> array(
		"driver"	=> "'.$_POST['driver'].'",
		"dbType"	=> "'.$_POST['dbType'].'",
		"host"		=> "'.$_POST['dbhost'].'",
		"user"		=> "'.$_POST['dbuser'].'",
		"pwd"		=> "'.$_POST['dbpwd'].'",
		"database"	=> "'.$_POST['dbname'].'",
	)';
		$config = preg_replace('#\'dsn\'.+\)#sU', $dsn, $config);
		$tp		= '"tablePrefix"		=> "'.$_POST['tablePrefix'].'",';
		$config = preg_replace('#"tablePrefix.+,#sU', $tp, $config);
		file_put_contents($f, $config);
	}
}
?>