<?php
session_start();
include("functions.php");
check_session_id();

// 現在ログインしている人のid・・・なりすましの履歴で用いる
$real_id = $_SESSION["id"];

// DB接続
$pdo = connect_to_db();

// データ取得SQL作成
// 履歴に5つだけ表示させる
$sql = "SELECT fake_id FROM tweet_table WHERE real_id =  $real_id ORDER BY id DESC LIMIT 5";;

// SQL準備&実行
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

// データ登録処理後
if ($status == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);  // データの出力用変数（初期値は空文字）を設定
    $output2 = "";

    foreach ($result as $record) {
        $output2 .= "<span>{$record["fake_id"]}</span>";
    }

    unset($value);
}

//   ↑なりすまし履歴
// ----------------------------------------------------------------------------------------------------
//   ↓なりすましID受け取り(1つだけ受け取る)

// データ取得SQL作成
// なりすまし一覧(pretend.php)で選んだfake_idをツイート画面(tweet.php)へ持ってくる→fake_idはツイート時に使用
// LIMIT 1 を付けないと、今まで選んだ全てのfake_idが飛んでくる
$sql = 'SELECT fake_id FROM tweet_table ORDER BY id DESC LIMIT 1';

// SQL準備&実行
$stmt = $pdo->prepare($sql);
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
    $output .= "{$record["fake_id"]}";
    }
    // $valueの参照を解除する．解除しないと，再度foreachした場合に最初からループしない
    // 今回は以降foreachしないので影響なし
    unset($value);
}
// ↑  なりすましID受け取り(1つだけ受け取る)
// ----------------------------------------------------------------------------------------------------
// ↓  1つ1つのfake_idを引っ張ってくる。過去5人になりすました履歴。

// ログインしたユーザー(real_id)が過去になりすました人(fake_id)の履歴を取得
// 「OFFSET 数字」は何行前のデータを取得するか選択できる。
$sql1 = "SELECT fake_id FROM tweet_table WHERE real_id =  $real_id ORDER BY id DESC LIMIT 1 OFFSET 0";;
$sql2 = "SELECT fake_id FROM tweet_table WHERE real_id =  $real_id ORDER BY id DESC LIMIT 1 OFFSET 1";;
$sql3 = "SELECT fake_id FROM tweet_table WHERE real_id =  $real_id ORDER BY id DESC LIMIT 1 OFFSET 2";;
$sql4 = "SELECT fake_id FROM tweet_table WHERE real_id =  $real_id ORDER BY id DESC LIMIT 1 OFFSET 3";;
$sql5 = "SELECT fake_id FROM tweet_table WHERE real_id =  $real_id ORDER BY id DESC LIMIT 1 OFFSET 4";;

// SQL準備&実行
$stmt1 = $pdo->prepare($sql1);
$status1 = $stmt1->execute();

$stmt2 = $pdo->prepare($sql2);
$status2 = $stmt2->execute();

$stmt3 = $pdo->prepare($sql3);
$status3 = $stmt3->execute();

$stmt4 = $pdo->prepare($sql4);
$status4 = $stmt4->execute();

$stmt5 = $pdo->prepare($sql5);
$status5 = $stmt5->execute();

// データ登録処理後
if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $picture1 = "";
    $picture2 = "";
    $picture3 = "";
    $picture4 = "";
    $picture5 = "";
    $result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    $result4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);
    $result5 = $stmt5->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result1 as $record1) {
        $picture1 .= "{$record1["fake_id"]}";
    }
    foreach ($result2 as $record2) {
        $picture2 .= "{$record2["fake_id"]}";
    }
    foreach ($result3 as $record3) {
        $picture3 .= "{$record3["fake_id"]}";
    }
    foreach ($result4 as $record4) {
        $picture4 .= "{$record4["fake_id"]}";
    }
    foreach ($result5 as $record5) {
        $picture5 .= "{$record5["fake_id"]}";
    }

    // $valueの参照を解除する．解除しないと，再度foreachした場合に最初からループしない
    // 今回は以降foreachしないので影響なし
    unset($value);
}

// ----------------------------------------------------------------------------------------------------
// ↓ 過去になりすました人の履歴を写真として表示する。上記で取得したfake_idを用いる

