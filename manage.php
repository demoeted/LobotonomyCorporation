<?php
    session_start();
    require('connect.php');

    $query = "SELECT * FROM user WHERE id > 1";

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
    <title>Manage Users</title>
</head>
<body>
    <div id="wrapper">
    <?php include('header.php')?>
    <nav>
        <ul id="menu">
            <li><a href="index.php">Home</a></li>
            <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])):?>
            <li><a href="edit.php">New Article</a></li>
            <li><a href="categories.php">Update Categories</a></li>
            <?php endif ?>
            <li><a href="allcategories.php">Filter Articles By Category</a></li>
        </ul>
    </nav>
    <main id="all_articles">
        <h2><a href="adduser.php">Add User</a></h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Type</th>
                <th>Update?</th>
                <th>Delete?</th>
            </tr>
            <?php while($row = $statement->fetch()):?>
                <tr>
                    <td><?= $row['user_name']?></td>
                    <td><?= $row['email']?></td>
                    <td><?= $row['type']?></td>
                    <?php if($_SESSION['acctype'] !== $row['type']):?>
                        <td><a href="updateuser.php?id=<?=$row['id']?>">Update User</a></td>
                    <?php elseif($_SESSION['email'] === $row['email']):?>
                        <td><a href="updateuser.php?id=<?=$row['id']?>">Update Yourself</a></td>
                    <?php endif ?>
                    <?php if($_SESSION['email'] !== $row['email'] || $_SESSION['acctype'] !== $row['type']):?>
                        <td><a href="deleteuser.php?id=<?=$row['id']?>" onclick="return confirm('Confirm Delete Post?')">Delete User?</a></td>
                    <?php endif ?>
                </tr>
            <?php endwhile?>
        </table>  
    </main>
    </div>
</body>
</html>