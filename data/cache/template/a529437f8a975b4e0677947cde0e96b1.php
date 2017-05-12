<?php $data=json_decode('{"tst1":{"0":"name1","data":["list1","olist1","item1"]},"tst3":{"0":"name2","data":["list2","olist2","item2"]},"tst4":{"0":"name3","data":["list3","olist3","item3"]}}',true);$title="首页";$test_var="测试";$is_close="1";?><!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo isset($title) ? $title : "";?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
        
    </head>
    <body>
        <span>
    include 标签测试
</span>
<br/>
        <p>
        <?php echo isset($test_var) ? $test_var : "";?>
        </p>
        <span>数组测试：<?php echo isset($data['test']) ? $data['test'] : "";?></span>
        <a href="index.php?a=clear_cache">清除缓存</a>
        <span>底部文件测试</span>
        <ul>
            <?php foreach($data as $mykey=>$obj){ ?>
            <li><?php echo isset($obj['name']) ? $obj['name'] : "";?><?php echo isset($mykey) ? $mykey : "";?>
                <?php foreach($obj['data'] as $subdata){ ?>
                <span><?php echo isset($subdata) ? $subdata : "";?></span>
                <?php } ?>
                </li>
            <?php } ?>
            <?php foreach($data as $mykey=>$obj){ ?>
            <li><?php echo isset($obj['name']) ? $obj['name'] : "";?><?php echo isset($mykey) ? $mykey : "";?>
                <?php foreach($obj['data'] as $subdata){ ?>
                <span><?php echo isset($subdata) ? $subdata : "";?></span>
                <?php if( $subdata=='item2'){ ?>
                    <i>item2有奖励</i>
                <?php } ?>
                <?php } ?>
                </li>
            <?php } ?>
        </ul>
        <?php if( $is_close==1){ ?>
            <a href="javascript:alert('close window');">关闭</a>
        <?php }elseif( $is_close==2){ ?>
            <a href="javascript:alert('retry window');">重试</a>
        <?php }else{ ?>
            <a href="javascript:alert('exit window');">退出</a>
        <?php } ?>

        <?php  echo defined('ROOT_PATH') ? ROOT_PATH : '<!--{const:ROOT_PATH}-->';?>
        <?php  echo defined('ROOT_PATH') ? ROOT_PATH : '__ROOT_PATH__';?>
        <?php  echo defined('ROOT') ? ROOT : '__ROOT__';?>
        <span style="display:block;">
            全局函数调用<?php echo function_exists('get_rand_str') ? get_rand_str(6,"name","key") : '';?>
            <?php echo function_exists('debug') ? debug($obj) : '';?>
            <?php echo function_exists('debug') ? debug($obj) : '';?>
        </span>
        <p style="font-size:18px;">18px</p>
        <p style="font-size:12px;">12px</p>
        <p style="font-size:10px;">10px</p>
        <p style="font-size:8px;">8px</p>
        <p style="font-size:6px;">6px</p>
    </body>