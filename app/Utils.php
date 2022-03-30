<?php

namespace Qanda;

require_once __DIR__ . '/../config/def.php';

class Utils
{
  // htmlで行表示対応の文字列作成
  public static function compatiStr($str)
  {
    return str_replace("\n", "<br/>", $str);
  }
  // 日時の文字列作成
  public static function compatiDate($date, $format = "Y-m-d H:i")
  {
    if (!$date) {
      return '';
    }
    return date($format, strtotime($date));
  }
  // EMAIL送信★
  public static function sendEmail($email, $subject, $msg)
  {
    if (LOCAL == "1") {
      // email認証を省略
      return true;
    }

    mb_language("Japanese");
    mb_internal_encoding("UTF-8");
    $heads = sprintf("From: %s", GMEMAIL);
    return mb_send_mail($email, $subject, $msg, $heads);
  }
  // 現在、期間内の時間か？★
  public static function isSpanOver($tgtstrdt, $spanhour)
  {
    if ($tgtstrdt == NULL) {
      return TRUE;
    }
    $tgtdt = strtotime($tgtstrdt);
    $nowdt = strtotime("now");
    $spandiff = $nowdt - $tgtdt;
    $daydiff = $spanhour * 60 * 60;
    //echo $spandiff . ':' . $daydiff;
    if ($spandiff >= $daydiff) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
  public static function checkLogin()
  {
    $result = false;
    // セッションにログインユーザが入っていなければfalse
    if (isset($_SESSION['login_user']) && $_SESSION['login_user']['user_id'] > 0) {
      return $result = true;
    }
    return $result;
  } 
  // 現在の時刻から $addday日後の日時の文字列を作成
  public static function addDay($addday, $format = 0)
  {
    $tgtdt = strtotime($addday . 'day');
    if ($format == 0) {
      return date('Y-m-d 00:00', $tgtdt);
    } else if ($format == 1) {
      return date('Y-m-d 17:00', $tgtdt);
    } else {
      return date('Y-m-d 23:59', $tgtdt);
    }
  }
  // 日時整形
  public static function dayFormat($datetime)
  {
    return date("Y-m-d H:i", strtotime($datetime));
  }
  // 両端をトリミング
  public static function mbtrim($str) {
    return preg_replace("/(^\s+)|(\s+$)/u", "", $str);
  }
  // 特殊文字指定除去
  public static function trimSQL($str) {
    return trim($str, " \n\r\t\v\x");
  }
  // 特殊文字指定変換
  public static function convertSQL($str) {
    $str = mb_ereg_replace('(["_%#])', '#\1', $str); // % や _ を #% #_ にする
    $str = mb_ereg_replace("(['\\\\])", '#\\\1', $str); // ' や \ を #\' #\\ にする
    return $str;
  }
  // 特殊文字除去(htmlのタグとして動作しないように変換)
  public static function h($str)
  {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
  // 特殊タグ除去(一部タグとして許容する)
  static $stable = array(
    '&lt;h1&gt;' => '<h1>',
    '&lt;/h1&gt;' =>'</h1>',
    '&lt;h2&gt;' => '<h2>',
    '&lt;/h2&gt;' =>'</h2>',
    '&lt;h3&gt;' => '<h3>',
    '&lt;/h3&gt;' =>'</h3>',
    '&lt;h4&gt;' => '<h4>',
    '&lt;/h4&gt;' =>'</h4>',
    '&lt;h5&gt;' => '<h5>',
    '&lt;/h5&gt;' =>'</h5>',
    '&lt;b&gt;' => '<b>',
    '&lt;/b&gt;' =>'</b>',
    '&lt;u&gt;' =>'<u>',
    '&lt;/u&gt;' =>'</u>',
    '&lt;p&gt;' =>'<p>',
    '&lt;/p&gt;' =>'</p>',
    '&lt;br&gt;' =>'<br>',
    '&lt;br/&gt;' =>'<br/>',
    '&lt;strong&gt;' => '<strong>',
    '&lt;/strong&gt;'=> '</strong>',
    '&lt;ul&gt;' => '<ul>',
    '&lt;/ul&gt;'=> '</ul>',
    '&lt;li&gt;' => '<li>',
    '&lt;/li&gt;'=> '</li>'
   );
  static $btable = array(
   '/&lt;img(.*)&gt;/' => '<img$1>'
  );

  public static function trimHtmlTag($str)
  {
    $str = htmlspecialchars($str, ENT_NOQUOTES);
    // 単純なタグ
    $search = array_keys(Utils::$stable);
    $replace = array_values(Utils::$stable);
    $str = str_replace($search, $replace, $str);
    // パラメータ付きタグ
    foreach (Utils::$btable as $key => $val) {
      $str = preg_replace($key, $val, $str);
    }

    return $str;
  }

  // 日付文字列チェック(todo)
  public static function checkDatetimeFormat($datetime)
  {
    $datetime = str_replace('/', '-', $datetime); // - に統一
    if (
      $datetime === date("Y-m-d H:i", strtotime($datetime))
      || $datetime === date("Y-m-d H:i:s", strtotime($datetime))
    ) {
      return true;
    }
    return false;
  }
  
  // 文字列チェック
  public static function isStrLen($str, $len)
  {
    if ($str && strlen($str) <= $len) {
      return true;
    } else {
      return false;
    }
  }

  public static function dump($text) {
    $h = fopen('./sql.txt', 'w');
    fwrite($h, $text);
    fclose($h);
  }
}
