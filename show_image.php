<?php
// menampilkan gambar (jpeg) yang disimpan dalam database Replit

require __DIR__.'/ReplitDB.php';
use Dannsbass\ReplitDB as Db;
$db = new Db;
$fn = 'img.jpeg';
//$db->set($fn, base64_encode(file_get_contents($fn)));
$img = $db->get($fn);

echo "<img src='data:image/jpeg;base64, $img'/>";