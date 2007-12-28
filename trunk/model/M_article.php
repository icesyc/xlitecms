<?php
//====================================================
//		FileName:M_article.php
//		Summary: ����Model
//		Author: ice_berg16(Ѱ�εĵ�����)
//		LastModifed:2006-09-23
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
class M_article extends Model
{

	protected $table = 'article';

	/* �������� */
	public function save($data)
	{
		//�������
		$newId = parent::save($data);
		$newId = !empty($data['id']) ? $data['id'] : $newId;
		//����ǩ���
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
		//����ID���뵽����
		$data['id'] = $newId;
		//����xml�ļ�
		$this->toHTML($data);
		return $newId;
	}
	
	//ɾ������
	public function delete($id)
	{
		//ɾ�����µľ�̬ҳ��
		$where = "id " . (is_array($id) ? "IN(".join(",", $id).")" : "=".$id);
		$sql = sprintf('SELECT id, sort_id FROM %s WHERE ' . $where, $this->fullTableName);
		$this->setFilter(
			create_function('$row', '@unlink(ARTICLE_PATH.DS.$row["sort_id"].DS.$row["id"].".xml");'),
			self::FILTER_FIND
		);
		$this->findAllBySql($sql);
		return parent::delete($id);
	}

	//��������
	public function find($condition)
	{
		if(!isset($condition['fields']))
			$condition['fields'] = "a.id,a.title,a.summary,a.post_time,a.sort_id,a.is_audit,a.is_pic,a.is_recmd,
									a.title_color,a.thumbnail,s.title AS sort";
		$condition['from']  = $this->fullTableName . " AS a";
		$condition['join'][]= array('table' => Config::get("tablePrefix") . 'sort AS s',
									'on'	=> 'a.sort_id=s.id');
		//��ǩ����
		if(isset($condition['tag']))
		{
			$condition['join'][] = array('table' => Config::get("tablePrefix") . 'tags AS t',
									'on'	=> 'a.id=t.article_id');
			$condition['where'][] = sprintf("t.keyword='%s'", $condition['tag']);
		}

		//��������
		if(isset($condition['sort_id']))
		{
			if(is_array($condition['sort_id']))
				$condition['where'][] = sprintf('a.sort_id in(%s)', join(",",$condition['sort_id']));
			else
				$condition['where'][] = sprintf('a.sort_id=%d', $condition['sort_id']);
		}
		//�Ƿ����
		if(isset($condition['is_audit']))
			$condition['where'][] = sprintf('a.is_audit=%d', $condition['is_audit']);
		$condition['order'] = 'a.id DESC';
		if(!isset($condition['count']))	 $condition['count'] = true;
		return parent::_query($condition);					
	}

	/**
	 * ���ݱ�ǩ������ָ��������������� 
	 *
	 * @param $tag string|array ��","��ո�����ı�ǩ�б��һ��ǩ������
	 * @param $num int ���ض�����Ĭ��Ϊ5��
	 * @param $article_id id Ҫ���˵�������id
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
		//���û��������£�����
		if(empty($rows)) return false;
		foreach($rows as $r)
			$artId[] = $r['article_id'];
		$condition['fields'] = "id, title, sort_id, post_time";
		$condition['where'] = "id IN(" . join(",", $artId) . ")";
		$this->setFilter(array($this, 'timeFormatter'), self::FILTER_FIND);
		return parent::_query($condition);
	}

	//���
	public function setAudit($id,$audit)
	{
		//Ϊnullʱ���������ݲ���
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

	//�Ƽ�
	public function setRecmd($id, $status)
	{
		$sql = sprintf('UPDATE %s SET is_recmd=%d WHERE id ', $this->fullTableName, $status);
		if(is_array($id))
			$sql .= sprintf('IN(%s)', join(",", $id));
		else
			$sql .= "=".$id;
		return $this->DB->exec($sql);
	}

	//ʱ���ʽ��
	public function timeFormatter($row)
	{
		$row['post_time'] = date("Y-m-d", $row['post_time']);
		return $row;
	}

	public function filter($row)
	{
		$row['post_time'] = date("Y-m-d", $row['post_time']);
		$row['auditText'] = $row['is_audit'] ? '<img src="images/audit1.gif" align="absmiddle"/> �����'
											: '<img src="images/audit0.gif" align="absmiddle"/> δ���';
		$row['title'] = !empty($row['titleColor']) ? "<span style='color:".$row['titleColor']."'>".$row['title']."</span>" : $row['title'];
		$row['thumb'] = $row['is_pic'] ? "<img src=images/pic.gif align='absmiddle' alt='����������ͼ'/>" : "";
		$row['is_recmd']= $row['is_recmd'] ? '��' : '';
		return $row;
	}

	//����xmlҳ��
	public function toXML($data)
	{
		//��ȡ���������
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
		//�Ƚ�ʱ��ת��
		$data['post_time'] = date("Y-m-d", $data['post_time']);
		$tpl = file_get_contents(XLITE_SYS_TPL . DS . "contentPageTpl.xml");
		$out = sprintf($tpl, $data['id'], $data['title'], $data['summary'], $data['post_time'], $data['author'],
					$data['come_from'], $data['content'], $data['sort_id'], $data['tags'], join("", $assoc));
		file_put_contents(ARTICLE_PATH . DS .$data['sort_id'] . DS . $data['id'] . ".xml", $out);
	}

	public function toHTML($data)
	{
		//����ʱ��ת��
		$data['post_time'] = date("Y-m-d", $data['post_time']);

		Plite::load('tplParser', Config::get("modelPath"));
		$tp = new tplParser(XLITE_APP_TPL.DS.'article.htm');
		//$tp->setDebug();
		$tp->parse();
		$n = $tp->getData('assocArtNumber');
		if(!$n) $n = 5; //Ĭ��ȡ��5��
		//ȡ���������
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