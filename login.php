<?php
// 共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログインページ　「「「「「「「「「「「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//==============================
// 画面処理
//==============================
// post送信されていた場合
if (!empty($_POST)) {
  debug('post送信があります。');

  //変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;

  //emailの形式チェック
  validEmail($email, 'email');
  //emailの最大文字数チェック
  validMaxLen($email, 'email');

  //パスワードの半角英数字チェック
  validHalf($pass, 'pass');
  //パスワードの最大文字数チェック
  validMaxLen($pass, 'pass');
  //パスワードの最小文字数チェック
  validMinLen($pass, 'pass');

  //未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');

  if (empty($err_msg)) {
    debug('バリデーションチェックOKです。');

    //例外処理
    try {
      //DB接続
      $dbh = dbConnect();
      //SQL文作成
      $sql = 'SELECT password, id FROM users WHERE email = :email AND delete_flg = 0';
      $data = array(':email' => $email);
      //クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
      //クエリ結果の値を取得
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      debug('クエリ結果の中身：' . print_r($result, true));

      //パスワード照合
      if (!empty($result) && password_verify($pass, array_shift($result))) {
        debug('パスワードが一致しました。');

        //ログイン有効期限（デフォルト：60分）
        $sesLimit = 60 * 60;
        //最終ログイン日時を現在の時刻に更新
        $_SESSION['login_date'] = time();

        //ログイン保持欄にチェックがある場合
        if ($pass_save) {
          debug('ログイン保持欄にチェックがあります');
          //ログイン有効期限を30日に延長
          $_SESSION['login_limit'] = $sesLimit * 24 * 30;
        } else {
          debug('ログイン保持欄にチェックはありません');
          //ログイン有効期限を60分に設定
          $_SESSION['login_limit'] = $sesLimit;
        }
        //ユーザーIDを格納
        $_SESSION['user_id'] = $result['id'];

        debug('セッション変数の中身：' . print_r($_SESSION, true));
        debug('マイページへ遷移します。');
        header("Location:mypage.php"); //マイページへ
      } else {
        debug('パスワードが一致しません。');
        $err_msg['common'] = MSG09;
      }
    } catch (Exception $e) {
      error_log('エラー発生：' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>');
debug('<<<<<<<<<<画面表示処理終了>>>>>>>>>>');
debug('<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>');
?>

<?php
$pageName = 'ログイン | どうぶつの森 カード検索(仮)';
require('head.php');
?>

<body class="page_login">
  <!--------ヘッダーメニュー部分-------->
  <?php
  require('header.php');
  ?>
  <p id="js_show_msg" style="display:none;" class="msg_slide">
    <?php echo getSessionFlash('msg_success'); ?>
  </p>

  <!--------メインコンテンツ部分-------->
  <div class="contents container">
    <section id="main">
      <form action="" method="post" class="form">
        <h2 class="title">ログイン</h2>
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
        <label class="<?php if (!empty($err_msg['pass'])) echo 'err' ?>">
          パスワード
          <input type="password" name="pass" value='<?php if (!empty($_POST['pass'])) echo $_POST['pass']; ?>'>
        </label>
        <div class="area_msg">
          <?php
          if (!empty($err_msg['pass'])) echo $err_msg['pass'];
          ?>
        </div>
        <label>
          <input type="checkbox" name="pass_save">ログインしたままにする
        </label>
        <div class="btn_container">
          <input type="submit" class="btn_mid" value="ログイン" />
        </div>
        パスワードを忘れた方は<a href="passRemindSend.php">コチラ</a>
      </form>
    </section>
  </div>

  <!--------フッター部分-------->
  <?php
  require('footer.php');
  ?>