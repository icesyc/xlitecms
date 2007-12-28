<?php
/**
 * link 控制器
 *
 * @author     ice_berg16(寻梦的稻草人)
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
	
	//列表
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

	//保存采集规则
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
				$this->set('msg', '链接添加成功');
			}
		}
		else
		{
			if(isset($_GET['id']))
			{
				$id  = $_GET['id'];
				$act = '修改';
			}
			else
			{
				$id  = null;
				$act = '新增';
			}
			$this->set('act', $act);
			$this->set($link->get($id));
		}
	}

	//删除
	public function delete()
	{
		$this->autoRender = false;
		$link = Plite::modelFactory("link");
		if(empty($_GET['id'])) throw new Exception("未指定id");
		if($link->delete($_GET['id']))
		{
			//ajax操作
			if(is_array($_GET['id']))
				exit('{"status":"REQUEST_OK"}');
			else
				$this->redirect($_SERVER['HTTP_REFERER']);
		}
		else
			die('删除失败.');
	}
}
?>