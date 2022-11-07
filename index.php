<?php 
require __DIR__ .'/Bot.php';
require __DIR__ .'/ReplitDB.php';

use \Dannsbass\ReplitDB;

$db = new ReplitDB();

if(empty(trim($db->get('token')))){
  $db->set('token', 'xxx'); //ganti xxx dengan token asli
  $token = $db->get('token');
}

if(empty(trim($db->get('username')))){
  $db->set('username', 'xxx'); //ganti xxx dengan username asli
  $username = $db->get('username');
}

$bot = new Bot($token, $username);

// START
$bot->start(function()use($db){
  //laporan ke admin (1231968913)
  Bot::sendMessage(Bot::$from_id, ['chat_id'=>1231968913]);
  
  $tg = Bot::message();
  if(empty(trim($db->get('ids')))){
    $db->set('ids', '');
  }
  $id = $db->get('ids');
  $ids = explode(' ', $id);
  
  //jika bukan private chat, balik
  if($tg['chat']['type'] != 'private') return;
  
  $uid = $tg['from']['id'];

  // jika pertama start
  if(!in_array($uid, $ids)){
    $db->set('ids', "$id $uid");
  }

  $pesan = 'Masukkan bot ini ke dalam grup SUMBER';
  
  $tombol = Bot::inline_keyboard('[PILIH GRUP SUMBER|https://t.me/'.$db->get('username').'?startgroup]');

  //Bot::sendMessage('id grup ini adalah '.Bot::$chat_id);
  
  return Bot::sendMessage($pesan,['reply_markup'=>$tombol]);
  
});

// JIKA BOT MASUK GRUP
$bot->new_chat_members(function()use($db){
  $tg = Bot::message();
  $user = Bot::$user;

  // jika bukan TembusanBot, balik
  if($tg['new_chat_member']['id'] != 5514017568 or $tg['new_chat_member']['username'] != $db->get('username')) return;

  $uid = $tg['from']['id'];
  $cid = $tg['chat']['id'];

  // jika grup sdh terdaftar, daftarkan sbg tujuan
  // from123
  $key = trim($db->keys("from$uid"));
  
  if(!empty($key)){
    // to456 = 456
    $db->set("to".$db->get("from$uid"), $cid);
    $db->del("from$uid");
    
    return Bot::sendMessage("Berhasil",['chat_id'=>$uid]);
  } 
    // jika blm, daftarkan sbg asal
  else {
    
    // kalau si admin belum pernah chat privat bot, balas di grup
    $id = $db->get('ids');
    $ids = explode(' ', $id);
    
    if(!in_array($uid, $ids)){
      
      $admin = "<a href='tg://user?id=$uid'>$user</a>";
      
      Bot::sendMessage("$admin silahkan hubungi saya secara private", ['parse_mode'=>'html']);

      Bot::leaveChat($cid);

      return;
      
    }

    // kalau admin sudah chat private

    $db->set("from$uid", $cid);

    $pesan = 'Masukkan bot ini ke dalam grup TUJUAN';
    
    $kb = Bot::inline_keyboard('[PILIH GRUP TUJUAN|https://t.me/'.$db->get('username').'?startgroup]
  ');
    
    return Bot::sendMessage($pesan,['chat_id'=>$uid, 'reply_markup'=>$kb]);
  }
  
});



// TEXT
$bot->chat('/keys', function()use($db){
  return Bot::sendMessage($db->keys());
});

$bot->chat('/del', function()use($db){
  foreach(explode("\n", $db->keys("to-")) as $to){
    $db->del($to);
  }
  return Bot::sendMessage($db->keys());
});

$bot->chat('/ids', function()use($db){
  return Bot::sendMessage($db->get('ids'));
});



  

// ALL MESSAGES
$bot->all(function()use($db){
  
  $cid = Bot::$chat_id;
  
  if(empty(trim($db->keys("to$cid")))) return;
  
  return Bot::forwardMessage([
    'chat_id'=>$db->get("to$cid"),
    'from_chat_id'=>$cid,
    'message_id'=>Bot::$message_id
  ]);
});

// RUN
$bot->run();