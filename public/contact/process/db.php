<?php 
class db{
  //インスタンスを一つにする
  private static $instance;
  
  public static function getInstance(){
    try{
      if(!isset(self::$instance)){//もしクラス変数がセットされていなかったら
        self::$instance = new PDO(
          DSN,
          DB_USER,
          DB_PASS,
          [
            //例外を投げるように設定
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
          ]
        );
      }
      return self::$instance;
    } catch(PDOException $e){
      //例外を受け取る
      echo $e->getMessage();
      exit;
    }
  }
}
