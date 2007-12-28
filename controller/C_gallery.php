<?php
/**
 * Gallery������
 *
 * @author     ice_berg16(Ѱ�εĵ�����)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */

class C_gallery extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->setLayout("head", "admin_head");
	}
	
	//�б�
	public function index()
	{
		$page = isset($_GET['page']) ? $_GET['page'] : 1;
		$sortId = isset($_GET['sort_id']) ? $_GET['sort_id'] : null;
		list($gallery, $sort) = Plite::modelFactory("gallery", "sort");
		$galleryList = $gallery->find($page, 16, $sortId, true);
		$this->set('galleryList', $galleryList);

		//�����ҳ����
		$pageParam = $gallery->getPageParam();
		$pager     = Plite::libFactory("Pager", array($pageParam));
		$pager->setFormat(file_get_contents(XLITE_SYS_TPL. "/pager.tpl"));
		$this->set('pager', $pager->makePage());
		
		$sortList = $sort->format($sort->listTree(),'select');
		$this->set('sortList', $sortList);
		$this->set('sort_id', $sortId);
		$this->set('noRecord', count($galleryList) > 0 ? 'none' : '');
	}

	//���ͼƬ��
	public function save()
	{
		list($gallery, $sort) = Plite::modelFactory("gallery", "sort");
		if($this->isPost())
		{
			try
			{
				$gallery->setDebug();
				$gallery->save($_POST, $_FILES['image']);
				$this->setView("flash_success");
				$this->set('msg', 'ͼƬ����ӳɹ�');
			}
			catch(Exception $e)
			{
				$this->setView("flash_error");
				$this->set('msg', $e->getMessage());
			}
		}
		else
		{
			$sortList = $sort->format($sort->listTree(),'select');
			$this->set($gallery->get());
			$this->set('sortList', $sortList);
			$this->set('maxFileSize', ini_get("upload_max_filesize"));
			$this->set('maxFormSize', ini_get("post_max_size" ));
		}
	}

	//�޸�ͼƬ���ı���
	public function editTitle()
	{
		$this->autoRender = false;
		try
		{
			$gallery = Plite::modelFactory("gallery");
			$data = array("id" => $_GET['id'], "title" => iconv("UTF-8", "GBK//IGNORE", $_GET['title']));
			$gallery->update($data);
			exit('{"status":"REQUEST_OK"}');
		}
		catch(Exception $e)
		{
			echo $e->getMessage($e);
		}
	}

	//ɾ��
	public function delete()
	{
		$this->autoRender = false;
		$gallery = Plite::modelFactory("gallery");
		if(empty($_GET['id'])) throw new Exception("δָ��id");
		try
		{
			$gallery->delete($_GET['id']);
			exit('{"status":"REQUEST_OK"}');
		}
		catch(Exception $e)
		{
			exit('{"status":"REQUEST_FAILED"}');
			//$this->setView("flash_error");
			//$this->set('msg', $e->getMessage());
		}
	}
}
?>