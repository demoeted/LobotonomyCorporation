<?php
require('connect.php');

session_start();

$article = [];
$categories = [];
$image = [];

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
    $query = "SELECT * FROM article a JOIN category c ON a.category = c.id WHERE a.article_id = :article_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':article_id', $id);
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

function getImage(){
    global $article;
    global $image;
    global $db;

    $statement;

    $query = "SELECT path, article FROM image WHERE path LIKE '%normal%' AND article = :article";

    $statement = $db->prepare($query);
    $statement->bindValue(":article", $article['article_id']);
    $statement->execute();

    $image = $statement->fetch();
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
            <li><a href="categories.php">Update Categories</a></li>
            <li><a href="allcategories.php">Filter Articles By Category</a></li>
        </ul>
    </nav>
    <main id="all_blogs">
        <?php if($isNewArticle): ?>
        <form method="post" action="insert.php" enctype="multipart/form-data">
        <?php else: ?>
        <form method="post" action="update.php?id=<?=$article['article_id']?>" enctype="multipart/form-data">
        <?php endif ?>

            <fieldset>
            <?php if($isNewArticle): ?>
                <legend>New Article</legend>
                <label for="title">Title:</label>
                <input type="text" autofocus id="title" name="title">

                <label for="category">Category</label>
                <select id="category" name="category">
                    <?php foreach($categories as $category):?>
                        <option value="<?=$category['id'] ?>"><?=$category['category_name']?></option>
                    <?php endforeach?>
                </select>
                
                <label for="image">Add an Image:</label>
                <input type="file" name="image" id="image">
                
                <label for="content">Caption:</label>
                <textarea id="content" name="content"></textarea>
                
                <label for="permalink">Optional - Set a Permalink:</label>
                <input type="permalink" autofocus id="permalink" name="permalink">

            <?php else: ?>
                <?php getImage()?>
                <legend>Edit Article</legend>
                <input type="hidden" name="id" value="<?=$article['article_id']?>">
                <label for="title" >Title:</label>
                <input type="text" autofocus id="title" name="title" value="<?= $article['title']?>">

                <label for="category">Category</label>
                <select id="category" name="category">
                        <option value="<?=$article['category']?>">Current Category: <?=$article['category_name']?></option>
                    <?php foreach($categories as $category):?>
                        <option value="<?=$category['id'] ?>"><?=$category['category_name']?></option>
                    <?php endforeach?>
                </select>

                <?php if(isset($image) && !empty($image)):?>
                    <img src="<?=$image['path']?>">
                    <label for="deleteImage">Delete image?</label>
                    <input type="checkbox" name="deleteImage" id="deleteImage">
                <?php else:?>
                    <label for="image">Add an Image:</label>
                    <input type="file" name="image" id="image">
                <?php endif?>

                <label for="content">Content:</label>
                <textarea id="content" name="content" ><?= $article['content']?></textarea>

                <label for="permalink">Optional - Set a Permalink:</label>
                <input type="text" id="permalink" name="permalink" value="<?= $article['slug']?>">
            <?php endif ?>

            <?php if($isNewArticle): ?>
                <button type="submit">Post!</button>
            <?php else:?>
                <button type="submit">Update!</button>
                <button type="submit" formaction="delete.php?id=<?=$article['article_id']?>" onclick="return confirm('Confirm Delete Post?')">Delete Post</button>
            <?php endif ?>
            </fieldset>
        </form>
    </main>
    </div>
</body>
</html>