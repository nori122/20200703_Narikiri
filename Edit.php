<?php
// 送信データのチェック

session_start();

// 関数ファイルの読み込み
include("functions.php");
check_session_id();

$id = $_SESSION["id"];

$pdo = connect_to_db();

// データ取得SQL作成
$sql = 'SELECT * FROM users_table WHERE id=:id';

// SQL準備&実行
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

// データ登録処理後
if ($status == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    // HTMLのuser_id表示欄で使用する変数
    $record_name = $stmt->fetch(PDO::FETCH_ASSOC);
}

//    ↑プロフィールの名前欄にuser_idの表示
// ------------------------------------------------------------------------------------------------
//    ↓プロフィール写真データ受け取り＆表示

// データ取得SQL作成
$sql = 'SELECT image FROM users_table WHERE id=:id';

// SQL準備&実行
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

// データ登録処理後
if ($status == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    // 正常にSQLが実行された場合は入力ページファイルに移動し，入力ページの処理を実行する
    // fetchAll()関数でSQLで取得したレコードを配列で取得できる
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);  // データの出力用変数（初期値は空文字）を設定
    $output = "";
    foreach ($result as $record) {
        $output .= "<p><img src='{$record["image"]}' height=150px></p>";
    }
    // $valueの参照を解除する．解除しないと，再度foreachした場合に最初からループしない
    // 今回は以降foreachしないので影響なし
    unset($value);
}

?>

<!---------------------
HTML 要素
--------------------->
<!DOCTYPE html>
<html lang='ja'>

<head>
    <meta charset='UTF-8'>
    <link rel='stylesheet' href='styles.css'>
    <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.13.0/css/all.css' integrity='sha384-Bfad6CLCknfcloXFOyFnlgtENryhrpZCe29RTifKEixXQZ38WheV+i/6YWSzkz3V' crossorigin='anonymous'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <title>Narikiri_Edit</title>
</head>

<body>
    <legend>なりきりプロフィール編集</legend>
    <a href="Timeline.php">ホーム画面</a>
    <form action="update.php" method="POST" enctype="multipart/form-data">

        <div>
            <input type="file" name="upfile" accept="image/*">
            <div> <?php echo $output ?> </div> <!-- プロフィール写真の表示 -->
        </div>

        <div>
            <!-- ユーザー新規登録時に登録したuser_idを表示する -->
            <span>User ID : <?php echo $_SESSION["user_id"] ?></p></span>
        </div>
        <div>
            <span>name :</span>
            <input type="text" name="name" placeholder="User Name" id="user_name" value="<?= $record_name["name"] ?>">
        </div>

        <div>
            <button>Save</button>
        </div>
    </form>

    <script>
        // もし、名前欄のvalueが空であれば、ユーザー新規登録時に登録したuser_idを名前欄に表示する
        if ($('#user_name').val() == "") {
            $('#user_name').val("<?= $_SESSION["user_id"] ?>");
        }
    </script>

</body>

</html>