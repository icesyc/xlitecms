<?php
//====================================================
//		FileName:tplParser.php
//		Summary: 模板文件解析器
//		Author: ice_berg16(寻梦的稻草人)
//		LastModifed:2006-10-12
//		copyright (c)2006 ice_berg16@163.com
//		http://www.plite.net
// --------------------------------------------------
//		标签的格式
//		<!-- xliteTag name="tagName" param="value" ...> -->
//		<!-- /xliteTag -->
//		可用的标签
//		sortNavigator 分类导航
//		参数 select 分类的id序列 如1,2,3 为current时 取当前分类
//		参数 number 取出分类的数目
//
//		hotTags	热门标签
//		参数 number 取出标签的数目
//
//		article 文章列表
//		参数 sort 分类id 如5	 为current时取当前分类
//		参数 number 取出的文章数
//		参数 length 文章标题的长度
//	
//		gallery 相册列表
//		参数 sort 分类id 如5	 为current时取当前分类
//		参数 number 取出的记录数
//
//		sort 分类
//		参数 sort 分类id  为current时取当前分类
//
//		pager 分页导航
//		
//		positionNavigator	位置导航
//====================================================

class tplParser
{
	//要解析的文件路径
	private $f;

	//解析标签的正则表达式
	private $re = "#<!-- xliteTag (.+) -->(.+)<!-- /xliteTag -->#isU";

	//用来记录解析标签的序号
	private $index=0;

	//保存解析后的标签
	private $tag = array();

	//保存数据
	private $data = array();

	//是否启用调试模式
	private $dbg = false;

	/*
	 * 构造函数 
	 *
	 * @param $file string 文件路径
	 */
	public function __construct($file=null)
	{
		if(!is_null($file))
			$this->setTpl($file);
	}
	
	/*
	 * 设置模板文件 
	 *
	 * @param $file 文件绝对路径
	 */
	public function setTpl($file)
	{
		if(!file_exists($file))
			die("指定的模板文件 $file 不存在");
		$this->f = file_get_contents($file);
	}

	/*
	 * 填充数据的函数 
	 *
	 * @param $tagName string 标签的名称
	 * @param $value array 数据
	 */
	public function setData($tagName, $value)
	{
		$this->data[$tagName] = $value;
	}

	/*
	 * 取得数据 
	 *
	 * @param $name 标签名称或变量名
	 * @return mix
	 */
	public function getData($name)
	{
		if(isset($this->data[$name]))
			return $this->data[$name];
		else
			return false;
	}

	/*
	 * 标签解析函数 
	 *
	 * @param $match array 匹配得到的数组
	 * @return 替换结果
	 */
	private function parseTag($match)
	{
		//分解标签参数
		$m = preg_match_all("#([a-z]+)=\"(.+)\"#iU", $match[1], $out, PREG_SET_ORDER);
		if(!$m) die("标签参数解析错误，请检查.:<pre>".$match[0]."</pre>");
		foreach( $out as $grp )
		{
			$tag[$grp[1]] = $grp[2];
		}
		//用于批量更新时的或搜索时的动态值, sort, sortNavigator, gallery, article
		if(in_array('current', $tag) || in_array('search', $tag))
		{	
			//取得批量更新的文章分页的pageSize
			if($tag['name'] == 'article')
				$this->data['articlePageSize'] = $tag['number'];
			//相册批量更新的分页pageSize
			if($tag['name'] == 'gallery')
				$this->data['galleryPageSize'] = $tag['number'];
			if($tag['name'] == 'sortNavigator' && isset($tag['number']))
				$this->data['sortNavNumber'] = $tag['number'];
			//文章详细内容页相关文章的数目
			if($tag['name'] == 'assocArticle' && isset($tag['number']))
				$this->data['assocArtNumber'] = $tag['number'];
			//重新定义标签名称
			$prefix = in_array('current', $tag) ? 'current' : 'search';
			$tag['name'] = $prefix.ucfirst($tag['name']);
		}
		//留言板的pageSize
		if($tag['name'] == 'guestbook')
			$this->data['guestbookPageSize'] = $tag['number'];

		$tag['tpl'] = $match[2];
		array_push($this->tag, $tag);
		return "__xlite__tag". $this->index++ ."__";
	}

	/*
	 * 解析函数 
	 *
	 * @return 返回解析的结果
	 */
	public function parse()
	{
		$this->f = preg_replace_callback($this->re, array($this, 'parseTag'), $this->f);
		$this->debug($this->tag);
		return $this->tag;
	}
	
