<?php
/**
 * 默认控制器
 *
 * @author     ice_berg16(寻梦的稻草人)
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
	
	//没有权限操作时的提示信息
	public function accessDenied()
	{
		$this->autoRender = false;
		if(!empty($_GET['ajax']))
		{
			exit('{"status":"ACCESS_DENIED"}');
		}
		else
		{
			$this->set('msg', '您所在的用户组没有权限进行此操作。');
			$this->renderView('flash_error');
		}
	}

	//登录函数
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