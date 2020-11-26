<?php
// 共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集ページ　「「「「「「「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//==============================
// 画面処理
//==============================
// DBからユーザー情報を取得
$userData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：' . print_r($userData, true));

//post送信されていた場合
if (!empty($_POST)) {
  debug('POST送信があります');
  debug('POST情報：' . print_r($_POST, true));

  //変数にユーザー情報を代入
  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];

  //未入力チェック
  validRequired($pass_old, 'pass_old');
  validRequired($pass_new, 'pass_new');
  validRequired($pass_new_re, 'pass_new_re');

  if (empty($err_msg)) {
    debug('未入力チェックOK');

    //古いパスワードのチェック
    validPass($pass_old, 'pass_old');
    //新しいパスワードのチェック
    validPass($pass_new, 'pass_new');

    //古いパスワードとDBのパスワードが一致するかチェック
    if (!password_verify($pass_old, $userData['password'])) {
      $err_msg['pass_old'] = MSG10;
    }

    //新旧パスワードが一致するかチェック
    if ($pass_old === $pass_new) {
      $err_msg['pass_new'] = MSG11;
    }

    //新パスワードと再入力が一致するかチェック
    validMatch($pass_new, $pass_new_re, 'pass_new_re');

    if (empty($err_msg)) {
      debug('バリデーションチェックOK');

      //例外処理
      try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'UPDATE users SET password = :pass WHERE id = :id';
        $data = array(':id' => $_SESSION['user_id'], ':pass' => password_hash($pass_new, PASSWORD_DEFAULT));
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        if ($stmt) {
          $_SESSION['msg_success'] = SUC01;

          //メールを送信
          $username = ($userData['username']) ? $userData['username'] : '名無し';
          $from = 'info@webukatu.com';
          $to = $userData['email'];
          $subject = 'パスワード変更のお知らせ';
          $comment = <<<EOT
{$username} さん
パスワードが変更されました。
よろしく！
EOT;
          sendMail($from, $to, $subject, $comment);
          debug('マイページへ遷移します。');
          header("Location:mypage.php"); //マイページへ
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
$pageName = 'パスワード変更 | どうぶつの森 カード検索(仮)';
require('head.php');
?>

<body class="page_passEdit">
  <!--------ヘッダーメニュー部分-------->
  <?php
  require('header.php');
  ?>

  <!--------メインコンテンツ部分-------->
  <div class="contents container">
    <div class="flexbox">

      <section id="main" class="main">
        <form action="" method="post" class="form">
          <h2 class="title">パスワード変更</h2>
          <label class="<?php if (!empty($err_msg['pass_old'])) echo 'err'; ?>">
            古いパスワード
            <input type="password" name="pass_old" value="<?php echo getFormData('pass_old'); ?>" autofocus>
          </label>
          <div class="area_msg" style="font-size: 14px">
            <?php
            echo getErrMsg('pass_old');
            ?>
          </div>
          <label class="<?php if (!empty($err_msg['pass_new'])) echo 'err'; ?>">
            新しいパスワード<span style="font-size: 12px">※半角英数字6文字以上</span>
            <input type="password" name="pass_new" value="<?php echo getFormData('pass_new'); ?>">
          </label>
          <div class="area_msg" style="font-size: 14px">
            <?php
            echo getErrMsg('pass_new');
            ?>
          </div>
          <label class="<?php if (!empty($err_msg['pass_new_re'])) echo 'err'; ?>">
            新しいパスワード(再入力)
            <input type="password" name="pass_new_re" value="<?php echo getFormData('pass_new_re'); ?>">
          </label>
          <div class="area_msg" style="font-size: 14px">
            <?php
            echo getErrMsg('pass_new_re');
            ?>

          </div>
          <div class="btn_container">
            <input type="submit" class="btn_mid" value="変更する" />
          </div>
        </form>
      </section>
      <!--------サイドバー部分-------->
      <?php
      require('sidebar_right.php');
      ?>

    </div>
  </div>
  <!--------フッター部分-------->
  <?php
  require('footer.php');
  ?>