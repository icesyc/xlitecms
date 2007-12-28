<?php
//====================================================
//		FileName:M_gallery.php
//		Summary: 图片组Model
//		Author: ice_berg16(寻梦的稻草人)
//		LastModifed:2006-10-03
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
class M_gallery extends model
{
	protected $table = 'gallery';
	
	//保存图片和图片集
	public function save($gallery, $files)
	{
		//先生成相册记录
		$galleryId = parent::save($gallery);
		@mkdir($this->p($galleryId)) or die("生成目录".$this->p($galleryId)."时失败，请确认有此权限");
		$img = Plite::modelFactory("image");

		//上传图片
		$imgPath = $img->upload($galleryId, $files, $gallery['thumbIndex']);

		//得到缩图虚拟路径
		$thumbnail = $img->thumbnail($galleryId);

		//更新相册信息
		parent::update(array("id" => $galleryId, "pic_number" => count($imgPath), "thumbnail" => $thumbnail));
		
		//添加图片信息到数据库
		foreach( $imgPath as $k => $image )
		{
			$title = $gallery['img_title'][$k];
			$img->save(array("title" => $title, "image" => $image, "gallery_id" => $galleryId));
		}
		
		return $galleryId;
	}

	//删除
	public function delete($id)
	{
		//删除图片信息
		if(!is_array($id)) $id = array($id);
		try{
		foreach( $id as $galleryId)
			Plite::libFactory("FileSystem")->removeDir($this->p($galleryId));
		}catch(Exception $e){}
		//删除数据库记录
		$img = Plite::modelFactory("image");
		$img->deleteByGalleryId($id);
		return $this->delete($id);
	}

	/*
	 * 更新相册的图片数目 
	 *
	 * @param $id int  相册id
	 * @param $num int 增加的数目
	 */
	public function updateNumber($id, $num)
	{
		$sql = sprintf("UPDATE %s SET pic_number=pic_number+%d WHERE id=%d", $this->fullTableName, $num, $id);
		return $this->DB->exec($sql);
	}

	/*
	 * 取记录集 
	 *
	 * @param $page int 页数
	 * @param $rows int 每页记录数
	 * @param $sortId int 分类id
	 * @param $count 是否计算分页参数
	 * @param $filter 记录回调函数
	 */
	public function find($page, $rows, $sortId=null, $count=true, $filter=null)
	{
		$condition['page']  = $page;
		$condition['limit'] = $rows;
		$condition['fields'] = "g.id, g.pic_number, g.title, g.thumbnail, g.sort_id, s.title AS sort";
		$condition['from']  = $this->fullTableName . " AS g";
		$condition['join']  = array("table" => Config::get("tablePrefix") . "sort AS s", "on" => "g.sort_id=s.id");
		//分类搜索
		if($sortId)
		{
			$sort  = Plite::modelFactory("sort");
			$child = $sort->getChild($sortId);
			if(is_array($child))
				$condition['where'][] = sprintf('g.sort_id in(%s)', join(",",$child));
			else
				$condition['where'][] = sprintf('g.sort_id=%d', $child);
		}
		$condition['order'] = "g.id DESC";
		$condition['count'] = $count;
		if(!is_null($filter)) $this->setFilter($filter, self::FILTER_FIND);
		return parent::_query($condition);
	}
	
	//返回相册的虚拟路径
	public function vp($id)
	{
		return GALLERY_VIRTUAL_PATH.DS.$id;
	}
	
	//返回相册的物理路径
	public function p($id)
	{
		return GALLERY_PATH.DS.$id;
	}
}
?>