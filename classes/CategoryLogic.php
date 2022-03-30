<?php

//ファイル読み込み
require_once '../../core/DBconnect.php';

class categoryLogic
{
    /**
     * カテゴリの内容を取得する
     * @return bool $result
     */
    public static function getCategory()
    {
      $result = false;

      $sql = 'SELECT * FROM categories';

      try{
        $stmt = connect()->prepare($sql);
        // SQL実行
        $result = $stmt-> execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $categories;
      }catch(\Exception $e){
        // エラーの出力
        echo $e;
        // ログの出力
        error_log($e, 3, '../error.log');
        return $result;
      }
    }
  }
