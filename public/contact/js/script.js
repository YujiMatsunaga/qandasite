$(function () {
    var $requiredElems = $(".required");
    var $patternElems = $(".pattern");
    var $equalToElems = $(".equal-to");
    var $minlengthElems = $(".minlength");
    var $maxlengthElems = $(".maxlength");
    var $showCountElems = $(".showCount");

    //初回送信前にはエラーを表示しない（送信時及び送信後にエラーがあればエラーを表示）
    var validateAfterFirstSubmit = true;
    //エラーを表示する span 要素に付与するクラス名
    var errorClassName = "error-js";

    //エラーメッセージを表示する span 要素を生成して親要素に追加する関数
    //$elem ：対象の要素
    //className ：エラーメッセージの要素に追加するクラス名
    //defaultMessage：デフォルトのエラーメッセージ
    var addError = function ($elem, className, defaultMessage) {
        //戻り値として返す変数 errorMessage にデフォルトのエラーメッセージを代入
        var errorMessage = defaultMessage;
        //data-error-xxxx  属性の値を取得
        var dataError = $elem.data("error-" + className);
        //data-error-xxxx  属性の値が label であれば
        if (dataError) {
            // data-error-xxxx  属性の値が label 以外の場合
            //data-error-xxxx  属性の値をエラーメッセージとする
            errorMessage = dataError;
        }
        //初回の送信前にはエラー表示はせず、送信時及び送信後の再入力時にエラーを表示
        if (!validateAfterFirstSubmit) {
            var classVal = errorClassName + " " + className;

            var html =
                '<span class="' +
                classVal +
                '" aria-live="polite">' +
                errorMessage +
                "</span>";
            $elem.parent().append(html);
        }
    };

    //値が空かどうかを検証及びエラーを表示する関数（空の場合は true を返す）
    //elem ：対象の要素
    var isValueMissing = function ($elem) {
        //テキストフィールドやテキストエリア、セレクトボックスの場合
        var className = "required";
        var $errorSpan = $elem
            .parent()
            .find("." + errorClassName + "." + className);

        //値が空の場合はエラーを表示して true を返す（trim() で前後の空白文字を削除）
        if ($elem.val().trim().length === 0) {
            if ($errorSpan.length === 0) {
                //テキストフィールドやテキストエリアの場合
                addError($elem, className, "入力は必須です");
            }
            return true;
        } else {
            if ($errorSpan.length !== 0) {
                $errorSpan.remove();
            }
            return false;
        }
    };

    //指定されたパターンにマッチしているかを検証する関数（マッチしていない場合は true を返す）
    //$elem ：対象の要素
    var isPatternMismatch = function ($elem) {
        //検証対象のクラス名
        var className = "pattern";
        //data-pattern 属性にパターンが指定されていればその値をパターンとする
        var pattern = new RegExp("^" + $elem.data(className) + "$");
        //data-pattern 属性の値が email の場合
        if ($elem.data(className) === "email") {
            pattern = /^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i;
        } else if ($elem.data(className) === "tel") {
            //data-pattern 属性の値が tel の場合
            pattern = /^\(?\d{2,5}\)?[-(\.\s]{0,2}\d{1,4}[-)\.\s]{0,2}\d{3,4}$/;
        }
        //エラーを表示する span 要素がすでに存在すれば取得
        var $errorSpan = $elem
            .parent()
            .find("." + errorClassName + "." + className);

        var val = $elem.val().trim();

        //対象の要素の値が空でなければパターンにマッチするかを検証
        if (val !== "") {
            if (!pattern.test($elem.val())) {
                if ($errorSpan.length === 0) {
                    addError($elem, className, "入力された値が正しくないようです");
                }
                return true;
            } else {
                if ($errorSpan.length !== 0) {
                    $errorSpan.remove();
                }
                return false;
            }
        } else if (val === "" && $errorSpan.length !== 0) {
            $errorSpan.remove();
        }
    };

    //指定された要素と値が一致するかどうかを検証する関数
    var isNotEqualTo = function ($elem) {
        //検証対象のクラス名
        var className = "equal-to";
        //比較対象の要素の id
        var equalTo = $elem.data(className);
        //比較対象の要素
        var $equalToElem = $("#" + equalTo);
        //エラーを表示する span 要素がすでに存在すれば取得
        var $errorSpan = $elem
            .parent()
            .find("." + errorClassName + "." + className);
        //対象の要素の値が空でなければ値が同じかを検証
        if ($elem.val().trim() !== "" && $equalToElem.val().trim() !== "") {
            if ($equalToElem.val() !== $elem.val()) {
                if ($errorSpan.length === 0) {
                    addError($elem, className, "入力された値が一致しません");
                }
                return true;
            } else {
                if ($errorSpan.length !== 0) {
                    $errorSpan.remove();
                }
                return false;
            }
        }
    };

    //サロゲートペアを考慮した文字数を返す関数
    var getValueLength = function (value) {
        return (value.match(/[\uD800-\uDBFF][\uDC00-\uDFFF]|[\s\S]/g) || []).length;
    };

    //指定された最小文字数を満たしているかを検証する関数（満たしていない場合は true を返す）
    var isTooShort = function ($elem) {
        //対象のクラス名
        var className = "minlength";
        //data-minlength 属性から最小文字数を取得
        var minlength = Number($elem.data(className));
        //エラーを表示する span 要素がすでに存在すれば取得（存在しなければ null が返る）
        var $errorSpan = $elem
            .parent()
            .find("." + errorClassName + "." + className);

        var val = $elem.val().trim();
        if (val !== "") {
            //サロゲートペアを考慮した文字数を取得
            var valueLength = getValueLength($elem.val());
            //値がdata-minlength属性で指定された最小文字数より小さければエラーを表示してtrueを返す
            if (valueLength < minlength) {
                if ($errorSpan.length === 0) {
                    addError($elem, className, minlength + "文字以上で入力ください");
                }
                return true;
            } else {
                //最小文字数より大きければエラーがあれば削除して false を返す
                if ($errorSpan.length !== 0) {
                    $errorSpan.remove();
                }
                return false;
            }
            //値が空でエラーを表示する要素が存在すれば削除
        } else if (val === "" && $errorSpan.length !== 0) {
            $errorSpan.remove();
        }
    };

    //指定された最大文字数を満たしているかを検証する関数（満たしていない場合は true を返す）
    var isTooLong = function ($elem) {
        //対象のクラス名
        var className = "maxlength";
        //data-maxlength 属性から最大文字数を取得
        var maxlength = Number($elem.data(className));
        //エラーを表示する span 要素がすでに存在すれば取得（存在しなければ null が返る）
        var $errorSpan = $elem
            .parent()
            .find("." + errorClassName + "." + className);

        var val = $elem.val().trim();
        if (val !== "") {
            //サロゲートペアを考慮した文字数を取得
            var valueLength = getValueLength($elem.val());
            //値がdata-maxlengthで指定された最大文字数より大きい場合はエラーを表示してtrueを返す
            if (valueLength > maxlength) {
                if ($errorSpan.length === 0) {
                    addError($elem, className, maxlength + "文字以内で入力ください");
                }
                return true;
            } else {
                if ($errorSpan.length !== 0) {
                    $errorSpan.remove();
                }
                return false;
            }
        } else if (val === "" && $errorSpan.length !== 0) {
            $errorSpan.remove();
        }
    };

    $requiredElems.on("input", function () {
        isValueMissing($(this));
    });

    $patternElems.on("input", function () {
        isPatternMismatch($(this));
    });

    $equalToElems.on("input", function () {
        isNotEqualTo($(this));
    });

    $minlengthElems.on("input", function () {
        isTooShort($(this));
    });

    $maxlengthElems.on("input", function () {
        isTooLong($(this));
    });

    //data-maxlength属性を指定した要素でshowCountクラスが指定されていれば入力文字数を表示
    $showCountElems.each(function () {
        //data-maxlength 属性の値を取得
        const dataMaxlength = $(this).data("maxlength");
        //data-maxlength 属性の値が存在し数値であれば
        if (dataMaxlength && !isNaN(dataMaxlength)) {
            //p要素のコンテンツを作成（.countSpanを指定したspan要素にカウントを出力。初期値は0）
            var countSpanHtml =
                '<span class="countSpan">0</span>/' + parseInt(dataMaxlength);
            
            var countHtml = '<p class="countSpanWrapper">' + countSpanHtml + "</p>";
            
            //入力文字数を表示する p 要素を追加
            $(this).after(countHtml);
        }
    });

    $showCountElems.on("input", function () {
        //上記で作成したカウントを出力する span 要素を取得
        var $countSpan = $(this).parent().find(".countSpan");
        //カウントを出力する span 要素が存在すれば
        if ($countSpan.length !== 0) {
            //入力されている文字数
            //サロゲートペアを考慮した文字数を取得
            var count = getValueLength($(this).val());
            //span 要素に文字数を出力
            $countSpan.text(count);
            //文字数が dataMaxlength（data-maxlength 属性の値）より大きい場合は文字を赤色に
            var dataMaxlength = $(this).data("maxlength");
            if (count > dataMaxlength) {
                $countSpan.css("color", "red");
                $countSpan.addClass("overMaxCount");
            } else {
                $countSpan.css("color", "");
                $countSpan.removeClass("overMaxCount");
            }
        }
    });

    //送信時の処理
    $(".validationForm").submit(function () {
        validateAfterFirstSubmit = false;

        let isError = false;

        try {
            //必須の検証
            $requiredElems.each(function () {
                if (isValueMissing($(this))) {
                    isError = true;
                }
            });

            //パターンの検証
            $patternElems.each(function () {
                if (isPatternMismatch($(this))) {
                    isError = true;
                }
            });

            //2つの値（メールアドレス）が一致するかどうかを検証
            $equalToElems.each(function () {
                if (isNotEqualTo($(this))) {
                    isError = true;
                }
            });

            //.minlength を指定した要素の検証
            $minlengthElems.each(function () {
                if (isTooShort($(this))) {
                    isError = true;
                }
            });

            //.maxlength を指定した要素の検証
            $maxlengthElems.each(function () {
                if (isTooLong($(this))) {
                    isError = true;
                }
            });

            //.error の要素を取得
            var $errorElem = $("." + errorClassName);
            if ($errorElem.length !== 0) {
                var errorElemOffsetTop = $errorElem.offset().top;
                //エラーの要素の位置へスクロール
                window.scrollTo({
                    top: errorElemOffsetTop - 40,
                    //スムーススクロール
                    behavior: "smooth",
                });
            }
        } catch (e) {
            console.log(e);
            return false;
        }

        return !isError;
    });
});
