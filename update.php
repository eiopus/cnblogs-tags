<?php
/**
 * 处理上传
 */

$path = $_FILES['tags']['tmp_name'];
$content = file_get_contents($path);

file_put_contents(sys_get_temp_dir() . '/bookmark.html', $content);

header("location:index.php");
