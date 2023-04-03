<?php

require('connect.php');

session_start();

$query = "SELECT * FROM article WHERE id = :id";

$statement = $db->prepare($query);

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$statement->bindValue('id', $id, PDO::PARAM_INT);

$statement->execute();

if($statement->rowCount()){
    $row = $statement->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css">
    <title><?= $row['title']?></title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <div id="wrapper">
    <?php include('header.php') ?>
        <nav>
            <ul id="menu">
                <li><a href="index.php">Home</a></li>
                <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])):?>
                <li><a href="edit.php">New Post</a></li>
                <?php endif ?>
            </ul>
        </nav>
        <main id="all_articles">
            <div class="article">
                <h2><a href="article.php?id=<?=$row['id']?>"><?=$row['title']?></a></h2>
                <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])):?>
                    <?php if ($row['date_edited']): ?>
                        <p>Posted: <?=date_format(date_create($row['date_posted']), "F d, Y, g:i a" )?> - Edited: <?=date_format(date_create($row['date_edited']), "F d, Y, g:i a" )?> - <a href="edit.php?id=<?=$row['id']?>">Edit</a></p>
                    <?php else: ?>
                        <p>Posted: <?=date_format(date_create($row['date_posted']), "F d, Y, g:i a" )?> - <a href="edit.php?id=<?=$row['id']?>">Edit</a></p>
                    <?php endif ?>
                <?php else: ?>
                    <?php if ($row['date_edited']): ?>
                        <p>Posted: <?=date_format(date_create($row['date_posted']), "F d, Y, g:i a" )?> - Edited: <?=date_format(date_create($row['date_edited']), "F d, Y, g:i a" )?></p>
                    <?php else: ?>
                        <p>Posted: <?=date_format(date_create($row['date_posted']), "F d, Y, g:i a" )?> </p>
                    <?php endif ?>
                <?php endif ?>
                <div class="article_content">
                    <?= nl2br(htmlspecialchars_decode(stripslashes($row['content'])))?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>