<?php
/**
 * 管理账号Controller
 *
 * @author     ice_berg16(寻梦的稻草人)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */

class C_admin extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->setLayout('head', 'admin_head');
	}
	
	//默认为列表操作
	public function index()
	{
		//加载角色对象
		Plite::load("Plite.Lib.RBAC.Role");
		$role = new Role();

		$admin = Plite::modelFactory("admin");
		$adminList = $admin->findAll();
		$this->set('roleList', $role->findAll());
		$this->set('adminList', $adminList);
	}

	//添加修改操作
	public function save()
	{
		if(!$this->isPost()) throw new Exception("错误的操作流程");
		$this->autoRender = false;
		$admin = Plite::modelFactory("admin");
		try
		{
			$admin->save($_POST);
			$this->forward('admin',null,null,true);
		}
		catch(Exception $e)
		{
			$this->setView('flash_error');
			$this->set('msg', $e->getMessage());
			$this->renderView();
		}
	}

	//删除
	public function delete()
	{
		$this->autoRender = false;
		$admin = Plite::modelFactory("admin");
		$admin->delete($_GET['id']);
		if(is_array($_GET['id']))	//ajax提交过来的
		{
			exit('{"status":"REQUEST_OK"}');
		}
		else
			$this->forward('admin',null,null,true);
	}

	//密码修改
	public function setPwd()
	{
		if(!$this->isPost()) throw new Exception("错误的操作流程");
		$admin = Plite::modelFactory("admin");
		$admin->setPwd($_POST['user_id'], $_POST['password']);
		exit('{"status":"REQUEST_OK"}');
	}

	//修改用户组
	public function chrole()
	{
		$this->autoRender = false;
		$admin = Plite::modelFactory("admin");
		$admin->update(array(
			'user_id'	=> $_POST['user_id'], 
			'role_id'	=> $_POST['role_id']
		));
		$this->redirect($_SERVER['HTTP_REFERER']);
	}
}
?>