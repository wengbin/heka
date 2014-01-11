<?php
   namespace MyFile;
   class fileUp{
  
	   protected $img_path = "imgs/";//图片上传路径
	   protected $song_path = "songs/";//音乐上传路径
       protected $img_type = array('jpg','png','jpeg','gif');//允许上传的图片格式
	   protected $song_type = array('mp3','midi','wav');//允许上传的图片格式
       protected $img_size = 52323232;//允许上传的图片大小
	   protected $song_size = 555555555;//允许上传的音乐大小
	   
	   public $tablename='pre_wxzs_heka';//表名
	   public $songUrl = null;//图片地址
	   public $imgUrl = null;//歌曲地址
   
   //图片合法性检查
   protected function checkImg($file){
		if($file['error']!=0){
			return array(
			   'error' => 1,
			   'msg' => '上传出错，请重试'
		   );   
		}
		
		$file_type = strtolower(substr(strrchr($file['name'], '.'), 1));
		
		//print_r($_FILES);die;
		if(!in_array($file_type,$this->img_type)){
		   return array(
			   'error' => 1,
			   'msg' => '您上传的图片'.$file['name'].'格式不合法'
		   );
		}			
		if($file['size']>$this->img_size){
			return array(
			   'error' => 1,
			   'msg' => '您上传的图片'.$file['name'].'大小超过限制'
		   );
		}
	return array('error'=> 0);               
   }
   
   //歌曲合法性检查
   protected function checkSong($file){
		if($file['error']!=0){
			return array(
			   'error' => 1,
			   'msg' => '上传出错，请重试'
		   );   
		}
		
		$file_type = strtolower(substr(strrchr($file['name'], '.'), 1));
		
		//print_r($_FILES);die;
		if(!in_array($file_type,$this->song_type)){
		   return array(
			   'error' => 1,
			   'msg' => '您上传的歌曲'.$file['name'].'格式不合法'
		   );
		}			
		if(!$file['size']>$this->song_size){
			return array(
			   'error' => 1,
			   'msg' => '您上传的歌曲'.$file['name'].'大小超过限制'
		   );
		}
	return array('error'=> 0);               
   }
   
   
   public function upLoad($file,$file_type){
        if($file_type=='img'){
		    $res=$this->checkImg($file);
		}else{
		    $res=$this->checkSong($file);
		}
		if($res['error']==1){
			   exit($res['msg']);
		}
		
        $path=($file_type=='img'?$this->img_path:$this->song_path);
        $dir = $path . date("Ymd"); //存放目录
		if(!is_dir($dir)){
		    mkdir($dir);
		}
		$fileUrl='';

		$tmp_name = $file['tmp_name'];
		//解决中文文件名可能上传失败
		$file_suffix = strtolower(substr(strrchr($file['name'], '.'), 1));
	    $filename = substr(md5($file['name']),0,10).'.'.$file_suffix;
		
		while(file_exists($dir.'/'.$filename)){
			  $filename = time().rand(1,10000).$filename;
		}
		if(!move_uploaded_file($tmp_name,$dir.'/'.$filename)){
			  exit('上传失败，请重试');
		}else{
              if($file_type=='img'){		
			      $this->imgUrl.=$dir.'/'.$filename;
			  }else{
			      $this->songUrl.=$dir.'/'.$filename;
			  }
		}
   
   }
  }
?>