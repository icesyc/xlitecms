<?php
/**
 * guestbook ������
 *
 * @author     ice_berg16(Ѱ�εĵ�����)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id: C_guestbook.php 86 2007-03-23 08:19:09Z icesyc $
 */

class C_guestbook extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->setLayout('head', 'admin_head');
	}
	
	//�б�
	public function index()
	{
		$guestbook	= Plite::modelFactory("guestbook");
		$page		= !empty($_GET['page']) ? $_GET['page'] : 1;
		$rows		= 10;
		$msgList	= $guestbook->find($rows, $page);
		$noRecord	= count($msgList) > 0 ? 'none' : '';
		$pager = Plite::libFactory("Pager", array($guestbook->getPageParam()));		
		$pager->setFormat(file_get_contents(XLITE_SYS_TPL. "/pager.tpl"));
		$this->set('pager', $pager->makePage());
		$this->set('msgList', $msgList);
		$this->set('noRecord', $noRecord);
	}

	//����
	public function save()
	{
		$this->autoRender = false;
		$guestbook = Plite::modelFactory("guestbook");
		if($this->isPost())
		{
			try
			{
				$guestbook->save($_POST);
				$this->redirect($_SERVER['HTTP_REFERER']);
			}
			catch(Exception $e)
			{
				echo $e->getMessage();
			}
		}
	}

	//ɾ��
	public function delete()
	{
		$this->autoRender = false;
		$guestbook = Plite::modelFactory("guestbook");	
		try
		{
			if(empty($_GET['id'])) throw new Exception("δָ��id");
			$guestbook->delete($_GET['id']);
			if(is_array($_GET['id']))
				exit('{"status":"REQUEST_OK"}');
			else
				$this->redirect($_SERVER['HTTP_REFERER']);
		}
		catch(Exception $e){
			echo $e->getMessage();
		}
	}
}
?>