	/*
	 *  填充数据
	 *
	 * @param $data
	 * @return 生成的数据页面
	 */
	public function fillData()
	{
		$result = $this->f;
		foreach( $this->tag as $k => $tag )
		{
			$data = null;
			if(isset($this->data[$tag['name']]))
				$data = $this->data[$tag['name']];
			$func = "fill_" . $tag['name'];
			if(!method_exists($this, $func))
				die($tag['name']." 处理函数还未实现.");
			$d = call_user_func(array($this, $func), $tag, $data);
			$result = str_replace("__xlite__tag". $k . "__", $d, $result);
		}
		return $result;
	}

	/*
	 * 返回结果，相当于调用parse和fillData
	 *
	 */
	public function result()
	{
		$this->parse();
		return $this->fillData();
	}

	/*
	 * 分类导航填充函数 
	 *
	 * @param array $tag 一个标签数组
	 * @param $data array 数据
	 * @return 填充好的数据
	 */
	public function fill_sortNavigator($tag, $data=null)
	{
		if(!isset($tag['select'])) die("解析sortNavigator标签时出错，未指定select参数");

		if(is_null($data))
		{
			$sort = Plite::modelFactory("sort");
			$data = $sort->listTree($tag['select'], 1);
			//没有子节点则取同级节点
			if(empty($tag['onlyChild']) && empty($data))
			{
				$parent = $sort->getParent($tag['select']);
				$data   = $sort->listTree($parent['id'], 1);
			}
			//*/
			if(isset($tag['number']))
				$data = array_slice($data, 0, $tag['number']);
		}
		if(!$data) return '';
		foreach( $data as $k => $r )
		{	
			$data[$k]['link'] = ARTICLE_VIRTUAL_PATH.DS.$r['id'].DS."list_1.htm";
		}
		return $this->replace($data, $tag['tpl']);
	}

	/*
	 * 动态分类导航填充函数 用于批量更新
	 *
	 * @param $tag 标签数组
	 * @return 填充好的数据
	 */
	private function fill_currentSortNavigator($tag, $data=null)
	{
		if(!isset($this->data['currentSort'])) throw new Exception("未指定当前分类");
		$tag['select'] = $this->data['currentSort']['id'];
		return $this->fill_sortNavigator($tag, $data);
	}

	/*
	 * 热门标签填充函数 
	 *
	 * @param array $tag 一个标签数组
	 * @param $data array 数据
	 * @return 填充好的数据
	 */
	private function fill_hotTags($tag, $data=null)
	{
	 	if(!array_key_exists('number', $tag)) $tag['number'] = 10;
		if(is_null($data))
		{
			$tags = Plite::modelFactory("tags");
			$data = $tags->findHot($tag['number']);
		}
		if(!$data) return '';
		return $this->replace($data, $tag['tpl']);
	}
	
	/*
	 * article填充函数 
	 *
	 * @param array $tag 一个标签数组
	 * @param $data array 数据
	 * @return 填充好的数据
	 */
	private function fill_article($tag, $data=null)
	{
	 	if(!array_key_exists('number', $tag))
			die("解析模板标签article出错, 未指定number参数");
		if(is_null($data))
		{
			//取得文章列表
			$art = Plite::modelFactory("article");
			$condition['fields'] = "a.id, a.title,a.summary, a.post_time, a.sort_id, a.thumbnail, a.title_color, s.title AS sort";
			
			//是否分页
			if(isset($tag['page']))
				$condition['page'] = $tag['page'];
			$condition['limit'] = $tag['number'];
			$condition['count'] = false;
			$condition['is_audit'] = 1;
			if(isset($tag['thumbnail']))
				$condition['where'][] = "a.is_pic=1";
			if(isset($tag['is_recmd']))
				$condition['where'][] = "a.is_recmd =1";
			if(isset($tag['sort']))
			{
				$condition['sort_id'] = $tag['sort'];
			}
			$data =  $art->find($condition);
		}
		if(!$data) return '';
		foreach($data as $k => $r)
		{
			$df = isset($tag['df']) ? $tag['df'] : "Y-m-d";
			$data[$k]["post_time"] = date($df, $r["post_time"]);
			if(isset($tag['length']))
				$data[$k]["title"] = Plite::libFactory("Utility")->csubstr($r["title"], 0, $tag['length'], "gb2312", false);
			$data[$k]['title'] = !empty($r['title_color'])
						? sprintf('<span style="color:%s">%s</span>', $r['title_color'], $r['title'])
						: $r['title'];
			$data[$k]['link'] = ARTICLE_VIRTUAL_PATH.DS.$r['sort_id'].DS.$r['id'].".htm";
		}
		return $this->replace($data, $tag['tpl']);
	}
	
