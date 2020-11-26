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
$dbFormData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報($dbFormData)：' . print_r($dbFormData, true));
debug('$_SESSION[login_date]のなかみ：' . print_r($_SESSION['login_date'], true));
debug('$_SESSION[login_limit]のなかみ：' . print_r($_SESSION['login_limit'], true));
debug('$_SESSION[user_id]のなかみ：' . print_r($_SESSION['user_id'], true));

// 画面表示用データ取得
//==============================
// 新規登録画面か編集画面か判定用フラグ
$edit_flg = (empty($dbFormData['username'])) ? false : true;
debug('$edit_flgのなかみ：' . print_r($edit_flg, true));
debug('$dbFormData[username]のなかみ：' . print_r($dbFormData['username'], true));


// POST送信時処理
//==============================
// post送信されていた場合
if (!empty($_POST)) {
  debug('POST送信があります($profEdit.php)');
  debug('POST情報($_POST)：' . print_r($_POST, true));
  debug('FILE情報($_FILES)：' . print_r($_FILES, true));

  //変数にユーザー情報を代入
  $username = $_POST['username'];
  $nickname = $_POST['nickname'];
  $email = $_POST['email'];

  if ($edit_flg) {
    // ①更新の場合
    //各項目について、DBの情報と入力情報が異なる場合にバリデーションチェックを行う
    if ($dbFormData['username'] !== $username) {
      //名前の未入力チェック
      validRequired($username, 'username');
      //名前の最大文字数チェック
      validMaxLen($username, 'username');
    }
    if ($dbFormData['nickname'] !== $nickname) {
      //ニックネームの最大文字数チェック
      validMaxLen($nickname, 'nickname');
    }
    if ($dbFormData['email'] !== $email) {
      //emailの最大文字数チェック
      validMaxLen($email, 'email');
      //emailの重複チェック
      validEmailDup($email, 'email');
      //emailの形式チェック
      validEmail($email, 'email');
      //emailの未入力チェック
      validRequired($email, 'email');
    }
  } else {
    // ②新規登録の場合
    //名前の未入力チェック
    validRequired($username, 'username');
    //emailの未入力チェック
    validRequired($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    //emailの形式チェック
    validEmail($email, 'email');
  }

  if (empty($err_msg)) {
    debug('バリデーションOKです');
    //画像をアップロードし、パスを格納
    $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
    //画像をPOSTしていない（登録していない）が既にDBに登録されている場合、DBのパスを入れる（POSTには反映されないので）
    $pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;

    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'UPDATE users 
              SET username = :u_name, nickname = :n_name, email = :email, pic = :pic
              WHERE id = :u_id';
      $data = array(':u_name' => $username, ':n_name' => $nickname, ':email' => $email, ':pic' => $pic, ':u_id' => $dbFormData['id']);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if ($stmt) {

        if (empty($edit_flg)) {
          $_SESSION['msg_success'] = SUC04;
          return;
        } else {
          $_SESSION['msg_success'] = SUC02;
          return;
        }
        debug('マイページへ遷移します');
        header("Location:mypage.php"); //マイページへ
      }
    } catch (Exception $e) {
      error_log('エラー発生' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}

debug('<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>');
debug('<<<<<<<<<<画面表示処理終了>>>>>>>>>>');
debug('<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>');

?>

<?php
$pageName = 'プロフィール編集 | どうぶつの森 カード検索(仮)';
require('head.php');
?>

<body class="page_profEdit">
  <!--------ヘッダーメニュー部分-------->
  <?php
  require('header.php');
  ?>

  <!--------メインコンテンツ部分-------->
  <div class="contents container">
    <div class="flexbox">
      <section id="main" class="main">
        <form action="" method="post" class="form" enctype="multipart/form-data">
          <h2 class="title"><?php echo (!$edit_flg) ? 'プロフィール登録' : 'プロフィール編集' ?></h2>
          <div class="flexbox">
            <div class="prof_left">
              アイコン画像
              <label class="area_drop <?php if (!empty($err_msg['pic'])) echo 'err'; ?>">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic" class="input_file">
                <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev_img" style="<?php if (empty(getFormData('pic'))) echo 'display: none;' ?>">
                ここに画像を<br>ドラッグ&ドロップ
              </label>
              <div class="area_msg">
                <?php
                if (!empty($err_msg['pic'])) echo $err_msg['pic'];
                ?>
              </div>
            </div>
            <div class="prof_right">
              <label class="<?php if (!empty($err_msg['username'])) echo 'err'; ?>">
                氏名(公開されません)
                <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
              </label>
              <div class="area_msg" style="font-size: 14px">
                <?php
                if (!empty($err_msg['username'])) echo $err_msg['username'];
                ?>
              </div>
              <label class="<?php if (!empty($err_msg['nickname'])) echo 'err'; ?>">
                ニックネーム(公開される名前)
                <input type="text" name="nickname" value="<?php echo getFormData('nickname'); ?>">
              </label>
              <div class="area_msg" style="font-size: 14px">
                <?php
                if (!empty($err_msg['nickname'])) echo $err_msg['nickname'];
                ?>
              </div>
              <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
                Email<?php if (empty($edit_flg)) echo '(変更不可)' ?>
                <input type="text" name="email" value="<?php echo getFormData('email'); ?>" <?php if (empty($edit_flg)) echo 'readonly' ?>>
              </label>
              <div class="area_msg" style="font-size: 14px">
                <?php
                if (!empty($err_msg['email'])) echo $err_msg['email'];
                ?>
              </div>
              <div class="btn_container">
                <input type="submit" class="btn_mid" value="<?php echo (!$edit_flg) ? '登録' : '変更する' ?>" />
              </div>
            </div>
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