<?php
/**
 * 实现对FTP上传时的监听，做一些相应的处理
 *
 * @package    model
 * @author     ice_berg16(寻梦的稻草人)
 * @copyright  2004-2006 ice_berg16@163.com
 * @version    $Id: ftpObserver.php 74 2006-12-02 02:31:33Z icesyc $
 * @link       http://www.plite.net
 */

class ftpObserver
{
	/*
	 * 保存当前ftp信息
	 *
	 * @access private
	 * @var resource
	 */
	private $ftp;

	/*
	 * 保存当前目录处理过的文件列表
	 *
	 * @access private
	 * @var array
	 */
	private $processedFileList;
	
	/*
	 * 保存处理过的目录列表
	 *
	 * @access private
	 * @var resource
	 */
	private $processedDirList;

	/*
	 * 保存处理过的文件总数
	 *
	 * @access private
	 * @var int
	 */
	private $processed;

	/*
	 * 要传输的文件总数 
	 *
	 * @access private
	 * @var int
	 */
	private $totalFiles;

	/*
	 * 保存要上传的路径
	 *
	 * @access private
	 * @var array
	 */
	private $path = array();

	/*
	 * 是否启用调试模式
	 *
	 * @access private
	 * @var bool
	 */
	private $dbg = false;

	/*
	 * 构造函数 
	 *
	 * @param array $ftpIdList 所有的ftpId
	 */
	public function __construct()
	{
		$this->timer = Plite::libFactory("Timer");
		
		//恢复传输状态
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
	 * FTP上传时的监听函数
	 *
	 * 使用该函数来对FTP上传进行进度监视，并返回给客户端一些信息
	 *
	 * @param array $status 当前上传状态的数组
	 */
	public function	listener($status)
	{
		
		$this->debug($status);
		//当文件上传完时保存到处理完的队列中
		if($status['act'] == 'putDirFinish')
		{
			//目录传输完之后，清空当前文件列表
			//$this->processedFileList = array();
			array_push($this->processedDirList, $status['localDir']);
		}
		if($status['act'] == 'putFinish')
		{
			//增加计数
			$this->processed++;
			array_push($this->processedFileList, $status['localDir']."/".$status['file']);

			//检测是否超时，超时则退出，同时发送继续的信号
			if($this->timer->getExecTime() > FTP_MAX_TIME * 1000)
			{
				$_SESSION['__processedDirList__']	= $this->processedDirList;
				$_SESSION['__processedFileList__']	= $this->processedFileList;
				$_SESSION['__processed__']			= $this->processed;
				exit(sprintf(
						"{status:'PROCESSING',total:%d,processed:%d,title:'正在向%s传送文件...'}",
						$this->totalFiles, $this->processed, $this->ftp['host']
					));
			}
		}
	}

	/*
	 * FTP上传时的过滤函数，用于断点续传
	 *
	 * @param string $path 源文件的路径
	 * @param bool $isDir 是否为目录
	 * @return bool 如果路径已经处理完，则跳过
	 */
	public function	filter($path, $isDir)
	{
		if($isDir)
			return in_array($path, $this->processedDirList);
		else
			return in_array($path, $this->processedFileList);
	}
	
	/*
	 * 处理开始时调用的函数 
	 *
	 * @param int $id	当前id
	 * @param string $host FTP主机名
	 */
	public function processStart()
	{
		//第一次处理，取得文件总数，建立处理过的id数组
		if(!isset($_SESSION['__totalFiles__']))
		{
			$_SESSION['__processedIdList__'] = array();
			$fs = Plite::libFactory("FileSystem");
			foreach( $this->path as $dir )
			{	
				//如果是文件，则计算在内
				if(!is_dir($dir))
					$this->totalFiles++;
				else
					$fs->recursiveDir($dir, array($this, 'countFile'), false, true);
			}
			$_SESSION['__totalFiles__'] = $this->totalFiles;			
			$this->debug("文件总数：". $this->totalFiles);
		}
		//检查是否已经处理过
		if(in_array($this->ftp['id'], $_SESSION['__processedIdList__']))
		{
			$this->debug("跳过 ".$this->ftp['host']);
			return false;
		}

		//新的ftpId处理
		if(!isset($_SESSION['__currentFtpId__']))
		{
			$this->debug("开始处理 ". $this->ftp['host']);

			//保存当前id
			$_SESSION['__currentFtpId__'] = $this->ftp['host'];
			exit(sprintf(
				"{status:'PROCESSING',total:%d,processed:0,title:'正在向%s传送文件...'}",
				$_SESSION['__totalFiles__'], $this->ftp['host']
			));
		}
		return true;
	}

	/*
	 * 处理结束时调用的函数 
	 *
	 * @param int $id	当前id
	 */
	public function processOver()
	{
		//将当前处理完的id加入到数组中
		array_push($_SESSION['__processedIdList__'], $this->ftp['id']);
		//释放处理完的id和文件数
		unset($_SESSION['__currentFtpId__']);
		unset($_SESSION['__processed__']);
		unset($_SESSION['__processedDirList__']);
		unset($_SESSION['__processedFileList__']);
		$this->processed = 0;
		$this->debug("处理 ".$this->ftp['host']." 结束.");
	}
	
	/*
	 * 全部处理结束时调用的函数 
	 *
	 */
	public function finish()
	{
		//释放所有session变量
		unset($_SESSION['__totalFiles__']);
		unset($_SESSION['__processedIdList__']);
		unset($_SESSION['__processed__']);
		$this->debug("全部处理结束.");
		exit(sprintf(
			"{status:'PROCESS_OVER',total:%d,processed:%1\$d}",	$this->totalFiles
		));
	}

	/*
	 * 调试用的函数 
	 *
	 * @param string|array $msg 输出的信息或信息数组
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
							$msg = '跳过文件: '.$msg['remoteDir']."/".$msg['file'];
						else					
							$msg = '跳过目录: '.$msg['remoteDir'];
						break;
					case 'putFinish':
						$msg = '上传成功: '. $msg['remoteDir']."/".$msg['file'];
						break;
					case 'putDirFinish':
						$msg = '上传目录结束: '. $msg['remoteDir'];
						break;
					case 'mkdir':
						$msg = '建立目录 '. $msg['remoteDir'];
						break;
					case 'put':
						$msg = '正在续传文件: '. $msg['remoteDir']."/".$msg['file'];
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
	 * 计算文件总数的回调函数 
	 *
	 * @param $f 文件名
	 */
	public function countFile($f)
	{
		$this->totalFiles++;
	}

	/*
	 * 设置要上传的路径 
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
	 * 设置一个FTP信息 
	 *
	 * @param array $ftp
	 */
	public function setFtp($ftp)
	{
		$this->ftp = $ftp;
	}

	/*
	 * 是否启用调试模式 
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