	/*
	 * 动态article填充函数 用于批量更新
	 *
	 * @param $tag 标签数组
	 * @return 填充好的数据
	 */
	private function fill_currentArticle($tag, $data=null)
	{
		if(!isset($this->data['currentSort'])) throw new Exception("未指定当前分类");
		if(!isset($this->data['pager'])) throw new Exception("未指定分页参数");
		if(!isset($this->data['currentSortChild'])) throw new Exception("未指定当前分类的子分类");
		$tag['sort'] = $this->data['currentSortChild'];
		$tag['page'] = $this->data['pager']['currentPage'];
		return $this->fill_article($tag, $data);
	}

	/*
	 * 动态搜索文章填充函数 
	 *
	 * @param $tag 标签数组
	 * @return 填充好的数据
	 */
	private function fill_searchArticle($tag, $data=null)
	{
		return $this->fill_article($tag, $data);
	}

	/*
	 * gallery填充函数 
	 *
	 * @param $tag  array 一个标签数组
	 * @param $data array 数据
	 * @return 填充好的数据
	 */
	private function fill_gallery($tag, $data=null)
	{
	 	if(!array_key_exists('number', $tag))
			die("解析模板标签gallery出错, 未指定number参数");
		if(is_null($data))
		{
			$sortId = isset($tag['sort']) ? $tag['sort'] : null;
			$gallery = Plite::modelFactory("gallery");
			//是否分页
			$page = isset($tag['page']) ? $tag['page'] : 1;
			$data = $gallery->find($page, $tag['number'], $sortId, false);
		}
		if(!$data) return '';
		foreach( $data as $k => $r )
		{
			$data[$k]['link'] = $gallery->vp($r['id']).DS."1.htm";
		}
		return $this->replace($data, $tag['tpl']);
	}

	/*
	 * 动态gallery填充函数 用于批量更新
	 *
	 * @param $tag 标签数组
	 * @return 填充好的数据
	 */
	private function fill_currentGallery($tag, $data=null)
	{
		if(!isset($this->data['currentSort'])) throw new Exception("未指定当前分类");
		$tag['sort'] = $this->data['currentSort']['id'];
		$tag['page'] = $this->data['pager']['currentPage'];
		return $this->fill_gallery($tag, $data);
	}

	/*
	 * 分类填充函数 
	 *
	 * @param $tag array 一个标签数组
	 * @param $data array 数据
	 * @return 填充好的数据
	 */
	private function fill_sort($tag, $data=null)
	{
	 	if(!array_key_exists('select', $tag))
			die("解析模板标签sortTitle出错, 未指定select参数");
		if(is_null($data))
		{
			$sort = Plite::modelFactory("sort");
			$id = explode(",", $tag['select']);
			$data = $sort->findById($id);
		}
		if(!$data) return '';
		foreach( $data as $k => $r )
		{	
			$data[$k]['link'] = ARTICLE_VIRTUAL_PATH.DS.$r['id'].DS."list_1.htm";
		}
		return $this->replace($data, $tag['tpl']);
	}

	/*
	 * 动态分类填充函数 用于批量更新
	 *
	 * @param $tag 标签数组
	 * @return 填充好的数据
	 */
	private function fill_currentSort($tag, $data=null)
	{
		if(!isset($this->data['currentSort'])) throw new Exception("未指定当前分类");
		//sort处理为记录集，单独记录应做为数组传递
		return $this->fill_sort($tag, array($data));
	}

	/*
	 * 分页导航填充函数 
	 *
	 * @param $tag 一个标签数组
	 * @param $data array 数据
	 * @return 填充好的数据
	 */
	private function fill_pager($tag, $data=null)
	{
		if(is_null($data)) $data = $this->data['pager'];
		if(!$data) return '';
		if(!isset($data['class'])) $data['class'] = "StaticPager";
		$sp = Plite::libFactory($data['class']);
		if(!isset($data['tpl'])) $data['tpl'] = file_get_contents(XLITE_SYS_TPL.DS."pager.tpl");
		$sp->setFormat($data['tpl']);
		$sp->setParam($data);
		$fields = "{pager}";
		return str_replace($fields, $sp->makePage(), $tag['tpl']);
	}

