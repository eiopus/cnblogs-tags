<?php

if (!file_exists('vendor/autoload.php')) {
    exit("请先使用'composer install'安装所需要的依赖");
}

require 'vendor/autoload.php';      //加载composer自动加载文件

use QL\QueryList;

$html = "";
$filepath = sys_get_temp_dir() . '/bookmark.html';        //用于存放cnblogs 的标签页面html源代码的一个文件
$ql = QueryList::getInstance();

if (file_exists($filepath)) {
    $html = file_get_contents($filepath);
}

// 抓取tags的名称和数目
$arr = $ql->setHtml($html)->find("#mywztag li a")->texts();
$all = $arr->all();

// 抓取tags的超链接
$links = $ql->setHtml($html)->find("#mywztag li a")->attrs('href');
$links = $links->all();

$data = array_combine($all, $links);
ksort($data, SORT_NATURAL);

// 按照类别来分组，拼接数组
$result = [];
foreach ($data as $k => $v) {
    if (strpos($k, '_') !== false) {
        $name = substr($k, 0, strpos($k, '_'));
    } else {
        $name = substr($k, 0, strpos($k, '('));
    }
    $result[$name][$k] = $v;
}







$str = '';
$i = 1;

foreach ($result as $category => $all) {
    $str .= '<br><h3>' . $category . '</h3>';
    $str .= '<ul>';
    foreach ($all as $k => $v) {
        $str .=
            '<li>
              <a target="_blank" href="' . $v . '">' . $k . '</a>
            </li>';
    }
    $str .= '</ul>';
}

echo str_replace("{str}", $str, file_get_contents("index.html"));
