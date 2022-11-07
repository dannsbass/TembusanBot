<?php
require __DIR__ .'/ReplitDB.php';
use Dannsbass\ReplitDB as DB;
$db = new DB;
if(empty(trim($db->get('token')))){
    $db->set('token', 'xxx'); //ganti xxx dengan token asli
}else{
    $token = $db->get('token');
}
$url = 'https://XXX.YYY.repl.co'; //ganti XXX dan YYY dengan URL asli
$tg = "https://api.telegram.org/bot$token/setwebhook?url=$url";
echo file_get_contents($tg);