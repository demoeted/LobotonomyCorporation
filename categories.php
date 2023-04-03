<?php
    session_start();

    require('connect.php');

    if(isset($_POST['name']) && !empty($_POST['name'])){
        $newCategory = filter_input(INPUT_POST,'name',FILTER_SANITIZE_FULL_SPECIAL_CHARS);

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
    <title>Add Categories</title>
</head>
<body>
    <?php include('header.php')?>
    <main>
        <nav>
            <ul id="menu">
                <li><a href="index.php" class="active">Home</a></li>
                <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])):?>
                <li><a href="edit.php">New Article</a></li>
                <?php endif ?>
            </ul>
        </nav>
        <form method="post" action="categories.php">
            <fieldset>
                <legend>Add Category</legend>
                <label for="name">Category Name</label>
                <input type="text" id="name" name="name" autofocus/>
            <button>Add Category</button>
            </fieldset>
    </main>
</body>
</html>