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

    <title>Narikiri_Register</title>
</head>

<body>
    <legend>なりきり新規登録</legend>
    <img src='asset/アイコン集/ひよこのシルエットアイコン.png' alt='' style="width:30px; height:30px;">
    <form action="register_act.php" method="POST">
        <div>
            <input type="text" name="user_id" placeholder="User ID">
        </div>
        <div>
            <input type="password" name="password" placeholder="Password">
        </div>
        <div>
            <input type="text" name="password" placeholder="Password(確認)">
        </div>

        <div>
            <button>Register</button>
        </div>
        <a href="login.php">Login</a>


</body>

</html>