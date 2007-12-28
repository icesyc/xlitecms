<?php
/**
 * link ������
 *
 * @author     ice_berg16(Ѱ�εĵ�����)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id: C_link.php 86 2007-03-23 08:19:09Z icesyc $
 */

class C_link extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->setLayout('head', 'admin_head');
	}
	
	//�б�
	public function index()
	{
		$_SESSION['__linkList__'] = $_SERVER['REQUEST_URI'];
		$link = Plite::modelFactory("link");
		$page = isset($_GET['page']) ? $_GET['page'] : 1;
		$rows = 20;
		$links = $link->find($rows, $page);
		$this->set('links', $links);
		$noRecord = count($links) > 0 ? 'none' : '';
		$this->set('noRecord', $noRecord);
	}

	//����ɼ�����
	public function save()
	{
		$link = Plite::modelFactory("link");
		if($this->isPost())
		{
			$link->save($_POST);
			if(!empty($_POST['id']))
				$this->redirect($_SESSION['__linkList__']);
			else
			{
				$this->setView('flash_success');
				$this->set('msg', '������ӳɹ�');
			}
		}
		else
		{
			if(isset($_GET['id']))
			{
				$id  = $_GET['id'];
				$act = '�޸�';
			}
			else
			{
				$id  = null;
				$act = '����';
			}
			$this->set('act', $act);
			$this->set($link->get($id));
		}
	}

	//ɾ��
	public function delete()
	{
		$this->autoRender = false;
		$link = Plite::modelFactory("link");
		if(empty($_GET['id'])) throw new Exception("δָ��id");
		if($link->delete($_GET['id']))
		{
			//ajax����
			if(is_array($_GET['id']))
				exit('{"status":"REQUEST_OK"}');
			else
				$this->redirect($_SERVER['HTTP_REFERER']);
		}
		else
			die('ɾ��ʧ��.');
	}
}
?>