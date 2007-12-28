<?php
/**
 * 应用程序入口文件
 *
 * @author     ice_berg16(寻梦的稻草人)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */
require_once("../PliteGBK/Plite/Init.php");

//加载配置文件
Config::import("config.php");

//加载MVC框架
Plite::load("Plite.MVC");

//运行
MVC::run();

?>