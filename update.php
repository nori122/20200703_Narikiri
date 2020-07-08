<?php

// 送信データのチェック


// 関数ファイルの読み込み
session_start();
include("functions.php");
check_session_id();

// 送信データ受け取り
$name = $_POST["name"];
$id = $_SESSION["id"];

// DB接続
$pdo = connect_to_db();

// ----------------------------------------------------------------------------------------

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

}


if (is_uploaded_file($tempPathName)) {
  if (move_uploaded_file($tempPathName, $fileNameToSave)) {
    chmod($fileNameToSave, 0644);
  } else {
    exit('Error:アップロードできませんでした'); // 画像の保存に失敗 
  }
}

// ----------------------------------------------------------------------------------------

// UPDATE文を作成&実行
$sql = "UPDATE users_table SET name=:name,image=:image,updated_at=sysdate() WHERE id=:id";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':image', $fileNameToSave, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

// var_dump($fileNameToSave);
// exit();

// データ登録処理後
if ($status == false) {
  // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
  exit();
} else {
  // 正常にSQLが実行された場合は一覧ページファイルに移動し，一覧ページの処理を実行する
  header("Location:Timeline.php");
  exit();
}
