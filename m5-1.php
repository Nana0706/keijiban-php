<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission5-1</title>
</head>
<body>
 
 <?php
  //データベース接続
  $dsn = 'データベース名';
  $user = 'ユーザー名';
  $password = 'パスワード';
  $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
  
  // テーブル作成
  $sql = "CREATE TABLE IF NOT EXISTS keijiban"
   ." ("
   . "id INT AUTO_INCREMENT PRIMARY KEY,"
   . "name char(32),"
   . "comment TEXT,"
   . "pass VARCHAR(15),"
   . "date DATETIME"
   .");";
   $stmt = $pdo->query($sql);
   
   //日付データ
   $date = date("Y/m/d H:i:s");
     
   //削除ボタンを押されたとき
   if(!empty($_POST["delete"])||!empty($_POST["delpass"])){
     $delete = $_POST["delete"];
     $delpass = $_POST["delpass"];
     
     $sql = 'SELECT * FROM keijiban WHERE id=:id';
     $stmt = $pdo->prepare($sql);
     $stmt->bindParam(':id', $delete, PDO::PARAM_INT); 
     $stmt->execute();
     $results = $stmt->fetchAll();
     foreach ($results as $row) {
         if ($delpass == $row['pass']){
             $sql = 'DELETE FROM keijiban WHERE id=:id';
             $stmt = $pdo->prepare($sql);
             $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
             $stmt->execute();
         }
     }  
   }
     
    //送信ボタンを押されたとき（名前とコメントがあるなら）
    if(!empty($_POST["name"])||!empty($_POST["comment"])){
      $name= ($_POST["name"]);
	  $comment = ($_POST["comment"]);
	  $pass = ($_POST["pass"]);
	  $editNO = $_POST["editNO"];
     
     //editNoがないときは新規投稿、ある場合は編集
     if (empty($_POST["editNO"])) {
        $sql = $pdo -> prepare("INSERT INTO keijiban (name, comment, pass, date) VALUES (:name, :comment, :pass, :date)");
        $sql->bindParam(':name', $name, PDO::PARAM_STR);
        $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql->bindParam(':pass', $pass, PDO::PARAM_STR);
        $sql->bindParam(':date', $date, PDO::PARAM_STR);
        $sql->execute();
         
     } else { //編集して投稿
     
        $sql = 'UPDATE keijiban SET name=:name,comment=:comment,pass=:pass,date=:date WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':id', $editNO, PDO::PARAM_INT);
        $stmt->execute();
     }
    }
    
     //編集機能
    if(!empty($_POST["edit"])) {
        $edit = $_POST["edit"];
        $editpass=$_POST["editpass"];

        $sql = 'SELECT * FROM keijiban WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach($results as $row) {
          if($edit == $row['id'] && $editpass == $row['pass']) {
             $editnum_form = $row['id'];
             $editname_form = $row['name'];
             $editcom_form = $row['comment'];
             $editpass_form = $row['pass'];
          } 
        }
    }

    ?>
    
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前" value="<?php if(!empty($editname_form)) {
                                            echo $editname_form;
                                           } ?>">
        <input type="text" name="comment" placeholder="コメント" value="<?php if(!empty($editcom_form)) {
                                            echo $editcom_form;
                                           } ?>">
        <input type="text" name="pass" placeholder="パスワード">
        <input type="hidden" name="editNO" value="<?php if(!empty($editnum_form)) {
                                            echo $editnum_form;
                                           } ?>">
        <input type="submit" name="submit">
    </form>
    <form action="" method="post">
        <input type="number" name="delete" placeholder="削除対象番号">
        <input type="text" name="delpass" placeholder="パスワード">
        <input type="submit" name="submit" value="削除">
    </form>
    <form action="" method="post">
        <input type="number" name="edit" placeholder="編集対象番号">
        <input type="text" name="editpass" placeholder="パスワード">
        <input type="submit" name="submit" value="編集">
    </form>
    
    <br>【 投稿一覧 】<br>
    
    <?php
    
    $sql = 'SELECT * FROM keijiban';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        echo $row['id'].', ';
        echo $row['name'].', ';
        echo $row['comment'].', ';
        echo $row['date'].'<br>';
        echo "<hr>";
    }

   ?>
   
</body>
</html>