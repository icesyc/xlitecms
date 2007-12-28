<?php
//====================================================
//		FileName:	incex.php
//		Summary:	安装程序入口
//		Author:		ice_berg16(寻梦的稻草人)
//		version:	$Id: index.php 84 2006-12-31 07:30:21Z icesyc $
//		copyright (c)2006 ice_berg16@163.com
//====================================================

try
{
	require_once("../../PliteGBK/Plite/Init.php");
	require_once('install.php');
	$act = isset($_REQUEST['A']) ? $_REQUEST['A'] : 'index';
	$ctl = new install();
	$ctl->$act();
}
catch(Exception $e)
{
	switch($e->getCode())
	{
		case 1045:
			$msg = '数据库连接失败，请检查用户名和密码.';
			break;
		default:
			$msg = ($e->getCode() ? '['.$e->getCode().'] ' : '') . $e->getMessage();
			break;
	}	
	$ctl->output('install_halt.htm', $msg);
}
?>