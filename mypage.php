<?php
// 共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　「「「「「「「「「「「「「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//==============================
// 画面処理
//==============================

// 画面表示用データ取得
//==============================
// ユーザーIDを取得
$u_id = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';

// DBからお気に入りデータを取得
$dbLikeData = getLikeData($u_id);
debug('$dbLikeDataのあたい：' . print_r($dbLikeData, true)); //OK
// DBからコメントデータを取得
$dbCommentData = getCommentData($u_id);
// debug('$dbCommentDataのあたい：' . print_r($dbCommentData, true)); //OK
?>

<?php
$pageName = 'マイページ | どうぶつの森 カード検索(仮)';
require('head.php');
?>

<body class="page_mypage">
  <!--------ヘッダーメニュー部分-------->
  <?php
  require('header.php');
  ?>
  <p id="js_show_msg" style="display:none;" class="msg_slide">
    <?php echo getSessionFlash('msg_success'); ?>
  </p>

  <!--------メインコンテンツ部分-------->
  <div class="contents container">
    <h1>マイページ</h1>
    <div class="flexbox">
      <!--------コンテンツ表示部分-------->
      <div class="main">
        <div class="section panel_list">
          <h3>最近いいねした一覧</h3>
          <?php if (!empty($dbLikeData)) { ?>
            <ul>
              <?php
              foreach ($dbLikeData as $key => $val) :
              ?>
                <li>
                  <div class="card_img">
                    <a href="cardDetail.php?c_no=<?php echo $val['card_no']; ?>" class="panel">
                      <img src="images/<?php echo $val['card_no']; ?>.png" alt="カード名" />
                    </a>
                    <i data-likecardno="<?php echo sanitize($val['card_no']); ?>" class="fas fa-heart fa-lg icn_fav js_click_like 
                    <?php if (isLike($_SESSION['user_id'], $val['card_no'])) {
                      echo 'active';
                    } ?>">
                    </i>
                  </div>
                </li>
              <?php
              endforeach;
              ?>
            </ul>
          <?php } else { ?>
            <p>最近「いいね」したカードがここに表示されます</p>
          <?php } ?>
        </div>

        <div class="section comment_list">
          <h3>最近コメントした一覧</h3>
          <table class="table">
            <thead>
              <tr>
                <th class="c_day">日付</th>
                <th class="c_char">名前</th>
                <th class="c_pac">収録弾</th>
                <th>コメント内容</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($dbCommentData)) { ?>

                <?php foreach ($dbCommentData as $key => $val) : ?>

                  <tr>
                    <td><?php echo mb_substr($val['update_date'], 2, 2); ?>年<?php echo mb_substr($val['update_date'], 5, 2); ?>月<?php echo mb_substr($val['update_date'], 8, 2); ?>日</td>
                    <td><a href="cardDetail.php?c_no=<?php echo $val['card_no'] ?>"><?php echo $val['name']; ?></a></td>
                    <td><?php echo $val['booster_name']; ?></td>
                    <td><?php echo mb_substr($val['comment'], 0, 17); ?><?php echo (mb_strlen($val['comment']) > 17) ? '...' : '' ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php } else { ?>
                <tr>
                  <td colspan="4" class="no_comment">最近コメントしたカードがここに表示されます</td>
                </tr>
              <?php } ?>

            </tbody>
          </table>
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