// $pictureにはfake_idの数字だけが入っている。
// その人が過去になりすました人のfake_idだけを取得
$sql1 = "SELECT image FROM users_table WHERE id = $picture1";
$sql2 = "SELECT image FROM users_table WHERE id = $picture2";
$sql3 = "SELECT image FROM users_table WHERE id = $picture3";
$sql4 = "SELECT image FROM users_table WHERE id = $picture4";
$sql5 = "SELECT image FROM users_table WHERE id = $picture5";

// SQL準備&実行
$stmt1 = $pdo->prepare($sql1);
$status1 = $stmt1->execute();

$stmt2 = $pdo->prepare($sql2);
$status2 = $stmt2->execute();

$stmt3 = $pdo->prepare($sql3);
$status3 = $stmt3->execute();

$stmt4 = $pdo->prepare($sql4);
$status4 = $stmt4->execute();

$stmt5 = $pdo->prepare($sql5);
$status5 = $stmt5->execute();


// データ登録処理後
if ($status == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
} else {
    $picture_output1 = "";
    $picture_output2 = "";
    $picture_output3 = "";
    $picture_output4 = "";
    $picture_output5 = "";

    $result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    $result4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);
    $result5 = $stmt5->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result1 as $record1) {
        $picture_output1 .= "<data value='$picture1' class='history'><img src='{$record1["image"]}' height=20px></data>";
    }
    foreach ($result2 as $record2) {
        $picture_output2 .= "<data value='$picture2' class='history'><img src='{$record2["image"]}' height=20px ></data value='$picture1'>";
    }
    foreach ($result3 as $record3) {
        $picture_output3 .= "<data value='$picture3' class='history'><img src='{$record3["image"]}' height=20px ></data value='$picture1'>";
    }
    foreach ($result4 as $record4) {
        $picture_output4 .= "<data value='$picture4' class='history'><img src='{$record4["image"]}' height=20px ></data value='$picture1'>";
    }
    foreach ($result5 as $record5) {
        $picture_output5 .= "<data value='$picture5' class='history'><img src='{$record5["image"]}' height=20px ></data value='$picture1'>";
    }

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
    <title>Narikiri_Tweet</title>
</head>

<body>
    <!-- ヘッダー -->
    <form action="create.php" method="POST" enctype="multipart/form-data">
        <header>
            <img src='asset/アイコン集/ひよこのシルエットアイコン.png' alt='' style="width:30px; height:30px;">
            <button>Tweet</button>
            <a href="Timeline.php">ホーム画面</a>
            <a href="pretend.php">なりきり一覧</a>
            <a href="todo_logout.php">ログアウト</a>
        </header>

        <!-- なりきり選択肢 -->
        <?php echo $output2 ?>
        <div id="Narikiri_wrapper">
            <?= $picture_output1 ?>
            <?= $picture_output2 ?>
            <?= $picture_output3 ?>
            <?= $picture_output4 ?>
            <?= $picture_output5 ?>
            <a href='pretend.html'><img src='asset/アイコン集/三点リーダーアイコン1.png' alt='' style="width:30px; height:30px;"></a>
        </div>
            <!-- なりすました人のID  hiddenで非表示にする予定。 -->
        <div>
            なりきりID: <input type="text" name="fake_id" id="fake_id" value="<?php echo $output ?>">
        </div>
        <div>
            <!-- 本人のID  hiddenで非表示にしている。 -->
            <input type="text" name="real_id" value='<?php echo $_SESSION["id"] ?>' hidden>
        </div>


        <!-- ツイート入力 -->
        <div>
            <img src='asset/アイコン集/f_f_object_110_s512_f_object_110_0.png' alt='' style="width:30px; height:30px;">
            <input type='textarea' style="width:200px; height: 200px;" name="tweet">
            <div>
                image: <input type="file" name="upfile" accept="image/*">
            </div>
        </div>
    </form>

    <script>
        // なりきりたいプロフィールの写真をクリックするとinputタグ(なりきりID)にvalue値が入力される
        $(".history").on('click', function() {
            var value_get = $(this).attr("value");
            $("#fake_id").val(value_get);
        });
    </script>
</body>

</html>