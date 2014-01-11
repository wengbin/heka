<?php
   namespace MyFile;
   use MyFile2\CreatMiniature as CreatMiniature;
   require 'function.php';
   $mysqli = initDB();
   if(getGet('mod'))
   {
      $mod = getGet('mod');
   }
   else if(getPost('mod'))
   {
      $mod = getPost('mod');
   }
   else
   {
      $mod='list';
   }
   session_start();
   $tablename='pre_wxzs_heka';

   
   switch($mod){
       case 'list':    
		   // 生成唯一的ID，并使用MD5来加密 
		   $post_id = md5(uniqid(rand(), true)); 
		   // 创建Session变量 
		   $_SESSION["post_id"] = $post_id;
		   $sql="select id,title,songUrl,suolueUrl,updatetime from ".$tablename;
		   $heka_list=getAllFiled($mysqli,$sql);
		   require 'heka_list.html';
	       break;
       
	   case 'add': 
		   $post_id = md5(uniqid(rand(), true)); 
		   $_SESSION["post_id"] = $post_id;
		   require 'add_heka.html';
		   break;
		   
		   case 'add_action':
		   if(getPost('post_id')!=$_SESSION['post_id'])
		   {
			  exit('非法操作');
		   } 
		   unset($_SESSION['post_id']);
		   
		   require 'fileUp.php';
		   require 'resizeImg.php';
		   $file=new fileUp();
		   $cm=new CreatMiniature();
		   if(empty($_FILES['img']['name']))
		   {
		      exit('背景图片不能为空');
		   }
		   else
		   {
		      $file->upLoad($_FILES['img'],'img');
              $cm->SetVar($file->imgUrl,"file");
			  $basename=basename($file->imgUrl);
			  $dirname=dirname($file->imgUrl);
			  $suolueUrl=$dirname.'/suolue_'.$basename;
              $cm->BackFill($suolueUrl,200,300);
		   }
		   if(!empty($_FILES['song']['name']))
		   {
		      $file->upLoad($_FILES['song'],'song');
		   }
		   
		   $imgUrl=$file->imgUrl;
		   $songUrl=$file->songUrl;
		   $title=getPost('tit');
		   $updatetime=date('Y-m-d h:i:s');
		   $data = array(
				//'uid' => $uid,
				'title' =>$title,
				'imgUrl'=> $imgUrl,
				 'suolueUrl' => $suolueUrl,
				'songUrl'=> $songUrl,
				'updatetime' => $updatetime
			);
			
			if(arg_sql_insert($mysqli,$data,$file->tablename))
			{	
				echo 'ok';//输出失败信息
			}
			else
			{
				echo 'fail';//输出失败信息
			} 
			
		   break;
		   
	   case 'del':
	       if(getPost('post_id')!=$_SESSION['post_id']){
			  exit('非法操作');
		   }
		   unset($_SESSION['post_id']);
		   
		   $id=getPost('id');
		   $sql="select imgUrl,suolueUrl,songUrl from ".$tablename." where id='$id'";
		   $url=getRowFiled($mysqli,$sql,true);
		   $sql="delete from ".$tablename." where id='$id'";
		   $res=mysqli_query($mysqli,$sql);
		   if($res){
			   if(file_exists($url['imgUrl']))
			   {
			       unlink($url['imgUrl']);
			   }
			   if(file_exists($url['suolueUrl']))
			   {
				   unlink($url['suolueUrl']);
			   }
			   
			   $img_dir=dirname($url['imgUrl']);
			   $dir_empty=1;
			   $dir=opendir($img_dir);
			   while($file=readdir($dir)){
				   if($file!='.'&&$file!='..'){
					  $dir_empty=0;
					  break;
				   }	      
			   }
			   if($dir_empty){
			       $dir_empty=1;
			       rmdir($img_dir);
			   }
			   if($url['songUrl'])
			   {
			       if(file_exists($url['songUrl']))
				   {
			            unlink($url['songUrl']);
			       }
				   $song_dir=dirname($url['songUrl']);
				   $dir=opendir($song_dir);
				   while($file=readdir($dir)){
				       if($file!='.'&&$file!='..'){
					       $dir_empty=0;
					       break;
					   }
				   }
				   if($dir_empty){
			       rmdir($song_dir);
			   }
				   

			   }
			   
		       echo  json_encode(array('error'=>0,'msg'=>'删除成功'));
		   }else{
		       echo  json_encode(array('error'=>1,'msg'=>'删除失败'));
		   }
		   break;
		   
	   case 'edit':
	       $post_id = md5(uniqid(rand(), true)); 
		   $_SESSION["post_id"] = $post_id;
	       $id=getGet('id');
		   $sql="select id,title,suolueUrl,songUrl from ".$tablename." where id='$id'";
		   $heka_data=getRowFiled($mysqli,$sql);
		   require 'edit_heka.html';
		   break;
	   case 'editAction':
	       if(getPost('post_id')!=$_SESSION['post_id'])
		   {
			  exit('非法操作');
		   } 
		   unset($_SESSION['post_id']);
		   require 'fileUp.php';
		   require 'resizeImg.php';
		   $id=getPost('id');
		   $title=getPost('tit');
		   
		   $file=new fileUp();
		   $cm=new CreatMiniature();
		   if(!empty($_FILES['img']['name']))
		   {
		       $file->upLoad($_FILES['img'],'img');
			   $cm->SetVar($file->imgUrl,"file");
			   $basename=basename($file->imgUrl);
			   $dirname=dirname($file->imgUrl);
			   $suolueUrl=$dirname.'/suolue_'.$basename;
               $cm->BackFill($suolueUrl,200,200);
		   }
		   if(!empty($_FILES['song']['name']))
		   {
		       $file->upLoad($_FILES['song'],'song');
		   }
		   $imgUrl=$file->imgUrl;
		   $songUrl=$file->songUrl;
		   
		   $sql="select imgUrl,songUrl,suolueUrl from ".$tablename." where id='$id'";
		   $url=getRowFiled($mysqli,$sql,true);
		   
		   $sql="update ".$tablename." set title='$title'";
		   if(!empty($imgUrl))
		   {
	          $sql.=",imgUrl='$imgUrl',suolueUrl='$suolueUrl'";
		   }
		   if(!empty($songUrl))
		   {
		      $sql.=",songUrl='$songUrl'";
		   }
		   $sql.=" where id='$id'";
		   $res=mysqli_query($mysqli,$sql);
           if($res){
		       if(!empty($imgUrl))
			   {
				   if(file_exists($url['imgUrl']))
				   {
					   unlink($url['imgUrl']);
				   }
				   if(file_exists($url['suolueUrl'])){
				       unlink($url['suolueUrl']);
				   }
			   }
			   if(!empty($songUrl))
			   {
				   if($url['songUrl'])
				   {
					   if(file_exists($url['songUrl'])){
							unlink($url['songUrl']);
					   }
				   }
			   }
               header("Location:vip.php?mod=list");		   
		   }else{
		       echo  "fail";
		   }
		   
		   break;
		      
   }
?>