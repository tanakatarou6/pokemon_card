// ==============================
// 各種DOM取得
// ==============================
// スムーススクロール
const smoothScroll = document.querySelectorAll(".js-smoothScroll");
// フロートヘッダーメニュー
const headerMenuElem = document.getElementById("header");
const heroElem = document.getElementById("hero");
// ハンバーガーメニュー
const toggleSpMenu = document.querySelector(".js-toggleSpMenu");
const toggleSpMenuTarget = document.querySelector(".js-toggleSpMenuTarget");
const bodyElem = document.getElementById("body");
// ハンバーガーメニューオートクローズ
const spMenuClose = document.querySelectorAll(".js-spMenuClose");

// ==============================
// スムーススクロール
// ==============================
for (let i = 0; i < smoothScroll.length; i++) {
  smoothScroll[i].addEventListener("click", function (e) {
    e.preventDefault();
    // ヘッダーの高さ分の調整値
    let adjust = 80;
    // クリックされたaタグのhref属性を取得し「#」をとる
    let href = this.getAttribute("href").replace("#", "");
    // hrefが「#」または「空欄」だったらhero、それ以外ならhref属性を返す
    let targetElement = href === "" ? "hero" : href;
    // クリックされたリンクのリンク先を取得
    let position = document.getElementById(targetElement);
    // 現在のスクロール位置からリンク先までの距離を取得
    let rect = position.getBoundingClientRect().top;
    // TOPからの現在のスクロール位置までの距離を取得
    let offset = window.pageYOffset;
    // [スクロール位置]=[TOPから現在地]+[現在地から着地点]+ｰ[各種調整値]
    let target = rect + offset - adjust;
    // スムーススクロール
    window.scrollTo({
      top: target,
      behavior: "smooth",
    });
  });
}

// ==============================
// フロートヘッダー
// ==============================
// window.addEventListener("scroll", function () {
//   let kvHeight = heroElem.offsetHeight;
//   if (kvHeight < this.window.pageYOffset) {
//     if (!headerMenuElem.classList.contains("is-fixed")) {
//       headerMenuElem.classList.add("is-fixed");
//     }
//   } else {
//     if (headerMenuElem.classList.contains("is-fixed")) {
//       headerMenuElem.classList.remove("is-fixed");
//     }
//   }
// });

// ==============================
// ハンバーガーメニュー
// ==============================
toggleSpMenu.addEventListener("click", function () {
  if (this.classList.contains("active")) {
    // 1.「active」クラスがついている場合
    // ハンバーガーメニュー周りの「active」クラスを削除、bodyの「fixed」も削除
    this.classList.remove("active");
    toggleSpMenuTarget.classList.remove("active");
    bodyElem.classList.remove("is_fixed");
  } else {
    // 2.「active」クラスがついてない場合
    // ハンバーガーメニュー周りに「active」クラスを付与、bodyに「fixed」も付与
    this.classList.add("active");
    toggleSpMenuTarget.classList.add("active");
    bodyElem.classList.add("is_fixed");
  }
});
// ==============================
// ハンバーガーメニューオートクローズ
// ==============================
for (let i = 0; i < spMenuClose.length; i++) {
  // ハンバーガーメニュー内のリンクがクリックされたとき
  spMenuClose[i].addEventListener("click", function () {
    // ハンバーガーメニュー周りの「active」クラスを削除、bodyの「fixed」も削除
    toggleSpMenu.classList.remove("active");
    toggleSpMenuTarget.classList.remove("active");
    bodyElem.classList.remove("is_fixed");
  });
}

// ==============================
// 検索ページ用JS
// ==============================
// コピーボタンのDOM取得
const btn_copy_text = document.querySelector("#js-txt_copy");
const btn_copy_clip = document.querySelector("#js-cb_copy");
// const btn_clear_all = document.querySelector("#js-cb_clear");
const btn_select_all = document.querySelector("#js-cb_all");

// const $btn_add_name = document.getElementsByClassName("js-add_name");
// const $btn_del_name = document.getElementsByClassName("js-del_name");
const btn_tgl_check = document.getElementsByClassName("js-tgl_check");
const btn_check_all = document.getElementsByClassName("js-check_all");
const btn_clear_all = document.getElementsByClassName("js-clear_all");

