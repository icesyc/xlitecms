<?php
//====================================================
//		FileName:	install.php
//		Summary:	安装程序controller文件
//		Author:		ice_berg16(寻梦的稻草人)
//		version:	$Id: install.php 84 2006-12-31 07:30:21Z icesyc $
//		copyright (c)2006 ice_berg16@163.com
//====================================================
class install
{
	public function __construct()
	{
	}
	
	//默认程序
	public function index()
	{
		if(PHP_VERSION < 5)
			throw new Exception("您的PHP版本小于5，Xlite只能在PHP5或更高的版本上运行。");
		$filemode = $this->sysCheck();
		$this->output('install_index.htm', array('filemode' => $filemode));
	}

	//系统检查
	public function sysCheck()
	{
		$files = array('../xml', '../gallery', '../cache', '../db', '../scratcher', '../userFiles', '../config.php');
		$filemode = array();
		foreach ($files as $f)
		{
			if (is_writable($f))
				$filemode[] = "<option style='color:#080'>".$f." 可写</option>";
			else
			{
				if( @chmod( $v, 0755 ) )
					$filemode[] = "<option style='color:#0A0'>".$f." 已配置为可写</option>";
				else
					$filemode[] = "<option style='color:#F00'>".$f." 配置属性失败</option>";
			}
		}
		return join("", $filemode);
	}

	//安装数据库
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

		//写入配置文件
		$file = "../config.php";
		$this->writeConfig($file);

		//是否要创建数据库
		if(isset($_POST['createDB']))
		{
			$sql = "CREATE DATABASE ". $_POST['dbname'];
			$DB->exec($sql);
		}
		$DB->exec("USE ". $_POST['dbname']);

		//执行SQL语句
		$file = "../db/xlite_install.sql";
		$override = isset($_POST['override']);
		$this->execSql($DB, $file, $_POST['tablePrefix'], $override);
		
		//添加超级管理员
		$sql = sprintf("INSERT INTO %s VALUES(1, '%s', '%s', 1)", 
				$_POST['tablePrefix'].'admin',
				$_POST['adminUser'],
				md5($_POST['adminPwd']));
		$DB->exec($sql);
		$root	  = dirname(dirname($_SERVER['PHP_SELF']));
		if(strlen($root) == 1) $root = "";
		$loginURL = $_SERVER['SERVER_NAME'].$root."/login";
		$this->output('install_halt.htm', 'xlite安装成功!<p>请使用 <a href="../login" style="font-size:14px;text-decoration:underline">http://'.$loginURL.'</a> 进行登录');
		//删除install文件夹
		//Plite::libFactory("FileSystem")->removeDir(dirname(__FILE__));
	}

	//输出程序
	public function output($file, $var=null)
	{
		if(is_null($var))
			exit(file_get_contents($file));
		if(is_array($var)) extract($var);
		else $msg = $var;
		$f = file_get_contents($file);
		echo preg_replace("#\{(.+)\}#Ue", '$\\1', $f);
	}

	//执行SQL文件
	private function execSql($DB, $dbFile, $tblPrefix, $override)
	{
		if( !file_exists($dbFile))
			throw new Exception("指定的数据库文件不存在. ->".$dbFile);
		
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
					//数据表存在则先删除原来的
					$tblName = str_replace('@@__prefix__@@', $tblPrefix, $tblarr[1]);
					$DB->exec("DROP TABLE IF EXISTS $tblName");	
				}
				//前缀替换
				$sql = str_replace('@@__prefix__@@', $tblPrefix, $sql);
				//echo "<p>".$sql."</p>";
				$DB->exec($sql);
			}
		}
		return true;
	}

	//写入配置文件
	private function writeConfig($f)
	{
		if( !file_exists($f))
			throw new Exception("指定的数据库文件不存在. ->".$f);
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