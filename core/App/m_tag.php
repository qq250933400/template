<?php
namespace mTemplate\App;
class m_tag{
    /**
     *常量替换
     *
     * @param string $content
     * @return string
     */
    public function const_tags($content){
        $content = preg_replace("/(__)([a-zA-Z0-9\_]*)(__)/","<?php  echo defined('$2') ? $2 : '$1$2$3';?>",$content);
        $content = preg_replace("/(<\!--\{const:)([a-zA-Z0-9\_]*)(\}-->)/","<?php  echo defined('$2') ? $2 : '$1$2$3';?>",$content);
        return $content; 
    }
    /**
     * 调用全局函数
     *
     * @param string $content
     * @return string
     */
    public function fn_tags($content){
        //{:get_rand_str|6}
        if(preg_match_all("/(<\!--\{\:)([a-zA-Z0-9\_|]*)\|([a-zA-Z0-9\_\,\$]*)(\}-->)/",$content,$p_arr)){
            foreach($p_arr[0] as $key=>$val){
                $param = $p_arr[3][$key];//要传参数
                $param = explode(",",$param);
                $p_val = "";
                foreach($param as $pa){
                    $pa = substr($pa,0,1)=='$' ? $pa : (is_numeric($pa) ? $pa : "\"".$pa."\"");
                    $p_val .= empty($p_val) ? $pa : ",".$pa;
                }
                $fn = $p_arr[2][$key];
                $fn = "<?php echo function_exists('$fn') ? {$fn}($p_val) : '';?>";
                $content = str_replace($val,$fn,$content);
            }
        }
        
        if(preg_match_all("/(\{\:)([a-zA-Z0-9\_|]*)\|([a-zA-Z0-9\_\,\$]*)(\})/",$content,$arr)){
            foreach($arr[0] as $key=>$val){
                $param = $arr[3][$key];//要传参数
                $param = explode(",",$param);
                $p_val = ""; 
                foreach($param as $pa){
                    $pa = substr($pa,0,1)=='$' ? $pa : (is_numeric($pa) ? $pa : "\"".$pa."\"");
                    $p_val .= empty($p_val) ? $pa : ",".$pa;
                }
                $fn = $arr[2][$key];
                $fn = "<?php echo function_exists('$fn') ? {$fn}($p_val) : '';?>";
                $content = str_replace($val,$fn,$content);
            }
        }
         
        
        /*$content = preg_replace("/(\{:)([a-zA-Z0-9\_]*)(|)(([a-zA-Z0-9\_\,]*))(\})/","<?php  echo defined('$2') ? $2 : '$1$2$3';?>",$content);*/
        return $content; 
    }
     
}
 