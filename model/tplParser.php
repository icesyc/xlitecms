<?php
//====================================================
//		FileName:tplParser.php
//		Summary: ģ���ļ�������
//		Author: ice_berg16(Ѱ�εĵ�����)
//		LastModifed:2006-10-12
//		copyright (c)2006 ice_berg16@163.com
//		http://www.plite.net
// --------------------------------------------------
//		��ǩ�ĸ�ʽ
//		<!-- xliteTag name="tagName" param="value" ...> -->
//		<!-- /xliteTag -->
//		���õı�ǩ
//		sortNavigator ���ർ��
//		���� select �����id���� ��1,2,3 Ϊcurrentʱ ȡ��ǰ����
//		���� number ȡ���������Ŀ
//
//		hotTags	���ű�ǩ
//		���� number ȡ����ǩ����Ŀ
//
//		article �����б�
//		���� sort ����id ��5	 Ϊcurrentʱȡ��ǰ����
//		���� number ȡ����������
//		���� length ���±���ĳ���
//	
//		gallery ����б�
//		���� sort ����id ��5	 Ϊcurrentʱȡ��ǰ����
//		���� number ȡ���ļ�¼��
//
//		sort ����
//		���� sort ����id  Ϊcurrentʱȡ��ǰ����
//
//		pager ��ҳ����
//		
//		positionNavigator	λ�õ���
//====================================================

class tplParser
{
	//Ҫ�������ļ�·��
	private $f;

	//������ǩ��������ʽ
	private $re = "#<!-- xliteTag (.+) -->(.+)<!-- /xliteTag -->#isU";

	//������¼������ǩ�����
	private $index=0;

	//���������ı�ǩ
	private $tag = array();

	//��������
	private $data = array();

	//�Ƿ����õ���ģʽ
	private $dbg = false;

	/*
	 * ���캯�� 
	 *
	 * @param $file string �ļ�·��
	 */
	public function __construct($file=null)
	{
		if(!is_null($file))
			$this->setTpl($file);
	}
	
	/*
	 * ����ģ���ļ� 
	 *
	 * @param $file �ļ�����·��
	 */
	public function setTpl($file)
	{
		if(!file_exists($file))
			die("ָ����ģ���ļ� $file ������");
		$this->f = file_get_contents($file);
	}

	/*
	 * ������ݵĺ��� 
	 *
	 * @param $tagName string ��ǩ������
	 * @param $value array ����
	 */
	public function setData($tagName, $value)
	{
		$this->data[$tagName] = $value;
	}

	/*
	 * ȡ������ 
	 *
	 * @param $name ��ǩ���ƻ������
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
	 * ��ǩ�������� 
	 *
	 * @param $match array ƥ��õ�������
	 * @return �滻���
	 */
	private function parseTag($match)
	{
		//�ֽ��ǩ����
		$m = preg_match_all("#([a-z]+)=\"(.+)\"#iU", $match[1], $out, PREG_SET_ORDER);
		if(!$m) die("��ǩ����������������.:<pre>".$match[0]."</pre>");
		foreach( $out as $grp )
		{
			$tag[$grp[1]] = $grp[2];
		}
		//������������ʱ�Ļ�����ʱ�Ķ�ֵ̬, sort, sortNavigator, gallery, article
		if(in_array('current', $tag) || in_array('search', $tag))
		{	
			//ȡ���������µ����·�ҳ��pageSize
			if($tag['name'] == 'article')
				$this->data['articlePageSize'] = $tag['number'];
			//����������µķ�ҳpageSize
			if($tag['name'] == 'gallery')
				$this->data['galleryPageSize'] = $tag['number'];
			if($tag['name'] == 'sortNavigator' && isset($tag['number']))
				$this->data['sortNavNumber'] = $tag['number'];
			//������ϸ����ҳ������µ���Ŀ
			if($tag['name'] == 'assocArticle' && isset($tag['number']))
				$this->data['assocArtNumber'] = $tag['number'];
			//���¶����ǩ����
			$prefix = in_array('current', $tag) ? 'current' : 'search';
			$tag['name'] = $prefix.ucfirst($tag['name']);
		}
		//���԰��pageSize
		if($tag['name'] == 'guestbook')
			$this->data['guestbookPageSize'] = $tag['number'];

		$tag['tpl'] = $match[2];
		array_push($this->tag, $tag);
		return "__xlite__tag". $this->index++ ."__";
	}

