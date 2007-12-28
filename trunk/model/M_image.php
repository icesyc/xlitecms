<?php
//====================================================
//		FileName:M_image.php
//		Summary: ͼƬModel
//		Author: ice_berg16(Ѱ�εĵ�����)
//		LastModifed:2006-10-03
//		copyright(c)2006 ice_berg16@163.com
//		http://www.plite.net
//====================================================
class M_image extends model
{
	protected $table = 'image';
	
	//����ͼƬ��·��
	public function f($galleryId, $id, $ext)
	{
		return GALLERY_PATH.DS.$galleryId.DS.$id.".".$ext;
	}

	//����ͼƬ�ļ�������·��
	public function vp($f)
	{
		return str_replace(GALLERY_PATH, GALLERY_VIRTUAL_PATH, $f);
	}
	
	//����ͼƬ�ļ�������·��
	public function p($f)
	{
		return str_replace(GALLERY_VIRTUAL_PATH, GALLERY_PATH, $f);
	}

	//��������ͼ������·��
	public function thumbnail($galleryId)
	{
		return GALLERY_VIRTUAL_PATH.DS.$galleryId.DS."index.jpg";
	}

	/*
	 * �ϴ�ͼƬ��UserFiles�ļ��� 
	 * ���ɵ�����Ϊgallery_galleryId_index.gif
	 * galleryIdΪ���id, indexΪ��������
	 *
	 * @param $galleryId int ����id
	 * @param $files $_FILES['image']
	 * @param $thumbIndex ��������ͼ��������null��������ͼ
	 * @return array �����ϴ����ͼƬ����·������
	 */
	public function upload($galleryId, $files, $thumbIndex=null)
	{
		$uploaded = array();
		foreach( $files['error'] as $k => $err )
		{
			//��Ϊ0˵���д����û�ϴ�
			if($err != 0) continue;
			$ext  = substr($files['name'][$k], strrpos($files['name'][$k], ".")+1); //�õ�ͼƬ��չ��
			//��������ļ���
			$fname = uniqid("img_");
			$newPath = $this->f($galleryId, $fname, $ext);
			if(!move_uploaded_file($files['tmp_name'][$k], $newPath))
				throw new Exception("�ϴ��ļ�ʱ������.");
			
			//������Ƭ
			if(!is_null($thumbIndex) && $k == $thumbIndex)
				$this->makeThumb($galleryId, $newPath);
			//��������·����ͬʱ������������
			$uploaded[$k] = $this->vp($newPath);
		}
		return $uploaded;
	}

	/*
	 * ����gallery������ͼ
	 *
	 * @param $galleryId ���id
	 * @param $index ��������ͼ�õ�ͼƬ�ľ���·��
	 * @param $fname �������ɵ�ͼƬ����
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