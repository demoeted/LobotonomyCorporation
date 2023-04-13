<?php
require('connect.php');

session_start();

$article = [];
$categories = [];

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
var_dump($article);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css">
    <script src="js\tinymce\tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: '#content'
      });
    </script>
    <title>Edit this Article!</title>
</head>
<body><!-- Remember that alternative syntax is good and html inside php is bad -->
    <div id="wrapper">
    <?php include('header.php') ?>
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
        <?php if($isNewArticle): ?>
        <form method="post" action="insert.php">
        <?php else: ?>
        <form method="post" action="update.php">
        <?php endif ?>

            <fieldset>
            <?php if($isNewArticle): ?>
                <legend>New Article</legend>
                <label for="title">Title:</label>
                <input type="text" autofocus id="title" name="title">

                <label for="category">Category</label>
                <select id="category" name="category">
                    <?php foreach($categories as $category):?>
                        <option value="<?=$category['id'] ?>"><?=$category['name']?></option>
                    <?php endforeach?>
                </select>
                
                <label for="content">Caption:</label>
                <textarea id="content" name="content"></textarea>

            <?php else: ?>
                <legend>Edit Article</legend>
                <input type="hidden" name="id" value="<?=$_GET['id']?>">
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
            <?php endif ?>

            <?php if($isNewArticle): ?>
                <button type="submit">Post!</button>
            <?php else:?>
                <button type="submit">Update!</button>
                <button type="submit" formaction="delete.php?id=<?=$_GET['id']?>" onclick="return confirm('Confirm Delete Post?')">Delete Post</button>
            <?php endif ?>
            </fieldset>
        </form>
    </main>
    </div>
</body>
</html>