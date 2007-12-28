<?php
/**
 * 角色管理控制器
 *
 * @package    
 * @author     ice_berg16(寻梦的稻草人)
 * @copyright  ice_berg16@163.com
 * @version    $Id$
 */

class C_role extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->setLayout('head', 'admin_head');
		$this->setLayout('foot', 'admin_foot');
	}

	//列表
	public function index()
	{
		Plite::load("Plite.Lib.RBAC.Role");
		$role = new Role();
		$this->set('roleList', $role->findAll());
	}

	//添加和修改操作
	public function save()
	{
		Plite::load("Plite.Lib.RBAC.Role");
		Plite::load("Plite.Lib.RBAC.Act");
		$role = new Role();

		if($this->isPost())
		{
			$act = isset($_POST['act']) ? $_POST['act'] : array();
			if(empty($_POST['role']['role_id']))
			{
				$role->create($_POST['role'], $act);
			}
			else
			{
				$role->update($_POST['role'], $act);
			}
			$this->set('msg', '保存成功');
			$this->setView('flash_success');
		}
		else
		{
			$role_id = isset($_GET['id']) ? $_GET['id'] : null;
			$record  = $role->get($role_id);
			if($role_id)
			{
				$roleAct = $role->getActList($role_id);
				
				$this->set('title', '修改');
				$this->set('roleAct', join(",", $roleAct));
			}
			else
			{
				$this->set('title', '添加');
				$this->set('roleAct', '');
			}
			$act  = new Act();
			$actList = $act->findAll(null, 'act_id, act_name, controller', 'controller');
			$actList = $this->groupAct($actList);
			$this->set('actList', $actList);
			$this->set($record);
		}
	}

	//删除角色
	public function delete()
	{
		Plite::load("Plite.Lib.RBAC.Role");
		$role = new Role();
		$role->delete($_GET['role_id']);
		if(is_array($_GET['role_id']))	//ajax提交过来的
			exit('{"status":"REQUEST_OK"}');
		else
			$this->forward('role',null,null,true);
	}

	//将相同controller的资源分组
	private function groupAct($acts)
	{
		return Plite::libFactory("Utility")->tableFormat($acts, 4, 'group');
	}
}
?>