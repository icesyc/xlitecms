<?php
//====================================================
//		FileName:M_ftp.php
//		Summary: 
//		Author: ice_berg16(Ѱ�εĵ�����)
//		LastModifed:$Id: M_ftp.php 85 2007-01-05 02:58:55Z icesyc $
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
class M_ftp extends model
{
	protected $table = 'ftp';

	//�б�
	public function find($condition=array())
	{
		$condition['fields']= 'f.*, s.title AS sort';
		$condition['from'] = $this->fullTableName . " AS f";
		$condition['join'] = array("table" => Config::get("tablePrefix") . "sort AS s", "on" => "f.sort_id=s.id");
		if(!isset($condition['filter']))
			$this->setFilter(array($this, 'filter'), self::FILTER_FIND);
		return parent::_query($condition);
	}
	
	//�����ϴη�������
	public function updatePubDate($id)
	{
		$id = is_array($id) ? 'IN ('.join(",", $id).')' : '='.$id;
		$sql = sprintf("UPDATE %s SET last_pub_date = %d	WHERE id %s",
						$this->table, time(), $id);
		return $this->DB->exec($sql);
	}

	//���˺���
	public function filter($r)
	{
		if($r['last_pub_date'] == 0)
			$r['last_pub_date'] = '��δ����';
		else
			$r['last_pub_date'] = date("Y-m-d", $r['last_pub_date']);
		if($r['sort_id'] == 0)
			$r['sort'] = 'ȫ������';
		return $r;
	}
}
?>