<?php
//====================================================
//		FileName:M_sort.php
//		Summary: ���·���Model
//		Author: ice_berg16(Ѱ�εĵ�����)
//		LastModifed:2006-09-23
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
Plite::load("Plite.Lib.Tree");

class M_sort extends Tree
{
	//����ʱ�ڵ�ǰ���СͼƬ
	public $indentImg = null;

	public function __construct()
	{
		try
		{
			$DB = Plite::get("DB");
		}
		catch(Exception $e)
		{
			Plite::load("Plite.Db.DataSource");
			$DB = DataSource::getInstance(Config::get("dsn"));
			Plite::set("DB", $DB);
		}
		$table = Config::get("tablePrefix") . 'sort';
		parent::__construct($DB, $table);
	}

	public function save($data)
	{
		if(empty($data['id']))
		{
			$newId = $this->add($data['parentId'], array($data['title']), $data['position']);
			if($newId)
				Plite::libFactory("FileSystem")->mkdir(ARTICLE_PATH . DS . $newId);
			return $newId;
		}
		else
			return $this->setNode($data['id'], array('title'=>$data['title']));
	}

	//ɾ���ڵ�
	public function delete($id)
	{
		if(is_array($id))
		{
			$sql = sprintf("DELETE FROM %s WHERE id IN(%s)", $this->table, join(",", $id));
			$this->DB->exec($sql);
		}
		else
		{
			//ȡ�øýڵ�������ӽڵ�
			$oldId = $id;
			$id = $this->getChild($oldId);
			parent::removeNode($oldId);
		}
		//ɾ�����¼�¼
		$art = Plite::modelFactory("article")->deleteBySortId($id);
		
		//ɾ����Ŀ¼������ҳ��
		$fs = Plite::libFactory("FileSystem");
		if(is_array($id))
		{			
			foreach( $id as $dir )
				$fs->removeDir(ARTICLE_PATH . DS . $dir);
		}
		else
		{
			$fs->removeDir(ARTICLE_PATH . DS . $id);
		}
		return true;
	}

	/*
	 * ����id���ҷ�����Ϣ 
	 *
	 * @param $id int | array 
	 * @return ��¼��
	 */
	public function findById($id)
	{
		$where = is_array($id) ? "id IN(". join(",", $id) . ")" : "id=". $id;
		$sql = sprintf("SELECT * FROM %s WHERE %s", $this->table, $where);
		$st  = $this->DB->query($sql);
		return $st->fetchAll();
	}

	/*
	 * �����ݸ�ʽ���������б��ҳ���б��ʽ 
	 *
	 * @param $data array ����
	 * @return array ��ʽ���������
	 */
	public function format($data, $for="select")
	{
		$func = "indent". ucfirst($for);
		return array_map(array($this, $func), $data);
	}

	/*
	 * ȡ�������ӽڵ�
	 *
	 * @param ���ڵ�
	 * @param $withParent �Ƿ���ͬ���ڵ�һ�𷵻�
	 * @return array|int �ӽڵ����鼰���ڵ� ��ֻ�и��ڵ�
	 */
	public function getChild($parentId, $withParent=true)
	{
		$rows = parent::listTree($parentId);
		$res  = array();
		foreach($rows as $row)
		{
			array_push($res, $row['id']);
		}
		if($withParent)	array_push($res, $parentId);
		return $res;
	}

	public function indentSelect($node)
	{
		$node['title'] = $this->setDeep($node['title'], $node['deep']);
		return $node;
	}
	public function indentImg($node)
	{
		$node['title'] = $this->setDeep($node['title'], $node['deep'], $this->indentImg);
		return $node;
	}
	public function indentChkbox($node)
	{
		$ckbox = sprintf("<input type='checkbox' name='id[]' value='%d'/>", $node['id']);
		$node['title'] = $this->setDeep($node['title'], $node['deep'], $ckbox);
		return $node;
	}
	public function setDeep( $text, $deep, $img=null )
	{
		if( $img != null )
		{
			$text = $img . $text;
		}
		if( $deep == 0 )
			return $text;
		$prefix = str_repeat( "��", $deep * 2 );

		return $prefix . $text;
	}
}
?>