 
<?php

define("ROOT_PATH",__DIR__);

require_once __DIR__."/core/m_app.php";
global $m_template;

$act = isset($_GET['a']) ? $_GET['a'] : "";
if($act == 'clear_cache'){
    $m_template->clearCache();
   
}
$test_array = array(
    'tst1'=>array('name1','data'=>array('list1','olist1','item1')),
    'tst3'=>array('name2','data'=>array('list2','olist2','item2')),
    'tst4'=>array('name3','data'=>array('list3','olist3','item3'))
);

$m_template->assign('data',$test_array );
$m_template->assign('title',"首页");
$m_template->assign('test_var','测试');
$m_template->assign('is_close',1);
$m_template->display('index');