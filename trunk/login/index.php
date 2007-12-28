<?php
/**
 * 登录跳转页面 
 *
 * 当访问该页面时，系统会自动跳转到登录界面
 *
 * @package    xlite
 * @author     ice_berg16(寻梦的稻草人)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id: index.php 63 2006-10-30 08:34:25Z icesyc $
 * @link       http://www.plite.net
 */

header("Location: ../admin.php?C=index&A=login");
?>