 
<?php

define("ROOT_PATH",__DIR__);
require __DIR__."/core/m_config.php";

global $m_template;

$act = isset($_GET['a']) ? $_GET['a'] : "";
if($act == 'clear_cache'){
    $templateApp->clearCache();
   
}
$test_array = array(
    'tst1'=>array('name1','data'=>array('list1','olist1','item1')),
    'tst3'=>array('name2','data'=>array('list2','olist2','item2')),
    'tst4'=>array('name3','data'=>array('list3','olist3','item3'))
);
error_reporting(0);
//报告运行时错误
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//报告所有错误
error_reporting(E_ALL);
$templateApp->assign('data',$test_array );
$templateApp->assign('title',"首页");
$templateApp->assign('test_var','测试');
$templateApp->assign('is_close',1);
$templateApp->display('index');