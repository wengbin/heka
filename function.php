<?php
function check_input($value)
{
	if( is_array($value) )
	{
		$result = array();
		foreach ($value as $in) {
			$result[] = check_input($in);
		}
		return $result;
	}
	else {
		// 去除斜杠
		if (get_magic_quotes_gpc())
		{
			$value = stripslashes($value);
		}

		$value = addslashes($value);
		return $value;
	}
}
//封装新方法
function arg_sql_insert($mysqli,$data,$tablename){
	$key=implode(",",array_keys($data));
	$value="'".implode("','",array_values($data))."'";
	$sql="insert into $tablename (".$key.")values(".$value.")";
	if($mysqli->query($sql)){
		return  mysqli_insert_id($mysqli);
	}else{
		echo  mysqli_error($mysqli);
		return false;
	}
}

//更新操作
function arg_sql_update($mysqli,$data,$tablename,$where){
	$sql="update ".$tablename." set ";
	foreach($data as $key=>$val){
		$sql.=$key."='".$val."',";
	}
	$sql.=" ".$where;
	$sql=substr_replace($sql,' ',strrpos($sql,','),1);
	if(mysqli_query($mysqli,$sql)){
		return mysqli_affected_rows($mysqli);
	}else{
		echo mysqli_error($mysqli);
		return false;
	}

}


function arg_check_input($value)
{
	if( is_array($value) )
	{
		$result = array();
		foreach ($value as $k=>$in) {
			$result[$k] = check_input($in);
		}
		return $result;
	}
	else {
		// 去除斜杠
		if (get_magic_quotes_gpc())
		{
			$value = stripslashes($value);
		}

		$value = addslashes($value);
		return $value;
	}
}

function getPost($name,$default='')
{
	if( isset($_POST[$name]) )
	{
		if( !empty($_POST[$name]) )
		{
			return check_input($_POST[$name]);
		}
	}
	return $default;
}

function getGet($name,$default='')
{
	if( isset($_GET[$name]) )
	{
		if( !empty($_GET[$name]) )
			return check_input($_GET[$name]);
	}
	return $default;
}

function get_args($name)
{
	if(isset($_GET[$name])&&!empty($_GET[$name]))return arg_check_input($_GET[$name]);
	if(isset($_POST[$name])&&!empty($_GET[$name]))return arg_check_input($_POST[$name]);
	return null;
}

// 初始化数据库,实例：
// $mysqli = initDB();
// 关闭数据库
// $mysqli->close();
function initDB()
{
/* 	  $dbname = 'NzQGsodAixmxEleNuYHV';
 
/*从环境变量里取出数据库连接需要的参数*/
//$host = getenv('HTTP_BAE_ENV_ADDR_SQL_IP');
//$port = getenv('HTTP_BAE_ENV_ADDR_SQL_PORT');
//$user = getenv('HTTP_BAE_ENV_AK');
//$pwd = getenv('HTTP_BAE_ENV_SK'); 
  
  
	/* $dbserver = 'rdsz2inyifbbi2m.mysql.rds.aliyuncs.com';
	if( getenv("DEVELBOX")==1 )
		$dbserver = '58.63.254.16'; */
	$mysqli = new mysqli('localhost','root',111,'lianxi');
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	$mysqli->set_charset('utf8');
	return $mysqli;
}

//获取所有数据，返回数组
function getAllFiled($mysqli,$sql){
	$rsArray = array();
	$rs = $mysqli->query($sql);
	if( $rs!==false && $rs->num_rows > 0 ){
		while ($row = $rs->fetch_assoc()){
			$rsArray[] = $row;
		}
		$rs->free();
	}
	return $rsArray;
}

// 获取一行数据
function getRowFiled($mysqli,$sql,$getassoc=false)
{
	$rs = $mysqli->query($sql);
	if( $rs!==false && $rs->num_rows > 0 )
	{
		if( $getassoc )
			$row = $rs->fetch_assoc();
		else
			$row = $rs->fetch_array();
		$rs->free();
		return $row;
	}
	return '';
}

// 获取指定的字段值
function getFiled($mysqli,$sql)
{
	$row = getRowFiled($mysqli,$sql);
	if($row != ''){
		return $row[0];
	}
	return '';
}

// 获取随机数
function g_suijishu($length) {
	$returnStr='';
	$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
	for($i = 0; $i < $length; $i ++) {
		$returnStr .= $pattern {mt_rand ( 0, 61 )}; //生成php随机数
	}
	return $returnStr;
}


// 网页抓取操作类
class fun_cURL {
	public
	$proxy,	// 设置本地代理
	$webproxy,	// 设置web代理
	$sck;// 设置web代理密钥

	var $headers;
	var $user_agent;
	var $compression;
	var $cookie_file;
	var $cookiefilename;
	function fun_cURL($cookies = true, $cookie = '', $compression = 'gzip') {
		$this->headers [] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
		$this->headers [] = 'Connection: Keep-Alive';
		$this->headers [] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
		$this->user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
		$this->compression = $compression;
		$this->cookies = $cookies;
		if ($this->cookies == TRUE)
			$this->setCookieFile ( $cookie );
	}

