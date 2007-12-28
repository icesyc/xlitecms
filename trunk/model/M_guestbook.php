<?php
//====================================================
//		FileName:M_guestbook.php
//		Summary: guestbook Model
//		Author: ice_berg16(寻梦的稻草人)
//		LastModifed:2006-10-27
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
class M_guestbook extends model
{
	protected $table = 'guestbook';

	//列表
	public function find($rows, $page=null)
	{
		if($page)
		{
			$condition['page']  = $page;
			$condition['count'] = true;
		}
		$condition['limit']  = $rows;
		$condition['order']  = "id DESC";
		$this->setFilter(array($this, 'filter'), self::FILTER_FIND);
		return parent::_query($condition);
	}
	
	//过滤器
	public function filter($row)
	{
		$row['postTime'] = date("Y-m-d", $row['postTime']);
		$row['content']  = Plite::libFactory("html")->h($row['content']);
		$row['reply']	 = Plite::libFactory("html")->h($row['reply']);
		return $row;
	}
}
?>