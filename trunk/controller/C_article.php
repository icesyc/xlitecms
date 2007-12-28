<?php
/**
 * ���¿�����
 *
 * @author     ice_berg16(Ѱ�εĵ�����)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */

class C_article extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->setLayout('head', 'admin_head');
	}
	
	//list
	public function index()
	{
		//�����б��λ��
		$_SESSION['url']  = $_SERVER['REQUEST_URI'];

		//���浱ǰѡ��ķ���
		$currentSortId	  = '';
		//������Ӧ��model
		list($art, $sort) = Plite::modelFactory('article', 'sort');
		$condition['page']  = isset($_GET['page']) ? $_GET['page'] : 1;
		$condition['limit'] = PAGE_SIZE;
		if(!empty($_GET['sortId']))
		{
			$currentSortId = $_GET['sortId'];
			$condition['sort_id'] = $sort->getChild($_GET['sort_id']);
		}
		if(!empty($_GET['tag']))
			$condition['tag'] = $_GET['tag'];
		
		//$art->setDebug(true);
		$art->setFilter(array($art, 'filter'), Model::FILTER_FIND);
		$artList = $art->find($condition);
		
		//���ط�ҳ��
		$pager = Plite::libFactory("Pager", array($art->getPageParam()));		
		$pager->setFormat(file_get_contents(XLITE_SYS_TPL. "/pager.tpl"));

		$sortList = $sort->format($sort->listTree(),'select');
		$noRecord = count($artList) > 0 ? 'none' : '';
		$this->set('noRecord', $noRecord);
		$this->set('sort', $sortList);
		$this->set('currentSortId', $currentSortId);
		$this->set('artList', $artList);
		$this->set('pager', $pager->makePage());		
	}

	public function save()
	{
		list($art, $sort, $tags) = Plite::modelFactory("article", "sort", "tags");
		if($this->isPost())
		{
			//�ж��Ƿ�������ͼ
			if(!isset($_POST['is_pic']))
			{
				$_POST['thumbnail'] = '';
			}
			list($y, $m, $d) = explode("-", $_POST['post_time']);
			$_POST['post_time'] = mktime(0, 0, 0, $m, $d, $y);
			$art->save($_POST);
			//�޸ģ����ص��б�ҳ
			if(!empty($_POST['id']))
				$this->redirect($_SESSION['url']);

			$this->setView('flash_success');
			$this->set('msg', '���±���ɹ�.');
		}
		else
		{			
			$sortList = $sort->format($sort->listTree(),'select');
			$data = $art->get(isset($_GET['id']) ? $_GET['id'] : null);
			if($data['post_time'] == '')
				$data['post_time'] = date("Y-m-d");
			else
				$data['post_time'] = date("Y-m-d", $data['post_time']);

			$this->set($data);
			$this->set('sort', $sortList);
			$this->set('act', isset($_GET['id']) ? '�༭' : '���');
			/* ���� editor */
			Plite::load('fckeditor',APP_ROOT . "/FCKeditor");
			$editor = new FCKeditor("content");
			$editor->BasePath = XLITE_ROOT . "/FCKeditor/";
			$editor->ToolbarSet = 'Normal';
			$editor->Value    = $data['content'];
			$editor->Height	  = "500";
			$this->set('editor', $editor->CreateHtml());
		}
	}

	//����ɾ������
	public function delete()
	{
		$art = Plite::modelFactory("article");
		if($art->delete($_REQUEST['id']))
			echo '{"status":"REQUEST_OK"}';
		else
			echo '{"status":"REQUEST_FAILED"}';
	}

	//�����Ƽ���ȡ���Ƽ�
	public function setRecmd()
	{
		$art = Plite::modelFactory("article");
		$act = $_GET['status'] == 1 ? 'recmd' :'unrecmd';

		if($art->setRecmd($_GET['id'], $_GET['status']) !== false)
			exit('{"status":"REQUEST_OK","action":"'.$act.'"}');
		else
			exit('{"status":"REQUEST_FAILED"}');
	}

	//������������˶���
	public function setAudit()
	{
		//�Ƿ�Ҫ����ȫ������
		if(isset($_GET['all'])) $_GET['id'] = null;
		$art = Plite::modelFactory("article");
		$act = $_GET['status'] == 1 ? 'audit' :'lock';

		if($art->setAudit($_GET['id'], $_GET['status']) !== false)
			exit('{"status":"REQUEST_OK","action":"'.$act.'"}');
		else
			exit('{"status":"REQUEST_FAILED"}');
	}
}
?>