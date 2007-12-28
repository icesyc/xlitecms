<?php
//====================================================
//		FileName:M_article.php
//		Summary: 文章Model
//		Author: ice_berg16(寻梦的稻草人)
//		LastModifed:2006-09-23
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
class M_article extends Model
{

	protected $table = 'article';

	/* 保存文章 */
	public function save($data)
	{
		//添加文章
		$newId = parent::save($data);
		$newId = !empty($data['id']) ? $data['id'] : $newId;
		//将标签入库
		$tags = Plite::modelFactory('tags');
		if(!empty($data['id']))
		{
			$tags->deleteByArticleId($newId);
		}
		if(!empty($data['tags']))
		{
			foreach(explode(",",$data['tags']) as $keyword)
				$tags->create(array('article_id' => $newId, 'keyword' => $keyword));
		}
		//将新ID加入到数组
		$data['id'] = $newId;
		//生成xml文件
		$this->toHTML($data);
		return $newId;
	}
	
	//删除文章
	public function delete($id)
	{
		//删除文章的静态页面
		$where = "id " . (is_array($id) ? "IN(".join(",", $id).")" : "=".$id);
		$sql = sprintf('SELECT id, sort_id FROM %s WHERE ' . $where, $this->fullTableName);
		$this->setFilter(
			create_function('$row', '@unlink(ARTICLE_PATH.DS.$row["sort_id"].DS.$row["id"].".xml");'),
			self::FILTER_FIND
		);
		$this->findAllBySql($sql);
		return parent::delete($id);
	}

	//查找文章
	public function find($condition)
	{
		if(!isset($condition['fields']))
			$condition['fields'] = "a.id,a.title,a.summary,a.post_time,a.sort_id,a.is_audit,a.is_pic,a.is_recmd,
									a.title_color,a.thumbnail,s.title AS sort";
		$condition['from']  = $this->fullTableName . " AS a";
		$condition['join'][]= array('table' => Config::get("tablePrefix") . 'sort AS s',
									'on'	=> 'a.sort_id=s.id');
		//标签搜索
		if(isset($condition['tag']))
		{
			$condition['join'][] = array('table' => Config::get("tablePrefix") . 'tags AS t',
									'on'	=> 'a.id=t.article_id');
			$condition['where'][] = sprintf("t.keyword='%s'", $condition['tag']);
		}

		//分类搜索
		if(isset($condition['sort_id']))
		{
			if(is_array($condition['sort_id']))
				$condition['where'][] = sprintf('a.sort_id in(%s)', join(",",$condition['sort_id']));
			else
				$condition['where'][] = sprintf('a.sort_id=%d', $condition['sort_id']);
		}
		//是否审核
		if(isset($condition['is_audit']))
			$condition['where'][] = sprintf('a.is_audit=%d', $condition['is_audit']);
		$condition['order'] = 'a.id DESC';
		if(!isset($condition['count']))	 $condition['count'] = true;
		return parent::_query($condition);					
	}

	/**
	 * 根据标签搜索出指定条数的相关文章 
	 *
	 * @param $tag string|array 由","或空格隔开的标签列表或单一标签或数组
	 * @param $num int 返回多少条默认为5条
	 * @param $article_id id 要过滤掉的文章id
	 */
	public function findAssoc($tag, $num=5, $article_id=null)
	{
		if(is_array($tag))
			$tag = "IN('" . join("','", $tag) . "')";
		elseif(strpos($tag, ','))
			$tag = "IN('" . join("','",explode(",", $tag)) . "')";
		elseif(strpos($tag, ' '))
			$tag = "IN('" . join("','",explode(" ", $tag)) . "')";
		else
			$tag = "='". $tag . "'";
		$condition['fields'] = "article_id, count(*) AS rank";
		$condition['from']  = Config::get("tablePrefix") . "tags";
		$condition['where'] = "keyword ". $tag;
		if($article_id) $condition['where'] .= " AND article_id !=" . $article_id;
		$condition['group'] = "article_id";
		$condition['order'] = "rank DESC";
		$condition['limit'] = $num;
		$this->clearFilter();
		$rows = parent::_query($condition);
		unset($condition);
		//如果没有相关文章，返回
		if(empty($rows)) return false;
		foreach($rows as $r)
			$artId[] = $r['article_id'];
		$condition['fields'] = "id, title, sort_id, post_time";
		$condition['where'] = "id IN(" . join(",", $artId) . ")";
		$this->setFilter(array($this, 'timeFormatter'), self::FILTER_FIND);
		return parent::_query($condition);
	}

