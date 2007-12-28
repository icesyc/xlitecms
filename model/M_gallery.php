<?php
//====================================================
//		FileName:M_gallery.php
//		Summary: ͼƬ��Model
//		Author: ice_berg16(Ѱ�εĵ�����)
//		LastModifed:2006-10-03
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
class M_gallery extends model
{
	protected $table = 'gallery';
	
	//����ͼƬ��ͼƬ��
	public function save($gallery, $files)
	{
		//����������¼
		$galleryId = parent::save($gallery);
		@mkdir($this->p($galleryId)) or die("����Ŀ¼".$this->p($galleryId)."ʱʧ�ܣ���ȷ���д�Ȩ��");
		$img = Plite::modelFactory("image");

		//�ϴ�ͼƬ
		$imgPath = $img->upload($galleryId, $files, $gallery['thumbIndex']);

		//�õ���ͼ����·��
		$thumbnail = $img->thumbnail($galleryId);

		//���������Ϣ
		parent::update(array("id" => $galleryId, "pic_number" => count($imgPath), "thumbnail" => $thumbnail));
		
		//���ͼƬ��Ϣ�����ݿ�
		foreach( $imgPath as $k => $image )
		{
			$title = $gallery['img_title'][$k];
			$img->save(array("title" => $title, "image" => $image, "gallery_id" => $galleryId));
		}
		
		return $galleryId;
	}

	//ɾ��
	public function delete($id)
	{
		//ɾ��ͼƬ��Ϣ
		if(!is_array($id)) $id = array($id);
		try{
		foreach( $id as $galleryId)
			Plite::libFactory("FileSystem")->removeDir($this->p($galleryId));
		}catch(Exception $e){}
		//ɾ�����ݿ��¼
		$img = Plite::modelFactory("image");
		$img->deleteByGalleryId($id);
		return $this->delete($id);
	}

	/*
	 * ��������ͼƬ��Ŀ 
	 *
	 * @param $id int  ���id
	 * @param $num int ���ӵ���Ŀ
	 */
	public function updateNumber($id, $num)
	{
		$sql = sprintf("UPDATE %s SET pic_number=pic_number+%d WHERE id=%d", $this->fullTableName, $num, $id);
		return $this->DB->exec($sql);
	}

	/*
	 * ȡ��¼�� 
	 *
	 * @param $page int ҳ��
	 * @param $rows int ÿҳ��¼��
	 * @param $sortId int ����id
	 * @param $count �Ƿ�����ҳ����
	 * @param $filter ��¼�ص�����
	 */
	public function find($page, $rows, $sortId=null, $count=true, $filter=null)
	{
		$condition['page']  = $page;
		$condition['limit'] = $rows;
		$condition['fields'] = "g.id, g.pic_number, g.title, g.thumbnail, g.sort_id, s.title AS sort";
		$condition['from']  = $this->fullTableName . " AS g";
		$condition['join']  = array("table" => Config::get("tablePrefix") . "sort AS s", "on" => "g.sort_id=s.id");
		//��������
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
	
	//������������·��
	public function vp($id)
	{
		return GALLERY_VIRTUAL_PATH.DS.$id;
	}
	
	//������������·��
	public function p($id)
	{
		return GALLERY_PATH.DS.$id;
	}
}
?>