// コピー対象のDOMやvalue
const copy_text_target = document.querySelector("#js-txt_target");
// const search_value = document.querySelector("#js-value_target");

// 「下のテキストエリアにコピーします」を押したとき
// btn_copy_text.addEventListener("click", function () {
//   // テキストエリアにコピーするイベント
//   copyText(copy_text_target);
// });

// 「上のテキストをクリップボードにコピーします」を押したとき
// btn_copy_clip.addEventListener("click", function () {
//   copyClip(copy_clip_target);
// });

// 「名前追加ボタン」を押したとき
// for (let i = 0; i < $btn_add_name.length; i++) {
//   $btn_add_name[i].addEventListener("click", function () {
//     addName(this);
//   });
// }

// 「名前削除ボタン」を押したとき
// for (let i = 0; i < $btn_del_name.length; i++) {
//   $btn_del_name[i].addEventListener("click", function () {
//     delName(this);
//   });
// }

// 「個別のチェックボックス」を押したとき
for (let i = 0; i < btn_tgl_check.length; i++) {
  btn_tgl_check[i].addEventListener("change", function () {
    // クリックされたチェックボックスの上のlabelタグの要素を取得
    let labelElem =
      this.closest(".p-search__list").querySelector(".js-value_target");
    toggleCheckbox(this);
    console.log("selectBox:" + labelElem.innerHTML);
  });
}

// 「すべて選択」を押したとき
for (let i = 0; i < btn_check_all.length; i++) {
  btn_check_all[i].addEventListener("click", function () {
    // ▼すべて選択からみた相対指定でのlabel要素
    labelElem =
      this.closest(".p-search__list").querySelector(".js-value_target");
    console.log(labelElem);
    labelElem.innerHTML = " ";
    // ▼すべて選択から見た相対指定での各チェックボックス
    checkBox =
      this.closest(".p-search__list").getElementsByClassName("js-tgl_check");
    console.log(checkBox);
    for (let i = 0; i < checkBox.length; i++) {
      // 1.すべてのチェックボックスにチェックを入れる
      checkBox[i].checked = true;
      // 2.各data属性をjs-value_targetに入れる
      addName(checkBox[i], labelElem);
    }
    // 末尾にあったら「/ 」を削除
    if (labelElem.innerHTML.endsWith("/ ")) {
      labelElem.innerHTML = labelElem.innerHTML.slice(0, -2);
    }
  });
}

// 「選択を解除」を押したとき
for (let i = 0; i < btn_clear_all.length; i++) {
  btn_clear_all[i].addEventListener("click", function () {
    // ▼「選択を解除」からみた相対指定でのlabel要素
    labelElem =
      this.closest(".p-search__list").querySelector(".js-value_target");
    console.log(labelElem);
    labelElem.innerHTML = "";
    // ▼「選択を解除」から見た相対指定での各チェックボックス
    checkBox =
      this.closest(".p-search__list").getElementsByClassName("js-tgl_check");
    console.log(checkBox);
    for (let i = 0; i < checkBox.length; i++) {
      // 1.すべてのチェックボックスのチェックを外す
      checkBox[i].checked = false;
    }
    // js-value_targetを「選択なし」で上書き
    labelElem.innerHTML = "選択なし";
  });
}

//------------------------------
// テキストエリアにコピーを出力
//------------------------------
const copyText = function (target) {
  // テキストエリアにコピーを出力
  copy_clip_target.value += target.value + "\n";
};

//------------------------------
// テキストエリアをクリップボードにコピー
//------------------------------
const copyClip = function (target) {
  // 仮のテキストエリア(実際には表示されない)を生成
  const clipboard = document.createElement("textarea");
  // 仮のテキストエリアのvalueにtargetのvalueをコピー
  clipboard.value = target.value;
  // 仮のテキストエリアを元のテキストエリアの子要素として追加
  document.body.appendChild(clipboard);
  // 仮のテキストエリアを選択状態にする
  clipboard.select();
  // 選択範囲をクリップボードにコピー
  document.execCommand("copy");
  // 仮のテキストエリアを削除する
  document.body.removeChild(clipboard);
  // アラートを出す
  alert("クリップボードにコピーしました");
};

