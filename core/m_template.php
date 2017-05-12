 
<?php
require_once "m_core.php";
/**
 * 模板引擎，只为学习使用，如需应用到项目中，请多做测试
 * 作者：莫书江
 * 日期：2017-04-28
 * 版本：v1.0.0
 */
class m_template{
    protected $_cache_dir = "";
    protected $_filter = ".html";
    protected $_template_dir = "";
    protected $_debug = true;
    protected $_is_cache = false;
    protected $_plugins = array();
    function __construct(){
       $this->set_cache_dir(ROOT_PATH."/data/cache/template");
       $this->set_template_dir(ROOT_PATH."/public/views");
       $this->set_filter(".html");
    }
    public function config_plugin($arr){
        $this->_plugins = $this->_plugins && is_array($this->_plugins) ? $this->_plugins : array();
        if(is_array($arr)){
            $this->_plugins = array_merge($this->_plugins,$arr);
        }else{
            throw new Exception("配置错误的参数!");
        }
    }
    /**
     * 清除缓存
     *
     * @return void
     */
    public function clearCache(){
        kill_files($this->_cache_dir);
    }
    public function set_debug($value){
        $this->_debug=$value;
    }
    /**
     *设置缓存目录
     *
     * @param string 目录
     * @return void
     */
    public function set_cache_dir($path){
        $path = preg_match("/\/$/",$path) ? $path : $path."/";
        $this->_cache_dir = $path;
    }
    /**
     *设置模板目录
     *
     * @param string 设置模板所在路径
     * @return void
     */
    public function set_template_dir($path){
        $path = preg_match("/\/$/",$path) ? $path : $path."/";
        $this->_template_dir = $path;
    }
    /**
     * 设置模板文件后缀
     *
     * @param [type] $filter
     * @return void
     */
    public function set_filter($filter){
        $filter = preg_match("/^\./",$filter) ? $filter : ".".$filter;
        $this->_filter = $filter;
    }
    /**
     * 注册模板变量
     *
     * @param string 变量名称
     * @param string|array 变量数据
     * @return void
     */
    public function assign($key,$value){
        global $m_template_var;
        $m_template_var = !empty($m_template_var) && is_array($m_template_var) ? $m_template_var : array();
        $m_template_var[$key] = $value;
    }
    public function display($filename){
        //------拼接出完整的模板文件地址
        $t_path = preg_match("/\/$/",$this->_template_dir) ? $this->_template_dir : $this->_template_dir."/";
        $filter = preg_match("/^\./",$this->_filter) ? $this->_filter : ".".$this->_filter;
        $reg_str = "/($filter)$/";
        $filename = preg_match($reg_str,$filename) ? $filename : $filename.$filter;
        $filename = $t_path.$filename; 
        //------拼接出完整的模板文件地址
        if(file_exists($filename)){
            try{
                $cache_dir = $this->_cache_dir;
                $filemd5 = md5_file($filename);
                $cache_file = preg_match("/\/$/",$cache_dir) ? $cache_dir : $cache_dir."/";
                $cache_file .= $filemd5.".php";
                header( 'Content-Type:text/html;charset=utf-8 '); 
                $str = "";
                if(!$this->_is_cache || !file_exists($cache_file)){
                    $core = new m_template_core(array(
                        'plugins'=>$this->_plugins,
                    ));
                    $str = $core->get_content($filename,
                                            $this->_cache_dir,
                                            $this->_template_dir,
                                            $this->_filter,
                                            $this->_is_cache);  
                   
                   file_put_contents($cache_file,$str);
                }else{
                   $str = file_get_contents($cache_file);
                } 
                $template = require_once($cache_file);
                echo substr($template,0,strlen($template)-1);
                // echo file_get_contents($cache_file);
            }catch(Exception $ex){
                $this->show_error($ex->getMessage(),0,"解析错误",$ex->getFile()."&nbsp;&nbsp;Line:".$ex->getLine(),$ex->getTraceAsString());
            }
        }else{
            $this->show_error("模板文件【{$filename}】不存在！",1);
        }
      
    }
    
    public function show_error($msg,$level=0,$title="错误",$line=0,$trace=""){
        $err_txt = "";
        if($this->_debug){
            //显示错误详细信息
            $trace = preg_replace("/#/","<br/>&nbsp;&nbsp;&nbsp;&nbsp;",$trace);
            $err_txt = "<p style='color:#333;'><span class='error_ct_tit'>错误信息：</span>".$msg."</p>";
            $err_txt .= "<hr/><p><span class='error_ct_tit'>位置:</span>".$line."行</p>";
            $err_txt .= "<hr/><p><span class='error_ct_tit'>trace:</span>".$trace."</p>";
        }else{
            $err_txt = "<p>模板解析错误</p>";
        }
        $style = $level !=1 ? "waring" : "error";
        echo <<<EOT
        <?DOCTYPE>
        <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
            <meta name="apple-mobile-web-app-capable" content="yes" /><!-- 删除苹果默认的工具栏和菜单栏 -->
            <meta name="apple-mobile-web-app-status-bar-style" content="black" /><!-- 设置苹果工具栏颜色 -->
            <meta name="format-detection" content="telephone=no, email=no" /><!-- 忽略页面中的数字识别为电话，忽略email识别 -->
            <!-- 启用360浏览器的极速模式(webkit) -->
            <meta name="renderer" content="webkit">
            <!-- 避免IE使用兼容模式 -->
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <!-- 针对手持设备优化，主要是针对一些老的不识别viewport的浏览器，比如黑莓 -->
            <meta name="HandheldFriendly" content="true">
            <!-- 微软的老式浏览器 -->
            <meta name="MobileOptimized" content="320">
            <!-- uc强制竖屏 -->
            <meta name="screen-orientation" content="portrait">
            <!-- QQ强制竖屏 -->
            <meta name="x5-orientation" content="portrait">
            <!-- UC强制全屏 -->
            <meta name="full-screen" content="yes">
            <!-- QQ强制全屏 -->
            <meta name="x5-fullscreen" content="true">
            <!-- UC应用模式 -->
            <meta name="browsermode" content="application">
            <!-- QQ应用模式 -->
            <meta name="x5-page-mode" content="app">
            <!-- windows phone 点击无高光 -->
            <meta name="msapplication-tap-highlight" content="no">
            <title>模板错误</title>
            <style>
                .waring .m_error{border:1px solid #b31109;background:#ffffff;}
                .waring .m_error>span{background:#f98496;border-bottom:1px solid #b31109;}
                .error .m_error{border:1px solid #4187e7;background:#ffffff;}
                .error .m_error>span{background:#75b7ff;border-bottom:1px solid #4187e7;}
                .m_error {display:block;min-width:200px;margin:0 auto;text-align:left;}
                .m_error>span{display:block;height:25px;line-height:25px;padding:0 10px;font-size:15px;color:#fff;}
                .m_error>div{display:block;min-height:25px;line-height:20px;font-size:14px;color:#333;padding:5px 10px;}
                .m_error>div p{margin:0;padding:0;color:#999;}
                .error_ct_tit{width:80px;display: inline-block;color:#333;}
                hr{display:block;height:0;border:none;border-bottom:1px dashed #ccc;}
            </style>
        </head>
        <body style="text-align:center;" class="{$style}">
            <div class="m_error">
                <span>{$title}</span>
                <div>{$err_txt}</div>
            </div>
        </body>
        </html>
EOT;
    }
}
 