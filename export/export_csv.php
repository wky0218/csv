<?php

    //error_reporting(E_ERROR | E_WARNING | E_PARSE );
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
    



    //连接数据库
    $db = Mysql::getInstance();
    $db->connect($dbconfig);    

    $total = $db->table('user')->count();

    $pageSize = 10000;//每页条数
    $totalPage = ceil($total/$pageSize);//页数


    /************************************************************************/
    header ( "Content-type:application/vnd.ms-excel" );  
    header ( "Content-Disposition:filename=" . iconv ( "UTF-8", "GB18030", "test" ) . ".csv" );  

    // 打开PHP文件句柄，php://output 表示直接输出到浏览器  
    $fp = fopen('php://output', 'a');   

    //列名
    $column_name = array('id','username','email','phone');
      
    // 将中文标题转换编码，否则乱码  
    foreach ($column_name as $i => $v) {    
		$column_name[$i] = iconv('utf-8', 'GB18030', $v);    
    }  
    // 将标题名称通过fputcsv写到文件句柄    
    fputcsv($fp, $column_name);  
         
    for ($i=1;$i<=$totalPage;$i++){  

        $limit = ($i-1)*$pageSize.','.$pageSize;
        $data = $db->table('user')->limit($limit)->select();
		
        foreach ($data as $item) { 
            $row = array();  			
			$row[] = iconv('utf-8', 'GB18030', $item['id']); 
			$row[] = iconv('utf-8', 'GB18030', $item['username']); 
			$row[] = iconv('utf-8', 'GB18030', $item['email']); 
			$row[] = iconv('utf-8', 'GB18030', $item['phone']); 
			  
           fputcsv($fp, $row);  
        }  
          
        // 将已经写到csv中的数据存储变量销毁，释放内存占用  
        unset($row);  
        ob_flush();  
        flush();  
    }  






?>


