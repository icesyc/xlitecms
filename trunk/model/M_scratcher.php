<?php
//====================================================
//		FileName:M_scratcher.php
//		Summary: 采集规则model
//		Author: ice_berg16(寻梦的稻草人)
//		LastModifed:2006-09-28
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
class M_scratcher extends model
{
	
	protected $table = 'scratcher';
	//采集规则
	private $rule;
	//httpClient
	private $http;
	//计时器
	private $timer;

	//保存资源链接的数组
	private $resLink = array();
	//调试标识
	private $dbg; 

	//查找函数
	public function find($condition)
	{
		$condition['fields'] = "s1.id, s1.name, s1.last_modified_time, s1.total_save, s1.is_rss, s2.title AS sort";
		$condition['from'] = $this->fullTableName . " AS s1";
		$condition['join'] = array("table" => Config::get("tablePrefix") . "sort AS s2", "on" => "s1.save_sort_id=s2.id");
		$condition['order'] = "s1.id DESC";
		$condition['count'] = true;
		$this->setFilter(array($this, 'filter'), self::FILTER_FIND);
		return parent::_query($condition);
	}

	public function filter($row)
	{
		if($row['last_modified_time'] == 0)
			$row['last_modified_time'] = '从未采集';
		else
			$row['last_modified_time'] = date("Y-m-d", $row['last_modified_time']);
		$row['is_rss'] = $row['is_rss'] ? "<img src='images/rss.gif' align='absmiddle' alt='RSS地址'/>" : '';
		return $row;
	}

	//采集初始化
	public function scratchInit($id)
	{
		$this->rule = $this->get($id);	
		$this->URL = $this->parseListURL($this->rule['list_url']);
		$this->http = Plite::libFactory("HttpClient", array($this->URL['host'], $this->URL['port']), "HttpClient.class.php");
		//是否启用cookie
		if(!empty($this->rule['cookie'])) $this->http->setCookies($this->rule['cookie']);
		$this->timer = Plite::libFactory("Timer");
	}

	//采集列表
	public function scratchList($id)
	{	
		$this->scratchInit($id);

		//初始化目录
		if(!is_dir($this->path())) mkdir($this->path());

		//循环取得列表页
		$info = $this->URL;
		$pn = $info['endPage'] - $info['startPage'] + 1;
		//先输出要采集的页数
		if(!isset($_SESSION['__scratchTotalPage__']))
		{
			$_SESSION['__scratchTotalPage__']  = $pn;
			file_put_contents($this->f(), "");
			exit(sprintf("{status:'PROCESSING',total:%d,processed:0}",$pn));
		}

		if(isset($info['startPage']) && isset($info['endPage']))
		{	
			$currentPage = !empty($_SESSION['__scratchPageOffset__']) 
						 ? $_SESSION['__scratchPageOffset__'] : $info['startPage'];
			$index		 = !empty($_SESSION['__scratchPageOffset__'])
						 ? $_SESSION['__scratchPageOffset__'] - $currentPage + 1 : 1;
		
			$count = !empty($_SESSION['__totalLinks__']) ? $_SESSION['__totalLinks__'] : 0;
			while($currentPage <= $info['endPage'])
			{
				//检测是否超时，超时则退出，更新当前未处理的列表，同时发送继续的信号
				if($this->timer->getExecTime() > MAX_EXEC_TIME * 1000)
				{
					$_SESSION['__scratchPageOffset__'] = $currentPage;
					$_SESSION['__totalLinks__'] = $count;
					exit(sprintf("{status:'PROCESSING',total:%d,processed:%d}",
								$_SESSION['__scratchTotalPage__'], $currentPage));
				}
				//取得列表内容
				$path = preg_replace("/\[\d+\-\d+\]/", $currentPage, $info['path']);
				$this->http->get($path);
				$listContent = $this->http->getContent();
				//从列表内容中分析文章列表并保存
				$count += $this->saveListFromContent($listContent, $index);
				unset($listContent);
				$currentPage++;
				$index++;
			}
			//读取列表完毕,将列表和链接数目信息写入文件
			$tpl = "<?xml version='1.0' encoding='gbk'?>\r\n<rule id='%d' page='%d' links='%d'>\r\n%s</rule>";			
			$content = file_get_contents($this->f());
			$content = sprintf($tpl, $this->rule['id'], $pn, $count, $content);
			file_put_contents($this->f(), $content);
			
			//输出结束信息,结果中包要跳转的页面信息
			echo sprintf("{status:'PROCESS_OVER',total:%d,processed:%1\$d,handler:function(){jump('scratcher','scratchPage','id=%d')}}",
								$_SESSION['__scratchTotalPage__'], $this->rule['id']);
			unset($_SESSION['__scratchPageOffset__'], $_SESSION['__scratchTotalPage__'], $_SESSION['__totalLinks__']);
		}
	}	

