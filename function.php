<?php

//==============================
// ログ関連
//==============================

//ログをとるか
ini_set('log_errors', 'on');
//ログの出力ファイルを指定
ini_set('error_log', 'php.log');


//==============================
// デバッグ関連
//==============================
//デバッグフラグ
$debug_flg = true; //サービス開始時にfalseに変更
//デバッグログ関連
function debug($str)
{
    global $debug_flg;
    if (!empty($debug_flg)) {
        error_log('デバッグ：' . $str);
    }
}


//==============================
// セッション準備・セッション有効期限を伸ばす
//==============================
//セッションファイルの保管場所変更(/var/tmpに配置すると30日は削除されない)
session_save_path("/var/tmp/");
//ガーベージコレクションが削除するセッションの有効期限を設定(30日以上経っているものに対してだけ1/100で削除)
ini_set('session gc_maxlifetime', 60 * 60 * 24 * 30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える(なりすましのセキュリティ対策)
session_regenerate_id();


//==============================
// 画面表示処理開始・ログ出力関数
//==============================
function debugLogStart()
{
    debug('<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>');
    debug('<<<<<<<<<<<画面表示処理開始>>>>>>>>>>');
    debug('<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>');
    debug('セッションID:' . session_id());
    debug('セッション変数の中身：' . print_r($_SESSION, true));
    debug('現在日時タイムスタンプ：' . time());
    if (!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])) {
        debug('ログイン期限日時タイムスタンプ：' . ($_SESSION['login_date'] + $_SESSION['login_limit']));
    }
}

//==============================
// 定数
//==============================
//メッセージを定数に設定
define('MSG01', '入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03', 'パスワード（再入力）が一致しません');
define('MSG04', '半角英数字のみで入力してください');
define('MSG05', '6文字以上で入力してください');
define('MSG06', '256文字以内で入力してください');
define('MSG07', 'エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのEmailは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');
define('MSG10', '古いパスワードが違います');
define('MSG11', '古いパスワードと同じです');
define('MSG12', '文字で入力してください');
define('MSG13', '正しくありません');
define('MSG14', '有効期限が切れています');
define('MSG15', 'チェックを入れてください');

define('SUC01', 'パスワードを変更しました');
define('SUC02', 'プロフィールを変更しました');
define('SUC03', 'メールを送信しました');
define('SUC04', 'プロフィールを登録しました');
define('SUC05', 'お気に入り情報を更新しました');

//==============================
// バリデーション関数
//==============================
//エラーメッセージ格納用の配列
$err_msg = array();
//dbアクセス結果用フラグ
$dbRst = false;

//バリデーション関数(未入力チェック)
function validRequired($str, $key)
{
    if ($str === '') {
        global $err_msg;
        $err_msg[$key] = MSG01;
    }
}

//バリデーション関数(Email形式チェック)
function validEmail($str, $key)
{
    if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)) {
        global $err_msg;
        $err_msg[$key] = MSG02;
    }
}

