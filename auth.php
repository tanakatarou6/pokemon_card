<?php

//==============================
// ログイン認証・自動ログアウト
//==============================
// ログインしている場合
if (!empty($_SESSION['login_date'])) {
    debug('ログイン済ユーザーです。');

    // 現在日時が最終ログイン一時＋有効期限を超えていた場合
    if (($_SESSION['login_date'] + $_SESSION['login_limit']) < time()) {
        debug('ログイン有効期限オーバーです。');

        // セッションを削除しログアウト
        session_destroy();
        // ログインページへ遷移
        debug('ログインページへ遷移します。');
        header("Location:login.php");
    } else {
        debug('ログイン有効期限内です。');
        // 最終ログイン日時を現在時刻に更新
        $_SESSION['login_date'] = time();

        //現在実行中のスクリプトファイル名が「login.php」の場合、
        //$_SERVER['PHP_SELF']はドメインからのパスを返すため、
        //今回は「/ws_op/login.php」が返ってくる
        //これにbasename関数を使うとファイル名だけを取り出せる
        if (basename($_SERVER['PHP_SELF']) === 'login.php') {
            debug('マイページへ遷移します。');
            header("Location:mypage.php");
        }
    }
} else {
    debug('未ログインユーザーです。');
    if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
        debug('ログインページへ遷移します。');
        header("Location:login.php");
    }
}
