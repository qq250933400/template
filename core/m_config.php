<?php
//定义初始化变量
$d_info = <<<EOT
<!--江涛模板引擎-->
<!--模板引擎，只为学习使用，如需应用到项目中，请多做测试-->
<!--作者：莫书江-->
<!--日期：2017-04-28-->
<!--版本：v1.0.0-->
EOT;
define("TEMPLATE_DIR",ROOT_PATH."/public/views/");
define("TEMPLATE_CACHE",ROOT_PATH."/data/cache/template");
define("TEMPLATE_FILTER",".html");
define("TEMPLATE_INFO",$d_info);

global $m_template_var;//定义模板变量存储