	//审核
	public function setAudit($id,$audit)
	{
		//为null时对所有数据操作
		if(is_null($id))
		{
			$search = $audit == 0 ? 1 : 0;
			$sql = sprintf('UPDATE %s SET is_audit=%d WHERE is_audit=%d', $this->fullTableName, $audit, $search);
			return $this->DB->exec($sql);
		}
		$sql = sprintf('UPDATE %s SET is_audit=%d WHERE id ', $this->fullTableName, $audit);
		if(is_array($id))
			$sql .= sprintf('IN(%s)', join(",", $id));
		else
			$sql .= "=".$id;
		return $this->DB->exec($sql);
	}

	//推荐
	public function setRecmd($id, $status)
	{
		$sql = sprintf('UPDATE %s SET is_recmd=%d WHERE id ', $this->fullTableName, $status);
		if(is_array($id))
			$sql .= sprintf('IN(%s)', join(",", $id));
		else
			$sql .= "=".$id;
		return $this->DB->exec($sql);
	}

	//时间格式化
	public function timeFormatter($row)
	{
		$row['post_time'] = date("Y-m-d", $row['post_time']);
		return $row;
	}

	public function filter($row)
	{
		$row['post_time'] = date("Y-m-d", $row['post_time']);
		$row['auditText'] = $row['is_audit'] ? '<img src="images/audit1.gif" align="absmiddle"/> 已审核'
											: '<img src="images/audit0.gif" align="absmiddle"/> 未审核';
		$row['title'] = !empty($row['titleColor']) ? "<span style='color:".$row['titleColor']."'>".$row['title']."</span>" : $row['title'];
		$row['thumb'] = $row['is_pic'] ? "<img src=images/pic.gif align='absmiddle' alt='文章有缩略图'/>" : "";
		$row['is_recmd']= $row['is_recmd'] ? '荐' : '';
		return $row;
	}

	//生成xml页面
	public function toXML($data)
	{
		//先取得相关文章
		$assocTpl =  file_get_contents(XLITE_SYS_TPL . DS . "assocTpl.xml");
		$assoc = array();
		if(!empty($data['tags']))
		{
			$res = $this->findAssoc($data['tags'], 5, $data['id']);
			if($res !== false)
			{
				foreach($res as $art)
					$assoc[] = sprintf($assocTpl, $art['id'], $art['title'], $art['sort_id'], $art['post_time']);
			}
		}
		//先将时间转换
		$data['post_time'] = date("Y-m-d", $data['post_time']);
		$tpl = file_get_contents(XLITE_SYS_TPL . DS . "contentPageTpl.xml");
		$out = sprintf($tpl, $data['id'], $data['title'], $data['summary'], $data['post_time'], $data['author'],
					$data['come_from'], $data['content'], $data['sort_id'], $data['tags'], join("", $assoc));
		file_put_contents(ARTICLE_PATH . DS .$data['sort_id'] . DS . $data['id'] . ".xml", $out);
	}

	public function toHTML($data)
	{
		//进行时间转换
		$data['post_time'] = date("Y-m-d", $data['post_time']);

		Plite::load('tplParser', Config::get("modelPath"));
		$tp = new tplParser(XLITE_APP_TPL.DS.'article.htm');
		//$tp->setDebug();
		$tp->parse();
		$n = $tp->getData('assocArtNumber');
		if(!$n) $n = 5; //默认取出5条
		//取出相关文章
		$res = $this->findAssoc($data['tags'], $n, $data['id']);
		$htm = ARTICLE_PATH . DS .$data['sort_id'] . DS . $data['id'] . ".htm";

		$sort		 = Plite::modelFactory("sort");
		$currentNode = $sort->getNode($data['sort_id']);
		$positionNav = $sort->getAncestor($data['sort_id']);		
		$tp->setData("currentSort", $currentNode);
		$tp->setData("positionNavigator", $positionNav);
		$tp->setData("articleDetail", $data);
		$tp->setData("assocArticle", $res);
		file_put_contents($htm, $tp->fillData());
	}
}
?>