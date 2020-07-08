<?php
session_start();
include("functions.php");
check_session_id();


// 項目入力のチェック
// 値が存在しないor空で送信されてきた場合はNGにする
if (
  !isset($_POST['fake_id']) || $_POST['fake_id'] == '' ||
  !isset($_POST['tweet']) || $_POST['tweet'] == ''||
  !isset($_POST['real_id']) || $_POST['real_id'] == ''
) {
  // 項目が入力されていない場合はここでエラーを出力し，以降の処理を中止する
  echo json_encode(["error_msg" => "no input"]);
  exit();
}

// 受け取ったデータを変数に入れる
$fake_id = $_POST['fake_id'];
$tweet = $_POST['tweet'];
$real_id = $_POST['real_id'];

// DB接続
$pdo = connect_to_db();


// ここからファイルアップロード&DB登録の処理を追加しよう！！！
if (isset($_FILES['upfile']) && $_FILES['upfile']['error'] == 0) {
  //ファイルがある ＆ ファイルにエラーがない（0） → 処理実行 

  //下記は一時保管場所
  $uploadedFileName = $_FILES['upfile']['name'];
  $tempPathName = $_FILES['upfile']['tmp_name'];
  // アップロード先のファイルは自分で決める。この場合は'upload/'
  $fileDirectoryPath = 'upload/';
  // pathinfoは拡張子を取得する関数。この場合はjpg。
  $extension = pathinfo($uploadedFileName, PATHINFO_EXTENSION);
  $uniqueName = date('YmdHis') . md5(session_id()) . "." . $extension;
  $fileNameToSave = $fileDirectoryPath . $uniqueName;

  // var_dump($fileNameToSave);
  // exit();
}


if (is_uploaded_file($tempPathName)) {
  if (move_uploaded_file($tempPathName, $fileNameToSave)) {
    chmod($fileNameToSave, 0644);
  } else {
    exit('Error:アップロードできませんでした'); // 画像の保存に失敗 
  }
}


// データ登録SQL作成
// `created_at`と`updated_at`には実行時の`sysdate()`関数を用いて実行時の日時を入力する
$sql = 'INSERT INTO tweet_table(id, fake_id, tweet, image, created_at,real_id) VALUES(NULL, :fake_id, :tweet, :image, sysdate(),:real_id)';

// SQL準備&実行
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':fake_id', $fake_id, PDO::PARAM_STR);
$stmt->bindValue(':tweet', $tweet, PDO::PARAM_STR);
$stmt->bindValue(':image', $fileNameToSave, PDO::PARAM_STR);
$stmt->bindValue(':real_id', $real_id, PDO::PARAM_STR);
$status = $stmt->execute();

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
