$(function () {
  const MSG_EMPTY = "入力必須です。";
  //入力必須の項目に入力されていない場合のエラー文
  const MSG_TEXT_OVER = "20文字以内で入力してください";
  //入力欄の文字数をオーバーした際のエラー文
  const MSG_EMAIL = "emailの形式で入力してください。";
  //メールアドレスの形式で入力されていない場合のエラー文
  const MSG_TEXTAREA_OVER = "200文字以内で入力してください。";
  //お問い合わせ内容の文字数上限オーバーした場合のエラー文
  const CHECK_EMAIL = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:.[a-zA-Z0-9-]+)*$/;
  //emailの形式かどうかチェックする用

  //==============================
  // 1.名前フォームの入力判定
  //==============================
  $(".valid_name").keyup(function () {
    //名前フォーム(valid_nameクラスのついている箇所)選択時にキーボードのキーから指が離れる(ボタンが上がる)度に、以下の処理を実行
    let form_g = $(this).closest(".form_group");
    //判定用の変数(仮)を用意し、名前フォームのhtml要素を代入

    if ($(this).val().length === 0) {
      //名前フォームの文字数が0文字だった場合
      form_g.removeClass("has_success").addClass("has_error");
      //名前フォームから「has_success」クラスを外して「has_error」クラスを付与
      form_g.find(".check").text(MSG_EMPTY);
      //名前フォームから見て子要素にある「check」クラスをもつ要素に「MSG_EMPTY」に代入されている文字を表示させる(エラー文を出す)
    } else if ($(this).val().length > 20) {
      //名前フォームの文字数が0文字ではなく20文字を超える場合
      form_g.removeClass("has_success").addClass("has_error");
      //名前フォームから「has_success」クラスを外して「has_error」クラスを付与
      form_g.find(".check").text(MSG_TEXT_OVER);
      //名前フォームから見て子要素にある「check」クラスをもつ要素に「MSG_TEXT_OVER」に代入されている文字を表示させる(エラー文を出す)
    } else {
      //上記以外の場合(問題ない場合)
      form_g.removeClass("has_error").addClass("has_success");
      //名前フォームから「has_error」クラスを外して「has_success」クラスを付与
      form_g.find(".check").text("");
      //名前フォームから見て子要素にある「check」クラスをもつ要素に何も表示させない(エラー文を消す)
    }
  });

  //==============================
  // 2.メールアドレスフォームの入力判定
  //==============================
  $(".valid_email").keyup(function () {
    //メールアドレスフォーム(valid_emailクラスのついている箇所)選択時にキーボードのキーから指が離れる(ボタンが上がる)度に、以下の処理を実行
    let form_g = $(this).closest(".form_group");
    //判定用の変数(仮)を用意し、メールアドレスフォームのhtml要素を代入

    if ($(this).val().length === 0) {
      //メールアドレスフォームの文字数が0文字だった場合
      form_g.removeClass("has_success").addClass("has_error");
      //メールアドレスフォームから「has_success」クラスを外して「has_error」クラスを付与
      form_g.find(".check").text(MSG_EMPTY);
      //メールアドレスフォームから見て子要素にある「check」クラスをもつ要素に「MSG_EMPTY」に代入されている文字を表示させる(エラー文を出す)
    } else if (!$(this).val().match(CHECK_EMAIL)) {
      //メールアドレスフォームの文字列がメールアドレスの形式でない場合
      form_g.removeClass("has_success").addClass("has_error");
      //メールアドレスフォームから「has_success」クラスを外して「has_error」クラスを付与
      form_g.find(".check").text(MSG_EMAIL);
      //メールアドレスフォームから見て子要素にある「check」クラスをもつ要素に「MSG_EMAIL」に代入されている文字を表示させる(エラー文を出す)
    } else {
      //上記以外の場合(問題ない場合)
      form_g.removeClass("has_error").addClass("has_success");
      //メールアドレスフォームから「has_error」クラスを外して「has_success」クラスを付与
      form_g.find(".check").text("");
      //名前フォームから見て子要素にある「check」クラスをもつ要素に何も表示させない(エラー文を消す)
    }
  });

  //==============================
  // 3.お問い合わせ内容フォームの入力判定
  //==============================
  $(".valid_message").keyup(function () {
    //お問い合わせ内容フォーム(valid_messageクラスのついている箇所)選択時にキーボードのキーから指が離れる(ボタンが上がる)度に、以下の処理を実行
    let form_g = $(this).closest(".form_group");
    //判定用の変数(仮)を用意し、メールアドレスフォームのhtml要素を代入

    if ($(this).val().length === 0) {
      //お問い合わせ内容フォームの文字数が0文字だった場合
      form_g.removeClass("has_success").addClass("has_error");
      //お問い合わせ内容フォームから「has_success」クラスを外して「has_error」クラスを付与
      form_g.find(".check").text(MSG_EMPTY);
      //お問い合わせ内容フォームから見て子要素にある「check」クラスをもつ要素に「MSG_EMPTY」に代入されている文字を表示させる(エラー文を出す)
    } else if ($(this).val().length > 200) {
      //お問い合わせ内容フォームの文字数が200文字を超える場合
      form_g.removeClass("has_success").addClass("has_error");
      //お問い合わせ内容フォームから「has_success」クラスを外して「has_error」クラスを付与
      form_g.find(".check").text(MSG_TEXTAREA_OVER);
      //お問い合わせ内容フォームから見て子要素にある「check」クラスをもつ要素に「MSG_TEXTAREA_OVER」に代入されている文字を表示させる(エラー文を出す)
    } else {
      //上記以外の場合(問題ない場合)
      form_g.removeClass("has_error").addClass("has_success");
      //お問い合わせ内容フォームから「has_error」クラスを外して「has_success」クラスを付与
      form_g.find(".check").text("");
      //お問い合わせ内容フォームから見て子要素にある「check」クラスをもつ要素に何も表示させない(エラー文を消す)
    }
  });

  //==============================
  // フッターの位置を画面最下部にする
  //==============================
  window.onload = function () {
    // ページが全て読み込み終わってから以下の処理をする
    let $ftr = $("#footer");
    // footerのDOMを取得
    if (window.innerHeight > $ftr.offset().top + $ftr.outerHeight()) {
      // 画面の高さが、表示するページの高さよりも高かった場合
      $ftr.attr({
        style:
          "position:fixed; top:" +
          (window.innerHeight - $ftr.outerHeight()) +
          "px",
        // 「footerを画面の最下部に固定する」というスタイルを記述する
      });
    }
  };

  //==============================
  // メッセージ表示(getSessionFlash)
  //==============================
  let $jsShowMsg = $("#js_show_msg");
  // jsshow_msgのDOMを取得
  let msg = $jsShowMsg.text();
  // 取得したDOMに入っているメッセージを取得して、
  if (msg.replace(/^[\s　]+|[\s　]+$/g, "").length) {
    // 半角や全角のスペースがあったら取り除く
    $jsShowMsg.slideToggle("slow");
    // 画面の上部から「にゅる」っとメッセージを出す
    setTimeout(function () {
      $jsShowMsg.slideToggle("slow");
      // 2000ミリ秒後に「にゅる」っと引っ込む
    }, 2000);
  }

  //==============================
  // 画像ライブプレビュー
  //==============================
  let $dropArea = $(".area_drop");
  let $fileInput = $(".input_file");
  $dropArea.on("dragover", function (e) {
    e.stopPropagation();
    e.preventDefault();
    $(this).css("border", "3px #ccc dashed");
  });
  $dropArea.on("dragleave", function (e) {
    e.stopPropagation();
    e.preventDefault();
    $(this).css("border", "none");
  });
  $fileInput.on("change", function (e) {
    $dropArea.css("border", "none");
    let file = this.files[0], // 2.files配列にファイルが入っています
      $img = $(this).siblings(".prev_img"), // 3.jQueryのsiblingsメソッドで兄弟のimgを取得
      fileReader = new FileReader(); // 4.ファイルを読み込むFileReaderオブジェクト

    // 5.読み込みが完了した際のイベントハンドラ。imgのsrcにデータをセット
    fileReader.onload = function (event) {
      //読み込んだデータをimgに設定
      $img.attr("src", event.target.result).show();
    };

    // 6.画像読み込み
    fileReader.readAsDataURL(file);
  });

  //==============================
  // 文字数カウント
  //==============================
  let $countUp = $("js_count");
  $countView = $("#js_count_view");
  $countUp.on("keyup", function (e) {
    $countView.html($(this).val().length);
  });

  //==============================
  // お気に入り登録 or 削除
  //==============================
  // 変数用意
  let $like;
  let likeCardNo;
  $like = $(".js_click_like") || null;
  // 変数にDOM情報を代入、取得できない場合はnullを代入
  if ($like !== undefined && $like !== null) {
    // $favが、undefinedでもnullでもない場合
    $like.on("click", function () {
      // $fav（.js_click_favクラスの付いているDOM）がクリックされた時、
      let $this = $(this);
      // $thisにクリックされた箇所のDOM情報を代入
      likeCardNo = $this.data("likecardno");
      // favCardNoに$thisに付いているdata属性の情報を代入
      $.ajax({
        type: "POST",
        // データ送信方法
        url: "ajaxLike.php",
        // データ送信先（相対パス）
        data: { like_card_no: likeCardNo },
        // 送信するデータ（「キー」：「内容」）
      })
        .done(function (data) {
          console.log("カードNo：", likeCardNo);
          // ajax送信に成功した場合
          console.log("Ajax Success");
          // $this（に代入されているDOM）に「active」クラスを付け外し
          $this.toggleClass("active");
        })
        .fail(function (msg) {
          // ajax送信に失敗した場合
          console.log("ajax Error");
        });
    });
  }
});