	public function saveListFromContent($content)
	{
		if($this->rule['is_rss'])
			return $this->saveListFromRss($content);
		else
			return $this->saveListFromHTML($content);
	}

	/*
	 * 保存列表中的网址 HTML文件
	 *
	 * @param $content string 列表的内容
	 * @return 该列表中获得的链接数
	 */
	public function saveListFromHTML($content)
	{
		//html如果为utf8需要转码,rss不需要转码
		$content = $this->convertCharset($content);
		//是否需要缩小列表范围
		if(!empty($this->rule['list_before_string']) && !empty($this->rule['list_after_string']))
		{
			$arr = explode($this->rule['list_before_string'], $content);
			$content = $arr[0];
			if(isset($arr[1]))
			{
				$arr = explode($this->rule['list_after_string'], $arr[1]);
				$content = $arr[0];
			}
			unset($arr);
		}
		$reg = "#<a[^>]+href=(['\"]?)(".$this->escapePattern($this->rule['article_url']).")\\1[^>]*>(.*)<\/a>#isU";
		preg_match_all($reg, $content, $m, PREG_SET_ORDER);
		if(empty($m)) return false;
		$list = "";
		//保存链接地址的数组，用来过滤重复链接
		$links =array();	
		$cnt = 0;
		foreach( $m as $rec )
		{
			//链接存在
			if(in_array($rec[2], $links)) continue;
			//分析是否为图片链接，图片链接则自动分析alt
			if(preg_match("#^\s*<img[^>]+alt=(['\"])(.+)\\1[^>]*>\s*$#isU", $rec[3], $out))
			{
				$title = $out[2];
			}
			else
				$title =  htmlspecialchars(strip_tags($rec[3]));

			//检查关键词
			$save = true;
			if(!empty($this->rule['keyword']))
			{
				$reg = "#".str_replace(",", "|", $this->rule['keyword'])."#i";
				if(!preg_match($reg, $title, $mm)) $save = false;
			}
			if($save)
			{
				$list .= sprintf("<link url='%s' title='%s'/>\r\n", $rec[2], $title);
				$cnt++;
			}
			array_push($links, $rec[2]);
		}
		file_put_contents($this->f(), $list, FILE_APPEND);
		return $cnt;
	}

	/*
	 * 保存列表中的网址 RSS文件
	 *
	 * @param $content string 列表的内容
	 * @param $page 当前处理的页
	 * @return 该列表中获得的链接数
	 */
	public function saveListFromRss($content)
	{
		$rss = simplexml_load_string($content);
		$list = "";
		$links = array();
		$cnt = 0;
		foreach( $rss->channel->item as $item )
		{
			$url   = (string)$item->link;
			$date  = (string)$item->pubDate;
			//过滤重复链接
			if(in_array($url, $links)) continue;
			$title = iconv("UTF-8", "GBK//IGNORE", $item->title);
			$save = true;
			if(!empty($this->rule['keyword']))
			{
				$reg = "#".str_replace(",", "|", $this->rule['keyword'])."#i";
				if(!preg_match($reg, $title, $mm)) $save = false;
			}
			if($save)
			{
				$list .= sprintf("<link url='%s' title='%s' pubDate='%s'/>\r\n", $url, $title, $date);
				$cnt++;
			}
			array_push($links, $url);
		}
		file_put_contents($this->f(), $list, FILE_APPEND);
		return $cnt;
	}

	/*
	 * 采集详细页面
	 * 该函数必须在调用scratchList之后进行调用
	 *
	 * @param $id int 规则id
	 * 
	 */
	public function scratchPage($id)
	{
		$this->scratchInit($id);

		//文件不存在则跳出
		if(!file_exists($this->f())) return;

		//取得该规则的页面和链接信息
		if(!$xml = simplexml_load_file($this->f())) die('加载xml文件错误->'.$this->f());

		//所有链接数
		$_SESSION['__totalLinks__'] = intval($xml['links']);

		//保存已经处理的链接数
		if(!isset($_SESSION['__processedLinks__']))
		{
			$_SESSION['__processedLinks__'] = 0;
			exit(sprintf("{status:'PROCESSING',total:%d,processed:0}", $_SESSION['__totalLinks__']));
		}
		$realOffset = $_SESSION['__processedLinks__'] + 1;
		
		$i = 0;
		foreach($xml->link as $lnk)
		{
			if(++$i < $realOffset) continue;
			$this->getDetail($lnk['url']);
		}

		//抓取结束，释放session
		$processed = $_SESSION['__totalLinks__'];
		echo sprintf("{status:'PROCESS_OVER',total:%d,processed:%1\$d,handler:function(){Scratcher.AfterScratchPage(%d);}}",
					$processed, $this->rule['id']);
		unset($_SESSION['__processedLinks__'], $_SESSION['__totalLinks__']);

		//更新已保存的记录数和采集时间
		$sql = sprintf("UPDATE %s SET total_save=total_save+%d, last_modified_time=%d WHERE id=%d",
			$this->fullTableName, $processed, time(), $id);
		return $this->DB->exec($sql);
	}

