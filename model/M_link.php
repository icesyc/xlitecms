<?php
//====================================================
//		FileName:M_link.php
//		Summary: 
//		Author: ice_berg16(Ѱ�εĵ�����)
//		LastModifed:2006-10-27
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
class M_link extends model
{
	protected $table = 'link';

	//����
	public function save($link)
	{
		//ͼƬ���ӣ��ϴ�ͼƬ
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
	
	//����
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

	//ɾ��
	public function delete($id)
	{
		//��ɾ������ͼƬ
		$res = $this->findById($id, 'image');
		foreach( $res as $r )
		{
			if(file_exists($r['image']))
				@unlink($this->rp($r['image']));
		}
		return parent::delete($id);
	}

	//������
	public function filter($row)
	{
		$row['title'] = $row['type'] == 1 ? $row['title'] : sprintf('<img src="%s" alt="%s" class="link-img"/>', $row['image'], $row['title']);
		$row['type'] = $row['type'] == 1 ? '����' : 'ͼƬ';
		return $row;
	}

	//��������·��
	private function rp($p=null)
	{
		if(is_null($p)) return SCRATCHER_RESOURCE_PATH . "/Image";
		return str_replace(RESOURCE_VIRTUAL_PATH, SCRATCHER_RESOURCE_PATH, $p);
	}
	//��������·��
	private function vp($p=null)
	{
		if(is_null($p)) return RESOURCE_VIRTUAL_PATH . "/Image";
		return str_replace(SCRATCHER_RESOURCE_PATH, RESOURCE_VIRTUAL_PATH, $p);
	}
}
?>