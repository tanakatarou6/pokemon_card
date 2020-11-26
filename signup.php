<?php

require('function.php');

//post送信されていた場合
if (!empty($_POST)) {
  debug('POST情報(signup.php)：' . print_r($_POST, true));

  //変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];

  //未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($pass_re, 'pass_re');

  if (empty($err_msg)) {
    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    //emailの重複チェック
    validEmailDup($email);

    //パスワードの半角英数字チェック
    validHalf($pass, 'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass, 'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass, 'pass');

    if (empty($err_msg)) {
      //パスワードとパスワード（再入力）が一致しているかチェック
      validMatch($pass, $pass_re, 'pass_re');

      if (empty($err_msg)) {
        //例外処理
        try {
          // DBへ接続
          $dbh = dbConnect();
          // SQL文作成
          $sql = 'INSERT INTO users (email, password, login_time, create_date)
                  VALUES(:email, :pass, :login_time, :create_date)';
          $data = array(
            ':email' => $email,
            ':pass' => password_hash($pass, PASSWORD_DEFAULT),
            ':login_time' => date('Y-m-d H:i:s'),
            ':create_date' => date('Y-m-d H:i:s')
          );

          // クエリ実行
          $stmt = queryPost($dbh, $sql, $data);

          // クエリ成功の場合(セッションを保管しログイン状態にする)
          if ($stmt) {
            // ログイン有効期限(60分に変更)
            $sesLimit = 60 * 60;
            // 最終ログイン日時を現在日時に変更
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            // ユーザーIDを格納
            $_SESSION['user_id'] = $dbh->lastInsertId();

            debug('新規登録後のセッション変数の中身' . print_r($_SESSION, true));

            header("Location:profEdit.php"); //プロフィール登録ページへ
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
$pageName = '新規登録 | どうぶつの森 カード検索(仮)';
require('head.php');
?>

<body class="page_signup">
  <!--------ヘッダーメニュー部分-------->
  <?php
  require('header.php');
  ?>

  <!--------メインコンテンツ部分-------->
  <div class="contents container">
    <section id="main">
      <form action="" method="post" class="form">
        <h2 class="title">新規ユーザー登録</h2>
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
          if (!empty($err_msg['email'])) echo $err_msg['email']; ?>
        </div>
        <label class="<?php if (!empty($err_msg['pass'])) echo 'err'; ?>">
          パスワード<span style="font-size: 12px">※半角英数字6文字以上</span>
          <input type="password" name="pass" value="<?php if (!empty($_POST['pass'])) echo $_POST['pass']; ?>">
        </label>
        <div class="area_msg">
          <?php
          if (!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
        </div>
        <label class="<?php if (!empty($err_msg['pass_re'])) echo 'err'; ?>">
          パスワード(再入力)
          <input type="text" name="pass_re" value="<?php if (!empty($_POST['pass_re'])) echo $_POST['pass_re'] ?>">
        </label>
        <div class="area_msg">
          <?php
          if (!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?>
        </div>

        <div class="btn_container">
          <input type="submit" class="btn_mid" value="登録する" />
        </div>
      </form>
    </section>
  </div>

  <!--------フッター部分-------->
  <?php
  require('footer.php');
  ?>