	//取得文章详细页面
	public function getDetail($link)
	{
		//检测是否超时，超时则退出，更新当前未处理的列表，同时发送继续的信号
		if($this->timer->getExecTime() > MAX_EXEC_TIME * 1000)
		{
			exit(sprintf("{status:'PROCESSING',total:%d,processed:%d}",
						$_SESSION['__totalLinks__'], $_SESSION['__processedLinks__']));
		}
		
		//采集达到目标数
		if($this->rule["get_number"] != 0 && $_SESSION['__processedLinks__'] >= $this->rule["get_number"])
		{
			$processed = $_SESSION['__processedLinks__'];
			echo sprintf("{status:'PROCESS_OVER',total:%d,processed:%1\$d,handler:function(){Scratcher.AfterScratchPage(%d);}}",
							$processed, $this->rule['id']);
			unset($_SESSION['__processedLinks__'], $_SESSION['__totalLinks__']);
			//更新已保存的记录数
			$this->update(array("id"=>$id, "total_save" => $processed));
		}
		
		//解析URL
		$url = $this->parseURL($link);

		//判断是不是绝对路径，不是则补上列表的路径
		if(substr($url['path'],0,1) != "/")
		{
			$path = dirname($this->URL['path']);
			if(strlen($path) == 1) $path = "";
			$url['path'] = $path ."/".$url['path'];
		}
		$this->http->get($url['path']);
		$content = $this->convertCharset($this->http->getContent());

		//echo $content;
		//循环匹配各个字段
		foreach( $this->rule as $k => $p )
		{
			if(strpos($k, "_pattern") === false) continue;
			$field = substr($k, 0, strlen($k)-8);
			$match[$field] =  "";
			if(!empty($p))
			{
				$reg = "#".$this->escapePattern($p)."#isU";
				preg_match($reg, $content, $m);
				$match[$field] = !empty($m) ? $m[1] : "";
			}
		}
		
		//是否下载图资源
		if($this->rule['save_resource'])
			$match['content'] = $this->saveResource($match['content']);
		$this->debugInfo("匹配结果", $match["title"]);
		$match['sort_id'] = $this->rule['save_sort_id'];
		$match['file'] = $this->rule['id'] . "_" . ($_SESSION['__processedLinks__']+1);
		$this->saveDetail($match);
		//处理计数加1
		$_SESSION['__processedLinks__']++;
	}

	//保存成XML文件
	public function saveDetail($data)
	{
		$xml = "<?xml version='1.0' encoding='gbk'?><article>";
		foreach($data as $f => $v )
		{
			//使用base64编码防止由xml加载错误
			$xml .= sprintf("<%s>%s</%1\$s>", $f, base64_encode($v));
		}
		$xml .= "</article>";
		file_put_contents($this->f($data['file']), $xml);
	}

	//保存资源
	public function saveResource($content)
	{
		//下载图片,flash,视频音频等
		$mediaReg = array("#<img[^>]+src=(['\"])(.+)\\1#isU",	//图片
					"#<param name=['\"]movie['\"] value=(['\"])(.+)\\2#isU",	//flash
					"#<param name=['\"]FileName['\"] value=(['\"])(.+)\\2#isU",	//wmp控件
					"#<param name=['\"]SRC['\"] value=(['\"])(.+)\\2#isU"	//real控件
					);
		return preg_replace_callback($mediaReg, array($this, 'saveCallback'), $content);
	}
	
