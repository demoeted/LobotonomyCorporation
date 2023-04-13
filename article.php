<?php

require('connect.php');

session_start();

$article = [];
$comments = [];

function getArticle(){
    global $article;
    global $db;

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT a.*, u.user_name, c.category_name FROM article a JOIN category c ON a.category = c.id JOIN user u ON a.poster = u.id WHERE a.id = :id";

    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id);
    $statement->execute();

    $article = $statement->fetch();
}

function getComments(){
    global $comments;
    global $db;

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

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

getArticle();
getComments();
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
                <?php endif ?>
            </ul>
        </nav>
        <main id="all_articles">
            <div class="article">
                <h2><a href="article.php?id=<?=$article['id']?>"><?=$article['title']?></a></h2>
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