//------------------------------
// 押したボタンのdata属性をテキストエリアに出力
//------------------------------
const addName = function (checkbox, labelElem) {
  labelElem.innerHTML += checkbox.dataset.value + " / ";
};

//------------------------------
// 押したボタンと同じ文字列を削除
//------------------------------
const delValue = function (target, str, label) {
  console.log("削除文字列：" + str);
  console.log(
    target.closest(".p-search__list").querySelector(".js-value_target")
  );
  // label
  console.log(label);
  // 削除する基準となる文字列を取得(2)
  console.log("削除元文字列：" + label);
  // 削除元文字列(2)から削除文字列(1)を削除+文字列内の「//」を「/」にする
  label = label.replace(str, "").replace("//", "/");
  // 削除後に文字列の先頭に「/」があったら削除
  if (label.slice(0, 1) === "/") {
    label = label.slice(1);
  }
  // 削除後に文字列の最後に「/」があったら削除
  if (label.endsWith("/")) {
    label = label.slice(0, -1);
  }
  // 削除後に文字列が空だったら「選択なし」を追加
  if (!label) {
    label = "選択なし";
  }
  console.log("削除処理後：" + label);
  // 削除処理をした文字列をテキストエリアに出力
  target
    .closest(".p-search__list")
    .querySelector(".js-value_target").innerHTML = label;
  console.log(
    target.closest(".p-search__list").querySelector(".js-value_target")
  );
};

//------------------------------
// テキストエリアでdata値をtoggleする
//------------------------------
const toggleCheckbox = function (target) {
  // targetにはクリックしたチェックボックスの要素が入る
  console.log(target);
  // クリックしたチェックボックスのdata属性を取得し、前後に半角スペースを付与
  let value = " " + target.dataset.value + " ";
  // labelの文字列を取得
  let label = target
    .closest(".p-search__list")
    .querySelector(".js-value_target").innerHTML;
  // labelにチェックしたinputのdataと同じものがあるかチェック
  console.log("検索条件：" + value);
  // 実際の処理
  if (label.match(value)) {
    console.log("labelに一致あり(data値を削除します)");
    // target:チェックボックスの要素、value:data属性値、label:labelの文字列(選択なし 等)
    delValue(target, value, label);
  } else {
    console.log("labelに一致なし(data値を追加します)");
    console.log("labelの文字列：" + label);
    // 最初の1回は「選択なし」を削除
    if (label === "選択なし") {
      label = " ";
    } else {
      label += "/ ";
    }
    label += target.dataset.value + " ";
    // addType(target);
    target
      .closest(".p-search__list")
      .querySelector(".js-value_target").innerHTML = label;
  }
};

//------------------------------
// モーダル外クリック時のイベント
//------------------------------
// document.addEventListener("click", (e) => {
//   if (!e.target.closest(".p-extendBox")) {
//     //ここに外側をクリックしたときの処理
//     if (1) {
//       console.log(this);
//       console.log("外側クリックされたよ");
//     }
//   } else {
//     //ここに内側をクリックしたときの処理
//     console.log("いえーい、みてるー？");
//   }
// });

// test
var modalOpen = document.getElementsByClassName("js-modal-open");
var modalClose = document.getElementsByClassName("js-modal-close");
for (var i = 0; i < modalOpen.length; i++) {
  modalOpen[i].addEventListener("click", function () {
    // クリック要素のdata属性値を取得
    let target = this.dataset.target;
    console.log("target:" + target);
    return false;
  });
}

// $(function () {
//   $(".js-modal-open").each(function () {
//     $(this).on("click", function () {
//       var target = $(this).data("target");
//       var modal = document.getElementById(target);
//       $(modal).fadeIn();
//       return false;
//     });
//   });
//   $(".js-modal-close").on("click", function () {
//     $(".js-modal").fadeOut();
//     return false;
//   });
// });
// test