	/*
	 * �������� 
	 *
	 * @return ���ؽ����Ľ��
	 */
	public function parse()
	{
		$this->f = preg_replace_callback($this->re, array($this, 'parseTag'), $this->f);
		$this->debug($this->tag);
		return $this->tag;
	}
	
	/*
	 *  �������
	 *
	 * @param $data
	 * @return ���ɵ�����ҳ��
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
				die($tag['name']." ��������δʵ��.");
			$d = call_user_func(array($this, $func), $tag, $data);
			$result = str_replace("__xlite__tag". $k . "__", $d, $result);
		}
		return $result;
	}

	/*
	 * ���ؽ�����൱�ڵ���parse��fillData
	 *
	 */
	public function result()
	{
		$this->parse();
		return $this->fillData();
	}

	/*
	 * ���ർ����亯�� 
	 *
	 * @param array $tag һ����ǩ����
	 * @param $data array ����
	 * @return ���õ�����
	 */
	public function fill_sortNavigator($tag, $data=null)
	{
		if(!isset($tag['select'])) die("����sortNavigator��ǩʱ����δָ��select����");

		if(is_null($data))
		{
			$sort = Plite::modelFactory("sort");
			$data = $sort->listTree($tag['select'], 1);
			//û���ӽڵ���ȡͬ���ڵ�
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
	 * ��̬���ർ����亯�� ������������
	 *
	 * @param $tag ��ǩ����
	 * @return ���õ�����
	 */
	private function fill_currentSortNavigator($tag, $data=null)
	{
		if(!isset($this->data['currentSort'])) throw new Exception("δָ����ǰ����");
		$tag['select'] = $this->data['currentSort']['id'];
		return $this->fill_sortNavigator($tag, $data);
	}

	/*
	 * ���ű�ǩ��亯�� 
	 *
	 * @param array $tag һ����ǩ����
	 * @param $data array ����
	 * @return ���õ�����
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
	 * article��亯�� 
	 *
	 * @param array $tag һ����ǩ����
	 * @param $data array ����
	 * @return ���õ�����
	 */
	private function fill_article($tag, $data=null)
	{
	 	if(!array_key_exists('number', $tag))
			die("����ģ���ǩarticle����, δָ��number����");
		if(is_null($data))
		{
			//ȡ�������б�
			$art = Plite::modelFactory("article");
			$condition['fields'] = "a.id, a.title,a.summary, a.post_time, a.sort_id, a.thumbnail, a.title_color, s.title AS sort";
			
			//�Ƿ��ҳ
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
	 * ��̬article��亯�� ������������
	 *
	 * @param $tag ��ǩ����
	 * @return ���õ�����
	 */
	private function fill_currentArticle($tag, $data=null)
	{
		if(!isset($this->data['currentSort'])) throw new Exception("δָ����ǰ����");
		if(!isset($this->data['pager'])) throw new Exception("δָ����ҳ����");
		if(!isset($this->data['currentSortChild'])) throw new Exception("δָ����ǰ������ӷ���");
		$tag['sort'] = $this->data['currentSortChild'];
		$tag['page'] = $this->data['pager']['currentPage'];
		return $this->fill_article($tag, $data);
	}

	/*
	 * ��̬����������亯�� 
	 *
	 * @param $tag ��ǩ����
	 * @return ���õ�����
	 */
	private function fill_searchArticle($tag, $data=null)
	{
		return $this->fill_article($tag, $data);
	}

	/*
	 * gallery��亯�� 
	 *
	 * @param $tag  array һ����ǩ����
	 * @param $data array ����
	 * @return ���õ�����
	 */
	private function fill_gallery($tag, $data=null)
	{
	 	if(!array_key_exists('number', $tag))
			die("����ģ���ǩgallery����, δָ��number����");
		if(is_null($data))
		{
			$sortId = isset($tag['sort']) ? $tag['sort'] : null;
			$gallery = Plite::modelFactory("gallery");
			//�Ƿ��ҳ
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
	 * ��̬gallery��亯�� ������������
	 *
	 * @param $tag ��ǩ����
	 * @return ���õ�����
	 */
	private function fill_currentGallery($tag, $data=null)
	{
		if(!isset($this->data['currentSort'])) throw new Exception("δָ����ǰ����");
		$tag['sort'] = $this->data['currentSort']['id'];
		$tag['page'] = $this->data['pager']['currentPage'];
		return $this->fill_gallery($tag, $data);
	}

	/*
	 * ������亯�� 
	 *
	 * @param $tag array һ����ǩ����
	 * @param $data array ����
	 * @return ���õ�����
	 */
	private function fill_sort($tag, $data=null)
	{
	 	if(!array_key_exists('select', $tag))
			die("����ģ���ǩsortTitle����, δָ��select����");
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
	 * ��̬������亯�� ������������
	 *
	 * @param $tag ��ǩ����
	 * @return ���õ�����
	 */
	private function fill_currentSort($tag, $data=null)
	{
		if(!isset($this->data['currentSort'])) throw new Exception("δָ����ǰ����");
		//sort����Ϊ��¼����������¼Ӧ��Ϊ���鴫��
		return $this->fill_sort($tag, array($data));
	}

	/*
	 * ��ҳ������亯�� 
	 *
	 * @param $tag һ����ǩ����
	 * @param $data array ����
	 * @return ���õ�����
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
	 * λ�õ�����亯��
	 *
	 * @param $tag һ����ǩ����
	 * @param $data array ����
	 * @return ���õ�����
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
	 * ͼƬ������亯�� 
	 *
	 * @param $tag һ����ǩ����
	 * @param $data array ����
	 * @return ���õ�����
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
	 * ������亯�� 
	 *
	 * @param $tag һ����ǩ����
	 * @param $data array ����
	 * @return ���õ�����
	 */
	private function fill_variable($tag, $data=null)
	{
		if(is_null($data)) return '';
		return $this->replace($data, $tag['tpl']);
	}

	/*
	 * ���������亯��
	 *
	 * @param $tag һ����ǩ����
	 * @param $data array ����
	 * @return ���õ�����
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
	 * ����������ϸ���ݱ�ǩ 
	 *
	 * @param $tag һ����ǩ����
	 * @param $data array ����
	 * @return ���õ�����
	 */
	private function fill_articleDetail($tag, $data=null)
	{
		if(is_null($data)) $data = $this->data['articleDetail'];
		if(!$data) return '';
		return $this->replace($data, $tag['tpl']);
	}
	
	/*
	 * �����������ӱ�ǩ 
	 *
	 * @param $tag һ����ǩ����
	 * @param $data array ����
	 * @return ���õ�����
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
	 * �������԰��ǩ 
	 *
	 * @param $tag һ����ǩ����
	 * @param $data array ����
	 * @return ���õ�����
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
	 * ģ���滻���� 
	 *
	 * @param $data array ���ݼ�¼�����м�¼
	 * @return �滻��Ľ��
	 */
	private function replace($data, $tpl)
	{
		//���Ǽ�¼������װ�ɼ�¼��
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

	//�Ƿ����	
	public function setDebug($bool=true)
	{
		$this->dbg = $bool;
	}

	//�����õĺ���
	private function debug($info)
	{
		if(!$this->dbg) return;
		if(is_array($info)) $info = print_r($info, true);
		file_put_contents("tpl_parser_debug.txt", $info, FILE_APPEND);
	}
}
?>