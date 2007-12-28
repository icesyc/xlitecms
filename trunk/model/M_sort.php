<?php
//====================================================
//		FileName:M_sort.php
//		Summary: 文章分类Model
//		Author: ice_berg16(寻梦的稻草人)
//		LastModifed:2006-09-23
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
Plite::load("Plite.Lib.Tree");

class M_sort extends Tree
{
	//缩进时节点前面的小图片
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

	//删除节点
	public function delete($id)
	{
		if(is_array($id))
		{
			$sql = sprintf("DELETE FROM %s WHERE id IN(%s)", $this->table, join(",", $id));
			$this->DB->exec($sql);
		}
		else
		{
			//取得该节点的所有子节点
			$oldId = $id;
			$id = $this->getChild($oldId);
			parent::removeNode($oldId);
		}
		//删除文章记录
		$art = Plite::modelFactory("article")->deleteBySortId($id);
		
		//删除子目录及文章页面
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
	 * 根据id查找分类信息 
	 *
	 * @param $id int | array 
	 * @return 记录集
	 */
	public function findById($id)
	{
		$where = is_array($id) ? "id IN(". join(",", $id) . ")" : "id=". $id;
		$sql = sprintf("SELECT * FROM %s WHERE %s", $this->table, $where);
		$st  = $this->DB->query($sql);
		return $st->fetchAll();
	}

	/*
	 * 将数据格式化成下拉列表或页面列表格式 
	 *
	 * @param $data array 数据
	 * @return array 格式化后的数据
	 */
	public function format($data, $for="select")
	{
		$func = "indent". ucfirst($for);
		return array_map(array($this, $func), $data);
	}

	/*
	 * 取得所有子节点
	 *
	 * @param 父节点
	 * @param $withParent 是否连同父节点一起返回
	 * @return array|int 子节点数组及父节点 或只有父节点
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
		$prefix = str_repeat( "　", $deep * 2 );

		return $prefix . $text;
	}
}
?>