	/*
	 * 保存资源的回调函数
	 *
	 * @param $m array 匹配到的数组
	 * @return 替换后的路径
	 */
	public function saveCallback($m)
	{	
		//解析URL
		$url = $this->parseURL($m[2]);
		//取得扩展名
		$ext  = substr($m[2], strrpos($m[2], ".")); //得到图片扩展名
		//用当前的时间做文件名
		$fname = microtime(true).$ext;
		//判断是不是绝对路径，不是则补上列表的路径
		if(substr($url['path'],0,1) != "/")
			$url['path'] = dirname($this->URL['path'])."/".$url['path'];		
		
		//取得路径
		$path = $this->rpath($ext).DS.$fname;		
		$npath = $this->rpath($ext,2).DS.$fname;

		//存在该图片，直接返回
		if(array_key_exists(md5($m[2]), $this->resLink))
		{
			return str_replace($m[2], $npath, $m[0]);
		}
		
		//http地址
		if(substr($m[2], 0, 7) == "http://")
			$file = file_get_contents($m[2]);
		else
		{
			$this->http->get($url['path']);
			$file = $this->http->getContent();
		}
		file_put_contents($path, $file);
		
		//保存链接
		$this->resLink[md5($m[2])] = $npath;
		return str_replace($m[2], $npath, $m[0]);
	}
	
	//将采集内容入库
	public function saveToDB($id)
	{		
		//取得要入库的链接数
		$index = $this->f('index', $id);
		$rule  = @simplexml_load_file($index) or die('加载xml文件出错->'.$index);
		$timer = Plite::libFactory("Timer");

		if((int)$rule['links'] == 0)	exit("{status:'PROCESS_OVER',total:0,processed:0,handler:function(){Scratcher.enableBtn()}}");

		//第一次处理，先报告要处理的数目
		if(!isset($_SESSION['__processedFiles__']))
		{
			$_SESSION['__processedFiles__'] = 0;			
			//取得链接的总数
			$_SESSION['__totalFiles__'] = (int)$rule['links'];
			exit(sprintf("{status:'PROCESSING',total:%d,processed:0}", $_SESSION['__totalFiles__']));
		}
		
		//当前偏移量
		$offset = $_SESSION['__processedFiles__'] + 1;

		$art = Plite::modelFactory("article");
		//循环读取文章的xml文件
		$dir = dir($this->path($id));
		$i = 0;
		while(($f = $dir->read()) !== false)
		{			
			//跳过索引文件
			if($f == "." || $f == ".." || $f == "index.xml") continue;
			//检测是否超时，超时则退出，同时发送继续的信号
			if($timer->getExecTime() > MAX_EXEC_TIME * 1000)
			{
				$_SESSION['__fileOffset__'] = $i;
				exit(sprintf("{status:'PROCESSING',total:%d,processed:%d}",
							$_SESSION['__totalFiles__'], $_SESSION['__processedFiles__']));
			}
			if(++$i < $offset) $continue;
			if(!$article = @simplexml_load_file($dir->path.DS.$f)) die('加载xml文件出错->'.$dir->path.DS.$f);
			//对象转换为数组
			foreach($article as $f => $v )
			{
				//解码
				$data[$f] = base64_decode($v);
			}
			//将字符串日期转换为时间戳
			$data['post_time'] = empty($data['post_time']) ? time() : strtotime($data['post_time']);
			if(empty($data['post_time']))
				$data['post_time'] = time();
			$art->save($data);
		}

		echo sprintf("{status:'PROCESS_OVER',total:%d,processed:%1\$d,handler:function(){Scratcher.enableBtn()}}", $_SESSION['__totalFiles__']);
		unset($_SESSION['__processedFiles__'], $_SESSION['__totalFiles__']);
	}

	/*
	 *  取得列表的页面内容，用于测试
	 *
	 * @param $url string URL地址
	 * @id int 规则id
	 */
	public function debugListURL($url)
	{
		$rule = $this->get($_SESSION['__scratcherRuleId__']);
		$url = $this->parseListURL($url);
		$http = Plite::libFactory("HttpClient", array($url['host'], $url['port']), "HttpClient.class.php");
		//是否启用cookie;
		if(!empty($rule['cookie']))	$http->setCookies($rule['cookie']);

		//取第一页
		$page = $url['startPage'];
		$path = preg_replace("/\[\d+\-\d+\]/", $page, $url['path']);
		$http->get($path);
		if($http->status != 200) return "ERROR";
		return $http->getContent();
	}

	//取得文章详细内容，用于测试
	public function debugArticle($articleURL, $listURL)
	{
		$rule = $this->get($_SESSION['__scratcherRuleId__']);
		$url = $this->parseListURL($listURL);
		$http = Plite::libFactory("HttpClient", array($url['host'], $url['port']), "HttpClient.class.php");
		//是否启用cookie;
		if(!empty($rule['cookie']))	$http->setCookies($rule['cookie']);
		$aurl = $this->parseURL($articleURL);
		//判断是不是绝对路径
		if(substr($aurl['path'],0,1) != "/")
		{
			$dir = dirname($url['path']);
			//修复dirname返回\的情况
			if(strlen($dir) == 1) $dir = "";
			$aurl['path'] = $dir."/".$aurl['path'];
		}
		$http->get($aurl['path']);
		if($http->status != 200) return "ERROR";
		return $this->convertCharset($http->getContent());
	}

