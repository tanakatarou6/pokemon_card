  <header>
    <div class="container">
      <div class="header_left">
        <h1><a href="index.php">ポケモンカードゲーム カード検索(仮)</a></h1>
      </div>
      <div class="header_right">
        <ul>
          <?php
          if (empty($_SESSION['user_id'])) {
          ?>
            <li><a href="guestlogin.php">ゲストログイン</a></li>
            <li><a href="login.php">ログイン</a></li>
            <li class="btn"><a href="signup.php">新規登録</a></li>
          <?php
          } else {
          ?>
            <li><a href="logout.php">ログアウト</a></li>
            <li class="btn"><a href="mypage.php">マイページ</a></li>
          <?php
          }
          ?>
        </ul>
      </div>
    </div>
  </header>