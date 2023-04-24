<?php
    session_start();

    require('connect.php');

    $row;

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(isset($id) && !empty($id)){

        $query = "SELECT * FROM user WHERE id = :id";

        $statement = $db->prepare($query);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();
    }

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if(isset($name) && !empty($name) && isset($email) && !empty($email)){
        if(isset($_POST['password']) && password_verify($_POST['password'], $_SESSION['password'])){
    
            $query = "UPDATE user SET name = :name, email = :email WHERE id = :id";
            $statement = $db->prepare($query);
    
            $statement->bindValue(":name", $name);
            $statement->bindValue(":email", $email);
            $statement->bindValue(":id", $_GET['id']);
    
            $statement->execute();
    
            header('Location: manage.php');
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Update User</title>
</head>
<body>
    <div id="wrapper">
    <?php include('header.php')?>
    <nav>
        <ul id="menu">
            <li><a href="index.php">Home</a></li>
            <?php if(isset($_SESSION['email'])):?>
            <li><a href="edit.php">New Post</a></li>
            <li><a href="categories.php">Update Categories</a></li>
            <?php endif ?>
            <li><a href="allcategories.php">Filter Articles By Category</a></li>
        </ul>
    </nav>
    <main id="all_articles">
    <?php if(isset($_GET['id']) && !empty($_GET['id'])):?>
    <form method="post" action="updateuser.php?id=<?=$_GET['id']?>">
    <?php endif?>
            <fieldset>
                <legend>Info</legend>
                <?php if(isset($row) && !empty($row)):?>
                    <label for="name">Name</label>
                    <input id="name" name="name" value="<?=$row['user_name']?>" type="text">

                    <label for="email">Email</label>
                    <input id="email" name="email" value="<?=$row['email']?>"type="text">

                    <?php if(isset($_POST['password']) || !empty($_POST['password'])):?>
                        <?php if(password_verify($_POST['password'], $_SESSION['password'])):?>
                            <p>Please try entering your password again.</p>
                        <?php endif ?>
                    <?php endif ?>
                    <label for="password">Enter Your Current Password</label>
                    <input id="password" name="password" type="password">
                <?php endif?>
            </fieldset>
            <?php if(isset($_GET['id']) && !empty($_GET['id'])):?>
                <button>Update User</button>
            <?php endif?>
        </form>
        </main>
    </div>
</body>
</html>