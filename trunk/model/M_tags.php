<?php
//====================================================
//		FileName:M_tags.php
//		Summary: 文章标签Model
//		Author: ice_berg16(寻梦的稻草人)
//		LastModifed:2006-12-30
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
class M_tags extends model
{
	protected $table = 'tags';

	/*
	 * 查找最热门的tag 
	 *
	 * @param int $limit 取得多少条记录
	 * @return array 返回的记录集
	 */
	public function findHot($limit=10)
	{
		$sql = "SELECT keyword, count(article_id) AS num
				FROM %s GROUP BY keyword
				ORDER BY num DESC
				LIMIT %d";
		return parent::findAllBySql(sprintf($sql, $this->fullTableName, $limit));
	}

	//标签关键字列表
	public function listTag($page=1, $limit=20, $keyword=null)
	{
		if(!is_null($keyword))
			$where = "keyword='$keyword'";
		$condition['fields'] = 'keyword, count(article_id) AS num';
		$condition['group']	 = 'keyword';
		$condition['page']	 = $page;
		$condition['limit']   = $limit;
		$condition['count']  = true;
		$this->setFilter(array($this, 'filter'), self::FILTER_FIND);
		return parent::_query($condition);
	}

	public function filter($rec)
	{
		$rec['enc_keyword'] = urlencode($rec['keyword']);
		return $rec;
	}
}
?>