      <div class="sidebar sidebar_left">
        <form name="" method="get">
          <h2>収録シリーズ</h2>
          <div class="selectbox">
            <select name="s_id">
              <option value="0" <?php if (getFormData('s_id', true) === 0) {
                                  echo 'selected';
                                } ?>>選択してください</option>
              <?php
              foreach ($dbBoosterData as $key => $val) {
              ?>
                <option value="<?php echo $val['id']; ?>" <?php if (getFormData('s_id', true) === $val['id']) {
                                                            echo 'selected';
                                                          } ?>><?php echo $val['booster_name'] ?></option>
              <?php
              }
              ?>
            </select>
          </div>
          <h2>種族</h2>
          <div class="selectbox">
            <select name="t_id">
              <option value="0" <?php if (getFormData('t_id', true) == 0) {
                                  echo 'selected';
                                } ?>>選択してください</option>
              <?php
              foreach ($dbTypesData as $key => $val) {
              ?>
                <option value="<?php echo $val['id']; ?>" <?php if (getFormData('t_id', true) === $val['id']) {
                                                            echo 'selected';
                                                          } ?>><?php echo $val['type_name']; ?></option>
              <?php
              }
              ?>
            </select>
          </div>
          <h2>誕生月</h2>
          <div class="selectbox">
            <select name="b_id">
              <option value="0" <?php
                                if (getFormData('b_id', true) == 0) {
                                  echo 'selected';
                                } ?>>選択してください</option>
              <?php
              for ($i = 1; $i <= 12; $i++) {
              ?>
                <option value="<?php echo $i; ?>" <?php
                                                  if (getFormData('b_id', true) == $i) {
                                                    echo 'selected';
                                                  } ?>><?php echo $i; ?>月</option>
              <?php
              }
              ?>
            </select>
          </div>
          <h2>性格</h2>
          <div class="selectbox">
            <select name="p_id">
              <option value="0" <?php if (getFormData('p_id', true) == 0) {
                                  echo 'selected';
                                } ?>>選択してください</option>
              <?php
              foreach ($dbPersonalData as $key => $val) {
              ?>
                <option value="<?php echo $val['id']; ?>" <?php if (getFormData('p_id', true) === $val['id']) {
                                                            echo 'selected';
                                                          } ?>><?php echo $val['personal_name']; ?></option>
              <?php
              }
              ?>
            </select>
          </div>
          <input type="submit" value="検索" />
        </form>
      </div>