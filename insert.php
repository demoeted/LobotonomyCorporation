<?php
    session_start();
    require('connect.php');
    require '\xampp\htdocs\a\php-image-resize-master\lib\ImageResize.php';
    require '\xampp\htdocs\a\php-image-resize-master\lib\ImageResizeException.php';

    use \Gumlet\ImageResize;

    //Source from: https://stackoverflow.com/questions/22642515/remove-all-punctuation-from-php-string-for-friendly-seo-url
    function seofy ($sString = '')
    {
        $sString = strip_tags($sString);
        $sString = preg_replace('/[^\\pL\d_]+/u', '-', $sString);
        $sString = trim($sString, "-");
        $sString = iconv('utf-8', "us-ascii//TRANSLIT", $sString);
        $sString = strtolower($sString);
        $sString = preg_replace('/[^-a-z0-9_]+/', '', $sString);

        return $sString;
    }

    function file_upload_path($original_filename, $upload_subfolder_name = 'uploads') {
        $current_folder = dirname(__FILE__);
        
        // Build an array of paths segment names to be joins using OS specific slashes.
        $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
        
        // The DIRECTORY_SEPARATOR constant is OS specific.
        return join(DIRECTORY_SEPARATOR, $path_segments);
     }
    
     function file_is_an_image($temporary_path, $new_path) {
        $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png', 'application/pdf'];
        $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png', 'pdf'];
        
        $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
        $actual_mime_type        = mime_content_type($temporary_path);
        
        $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
        $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
        
        return $file_extension_is_valid && $mime_type_is_valid;
    }

    $image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
    
    //  Sanitize user input to escape HTML entities and filter out dangerous characters.
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if (isset($title) && !empty($title) && isset($content)  && !empty($content)) {
        $poster = $_SESSION['id'];
        $category = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT);

        $permalink = seofy($_POST['permalink']);

        if(isset($_POST['permalink']) && !empty($_POST['permalink']) && !empty($permalink)){
            $query = "INSERT INTO article (title, content, poster, category, slug) VALUES (:title, :content, :poster, :category, :slug)";
            $statement = $db->prepare($query);

            $statement->bindValue(":title", $title);
            $statement->bindValue(":content", $content);
            $statement->bindValue(":poster", $poster);
            $statement->bindValue(":category", $category, PDO::PARAM_INT);
            $statement->bindValue(":slug", $permalink);

            $statement->execute();
        }
        else{
            $query = "INSERT INTO article (title, content, poster, category, slug) VALUES (:title, :content, :poster, :category, :slug)";
            $statement = $db->prepare($query);

            $statement->bindValue(":title", $title);
            $statement->bindValue(":content", $content);
            $statement->bindValue(":poster", $poster);
            $statement->bindValue(":category", $category, PDO::PARAM_INT);
            $statement->bindValue(":slug", seofy($title));

            $statement->execute();
        }


        if ($image_upload_detected) { 
            $image_filename        = $_FILES['image']['name'];
            $temporary_image_path  = $_FILES['image']['tmp_name'];
            $new_image_path        = file_upload_path($image_filename);

            if (file_is_an_image($temporary_image_path, $new_image_path)) {
                $article_id = $db->lastInsertId();

                $image = new ImageResize($temporary_image_path);

                $normal_path = pathinfo($image_filename, PATHINFO_FILENAME) . '_normal.' . pathinfo($image_filename, PATHINFO_EXTENSION);
                $image->save(file_upload_path($normal_path));
                $normal_path = "uploads/" . $normal_path;

                $query = "INSERT INTO `image` (path, article) VALUES (:path, :article)";
                $statement = $db->prepare($query);

                $statement->bindValue(":path", $normal_path);
                $statement->bindValue(":article", $article_id);
                $statement->execute();
        
                $image->resizeToWidth(700);
                $large_path = pathinfo($image_filename, PATHINFO_FILENAME) . '_large.' . pathinfo($image_filename, PATHINFO_EXTENSION);
                $image->save(file_upload_path($large_path));
        
                $query = "INSERT INTO `image` (path, article) VALUES (:path, :article)";
                $statement = $db->prepare($query);

                $large_path = "uploads/" . $large_path;

                $statement->bindValue(":path", $large_path);
                $statement->bindValue(":article", $article_id);
                $statement->execute();

                $image->resizeToWidth(100);
                $thumbnail_path = pathinfo($image_filename, PATHINFO_FILENAME) . '_thumbnail.' . pathinfo($image_filename, PATHINFO_EXTENSION);
                $image->save(file_upload_path($thumbnail_path));

                $query = "INSERT INTO `image` (path, article) VALUES (:path, :article)";
                $statement = $db->prepare($query);

                $thumbnail_path = "uploads/" . $thumbnail_path;

                $statement->bindValue(":path", $thumbnail_path);
                $statement->bindValue(":article", $article_id);
                $statement->execute();
            }
        }

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
    <script src="js\tinymce\tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: '#content'
      });
    </script>
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
                        <option value="<?=$category['id'] ?>"><?=$category['category_name']?></option>
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