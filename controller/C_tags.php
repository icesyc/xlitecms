<?php
/**
 * ��ǩ���������
 *
 * @author     ice_berg16(Ѱ�εĵ�����)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */

class C_tags extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->setLayout('head', 'admin_head');
	}
	
	public function index()
	{
		$page  = isset($_GET['page']) ? $_GET['page'] : 1;
		$tags = Plite::modelFactory("tags");
		$keyword = empty($_GET['keyword']) ? null : $_GET['keyword'];
		$tagList = $tags->listTag($page, PAGE_SIZE, $keyword);
		
		//���ط�ҳ��
		$pager = Plite::libFactory("Pager", array($tags->getPageParam()));		
		$pager->setFormat(file_get_contents(XLITE_SYS_TPL. "/pager.tpl"));
		$this->set('tagList', $tagList);
		$this->set('pager', $pager->makePage());
	}
	
	//ɾ������
	public function delete()
	{
		$tags = Plite::modelFactory("tags");
		$tags->deleteBykeyword($_REQUEST['id']);
		if(is_array($_REQUEST['id']))
			exit('{"status":"REQUEST_OK"}');
		else
			$this->redirect($_SERVER['HTTP_REFERER']);
	}
}
?>