	/*
	 * 位置导航填充函数
	 *
	 * @param $tag 一个标签数组
	 * @param $data array 数据
	 * @return 填充好的数据
	 */
	private function fill_positionNavigator($tag, $data=null)
	{
		if(is_null($data)) $data = $this->data['positionNavigator'];
		if(!$data) return '';		
		foreach( $data as $k => $r )
		{
			$data[$k]['link'] = ARTICLE_VIRTUAL_PATH.DS.$r['id'].DS."list_1.htm";
		}
		return $this->replace($data, $tag['tpl']);
	}

	/*
	 * 图片数据填充函数 
	 *
	 * @param $tag 一个标签数组
	 * @param $data array 数据
	 * @return 填充好的数据
	 */
	private function fill_image($tag, $data=null)
	{
		if(is_null($data)) $data = $this->data['image'];
		if(!$data) return '';
		$pager = $this->data['pager'];
		$data['nextPage'] =	$pager['recordCount'] == $pager['currentPage'] ? '#' : ($pager['currentPage']+1) . ".htm";
		return $this->replace($data, $tag['tpl']);
	}

	/*
	 * 变量填充函数 
	 *
	 * @param $tag 一个标签数组
	 * @param $data array 数据
	 * @return 填充好的数据
	 */
	private function fill_variable($tag, $data=null)
	{
		if(is_null($data)) return '';
		return $this->replace($data, $tag['tpl']);
	}

	/*
	 * 相关文章填充函数
	 *
	 * @param $tag 一个标签数组
	 * @param $data array 数据
	 * @return 填充好的数据
	 */
	private function fill_assocArticle($tag, $data=null)
	{
		if(is_null($data)) $data = $this->data['assocArticle'];
		if(!$data) return '';
		foreach( $data as $k => $r )
		{
			$data[$k]['link'] = ARTICLE_VIRTUAL_PATH.DS.$r['sort_id'].DS.$r['id'].".htm";
		}
		return $this->replace($data, $tag['tpl']);
	}
	
	/*
	 * 解析文章详细内容标签 
	 *
	 * @param $tag 一个标签数组
	 * @param $data array 数据
	 * @return 填充好的数据
	 */
	private function fill_articleDetail($tag, $data=null)
	{
		if(is_null($data)) $data = $this->data['articleDetail'];
		if(!$data) return '';
		return $this->replace($data, $tag['tpl']);
	}
	
	/*
	 * 解析友情链接标签 
	 *
	 * @param $tag 一个标签数组
	 * @param $data array 数据
	 * @return 填充好的数据
	 */
	private function fill_link($tag, $data=null)
	{
		if(is_null($data))
		{
			$link = Plite::modelFactory("link");
			$type = isset($tag['type']) ? $tag['type'] : null;
			$rows = $tag['number'];
			$data = $link->find($rows, null, $type);

		}
		if(!$data) return '';
		return $this->replace($data, $tag['tpl']);
	}

	/*
	 * 解析留言板标签 
	 *
	 * @param $tag 一个标签数组
	 * @param $data array 数据
	 * @return 填充好的数据
	 */
	private function fill_guestbook($tag, $data=null)
	{
		if(is_null($data))
		{
			$gb = Plite::modelFactory("guestbook");
			$rows = $tag['number'];
			$data = $link->find($rows, null);

		}
		if(!$data) return '';
		return $this->replace($data, $tag['tpl']);
	}

	/*
	 * 模板替换函数 
	 *
	 * @param $data array 数据记录集或单行记录
	 * @return 替换后的结果
	 */
	private function replace($data, $tpl)
	{
		//不是记录集，包装成记录集
		if(!isset($data[0])) $data = array($data);
		foreach( $data as $rec )
		{
			$fields = array_keys($rec);
			$re = "#\{(".join("|",$fields).")\}#e";
			$result = preg_replace($re, '$rec["\\1"]', $tpl);
			$rtn[] = $result;
		}
		return join("", $rtn);
	}

	//是否调试	
	public function setDebug($bool=true)
	{
		$this->dbg = $bool;
	}

	//调试用的函数
	private function debug($info)
	{
		if(!$this->dbg) return;
		if(is_array($info)) $info = print_r($info, true);
		file_put_contents("tpl_parser_debug.txt", $info, FILE_APPEND);
	}
}
?>