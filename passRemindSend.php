<?php
// 共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再発行メール送信ページ　「「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
// require('auth.php');
// ログイン認証はナシ（ログインできない人が使う画面なので）

//==============================
// 画面処理
//==============================
// post送信されていた場合
if (!empty($_POST)) {
    debug('POST送信があります。');
    debug('POST情報：' . print_r($_POST, true));

    //変数にPOST情報を代入
    $email = $_POST['email'];

    //未入力チェック
    validRequired($email, 'email');

    if (empty($err_msg)) {
        debug('未入力チェックOK');

        //emailの形式チェック
        validEmail($email, 'email');
        //emailの最大文字数チェック
        validMaxLen($email, 'email');

        if (empty($err_msg)) {
            debug('バリデーションOK');

            //例外処理
            try {
                // DBへ接続
                $dbh = dbConnect();
                // SQL文作成
                $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
                $data = array(':email' => $email);
                // クエリ実行
                $stmt = queryPost($dbh, $sql, $data);
                // クエリ結果の値を取得
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                //EmailがDBに登録されている場合
                if ($stmt && array_shift($result)) {
                    debug('クエリ成功。DBに登録あり。');
                    $_SESSION['msg_success'] = SUC03;

                    $auth_key = makeRandKey(); //認証キー生成

                    //メールを送信
                    $from = 'info@webukatu.com';
                    $to = $email;
                    $subject = '【パスワード再発行認証】';
                    $comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記URLにて認証キーをご入力いただくとパスワードが再発行されます。

パスワード再発行認証キー入力ページ： http://localhost:8888/ws_op/passRemindRecieve.php
認証キー：{$auth_key}
＊認証キーの有効期限は30分です。

認証キーの再発行は下記ページよりお願いいたします。
http://localhost:8888/ws_op/passRemindSend.php
EOT;

                    sendMail($from, $to, $subject, $comment);

                    //認証に必要な情報をセッションへ保存
                    $_SESSION['auth_key'] = $auth_key;
                    $_SESSION['auth_email'] = $email;
                    $_SESSION['auth_key_limit'] = time() + (60 * 30); //現在時刻より30分後のUNIXタイムスタンプを入れる
                    debug('セッション変数の中身：' . print_r($_SESSION, true));

                    header("Location:passRemindRecieve.php"); //認証キー入力ページへ
                } else {
                    debug('クエリに失敗 or DBに登録のないEmailが入力されました');
                    $err_msg['common'] = MSG07;
                }
            } catch (Exception $e) {
                error_log('エラー発生：' . $e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
    }
}
?>

<?php
$pageName = 'パスワード再発行 | どうぶつの森 カード検索(仮)';
require('head.php');
?>

<body class="page_passRemindSend">
    <!--------ヘッダーメニュー部分-------->
    <?php
    require('header.php');
    ?>

    <!--------メインコンテンツ部分-------->
    <div class="contents container">
        <section id="main">
            <form action="" method="post" class="form">
                <p>ご入力いただいたメールアドレス宛にパスワード再発行用のURLと認証キーをお送りします。</p>
                <div class="area_msg">
                    <?php
                    if (!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
                    メールアドレス
                    <input type="text" name="email" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>" autofocus>
                </label>
                <div class="area_msg">
                    <?php
                    if (!empty($err_msg['email'])) echo $err_msg['email'];
                    ?>
                </div>
                <div class="btn_container">
                    <input type="submit" class="btn_mid" value="送信する" />
                </div>
            </form>
            <a href="login.php">&lt; ログインページに戻る</a>
        </section>
    </div>

    <!--------フッター部分-------->
    <?php
    require('footer.php');
    ?>