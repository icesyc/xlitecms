<?php
/**
 * ���³��������
 *
 * @author     ice_berg16(Ѱ�εĵ�����)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */

class C_update extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	//������ҳ�ĳ���
	public function updateIndex()
	{
		Plite::load('tplParser', Config::get("modelPath"));
		$tp = new tplParser(XLITE_APP_TPL.DS."index.htm");
		$res = $tp->result();
		file_put_contents("index.htm", $res);
		$this->setView("flash_success");
		$this->set('msg', '��ҳ���³ɹ�.');
	}

	//���ɷ����б�ҳ��
	public function updateList()
	{		
		$this->autoRender = false;
		$m     = $this->getModel();
		$timer = Plite::libFactory("Timer");
		//����ģ����
		Plite::load('tplParser', Config::get("modelPath"));
		$tp = new tplParser($m['tpl']);
		$tp->parse();

		if(empty($_GET['id']) && empty($_SESSION['__updateSortIdList__']))
			throw new Exception("����Ĳ�������.");
		list($cls,$sort) = Plite::modelFactory($m['class'], "sort");

		//���û�б���������µ��б��򱣴浽session
		if(empty($_SESSION['__updateSortIdList__']))
		{
			$sortId = is_array($_GET['id']) ? $_GET['id'] : $sort->getChild($_GET['id']);
			if(!is_array($sortId)) $sortId = array($sortId);
			$_SESSION['__updateSortIdList__'] = join(",", $sortId);
			$_SESSION['__totalSortNumber__'] = count($sortId);
			exit(sprintf("{status:'PROCESSING',total:%d,processed:0}", $_SESSION['__totalSortNumber__']));
		}
		
		//��ʼ����
		$idList = explode(",", $_SESSION['__updateSortIdList__']);
		foreach( $idList as $sortOffset => $sortId )
		{
			//ȡ�ø÷����������ӷ���
			$subSort = $sort->getChild($sortId);
			$currentNode = $sort->getNode($sortId);
			$positionNav = $sort->getAncestor($sortId);
			
			$tp->setData("currentSort", $currentNode);
			$tp->setData("positionNavigator", $positionNav);
			$child =  $sort->listTree($sortId, 1);
			//û���¼����࣬ȡͬ�������б�
			if(empty($child))
			{
				$parent = array_pop($positionNav);
				$parentId = $parent['id'];
				$child = $sort->listTree($parentId, 1);
			}

			//�Ƿ�ָ������Ŀ
			if($num = $tp->getData("sortNavNumber"))
				$child = array_slice($child, 0, $num);

			$tp->setData("currentSortNavigator",$child);
			$tp->setData('currentSortChild', $subSort);
			$sortWhere = sprintf('sort_id IN(%s)', join(",", $subSort));
			$res = $cls->findAll(array($sortWhere, $m['where']), $m['fields']);
			$total = !empty($res) ? $res[0]['total'] : 0;
			//ȡ�ø÷������ҳ��
			$p['recordCount'] = $total;
			$p['pageSize']	  = $tp->getData($m['psv']);
			$p['currentPage'] = empty($_SESSION['__pageOffset__']) ? 1 : $_SESSION['__pageOffset__'];
			$p['filePrefix'] = "list_";
			$sortPageNumber  = max(1, ceil($p['recordCount'] / $p['pageSize']));
			//ѭ������ÿ����ҳ�ļ�¼
			for($i=$p['currentPage']; $i<=$sortPageNumber; $i++)
			{
				$p['currentPage'] = $i;
				$tp->setData("pager", $p);
				file_put_contents(sprintf($m['toPage'], $sortId, $i), $tp->fillData());
				
				//����Ƿ�ʱ����ʱ���˳������µ�ǰδ������б�ͬʱ���ͼ������ź�
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
			//������÷��࣬��pageOffset���
			unset($_SESSION['__pageOffset__']);
		}
		
		//�����������ձ��������ͽ������ź�
		$processed = $_SESSION['__totalSortNumber__'];
		echo sprintf("{status:'PROCESS_OVER',total:%d,processed:%d,handler:function(){%s}}", $processed, $processed, $m['jsCode']);
		unset($_SESSION['__updateSortIdList__'], $_SESSION['__totalSortNumber__'], $_SESSION['__pageOffset__']);
	}
	
	//���ݷ�����������ҳҳ
	public function UpdateSortArticle()
	{
		$timer = Plite::libFactory("Timer");
		$this->autoRender = false;
		if(empty($_GET['id']) && empty($_SESSION['__updateArticleIdList__']))
			throw new Exception("����Ĳ�������.");
		list($art,$sort) = Plite::modelFactory("article", "sort");

		//���û�б���������µ��б��򱣴浽session
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
			else	//û�м�¼���˳�
				exit("{status:'PROCESS_OVER',total:0,processed:0,handler:function(){TRSelect.unselect()}}");
		}
		
		//��ʼ����
		$idList = explode(",", $_SESSION['__updateArticleIdList__']);
		foreach( $art->findAllById($idList) as $offset => $article )
		{
			//����Ƿ�ʱ����ʱ���˳������µ�ǰδ������б�ͬʱ���ͼ������ź�
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
		//�����������ձ��������ͽ������ź�
		$processed = $_SESSION['__totalArticleNumber__'];
		echo sprintf("{status:'PROCESS_OVER',total:%d,processed:%d,handler:function(){TRSelect.unselect()}}", $processed, $processed);
		unset($_SESSION['__updateArticleIdList__'], $_SESSION['__totalArticleNumber__']);
	}

	//����id�б��������ҳ��
	public function updateArticleById()
	{
		if(!isset($_GET['id']))	throw new Exception("δָ��id");
		$this->autoRender = false;
		$art = Plite::modelFactory("article");
		foreach( $art->findAllById($_GET['id']) as $article )
		{	
			$art->toHTML($article);
		}
		exit('{"status":"REQUEST_OK"}');
	}

	//�������ҳ��
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

			//λ�õ�����
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
	 * ȡ��ҵ��ģ������ �������ɷ����б�ҳ�� 
	 * ����һ��Ԫ��Ϣ�������� 
	 * classΪҵ���� tplΪ���ɾ�̬ҳ�����õ�ģ��ҳ�� 
	 * psvΪ��ҳ�Ĳ��� filePrefixΪ���ɾ�̬�б�ҳ��ǰ׺
	 * toPageΪ��̬ҳ���ļ��� ��Ҫ��sprintf($m['toPage'], $sortId, $i)��ʽ�����붯̬����
	 * countConditionΪ������������������
	 * jsCode �������ʱִ�е�JS����
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
				//δָ������ʱȡ�����з���
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