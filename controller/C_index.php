<?php
/**
 * Ĭ�Ͽ�����
 *
 * @author     ice_berg16(Ѱ�εĵ�����)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */

class C_index extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->set('APP_NAME', APP_NAME);
		$this->setLayout('head', 'admin_head');
	}
	
	public function index()
	{
		$admin = Plite::modelFactory("admin");
		$rbac  = $admin->RBACInfo();
		$this->set('user_name', $rbac['user_name']);		
		$this->setLayout('foot','admin_foot');
	}

	public function main()
	{
	}
	
	//û��Ȩ�޲���ʱ����ʾ��Ϣ
	public function accessDenied()
	{
		$this->autoRender = false;
		if(!empty($_GET['ajax']))
		{
			exit('{"status":"ACCESS_DENIED"}');
		}
		else
		{
			$this->set('msg', '�����ڵ��û���û��Ȩ�޽��д˲�����');
			$this->renderView('flash_error');
		}
	}

	//��¼����
	public function login()
	{
		if($this->isPost())
		{
			$admin = Plite::modelFactory("admin");
			try
			{
				$admin->login($_POST['user_name'], $_POST['password']);
				$this->forward('index', 'index', null, true);
			}
			catch(Exception $e)
			{
				$this->set('msg', '<img src="images/alert.gif" align="absmiddle"/> ' . $e->getMessage());
			}
		}
		else
		{
			$this->set('msg', '');
		}
	}

	public function logout()
	{
		$admin = Plite::modelFactory("admin");
		$admin->logout();
		$this->forward('index', 'index', null, true);
	}
}
?>