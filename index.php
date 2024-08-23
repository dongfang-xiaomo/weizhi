<?php
// 获取访问者的相关信息
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$referer = $_SERVER['HTTP_REFERER'];
$remote_addr = $_SERVER['REMOTE_ADDR'];

// 检查 app 目录是否存在，不存在则创建
if (!is_dir('app')) {
    mkdir('app');
}

// 创建以 IP 命名的目录
$ip_directory = 'app/'. $remote_addr. '.txt';

// 检查访问次数记录文件
$ipjl_file = 'ipjl.php';
if (!file_exists($ipjl_file)) {
    // 如果文件不存在，创建并初始化
    $ip_count = array();
    file_put_contents($ipjl_file, serialize($ip_count));
}

// 读取访问次数记录
$ip_count = unserialize(file_get_contents($ipjl_file));

// 如果 IP 已存在于记录中
if (array_key_exists($remote_addr, $ip_count)) {
    // 增加访问次数
    $ip_count[$remote_addr]++;
} else {
    // 如果是新 IP，初始化为 1
    $ip_count[$remote_addr] = 1;
}

// 写入更新后的访问次数记录
file_put_contents($ipjl_file, serialize($ip_count));

// 如果访问次数超过 10 次，拒绝访问并提示
if ($ip_count[$remote_addr] > 10) {
    echo "您的访问次数已超过限制。";
    exit;
}

// 打开或创建文件以保存信息
$file = fopen($ip_directory, "a");  // 使用 "a" 模式以追加内容，不覆盖原有内容

// 写入访问者的信息和访问次数
$content = "访问次数：". $ip_count[$remote_addr]. "\n";
$content.= "访问时间：". date('Y-m-d H:i:s'). "\n";
$content.= "用户代理：". $user_agent. "\n";
$content.= "来源页面：". $referer. "\n";

// 写入文件
fwrite($file, $content);

// 关闭文件
fclose($file);

// 自动跳转至百度
header("Location: https://www.baidu.com");
?>