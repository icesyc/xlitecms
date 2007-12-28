<?php
/**
 * 更新程序控制器
 *
 * @author     ice_berg16(寻梦的稻草人)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */

class C_update extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	//更新首页的程序
	public function updateIndex()
	{
		Plite::load('tplParser', Config::get("modelPath"));
		$tp = new tplParser(XLITE_APP_TPL.DS."index.htm");
		$res = $tp->result();
		file_put_contents("index.htm", $res);
		$this->setView("flash_success");
		$this->set('msg', '首页更新成功.');
	}

	//生成分类列表页面
	public function updateList()
	{		
		$this->autoRender = false;
		$m     = $this->getModel();
		$timer = Plite::libFactory("Timer");
		//加载模板类
		Plite::load('tplParser', Config::get("modelPath"));
		$tp = new tplParser($m['tpl']);
		$tp->parse();

		if(empty($_GET['id']) && empty($_SESSION['__updateSortIdList__']))
			throw new Exception("错误的参数调用.");
		list($cls,$sort) = Plite::modelFactory($m['class'], "sort");

		//如果没有保存更新文章的列表，则保存到session
		if(empty($_SESSION['__updateSortIdList__']))
		{
			$sortId = is_array($_GET['id']) ? $_GET['id'] : $sort->getChild($_GET['id']);
			if(!is_array($sortId)) $sortId = array($sortId);
			$_SESSION['__updateSortIdList__'] = join(",", $sortId);
			$_SESSION['__totalSortNumber__'] = count($sortId);
			exit(sprintf("{status:'PROCESSING',total:%d,processed:0}", $_SESSION['__totalSortNumber__']));
		}
		
		//开始处理
		$idList = explode(",", $_SESSION['__updateSortIdList__']);
		foreach( $idList as $sortOffset => $sortId )
		{
			//取得该分类下所有子分类
			$subSort = $sort->getChild($sortId);
			$currentNode = $sort->getNode($sortId);
			$positionNav = $sort->getAncestor($sortId);
			
			$tp->setData("currentSort", $currentNode);
			$tp->setData("positionNavigator", $positionNav);
			$child =  $sort->listTree($sortId, 1);
			//没有下级分类，取同级分类列表
			if(empty($child))
			{
				$parent = array_pop($positionNav);
				$parentId = $parent['id'];
				$child = $sort->listTree($parentId, 1);
			}

			//是否指定了数目
			if($num = $tp->getData("sortNavNumber"))
				$child = array_slice($child, 0, $num);

			$tp->setData("currentSortNavigator",$child);
			$tp->setData('currentSortChild', $subSort);
			$sortWhere = sprintf('sort_id IN(%s)', join(",", $subSort));
			$res = $cls->findAll(array($sortWhere, $m['where']), $m['fields']);
			$total = !empty($res) ? $res[0]['total'] : 0;
			//取得该分类的总页数
			$p['recordCount'] = $total;
			$p['pageSize']	  = $tp->getData($m['psv']);
			$p['currentPage'] = empty($_SESSION['__pageOffset__']) ? 1 : $_SESSION['__pageOffset__'];
			$p['filePrefix'] = "list_";
			$sortPageNumber  = max(1, ceil($p['recordCount'] / $p['pageSize']));
			//循环查找每个分页的记录
			for($i=$p['currentPage']; $i<=$sortPageNumber; $i++)
			{
				$p['currentPage'] = $i;
				$tp->setData("pager", $p);
				file_put_contents(sprintf($m['toPage'], $sortId, $i), $tp->fillData());
				
				//检测是否超时，超时则退出，更新当前未处理的列表，同时发送继续的信号
				if($timer->getExecTime() > MAX_EXEC_TIME * 1000)
				{
					$_SESSION['__updateSortIdList__'] = join(",", array_slice($idList, $sortOffset));
					$_SESSION['__pageOffset__'] = $i+1;
					$processed = $_SESSION['__totalSortNumber__'] - count(array_slice($idList, $sortOffset));
					exit(sprintf("{status:'PROCESSING',total:%d,processed:%d}",
								$_SESSION['__totalSortNumber__'], $processed));
				}
				//if($sortOffset % 3 == 0) sleep(1);
			}
			//处理完该分类，将pageOffset清空
			unset($_SESSION['__pageOffset__']);
		}
		
		//处理结束，清空变量，发送结束的信号
		$processed = $_SESSION['__totalSortNumber__'];
		echo sprintf("{status:'PROCESS_OVER',total:%d,processed:%d,handler:function(){%s}}", $processed, $processed, $m['jsCode']);
		unset($_SESSION['__updateSortIdList__'], $_SESSION['__totalSortNumber__'], $_SESSION['__pageOffset__']);
	}
	
	//根据分类生成文章页页
	public function UpdateSortArticle()
	{
		$timer = Plite::libFactory("Timer");
		$this->autoRender = false;
		if(empty($_GET['id']) && empty($_SESSION['__updateArticleIdList__']))
			throw new Exception("错误的参数调用.");
		list($art,$sort) = Plite::modelFactory("article", "sort");

		//如果没有保存更新文章的列表，则保存到session
		if(empty($_SESSION['__updateArticleIdList__']))
		{
			$sortId = is_array($_GET['id']) ? $_GET['id'] : $sort->getChild($_GET['id']);
			$artList = $art->findAllBySortId($sortId, 'id');
			if(!empty($artList))
			{
				foreach($artList as $k => $v)
					$idList[] = $v['id'];
				$_SESSION['__updateArticleIdList__'] = join(",", $idList);
				$_SESSION['__totalArticleNumber__'] = count($idList);
				exit(sprintf("{status:'PROCESSING',total:%d,processed:0}", $_SESSION['__totalArticleNumber__']));
			}
			else	//没有记录，退出
				exit("{status:'PROCESS_OVER',total:0,processed:0,handler:function(){TRSelect.unselect()}}");
		}
		
		//开始处理
		$idList = explode(",", $_SESSION['__updateArticleIdList__']);
		foreach( $art->findAllById($idList) as $offset => $article )
		{
			//检测是否超时，超时则退出，更新当前未处理的列表，同时发送继续的信号
			if($timer->getExecTime() > MAX_EXEC_TIME * 1000)
			{
				$_SESSION['__updateArticleIdList__'] = join(",", array_slice($idList, $offset));
				$processed = $_SESSION['__totalArticleNumber__'] - count(array_slice($idList, $offset));
				exit(sprintf("{status:'PROCESSING',total:%d,processed:%d}",
							$_SESSION['__totalArticleNumber__'], $processed));
			}
			$art->toHTML($article);
			//if($offset == 10) sleep(1);
		}
		//处理结束，清空变量，发送结束的信号
		$processed = $_SESSION['__totalArticleNumber__'];
		echo sprintf("{status:'PROCESS_OVER',total:%d,processed:%d,handler:function(){TRSelect.unselect()}}", $processed, $processed);
		unset($_SESSION['__updateArticleIdList__'], $_SESSION['__totalArticleNumber__']);
	}

	//根据id列表更新文章页面
	public function updateArticleById()
	{
		if(!isset($_GET['id']))	throw new Exception("未指定id");
		$this->autoRender = false;
		$art = Plite::modelFactory("article");
		foreach( $art->findAllById($_GET['id']) as $article )
		{	
			$art->toHTML($article);
		}
		exit('{"status":"REQUEST_OK"}');
	}

	//生成相册页面
	public function updateGallery()
	{
		$this->autoRender = false;
		list($g, $i, $s) = Plite::modelFactory("gallery", "image", "sort");
		if(!is_array($_GET['id'])) $_GET['id'] = array($_GET['id']);
		foreach($_GET['id'] as $id)
		{
			$gallery = $g->get($id);
			$img = $i->findAllByGalleryId($id);
			$p['recordCount'] = $gallery['pic_number'];
			$p['pageSize']	  = 1;
			$p['filePrefix']  = "";
			$p['tpl'] = file_get_contents(XLITE_SYS_TPL.DS."galleryPager.tpl");
			Plite::load('tplParser', Config::get("modelPath"));
			$tp = new tplParser(XLITE_APP_TPL.DS."gallery.htm");
			$tp->parse();

			//位置导航条
			$tp->setData("positionNavigator", $s->getAncestor($gallery['sort_id']));
			$tp->setData("currentSort", $s->getNode($gallery['sort_id']));

			for($j=0;$j<$gallery['pic_number'];$j++)
			{
				
				$img[$j]['galleryTitle'] = $gallery['title'];
				$p['currentPage'] = $j+1;
				$tp->setData("pager", $p);
				$tp->setData("image", $img[$j]);
				file_put_contents($g->p($gallery['id']).DS.($j+1).".htm", $tp->result());
			}			
		}

		exit('{"status":"REQUEST_OK"}');
	}

	/*
	 * 取得业务模型数据 用于生成分类列表页面 
	 * 返回一个元信息关联数组 
	 * class为业务类 tpl为生成静态页面所用的模板页面 
	 * psv为分页的参数 filePrefix为生成静态列表页的前缀
	 * toPage为静态页的文件名 需要用sprintf($m['toPage'], $sortId, $i)方式来输入动态参数
	 * countCondition为计算总数的条件参数
	 * jsCode 处理结束时执行的JS代码
	 * @return $m array 
	 */
	private function getModel()
	{
		$model = !isset($_GET['model']) ? 'article' : $_GET['model'];
		switch($model)
		{
			case 'article':
				$m['class'] = 'article';
				$m['tpl']	= XLITE_APP_TPL.DS."sortList.htm";
				$m['psv']	= 'articlePageSize';	//page size variable
				$m['filePrefix'] = "list_";
				$m['toPage']= ARTICLE_PATH.DS."%d".DS.$m['filePrefix']."%d.htm";				
				$m['fields'] = "count(*) AS total";
				$m['where'] = "is_audit=1";
				$m['jsCode'] = "TRSelect.unselect()";
				break;
			case 'gallery':
				$m['class'] = 'gallery';
				$m['tpl']	= XLITE_APP_TPL.DS."galleryList.htm";
				$m['psv']	= 'galleryPageSize';	//page size variable
				$m['filePrefix'] = "gallery_list_";
				$m['toPage']= ARTICLE_PATH.DS."%d".DS.$m['filePrefix']."%d.htm";
				$m['fields'] = "count(*) AS total";
				$m['where']  = null;
				$m['jsCode'] = "";
				//未指定分类时取出所有分类
				if(empty($_GET['id']))
				{
					$_GET['id'] = array();
					$all = Plite::modelFactory("sort")->listTree();
					foreach( $all as $s )
						array_push($_GET['id'], $s['id']);
					if(empty($_GET['id']))
						die("{status:'PROCESS_OVER',total:0,processed:0}");
				}
				break;
		}
		return $m;
	}
}
?>