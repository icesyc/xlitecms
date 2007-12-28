<?php
/**
 * 远程FTP管理的controller
 *
 * @author     ice_berg16(寻梦的稻草人)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id: C_ftp.php 86 2007-03-23 08:19:09Z icesyc $
 */

class C_ftp extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->setLayout('head', 'admin_head');
	}
	
	//列表
	public function index()
	{
		$ftp = Plite::modelFactory("ftp");
		$ftpList = $ftp->find();
		$this->set('ftpList', $ftpList);
		$noRecord = count($ftpList) > 0 ? 'none' : '';
		$this->set('noRecord', $noRecord);
	}

	//保存FTP账号信息
	public function save()
	{
		$ftp = Plite::modelFactory("ftp");
		if($this->isPost())
		{
			try
			{
				$ftp->save($_POST);
				if(!empty($_POST['id']))
				{
					$this->forward('ftp', 'index', null, true);
				}
				else
				{
					$this->setView('flash_success');
					$this->set('msg', 'FTP账号添加成功');
				}
			}
			catch(Exception $e)
			{
				$this->setView('flash_error');
				$this->set('msg', '添加FTP账号时发生错误<br/>'.$e->getMessage());
			}
		}
		else
		{
			$id = isset($_GET['id']) ? $_GET['id'] : null;
			$data = $ftp->get($id);
			$this->set($data);
			//取得分类列表
			$sort = Plite::modelFactory("sort");
			$sortList = $sort->format($sort->listTree(),'select');
			$this->set('sortList', $sortList);
		}
	}

	//删除
	public function delete()
	{
		$this->autoRender = false;
		$ftp = Plite::modelFactory("ftp");
		if(empty($_GET['id'])) throw new Exception("未指定id");
		$ftp->delete($_GET['id']);

		//ajax方式提交
		if(is_array($_GET['id']))
		{
			exit('{"status":"REQUEST_OK"}');
		}
		else
		{
			$this->redirect($_SERVER['HTTP_REFERER']);
		}
	}

	//测试连接
	public function test()
	{
		$this->autoRender = false;
		$ftp = Plite::modelFactory("ftp");
		$res = $ftp->get($_GET['id']);
		$ftp = Plite::libFactory("ftp");
		try
		{
			$ftp->login($res['host'], $res['user'], $res['pwd']);
			exit('{"status":"REQUEST_OK"}');
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}
	
	//ftp上传
	public function pub()
	{
		$mftp = Plite::modelFactory("ftp");
		$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);		

		$ftp = Plite::libFactory("ftp");
		Plite::load("ftpObserver", Config::get("modelPath"));
		$fo = new ftpObserver();

		//设置监听者和过滤器
		$ftp->setListener(array($fo, 'listener'));
		$ftp->setFilter(array($fo, 'filter'));

		foreach( $ids as $id )
		{
			try
			{					
				$rec = $mftp->get($id);						
				$path = array(APP_ROOT.'/tpl', 'index.htm');
				//上传所有分类
				if($rec['sortId'] == 0)
					array_push($path, ARTICLE_PATH);
				else
				{
					//分类发布
					$sort = Plite::modelFactory("sort");
					$sortId = $sort->getChild($rec['sortId']);
					foreach( $sortId as $dir )
					{
						array_push($path, ARTICLE_PATH.DS.$dir);
					}
				}
				$fo->setPath($path);
				//$fo->setDebug(true);
				$fo->setFtp($rec);
				//处理过则跳过
				if(!$fo->processStart()) continue;
				
				//更新发布日期
				$mftp->updatePubDate($id);
				$ftp->login($rec['host'], $rec['user'], $rec['pwd']);
				//启用被动模式
				$ftp->setPasv(true);
				//如果远程目录不存在，则尝试创建
				if(!@$ftp->chdir($rec['pubDir']))
				{
					if(!@$ftp->mkdir($rec['pubDir']))
						throw new Exception("创建远程目录 {$rec['pubDir']} 时失败");
				}
				//补齐目录
				if(substr($rec['pubDir'],-1) != '/') $rec['pubDir'] .= '/';
			
				foreach( $path as $p)
				{
					if(is_dir($p))
						$ftp->putDir($p, $rec['pubDir'].basename($p));
					else
						$ftp->put($p, $rec['pubDir'].basename($p));
				}
				$ftp->quit();
				$fo->processOver();

			}
			catch(Exception $e)
			{
				exit($e->getMessage());
			}
		}
		$fo->finish();
	}
}
?>