<?php
/**
 * 处理上传
 */

if (PHP_SESSION_ACTIVE != session_status()) {
    session_start();
}

if ('post' == strtolower($_SERVER['REQUEST_METHOD'])) {
    if ($_SESSION['token'] != $_POST['token'] ||
        $_FILES['tags']['error'] != 0 ||
        $_FILES['tags']['type'] != 'text/html') {
        echo '无效提交';
        exit();
    }

    $file = new SplFileInfo($_FILES['tags']['tmp_name']);
    if (!$file->isFile() || $file->getExtension() != 'html') {
        echo '无效提交';
        exit();
    }

    $path = $_FILES['tags']['tmp_name'];
    $content = file_get_contents($path);

    file_put_contents(sys_get_temp_dir() . '/bookmark.html', $content);

    header("location:index.php");
}

$randomToken = uniqid();
$_SESSION['token'] = $randomToken;

$str = file_get_contents('update.html');
echo str_replace('{{token}}', $randomToken, $str);
