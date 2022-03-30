<!DOCTYPE html>
<html lang="ja">

<?php require_once "bookAPI/head.php" ?>

<body>
  <div class="wrap">
    <div class="container">
      <div class="header col-8 offset-2">
        <p class="text-center mt-5 p-2" id="title">GoogleBook Api</p>
      </div>

      <div class="search col-8 offset-2">
        <div class="search__text">
          <input type="text" id="search-word" class="search__text__input text-center" placeholder="検索する">
        </div>
        <button id="search-button" class="search__btn">検索する</button>
      </div>

      <div class="row flex">
        <div class="col-0 offset-7 mb-3">
          表示件数：
          <select id="displayed-num">
            <option value=10>10件</option>
            <option value=20>20件</option>
            <option value=30>30件</option>
            <option value=40>40件</option>
          </select>
        </div>

        <div class="col-2 mb-3">
          表示件数：
          <select id="displayed-orderBy">
            <option value=relevance>関連度順</option>
            <option value=newest>新着順</option>
          </select>
        </div>
      </div>

      <ul class="lists"></ul>

    </div>
  </div>
</body>

</html>