	//分析URL
	public function parseURL($url)
	{
		$info = parse_url($url);
		$info['host'] = isset($info['host']) ? $info['host'] : '';
		$info['port'] = isset($info['port']) ? $info['port'] : 80;
        $info['path'] = isset($info['path']) ? $info['path'] : '/';
        if(isset($info['query'])) $info['path'] .= '?'.$info['query'];
		return $info;
	}
	
	//解析列表的url
	public function parseListURL($url)
	{
		$info = $this->parseURL($url);		
		preg_match("/\[(\d+)\-(\d+)\]/", $info['path'], $out);
		if(!empty($out))
		{
			$info['startPage'] = $out[1];
			$info['endPage']   = $out[2];
		}
		else
		{
			$info['startPage'] = $info['endPage'] = 1;
		}
		return $info;
	}

	//正则表达式过滤函数
	public function escapePattern($pattern)
	{
		$source = array("[","]","(",")","{","}","+","*","?","^","$",".","|", "#");
		$target = array("\\[","\\]","\\(","\\)","\\{","\\}","\\+","\\*","\\?","\\^","\\$","\\.","\\|", "\#");

		//解析正则变量
		$var   = array("__CATCH__", "__ANY__", "__SPACE__", "__DIGITAL__", "__ALNUM__");
		$trans = array("(.+)", ".+", "\s+", "\d+", "[A-Za-z0-9]+");
		
		$var2  = array("#__DIGITAL<(.+)>__#U", "#__ALNUM<(.+)>__#U" );
		$trans2= array("\d{\\1}", "[A-Za-z0-9]{\\1}");
		
		$pattern = str_replace($var, $trans, str_replace($source, $target, $pattern));
		return preg_replace($var2, $trans2, $pattern);
	}
	
	//编码转换
	public function convertCharset($content, $charset=null)
	{
		if(is_null($charset)) $charset = $this->rule['charset'];
		if($charset == 'UTF-8')
			$content = iconv("UTF-8", "GBK//IGNORE", $content);
		return $content;
	}

	//设置为调试模式
	function setDebug($bool=true)
	{
		$this->dbg = $bool;
	}

	//调试用
    function debugInfo($msg, $object = false) {
        if ($this->dbg) {
            print '<div style="border: 4px solid #abc211; padding: 0.5em; margin: 0.5em;"><strong>Debug:</strong> '.$msg;
            if ($object) {
                ob_start();
        	    print_r($object);
        	    $content = htmlspecialchars(ob_get_contents());
        	    ob_end_clean();
        	    print '<pre>'.$content.'</pre>';
        	}
        	print '</div>';
        }
    }

	//返回规则对应xml文件的名称
	//默认为返回主信息文件
	public function f($f="index", $id=null)
	{
		return $this->path($id).DS.$f.".xml";
	}

	//返回路径信息
	public function path($id=null)
	{
		if(is_null($id)) $id = $this->rule['id'];
		return SCRATCHER_URL_PATH.DS."rule_".$id;
	}

	/*
	 * 返回resource的path
	 *
	 * @param $type 文件的扩展名
	 * @param $t 类型 1返回物理路径 2返回网络路径
	 * @return
	 */
	public function rpath($type, $t=1)
	{
		$root = $t == 1 ? SCRATCHER_RESOURCE_PATH : RESOURCE_VIRTUAL_PATH;
		switch($type)
		{
			case '.jpg':
			case '.gif':
			case '.bmp':
			case '.png':
				$path = $root.DS."Image";
				break;
			case '.swf':
				$path = $root.DS."Flash";
				break;
			case 'mpg':
			case 'avi':
			case 'mp3':
			case 'rm':
			case 'rmvb':
			case 'wav':
			case 'wma':
				$path = $root.DS."Media";
				break;
			default:
				$path = $root.DS."File";
		}
		return $path;
	}

	//检查某个规则是否已经缓存列表
	public function hasCache($id)
	{
		return is_dir($this->path($id)) && !isset($_SESSION['__scratchTotalPage__']);
	}

	//删除缓存文件
	public function clearCache($id)
	{
		$fs = Plite::libFactory("FileSystem");
		if(!is_array($id)) $id = array($id);
		foreach( $id as $path )
		{
			if(is_dir($this->path($path)))
				$fs->removeDir($this->path($path));
		}
		return true;
	}
}
?>