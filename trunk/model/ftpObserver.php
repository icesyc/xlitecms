<?php
/**
 * ʵ�ֶ�FTP�ϴ�ʱ�ļ�������һЩ��Ӧ�Ĵ���
 *
 * @package    model
 * @author     ice_berg16(Ѱ�εĵ�����)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id: ftpObserver.php 74 2006-12-02 02:31:33Z icesyc $
 * @link       http://www.plite.net
 */

class ftpObserver
{
	/*
	 * ���浱ǰftp��Ϣ
	 *
	 * @access private
	 * @var resource
	 */
	private $ftp;

	/*
	 * ���浱ǰĿ¼��������ļ��б�
	 *
	 * @access private
	 * @var array
	 */
	private $processedFileList;
	
	/*
	 * ���洦�����Ŀ¼�б�
	 *
	 * @access private
	 * @var resource
	 */
	private $processedDirList;

	/*
	 * ���洦������ļ�����
	 *
	 * @access private
	 * @var int
	 */
	private $processed;

	/*
	 * Ҫ������ļ����� 
	 *
	 * @access private
	 * @var int
	 */
	private $totalFiles;

	/*
	 * ����Ҫ�ϴ���·��
	 *
	 * @access private
	 * @var array
	 */
	private $path = array();

	/*
	 * �Ƿ����õ���ģʽ
	 *
	 * @access private
	 * @var bool
	 */
	private $dbg = false;

	/*
	 * ���캯�� 
	 *
	 * @param array $ftpIdList ���е�ftpId
	 */
	public function __construct()
	{
		$this->timer = Plite::libFactory("Timer");
		
		//�ָ�����״̬
		$this->processedDirList  = isset($_SESSION['__processedDirList__'])
								 ? $_SESSION['__processedDirList__']
								 : array();
		$this->processedFileList = isset($_SESSION['__processedFileList__'])
								 ? $_SESSION['__processedFileList__']
								 : array();
		$this->processed		 = isset($_SESSION['__processed__'])
								 ? $_SESSION['__processed__']
								 : 0;
		$this->totalFiles		 = isset($_SESSION['__totalFiles__'])
								 ? $_SESSION['__totalFiles__']
								 : 0;
	}

	/*
	 * FTP�ϴ�ʱ�ļ�������
	 *
	 * ʹ�øú�������FTP�ϴ����н��ȼ��ӣ������ظ��ͻ���һЩ��Ϣ
	 *
	 * @param array $status ��ǰ�ϴ�״̬������
	 */
	public function	listener($status)
	{
		
		$this->debug($status);
		//���ļ��ϴ���ʱ���浽������Ķ�����
		if($status['act'] == 'putDirFinish')
		{
			//Ŀ¼������֮����յ�ǰ�ļ��б�
			//$this->processedFileList = array();
			array_push($this->processedDirList, $status['localDir']);
		}
		if($status['act'] == 'putFinish')
		{
			//���Ӽ���
			$this->processed++;
			array_push($this->processedFileList, $status['localDir']."/".$status['file']);

			//����Ƿ�ʱ����ʱ���˳���ͬʱ���ͼ������ź�
			if($this->timer->getExecTime() > FTP_MAX_TIME * 1000)
			{
				$_SESSION['__processedDirList__']	= $this->processedDirList;
				$_SESSION['__processedFileList__']	= $this->processedFileList;
				$_SESSION['__processed__']			= $this->processed;
				exit(sprintf(
						"{status:'PROCESSING',total:%d,processed:%d,title:'������%s�����ļ�...'}",
						$this->totalFiles, $this->processed, $this->ftp['host']
					));
			}
		}
	}

	/*
	 * FTP�ϴ�ʱ�Ĺ��˺��������ڶϵ�����
	 *
	 * @param string $path Դ�ļ���·��
	 * @param bool $isDir �Ƿ�ΪĿ¼
	 * @return bool ���·���Ѿ������꣬������
	 */
	public function	filter($path, $isDir)
	{
		if($isDir)
			return in_array($path, $this->processedDirList);
		else
			return in_array($path, $this->processedFileList);
	}
	
