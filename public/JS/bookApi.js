$(function () {

  $("#search-button").click(function () {
    searchBooks();
  });

  function searchBooks() {
    const searchText = $(".search__text__input").val();
    const num = $("#displayed-num option:selected").val();
    const orderBy = $("#displayed-orderBy option:selected").val();
    
    const displayedNum = "&maxResults=" + num;
    const displayedOrderBy = "&orderBy=" + orderBy;

    //lists__itemのclassがついた要素を消去
    $(".lists__item").remove();
    $(".message").remove();

    $.ajax({
      url: 'https://www.googleapis.com/books/v1/volumes?q=' + searchText + displayedNum + displayedOrderBy,
      type: 'GET',// HTTP通信の種類を設定
      datatype: 'json',//サーバから返されるデータの型
    })

    //通信成功の場合の記述
    .done(function (data) {
      displayBooks(data);
    })

    //通信失敗の場合の記述
    .fail(function (err) {
      displayError(err);
    });
  }

  // 通信成功した場合の画面に表示するための処理
  function displayBooks(data) {
    var hitsData = data.totalItems;
    var itemsData = data.items;
    var template = '';

    if (hitsData === 0) {
      $(".search").after("<p class='message' style='color:red; font-weight:bold;'>検索結果が見つかりませんでした。</p>");
    } else {
      // $(".search").after("<p class='message'>" + hitsData + "件ヒットしました</p>");
    }


    // title	    タイトル
    // authors	  作者
    // publisher	出版社
    // infoLink	  URL
    // thumbnail	画像

    $.each(itemsData, function (index, el) {
      var bookData = el;

      var titleData = bookData.volumeInfo.title;
      if (bookData.volumeInfo.authors == undefined) {
        var authorData = "不明";
      } else {
        var authorData = bookData.volumeInfo.authors;
      }
      if (bookData.volumeInfo.publisher == undefined) {
        var publisherNameData = "不明";
      } else {
        var publisherNameData = bookData.volumeInfo.publisher;
      }
      var itemUrl = bookData.volumeInfo.infoLink;

      if (bookData.volumeInfo.imageLinks) {
        var imageData = bookData.volumeInfo.imageLinks.thumbnail;
      } else {
        //画像データがない時
        var imageData = src = "https://dummyimage.com/600x600/cbcbcb/ffffff&text=NO+IMAGE";
      }


      //↓画面に表示するためのHTMLタグを変数templateへ代入
      template += 
        "<li class='lists__item'>" +
        "<div class='lists__item__inner'>" +
        "<a href='" + itemUrl + "' class='lists__item__link' target='_blank'>" +
        "<img src='" + imageData + "' class='lists__item__img' alt=''>" +
        "<p class='lists__item__detail'>作品名：" + titleData + "</p>" +
        "<p class='lists__item__detail'>作者　：" + authorData + "</p>" +
        "<p class='lists__item__detail'>出版社：" + publisherNameData + "</p>" +
        "</a>" +
        "</div>" +
        "</li>";
    });
    //↓"ulのlists"に変数templateを挿入
    $("ul.lists").prepend(template);
  }

  // 通信失敗した場合のエラーメッセージを表示するための処理
  function displayError(err) {
    var error_code = err.status;
    // 400: リクエストエラー
    if (error_code === 400) {
      $(".search").after("<p class='message' style='color:red; font-weight:bold;'>何も入力されていません</p>");
    }
  }

});

