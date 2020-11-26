<?php
// 共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ajaxLike.php開始　「「「「「「「「「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//==============================
// ajax処理
//==============================

// postがあり、ユーザーIDがあり、ログインしている場合
// debug('$_POSTのなかみ' . print_r($_POST, true));
// debug('$_SESSION[user_id]のなかみ：' . print_r($_SESSION['user_id'], true));
if (isset($_POST['like_card_no']) && isset($_SESSION['user_id']) && isLogin()) {
    debug('POST通信があります。(ajaxLike.php)');
    $u_id = $_SESSION['user_id'];
    $c_no = $_POST['like_card_no'];
    debug('ユーザーID：' . $u_id);
    debug('カードNo：' . $c_no);

    // 例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        $sql = 'SELECT * FROM likes WHERE like_card_no = :c_no AND user_id = :u_id AND like1 = 1';
        $data = array(':c_no' => $c_no, ':u_id' => $u_id);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        // debug('$stmtのあたい：' . print_r($stmt, true));
        $resultCount = $stmt->rowCount();
        debug('$resultCountのあたい：' . $resultCount); //OK
        if (!empty($resultCount)) {
            // お気に入りにレコードがある場合
            debug('お気に入り情報解除します');
            $sql = 'DELETE FROM likes WHERE like_card_no = :c_no AND user_id = :u_id';
            $data = array(':c_no' => $c_no, ':u_id' => $u_id);
        } else {
            // お気に入りにレコードがない場合
            debug('お気に入り情報登録します');
            $sql = 'INSERT INTO likes SET like_card_no = :c_no, user_id = :u_id, create_date = :date, like1 = 1';
            $data = array('c_no' => $c_no, ':u_id' => $u_id, ':date' => date('Y-m-d H:i:s'));
        }
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
    }
}
debug('<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>');
debug('<<<<<<<<<<< ajax処理終了 >>>>>>>>>>');
debug('<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>');
