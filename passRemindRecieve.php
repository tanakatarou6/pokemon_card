<?php
// 共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再発行認証ページ　「「「「「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
// require('auth.php');
// ログイン認証はナシ（ログインできない人が使う画面なので）

//SESSIONに認証キーがあるか確認、なければリダイレクト
if (empty($_SESSION['auth_key'])) {
    header("Location:passRemindsSend.php"); //認証キー送信ページへ
}

//==============================
// 画面処理
//==============================
//post送信されていた場合
if (!empty($_POST)) {
    debug('POST送信があります');
    debug('POST情報：' . print_r($_POST, true));

    //変数に認証キーを代入
    $auth_key = $_POST['token'];

    //未入力チェック
    validRequired($auth_key, 'token');

    if (empty($err_msg)) {
        debug('未入力チェックOK');

        //固定長チェック
        validLength($auth_key, 'token');
        //半角チェック
        validHalf($auth_key, 'token');

        if (empty($err_msg)) {
            debug('バリデーションOK');

            //認証キーが一致しているかチェック
            if ($auth_key !== $_SESSION['auth_key']) {
                $err_msg['common'] = MSG13;
            }
            //認証キーがタイムアウトしていないかチェック
            if (time() > $_SESSION['auth_key_limit']) {
                $err_msg['common'] = MSG14;
            }

            if (empty($err_msg)) {
                debug('認証キーOK');

                $pass = makeRandKey(); //パスワード生成

                //例外処理
                try {
                    // DBへ接続
                    $dbh = dbConnect();
                    // SQL文作成
                    $sql = 'UPDATE users SET password = :pass WHERE email = :email AND delete_flg = 0';
                    $data = array(':email' => $_SESSION['auth_email'], ':pass' => password_hash($pass, PASSWORD_DEFAULT));
                    // クエリ実行
                    $stmt = queryPost($dbh, $sql, $data);

                    // クエリ成功の場合
                    if ($stmt) {
                        debug('クエリ成功');

                        //メールを送信
                        $from = 'info@webukatu.com';
                        $to = $_SESSION['auth_email'];
                        $subject = '【パスワード再発行完了】';
                        $comment = <<<EOT
本メールアドレス宛にパスワード再発行をいたしました。
下記URLにて再発行パスワードを入力してログインしてください。

ログインページ： http://localhost:8888/ws_op/login.php
再発行パスワード：{$pass}
＊ログイン後、パスワードを変更してください。
EOT;
                        sendMail($from, $to, $subject, $comment);

                        //セッション削除
                        session_unset();
                        $_SESSION['msg_success'] = SUC03;
                        debug('セッション変数の中身：' . print_r($_SESSION, true));

                        header("Location:login.php"); //ログインページへ
                    } else {
                        debug('クエリに失敗しました');
                        $err_msg['common'] = MSG07;
                    }
                } catch (Exception $e) {
                    error_log('エラー発生：' . $e->getMessage());
                    $err_msg['common'] = MSG07;
                }
            }
        }
    }
}
?>

<?php
$pageName = 'パスワード再発行キー認証 | どうぶつの森 カード検索(仮)';
require('head.php');
?>

<body class="page_passRemaindRecieve">
    <!--------ヘッダーメニュー部分-------->
    <?php
    require('header.php');
    ?>

    <!--------メインコンテンツ部分-------->
    <div class="contents container">
        <section id="main" class="main">
            <form action="" method="post" class="form">
                <p>ご指定のメールアドレスにお送りした【パスワード再発行認証】メールにある「認証キー」をご入力ください。</p>
                <div class="area_msg">
                    <?php if (!empty($err_msg['common'])) echo $err_msg['common']; ?>
                </div>
                <label class="<?php if (!empty($err_msg['token'])) echo 'err'; ?>">
                    認証キー
                    <input type="text" name="token" value="<?php echo getFormData('token'); ?>" autofocus>
                </label>
                <div class="area_msg" style="font-size: 14px">
                    <?php
                    echo getErrMsg('token');
                    ?>
                </div>
                <div class="btn_container">
                    <input type="submit" class="btn_mid" value="再発行する" />
                </div>
            </form>
        </section>
    </div>
    <!--------フッター部分-------->
    <?php
    require('footer.php');
    ?>