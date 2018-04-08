<?php

class TemplateAutoLoader{
    public static function includePath(){
        return array(
            "mTemplate\\App\\"   => __DIR__."/App/"
        );
    }
    public static function AutoLoad($className){
        $paths = self::includePath();
        $matchLength = 0;
        $matchPath = "";
        $matchNamespace = "";
        $isMatch = false;
        foreach($paths as $key => $path){
            $path = str_replace("\\","/", $path);
            $keyLen = strlen($key);
            $matchStr = substr($className,0, $keyLen);
            if($matchStr == $key){
                if(strlen($key)>$matchLength){
                    $matchPath = $path;
                    $isMatch = true;
                    $matchNamespace = $key;
                }
            }
        }
        if($isMatch){
            $name = str_replace($matchNamespace, "", $className);
            $fileName = $matchPath.$name.".php";
            if(file_exists($fileName)){
                self::log("include ".$fileName);
                require $fileName;
            }else{
                self::log("Can not find the class's filename:".$fileName);
            }
        }else{
            self::log("Can not match namespace:".$className);
        }
    }
    static function log($msg){
        if(defined("debug") && debug){
            debug($msg);
        }
    }
}

spl_autoload_register('TemplateAutoLoader::AutoLoad');