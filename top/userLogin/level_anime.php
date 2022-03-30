<?php
    $user_data = UserLogic::levelModal();
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport">
  <title>Cherry Blossom DEMO</title>
  <link rel="stylesheet" type="text/css" href="../../css/level_anime.css">
</head>
<body>
  <div class="mx-auto" style="width: 200px;">
  <div class="cherry-blossom-container text-center">
    <!-- レベルによる表示画像変更 -->
    <img id="hero" class="col-4 d-block" style="width: 100px; height: auto" src="img/22338667.png" alt="ないよ">
    <div id="lv" class="col-4 d-block">Lv.
      <span id="level"><?php echo $user_data['pre_level'] ?></span>
    </div>
      <progress class=" d-block mx-auto" id="lifeBar" value="0" max="100" min="0" optimum="100"></progress>
      <div class="col-4 d-block  element js-animation" id="ohome_word">Congratulation!</div>
    <div id="bg"></div>
  </div>
  </div>
  
  <script type="text/javascript">
    // 前提条件の定義
    let previousLevel = <?php echo $user_data['pre_level'] ?>;
    let currentLevel = <?php echo $user_data['level'] ?>;
    let previousExp = <?php echo $user_data['pre_exp'] ?>;
    let currentExp = <?php echo $user_data['exp'] ?>;
    lifeBar.value = previousExp;
    let exExp = currentExp % 100;
    
    // 前回開いたときからのレベルアップ処理
    function update() {
      // 変数の定義
      let heroImg = document.getElementById('hero');
      let lifeBar = document.getElementById('lifeBar');
      let level = document.getElementById('level'); 
      let bg = document.getElementById('bg'); 
      gameTimer = setTimeout(update, 4);
      lifeBar.value++;
    
      
      // レベルアップ時エフェクトを除去（再使用可能にする）
      if(lifeBar.value == 95){
        document.getElementById("bg").classList.remove("before");
      }
      // 経験値100でレベルアップ
      if(lifeBar.value >= lifeBar.max){
        lifeBar.value = 0;
        // レベルアップ時エフェクト起動
        document.getElementById("bg").classList.add("before");
        level.innerHTML++;
        
        // 一定レベルを超えたら表示画像が変更される
        if(level.innerHTML >= 5){
          document.getElementById("hero").src='/img/22503431.png';
        } 
        if(level.innerHTML >= 10){
          document.getElementById("hero").src='/img/22350820.png';
        }
        if (level.innerHTML >= 20){
          document.getElementById("hero").src='/img/22493175.png';
        }
      }
        //規定レベルに達したらループ終了
        if(level.innerHTML >= currentLevel && lifeBar.value >= exExp){
          clearTimeout(gameTimer);        
          // お褒めの言葉を表示
          document.getElementById("ohome_word").classList.add("is-show");
          
          // レベルが上がった場合、桜の花びらを表示
          if(previousLevel != currentLevel){
            // コンテナを指定
            const section = document.querySelector('.cherry-blossom-container');
            // 花びらを生成する関数
            const createPetal = () => {
              const petalEl = document.createElement('span');
              petalEl.className = 'petal';
              const minSize = 10;
              const maxSize = 15;
              const size = Math.random() * (maxSize + 1 - minSize) + minSize;
              petalEl.style.width = `${size}px`;
              petalEl.style.height = `${size}px`;
              petalEl.style.left = Math.random() * innerWidth + 'px';
              section.appendChild(petalEl);
              
              // 一定時間が経てば花びらを消す
              setTimeout(() => {
                petalEl.remove();
              }, 10000);
            }
            // 花びらを生成する間隔をミリ秒で指定
            setInterval(createPetal, 300);
          }
        }
    }    

    window.onload = update();


</script>
</body>



