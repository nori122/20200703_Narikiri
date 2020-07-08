<?php

include('functions.php');

$user_id = $_GET['user_id'];
$like_id = $_GET['like_id'];

$pdo = connect_to_db();

//SELECT文はtableから好きな値を取り出す事ができる
//COUNT(*)はテーブルの値の数の事→この場合はuser_idとlike_idの2つがある為、「2」となる。
$sql = 'SELECT COUNT(*) FROM like_table WHERE user_id=:user_id AND like_id=:like_id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':like_id', $like_id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status == false){
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
} else {
  $like_count = $stmt->fetch();
  // header('Location:todo_read.php');
}

if ($like_count[0] != 0) {
  //既にいいねされてる場合、$sqlをここで上書きして、DELETEする
$sql ='DELETE FROM like_table WHERE user_id=:user_id AND like_id=:like_id';
  //いいねされていない場合、$sqlをここで上書きして、INSERT(追加)でテーブルを追加する
} else {
$sql = 'INSERT INTO like_table(id, user_id, like_id) VALUES(NULL, :user_id, :like_id)'; // 1行で記述！ 
}

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':like_id', $like_id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status == false) {
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
} else {
  header('Location:Timeline.php');
}