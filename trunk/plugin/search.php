<?php
//====================================================
//		FileName:	search.php
//		Summary:	搜索程序
//		Author:		ice_berg16(寻梦的稻草人)
//		LastModifed:2006-10-13
//		copyright (c)2006 ice_berg16@163.com
//====================================================
require_once("../../PliteGBK/Plite/Init.php");
Config::import("../config.php");
Plite::load('Plite.Model');

//显示搜索结果
if(!empty($_GET['keyword']))
{
	Plite::load('tplParser', Config::get("modelPath"));
	$tp = new tplParser(XLITE_APP_TPL.DS."searchResult.htm");
	$tp->parse();
	$art = Plite::modelFactory("article");
	$condition['tag'] = $_GET['keyword'];
	$condition['isAudit'] = 1;
	$condition['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
	$condition['limit'] = $tp->getData("articlePageSize");
	$result = $art->find($condition);
	$p = $art->getPageParam();
	$p['class'] = 'Pager';	//分页导航使用的类
	$tp->setData("searchArticle", $result);
	$tp->setData("pager", $p);
	$tp->setData("variable", array("keyword" => $_GET['keyword']));
	echo $tp->result();
}
else	//显示表单
{
	
}

?>