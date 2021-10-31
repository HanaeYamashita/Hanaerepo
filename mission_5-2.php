<?php 

//DBの接続設定(伏字Vr. )
$dsn = 'mysql:dbname=********;host=localhost';
    $user = '*********';
    $password = '**********';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //pdoとは（PHP Date Object）データベース接続クラスのこと 


    /****テーブルを作成*****/ 
    $sql = "CREATE TABLE IF NOT EXISTS tbtest" //tbtestというテーブルが存在しないときにテーブルを作成する。 
    ." (" 
    . "id INT AUTO_INCREMENT PRIMARY KEY," 
    . "name char(32)," 
    . "comment TEXT," 
    . "date DATETIME DEFAULT CURRENT_TIMESTAMP," 
    . "password TEXT" 
    .");"; 
    $stmt = $pdo->query($sql); 
    //テーブルの上書きは出来ない！！いったん削除してからじゃないとダメ！ 

    /***削除機能***/ 
    if(!empty($_POST["delete"]) && !empty($_POST["pass_2"])){ 
        $delete = $_POST["delete"]; 
        $pass_2 = $_POST["pass_2"]; 

        $id=$delete; 
        $password=$pass_2; 
        //テーブルから、id（投稿ナンバー）とパスワードが一致しているものを探す。 
        $sql = 'delete from tbtest where id=:id AND password=:password'; 
        //prepare→$sql(table)に値をつけて（文を準備）executeで実行 
        $stmt = $pdo->prepare($sql); 
        //bindParam→どんな名前のカラムを設定したかで変える必要がある→idに$idを挿入（INT） 
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
        //STRにした理由➤パスワードは文字列として扱うから。 
        $stmt->bindParam(':password', $pass_2, PDO::PARAM_STR); 
        //prepareで準備した命令をexecuteで実行。 
        $stmt->execute(); 
     
    } 

    /***編集機能***/ 
    if(!empty($_POST["editnum"]) && !empty($_POST["pass_3"])){ 
        $editnum = $_POST["editnum"]; 
        $pass_3 = $_POST["pass_3"]; 
        $id=$editnum; 
        $password=$pass_3; 

        //tableからidとpasswordが一致している者を探す（where文）＆それらを抽出する。(select文) 
        $sql = 'SELECT * FROM tbtest WHERE id=:id AND password=:password'; 
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、 
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、 
        $stmt->bindParam(':password', $pass_3, PDO::PARAM_STR); 
        $stmt->execute();                             // ←SQLを実行する。 
        $results = $stmt->fetchAll(); //SQLで検索したデータを全て一括で配列に取り込むメソッド 
            foreach ($results as $row){ 
                //$rowの中にはテーブルのカラム名が入る 
                 $editname=$row['name']; 
                 $editcomm=$row['comment']; 
                 $editpass=$row['password']; 
            echo "<hr>"; 
            } 
    } 

    /***編集モードか新規投稿かの条件***/ 
    if(!empty($_POST["name"]) && !empty($_POST["comment"])){ 
        $name=$_POST["name"]; 
        $comment=$_POST["comment"]; 
        

        //編集→UPDATE文を使ってデータベースのテーブルに登録したデータレコードを更新する。 
        if(!empty($_POST["edited"])){ 
            $edited=$_POST["edited"]; 
            $id=$edited;//変更する投稿番号 
            //var_dump($edited);
            $name=$_POST["name"];//変更したい名前 
            $comment=$_POST["comment"];//変更したいコメント 
            $pass_1=$_POST["pass_1"];//変更したいパスワード 
            //var_dump($edited); 
            //var_dump($id); 
           //var_dump($name); 
            //var_dump($comment); 
            //var_dump($pass_1); 
             

            $sql='UPDATE tbtest SET name=:name,comment=:comment,password=:password WHERE id=:id AND password=:password'; 
            //echo "hey"; 
            $stmt=$pdo->prepare($sql); 
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
            $stmt->bindParam(':name', $name, PDO::PARAM_STR); 
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR); 
            $stmt->bindParam(':password', $pass_1, PDO::PARAM_STR); 
            //var_dump($id);
            //var_dump($name);
            //var_dump($comment);
            //var_dump($pass_1);
            $stmt->execute(); 
             
        //新規投稿→INSERT文でデータ（レコード）を登録     
        }else{ 
            $sql=$pdo -> prepare("INSERT INTO tbtest (name, comment, password) VALUES (:name, :comment, :password)"); 
            $name=$_POST["name"]; 
             //echo "hello<br>"; 
            $comment=$_POST["comment"]; 
             //echo "hi"; 
            $pass_1=$_POST["pass_1"]; 
             //echo "hey"; 
            $sql -> bindParam(':name', $name, PDO::PARAM_STR); 
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR); 
            $sql -> bindParam(':password', $pass_1, PDO::PARAM_STR); 
            $sql -> execute(); 
            //var_dump($name); 
            //var_dump($comment); 
            //var_dump($pass_1); 
        } 
    } 
?> 

<html> 
    <head> 
        <title>mission_5</title> 
    </head> 

    <body> 
            <!----------新規＆編集表示--------------->             
            <form action="" method="post"> 
                <p><label for ="name">名前</label> 
            　　<input type="text" 
                 value="<?php if(isset($editname)&& isset($editcomm) && isset($editpass)){echo $editname;} ?>" 
                 name="name" placeholder="your name" required></p> 
                <br> 

               <p><label for ="com">コメント</label> 
               <input type ="text" 
                value="<?php if(isset($editname)&& isset($editcomm) && isset($editpass)){echo $editcomm;} ?>" 
                name="comment" placeholder="please comment here!" required></p> 
                <br> 

                <p><label for ="pass">パスワード</label> 
                <input type= "password"   
                value ="<?php if(isset($editname) &&  isset($editcomm) && isset($editpass)){echo $editpass;} ?>" 
                name="pass_1" placeholder="please password here." required></p> 
                <input type = "submit" name="submit" > 

                 
                <input type= "hidden" 
                value="<?php if(isset($editname) && isset($editcomm) && isset($editpass)){echo $editnum;}?>"  
                name="edited" placeholder="editnumber" required> 
                     
            </form> 
            <br> 


            <!-----------消去----------------> 
            <p>削除番号</p> 
            <form action="" method="post"> 
             <input type ="number" name ="delete" placeholder ="delete number here"> 
             <br> 
             <input type ="password" name="pass_2" placeholder="password"> 
            　  <input type = "submit" name="submit2" value="delete"> 
             <br> 
            </form> 


            <!------------編集----------------> 
            <p>編集番号</p> 
            <form action ="" method="post"> 
                <input type = "text" name = "editnum"　placeholder ="edit number here"> 
                    <input type ="password" name = "pass_3" placeholder="password"> 
                    <input type = "submit" name = "submit3" value = "edit"> 
                <br> 
            </form> 
        </body> 
</html> 

<?php 
        /*-------ファイルを表示(投稿一覧)---------*/  
        $dsn = 'mysql:dbname=**********;host=localhost';
        $user = '*********';
        $password = '**********';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $sql = 'SELECT * FROM tbtest'; 
        $stmt = $pdo->query($sql); 
        $results = $stmt->fetchAll(); 
        foreach ($results as $row){ 
        //$rowの中にはテーブルのカラム名が入る 
        echo $row['id'].','; 
        echo $row['name'].','; 
        echo $row['comment'].','; 
        echo $row['date'].'<br>'; 
    echo "<hr>"; 
    } 
?> 