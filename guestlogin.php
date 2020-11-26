<?php
//共通変数・関数ファイル読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ゲストログインページ　「「「「「「「「「「「「「「「「「');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

debug('ゲストログインします。');

// ログイン有効期限(60分に変更)
$sesLimit = 60 * 60;
// 最終ログイン日時を現在日時に変更
$_SESSION['login_date'] = time();
$_SESSION['login_limit'] = $sesLimit;
// ユーザーIDを格納
$_SESSION['user_id'] = 1;

debug('新規登録後のセッション変数の中身' . print_r($_SESSION, true));

// セッションを削除しログアウトする
debug('マイページへ遷移します。');
header("Location:mypage.php");
