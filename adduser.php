<?php
    session_start();

    require('connect.php');

    function dataFiltering(){
        $isClean = true;

        if(isset($_POST['name']) && !filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)){
            $isClean = false;
        }

        if(isset($_POST['email']) && !filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)){
            $isClean = false;
        }

        if(isset($_POST['password']) && !filter_input(INPUT_POST, 'password', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/m")))){
            $isClean = false;
        }

        if(isset($_POST['retypedpass']) && !filter_input(INPUT_POST, 'retypedpass', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/m")))){
            $isClean = false;
        }

        if(isset($_POST['password']) && $_POST['retypedpass']){
            if(strcmp($_POST['password'], $_POST['retypedpass']) !== 0){
                $isClean = false;
            }
        }

        if($_SESSION['accType'] === 'O'){
            if($_POST['type'] === 'O'){
                $isClean = false;
            }
            else if($_POST['type'] !== 'U' || $_POST['type'] !== 'M' ||$_POST['type'] !== 'A'){
                $isClean = false;
            }
        }

        if($_SESSION['accType'] === 'A'){
            if($_POST['type'] === 'O' || $_POST['type'] === 'A'){
                $isClean = false;
            }
            else if($_POST['type'] !== 'u' || $_POST['type'] !== 'M'){
                $isClean = false;
            }
        }

        return $isClean;
    }

    if(isset($_POST['name']) && !empty($_POST['name']) && dataFiltering()){
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/m")));
        $password = password_hash($password, PASSWORD_BCRYPT);
        $type = $_POST['type'];

        $query = "INSERT INTO user (name, email, password, type) VALUES (:name, :email, :password, :type)";
        $statement = $db->prepare($query);

        $statement->bindValue(":name", $name);
        $statement->bindValue(":email", $email);
        $statement->bindValue(":password", $password);
        $statement->bindValue(":type", $type);

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
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Add User</title>
</head>
<body>
    <div id="wrapper">
    <?php include('header.php')?>
    <nav>
        <ul id="menu">
            <li><a href="index.php">Home</a></li>
            <?php if(isset($_SESSION['email'])):?>
            <li><a href="edit.php">New Article</a></li>
            <li><a href="categories.php">Update Categories</a></li>
            <?php endif ?>
            <li><a href="allcategories.php">Filter Articles By Category</a></li>
        </ul>
    </nav>
    <main id="all_articles">
    <form method="post" action="adduser.php">
        <fieldset>
            <legend>Info</legend>
            
                <label for="name">Name</label>
                <input id="name" name="name" type="text">

                <label for="email">Email</label>
                <input id="email" name="email" type="text">

                <label for="type">Account Type</label>
                <select id="type" name="type">
                       <option value='U'>User</option>
                    <option value='M'>Moderator</option>
                    <?php if($_SESSION['acctype'] === 'O'):?>
                        <option value="A">Admin</option>
                    <?php endif ?>
                </select>

                <?php if(isset($_POST['password']) && isset($_POST['retypedpass'])):?>
                    <?php if(strcmp($_POST['password'], $_POST['retypedpass']) !== 0):?>
                        <p>Password and Retyped Password do not match. Please try again.</p>
                    <?php endif ?>
                <?php endif ?>
                <label for="password">Password</label>
                <input id="password" name="password" type="password">
                <ul>
                    <li>Password Requirements:</li>
                    <li>At least 8 characters</li>
                    <li>At least 1 uppercase</li>
                    <li>At least 1 lowercase</li>
                    <li>At least 1 special character</li>
                </ul>
                <label for="retypedpass">Retype Password</label>
                <input id="retypedpass" name="retypedpass" type="password">

                
            </fieldset>
            
            <button>Add User</button>
        </form>
        </main>
    </div>
</body>
</html>