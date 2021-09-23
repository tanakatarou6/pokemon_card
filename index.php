<?php
// 共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　トップページ　「「「「「「「「「「「「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();




//==============================
// 画面処理
//==============================

// 画面表示用データ取得
//==============================
// 現在のページのGETパラメータを取得
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;  //デフォルトは1ページ目
debug('$_SESSIONのなかみ：' . print_r($_SESSION, true)); //OK
debug('$_GETのなかみ：' . print_r($_GET, true)); //OK
// debug('$currentPageNumのなかみ：' . print_r($currentPageNum, true)); //OK
// ブースター情報
$booster = (!empty($_GET['s_id'])) ? $_GET['s_id'] : '';
// 種族情報
$type = (!empty($_GET['t_id'])) ? $_GET['t_id'] : '';
// 誕生月情報
$birth_m = (!empty($_GET['b_id'])) ? $_GET['b_id'] : '';
// 性格情報
$personal = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';


// パラメータに不正な値が入っていないかチェック
if (!is_int((int)$currentPageNum)) {
  error_log('エラー発生：指定ページ（$currentPageNum）に不正な値が入りました。トップページへ遷移します。');
  header("Location:index.php"); //トップページへ
}
// 表示件数
$listSpan = 20;
// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum - 1) * $listSpan);
debug('$currentPageNumのなかみ：' . print_r($currentPageNum, true));
debug('$currentMinNumのなかみ：' . print_r($currentMinNum, true));
// ↑1ページ目なら【(1-1)*20 = 0】、2ページ目は【(2-1)*20 = 20】番目の商品が表示される、その最小Noのアイテム
// DBからアイテムデータを取得
$dbCardData = getCardList($currentMinNum, $booster, $type, $birth_m, $personal);
// debug('$dbCardDataのなかみ：' . print_r($dbCardData, true)); //OK
// DBからブースターデータを取得
$dbBoosterData = getBooster();
// debug('$dbBoosterDataのなかみ：' . print_r($dbBoosterData, true)); //OK
// DBから種族データを取得
$dbTypesData = getTypes();
// debug('$dbTypesDataのなかみ' . print_r($dbTypesData, true)); //OK
// DBから性格データを取得
$dbPersonalData = getPersonal();
// debug('$dbPersonalDataのなかみ：' . print_r($dbPersonalData, true)); //OK
debug('現在のページ：' . $currentPageNum);
// debug('フォーム用DBデータ：' . print_r($dbFormData, true));

?>

<?php
$pageName = 'トップページ | どうぶつの森 カード検索(仮)';
require('head.php');
?>

<body class="page_index">
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
      <!--------サイドバー部分-------->
      <?php
      require('sidebar_left.php');
      ?>
      <!--------一覧表示部分-------->
      <div class="main">
        <form action="#" method="get">
          <input type="text">
          <input type="submit" value="検索">
        </form>
        <div class="search_title">
          <div class="search_left">
            <span class="total_num"><?php echo sanitize($dbCardData['total']); ?></span>点のアイテムが見つかりました。
          </div>
          <div class="search_right">
            <span class="num"><?php echo (!empty($dbCardData['data'])) ? $currentMinNum + 1 : 0; ?></span>
            〜
            <span class="num"><?php echo $currentMinNum + count($dbCardData['data']); ?></span>
            枚 /
            <span class="num"><?php echo sanitize($dbCardData['total']); ?></span>
            枚中
          </div>
        </div>
        <div class="panel_list">
          <ul>
            <?php
            foreach ($dbCardData['data'] as $key => $val) :
            ?>
              <li>
                <a href="cardDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam() . '&c_no=' . $val['card_no'] : '?c_no=' . $val['card_no'] . '&p=' . $currentPageNum; ?>" class="panel">
                  <div class="card_img">
                    <img src="images/<?php echo $val['card_no']; ?>.png" alt="<?php sanitize($val['name']); ?>" />
                  </div>
                  <div class="card_name">
                    <img src="images/<?php echo $val['card_no']; ?>_n.png" alt="<?php sanitize($val['name']); ?>" />
                  </div>
                </a>
                <?php if (!empty($_SESSION['user_id'])) { ?>
                  <i data-likecardno="<?php echo sanitize($val['card_no']); ?>" class="fas fa-heart fa-lg icn_fav js_click_like 
                    <?php if (isLike($_SESSION['user_id'], $val['card_no'])) {
                      echo 'active';
                    } ?>">
                  </i>
                <?php } ?>
              </li>
            <?php
            endforeach;
            ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!--------ページネーション部分-------->
  <?php echo pagenation($currentPageNum, $dbCardData['total_page'], 5, '&s_id=' . $booster . '&t_id=' . $type . '&b_id=' . $birth_m . '&p_id=' . $personal) ?>
  <!--------フッター部分-------->
  <?php
  require('footer.php');
  ?>