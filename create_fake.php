<?php
session_start();
include("functions.php");
check_session_id();


// 受け取ったデータを変数に入れる
$fake_id = $_GET['fake_id'];
// var_dump($_GET);
// exit;

// DB接続
$pdo = connect_to_db();

// データ登録SQL作成
// `created_at`と`updated_at`には実行時の`sysdate()`関数を用いて実行時の日時を入力する
// $sql ='INSERT INTO fake_table(id,fake_id) VALUES(NULL,:fake_id)';
$sql = 'INSERT INTO tweet_table(id, fake_id, tweet, image, created_at,real_id) VALUES(NULL, :fake_id, 0, 0, sysdate(),0)';

// SQL準備&実行
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':fake_id', $fake_id, PDO::PARAM_STR);
$status = $stmt->execute();

// var_dump($_GET);
// exit;

// データ登録処理後
if ($status == false) {
  // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
  exit();
} else {
  // 正常にSQLが実行された場合は入力ページファイルに移動し，入力ページの処理を実行する
  header("Location:tweet.php");
  exit();
}

?>