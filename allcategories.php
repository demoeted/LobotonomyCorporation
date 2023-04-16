<?php
    session_start();
    require('connect.php');

    $query = "SELECT * FROM category";
    $statement = $db->prepare($query);
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
                <li><a href="index.php">Home</a></li>
                <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])):?>
                <li><a href="edit.php">New Article</a></li>
                <li><a href="categories.php">Update Categories</a></li>
                <?php endif ?>
                <li><a href="allcategories.php" class="active">Filter Articles By Category</a></li>
            </ul>
        </nav>
        <main id="all_articles">
            <h1>View Articles By Category</h1>
            <?php if($statement->rowCount()):?>
                <ul>
                <?php while($row = $statement->fetch()):?>
                    <li><a href="sort.php?category=<?=$row['id']?>"><?=$row['category_name']?></a></li>
                <?php endwhile?>
                </ul>
            <?php endif?>
        </main>
    </div>
</body>