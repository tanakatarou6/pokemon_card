<?php
// 共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　お気に入りリストページ　「「「「「「「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//==============================
// 画面処理
//==============================

// 画面表示用データ取得
//==============================
// ユーザーIDを取得
$u_id = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';
// debug('$u_idのなかみ：' . print_r($u_id, true)); //OK

// 現在のページのGETパラメータを取得
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;  //デフォルトは1ページ目
debug('現在のページ：' . $currentPageNum);
// debug('フォーム用DBデータ：' . print_r($dbFormData, true));
// ブースター情報
$booster = (!empty($_GET['s_id'])) ? $_GET['s_id'] : '';
// 種族情報
$type = (!empty($_GET['t_id'])) ? $_GET['t_id'] : '';
// 誕生月情報
$birth_m = (!empty($_GET['b_id'])) ? $_GET['b_id'] : '';
// 性格情報
$personal = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';


// パラメータに不正な値が入っているかチェック
if (!is_int((int)$currentPageNum)) {
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:index.php");
}
// 表示件数
$listSpan = 20;
// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum - 1) * $listSpan);
debug('$currentPageNumのなかみ：' . print_r($currentPageNum, true));
debug('$currentMinNumのなかみ：' . print_r($currentMinNum, true));
// ↑1ページ目なら【(1-1)*20 = 0】、2ページ目は【(2-1)*20 = 20】番目の商品が表示される、その最小Noのアイテム
// DBからお気に入りデータを取得
$dbFavoriteData = getFavoriteList($u_id, $currentMinNum, $booster, $type, $birth_m, $personal);
debug('$dbFavoriteDataのなかみ：' . print_r($dbFavoriteData, true));
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
$pageName  = 'お気に入りカード一覧 | どうぶつの森 カード検索(仮)';
require('head.php');
?>

<body class="page_favoritList">
  <!--------ヘッダーメニュー部分-------->
  <?php
  require('header.php');
  ?>

  <!--------メインコンテンツ部分-------->
  <div class="contents container">
    <div class="flexbox">
      <!--------一覧表示部分-------->
      <div class="main">
        <div class="search_title">
          <div class="search_left">
            <span class="total_num"><?php echo sanitize($dbFavoriteData['total']); ?></span>点のアイテムがお気に入り登録されています。
          </div>
          <div class="search_right">
            <span class="num"><?php echo (!empty($dbFavoriteData['data'])) ? $currentMinNum + 1 : 0; ?></span>
            〜
            <span class="num"><?php echo $currentMinNum + count($dbFavoriteData['data']); ?></span>
            枚 /
            <span class="num"><?php echo sanitize($dbFavoriteData['total']); ?></span>
            枚中
          </div>
        </div>
        <div class="card_box_wrapper">
          <ul>

            <?php
            foreach ($dbFavoriteData['data'] as $key => $val) :
            ?>
              <li class="card_box">
                <div class="card_img_box">
                  <a href="cardDetail.php?c_no=<?php echo $val['card_no']; ?>">
                    <img src="images/<?php echo $val['card_no']; ?>.png" />
                  </a>
                </div>
                <div class="card_info_box">
                  <table>
                    <tbody>
                      <tr>
                        <th>名前</th>
                        <td><?php echo $val['name']; ?></td>
                      </tr>
                      <tr>
                        <th>誕生日</th>
                        <td><?php echo $val['birth_m'] . '月' . $val['birth_d'] . '日'; ?></td>
                      </tr>
                      <tr>
                        <th>種族</th>
                        <td><?php echo $val['type_name']; ?></td>
                      </tr>
                      <tr>
                        <th>性格</th>
                        <td><?php echo $val['personal_name']; ?></td>
                      </tr>
                      <tr>
                        <th>収録パック</th>
                        <td><?php echo $val['booster_name']; ?></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <!--------サイドバー部分-------->
      <?php
      require('sidebar_right.php');
      ?>

    </div>
  </div>

  <!--------ページネーション部分-------->
  <?php echo pagenation($currentPageNum, $dbFavoriteData['total_page'], 5, '&s_id=' . $booster . '&t_id=' . $type . '&b_id=' . $birth_m . '&p_id=' . $personal) ?>
  <!--------フッター部分-------->
  <?php
  require('footer.php');
  ?>