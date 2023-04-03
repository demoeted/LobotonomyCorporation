<?php
session_start();

require('connect.php');

$statement;

if(isset($_POST['sort']) && !empty($_POST['sort'])){
    $query = "SELECT * FROM article ORDER BY :sort";
    $statement = $db->prepare($query);
    $statement->bindValue(":sort", $_POST['sort']);
}
else{
    $query = "SELECT * FROM article ORDER BY id DESC";
    $statement = $db->prepare($query);
}

$statement->execute();

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
                <li><a href="edit.php">New Post</a></li>
                <?php endif ?>
            </ul>
        </nav>
        <?php if(isset($_SESSION['loggedIn'])): ?>
            <?php if($_SESSION['loggedIn']):?>
                <h2>Successfully Logged In!</h2>
            <?php $_SESSION['loggedIn'] = null ?>
            <?php else: ?>
                <h2>Successfully Logged Out!</h2>
                <?php $_SESSION['loggedIn'] = null ?>
            <?php endif ?>
        <?php endif ?>
        <main id="all_articles">
            <?php if(isset($_SESSION['email'])):?>
                <form method="post" action="index.php">
                    <?php if(isset($_POST['sort']) && $_POST['sort'] === 'title'): ?>
                        <p>Currently Sorted by: Title</p>
                        <?php elseif(isset($_POST['sort']) && $_POST['sort'] === 'date_edited'): ?>
                        <p>Currently Sorted by: Date Updated</p>
                    <?php else: ?>
                        <p>Currently Sorted by: Date Created</p>
                    <?php endif ?>
                    <label>Sort by:</label>
                    <select id="sort" name="sort">
                        <option value="title">Title</option>
                        <option value="date_posted">Date Created</option>
                        <option value="date_edited">Date Updated</option>
                    </select>
                    <button>Sort!</button>
                </form>
            <?php endif ?>
            <?php if($statement->rowCount()):?>
                <?php while($row = $statement->fetch()):?>
                <div class="article">
                    <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])): ?>
                        <h2><a href="article.php?id=<?=$row['id']?>"><?=$row['title']?></a></h2>
                        <?php if ($row['date_edited']): ?>
                            <p>Posted: <?=date_format(date_create($row['date_posted']), "F d, Y, g:i a" )?> - Edited: <?=date_format(date_create($row['date_edited']), "F d, Y, g:i a" )?> - <a href="edit.php?id=<?=$row['id']?>">Edit</a></p>
                        <?php else: ?>
                            <p>Posted: <?=date_format(date_create($row['date_posted']), "F d, Y, g:i a" )?> - <a href="edit.php?id=<?=$row['id']?>">Edit</a></p>
                        <?php endif ?>
                        
                    <?php else: ?>
                        <h2><a href="article.php?id=<?=$row['id']?>"><?=$row['title']?></a></h2>
                        <?php if ($row['date_edited']): ?>
                            <p>Posted: <?=date_format(date_create($row['date_posted']), "F d, Y, g:i a" )?> - Edited: <?=date_format(date_create($row['date_edited']), "F d, Y, g:i a" )?></p>
                        <?php else: ?>
                            <p>Posted: <?=date_format(date_create($row['date_posted']), "F d, Y, g:i a" )?></p>
                        <?php endif ?>
                    <?php endif ?>
                </div>
                <?php endwhile ?>
            <?php endif ?>
        </main>
    </div>
</body>
</html>