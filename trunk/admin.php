<?php
/**
 * Ӧ�ó�������ļ�
 *
 * @author     ice_berg16(Ѱ�εĵ�����)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */
require_once("../PliteGBK/Plite/Init.php");

//���������ļ�
Config::import("config.php");

//����MVC���
Plite::load("Plite.MVC");

//����
MVC::run();

?>