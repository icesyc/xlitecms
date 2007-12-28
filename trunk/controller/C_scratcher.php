<?php
/**
 * scratcher 控制器
 *
 * @author     ice_berg16(寻梦的稻草人)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */

class C_scratcher extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->setLayout('head', 'admin_head');
	}
	
	//列表
	public function index()
	{
		$condition['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
		$condition['limit'] = PAGE_SIZE;
		$scratcher = plite::modelFactory("scratcher");
		$ruleList = $scratcher->find($condition);
		$pager = Plite::libFactory("Pager");
		$pager->setParam($scratcher->getPageParam());
		$pager->setFormat(file_get_contents(XLITE_SYS_TPL . "/pager.tpl"));
		
		$noRecord = count($ruleList) > 0 ? 'none' : '';
		$this->set('noRecord', $noRecord);
		$this->set('ruleList', $ruleList);
		$this->set('pager', $pager->makePage());
		$_SESSION['__listScratcherURL__'] = $_SERVER['REQUEST_URI'];
	}

	//保存采集规则
	public function save()
	{
		$scratcher = Plite::modelFactory("scratcher");
		if($this->isPost())
		{
			if($_POST['is_rss'])	$_POST['article_url'] = "从RSS文件中读取";
			$_POST['is_rss'] = empty($_POST['is_rss']) ? 0 : 1;
			if(!isset($_POST['enable_cookie'])) $_POST['cookie'] = '';
			if(empty($_POST['get_number'])) $_POST['get_number'] = 0;
			//清除session数据
			$this->clearSessionData();
			if($scratcher->save($_POST))
			{
				//编辑
				if(!empty($_POST['id']))
				{
					$url = $_SESSION['__listScratcherURL__'];
					unset($__SESSION['__listScratcherURL__']);
					$this->redirect($url);
				}
				else
				{
					$this->setView("flash_success");
					$this->set('msg', '采集规则保存成功');
				}
			}
			else
				throw new Exception("采集规则保存失败");
		}
		else
		{	
			$id  = isset($_GET['id']) ? $_GET['id'] : null;
			$data = $scratcher->get($id);
			if(!empty($_GET['id']))
			{
				$data['act'] = '编辑';
			}
			else
			{
				$data['act'] = '添加';
			}
			if(!empty($_GET['clone']))
			{
				$data['id'] = "";
				$data['act'] = "添加";
			}
			$sort = Plite::modelFactory("sort");
			$data['sortList'] = $sort->format($sort->listTree(),'select');
			$this->set($data);
		}
	}

	//删除
	public function delete()
	{
		try
		{
			$this->autoRender = false;
			$scratcher = Plite::modelFactory("scratcher");
			if(empty($_GET['id'])) throw new Exception("未指定id");
			$scratcher->delete($_GET['id']);
			//删除缓存
			$scratcher->clearCache($_GET['id']);

			if(is_array($_GET['id']))
				exit('{"status":"REQUEST_OK"}');
			else
				$this->redirect($_SERVER['HTTP_REFERER']);
		}
		catch(Exception $e)
		{
			$this->setView("flash_error");
			$this->set('msg', $e->getMessage());
		}
	}

	//删除缓存
	function clearCache()
	{
		$this->autoRender = false;
		if(empty($_GET['id'])) throw new Exception("未指定id");
		$scratcher = Plite::modelFactory("scratcher");
		if($scratcher->clearCache($_GET['id']))
			exit('{"status":"REQUEST_OK"}');
		else
			exit('{"status":"REQUEST_FAILED"}');
	}

	//测试
	public function test()
	{
		if(empty($_GET['id'])) throw new Exception("未指定id");
		//先清除以前的数据
		$this->clearSessionData();

		$_SESSION['__scratcherRuleId__'] = $_GET['id'];
		$scratcher = Plite::modelFactory("scratcher");
		$data = $scratcher->get($_GET['id']);
		$sort = Plite::modelFactory("sort");
		$data['sortList'] = $sort->format($sort->listTree(),'select');
		$this->set($data);
	}

	//采集列表
	public function scratchList()
	{
		$this->autoRender = false;
		if(empty($_GET['id'])) throw new Exception("未指定id");
		$scratcher = Plite::modelFactory("scratcher");
		//检查是否有列表文件，
		if($scratcher->hasCache($_GET['id']))
		{
			$xml = simplexml_load_file($scratcher->f('index',$_GET['id']));
			exit(sprintf(
		"{status:'PROCESS_OVER',total:%d,processed:%d,handler:function(){jump('scratcher','scratchPage','id=%d')}}",
				$xml["page"], $xml["page"], $_GET['id']
			));
		}
		$scratcher->scratchList($_GET['id']);
	}

	//采集具体页面
	public function scratchPage()
	{
		$this->autoRender = false;
		if(empty($_GET['id'])) throw new Exception("未指定id");		
		$scratcher = Plite::modelFactory("scratcher");

		if(!empty($_GET['scratch']))
		{
			//$scratcher->setDebug();
			$scratcher->scratchPage($_GET['id']);
		}
		else
		{
			$rule = $scratcher->get($_GET['id']);
			if(!$xml = simplexml_load_file($scratcher->f('index', $_GET['id']))) die();			
			$param['currentPage'] = isset($_GET['page']) ? $_GET['page'] : 1;
			$param['pageSize'] = 20;
			$param['recordCount'] = $xml['links'];			
			$pager = Plite::libFactory("Pager", array($param));
			$pager->setFormat(file_get_contents(XLITE_SYS_TPL.DS."pager.tpl"));
			$offset = $param['pageSize'] * ($param['currentPage'] - 1);
			//输出链接
			$linkList = array();
			$i = 0;
			foreach($xml->link as $l)
			{
				$i++;
				if($i <= $offset) continue;
				if($i > $offset + $param['pageSize']) break;
				$linkList[] = array(
							"id"	=> $i,
							"title" => iconv("UTF-8", "GBK", $l["title"]),
							"url"	=> iconv("UTF-8", "GBK", $l["url"])
						 );
				
			}
			$this->set('id', $rule['id']);
			$this->set('name', $rule['name']);
			$this->set('page', $xml['page']);
			$this->set('links', $xml['links']);
			$this->set('total_save', $rule['total_save']);
			$this->set('linkList', $linkList);
			$this->set('noRecord', count($linkList) > 0 ? 'none' : '');
			$this->set('pager', $pager->makePage());
			$this->renderView();
		}
	}

	//保存到数据库
	function saveToDB()
	{
		$this->autoRender = false;
		if(empty($_GET['id'])) throw new Exception("未指定id");		
		$scratcher = Plite::modelFactory("scratcher");
		$scratcher->saveToDB($_GET['id']);
	}

	//----------- 断点调试部分的函数 --------------------

	//取得列表URL并返回
	public function debugListURL()
	{
		$this->autoRender = false;
		if(!isset($_GET['list_url'])) throw new Exception("未指定url");
		$scratcher = Plite::modelFactory("scratcher");
		$rule	   = $scratcher->get($_SESSION['__scratcherRuleId__']);
		$content = $scratcher->debugListURL($_GET['list_url']);		

		$_SESSION['__listContent__'] = $content;

		$content = $scratcher->convertCharset($content, $rule['charset']);
		echo $content;
	}
	
	//测试列表截取功能
	public function debugListSplit()
	{
		$this->autoRender = false;
		if(!isset($_SESSION['__listContent__'])) exit("没有列表内容.");
		//是否需要缩小列表范围
		$lbs = iconv("UTF-8", "GBK//IGNORE", $_GET['lbs']);
		$las = iconv("UTF-8", "GBK//IGNORE", $_GET['las']);
		if(empty($lbs) || empty($las)) die("前后字符串均不能为空");
		//如果需要，将列表内容进行编码转换
		$scratcher = Plite::modelFactory("scratcher");
		$rule	   = $scratcher->get($_SESSION['__scratcherRuleId__']);
		$content   = $scratcher->convertCharset($_SESSION['__listContent__'], $rule['charset']);
		$arr = explode($lbs, $content);
		$content = $arr[0];
		if(isset($arr[1]))
		{
			$arr = explode($las, $arr[1]);
			$content = $arr[0];
		}
		echo $content;
	}

	//测试从列表内容中取得文章链接列表
	public function debugArticleURL()
	{
		$this->autoRender = false;
		if(!isset($_GET['article_url'])) throw new Exception("未指定articleURL");		
		if(!isset($_SESSION['__listContent__'])) exit("没有列表内容.");

		$scratcher = Plite::modelFactory("scratcher");
		$rule      = $scratcher->get($_SESSION['__scratcherRuleId__']);
		//读RSS
		if($rule['is_rss'])
		{
			$rss = @simplexml_load_string($_SESSION['__listContent__']);
			foreach($rss->channel->item as $item )
			{
				$save = true;
				$title = iconv("UTF-8", "GBK//IGNORE", $item->title);
				if(!empty($rule['keyword']))
				{
					$reg = "#".str_replace(",", "|", $rule['keyword'])."#i";
					if(!preg_match($reg, $title, $mm)) $save = false;
				}
				if($save)
					$res[] = sprintf("<item link='%s' title='%s'/>", $item->link, $title);
			}
		}
		else	//HTML代码中提取
		{
			$content = $_SESSION['__listContent__'];
			$reg = "#<a[^>]+href=(['\"]?)(".$scratcher->escapePattern($_GET['article_url']).")\\1[^>]*>(.*)<\/a>#isU";
			preg_match_all($reg, $content, $m, PREG_SET_ORDER);
			if(empty($m)) exit;			
			$links = array();
			foreach( $m as $link )
			{
				if(in_array($link[2], $links)) continue;
				$link[0] = $scratcher->convertCharset($link[0], $rule['charset']);
				$link[3] = $scratcher->convertCharset($link[3], $rule['charset']);
				$save = true;
				if(!empty($rule['keyword']))
				{
					$reg = "#".str_replace(",", "|", $rule['keyword'])."#i";
					if(!preg_match($reg, $link[3], $mm)) $save = false;
				}
				if($save)
					$res[] = $link[0];
				array_push($links, $link[2]);
			}
		}
		echo join("\r\n", $res);
	}
	
	//从链接列表取出一条链接获取内容
	public function debugArticle()
	{
		$this->autoRender = false;
		if(!isset($_GET['link']) || !isset($_GET['list_url'])) throw new Exception("参数错误.");
		$scratcher = Plite::modelFactory("scratcher");		
		$rule	   = $scratcher->get($_SESSION['__scratcherRuleId__']);
		$content = $scratcher->debugArticle($_GET['link'], $_GET['list_url']);
		if($rule['charset'] == 'UTF-8')
			$content = iconv("UTF-8", "GBK//IGNORE", $content);
		$_SESSION['__articleContent__'] = $content;
		echo $_SESSION['__articleContent__'];
	}

	//测试各个模式
	public function debugPattern()
	{
		$this->autoRender = false;
		if(!isset($_SESSION['__articleContent__'])) exit("没有相关的文章内容");
		$content = $_SESSION['__articleContent__'];
		if(!isset($_GET['pattern'])) throw new Exception("未指定规则");

		$scratcher = Plite::modelFactory("scratcher");
		//由xmlhttp提交的中文为UTF8，所以要转换，实际采集时不需要进行编码转换
		$pattern = $scratcher->escapePattern(iconv("UTF-8", "GBK//IGNORE", $_GET['pattern']));
		$reg = '#'.$pattern.'#isU';
		preg_match($reg, $content, $out);
		echo !empty($out) ? $out[1] : "";
	}


	//导出规则
	public function export()
	{
		$this->autoRender = false;
		if(empty($_GET['id'])) throw new Exception('未指定id');
		$scratcher = Plite::modelFactory("scratcher");
		$rules = $scratcher->findAllById($_GET['id']);
		$xml = '<?xml version="1.0" encoding="gbk"?>';
		$xml.= '<rules>';

		$skip = array('id', 'total_save', 'last_modified_time');
		foreach( $rules as $rule )
		{
			$xml .= "<rule>";
			foreach( $rule as $k => $v )
			{
				if(in_array($k, $skip)) continue;
				$xml .= sprintf("<%s>%s</%1\$s>", $k, base64_encode($v));
			}
			$xml .= "</rule>";
		}
		$xml.= '</rules>';
		$fn  = "rules_export.xml";
		header("Content-type: text/xml");
		header("Content-Length: " . strlen($xml) );
		header("Content-Disposition: attachment; filename=\"$fn\"" );
		header('Content-Transfer-Encoding: binary' );
		echo $xml;
	}

	//导入规则
	public function import()
	{
		$scratcher= Plite::modelFactory("scratcher");
		$savePath = SCRATCHER_RESOURCE_PATH;
		$file = Plite::libFactory("FileUploader")->getFile('rules')->move($savePath, UploadFile::FILE_RANDOM_NAME);
		if(!preg_match("#\.xml$#i", basename($file))) die("文件不是有效的XML文件");
		$rules = @simplexml_load_file($file);
		foreach( $rules->rule as $rule )
		{
			foreach( $rule->children() as $k => $v )
			{
				$r[$k] = base64_decode($v);
			}
			$scratcher->save($r);
		}
		//删除上传的文件
		@unlink($file);
		$this->redirect($_SESSION['__listScratcherURL__']);
	}

	//清除一些session数据
	private function clearSessionData()
	{
		if(isset($_SESSION['__scratcherRuleId__']))
			unset($_SESSION['__scratcherRuleId__']);
		if(isset($_SESSION['__listContent__']))
			unset($_SESSION['__listContent__']);
		if(isset($_SESSION['__articleContent__']))
			unset($_SESSION['__articleContent__']);
	}
}
?>