<?php
//====================================================
//		FileName:M_image.php
//		Summary: 图片Model
//		Author: ice_berg16(寻梦的稻草人)
//		LastModifed:2006-10-03
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
class M_image extends model
{
	protected $table = 'image';
	
	//生成图片的路径
	public function f($galleryId, $id, $ext)
	{
		return GALLERY_PATH.DS.$galleryId.DS.$id.".".$ext;
	}

	//返回图片文件的虚拟路径
	public function vp($f)
	{
		return str_replace(GALLERY_PATH, GALLERY_VIRTUAL_PATH, $f);
	}
	
	//返回图片文件的物理路径
	public function p($f)
	{
		return str_replace(GALLERY_VIRTUAL_PATH, GALLERY_PATH, $f);
	}

	//返回缩略图的虚拟路径
	public function thumbnail($galleryId)
	{
		return GALLERY_VIRTUAL_PATH.DS.$galleryId.DS."index.jpg";
	}

	/*
	 * 上传图片到UserFiles文件夹 
	 * 生成的名称为gallery_galleryId_index.gif
	 * galleryId为相册id, index为递增序列
	 *
	 * @param $galleryId int 相册的id
	 * @param $files $_FILES['image']
	 * @param $thumbIndex 生成缩略图的索引，null则不生成缩图
	 * @return array 返回上传后的图片虚拟路径数组
	 */
	public function upload($galleryId, $files, $thumbIndex=null)
	{
		$uploaded = array();
		foreach( $files['error'] as $k => $err )
		{
			//不为0说明有错误或没上传
			if($err != 0) continue;
			$ext  = substr($files['name'][$k], strrpos($files['name'][$k], ".")+1); //得到图片扩展名
			//随机产生文件名
			$fname = uniqid("img_");
			$newPath = $this->f($galleryId, $fname, $ext);
			if(!move_uploaded_file($files['tmp_name'][$k], $newPath))
				throw new Exception("上传文件时发错误.");
			
			//生成缩片
			if(!is_null($thumbIndex) && $k == $thumbIndex)
				$this->makeThumb($galleryId, $newPath);
			//保存虚拟路径，同时保持索引关联
			$uploaded[$k] = $this->vp($newPath);
		}
		return $uploaded;
	}

	/*
	 * 生成gallery的缩略图
	 *
	 * @param $galleryId 相册id
	 * @param $index 生成缩略图用的图片的绝对路径
	 * @param $fname 返回生成的图片名称
	 */
	public function makeThumb($galleryId, $file)
	{
		$fname = 'index.jpg';
		$img = Plite::libFactory("Image", array($file));
		$img->thumbnail(THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT, dirname($file).DS.$fname);
		return $fname;
	}
}
?>