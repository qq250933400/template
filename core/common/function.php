<?php

function debug($obj){
    echo "<pre>";
    print_r($obj);
    echo "</pre>";
}
/**
 *自动检查路径不存在则创建
 *
 * @param string 检查目录
 * @return void
 */
function check_dir($path){
    if(empty($path))return ;
    $path = str_replace("\\","/",$path);
    $is_win = preg_match("/(:\/)/",$path) ? true : false;
    $arr = explode("/",$path);
    $tmp_path = "";
    foreach($arr as $key=>$str){
        $tmp_path .= !empty($tmp_path) ? "/".$str : $str;
        try {
            if (!empty($tmp_path)) {
                if($is_win){
                    if($key>0){
                        if(!is_dir($tmp_path))mkdir($tmp_path,0777,0777);
                    }
                }else{
                    $tmp_path = !preg_match('/^\//',$tmp_path) ? "/".$tmp_path : $tmp_path;
                    if(!is_dir($tmp_path))mkdir($tmp_path,0777,0777);
                }
            }
        }catch(Exception $e) {
            debug($e->getMessage());
            debug($tmp_path); 
        }
    }
}
/**
 * 删除文件
 *
 * @param string 指定路径
 * @param boolean 是否删除文件夹
 * @return void
 */
function kill_files($path,$kill_path=false){
    if(is_dir($path)){
        $handler = opendir($path);
        if($handler){
            while(($file = readdir($handler))!=false){
                $filename = $path."/".$file; 
                if($file != "." && $file != ".."){
                    if(is_dir($filename)){
                        kill_files($filename);
                        if($kill_path){
                            rmdir($filename);
                        }
                    }else{
                        $filename = preg_replace("/\/\//","/",$filename);
                        if(!unlink($filename)){
                            echo "文件【{$filename}】删除失败<br/>";
                        }
                    } 
                }
            }
            closedir($handler);
        }
    }
}
function get_rand_str($len=5,$include_Upper=true,$include_number=true){
	$base_str = "abcdefghijklmnopqrstuvwxyz";
	$base_str .= $include_Upper ? strtoupper($base_str) : "";
	$base_str .= $include_number ? "0123456789" : "";
	$len = $len>=5 ? $len : 5;
	$result = "";
	$str_len = strlen($base_str);
	for($i=0;$i<$len;$i++){
		$t_index = rand(0,$str_len);
		$result .= substr($base_str,$t_index,1);
	}
	return $result;
}