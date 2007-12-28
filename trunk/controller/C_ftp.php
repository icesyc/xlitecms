<?php
/**
 * Զ��FTP�����controller
 *
 * @author     ice_berg16(Ѱ�εĵ�����)
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
	
	//�б�
	public function index()
	{
		$ftp = Plite::modelFactory("ftp");
		$ftpList = $ftp->find();
		$this->set('ftpList', $ftpList);
		$noRecord = count($ftpList) > 0 ? 'none' : '';
		$this->set('noRecord', $noRecord);
	}

	//����FTP�˺���Ϣ
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
					$this->set('msg', 'FTP�˺���ӳɹ�');
				}
			}
			catch(Exception $e)
			{
				$this->setView('flash_error');
				$this->set('msg', '���FTP�˺�ʱ��������<br/>'.$e->getMessage());
			}
		}
		else
		{
			$id = isset($_GET['id']) ? $_GET['id'] : null;
			$data = $ftp->get($id);
			$this->set($data);
			//ȡ�÷����б�
			$sort = Plite::modelFactory("sort");
			$sortList = $sort->format($sort->listTree(),'select');
			$this->set('sortList', $sortList);
		}
	}

	//ɾ��
	public function delete()
	{
		$this->autoRender = false;
		$ftp = Plite::modelFactory("ftp");
		if(empty($_GET['id'])) throw new Exception("δָ��id");
		$ftp->delete($_GET['id']);

		//ajax��ʽ�ύ
		if(is_array($_GET['id']))
		{
			exit('{"status":"REQUEST_OK"}');
		}
		else
		{
			$this->redirect($_SERVER['HTTP_REFERER']);
		}
	}

	//��������
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
	
	//ftp�ϴ�
	public function pub()
	{
		$mftp = Plite::modelFactory("ftp");
		$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);		

		$ftp = Plite::libFactory("ftp");
		Plite::load("ftpObserver", Config::get("modelPath"));
		$fo = new ftpObserver();

		//���ü����ߺ͹�����
		$ftp->setListener(array($fo, 'listener'));
		$ftp->setFilter(array($fo, 'filter'));

		foreach( $ids as $id )
		{
			try
			{					
				$rec = $mftp->get($id);						
				$path = array(APP_ROOT.'/tpl', 'index.htm');
				//�ϴ����з���
				if($rec['sortId'] == 0)
					array_push($path, ARTICLE_PATH);
				else
				{
					//���෢��
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
				//�����������
				if(!$fo->processStart()) continue;
				
				//���·�������
				$mftp->updatePubDate($id);
				$ftp->login($rec['host'], $rec['user'], $rec['pwd']);
				//���ñ���ģʽ
				$ftp->setPasv(true);
				//���Զ��Ŀ¼�����ڣ����Դ���
				if(!@$ftp->chdir($rec['pubDir']))
				{
					if(!@$ftp->mkdir($rec['pubDir']))
						throw new Exception("����Զ��Ŀ¼ {$rec['pubDir']} ʱʧ��");
				}
				//����Ŀ¼
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