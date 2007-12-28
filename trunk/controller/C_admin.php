<?php
/**
 * �����˺�Controller
 *
 * @author     ice_berg16(Ѱ�εĵ�����)
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
	
	//Ĭ��Ϊ�б����
	public function index()
	{
		//���ؽ�ɫ����
		Plite::load("Plite.Lib.RBAC.Role");
		$role = new Role();

		$admin = Plite::modelFactory("admin");
		$adminList = $admin->findAll();
		$this->set('roleList', $role->findAll());
		$this->set('adminList', $adminList);
	}

	//����޸Ĳ���
	public function save()
	{
		if(!$this->isPost()) throw new Exception("����Ĳ�������");
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

	//ɾ��
	public function delete()
	{
		$this->autoRender = false;
		$admin = Plite::modelFactory("admin");
		$admin->delete($_GET['id']);
		if(is_array($_GET['id']))	//ajax�ύ������
		{
			exit('{"status":"REQUEST_OK"}');
		}
		else
			$this->forward('admin',null,null,true);
	}

	//�����޸�
	public function setPwd()
	{
		if(!$this->isPost()) throw new Exception("����Ĳ�������");
		$admin = Plite::modelFactory("admin");
		$admin->setPwd($_POST['user_id'], $_POST['password']);
		exit('{"status":"REQUEST_OK"}');
	}

	//�޸��û���
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