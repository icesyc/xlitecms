<?php
/**
 * ��ɫ���������
 *
 * @package    
 * @author     ice_berg16(Ѱ�εĵ�����)
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

	//�б�
	public function index()
	{
		Plite::load("Plite.Lib.RBAC.Role");
		$role = new Role();
		$this->set('roleList', $role->findAll());
	}

	//��Ӻ��޸Ĳ���
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
			$this->set('msg', '����ɹ�');
			$this->setView('flash_success');
		}
		else
		{
			$role_id = isset($_GET['id']) ? $_GET['id'] : null;
			$record  = $role->get($role_id);
			if($role_id)
			{
				$roleAct = $role->getActList($role_id);
				
				$this->set('title', '�޸�');
				$this->set('roleAct', join(",", $roleAct));
			}
			else
			{
				$this->set('title', '���');
				$this->set('roleAct', '');
			}
			$act  = new Act();
			$actList = $act->findAll(null, 'act_id, act_name, controller', 'controller');
			$actList = $this->groupAct($actList);
			$this->set('actList', $actList);
			$this->set($record);
		}
	}

	//ɾ����ɫ
	public function delete()
	{
		Plite::load("Plite.Lib.RBAC.Role");
		$role = new Role();
		$role->delete($_GET['role_id']);
		if(is_array($_GET['role_id']))	//ajax�ύ������
			exit('{"status":"REQUEST_OK"}');
		else
			$this->forward('role',null,null,true);
	}

	//����ͬcontroller����Դ����
	private function groupAct($acts)
	{
		return Plite::libFactory("Utility")->tableFormat($acts, 4, 'group');
	}
}
?>