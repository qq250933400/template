<?php

require_once "m_config.php";
require_once "function.php";
require_once "m_core.php";
require_once "m_template.php";

global $m_template;
$m_template = new m_template();
$m_template->set_cache_dir(ROOT_PATH."/data/cache/template");
$m_template->set_template_dir(ROOT_PATH."/public/views");
$m_template->set_filter(".html");
$m_template->config_plugin(array(
    array(
        'class_name'=>"m_template_tags",'filename'=>__DIR__.'/m_tag.php'
    ),
));
ini_set("display_errors", "On"); 
error_reporting(E_ALL | E_STRICT);