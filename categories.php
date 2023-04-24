<?php
    session_start();

    require('connect.php');

    $newCategory = filter_input(INPUT_POST,'name',FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if(isset($newCategory) && !empty($newCategory)){
        $query = "INSERT INTO category (name) VALUES (:name)";
    
        $statement = $db->prepare($query);
        $statement->bindValue(":name", $newCategory);
        $statement->execute();

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Add Categories</title>
</head>
<body>
    <div id="wrapper">
    <?php include('header.php')?>
    <main>
        <nav>
            <ul id="menu">
                <li><a href="index.php">Home</a></li>
                <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])):?>
                <li><a href="edit.php">New Article</a></li>
                <li><a href="categories.php" class="active">Update Categories</a></li>
                <?php endif ?>
                <li><a href="allcategories.php">Filter Articles By Category</a></li>
            </ul>
        </nav>
        <form method="post" action="categories.php">
            <fieldset>
                <legend>Add Category</legend>
                <label for="name">Category Name</label>
                <input type="text" id="name" name="name" autofocus>
            <button>Add Category</button>
            </fieldset>
                </form>
    </main>
    </div>
</body>
</html>