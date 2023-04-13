<?php
    session_start();
    require('connect.php');
    
    $user_id = $_SESSION['id'];
    $article_id = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if(isset($_POST['message']) && !empty($_POST['message'])){

        $query = "INSERT INTO comment (user_id, article_id, message) VALUES (:user_id, :article_id, :message)";

        $statement = $db->prepare($query);

        $statement->bindvalue(':user_id', $user_id);
        $statement->bindValue('article_id', $article_id);
        $statement->bindValue(':message', $message);

        $statement->execute();
    }

    header("Location: article.php?id=$article_id");
    exit();
?>