//バリデーション関数(Email重複チェック)
function validEmailDup($email)
{
    global $err_msg;
    //例外処理
    try {
        // DB接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        //クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty(array_shift($result))) {
            $err_msg['email'] = MSG08;
        }
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

//バリデーション関数（同値チェック）
function validMatch($str1, $str2, $key)
{
    if ($str1 !== $str2) {
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}
//バリデーション関数（最小文字数チェック）
function validMinLen($str, $key, $min = 6)
{
    if (mb_strlen($str) < $min) {
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}

//バリデーション関数（最大文字数チェック）
function validMaxLen($str, $key, $max = 256)
{
    if (mb_strlen($str) > $max) {
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}

//バリデーション関数（半角チェック）
function validHalf($str, $key)
{
    if (!preg_match("/^[a-zA-Z0-9]+$/", $str)) {
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}

//固定長チェック
function validLength($str, $key, $len = 8)
{
    if (mb_strlen($str) !== $len) {
        global $err_msg;
        $err_msg[$key] = $len . MSG12;
    }
}

//パスワードチェック
function validPass($str, $key)
{
    //半角英数字チェック
    validHalf($str, $key);
    //最大文字数チェック
    validMaxLen($str, $key);
    //最小文字数チェック
    validMinLen($str, $key);
}

//エラーメッセージ表示
function getErrMsg($key)
{
    global $err_msg;
    if (!empty($err_msg[$key])) {
        return $err_msg[$key];
    }
}

// ログイン認証（簡易版）
function isLogin()
{
    // ログインしている場合
    if (!empty($_SESSION['login_date'])) {
        debug('ログイン済ユーザーです');

        // 現在日時が最終ログイン日時+有効期限をオーバーしていた場合
        if (($_SESSION['login_date']) + $_SESSION['login_limit'] < time()) {
            debug('ログイン有効期限オーバーです');

            // セッションを削除(ログアウトする)
            session_destroy();
            return false;
        } else {
            debug('ログイン有効期限内です');
            return true;
        }
    } else {
        debug('未ログインユーザーです');
        return false;
    }
}

//==============================
// データベース関連
//==============================
//DB接続関数
function dbConnect()
{
    //DBへの接続準備
    $dsn = 'mysql:dbname=animals;
            host=localhost;
            charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
        // SQL実行失敗時にはエラーコードのみ設定
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // デフォルトフェッチモードを連想配列形式に設定
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // バッファードクエリを使う（一度に結果セットを全て取得し、サーバー負荷を軽減）
        // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    // PDOオブジェクト生成（）
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
}
function queryPost($dbh, $sql, $data)
{
    //クエリー作成
    $stmt = $dbh->prepare($sql);
    //プレースホルダに値をセットし、SQL文を実行
    if (!$stmt->execute($data)) {
        debug('クエリに失敗しました。');
        debug('失敗したSQL：' . print_r($stmt, true));
        $err_msg['common'] = MSG07;
        return 0;
    }
    // debug('クエリ成功。');
    return $stmt;
}
function getUser($u_id)
{
    debug('ユーザー情報を取得します(getUser');
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        // // クエリ成功の場合
        // if ($stmt) {
        //     debug('クエリ成功');
        // } else {
        //     debug('クエリに失敗しました');
        // }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
    }
    // クエリ結果のデータを返却
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getCardList($currentMinNum = 1, $booster, $type, $birth_m, $personal, $span = 20)
{
    debug('カード情報を取得します(getCardList)');
    // 例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        $sql = 'SELECT card_no FROM cards';
        // $data = array();
        // // クエリ実行
        // $stmt = queryPost($dbh, $sql, $data);
        // $rst['total'] = $stmt->rowCount(); //総レコード数
        // $rst['total_page'] = ceil($rst['total'] / $span); //総ページ数
        // if (!$stmt) {
        //     return false;
        // }
        // debug('$rst[totla]のなかみ：' . print_r($rst['total']), true);
        // debug('$rst[totla_page]のなかみ：' . print_r($rst['total_page']), true);

        // ページング用のSQL文作成
        $sql = 'SELECT * FROM cards';
        if (!empty($booster)) {
            $sql .= ' WHERE booster = ' . $booster; // 1
            if (!empty($type)) {
                $sql .= ' AND type = ' . $type;  // 12
                if (!empty($birth_m)) {
                    $sql .= ' AND birth_m = ' . $birth_m; // 123
                    if (!empty($personal)) {
                        $sql .= ' AND personal = ' . $personal;  // 1234
                    }
                } elseif (!empty($personal)) {
                    $sql .= ' AND personal = ' . $personal;  // 12 4
                }
            } elseif (!empty($birth_m)) {
                $sql .= ' AND birth_m = ' . $birth_m; // 1 3
                if (!empty($personal)) {
                    $sql .= ' AND personal = ' . $personal; // 1 34
                }
            } elseif (!empty($personal)) {
                $sql .= ' AND personal = ' . $personal; // 1  4
            }
        } elseif (!empty($type)) {
            $sql .= ' WHERE type = ' . $type; // 2
            if (!empty($birth_m)) {
                $sql .= ' AND birth_m = ' . $birth_m; // 23
                if (!empty($personal)) {
                    $sql .= ' AND personal = ' . $personal;  // 234
                }
            } elseif (!empty($personal)) {
                $sql .= ' AND personal = ' . $personal;  // 2 4
            }
        } elseif (!empty($birth_m)) {
            $sql .= ' WHERE birth_m = ' . $birth_m;  // 3
            if (!empty($personal)) {
                $sql .= ' AND personal = ' . $personal;  // 34
            }
        } elseif (!empty($personal)) {
            $sql .= ' WHERE personal = ' . $personal;  // 4
        }

        $data = array();
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        if ($stmt) {
            // クエリ結果の件数を格納
            $rst['total'] = $stmt->rowCount(); //総レコード数
            $rst['total_page'] = ceil($rst['total'] / $span); //総ページ数
        }

        $sql .= ' LIMIT ' . $span . ' OFFSET ' . $currentMinNum;
        $data = array();
        debug('完成したSQL文：' . $sql);

        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if ($stmt) {
            // クエリ結果のデータの全レコード格納
            $rst['data'] = $stmt->fetchAll();
            // debug('$rstのなかみ：' . print_r($rst, true)); // $dbCardDataへ代入される
            return $rst;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
    }
}

function getBooster()
{
    debug('ブースター情報を取得します(getBooster)');
    // 例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM booster WHERE delete_flg = 0';
        $data = array();
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if ($stmt) {
            //クエリ結果の全データを返却
            return $stmt->fetchAll();
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
    }
}

function getTypes()
{
    debug('種族情報を取得します(getTypes)');
    // 例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM types WHERE delete_flg = 0';
        $data = array();
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if ($stmt) {
            // クエリ結果の全データを返却
            return $stmt->fetchAll();
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生；' . $e->getMessage());
    }
}
function getPersonal()
{
    debug('性格情報を取得します(getPersonal)');
    // 例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM personal WHERE delete_flg = 0';
        $data = array();
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if ($stmt) {
            // クエリ結果の全データを返却
            return $stmt->fetchAll();
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
    }
}
function getCardData($c_no, $u_id)
{
    debug('カード情報を取得します(getCardData)');
    debug('カードID($c_no)：' . $c_no);
    debug('ユーザーID($u_id)：' . $u_id);
    //例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        $sql = 'SELECT c.card_no, c.name, c.type, c.birth_m, c.birth_d, c.personal, c.habit, c.motto, c.booster, f.fav_id, f.user_id, f.fav_card_no, f.comment, b.booster_name, t.type_name, p.personal_name
                FROM cards AS c
                LEFT JOIN favorite AS f
                ON f.user_id = :u_id
                AND f.fav_card_no = :c_no 
                LEFT JOIN booster AS b
                ON c.booster = b.id
                LEFT JOIN types AS t
                ON c.type = t.id
                LEFT JOIN personal AS p
                ON c.personal = p.id
                WHERE c.card_no = :c_no';
        $data = array(':c_no' => $c_no, ':u_id' => $u_id);
        // debug('$dataのなかみ：' . print_r($data, true));　// OK
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        // debug('$stmtのなかみ：' . print_r($stmt, true));
        if ($stmt) {
            // クエリ結果のデータを1レコード返却
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
    }
}
function getLikeData($u_id, $span = 5)
{
    debug('お気に入り情報を取得します(getLikeData)');
    debug('ユーザーID($u_id)：' . $u_id);
    debug('取得するデータ件数：' . $span);
    // 例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();

        // SQL文作成
        $sql = 'SELECT l.user_id, c.card_no
                FROM likes AS l
                LEFT JOIN cards AS c
                ON l.like_card_no = c.card_no
                WHERE user_id = :u_id
                ORDER BY l.create_date DESC
                LIMIT ' . $span;
        $data = array(':u_id' => $u_id);

        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if ($stmt) {
            return $stmt->fetchAll();
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
    }
}
function getFavoriteList($u_id, $currentMinNum = 1, $booster, $type, $birth_m, $personal, $span = 20)
{
    debug('お気に入りリスト情報を取得します(getLikeData)');
    debug('ユーザーID($u_id)：' . $u_id);
    debug('取得するデータ件数：' . $span);
    // 例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();

        // 基本のSQL作成
        $sql = 'SELECT l.user_id, c.card_no, c.name, c.birth_m, c.birth_d, t.type_name, p.personal_name, b.booster_name, l.create_date
                FROM likes AS l
                LEFT JOIN cards AS c
                ON l.like_card_no = c.card_no
                LEFT JOIN booster AS b
                ON c.booster = b.id
                LEFT JOIN types AS t
                ON c.type = t.id
                LEFT JOIN personal AS p
                ON c.personal = p.id
                WHERE user_id = :u_id
                ORDER BY c.card_no ASC';
        $data = array(':u_id' => $u_id);

        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        if ($stmt) {
            // クエリ結果の件数を格納
            $rst['total'] = $stmt->rowCount(); //お気に入りに登録されている件数
            $rst['total_page'] = ceil($rst['total'] / $span); //総ページ数
        }

        $sql .= ' LIMIT ' . $span . ' OFFSET ' . $currentMinNum;
        $data = array(':u_id' => $u_id);

        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if ($stmt) {
            // クエリ結果のデータの全レコードを格納
            $rst['data'] = $stmt->fetchAll();
            return $rst;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
    }
}
function getCommentData($u_id, $span = 8)
{
    debug('最近コメントしたカード情報を取得します(getCommentData');
    debug('ユーザーID：' . $u_id);
    // 例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        $sql = 'SELECT c.card_no, c.name, c.booster, f.user_id, f.comment, f.update_date, b.booster_name
                FROM cards AS c
                LEFT JOIN favorite AS f
                ON f.fav_card_no = c.card_no
                LEFT JOIN booster AS b
                ON c.booster = b.id
                WHERE f.user_id = :u_id
                ORDER BY f.update_date DESC
                LIMIT ' . $span;
        $data = array(':u_id' => $u_id);

        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if ($stmt) {
            // クエリ結果の全データを返却
            return $stmt->fetchAll();
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
    }
}
function isLike($u_id, $c_no)
{
    // debug('お気に入り情報があるか確認します(siLike)');
    // debug('ユーザーID：' . $u_id);
    // debug('カードNo.：' . $c_no);
    // 例外処理
    try {
        // DBへ接続
        $dbh = dbConnect();
        // SQL文作成
        $sql = 'SELECT * FROM likes WHERE like_card_no = :c_no AND user_id = :u_id';
        $data = array(':c_no' => $c_no, ':u_id' => $u_id);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        // debug('$stmt->rowCount()のあたい：' . print_r($stmt->rowCount(), true));
        if ($stmt->rowCount()) {
            // debug('お気に入りです(isLike)');
            return true;
        } else {
            // debug('お気に入りではないです(isLike)');
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
    }
}

//==============================
// メール送信
//==============================
function sendMail($from, $to, $subject, $comment)
{
    if (!empty($to) && !empty($subject) && !empty($comment)) {
        //下記2行は文字化けしないように記述（お決まりパターン）
        mb_language("Japanese"); //現在使用している言語を設定
        mb_internal_encoding("UTF-8"); //内部の文字をどうエンコードするか設定

        //メールを送信（送信結果はtrueかfalseで返ってくる）
        $result = mb_send_mail($to, $subject, $comment, "From:" . $from);
        //送信結果を判定
        if ($result) {
            debug('メール送信に成功しました');
        } else {
            debug('【エラー発生】メール送信に失敗しました');
        }
    }
}


//==============================
// その他
//==============================
//サニタイズ
function sanitize($str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}
//フォーム入力保持
function getFormData($str, $flg = false)
{
    if ($flg) {
        $method = $_GET;
    } else {
        $method = $_POST;
    }
    global $dbFormData;
    // debug('$dbFormDataのなかみ：' . print_r($dbFormData, true));
    // ユーザーデータがある場合
    if (!empty($dbFormData)) {
        //フォームのエラーがある場合
        if (!empty($err_msg[$str])) {
            //POST or GETにデータがある場合
            if (isset($method[$str])) { //「isset」は0や空の配列をを「入っている」と判断する
                return sanitize($method[$str]);
            } else {
                //ない場合はDBの情報を表示（通常はありえない）
                return sanitize($dbFormData[$str]);
            }
        } else {
            //POST or GETにデータがあり、DBの情報と違う場合(このフォームも変更していて、他のフォームがエラーになっている場合)
            if (isset($method[$str]) && $method[$str] !== $dbFormData[$str]) {
                return sanitize($method[$str]);
            } else {
                //そもそも変更していない
                return sanitize($dbFormData[$str]);
            }
        }
    } else {
        if (isset($method[$str])) {
            // debug('$method[$str]のなかみ：' . print_r($method[$str], true));
            // debug('$strのなかみ：' . print_r($str, true));
            return sanitize($method[$str]);
        }
    }
}
//sessionを1回だけ取得できる(取得後に初期化する)
function getSessionFlash($key)
{
    if (!empty($_SESSION[$key])) {
        $data = $_SESSION[$key];
        $_SESSION[$key] = '';
        return $data;
    }
}
//認証キー生成
function makeRandKey($length = 8)
{
    static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $str = '';
    for ($i = 0; $i < $length; ++$i) {
        $str .= $chars[mt_rand(0, 61)];
    }
    return $str;
}
//画像処理
function uploadImg($file, $key)
{
    debug('画像アップロード処理開始');
    debug('FILE情報：' . print_r($file, true));

    if (isset($file['error']) && is_int($file['error'])) {
        try {
            // バリデーション
            // $file['error'] の値を確認。配列内には「UPLOAD_ERR_OK」などの定数が入っている。
            // 「UPLOAD_ERR_OK」などの定数はphpでファイルアップロード時に自動的に定義される。定数には値として0や1などの数値が入っている。
            switch ($file['error']) {
                case UPLOAD_ERR_OK: //OK
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('ファイルが選択されていません');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('ファイルサイズが大きすぎます');
                default:
                    throw new RuntimeException('その他エラーが発生しました');
            }

            // $file['mime']の値はブラウザ側で偽装可能なので、MIMEタイプを自前でチェックする
            // exif__imagetype関数は「IMAGETYPE_GIF」「IMAGETYPE_JPEG」などの定数を返す
            $type = @exif_imagetype($file['tmp_name']);
            if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
                throw new RuntimeException('未対応形式のファイルです');
            }

            //ファイルデータからSHA-1ハッシュを取ってファイル名を決定し、ファイルを保存する
            //ハッシュ化しないとファイル名がダブり、DBに保存した際にどの画像か判断できなくなる
            //image_type_to_extension関数はファイルの拡張子を取得するもの
            $path = 'uploads/' . sha1_file($file['tmp_name']) . image_type_to_extension($type);

            if (!move_uploaded_file($file['tmp_name'], $path)) { //ファイルを移動する
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }
            // 保存したファイルパスのパーミッションを変更する
            chmod($path, 0644);

            debug('ファイルは正常にアップロードされました');
            debug('ファイルパス：' . $path);
            return $path;
        } catch (RuntimeException $e) {
            debug($e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }
    }
}
// ページング
function pagenation($currentPageNum, $totalPageNum, $pageColNum = 10, $link = '')
{
    // 総ページ数が、表示ページ数以下の場合
    if ($totalPageNum <= $pageColNum) {
        $minPageNum = 1;
        $maxPageNum = $totalPageNum;
        // 現在のページが、1か2、総ページ数が表示ページ数より大きい場合
    } elseif ($currentPageNum <= 2) {
        $minPageNum = 1;
        $maxPageNum = $pageColNum;
        // 現在のページが、最終ページの1つ前までの場合
    } elseif ($currentPageNum >= $totalPageNum - 1) {
        $minPageNum = $totalPageNum - $pageColNum + 1;
        $maxPageNum = $totalPageNum;
        // それ以外（左右に2個ずつ出す場合）
    } else {
        $minPageNum = $currentPageNum - 2;
        $maxPageNum = $currentPageNum + 2;
    }

    echo '<div class="pagination">';
    echo '<div class="container">';
    echo '<ul class="pagination_list">';
    if ($currentPageNum != 1) {
        echo '<li class="list_item"><a href="?p=1' . $link . '">&lt;</a></li>';
    }
    for ($i = $minPageNum; $i <= $maxPageNum; $i++) {
        echo '<li class="list_item ';
        if ($currentPageNum == $i) {
            echo 'active';
        }
        echo '"><a href="?p=' . $i . $link . '">' . $i . '</a></li>';
    }
    if ($currentPageNum != $maxPageNum) {
        echo '<li class="list_item"><a href="?p=' . $totalPageNum . $link . '">&gt;</a></li>';
    }
    echo '</ul>';
    echo '</div>';
    echo '</div>';
}
// GETパラメータ付与
// $arr_del_key：付与から取り除きたいGETパラメータのキー（キーがない場合はカラの配列が代入される）
function appendGetParam($arr_del_key = array())
{
    if (!empty($_GET)) {
        // GETパラメータがあった場合
        // debug('appendGetParamの$_GETのあたい：' . print_r($_GET, true));
        $str = '?';
        // $strに、まず「?」を入れる
        foreach ($_GET as $key => $val) {
            // 取り除きたいパラメータじゃない場合にURLにくっつけるパラメータを生成
            // debug('$key => $valのあたい：' . $key . ' => ' . $val);
            if (!in_array($key, $arr_del_key, true)) {
                $str .= $key . '=' . $val . '&';
                // $strに $str+$key+=+$val+&を代入  を繰り返して、GETパラメータを作成
                // 例：?c_no=001&p=1 など
            }
        }
        $str = mb_substr($str, 0, -1, "UTF-8");
        // debug('returnする$strのあたい：' . print_r($str, true));
        return $str;
    }
    // GETパラメータがない場合は何もしない
}
