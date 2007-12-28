<?php
/**
 * ���԰���ʾ����
 *
 * @package    controller
 * @author     ice_berg16(Ѱ�εĵ�����)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id: guestbook.php 81 2006-12-20 06:45:56Z icesyc $
 * @link       http://www.xlite.cn
 */

require_once("../../PliteGBK/Plite/Init.php");
Config::import("../config.php");
Plite::load('Plite.Model');

if(!isset($_REQUEST['A'])) $_REQUEST['A'] = 'index';
switch($_REQUEST['A'])
{
	//��ʾ��֤��
	case 'imgCode':
		$ic = Plite::libFactory("ImageCode");
		$ic->makeCode();
		break;
	case 'save':
		$ic = Plite::libFactory("ImageCode");
		//�����֤��
		if(!$ic->isValidCode()) die("��֤�����");
		$guestbook = Plite::modelFactory("guestbook");
		$guestbook->save($_POST);
		header("Location: ". $_SERVER['HTTP_REFERER']);
		break;
	case 'index':
		Plite::load('tplParser', Config::get("modelPath"));
		$tp = new tplParser(XLITE_APP_TPL.DS."guestbook.htm");
		$tp->parse();
		$guestbook = Plite::modelFactory("guestbook");
		$page = isset($_GET['page']) ? $_GET['page'] : 1;
		$rows = $tp->getData("guestbookPageSize");
		$result = $guestbook->find($rows, $page);
		$p = $guestbook->getPageParam();
		$p['class'] = 'Pager';	//��ҳ����ʹ�õ���
		$tp->setData("guestbook", $result);
		$tp->setData("pager", $p);
		echo $tp->result();
		break;
	default:
		die('ϵͳ����!');
		break;
}
?>