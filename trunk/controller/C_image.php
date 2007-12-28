<?php
/**
 * image 控制器
 *
 * @author     ice_berg16(寻梦的稻草人)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id$
 */

class C_image extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->setLayout('head', 'admin_head');
	}
	
	//列表
	public function index()
	{
		list($gallery, $image) = Plite::modelFactory("gallery", "image");
		$g = $gallery->get($_GET['gallery_id']);
		$imageList = $image->findAllByGalleryId($_GET['gallery_id'], 'id, title, image', 'id');
		$this->set('imageList', $imageList);
		$this->set('galleryTitle', $g['title']);
		$this->set('gallery_id', $g['id']);
		$this->set('noRecord', count($imageList) > 0 ? 'none' : '');
	}

	//上传一个新图片到当前相册
	public function save()
	{
		try
		{
			$this->autoRender = false;
			if(!$this->isPost()) throw new Exception("错误的操作.");
			list($gallery, $image) =  Plite::modelFactory("gallery", "image");
			//上传图片
			$uploaded = $image->upload($_POST['gallery_id'], $_FILES['image']);
			$data = array("title" => $_POST['title'], "image" => $uploaded[0], "gallery_id" => $_POST['gallery_id']);
			//更新相册图片数量
			$gallery->updateNumber($_POST['gallery_id'], count($uploaded));
			//保存图片信息到数据库
			$image->save($data);
			$this->redirect($_SERVER['HTTP_REFERER']);
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}

	//删除
	public function delete()
	{
		try
		{
			$this->autoRender = false;
			list($gallery, $image) = Plite::modelFactory("gallery", "image");
			if(empty($_GET['id'])) throw new Exception("未指定id");
			//先得到图片信息
			$imgList = $image->findAllById($_GET['id']);
			if(empty($imgList)) throw new Exception("没找到记录");
			$galleryId = $imgList[0]['gallery_id'];
			//删除图片信息
			$num = $image->delete($_GET['id']);
			//删除图片
			foreach( $imgList as $img )
			{
				if(file_exists($image->p($img['image'])))
					@unlink($image->p($img['image']));
			}
			//更新相册图片数目
			$gallery->updateNumber($galleryId, -$num);
			exit('{"status":"REQUEST_OK"}');
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}
}
?>