	// 检验web代理服务器是否工作正常,返回false则代表服务器异常，返回true代表服务器是正常的
	function checkWebProxy()
	{
		$check_url  = "http://www.ygj.com.cn/pp/verify.php?verify_ygj=1";
		$content = $this->get($check_url);
		if( $content=="yunguanjia" )
			return true;
		return false;
	}

	function setCookieFile($cookie_file='') {
		if( empty($cookie_file) )
			$cookie_file = getContextCookie();
		$this->cookies = true;
		$cookie_file = dirname(__FILE__).DIRECTORY_SEPARATOR.$cookie_file;
		if (file_exists ( $cookie_file )) {
			$this->cookie_file = $cookie_file;
		} else {
			$handler = fopen ( $cookie_file, 'w' ) or $this->error ( 'The cookie file could not be opened. Make sure this directory has the correct permissions' );
			$this->cookie_file = $cookie_file;
			fclose (  $handler );
		}
	}

	function get($url,$getFinalUrl=false,$follow = true,$withHeader=false,$refer=false) {
		if( $this->webproxy )
		{
			$url = $this->webproxy."?url=".urlencode($url)."&sck=".$this->sck;
		}
		$process = curl_init ( $url );
		curl_setopt ( $process, CURLOPT_HTTPHEADER, $this->headers );
		curl_setopt ( $process, CURLOPT_HEADER, $withHeader );
		curl_setopt ( $process, CURLOPT_USERAGENT, $this->user_agent );
		if ($this->cookies == TRUE)
			curl_setopt ( $process, CURLOPT_COOKIEFILE, $this->cookie_file );
		if ($this->cookies == TRUE)
			curl_setopt ( $process, CURLOPT_COOKIEJAR, $this->cookie_file );
		curl_setopt ( $process, CURLOPT_ENCODING, $this->compression );
		curl_setopt ( $process, CURLOPT_TIMEOUT, 3 );
		if ($this->proxy)
			curl_setopt ( $process, CURLOPT_PROXY, $this->proxy );
		curl_setopt ( $process, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $process, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $process, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $process, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt( $process, CURLOPT_FOLLOWLOCATION, $follow);
		if( $refer )
			curl_setopt($process, CURLOPT_REFERER, $refer);
		$return = curl_exec ( $process );
		if( $getFinalUrl )
		{
			$info = curl_getinfo($process,CURLINFO_EFFECTIVE_URL);
			curl_close ( $process );
			return $info;
		}
		curl_close ( $process );
		return $return;
	}
	function post($url, $data,$refer=false,$getFinalUrl=false) {
		if( $this->webproxy )
		{
			$url = $this->webproxy."?url=".urlencode($url)."&sck=".$this->sck;
		}
		$process = curl_init ( $url );
		curl_setopt ( $process, CURLOPT_HTTPHEADER, $this->headers );
		// curl_setopt ( $process, CURLOPT_HEADER, 1 );
		curl_setopt ( $process, CURLOPT_USERAGENT, $this->user_agent );
		if ($this->cookies == TRUE)
			curl_setopt ( $process, CURLOPT_COOKIEFILE, $this->cookie_file );
		if ($this->cookies == TRUE)
			curl_setopt ( $process, CURLOPT_COOKIEJAR, $this->cookie_file );
		curl_setopt ( $process, CURLOPT_ENCODING, $this->compression );
		curl_setopt ( $process, CURLOPT_TIMEOUT, 3 );
		if ($this->proxy)
			curl_setopt ( $process, CURLOPT_PROXY, $this->proxy );
		curl_setopt ( $process, CURLOPT_POSTFIELDS, $data );
		curl_setopt ( $process, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $process, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $process, CURLOPT_POST, 1 );
		if( $refer )
			curl_setopt($process, CURLOPT_REFERER, $refer);
		curl_setopt ( $process, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $process, CURLOPT_SSL_VERIFYHOST, 0);
		$return = curl_exec ( $process );
		if( $getFinalUrl )
		{
			$info = curl_getinfo($process,CURLINFO_EFFECTIVE_URL);
			curl_close ( $process );
			return $info;
		}
		curl_close ( $process );
		return $return;
	}
	function error($error) {
		echo "<center><div style='width:500px;border: 3px solid #FFEEFF; padding: 3px; background-color: #FFDDFF;font-family: verdana; font-size: 10px'><b>cURL Error</b><br>$error</div></center>";
		die ();
	}
}

// 发送短信
// $mobile 手机号码
// $msg 消息内容
function sendsms($mobile,$msg)
{
	return sendsmsBy10086($mobile, $msg."【微商铺】");
}

function sendsmsBy10086($mobile,$msg)
{
	// 判断手机号码个数
	$fenlei = 20; // K通道
	$mobiles = explode(",",$mobile);
	if( count($mobiles)>1 )
	{
		$fenlei = 11; // E通道
	}
// 	$fenlei = 11;
	$c = new fun_cURL(false);
	// 使用代理发送信息
	$c->sck = 'cGNBEROB';
	// $c->webproxy = "http://haha168.duapp.com/ygj.php";
	// $msg = urlencode(mb_convert_encoding($msg,'gb2312','utf-8'));
	$msg = urlencode($msg);
	$url = "http://10086.postyp.com.cn/Service.asmx/sendsms?zh=weishangpu&mm=Cab5LbzFKe&hm=$mobile&nr=$msg&dxlbid=$fenlei";
	$result = $c->get($url);
	return true;
}

?>