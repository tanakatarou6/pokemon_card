<?php
// 共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　退会ページ　「「「「「「「「「「「「「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//==============================
// 画面処理
//==============================

debug('$_POSTのなかみ：' . print_r($_POST, true));
// post送信されていた場合
if (!empty($_POST) && !empty($_POST['withdraw'])) {
  debug('POST送信があります。');
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id';
    $sql2 = 'UPDATE favorite SET delete_flg = 1 WHERE user_id = :us_id';
    // データ流し込み
    $data = array(':us_id' => $_SESSION['user_id']);
    // クエリ実行
    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);

    // クエリ実行成功の場合
    if ($stmt1 && $stmt2) {
      // セッション削除
      session_destroy();
      debug('セッション変数の中身(ページ遷移で消去されます)：' . print_r($_SESSION, true));
      debug('トップページへ遷移します');
      header("Location:index.php");
    } else {
      debug('クエリが失敗しました。');
      $err_msg['common'] = MSG07;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
} elseif (!empty($_POST)) {
  $err_msg['common'] = MSG15;
}
debug('<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>');
debug('<<<<<<<<<<画面表示処理終了>>>>>>>>>>');
debug('<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>');
?>
<?php
$pageName = '退会 | どうぶつの森 カード検索(仮)';
require('head.php')
?>

<body class="page_withdraw">
  <!--------ヘッダーメニュー部分-------->
  <?php
  require('header.php');
  ?>

  <!--------メインコンテンツ部分-------->
  <div class="contents container">
    <section id="main">
      <form action="" method="post" class="form">
        <h2 class="title">退会</h2>
        <p>退会するとアカウント情報は元に戻せません</p>
        <label>
          <input type="checkbox" name="withdraw" />情報を削除し退会する
        </label>
        </label>
        <div class="area_msg" style="font-size: 14px">
          <?php
          if (!empty($err_msg['common'])) echo $err_msg['common'];
          ?>
        </div>
        <div class="btn_container">
          <input type="submit" class="btn_mid" value="退会する" name="submit">
        </div>
      </form>
      <a href="mypage.php">&lt; マイページに戻る</a>
    </section>
  </div>

  <!--------フッター部分-------->
  <?php
  require('footer.php');
  ?>