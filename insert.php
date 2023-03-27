<?php
    require('connect.php');
    
    if ($_POST && !empty($_POST['title']) && !empty($_POST['content'])) {
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $poster = 1;
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

        if($id){
            $query = "UPDATE article SET title = :title, content = :content, poster = :poster, date_edited = CURRENT_TIMESTAMP() WHERE id = :id LIMIT 1";
            $statement = $db->prepare($query);

            $statement->bindValue(":title", $title);
            $statement->bindValue(":content", $content);
            $statement->bindValue(":poster", $poster);
            $statement->bindValue(":id", $_POST['id'], PDO::PARAM_INT);

            $statement->execute();
        
        }else{
            $query = "INSERT INTO article (title, content, poster) VALUES (:title, :content, :poster)";
            $statement = $db->prepare($query);

            $statement->bindValue(":title", $title);
            $statement->bindValue(":content", $content);
            $statement->bindValue(":poster", $poster);

            $statement->execute();
        }

        header('Location: index.php');
        exit;
    }

    $article = [];

    $isNewArticle = checkNewArticle();

    function checkNewArticle(){
        $isNewArticle = true;

        if(isset($_GET['id'])){
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

            if($id){
                getArticle($id);

                $isNewArticle = false;
            }
        }

        return $isNewArticle;
    }

    function getArticle($id){
        global $article;
        global $db;
        $query = "SELECT * FROM article WHERE id = :id";

        $statement = $db->prepare($query);
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();

        $article = $statement->fetch();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Error</title>
</head>
<body>
    <div id="wrapper">
    <?php include('header.php')?>
    <nav>
        <ul id="menu">
            <li><a href="index.php" >Home</a></li>
            <?php if($isNewArticle): ?>
            <li><a href="edit.php" class="active">New Article</a></li>
            <?php else: ?>
            <li><a href="edit.php">New Article</a></li>
            <?php endif ?>  
        </ul>
    </nav>
    <main id="all_blogs">
        <h2>There was an error with the post.</h2>
        <?php if($isNewPost): ?>
        <form method="post" action="insert.php">
            
        <?php else: ?>
        <form method="post" action="insert.php?id=<?=$_GET['id']?>">
        <?php endif ?>
            <fieldset>
            <?php if($isNewPost): ?>
                <legend>New Article</legend>
                <label for="title">Title:</label>
                <input type="text" autofocus id="title "name="title">
                <label for="content">Caption:</label>
                <textarea id="content" name="content"></textarea>

            <?php else: ?>
                <legend>Edit Article</legend>
                <input type="hidden" name="id" value="<?=$article['id']?>">
                <label for="title" >Title:</label>
                <input type="text" autofocus id="title" name="title" value="<?= $article['title']?>">
                <label for="content">Content:</label>
                <textarea id="content" name="content" ><?= $article['content']?></textarea>
            <?php endif ?>

            <?php if($isNewArticle): ?>
                <button type="submit">Post!</button>
            <?php else: ?>
                <button type="submit">Update!</button>
            <?php endif?>

            <?php if(!$isNewArticle):?>
                <button type="submit" formaction="delete.php?id=<?=$_GET['id']?>" onclick="return confirm('Confirm Delete Post?')">Delete Post</button>
            <?php endif ?>
            </fieldset>
        </form>
    </main>
    </div>
</body>
</html>