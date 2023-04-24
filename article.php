<?php

require('connect.php');

session_start();

$article = [];
$comments = [];

function getArticle($id){
    global $article;
    global $db;

    $query = "SELECT a.*, u.user_name, c.category_name FROM article a JOIN category c ON a.category = c.id JOIN user u ON a.poster = u.id WHERE a.article_id = :article_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':article_id', $id);
    $statement->execute();

    $article = $statement->fetch();
}

function getComments($id){
    global $comments;
    global $db;

    if(isset($_SESSION['email']) && !empty($_SESSION['email'])){
        $query = "SELECT c.*, u.user_name FROM comment c JOIN user u ON c.user_id = u.id WHERE c.article_id = :article_id ORDER BY c.article_id DESC";
    }
    else{
        $query = "SELECT c.*, u.user_name FROM comment c JOIN user u ON c.user_id = u.id WHERE c.article_id = :article_id GROUP BY c.private HAVING c.private = 0 ORDER BY c.article_id DESC";
    }

    $statement = $db->prepare($query);
    $statement->bindValue(':article_id', $id);
    $statement->execute();

    $comments = $statement->fetchAll();
}

function getImage($article_id){
    global $db;

    $statement;

    $query = "SELECT path, article FROM image WHERE path LIKE '%large%' AND article = :article";

    $statement = $db->prepare($query);
    $statement->bindValue(":article", $article_id);
    $statement->execute();

    return $statement->fetch();
}

$article_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if($article_id){
    getArticle($article_id);
    getComments($article_id);

    if($article['slug'] !== $_GET['slug']){
        if($article_id !== $article['article_id']){
            header('Location: index.php');
        }
        header('Location: index.php');
    }
}
else{
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css">
    <title><?= $article['title']?></title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <div id="wrapper">
    <?php include('header.php') ?>
        <nav>
            <ul id="menu">
                <li><a href="index.php">Home</a></li>
                <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])):?>
                <li><a href="edit.php">New Article</a></li>
                <li><a href="categories.php">Update Categories</a></li>
                <?php endif ?>
                <li><a href="allcategories.php">Filter Articles By Category</a></li>
            </ul>
        </nav>
        <main id="all_articles">
            <div class="article">
                <h2><a href="article.php?id=<?=$article['article_id']?>"><?=$article['title']?></a></h2>
                <p>Category: <?= $article['category_name'] ?>
                <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])):?>
                    <?php if ($article['date_edited']): ?>
                        <p>By: <?= $article['user_name']?> - Posted: <?=date_format(date_create($article['date_posted']), "F d, Y, g:i a" )?> - Edited: <?=date_format(date_create($article['date_edited']), "F d, Y, g:i a" )?> - <a href="edit.php?id=<?=$_GET['id']?>">Edit</a></p>
                    <?php else: ?>
                        <p>By: <?= $article['user_name']?> - Posted: <?=date_format(date_create($article['date_posted']), "F d, Y, g:i a" )?> - <a href="edit.php?id=<?=$_GET['id']?>">Edit</a></p>
                    <?php endif ?>
                <?php else: ?>
                    <?php if ($article['date_edited']): ?>
                        <p>By: <?= $article['user_name']?> - Posted: <?=date_format(date_create($article['date_posted']), "F d, Y, g:i a" )?> - Edited: <?=date_format(date_create($article['date_edited']), "F d, Y, g:i a" )?></p>
                    <?php else: ?>
                        <p>By: <?= $article['user_name']?> - Posted: <?=date_format(date_create($article['date_posted']), "F d, Y, g:i a" )?> </p>
                    <?php endif ?>
                <?php endif ?>

                <?php if($image = getImage($article['article_id'])):?>
                    <img src="<?=$image['path']?>" alt="<?=$article['title']?>">
                <?php endif?>

                <div class="article_content">
                    <?= nl2br(htmlspecialchars_decode(stripslashes($article['content'])))?>
                </div>
            </div>

            <?php if(isset($_SESSION['email'])):?>
                <form method="post" action="insertcomment.php">
                    <fieldset>
                        <legend>Add a Comment</legend>
                        <input id="article_id" name="article_id" type="hidden" value="<?=$_GET['id'] ?>">
                        <textarea id="message" name="message"></textarea>
                        <button>Comment!</button>
                    </fieldset>
                </form>
            <?php else:?>
                <p>Want to add a comment? <a href="login.php">Log In</a> | <a href="signup.php">Sign up</a></p>
            <?php endif ?>

            <?php if(isset($comments) && !empty($comments)):?>
                <?php foreach($comments as $comment):?>
                    <h3><?= $comment['message']?></h3>
                    <?php if(isset($comment['date_edited'])):?>
                        <?php if($_SESSION['acctype'] === 'M' || $_SESSION['acctype'] === 'A' || $_SESSION['acctype'] === 'O'):?>
                            <p>By: <?= $comment['user_name']?> - Posted: <?=date_format(date_create($comment['date_posted']), "F d, Y, g:i a" )?> - Edited: <?=date_format(date_create($comment['date_edited']), "F d, Y, g:i a" )?> - <a href="hidecomment.php?id=<?=$comment['id']?>&article_id=<?=$_GET['id']?>">Hide from Public</a></p>
                        <?php else:?>
                            <p>By: <?= $comment['user_name']?> - Posted: <?=date_format(date_create($comment['date_posted']), "F d, Y, g:i a" )?> - Edited: <?=date_format(date_create($comment['date_edited']), "F d, Y, g:i a" )?></p>
                        <?php endif?>
                    <?php else:?>
                        <?php if($_SESSION['acctype'] === 'M' || $_SESSION['acctype'] === 'A' || $_SESSION['acctype'] === 'O'):?>
                            <p>By: <?= $comment['user_name']?> - Posted: <?=date_format(date_create($comment['date_posted']), "F d, Y, g:i a" )?> - <a href="hidecomment.php?id=<?=$comment['id']?>&article_id=<?=$_GET['id']?>">Hide from Public</a></p>
                        <?php else:?>
                            <p>By: <?= $comment['user_name']?> - Posted: <?=date_format(date_create($comment['date_posted']), "F d, Y, g:i a" )?></p>
                        <?php endif?>
                    <?php endif ?>
                <?php endforeach ?>
            <?php else:?>
                <h3>No comments <i>yet</i></h3>
            <?php endif?>
        </main>
    </div>
</body>
</html>