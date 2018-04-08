<?php

namespace mTemplate\App;
use mTemplate\App\m_Exception;
/**
 * 模板引擎内容解析
 */
class m_core{
    protected $template_dir = "";
    protected $template_filter = "";
    protected $cache_dir="";
    protected $is_cache=false;
    protected $_plugins = array();
    protected $_plugins_obj = array();
    
    function __construct($config=array()){
        if($config && $config['plugins']){
            $this->_plugins = $config['plugins'];
            //加载插件
            $has_error = false;
            $err = array();
            foreach($this->_plugins as $plugin){
                if(!empty($plugin['class_name']) && !empty($plugin['filename'])){
                    $file_name = $plugin['filename'];
                    if(file_exists($file_name)){
                        require $file_name;
                        $class_name = $plugin['class_name'];
                        if(class_exists($class_name)){
                            $this->_plugins_obj[] = new $class_name();
                        }else{
                            $has_error = true;
                            $err[] = $plugin['class_name'];
                        }
                    }else{
                        throw new m_Exception("配置插件有误，文件【{$file_name}】不存在！");
                    }
                }
            }
            if( $has_error){
                throw new m_Exception("类【".implode(";",$err)."】不存在！");
            }
        }
    }
    public function get_content($file_name,$cache_dir,$template_dir,$template_filter=".html",$is_cache=false,$is_include_var=true){
        $cache_dir = str_replace("\\","/",$cache_dir);
        check_dir($cache_dir);
        //***************************保存设置
        $this->template_dir = $template_dir;
        $this->template_filter = $template_filter;
        $this->cache_dir = $cache_dir;
        $this->is_cache = $is_cache;
        //**************************************
        return $template = $this->load_template($file_name,$is_include_var);
    }
    private function load_template($file_name,$is_include_var=true){
        $content = file_get_contents($file_name);

        if($is_include_var)$content = $this->include_var($content);
        $content = $this->analysis_tags($content);
        return $content;
    }
    private function analysis_tags($content){
        $content = $this->plugins_load_tags($content);//加载插件，解析模板
        $content = $this->foreach_tag($content);//先对循环进行替换，防止循环内部变量的便利问题
        $content = $this->replace_var($content);//变量替换
        $content = $this->if_tags($content);//if语句标签
        $content = $this->include_template($content);//变量替换
        return $content;
    }
    /**
     * 加载定义的变量
     *
     * @param [type] $content
     * @return void
     */
    private function include_var($content){
        global $m_template_var;
        $str = "<?php ";
        foreach($m_template_var as $key=>$val){
            if(is_array($val)){
                $j_str = json_encode($val);
                $str .= "$".$key.'=json_decode(\''.$j_str.'\',true);';
            }else{
                $str .= "$".$key.'="'.$val.'";';
            }
        }
        $str .= "?>";
        return $str.$content;
    }
    /**
     * 替换模板变量
     *
     * @param [type] $content
     * @return void
     */
    private function replace_var($content){
        global $m_template_var;
        $str = '{$title}name{$obj}test';
        $pattern = '/\{\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\.\x7f-\xff]*)\s*\}/i';
        $pattern1 = '/<!--\{\$[a-zA-Z_\.\x7f-\xff]*}-->/';//'/<!--\{\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\}-->/i';
        preg_match_all($pattern1,$content,$arr_ex);//注释写法
        //注釋寫法变量替换
       if(is_array($arr_ex) && count($arr_ex)>0){
            foreach($arr_ex[0] as $ex_val){
                $p_value = str_replace("<!--{","",$ex_val);
                $p_value = str_replace("}-->","",$p_value);
                $_key = substr($p_value,1);
                if(!strpos($_key,'.')){
                    $content = str_replace($ex_val,'<?php echo isset('.$p_value.') ? '.$p_value.' : "";?>',$content );
                }else{
                    $ar = explode(".",$_key); 
                    $t_str = '';
                    foreach($ar as $key=>$t){
                        $t_str .= $key>0 ? "['{$t}']" : "$".$t;
                    }
                    $content = str_replace($ex_val,'<?php echo isset('.$t_str.') ? '.$t_str.' : "";?>',$content );
                }
            }
        }
        //------------------------------------------
        preg_match_all($pattern,$content,$arr);
        if(is_array($arr) && count($arr)>1){
            $key_arr = $arr[1];
            foreach($key_arr as $key_val){
                if(!strpos($key_val,'.')){
                    $content = str_replace('{$'.$key_val.'}','<?php echo isset($'.$key_val.') ? $'.$key_val.' : "";?>',$content );
                }else{
                    $ar = explode(".",$key_val);
                    $t_str = '';
                    foreach($ar as $key=>$t){
                        $t_str .= $key>0 ? "['{$t}']" : "$".$t;
                    }
                    $content = str_replace('{$'.$key_val.'}','<?php echo isset('.$t_str.') ? '.$t_str.' : "";?>',$content );
                }
            }
        }
        return $content;
        
    }
    /**
     * 加载外部文件引用
     *
     * @param [type] $content
     * @return void
     */
    private function include_template($content){
        $patetern1 = "/(<include).\s*\S*(\/>)/i";
        $template_dir = $this->template_dir;
        $template_filter = $this->template_filter;
        $t_path = preg_match("/\/$/",$template_dir) ? $template_dir : $template_dir."/";
        $filter = preg_match("/^\./",$template_filter) ? $template_filter : ".".$template_filter;
        $reg_str = "/($filter)$/";
        $r_path = $_SERVER['DOCUMENT_ROOT'];
        //include标签
        preg_match_all($patetern1,$content,$arr);
        if($arr && count($arr)>0){
            $tag = $arr[0];
            foreach($tag as $t_clude){
                if(preg_match("/file='.\S*'/i",$t_clude,$t_arr)){
                    $f_str = $t_arr[0];
                    $f_str = preg_replace("/file='/i",'',$f_str);
                    $f_str = str_replace("'",'',$f_str); 
                    if(!preg_match("/^\//",$f_str)){
                        $filename = preg_match($reg_str,$f_str) ? $f_str : $f_str.$filter;
                        $filename = $t_path.$filename; 
                        if(!file_exists($filename)){
                            throw new Exception("模板文件不存在：【{$f_str}】");
                        }else{
                            
                            $t_content = $this->get_content($filename,$this->cache_dir,$this->template_dir,$this->template_filter,$this->is_cache,false);
                            $content = str_replace($t_clude,$t_content,$content);
                        }
                    }else{
                        $filename = preg_match($reg_str,$f_str) ? $f_str : $f_str.$filter;
                        $filename = $r_path.$filename; 
                         if(!file_exists($filename)){
                            throw new Exception("模板文件不存在：【{$f_str}】");
                        }else{
                           
                           $t_content = $this->get_content($filename,$this->cache_dir,$this->template_dir,$this->template_filter,$this->is_cache,false);
                           $content = str_replace($t_clude,$t_content,$content);
                        }
                    }
                }
            }
        }
        //注释写法include引用
        $patten2 = "/(<!--\{include).\s*.\S*}-->/";
        if(preg_match_all($patten2,$content,$p_arr)){
            $tags = $p_arr[0];
            foreach($tags as $tag){
                $m_file = str_replace("<!--{include ","",$tag);
                $m_file = str_replace("}-->","",$m_file);
                if(!preg_match("/^\//",$m_file)){
                    $filename = preg_match($reg_str,$m_file) ? $m_file : $m_file.$filter;
                    $filename = $t_path.$filename; 
                    if(!file_exists($filename)){
                        throw new Exception("模板文件不存在：【{$m_file}】");
                    }else{
                        $t_content = $this->get_content($filename,$this->cache_dir,$this->template_dir,$this->template_filter,$this->is_cache,false);
                        $content = str_replace($tag,$t_content,$content);
                    } 
                }else{
                    $filename = preg_match($reg_str,$m_file) ? $m_file : $m_file.$filter;
                    $filename = $r_path.$filename; 
                        if(!file_exists($filename)){
                        throw new Exception("模板文件不存在：【{$m_file}】");
                    }else{
   
                        $t_content = $this->get_content($filename,$this->cache_dir,$this->template_dir,$this->template_filter,$this->is_cache,false);
                        $content = str_replace($tag,$t_content,$content);
                    }
                }
            }
        }
         
        return $content;
    }
    /**
     * foreach循环标签解析
     *
     * @param [type] $content
     * @return void
     */
    private function foreach_tag($content){
        $content = $this->get_foreach_tags($content);
        return $content;
        //echo $content;
    }
    private function get_foreach_tags($content){
        $patten = '/<\!--\{foreach.*(<\!--\{\/foreach\}-->)/isU';
        $p_arr = array();
        $p_str = $content;
        $t_arr = array();
        $foreach_tag = "";
        while(preg_match($patten,$p_str,$p_arr)){
            $t_arr = $p_arr;
            $tag_str= $p_arr[0]; 
            $t_patten = "/<\!--\{foreach.*-->/";
            if(preg_match($t_patten,$tag_str,$t_arr)){
                $m_index = strpos($tag_str,$t_arr[0]);
                $p_str = substr($tag_str,$m_index+strlen($t_arr[0]));
                if(!preg_match($t_patten,$p_str,$t_arr)){
                     $foreach_tag = $tag_str;break;
                }     
            }else{
                $p_str='';
            }
        }
        if(preg_match("/<\!--\{foreach.*-->/",$foreach_tag,$t_arr)){
            $tag = $t_arr[0];
            $tag_str = str_replace("<!--{",'',$tag);
            $tag_str = str_replace("}-->",'',$tag_str);
            while(strpos($tag_str,"  ")){
                $tag_str = str_replace("  "," ",$tag_str);
            }
            $tag_arr = explode(" ",$tag_str);
            $from = "";
            $item = "";
            $f_key = "";
            foreach($tag_arr as $tag_val){
                $eq_index = strpos($tag_val,"=");
                if($eq_index){
                    $key = substr($tag_val,0,$eq_index);
                    $val = substr($tag_val,$eq_index+1);
                    if(strtolower($key)=="from")$from = $val;
                    if(strtolower($key)=="item")$item = $val;
                    if(strtolower($key)=="key")$f_key = $val;
                }
            }
            //解析from参数出现.符号替换成数组
            if(strpos($from,'.')){
                $ar = explode(".",$from);
                $t_str = '';
                foreach($ar as $t_key=>$t){
                    $t_str .= $t_key>0 ? "['{$t}']" : $t;
                }
                $from = $t_str;
            }
            $foreach_content = str_replace($tag,"",$foreach_tag); 
            $foreach_content = str_replace("<!--{/foreach}-->","",$foreach_content);
            $_tag_code = !empty($f_key) ? "<?php foreach($".$from." as $".$f_key."=>$".$item."){ ?>" : "<?php foreach($".$from." as $".$item."){ ?>";
            $foreach_content_code = $this->analysis_tags($foreach_content);
            $foreach_tag_code = str_replace($tag,$_tag_code,$foreach_tag);
            $foreach_tag_code = str_replace($foreach_content,$foreach_content_code,$foreach_tag_code);
            $foreach_tag_code = str_replace("<!--{/foreach}-->","<?php } ?>",$foreach_tag_code);
            
            $content = str_replace($foreach_tag,$foreach_tag_code,$content); 
            if(preg_match($patten,$content)){
                $content = $this->get_foreach_tags($content);
            }
        }
        return $content;
    }
    /**
     * if语句解析
     *
     * @param [type] $content
     * @return void
     */
    private function if_tags($content){
        $content = preg_replace("/(<\!--\{if)(.*)(}-->)/","<?php if($2){ ?>",$content);
        $content = preg_replace("/<\!--\{else\}-->/","<?php }else{ ?>",$content);
        $content = preg_replace("/<\!--\{\/if\}-->/","<?php } ?>",$content);
        $content = preg_replace("/(<\!--\{elseif)(.*)(}-->)/","<?php }elseif($2){ ?>",$content);
        return $content;
    }
    private function plugins_load_tags($content){
        foreach($this->_plugins_obj as $obj){
            $methods = get_class_methods($obj);
            foreach($methods as $method){
               $content = $obj->$method($content); 
            }
        }
        return $content;
    } 
}