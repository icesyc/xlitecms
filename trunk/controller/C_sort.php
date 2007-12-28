<?php
/**
 * 分类操作控制器
 *
 * @author     ice_berg16(寻梦的稻草人)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */

class C_sort extends Controller
{
	public function __construct()
	{
		parent::__construct();		
		$this->setLayout('head', 'admin_head');
	}
	
	public function index()
	{
		$sort = Plite::modelFactory('sort');
		$data       = $sort->listTree('list');
		$sortSelect = $sort->format($data, 'select');
		$sortList   = $sort->format($data,'chkbox');
		$noRecord = count($data) > 0 ? 'none' : '';
		$this->set('noRecord', $noRecord);
		$this->set('sortSelect', $sortSelect);
		$this->set('sortList', $sortList);
	}

	//保存分类
	public function save()
	{
		if($this->isPost())
		{
			$sort = Plite::modelFactory('sort');
			if($sort->save($_POST))
				$this->forward('sort','index',null,true);
			else
			{
				$this->setView('flash_error');
				$this->set('msg', '保存分类时失败');
			}
		}
	}
	
	//删除分类
	public function delete()
	{
		$this->autoRender = false;

		if(empty($_GET['id']))
			throw new Exception("未指定id");
		
		$sort = Plite::modelFactory('sort');
		if($sort->delete($_GET['id']))
		{
			if(is_array($_GET['id']))
				exit('{"status":"REQUEST_OK"}');
			else
				$this->forward('sort',null,null,true);
		}
		else
		{
			if(is_array($_GET['id']))
			{
				exit('{"status":"REQUEST_FAILED"}');
			}
			else
			{
				$this->setView("flash_error");
				$this->set('msg', '删除失败');
				$this->renderView();
			}
		}
	}
	
	//生成分类的xml文件
	public function updateXML()
	{
		$sort = Plite::modelFactory("sort");
		$deep = -1;
		$xml = array();
		$xml[] = "<?xml version='1.0' encoding='gb2312'?>";
		$xml[] = "<sorts>";
		foreach($sort->listTree() as $node)
		{
			if($node['deep'] <= $deep) $xml[count($xml)-1] .= "</sort>";
			$diff = $deep - $node['deep'];
			while($diff-- > 0) $xml[] = "</sort>";

			$xml[] = str_repeat("\t", $node['deep']) . sprintf("<sort id='%d' title='%s' deep='%d'>", $node['id'], $node['title'], $node['deep']);
			$deep = $node['deep'];
		}
		while($deep-- > -1)
			$xml[] = "</sort>";
		$xml[] = "</sorts>";
		file_put_contents(XLITE_SYS_TPL.DS."sort.xml", join("\r\n", $xml));
		$this->setView("flash_success");
		$this->set('msg', '更新sort.xml完毕.');
	}
}
?>