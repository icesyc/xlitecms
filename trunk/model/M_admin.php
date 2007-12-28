<?php
//====================================================
//		FileName: M_admin.php
//		Summary:  �ʻ�������
//		Author: ice_berg16(Ѱ�εĵ�����)
//		LastModifed:2006-9-15
//		copyright (c)2004-2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================

Plite::load("Plite.Lib.RBAC.User");
class M_admin extends User
{
	protected $table = 'admin';

	//ȡ�������˻��б�
	public function findAll()
	{
		$condition['fields'] = 'a.user_id, a.user_name, a.role_id, r.role_name';
		$condition['from'] = $this->fullTableName . " AS a";
		$condition['join'] = array(
			'table' => Config::get("tablePrefix") . 'role AS r',
			'on'	=> 'a.role_id=r.role_id'
		);
		return parent::_query($condition);
	}
}
?>