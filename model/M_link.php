<?php
//====================================================
//		FileName:M_link.php
//		Summary: 
//		Author: ice_berg16(寻梦的稻草人)
//		LastModifed:2006-10-27
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
class M_link extends model
{
	protected $table = 'link';

	//保存
	public function save($link)
	{
		//图片链接，上传图片
		if($link['type'] == 2)
		{
			$uf = Plite::libFactory("FileUploader")->getFile('image');
			
			if($uf->uploadOK())
			{
				$link['image'] = $this->vp($uf->move($this->rp()));
			}
		}
		return parent::save($link);		
	}
	
	//查找
	public function find($rows, $page=null, $type=null)
	{
		if($page)
		{
			$condition['page']  = $page;
			$condition['count'] = true;
		}
		$condition['limit'] = $rows;
		if($type)
			$condition['where'] = 'type='.$type;
		$this->setFilter(array($this, 'filter'), self::FILTER_FIND);
		return parent::_query($condition);
	}

	//删除
	public function delete($id)
	{
		//先删除所有图片
		$res = $this->findById($id, 'image');
		foreach( $res as $r )
		{
			if(file_exists($r['image']))
				@unlink($this->rp($r['image']));
		}
		return parent::delete($id);
	}

	//过滤器
	public function filter($row)
	{
		$row['title'] = $row['type'] == 1 ? $row['title'] : sprintf('<img src="%s" alt="%s" class="link-img"/>', $row['image'], $row['title']);
		$row['type'] = $row['type'] == 1 ? '文字' : '图片';
		return $row;
	}

	//返回物理路径
	private function rp($p=null)
	{
		if(is_null($p)) return SCRATCHER_RESOURCE_PATH . "/Image";
		return str_replace(RESOURCE_VIRTUAL_PATH, SCRATCHER_RESOURCE_PATH, $p);
	}
	//返回虚拟路径
	private function vp($p=null)
	{
		if(is_null($p)) return RESOURCE_VIRTUAL_PATH . "/Image";
		return str_replace(SCRATCHER_RESOURCE_PATH, RESOURCE_VIRTUAL_PATH, $p);
	}
}
?>