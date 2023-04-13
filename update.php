<?php
    session_start();
    require('connect.php');
    
    if ($_POST && !empty($_POST['title']) && !empty($_POST['content']) && !empty($_POST['id'])) {
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);

        $query = "UPDATE article SET title = :title, content = :content, category = :category, date_edited = CURRENT_TIMESTAMP() WHERE id = :id LIMIT 1";
        $statement = $db->prepare($query);

        $statement->bindValue(":title", $title);
        $statement->bindValue(":content", $content);
        $statement->bindValue(":category", $category, PDO::PARAM_INT);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);

        $statement->execute();
        
        

        header('Location: index.php');
        exit;
    }

    $article = [];
    $categories = [];

    $isNewArticle = checkNewArticle();

    function getArticle($id){
        global $article;
        global $db;
        $query = "SELECT * FROM article a JOIN category c ON a.category = c.id WHERE a.id = :id";
    
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
    
        $article = $statement->fetch();
    }

    function getCategories(){
        global $categories;
        global $db;
        $query = "SELECT * FROM category";
    
        $statement = $db->prepare($query);
        $statement->execute();
    
        $categories = $statement->fetchAll();
    }
    
    getCategories();
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
            <li><a href="edit.php">New Article</a></li>
        </ul>
    </nav>
    <main id="all_blogs">
        <h2>There was an error with the post.</h2>
        <form method="post" action="update.php">
            <fieldset>
                <legend>Edit Article</legend>
                <input type="hidden" name="id" value="<?=$article['id']?>">
                <label for="title" >Title:</label>
                <input type="text" autofocus id="title" name="title" value="<?= $article['title']?>">

                <label for="category">Category</label>
                <select id="category" name="category">
                        <option value="<?=$article['category']?>">Current Category: <?=$article['name']?></option>
                    <?php foreach($categories as $category):?>
                        <option value="<?=$category['id'] ?>"><?=$category['name']?></option>
                    <?php endforeach?>
                </select>
                
                <label for="content">Content:</label>
                <textarea id="content" name="content" ><?= $article['content']?></textarea>

                <button type="submit">Update!</button>
                <button type="submit" formaction="delete.php?id=<?=$_GET['id']?>" onclick="return confirm('Confirm Delete Post?')">Delete Post</button>
            </fieldset>
        </form>
    </main>
    </div>
</body>
</html>