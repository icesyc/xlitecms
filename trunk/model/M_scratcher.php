<?php
//====================================================
//		FileName:M_scratcher.php
//		Summary: �ɼ�����model
//		Author: ice_berg16(Ѱ�εĵ�����)
//		LastModifed:2006-09-28
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
class M_scratcher extends model
{
	
	protected $table = 'scratcher';
	//�ɼ�����
	private $rule;
	//httpClient
	private $http;
	//��ʱ��
	private $timer;

	//������Դ���ӵ�����
	private $resLink = array();
	//���Ա�ʶ
	private $dbg; 

	//���Һ���
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
			$row['last_modified_time'] = '��δ�ɼ�';
		else
			$row['last_modified_time'] = date("Y-m-d", $row['last_modified_time']);
		$row['is_rss'] = $row['is_rss'] ? "<img src='images/rss.gif' align='absmiddle' alt='RSS��ַ'/>" : '';
		return $row;
	}

	//�ɼ���ʼ��
	public function scratchInit($id)
	{
		$this->rule = $this->get($id);	
		$this->URL = $this->parseListURL($this->rule['list_url']);
		$this->http = Plite::libFactory("HttpClient", array($this->URL['host'], $this->URL['port']), "HttpClient.class.php");
		//�Ƿ�����cookie
		if(!empty($this->rule['cookie'])) $this->http->setCookies($this->rule['cookie']);
		$this->timer = Plite::libFactory("Timer");
	}

	//�ɼ��б�
	public function scratchList($id)
	{	
		$this->scratchInit($id);

		//��ʼ��Ŀ¼
		if(!is_dir($this->path())) mkdir($this->path());

		//ѭ��ȡ���б�ҳ
		$info = $this->URL;
		$pn = $info['endPage'] - $info['startPage'] + 1;
		//�����Ҫ�ɼ���ҳ��
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
				//����Ƿ�ʱ����ʱ���˳������µ�ǰδ������б�ͬʱ���ͼ������ź�
				if($this->timer->getExecTime() > MAX_EXEC_TIME * 1000)
				{
					$_SESSION['__scratchPageOffset__'] = $currentPage;
					$_SESSION['__totalLinks__'] = $count;
					exit(sprintf("{status:'PROCESSING',total:%d,processed:%d}",
								$_SESSION['__scratchTotalPage__'], $currentPage));
				}
				//ȡ���б�����
				$path = preg_replace("/\[\d+\-\d+\]/", $currentPage, $info['path']);
				$this->http->get($path);
				$listContent = $this->http->getContent();
				//���б������з��������б�����
				$count += $this->saveListFromContent($listContent, $index);
				unset($listContent);
				$currentPage++;
				$index++;
			}
			//��ȡ�б����,���б��������Ŀ��Ϣд���ļ�
			$tpl = "<?xml version='1.0' encoding='gbk'?>\r\n<rule id='%d' page='%d' links='%d'>\r\n%s</rule>";			
			$content = file_get_contents($this->f());
			$content = sprintf($tpl, $this->rule['id'], $pn, $count, $content);
			file_put_contents($this->f(), $content);
			
			//���������Ϣ,����а�Ҫ��ת��ҳ����Ϣ
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
	 * �����б��е���ַ HTML�ļ�
	 *
	 * @param $content string �б������
	 * @return ���б��л�õ�������
	 */
	public function saveListFromHTML($content)
	{
		//html���Ϊutf8��Ҫת��,rss����Ҫת��
		$content = $this->convertCharset($content);
		//�Ƿ���Ҫ��С�б�Χ
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
		//�������ӵ�ַ�����飬���������ظ�����
		$links =array();	
		$cnt = 0;
		foreach( $m as $rec )
		{
			//���Ӵ���
			if(in_array($rec[2], $links)) continue;
			//�����Ƿ�ΪͼƬ���ӣ�ͼƬ�������Զ�����alt
			if(preg_match("#^\s*<img[^>]+alt=(['\"])(.+)\\1[^>]*>\s*$#isU", $rec[3], $out))
			{
				$title = $out[2];
			}
			else
				$title =  htmlspecialchars(strip_tags($rec[3]));

			//���ؼ���
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
	 * �����б��е���ַ RSS�ļ�
	 *
	 * @param $content string �б������
	 * @param $page ��ǰ�����ҳ
	 * @return ���б��л�õ�������
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
			//�����ظ�����
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
	 * �ɼ���ϸҳ��
	 * �ú��������ڵ���scratchList֮����е���
	 *
	 * @param $id int ����id
	 * 
	 */
	public function scratchPage($id)
	{
		$this->scratchInit($id);

		//�ļ�������������
		if(!file_exists($this->f())) return;

		//ȡ�øù����ҳ���������Ϣ
		if(!$xml = simplexml_load_file($this->f())) die('����xml�ļ�����->'.$this->f());

		//����������
		$_SESSION['__totalLinks__'] = intval($xml['links']);

		//�����Ѿ������������
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

		//ץȡ�������ͷ�session
		$processed = $_SESSION['__totalLinks__'];
		echo sprintf("{status:'PROCESS_OVER',total:%d,processed:%1\$d,handler:function(){Scratcher.AfterScratchPage(%d);}}",
					$processed, $this->rule['id']);
		unset($_SESSION['__processedLinks__'], $_SESSION['__totalLinks__']);

		//�����ѱ���ļ�¼���Ͳɼ�ʱ��
		$sql = sprintf("UPDATE %s SET total_save=total_save+%d, last_modified_time=%d WHERE id=%d",
			$this->fullTableName, $processed, time(), $id);
		return $this->DB->exec($sql);
	}

	//ȡ��������ϸҳ��
	public function getDetail($link)
	{
		//����Ƿ�ʱ����ʱ���˳������µ�ǰδ������б�ͬʱ���ͼ������ź�
		if($this->timer->getExecTime() > MAX_EXEC_TIME * 1000)
		{
			exit(sprintf("{status:'PROCESSING',total:%d,processed:%d}",
						$_SESSION['__totalLinks__'], $_SESSION['__processedLinks__']));
		}
		
		//�ɼ��ﵽĿ����
		if($this->rule["get_number"] != 0 && $_SESSION['__processedLinks__'] >= $this->rule["get_number"])
		{
			$processed = $_SESSION['__processedLinks__'];
			echo sprintf("{status:'PROCESS_OVER',total:%d,processed:%1\$d,handler:function(){Scratcher.AfterScratchPage(%d);}}",
							$processed, $this->rule['id']);
			unset($_SESSION['__processedLinks__'], $_SESSION['__totalLinks__']);
			//�����ѱ���ļ�¼��
			$this->update(array("id"=>$id, "total_save" => $processed));
		}
		
		//����URL
		$url = $this->parseURL($link);

		//�ж��ǲ��Ǿ���·�������������б��·��
		if(substr($url['path'],0,1) != "/")
		{
			$path = dirname($this->URL['path']);
			if(strlen($path) == 1) $path = "";
			$url['path'] = $path ."/".$url['path'];
		}
		$this->http->get($url['path']);
		$content = $this->convertCharset($this->http->getContent());

		//echo $content;
		//ѭ��ƥ������ֶ�
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
		
		//�Ƿ�����ͼ��Դ
		if($this->rule['save_resource'])
			$match['content'] = $this->saveResource($match['content']);
		$this->debugInfo("ƥ����", $match["title"]);
		$match['sort_id'] = $this->rule['save_sort_id'];
		$match['file'] = $this->rule['id'] . "_" . ($_SESSION['__processedLinks__']+1);
		$this->saveDetail($match);
		//���������1
		$_SESSION['__processedLinks__']++;
	}

	//�����XML�ļ�
	public function saveDetail($data)
	{
		$xml = "<?xml version='1.0' encoding='gbk'?><article>";
		foreach($data as $f => $v )
		{
			//ʹ��base64�����ֹ��xml���ش���
			$xml .= sprintf("<%s>%s</%1\$s>", $f, base64_encode($v));
		}
		$xml .= "</article>";
		file_put_contents($this->f($data['file']), $xml);
	}

	//������Դ
	public function saveResource($content)
	{
		//����ͼƬ,flash,��Ƶ��Ƶ��
		$mediaReg = array("#<img[^>]+src=(['\"])(.+)\\1#isU",	//ͼƬ
					"#<param name=['\"]movie['\"] value=(['\"])(.+)\\2#isU",	//flash
					"#<param name=['\"]FileName['\"] value=(['\"])(.+)\\2#isU",	//wmp�ؼ�
					"#<param name=['\"]SRC['\"] value=(['\"])(.+)\\2#isU"	//real�ؼ�
					);
		return preg_replace_callback($mediaReg, array($this, 'saveCallback'), $content);
	}
	
	/*
	 * ������Դ�Ļص�����
	 *
	 * @param $m array ƥ�䵽������
	 * @return �滻���·��
	 */
	public function saveCallback($m)
	{	
		//����URL
		$url = $this->parseURL($m[2]);
		//ȡ����չ��
		$ext  = substr($m[2], strrpos($m[2], ".")); //�õ�ͼƬ��չ��
		//�õ�ǰ��ʱ�����ļ���
		$fname = microtime(true).$ext;
		//�ж��ǲ��Ǿ���·�������������б��·��
		if(substr($url['path'],0,1) != "/")
			$url['path'] = dirname($this->URL['path'])."/".$url['path'];		
		
		//ȡ��·��
		$path = $this->rpath($ext).DS.$fname;		
		$npath = $this->rpath($ext,2).DS.$fname;

		//���ڸ�ͼƬ��ֱ�ӷ���
		if(array_key_exists(md5($m[2]), $this->resLink))
		{
			return str_replace($m[2], $npath, $m[0]);
		}
		
		//http��ַ
		if(substr($m[2], 0, 7) == "http://")
			$file = file_get_contents($m[2]);
		else
		{
			$this->http->get($url['path']);
			$file = $this->http->getContent();
		}
		file_put_contents($path, $file);
		
		//��������
		$this->resLink[md5($m[2])] = $npath;
		return str_replace($m[2], $npath, $m[0]);
	}
	
	//���ɼ��������
	public function saveToDB($id)
	{		
		//ȡ��Ҫ����������
		$index = $this->f('index', $id);
		$rule  = @simplexml_load_file($index) or die('����xml�ļ�����->'.$index);
		$timer = Plite::libFactory("Timer");

		if((int)$rule['links'] == 0)	exit("{status:'PROCESS_OVER',total:0,processed:0,handler:function(){Scratcher.enableBtn()}}");

		//��һ�δ����ȱ���Ҫ�������Ŀ
		if(!isset($_SESSION['__processedFiles__']))
		{
			$_SESSION['__processedFiles__'] = 0;			
			//ȡ�����ӵ�����
			$_SESSION['__totalFiles__'] = (int)$rule['links'];
			exit(sprintf("{status:'PROCESSING',total:%d,processed:0}", $_SESSION['__totalFiles__']));
		}
		
		//��ǰƫ����
		$offset = $_SESSION['__processedFiles__'] + 1;

		$art = Plite::modelFactory("article");
		//ѭ����ȡ���µ�xml�ļ�
		$dir = dir($this->path($id));
		$i = 0;
		while(($f = $dir->read()) !== false)
		{			
			//���������ļ�
			if($f == "." || $f == ".." || $f == "index.xml") continue;
			//����Ƿ�ʱ����ʱ���˳���ͬʱ���ͼ������ź�
			if($timer->getExecTime() > MAX_EXEC_TIME * 1000)
			{
				$_SESSION['__fileOffset__'] = $i;
				exit(sprintf("{status:'PROCESSING',total:%d,processed:%d}",
							$_SESSION['__totalFiles__'], $_SESSION['__processedFiles__']));
			}
			if(++$i < $offset) $continue;
			if(!$article = @simplexml_load_file($dir->path.DS.$f)) die('����xml�ļ�����->'.$dir->path.DS.$f);
			//����ת��Ϊ����
			foreach($article as $f => $v )
			{
				//����
				$data[$f] = base64_decode($v);
			}
			//���ַ�������ת��Ϊʱ���
			$data['post_time'] = empty($data['post_time']) ? time() : strtotime($data['post_time']);
			if(empty($data['post_time']))
				$data['post_time'] = time();
			$art->save($data);
		}

		echo sprintf("{status:'PROCESS_OVER',total:%d,processed:%1\$d,handler:function(){Scratcher.enableBtn()}}", $_SESSION['__totalFiles__']);
		unset($_SESSION['__processedFiles__'], $_SESSION['__totalFiles__']);
	}

	/*
	 *  ȡ���б��ҳ�����ݣ����ڲ���
	 *
	 * @param $url string URL��ַ
	 * @id int ����id
	 */
	public function debugListURL($url)
	{
		$rule = $this->get($_SESSION['__scratcherRuleId__']);
		$url = $this->parseListURL($url);
		$http = Plite::libFactory("HttpClient", array($url['host'], $url['port']), "HttpClient.class.php");
		//�Ƿ�����cookie;
		if(!empty($rule['cookie']))	$http->setCookies($rule['cookie']);

		//ȡ��һҳ
		$page = $url['startPage'];
		$path = preg_replace("/\[\d+\-\d+\]/", $page, $url['path']);
		$http->get($path);
		if($http->status != 200) return "ERROR";
		return $http->getContent();
	}

	//ȡ��������ϸ���ݣ����ڲ���
	public function debugArticle($articleURL, $listURL)
	{
		$rule = $this->get($_SESSION['__scratcherRuleId__']);
		$url = $this->parseListURL($listURL);
		$http = Plite::libFactory("HttpClient", array($url['host'], $url['port']), "HttpClient.class.php");
		//�Ƿ�����cookie;
		if(!empty($rule['cookie']))	$http->setCookies($rule['cookie']);
		$aurl = $this->parseURL($articleURL);
		//�ж��ǲ��Ǿ���·��
		if(substr($aurl['path'],0,1) != "/")
		{
			$dir = dirname($url['path']);
			//�޸�dirname����\�����
			if(strlen($dir) == 1) $dir = "";
			$aurl['path'] = $dir."/".$aurl['path'];
		}
		$http->get($aurl['path']);
		if($http->status != 200) return "ERROR";
		return $this->convertCharset($http->getContent());
	}

	//����URL
	public function parseURL($url)
	{
		$info = parse_url($url);
		$info['host'] = isset($info['host']) ? $info['host'] : '';
		$info['port'] = isset($info['port']) ? $info['port'] : 80;
        $info['path'] = isset($info['path']) ? $info['path'] : '/';
        if(isset($info['query'])) $info['path'] .= '?'.$info['query'];
		return $info;
	}
	
	//�����б��url
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

	//������ʽ���˺���
	public function escapePattern($pattern)
	{
		$source = array("[","]","(",")","{","}","+","*","?","^","$",".","|", "#");
		$target = array("\\[","\\]","\\(","\\)","\\{","\\}","\\+","\\*","\\?","\\^","\\$","\\.","\\|", "\#");

		//�����������
		$var   = array("__CATCH__", "__ANY__", "__SPACE__", "__DIGITAL__", "__ALNUM__");
		$trans = array("(.+)", ".+", "\s+", "\d+", "[A-Za-z0-9]+");
		
		$var2  = array("#__DIGITAL<(.+)>__#U", "#__ALNUM<(.+)>__#U" );
		$trans2= array("\d{\\1}", "[A-Za-z0-9]{\\1}");
		
		$pattern = str_replace($var, $trans, str_replace($source, $target, $pattern));
		return preg_replace($var2, $trans2, $pattern);
	}
	
	//����ת��
	public function convertCharset($content, $charset=null)
	{
		if(is_null($charset)) $charset = $this->rule['charset'];
		if($charset == 'UTF-8')
			$content = iconv("UTF-8", "GBK//IGNORE", $content);
		return $content;
	}

	//����Ϊ����ģʽ
	function setDebug($bool=true)
	{
		$this->dbg = $bool;
	}

	//������
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

	//���ع����Ӧxml�ļ�������
	//Ĭ��Ϊ��������Ϣ�ļ�
	public function f($f="index", $id=null)
	{
		return $this->path($id).DS.$f.".xml";
	}

	//����·����Ϣ
	public function path($id=null)
	{
		if(is_null($id)) $id = $this->rule['id'];
		return SCRATCHER_URL_PATH.DS."rule_".$id;
	}

	/*
	 * ����resource��path
	 *
	 * @param $type �ļ�����չ��
	 * @param $t ���� 1��������·�� 2��������·��
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

	//���ĳ�������Ƿ��Ѿ������б�
	public function hasCache($id)
	{
		return is_dir($this->path($id)) && !isset($_SESSION['__scratchTotalPage__']);
	}

	//ɾ�������ļ�
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