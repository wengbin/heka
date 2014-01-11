<?php
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
      $mod='index';
   }
   $tablename='pre_wxzs_heka';
   $tablename1='pre_wxzs_heka_send_info';
   
   switch($mod){
	   case 'index':
	   $sql="select id,title,suolueUrl from $tablename";
	   $hekas_data=getAllFiled($mysqli,$sql);
	   require 'heka_index.html';
	   break;
	   
	   case 'preview':
	   $hid=getGet('id');
	   $sql="select id,title,imgUrl,songUrl from $tablename where id='$hid'";
	   $heka_data=getRowFiled($mysqli,$sql);
	   $send_info['reciever']="萌妹子";
	   $send_info['poster']="小正太";
	   $send_info['content']="一元复始，万象更新。在这里，我向全国各族人民，向香港特别行政区同胞和澳门特别行政区同胞，向台湾同胞和海外侨胞，向世界各国和各地区的朋友们，致以新年的祝福！";
	   if(empty($heka_data)){
			   exit('没有此贺卡信息');
		}

	   require 'preview.html';
	   break;
	   
	   
	   case 'xiangQing':
	   session_start();
	   $act=isset($_SESSION['status'])?$_SESSION['status']:null;
     //unset($_SESSION['status']);
	   if(isset($_GET['gid'])){
	       $gid=getGet('gid');
		   $sql="select h_id,reciever,poster,content from $tablename1 where get_id='$gid'";
		   $send_info=getRowFiled($mysqli,$sql);
		   if(empty($send_info)){
			   exit('没有此贺卡信息');
			}
		   $sql="select id,title,imgUrl,songUrl,suolueUrl from $tablename where id=".$send_info['h_id'];
	       $heka_data=getRowFiled($mysqli,$sql);
		  
	   }else{
	        $hid=getGet('id');
	        $sql="select id,title,imgUrl,songUrl,suolueUrl from $tablename where id='$hid'";
	        $heka_data=getRowFiled($mysqli,$sql);
			$send_info['reciever']="萌妹子";
			$send_info['poster']="小正太";
			$send_info['content']="一元复始，万象更新。在这里，我向全国各族人民，向香港特别行政区同胞和澳门特别行政区同胞，向台湾同胞和海外侨胞，向世界各国和各地区的朋友们，致以新年的祝福！";
			
	    }
	   if(empty($heka_data)){
			   exit('没有此贺卡信息');
		}
        //输出过滤
        foreach($send_info as $key=>$v){
		      $send_info[$key]=htmlspecialchars($v,ENT_QUOTES);
		}	
	   //print_r($send_info);
       //背景大小
/* 	   $bg_file=getimagesize($heka_data['imgUrl']);
	   $width=$bg_file['0'];
	   $height=$bg_file['1']; */
	   require 'xiangQing.html';
       if(isset($_SESSION['status'])){
          unset($_SESSION['status']);
       }
	   break;
	   
	   case 'send':
	   $hid=getGet('hid');
	   if(isset($_GET['gid'])){
	      $gid=getGet('gid');
		  $sql="select h_id,reciever,poster,content from $tablename1 where get_id='$gid'";
		  $send_info=getRowFiled($mysqli,$sql);
		  //输出过滤
          foreach($send_info as $key=>$v){
		       $send_info[$key]=htmlspecialchars($v,ENT_QUOTES);
		  }		   
	   }else{
		   $send_info['reciever']="萌妹子";
		   $send_info['poster']="小正太";
		   $send_info['content']="一元复始，万象更新。在这里，我向全国各族人民，向香港特别行政区同胞和澳门特别行政区同胞，向台湾同胞和海外侨胞，向世界各国和各地区的朋友们，致以新年的祝福！";
	   }
       $sql="select title from $tablename where id=".$hid;
	   $heka_data=getRowFiled($mysqli,$sql);
	   require 'send.html';
	   break;
	   
	   case 'save':
	   $to=getPost('to');
	   $from=getPost('from');
	   $content=getPost('content');
	   $hid=getPost('hid');
	   $hid=intval($hid);
       $get_id=getPost('gid')?getPost('gid'):null;
	   $nochange=getPost('nochange');
	   if(!$nochange){
		   $updatetime=date('Y-m-d h:i:s',time());
		   $get_id=substr(md5(md5($to.$from.$content.$updatetime)),0,10);
		   $sql="INSERT INTO `pre_wxzs_heka_send_info` (`id`, `h_id`, `reciever`, `content`,`get_id`, `poster`, `updatetime`) VALUES (NULL, '$hid', '$to', '$content','$get_id', '$from','$updatetime')";
		   $res=mysqli_query($mysqli,$sql);
	   }else{
	       $res=1;
	   }
	   if($res){
	       session_start();
	       $_SESSION['status']='send';
         if($get_id){
	       echo json_encode(array('error'=>0,'gid'=>$get_id));
         }else{
           echo json_encode(array('error'=>0,'hid'=>$hid));
         }  
	   }else{
	       echo json_encode(array('error'=>1,'msg'=>'保存失败，请重试'));
	   }
	   break;
   }
?>