<?php
// 共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　カード詳細ページ　「「「「「「「「「「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();




//==============================
// 画面処理
//==============================

// 画面表示用データ取得
//==============================
// ユーザーIDを取得
$u_id = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';
// debug('$u_idのなかみ：' . print_r($u_id, true)); //OK
// カードNoのGETパラメータ取得
$c_no = (!empty($_GET['c_no'])) ? $_GET['c_no'] : '';
// DBからカードデータを取得
$viewData = getCardData($c_no, $u_id);
// DBからブースターデータを取得
$dbBoosterData = getBooster();
// DBから種族データを取得
$dbTypesData = getTypes();
// DBから性格データを取得
$dbPersonalData = getPersonal();

// 新規登録画面か編集画面か判定用フラグ
$edit_flg = (!empty($viewData['fav_id'])) ? true : false;

// パラメータに不正な値が入っているかチェック
if (empty($viewData)) {
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:index.php");
}
// debug('取得したDBデータ($viewDataのなかみ)：' . print_r($viewData, true));

// POST送信されていた場合、かつメモ（コメント）があった場合
if (!empty($_POST) && $_POST['comment']) {
  debug('POST送信があります(CardDetail.php)');
  debug('POST情報($_POST)：' . print_r($_POST, true));

  // 変数にPOST情報を代入
  $comment = $_POST['comment'];

  // DBの情報と入力情報が異なる場合、バリデーションチェックを行う
  if ($viewData['comment'] !== $comment) {
    debug('内容が編集されました、各種バリデーションチェックを行います');
    // 必要なバリデーションチェックを追加すること！
  }

  if (empty($err_msg)) {
    debug('バリデーションOKです');
    // 例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      if ($edit_flg) {
        // ①更新の場合
        if (!empty($_POST['comment'])) {
          // post送信に内容がある場合
          debug('post送信は内容あるので、更新します');
          $sql = 'UPDATE favorite SET comment = :comment WHERE fav_id = :fav_id';
          $data = array(':comment' => $comment, ':fav_id' => $viewData['fav_id']);
        } else {
          // post送信に内容がない場合
          debug('post送信は空です、レコードを削除します');
          $sql = 'DELETE FROM favorite WHERE fav_id = :fav_id';
          $data = array(':fav_id' => $viewData['fav_id']);
        }
      } else {
        // ②新規登録の場合
        $sql = 'INSERT INTO favorite (user_id, fav_card_no, comment, create_date, update_date)
                VALUES (:user_id, :fav_card_no, :comment, :create_date, :update_date)';
        $data = array(
          ':user_id' => $u_id,
          ':fav_card_no' => $c_no,
          ':comment' => $comment,
          ':create_date' => date('Y-m-d H:i:s'),
          ':update_date' => date('Y-m-d H:i:s')
        );
      }

      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if ($stmt) {
        $_SESSION['msg_success'] = SUC05;
        debug('$_POSTの中身を削除し、カード詳細ページへ遷移します');
        $_POST = array();
        debug('$_SESSIONのなかみ：' . print_r($_SESSION, true));
        debug('$_SERVERのなかみ：' . print_r($_SERVER, true));
        header("Location:" . $_SERVER['PHP_SELF'] . appendGetParam()); // 自分自身に遷移する
        return;
      }
    } catch (Exception $e) {
      error_log('エラー発生：' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}

?>
<?php
$pageName = 'カード詳細ページ | どうぶつの森 カード検索(仮)';
require('head.php');
?>

<body class="page_cardDetail">
  <!--------ヘッダーメニュー部分-------->
  <?php
  require('header.php');
  ?>
  <p id="js_show_msg" style="display:none;" class="msg_slide">
    <?php echo getSessionFlash('msg_success'); ?>
  </p>

  <!--------メインコンテンツ部分-------->
  <div class="contents container">
    <div class="flexbox">
      <div class="main">
        <div class="card_box">
          <div class="card_img_box">
            <img src="images/<?php echo $viewData['card_no']; ?>.png" />
          </div>
          <div class="card_info_box">
            <table>
              <tbody>
                <tr>
                  <th>カードNo.</th>
                  <th>キャラクター名</th>
                </tr>
                <tr>
                  <td><?php echo $viewData['card_no']; ?></td>
                  <td><?php echo $viewData['name']; ?></td>
                </tr>
                <tr>
                  <th>種族</th>
                  <th>誕生日</th>
                </tr>
                <tr>
                  <td><?php echo $viewData['type_name']; ?></td>
                  <td><?php echo $viewData['birth_m']; ?>月<?php echo $viewData['birth_d']; ?>日</td>
                </tr>
                <tr>
                  <th>性格</th>
                  <th>くちぐせ</th>
                </tr>
                <tr>
                  <td><?php echo $viewData['personal_name']; ?></td>
                  <td><?php echo $viewData['habit']; ?></td>
                </tr>
                <tr>
                  <th colspan="2">座右の銘</th>
                </tr>
                <tr>
                  <td colspan="2"><?php echo $viewData['motto']; ?></td>
                </tr>
                <tr>
                  <th colspan="2">収録パック</th>
                </tr>
                <tr>
                  <td colspan="2"><?php echo $viewData['booster_name']; ?></td>
                </tr>
              </tbody>
            </table>

            <?php if (!empty($_SESSION['user_id'])) { ?>
              <form action="" method="post">
                <textarea name="comment" placeholder="個人用メモ(公開されません)"><?php echo $viewData['comment']; ?></textarea>
                <div class="card_info_bottom">
                  <div class="icons">
                    <i data-likecardno="<?php echo sanitize($viewData['card_no']); ?>" class="fas fa-heart fa-2x icn_fav js_click_like 
                    <?php if (isLike($u_id, $c_no)) {
                      echo 'active';
                    } ?>"></i>
                  </div>
                  <input type="submit" name="submit" onclick="next_page()" value="メモを保存する" />
                </div>
              </form>
            <?php } else { ?>
              <div class="no_login_msg">
                <p>ログインするとお気に入り機能やコメント機能が使えます</p>
              </div>
            <?php } ?>
          </div>
        </div>
        <div class="page_back">
          <a href="index.php<?php echo appendGetParam(array('c_no')); ?>">&lt; 一覧に戻る</a>
        </div>
      </div>
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