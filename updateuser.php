<?php
    session_start();

    require('connect.php');

    $row;

    if(isset($_GET['id']) && !empty($_GET['id'])){
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        $query = "SELECT * FROM user WHERE id = :id";

        $statement = $db->prepare($query);
        $statement->bindValue(":id", $_GET['id'], PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();
    }

    if(isset($_POST['name']) && !empty($_POST['name'])){
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $type = $_POST['type'];

        $query = "UPDATE user SET name = :name, email = :email, type = :type WHERE id = :id";
        $statement = $db->prepare($query);

        $statement->bindValue(":name", $name);
        $statement->bindValue(":email", $email);
        $statement->bindValue(":type", $type);
        $statement->bindValue(":id", $_GET['id']);

        $statement->execute();

        header('Location: manage.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
</head>
<body>
    <?php include('header.php')?>
    <nav>
        <ul id="menu">
            <li><a href="index.php">Home</a></li>
            <?php if(isset($_SESSION['email'])):?>
            <li><a href="edit.php">New Post</a></li>
            <?php endif ?>
        </ul>
    </nav>
    <?php if(isset($_GET['id']) && !empty($_GET['id'])):?>
    <form method="post" action="updateuser.php?id=<?=$_GET['id']?>">
    <?php endif?>
            <fieldset>
                <legend>Info</legend>
                <?php if(isset($row) && !empty($row)):?>
                    <label for="name">Name</label>
                    <input id="name" name="name" value="<?=$row['name']?>" type="text">

                    <label for="email">Email</label>
                    <input id="email" name="email" value="<?=$row['email']?>"type="text">

                    <label for="type">Account Type</label>
                    <select id="type" name="type">
                        <option value=<?=$row['type']?>>Current Type</option>
                        <option value="U">User</option>
                        <option value="M">Moderator</option>
                        <?php if($_SESSION['acctype'] === 'O'):?>
                            <option value="A">Admin</option>
                        <?php endif ?>
                    </select>

                    <?php if(isset($_POST['password']) && !empty($_POST['password'])):?>
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
</body>
</html>