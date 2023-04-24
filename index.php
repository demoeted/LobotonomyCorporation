<?php
session_start();

require('connect.php');

$allArticles = [];

function getAllArticles(){
    global $allArticles;
    global $db;

    $statement;

    $query = "SELECT a.*, u.user_name FROM article a JOIN user u ON a.poster = u.id ORDER BY a.article_id DESC";
    $statement = $db->prepare($query);

    $statement->execute();

    $allArticles = $statement->fetchAll();
}

function getImage($article_id){
    global $db;

    $statement;

    $query = "SELECT path, article FROM image WHERE path LIKE '%thumbnail%' AND article = :article";

    $statement = $db->prepare($query);
    $statement->bindValue(":article", $article_id);
    $statement->execute();

    return $statement->fetch();
}

getAllArticles();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Welcome - Lobotonomy Corporation</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <div id="wrapper">   
        <?php include('header.php') ?>
        <nav>
            <ul id="menu">
                <li><a href="index.php" class="active">Home</a></li>
                <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])):?>
                <li><a href="edit.php">New Article</a></li>
                <li><a href="categories.php">Update Categories</a></li>
                <?php endif ?>
                <li><a href="allcategories.php">Filter Articles By Category</a></li>
            </ul>

        </nav>
        <main id="all_articles">
            <?php if(isset($_SESSION['loggedIn'])): ?>
                <?php if($_SESSION['loggedIn']):?>
                    <h2>Successfully Logged In!</h2>
                <?php $_SESSION['loggedIn'] = null ?>
                <?php else: ?>
                    <h2>Successfully Logged Out!</h2>
                    <?php $_SESSION['loggedIn'] = null ?>
                <?php endif ?>
            <?php endif ?>
            
            <?php if(isset($_SESSION['email'])):?>
                <form method="post" action="sort.php">
                    <p>Currently Sorted by: Date Created</p>
                    <label>Sort by:</label>
                    <select id="sortorder" name="sortorder">
                        <option value="title">Title</option>
                        <option value="date_posted">Date Created</option>
                        <option value="date_edited">Date Updated</option>
                    </select>
                    <button>Sort!</button>
                </form>
            <?php endif ?>
            <?php foreach($allArticles as $article):?>
                <div class="article">
                    <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])): ?>
                        <h2><a href="article.php?id=<?=$article['article_id']?>&slug=<?=$article['slug']?>"><?=$article['title']?></a></h2>
                        <?php if ($article['date_edited']): ?>
                            <p>By: <?= $article['user_name']?> - Posted: <?=date_format(date_create($article['date_posted']), "F d, Y, g:i a" )?> - Edited: <?=date_format(date_create($article['date_edited']), "F d, Y, g:i a" )?> - <a href="edit.php?id=<?=$article['article_id']?>">Edit</a></p>
                        <?php else: ?>
                            <p>By: <?= $article['user_name']?> - Posted: <?=date_format(date_create($article['date_posted']), "F d, Y, g:i a" )?> - <a href="edit.php?id=<?=$article['article_id']?>">Edit</a></p>
                        <?php endif ?>
                        
                    <?php else: ?>
                        <h2><a href="article.php?id=<?=$article['article_id']?>&slug=<?=$article['slug']?>"><?=$article['title']?></a></h2>
                        <?php if ($article['date_edited']): ?>
                            <p>By: <?= $article['user_name']?> - Posted: <?=date_format(date_create($article['date_posted']), "F d, Y, g:i a" )?> - Edited: <?=date_format(date_create($article['date_edited']), "F d, Y, g:i a" )?></p>
                        <?php else: ?>
                            <p>By: <?= $article['user_name']?> - Posted: <?=date_format(date_create($article['date_posted']), "F d, Y, g:i a" )?></p>
                        <?php endif ?>
                    <?php endif ?>
                    
                    <?php if($image = getImage($article['article_id'])):?>
                        <img src="<?= $image['path']?>" alt="<?= $article['title']?>">
                    <?php endif?>
                </div>
            <?php endforeach ?>
        </main>
    </div>
</body>
</html>