<?php
    session_start();
    require('connect.php');

    
    if ($_POST && !empty($_POST['title']) && !empty($_POST['content'])) {
        //  Sanitize user input to escape HTML entities and filter out dangerous characters.
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $poster = $_SESSION['id'];
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);

        
        $query = "INSERT INTO article (title, content, poster, category) VALUES (:title, :content, :poster, :category)";
        $statement = $db->prepare($query);

        $statement->bindValue(":title", $title);
        $statement->bindValue(":content", $content);
        $statement->bindValue(":poster", $poster);
        $statement->bindValue(":category", $category, PDO::PARAM_INT);

        $statement->execute();

        header('Location: index.php');
        exit;
    }
    
    $categories = [];

    function getCategories(){
        global $categories;
        global $db;
        $query = "SELECT * FROM category";
    
        $statement = $db->prepare($query);
        $statement->execute();
    
        $categories = $statement->fetchAll();
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
    <title>Error</title>
</head>
<body>
    <div id="wrapper">
    <?php include('header.php')?>
    <nav>
        <ul id="menu">
            <li><a href="index.php" >Home</a></li>
            <li><a href="edit.php" class="active">New Article</a></li>
        </ul>
    </nav>
    <main id="all_blogs">
        <h2>There was an error with the post.</h2>
        <form method="post" action="insert.php">
            <fieldset>
                <legend>New Article</legend>
                <label for="title">Title:</label>
                <input type="text" autofocus id="title "name="title" value="<?= $_POST['title']?>">

                <label for="category">Category</label>
                <select id="category" name="category">
                    <?php foreach($categories as $category):?>
                        <option value="<?=$category['id'] ?>"><?=$category['name']?></option>
                    <?php endforeach?>
                </select>

                <label for="content">Caption:</label>
                <textarea id="content" name="content"><?= $_POST['content']?></textarea>
                <button type="submit">Post!</button>
            </fieldset>
        </form>
    </main>
    </div>
</body>
</html>