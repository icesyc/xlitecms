<?php
/**
 * image ������
 *
 * @author     ice_berg16(Ѱ�εĵ�����)
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
	
	//�б�
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

	//�ϴ�һ����ͼƬ����ǰ���
	public function save()
	{
		try
		{
			$this->autoRender = false;
			if(!$this->isPost()) throw new Exception("����Ĳ���.");
			list($gallery, $image) =  Plite::modelFactory("gallery", "image");
			//�ϴ�ͼƬ
			$uploaded = $image->upload($_POST['gallery_id'], $_FILES['image']);
			$data = array("title" => $_POST['title'], "image" => $uploaded[0], "gallery_id" => $_POST['gallery_id']);
			//�������ͼƬ����
			$gallery->updateNumber($_POST['gallery_id'], count($uploaded));
			//����ͼƬ��Ϣ�����ݿ�
			$image->save($data);
			$this->redirect($_SERVER['HTTP_REFERER']);
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}

	//ɾ��
	public function delete()
	{
		try
		{
			$this->autoRender = false;
			list($gallery, $image) = Plite::modelFactory("gallery", "image");
			if(empty($_GET['id'])) throw new Exception("δָ��id");
			//�ȵõ�ͼƬ��Ϣ
			$imgList = $image->findAllById($_GET['id']);
			if(empty($imgList)) throw new Exception("û�ҵ���¼");
			$galleryId = $imgList[0]['gallery_id'];
			//ɾ��ͼƬ��Ϣ
			$num = $image->delete($_GET['id']);
			//ɾ��ͼƬ
			foreach( $imgList as $img )
			{
				if(file_exists($image->p($img['image'])))
					@unlink($image->p($img['image']));
			}
			//�������ͼƬ��Ŀ
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