	/*
	 * ����ʼʱ���õĺ��� 
	 *
	 * @param int $id	��ǰid
	 * @param string $host FTP������
	 */
	public function processStart()
	{
		//��һ�δ���ȡ���ļ������������������id����
		if(!isset($_SESSION['__totalFiles__']))
		{
			$_SESSION['__processedIdList__'] = array();
			$fs = Plite::libFactory("FileSystem");
			foreach( $this->path as $dir )
			{	
				//������ļ������������
				if(!is_dir($dir))
					$this->totalFiles++;
				else
					$fs->recursiveDir($dir, array($this, 'countFile'), false, true);
			}
			$_SESSION['__totalFiles__'] = $this->totalFiles;			
			$this->debug("�ļ�������". $this->totalFiles);
		}
		//����Ƿ��Ѿ������
		if(in_array($this->ftp['id'], $_SESSION['__processedIdList__']))
		{
			$this->debug("���� ".$this->ftp['host']);
			return false;
		}

		//�µ�ftpId����
		if(!isset($_SESSION['__currentFtpId__']))
		{
			$this->debug("��ʼ���� ". $this->ftp['host']);

			//���浱ǰid
			$_SESSION['__currentFtpId__'] = $this->ftp['host'];
			exit(sprintf(
				"{status:'PROCESSING',total:%d,processed:0,title:'������%s�����ļ�...'}",
				$_SESSION['__totalFiles__'], $this->ftp['host']
			));
		}
		return true;
	}

	/*
	 * �������ʱ���õĺ��� 
	 *
	 * @param int $id	��ǰid
	 */
	public function processOver()
	{
		//����ǰ�������id���뵽������
		array_push($_SESSION['__processedIdList__'], $this->ftp['id']);
		//�ͷŴ������id���ļ���
		unset($_SESSION['__currentFtpId__']);
		unset($_SESSION['__processed__']);
		unset($_SESSION['__processedDirList__']);
		unset($_SESSION['__processedFileList__']);
		$this->processed = 0;
		$this->debug("���� ".$this->ftp['host']." ����.");
	}
	
	/*
	 * ȫ���������ʱ���õĺ��� 
	 *
	 */
	public function finish()
	{
		//�ͷ�����session����
		unset($_SESSION['__totalFiles__']);
		unset($_SESSION['__processedIdList__']);
		unset($_SESSION['__processed__']);
		$this->debug("ȫ���������.");
		exit(sprintf(
			"{status:'PROCESS_OVER',total:%d,processed:%1\$d}",	$this->totalFiles
		));
	}

	/*
	 * �����õĺ��� 
	 *
	 * @param string|array $msg �������Ϣ����Ϣ����
	 */
	public function debug($msg)
	{
		if($this->dbg)
		{
			if(is_array($msg))
			{
				switch($msg['act'])
				{
					case 'skip':
						if(isset($msg['file']))
							$msg = '�����ļ�: '.$msg['remoteDir']."/".$msg['file'];
						else					
							$msg = '����Ŀ¼: '.$msg['remoteDir'];
						break;
					case 'putFinish':
						$msg = '�ϴ��ɹ�: '. $msg['remoteDir']."/".$msg['file'];
						break;
					case 'putDirFinish':
						$msg = '�ϴ�Ŀ¼����: '. $msg['remoteDir'];
						break;
					case 'mkdir':
						$msg = '����Ŀ¼ '. $msg['remoteDir'];
						break;
					case 'put':
						$msg = '���������ļ�: '. $msg['remoteDir']."/".$msg['file'];
						break;
					default:
						$msg = join("\t", $msg);
						break;
				};
			}
			file_put_contents("ftpDebug.txt", $msg . "\r\n", FILE_APPEND);
		}
	}

	/*
	 * �����ļ������Ļص����� 
	 *
	 * @param $f �ļ���
	 */
	public function countFile($f)
	{
		$this->totalFiles++;
	}

	/*
	 * ����Ҫ�ϴ���·�� 
	 *
	 * @param string|array $path
	 */
	public function setPath($path)
	{
		if(is_array($path))
			$this->path = array_merge($this->path, $path);
		else
			$this->path[] = $path;
	}

	/*
	 * ����һ��FTP��Ϣ 
	 *
	 * @param array $ftp
	 */
	public function setFtp($ftp)
	{
		$this->ftp = $ftp;
	}

	/*
	 * �Ƿ����õ���ģʽ 
	 *
	 * @param bool $debug
	 * @return
	 */
	public function setDebug($debug=true)
	{
		$this->dbg = $debug;
	}
}
?>