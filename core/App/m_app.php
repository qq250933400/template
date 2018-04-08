<?php
namespace mTemplate\App;
use mTemplate\App\m_template;

$m_template_info = <<<EOT
<!--江涛模板引擎-->
<!--模板引擎，只为学习使用，如需应用到项目中，请多做测试-->
<!--作者：莫书江-->
<!--日期：2017-04-28-->
<!--版本：v1.0.0-->
EOT;
class m_app {
    private $template = null;
    function __construct(){
        $this->defineConfig();
        $this->template = new m_template();
    }
    public function defineConfig(){
        $templatePath = TEMPLATEPATH;
        if(!defined("TEMPLATE_INFO")){
            define('TEMPLATE_INFO', $m_template_info);
        }
        if(!defined('TEMPLATE_DIR')){
            define('TEMPLATE_DIR', $templatePath."/views");
        }
        if(!defined('TEMPLATE_CACHE')){
            define("TEMPLATE_CACHE",$templatePath."/data/cache");
        }
        if(!defined('TEMPLATE_FILTER')){
            define("TEMPLATE_FILTER",".html");
        }
    }
    public function debugOn(){
                //禁用错误报告
        error_reporting(0);
        //报告运行时错误
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        //报告所有错误
        error_reporting(E_ALL);
    }
    public function init(){
        $this->template->set_cache_dir(TEMPLATE_CACHE);
        $this->template->set_template_dir(TEMPLATE_DIR);
        $this->template->set_filter(TEMPLATE_FILTER);
        $this->template->config_plugin(array(
            array(
                'class_name'=>"mTemplate\App\m_tag",'filename'=>__DIR__.'/m_tag.php'
            ),
        ));
    }
    public function assign($key,$value){
        $this->template->assign($key,$value);
    }
    public function display($fileName){
        $this->template->display($fileName);
    }
    public function clearCache(){
        $this->template->clearCache();
    }
}
