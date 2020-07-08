<?php
session_start();
include("functions.php");
check_session_id();

// ユーザ名取得
$user_id = $_SESSION['id'];

// DB接続
$pdo = connect_to_db();

// いいね数カウント
$sql = 'SELECT like_id, COUNT(id) AS cnt FROM like_table GROUP BY like_id';

$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

if ($status == false) {
    //授業後に付けた内容
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
} else {
    $like_count = $stmt->fetchAll(PDO::FETCH_ASSOC); // fetchAllで全件取得

}

//  ↑「いいね」数カウント用
// ----------------------------------------------------------------------------------------------------

// $sql = "SELECT * FROM tweet_table INNER JOIN users_table ON tweet_table.fake_id = users_table.id";

// $stmt = $pdo->prepare($sql);
// $status = $stmt->execute();

// if ($status == false) {
//     $error = $stmt->errorInfo();
//     echo json_encode(["error_msg" => "{$error[2]}"]);
//     exit();
// } else {
//     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);  
//     $fake_pictures = "";

//     foreach ($result as $record) {
//         $fake_pictures .= "<td><img src='{$record["image"]}' height=20px></td>";
//     }

//     unset($value);
// }

// ----------------------------------------------------------------------------------------------------
// ↓ like_tableとtweet_table結合

// データ取得SQL作成
$sql = 'SELECT * FROM tweet_table LEFT OUTER JOIN(SELECT like_id, COUNT(id) AS cnt FROM like_table GROUP BY like_id) AS likes ON tweet_table.id = likes.like_id WHERE NOT real_id = 0';
// ※WHERE NOT real_id = 0 をつける理由
//   なり変わり一覧(pretend.php)からツイート画面(tweet.php)にfake_idを飛ばす時fake_id以外の値は0を入れている。
//   Timelineページにもその値が表示されてしまうのを防ぐ為にWHERE NOT real_id = 0で非表示にする。

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
    // $like_count = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);  // データの出力用変数（初期値は空文字）を設定
    $output = "";
    // <tr><td>tweet</td><td>todo</td><tr>の形になるようにforeachで順番に$outputへデータを追加
    // `.=`は後ろに文字列を追加する，の意味

    foreach ($result as $record) {
        $output .= "<tr>";
        $output .= "<td>{$record["tweet"]}</td>";
        $output .= "<td class='real_profile'>{$record["real_id"]}</td>";
        $output .= "<td class='fake_profile'>{$record["fake_id"]}</td>";
        $output .= "<td><img src='{$record["image"]}' height=150px></td>";
        // deleteリンクを追加
        $output .= "<td><a href='like_create.php?user_id={$user_id}&like_id={$record["id"]}'>like{$record["cnt"]}</a></td>";
        $output .= "<td><a href='delete.php?id={$record["id"]}'>delete</a></td>";
        $output .= "</tr>";
    }
    unset($value);
}

// ----------------------------------------------------------------------------------------------------


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

    <title>Narikiri_Timeline</title>
</head>

<body>
    <!-- ヘッダー -->
    <header>
        <img src='asset/アイコン集/f_f_object_110_s512_f_object_110_0.png' alt='' style="width:30px; height:30px;">
        <label>
            <input type="radio" name="rdo" value="v1" id="fake_btn">なりきり
        </label>
        <label>
            <input type="radio" name="rdo" value="v2" id="real_btn">ほんとう
        </label>
        <img src='asset/アイコン集/ひよこのシルエットアイコン.png' alt='' style="width:30px; height:30px;">
    </header>

    <!-- タイムライン -->

    <div id="timeline_wrapper">
        <div id="tweet">
            <fieldset>
                <legend>ホーム画面</legend>
                <a href="tweet.php">ツイート画面</a>
                <a href="Edit.php">プロフィール画面</a>
                <a href="todo_logout.php">logout</a>
                <table>
                    <thead>
                        <tr>
                            <th>tweet</th>
                            <th>プロフィール写真</th>
                            <th>img</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?= $output ?>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>


    <!-- フッター -->
    <footer>
        <img src='asset/アイコン集/ホームアイコン.png' alt='' style="width:30px; height:30px;">
        <img src='asset/アイコン集/クリスマスベルの無料アイコン2.png' alt='' style="width:30px; height:30px;">
        <img src='asset/アイコン集/ブログを書くアイコン.png' alt='' style="width:30px; height:30px;">
    </footer>

    <script>
        // 通常は なりきりツイート画面を表示
        $(".real_profile").hide();

        // 「ほんとう」のradioボタンクリック→本人の名前、プロフィール写真が表示
        $("#real_btn").on('click', function() {
            $(".fake_profile").hide();
            $(".real_profile").show();
        });

        // 「なりきり」のradioボタンクリック→なりきった人の名前、プロフィール写真が表示
        $("#fake_btn").on('click', function() {
            $(".real_profile").hide();
            $(".fake_profile").show();
        });
    </script>
</body>

</html>