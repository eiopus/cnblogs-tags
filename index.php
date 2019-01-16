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

// 拼接数组，排序
$data = array_combine($all, $links);
ksort($data, SORT_NATURAL);

// 按照类别来分组
$info = [];
foreach ($data as $tag => $link) {
    $posLeftBrackets = stripos($tag, '(');

    // 共有多少个收藏
    $num = substr($tag, $posLeftBrackets + 1, -1);

    // tag进行分类
    if (stripos($tag, '_') !== false) {
        // a_b_c(10)
        $category = substr($tag, 0, strpos($tag, '_'));
        $subCategory = substr($tag, 0, stripos($tag, '_'));
    } else {
        // a(10)
        $subCategory = $category = substr($tag, 0, $posLeftBrackets);
    }


    $list[$subCategory][$tag] = [
        'link' => $link,
        'num' => $num,
    ];
    $info[$category] = [
        'list' => $list,
        'num' => $num,
    ];
}



/**
 * 页面显示
 */
$loader = new Twig_Loader_Filesystem(__DIR__);
$twig = new Twig_Environment($loader, [
    'cache' => __DIR__ . '/twig_cache/',
]);

echo $twig->render('index.html', ['info' => $info]);









// $str = '';
// $i = 1;
//
// foreach ($result as $category => $all) {
//     // 大类别
//     $str .= '<br><h3>' . $category . '</h3>';
//
//     // 子类别
//     $str .= '<ul>';
//     foreach ($all as $k => $v) {
//         $str .=
//             '<li>
//               <a target="_blank" href="' . $v . '">' . $k . '</a>
//             </li>';
//     }
//     $str .= '</ul>';
// }
//
// echo str_replace("{str}", $str, file_get_contents("index.html"));
