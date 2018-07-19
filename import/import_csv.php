<?php

header("Content-type: text/html; charset=utf-8");
date_default_timezone_set("PRC"); 

include_once("../Upload.class.php");
include_once("../Mysql.class.php");

/*数据库配置*/
$dbconfig = array(			
	'type'					=> 'mysql', //数据库类型
	'host'					=> 'localhost', //数据库连接地址
	'user'					=> 'root', //数据库用户名
	'password'				=> '', //数据库密码
	'dbname'				=> 'test', //数据库名称
	'prefix'				=> 'jdp_', //数据库表前缀
	'charset'    			=> 'utf8',
);

function input_csv($handle) { 
    $out = array ();   
    while ($data = fgetcsv($handle, 100000)) {        
        foreach($data as $k=>$v){
        	$newdata[$k] = iconv('gb2312','utf-8',$v);
        }
		$out[] = $newdata;             
    } 
    return $out; 
} 


if(@$_GET["act"]=="AddSave") {
	
	$config['maxSize'] = 1024 * 1024 * 4; //4M 设置附件上传大小
	$config['allowExts'] = array("csv"); // 设置附件上传类型      
	$config['savePath'] = ''; // 保存路径
	$config['subName'] = ''; // 子目录
    // $config['saveName'] ='';
	$config['replace'] = true;
	$upload = new Upload($config); // 实例化上传类
	$info = $upload->upload();	
	
	if (!$info) {
		$errormsg = $upload->getError();
		$arr = array(
			'error' => $errormsg, //返回错误
		);
		echo "<script>alert('".$arr['error']."');history.go(-1);</script>";
		exit;
	} else {
		//上传成功 获取上传文件信息			
		$filename = $info['pexcel']['savename'];

	}
	
	$file_excel = 'Uploads/'.$filename;
    $handle = fopen($file_excel, 'r'); 
    $result = input_csv($handle); //解析csv 
    
	//转换成关联数组
	$rows = array();	
    foreach($result as $k=>$v){
    	$row['username'] = $v[0];
		$row['email'] = $v[1];
		$row['phone'] = $v[2];
 		$rows[] = $row;
    }	
	
    //连接数据库
    $db = Mysql::getInstance();
    $db->connect($dbconfig);
	//写入数据库
	$r = $db->table('user')->insertAll($rows,100);	
   
   if($r){
        $html = "<html>
                  <head>
                    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>
                    <title></title>
                  </head>
                  <body>
                    <div style='width: 100%; height: 200px;margin:0 auto; text-align:center; border: 1px solid red; font-size: 12px;'>
                        <div>已成功导入".$r."条记录,<a href='javascript:history.go(-1)'>返回继续导入</a></div>
                      
                    </div>
                  </body>
               </html>
               ";
        echo $html;
        exit();

   }else{
   	echo "<script>alert('导入失败');history.go(-1);</script>";
   }
	
}
	
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>导入</title>
    <meta name="viewport" content="width=device-width, user-scalable=0, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <style>
		html,body{width: 100%; height: 100%;margin: 0;padding: 0;}
		.form{width: 480px;height: 300px;margin:0 auto;border: solid 1px #ddd;}
		.form div{padding: 10px;}
    </style>
</head>
<body>

	<div class="form">
		<form action="?act=AddSave" method="post" name="form1" id="form1" enctype="multipart/form-data" >		
			<div>
				csv文件：<input type="file" name='pexcel'>
			</div>
			<div>
				<input type="submit" id="sub" value="导入">
			</div>	
			<div id="loading"></div>				
		</form>
	</div>
	
	<script>
	
		document.getElementById("sub").onclick=function(){
			document.getElementById("loading").innerHTML='正在导入，请稍候...';
			
		};

	</script>


</body>
</html>


